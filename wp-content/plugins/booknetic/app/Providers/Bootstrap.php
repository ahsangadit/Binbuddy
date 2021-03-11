<?php

namespace
{
	/**
	 * @param $text
	 * @param array $params
	 * @param bool $esc
	 * @return mixed
	 */
	function bkntc__( $text, $params = [], $esc = true )
	{
		$func = $esc ? 'esc_html__' : '__';

		if( empty( $params ) )
		{
			return $func($text, 'booknetic');
		}
		else
		{
			$args = array_merge( [ $func($text, 'booknetic') ] , (array)$params );
			return call_user_func_array('sprintf', $args );
		}
	}
}

namespace BookneticApp\Providers
{

	use BookneticApp\Integrations\WooCommerce\WooCommerceService;

	/**
	 * Class Bootstrap
	 * @package BookneticApp
	 */
	class Bootstrap
	{

		/**
		 * Bootstrap constructor.
		 */
		public function __construct()
		{
			$this->loadPluginTextDomain();

			if( !static::isInstalled() )
			{
				add_action('init', [$this, 'init_installation']);
			}
			else
			{
				$this->getNotifications();

				if ( $this->checkLicense() === false )
				{
					add_action('init', [$this, 'init_disabled_page']);
				}
				else
				{
					add_action('init', [$this, 'init_app']);
				}
			}
		}

		public function loadPluginTextDomain()
		{
			add_action( 'plugins_loaded', function()
			{
				$path = 'booknetic/languages';

				if( Helper::isSaaSVersion() && Permission::tenantId() > 0 && file_exists( WP_PLUGIN_DIR . '/' . $path . '/' . Permission::tenantId() . '/booknetic-' . get_locale() . '.mo' ) )
				{
					$path .= '/' . Permission::tenantId();
				}

				load_plugin_textdomain( 'booknetic', FALSE, $path );
			});
		}

		public function init_app()
		{
			CronJob::init();

			if ( !is_admin() || ( Helper::is_ajax() && !Helper::is_update_process() ) )
			{
				Frontend::init();
			}
			else if( is_admin() )
			{
				add_filter('woocommerce_prevent_admin_access', function ()
				{
					return false;
				});

				Backend::init();
			}

			WooCommerceService::init();

			add_role( 'booknetic_customer', bkntc__('Booknetic Customers'), [
				'read'         => false,
				'edit_posts'   => false,
				'upload_files' => false,
			]);

			$getTenantRole = get_role( 'booknetic_staff' );
			$createTenantRole = !$getTenantRole;

			if( $getTenantRole && !$getTenantRole->has_cap('read') )
			{
				remove_role('booknetic_staff');
				$createTenantRole = true;
			}

			if( $createTenantRole )
			{
				add_role( 'booknetic_staff', bkntc__('Booknetic Staff'), [
					'read'         => true,
					'edit_posts'   => false,
					'upload_files' => false
				]);
			}
		}

		public function init_installation()
		{
			if( is_admin() )
			{
				Backend::initInstallation();
			}
		}

		public function init_disabled_page()
		{
			if( is_admin() )
			{
				Backend::initDisabledPage();
			}
		}

		public static function isInstalled()
		{
			$purchase_code = Helper::getOption('purchase_code', '', false);
			$version = Helper::getOption('plugin_version', '', false);

			if( empty( $purchase_code ) || empty( $version ) )
				return false;

			return true;
		}

		private function getNotifications ()
		{
			$lastTime = Helper::getOption( 'license_last_checked_time', 0, false );

			if ( time() - $lastTime < 10 * 60 * 60 )
			{
				return;
			}

			$purchaseCode = Helper::getOption( 'purchase_code', '', false );

			$checkPurchaseCodeURL = Backend::API_URL . "?act=get_notifications&purchase_code=" . $purchaseCode . "&domain=" . site_url();
			$result2              = Curl::getURL( $checkPurchaseCodeURL );
			$result               = json_decode( $result2, true );

			if ( ! isset( $result ) || empty( $result ) )
			{
				return;
			}

			if ( $result[ 'action' ] === 'empty' )
			{
				Helper::setOption( 'plugin_alert', '', false );
				Helper::setOption( 'plugin_disabled', '0', false );
			}
			else if ( $result[ 'action' ] === 'warning' && ! empty( $result[ 'message' ] ) )
			{
				Helper::setOption( 'plugin_alert', $result[ 'message' ], false );
				Helper::setOption( 'plugin_disabled', '0', false );
			}
			else if ( $result[ 'action' ] === 'disable' )
			{
				if ( ! empty( $result[ 'message' ] ) )
				{
					Helper::setOption( 'plugin_alert', $result[ 'message' ], false );
				}

				Helper::setOption( 'plugin_disabled', '1', false );
			}
			else if ( $result[ 'action' ] === 'error' )
			{
				if ( ! empty( $result[ 'message' ] ) )
				{
					Helper::setOption( 'plugin_alert', $result[ 'message' ], false );
				}

				Helper::setOption( 'plugin_disabled', '2', false );
			}

			if ( ! empty( $result[ 'remove_license' ] ) )
			{
				Helper::deleteOption( 'purchase_code', false );
			}

			Helper::setOption( 'license_last_checked_time', time(), false );
		}

		private function checkLicense()
		{
			$alert    = Helper::getOption( 'plugin_alert', '', false );
			$disabled = Helper::getOption( 'plugin_disabled', '0', false );

			if ( $disabled === '1' )
			{
				return false;
			}
			else if ( $disabled === '2' )
			{
				if ( ! empty( $alert ) )
				{
					echo $alert;
				}

				exit();
			}

			if ( ! empty( $alert ) )
			{
				add_action( 'admin_notices', function () use ( $alert )
				{
					print '<div class="notice notice-error"><p>'.$alert.'</p></div>';
				});
			}

			return true;
		}

	}

}


