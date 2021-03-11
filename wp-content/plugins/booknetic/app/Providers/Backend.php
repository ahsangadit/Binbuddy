<?php

namespace BookneticApp\Providers;

use BookneticApp\Backend\Settings\Helpers\LocalizationService;
use BookneticApp\Providers\Helper;

class Backend
{

	const MENU_SLUG			= 'booknetic';
	const DEFAULT_ACTION	= 'index';
	const MODULES_DIR		= __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Backend' . DIRECTORY_SEPARATOR;
	const API_URL			= 'https://www.booknetic.com/api/api.php';

	private static $DEFAULT_MODULE	= 'Dashboard';

	private static $installError = '';

	public static $currentModule;

	private static $menus = [];
	private static $middlewares = [];

	public static function init()
	{
		if( Helper::isSaaSVersion() && Permission::tenantId() > 0 && Permission::tenantInf()->getPermission( 'dashboard' ) === 'off' )
		{
			self::$DEFAULT_MODULE = 'Billing';
		}

		Permission::setAsBackEnd();

		self::initAdditionalData( true );

		$result = self::updatePluginDB();

		if( $result !== false && Permission::canUseBooknetic() )
		{
			add_action( 'admin_menu', function()
			{
				add_menu_page(
					'Booknetic',
					'Booknetic',
					'read',
					self::MENU_SLUG,
					[ self::class , 'initMenu' ],
					Helper::assets('images/logo-sm.svg'),
					90
				);
			});

			add_action('admin_init', function ()
			{
				$page = Helper::_get('page' , '', 'string');

				if( $page == self::MENU_SLUG && is_user_logged_in() )
				{
					self::initMiddlewares();
					self::constructBackend();
					exit();
				}
				else
				{
					self::initGutenbergBlocks();
				}
			});
		}
	}

	public static function initInstallation()
	{
		self::initAdditionalData( false );

		self::checkInstallationRequest();

		add_action( 'admin_menu', function()
		{
			add_menu_page(
				'Booknetic',
				'Booknetic',
				'read',
				self::MENU_SLUG,
				array( self::class , 'installationMenu' ),
				Helper::assets('images/logo-sm.svg'),
				90
			);
		});

		if( Helper::_get('page', '', 'string') == self::MENU_SLUG )
		{
			wp_enqueue_script( 'booknetic-install', Helper::assets('js/install.js'), ['jquery'] );
			wp_enqueue_style('booknetic-install', Helper::assets('css/install.css') );
		}
	}

	private static function checkInstallationRequest()
	{
		add_action( 'wp_ajax_booknetic_install_plugin', function ()
		{
			$purchase_code = Helper::_post('purchase_code', '', 'string');
			$where_find = Helper::_post('where_find', '', 'string');
			$email = Helper::_post('email', '', 'string');

			if( empty( $purchase_code ) || empty( $where_find ) )
			{
				Helper::response(false, 'Please fill the form correctly!');
			}

			$request = wp_remote_get( self::API_URL . '?act=install&version=' . Helper::getVersion() . '&purchase_code=' . $purchase_code . '&domain=' . site_url() . '&statistic=' . $where_find . '&email=' . $email );

			if ( !is_wp_error( $request ) && isset( $request['response']['code'] ) && $request['response']['code'] == 200 && !empty( $request['body'] ) )
			{
				$result = json_decode( $request['body'], true );

				if( !is_array( $result ) )
				{
					Helper::response(false, 'Installation error! Rosponse error! Response: ' . htmlspecialchars( $request['body'] ));
				}

				if( !($result['status'] == 'ok' && isset($result['sql'])) )
				{
					Helper::response(false, isset($result['error_msg']) ? $result['error_msg'] : 'Error! Response: ' . htmlspecialchars( $request['body'] ) );
				}

				$sql = str_replace( [ '{tableprefix}', '{tableprefixbase}' ] , [ (DB::DB()->base_prefix . DB::PLUGIN_DB_PREFIX) , DB::DB()->base_prefix ] , base64_decode($result['sql']) );

				if( empty( $sql ) )
				{
					Helper::response(false, 'Error! Please contact the plugin administration! ( info@booknetic.com )');
				}

				set_time_limit(0);
				foreach( explode(';' , $sql) AS $sqlQueryOne )
				{
					$checkIfEmpty = preg_replace('/\s/', '', $sqlQueryOne);
					if( !empty( $checkIfEmpty ) )
					{
						DB::DB()->query( $sqlQueryOne );
					}
				}

				register_uninstall_hook( dirname( dirname( __DIR__ ) ) . '/init.php', [ Helper::class, 'uninstallPlugin' ]);

				Helper::setOption( 'plugin_version', Helper::getVersion(), false );
				Helper::setOption( 'purchase_code', $purchase_code, false );

				if( isset( $result['saas_url'] ) && !empty( $result['saas_url'] ) )
				{
					$saasInstaller = new PluginInstaller( $result['saas_url'] );
					if( $saasInstaller->install() === false )
					{
						Helper::response(false, bkntc__('An error occurred, please try again later'));
					}

					Helper::setOption('saas_plugin_version', '0.0.0', false);
				}

				Helper::response(true);
			}
			else
			{
				Helper::response(false, bkntc__('An error occurred, please try again later'));
			}
		});

	}

	public static function installationMenu()
	{
		$select_options = [];

		$data = wp_remote_get(self::API_URL . '?act=statistic_option');

		if ( !is_wp_error( $data ) && isset( $data['response']['code'] ) && $data['response']['code'] == 200 && !empty( $data['body'] ) )
		{
			$select_options = json_decode( $data['body'], true );
		}

		$hasError = self::$installError;
		require_once self::MODULES_DIR . 'Base/view/install.php';
	}

	public static function initDisabledPage()
	{
		self::initAdditionalData( false );

		self::checkReActivateAction();

		add_action( 'admin_menu', function()
		{
			add_menu_page(
				'Booknetic (!)',
				'Booknetic (!)',
				'read',
				self::MENU_SLUG,
				[ self::class , 'disabledMenu' ],
				Helper::assets('images/logo-sm.svg'),
				90
			);
		});

		if( Helper::_get('page', '', 'string') == self::MENU_SLUG )
		{
			wp_enqueue_script( 'booknetic-disabled', Helper::assets('js/disabled.js'), ['jquery'] );
			wp_enqueue_style('booknetic-disabled', Helper::assets('css/disabled.css') );
		}
	}

	private static function checkReActivateAction()
	{
		add_action( 'wp_ajax_booknetic_reactivate_plugin', function ()
		{
			$code = Helper::_post( 'code', '', 'string' );

			if ( empty( $code ) )
			{
				Helper::response( false, bkntc__( 'Please enter the purchase code!' ) );
			}

			if ( Helper::getInstalledVersion() == Helper::getVersion() )
			{
				Helper::response( false, bkntc__( 'Your plugin is already activated!' ) );
			}

			set_time_limit( 0 );

			$check_purchase_code = self::API_URL . '?act=install&version=' . urlencode( Helper::getVersion() ) . '&purchase_code=' . urlencode( $code ) . '&domain=' . urlencode( site_url() );
			$api_result          = Curl::getURL( $check_purchase_code );

			if ( empty( $api_result ) )
			{
				Helper::response( false, bkntc__( 'Your server can not access our license server via CURL! Our license server is "%s". Please contact your hosting provider/server administrator and ask them to solve the problem. If you are sure that problem is not your server/hosting side then contact FS Poster administrators.', [ htmlspecialchars( self::API_URL ) ] ) );
			}

			$result = json_decode( $api_result, TRUE );

			if ( ! is_array( $result ) )
			{
				Helper::response( false, bkntc__( 'Reactivation failed! Response: %s', [ esc_html( $api_result ) ] ) );
			}

			if ( ! ( $result[ 'status' ] === 'ok' && isset( $result[ 'sql' ] ) ) )
			{
				Helper::response( false, isset( $result[ 'error_msg' ] ) ? $result[ 'error_msg' ] : bkntc__( 'Error! Response: %s', [ esc_html( $api_result ) ] ) );
			}

			Helper::setOption( 'plugin_disabled', '0', TRUE );
			Helper::setOption( 'plugin_alert', '', TRUE );
			Helper::setOption( 'poster_plugin_installed', Helper::getVersion(), TRUE );
			Helper::setOption( 'poster_plugin_purchase_key', $code, TRUE );

			Helper::response( TRUE, [ 'msg' => bkntc__( 'Plugin reactivated!' ) ] );
		});

	}

	public static function disabledMenu()
	{
		$select_options = [];

		require_once self::MODULES_DIR . 'Base/view/disabled.php';
	}

	private static function constructBackend()
	{
		// check if Ajax request...
		$isAjax = Helper::_get('ajax', '0', 'int');

		if( $isAjax == 1 )
		{
			self::initAjaxRequest();

			return;
		}

		// get and sanitize module parameter...
		$module = Helper::_get('module', self::$DEFAULT_MODULE, 'string');
		$module = preg_replace('/[^a-zA-Z0-9\-\_]/', '', $module);
		$module = ucfirst($module);

		// check if exists entered module
		if( empty( $module ) || !file_exists( self::MODULES_DIR . $module ) )
			$module = self::$DEFAULT_MODULE;

		// get and sanitize action parameter...
		$action = Helper::_get('action', self::DEFAULT_ACTION, 'string');
		$action = preg_replace('/[^a-zA-Z0-9\-\_]/', '', $action);

		if( empty( $action ) || strpos($action, '_') === 0 )
			$action = self::DEFAULT_ACTION;

		self::$currentModule = $module;

		$moduleMainClass = '\BookneticApp\\Backend\\' . $module . '\\Controller\\Main';

		if( method_exists( $moduleMainClass , $action ) )
		{
			$module = new $moduleMainClass();
			$module->$action();
		}
		else
		{
			print bkntc__( '%s - action not exists in %s module!' , [ htmlspecialchars( $action ) , htmlspecialchars( $module ) ]);
		}
	}

	private static function initMiddlewares()
	{
		$findMiddlewareClasses = glob(self::MODULES_DIR . '/*/Middleware.php');

		$modulesDir = str_replace( '\\' , '/' , realpath(self::MODULES_DIR));

		foreach ($findMiddlewareClasses as $middlewareClass)
		{
			$middlewareClass = str_replace( '\\' , '/' , realpath( $middlewareClass ) );

			$startIndex = strpos( $middlewareClass, $modulesDir );
			if( $startIndex !== 0 )
				continue;

			$getModuleName = substr($middlewareClass, strlen( rtrim( $modulesDir , '/' ) ) + 1);
			$getModuleName = explode('/', $getModuleName);

			$moduleName = $getModuleName[0];

			$middlewareClassName = "\\BookneticApp\\Backend\\{$moduleName}\\Middleware";

			self::$middlewares[] = new $middlewareClassName();
		}

	}

	private static function initAjaxRequest()
	{
		// get and sanitize module parameter...
		$module = Helper::_post('module', self::$DEFAULT_MODULE, 'string');
		$module = preg_replace('/[^a-zA-Z0-9\-\_]/', '', $module);
		$module = ucfirst($module);

		// chech if exists entered module
		if( !file_exists( self::MODULES_DIR . $module ) )
			$module = self::$DEFAULT_MODULE;

		// get and sanitize action parameter...
		$action = Helper::_post('action', self::DEFAULT_ACTION, 'string');
		$action = preg_replace('/[^a-zA-Z0-9\-\_]/', '', $action);

		self::$currentModule = $module;

		$moduleMainClass = '\BookneticApp\\Backend\\' . $module . '\\Controller\\Ajax';

		if( method_exists( $moduleMainClass , $action ) )
		{
			$module = new $moduleMainClass();
			$module->$action();
		}
		else
		{
			Helper::response(false, bkntc__( '%s - action not exists in %s module!' , [ htmlspecialchars( $action ) , htmlspecialchars( $module ) ]));
		}
	}

	public static function initMenu()
	{
		return;
	}

	public static function addMenu( Menu $menu )
	{
		if( !isset( self::$menus[ $menu->getType() ] ) )
		{
			self::$menus[ $menu->getType() ] = [];
		}

		self::$menus[ $menu->getType() ][] = $menu;
	}

	public static function sortMenu( $type )
	{
		if( isset( self::$menus[ $type ] ) )
		{
			uasort( self::$menus[ $type ], function( $a, $b )
			{
				if ( $a->getOrder() == $b->getOrder() )
				{
					return 0;
				}

				return ( $a->getOrder() < $b->getOrder() ) ? -1 : 1;
			});
		}
	}

	public static function getMenu( $type )
	{
		self::sortMenu( $type );
		return isset( self::$menus[ $type ] ) ? self::$menus[ $type ] : [];
	}

	private static function initAdditionalData( $initUpdater )
	{
		if( $initUpdater )
		{
			$purchaseCode = Helper::getOption('purchase_code', null, false);
			$updater = new FSCodeUpdater( self::MENU_SLUG, self::API_URL, $purchaseCode );
		}

		add_filter('plugin_action_links_booknetic/init.php' , function ($links)
		{
			$newLinks = [
				'<a href="https://www.booknetic.com/support/" target="_blank">' . __('Support', 'booknetic') . '</a>',
				'<a href="https://www.booknetic.com/documentation/" target="_blank">' . __('Doc', 'booknetic') . '</a>'
			];

			return array_merge($newLinks, $links);
		});
	}

	private static function updatePluginDB()
	{
		$code = Helper::getOption('purchase_code', null, false);

		$installed_version = Helper::getInstalledVersion();
		$current_version = Helper::getVersion();

		if($installed_version == $current_version)
		{
			return true;
		}

		set_time_limit( 0 );

		$result2 = Curl::getURL( self::API_URL . '?act=update&version1=' . $installed_version . '&version2=' . $current_version . '&purchase_code=' . $code . '&domain=' . site_url() );

		$result = json_decode( $result2 , true );

		if( !is_array( $result ) )
		{
			if( empty( $result2 ) )
			{
				return [ false, bkntc__('Booknetic! Your server can not access our license server via CURL! Our license server is "%s". Please contact your hosting provider/server administrator and ask them to solve the problem. If you are sure that problem is not your server/hosting side then contact Booknetic administrators.' , [ esc_html(self::API_URL) ] ) ];
			}

			return [ false, bkntc__( 'Booknetic! Installation error! Rosponse error! Response: %s' , [ esc_html( $result2 ) ] ) ];
		}

		if( !($result['status'] == 'ok' && isset($result['sql'])) )
		{
			return [ false, ( isset($result['error_msg']) ? $result['error_msg'] : bkntc__('Error! Response: %s', [ esc_html( $result2 ) ] ) ) ];
		}

		$sql = str_replace( ['{tableprefix}', '{tableprefixbase}'] , [(DB::DB()->base_prefix . DB::PLUGIN_DB_PREFIX), DB::DB()->base_prefix] , base64_decode($result['sql']) );

		foreach( explode(';' , $sql) AS $sqlQueryOne )
		{
			$checkIfEmpty = preg_replace('/\s/', '', $sqlQueryOne);
			if( !empty( $checkIfEmpty ) )
			{
				DB::DB()->query( $sqlQueryOne );
			}
		}

		self::restoreLocalizations();

		Helper::setOption( 'plugin_version', Helper::getVersion(), false );

		return true;
	}

	private static function initGutenbergBlocks()
	{
		if( !function_exists('register_block_type') )
			return;

		wp_register_script(
			'booknetic-blocks',
			plugins_url( 'assets/gutenberg-block.js', dirname(dirname(__DIR__)) . '/init.php' ),
			[ 'wp-blocks', 'wp-element', 'wp-editor', 'wp-components' ]
		);
		wp_localize_script( 'booknetic-blocks', 'BookneticData', [
			'appearances'	    =>	DB::fetchAll('appearance', null, null, ['`id` AS `value`', '`name` AS `label`']),
			'staff'			    =>	DB::fetchAll('staff', null, null, ['`id` AS `value`', '`name` AS `label`']),
			'services'		    =>	DB::fetchAll('services', null, null, ['`id` AS `value`', '`name` AS `label`']),
			'service_categs'	=>	DB::fetchAll('service_categories', null, null, ['`id` AS `value`', '`name` AS `label`']),
			'locations'		    =>	DB::fetchAll('locations', null, null, ['`id` AS `value`', '`name` AS `label`'])
		] );

		register_block_type( 'booknetic/booking' , ['editor_script' => 'booknetic-blocks'] );
		register_block_type( 'booknetic/customerpanel' , ['editor_script' => 'booknetic-blocks'] );

		add_filter( 'block_categories', function( $categories )
		{
			return array_merge(
				$categories,
				[
					[
						'slug' => 'booknetic',
						'title' => 'Booknetic',
					],
				]
			);
		}, 10, 2);
	}

	private static function restoreLocalizations()
	{
		$languages = glob( Helper::uploadedFile( 'booknetic_*.lng', 'languages' ) );

		foreach( $languages AS $language )
		{
			if( !preg_match( '/booknetic_([a-zA-Z0-9\-\_]+)\.lng$/', $language, $lang_name ) )
				continue;

			$lang_name = $lang_name[1];

			if( !LocalizationService::isLngCorrect( $lang_name ) )
				continue;

			$translations = file_get_contents( $language );
			$translations = json_decode( base64_decode( $translations ), true );

			if( is_array( $translations ) && !empty( $translations ) )
			{
				LocalizationService::saveFiles( $lang_name, $translations );
			}
		}
	}

}

