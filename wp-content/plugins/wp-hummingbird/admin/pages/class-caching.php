<?php
/**
 * Caching pages: page caching, browser caching, gravatar caching, rss caching, settings for page caching.
 *
 * @package Hummingbird
 *
 * @since 1.9.0  Refactored to run admin page actions in order (first - register_meta_boxes, second - on_load, etc).
 */

namespace Hummingbird\Admin\Pages;

use Hummingbird\Admin\Page;
use Hummingbird\Core\Integration\Opcache;
use Hummingbird\Core\Module_Server;
use Hummingbird\Core\Modules\Page_Cache;
use Hummingbird\Core\Settings;
use Hummingbird\Core\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Caching
 *
 * @property array tabs
 */
class Caching extends Page {

	use \Hummingbird\Core\Traits\WPConfig;

	/**
	 * Current report.
	 *
	 * @since  1.5.3
	 * @var    array $report
	 * @access private
	 */
	private $report;

	/**
	 * Number of issues.
	 *
	 * If Cloudflare is enabled will calculate number of issues for it, if not - number of local issues.
	 *
	 * @since 1.5.3
	 * @var   int $issues  Default 0.
	 */
	private $issues = 0;

	/**
	 * Settings expiration values.
	 *
	 * @since 1.5.3
	 * @var   array $expires
	 */
	private $expires;

	/**
	 * Cloudflare module status.
	 *
	 * @since  1.5.3
	 * @var    bool $cloudflare  Default false.
	 * @access private
	 */
	private $cloudflare = false;

	/**
	 * If site is using Cloudflare.
	 *
	 * @since 1.7.1
	 * @var   bool $cf_server
	 */
	private $cf_server = false;

	/**
	 * Cloudflare expiration value.
	 *
	 * @since  1.5.3
	 * @var    int $expiration Default 0.
	 * @access private
	 */
	private $expiration = 0;

	/**
	 * If .htaccess is written by the module.
	 *
	 * @var bool
	 */
	private $htaccess_written = false;

	/**
	 * Register meta boxes for the page.
	 */
	public function register_meta_boxes() {
		/**
		 * PAGE CACHING META BOXES.
		 */
		$caching_callback = false;
		if ( ( is_multisite() && is_network_admin() ) || ! is_multisite() ) {
			/**
			 * SUMMARY META BOX
			 */
			$this->add_meta_box(
				'summary',
				null,
				array( $this, 'caching_summary' ),
				null,
				null,
				'main',
				array(
					'box_class'         => 'sui-box sui-summary',
					'box_content_class' => false,
				)
			);

			// Main site.
			$caching_callback = true;
		} elseif ( is_super_admin() || 'blog-admins' === Settings::get_setting( 'enabled', 'page_cache' ) ) {
			// Sub sites.
			$caching_callback = true;
		}

		/**
		 * PAGE CACHE META BOXES
		 */
		if ( $caching_callback && Utils::get_module( 'page_cache' )->is_active() ) {
			$footer = ( is_multisite() && is_network_admin() ) || ! is_multisite();
			$this->add_meta_box(
				'caching/page-caching',
				__( 'Page Caching', 'wphb' ),
				array( $this, 'page_caching_metabox' ),
				array( $this, 'page_caching_metabox_header' ),
				$footer ? array( $this, 'page_caching_metabox_footer' ) : null,
				'page_cache'
			);
		} elseif ( $caching_callback ) {
			$this->add_meta_box(
				'caching/page-caching-disabled',
				__( 'Page Caching', 'wphb' ),
				array( $this, 'page_caching_disabled_metabox' ),
				null,
				null,
				'page_cache',
				array( 'box_content_class' => 'sui-box sui-message' )
			);
		}

		// Do not continue on subsites.
		if ( is_multisite() && ! is_network_admin() ) {
			return;
		}

		/**
		 * BROWSER CACHING META BOXES.
		 */
		$this->add_meta_box(
			'caching-status',
			__( 'Status', 'wphb' ),
			array( $this, 'caching_summary_metabox' ),
			array( $this, 'caching_summary_metabox_header' ),
			null,
			'caching'
		);

		$this->add_meta_box(
			'caching-settings',
			__( 'Configure', 'wphb' ),
			array( $this, 'caching_settings_metabox' ),
			array( $this, 'caching_settings_metabox_header' ),
			null,
			'caching'
		);

		/**
		 * GRAVATAR CACHING META BOXES.
		 */
		if ( Utils::get_module( 'gravatar' )->is_active() ) {
			$this->add_meta_box(
				'caching/gravatar',
				__( 'Gravatar Caching', 'wphb' ),
				array( $this, 'caching_gravatar_metabox' ),
				null,
				null,
				'gravatar'
			);
		} else {
			$this->add_meta_box(
				'gravatar-disabled',
				__( 'Gravatar Caching', 'wphb' ),
				array( $this, 'caching_gravatar_disabled_metabox' ),
				null,
				null,
				'gravatar',
				array( 'box_content_class' => 'sui-box sui-message' )
			);
		}

		/**
		 * RSS CACHING META BOXES.
		 */
		$this->add_meta_box(
			Utils::get_module( 'rss' )->is_active() ? 'caching/rss' : 'caching/rss-disabled',
			__( 'RSS Caching', 'wphb' ),
			array( $this, 'caching_rss_metabox' ),
			null,
			function () {
				$this->view( 'caching/meta-box-footer', array() );
			},
			'rss'
		);

		/**
		 * INTEGRATION META BOXES.
		 *
		 * @since 2.5.0
		 */
		$this->add_meta_box(
			'integrations',
			__( 'Integrations', 'wphb' ),
			array( $this, 'integrations_metabox' ),
			null,
			null,
			'integrations'
		);

		/**
		 * SETTINGS META BOX
		 */
		$this->add_meta_box(
			'caching/other-settings',
			__( 'Settings', 'wphb' ),
			array( $this, 'settings_metabox' ),
			null,
			function () {
				$this->view( 'caching/meta-box-footer', array() );
			},
			'settings'
		);
	}

	/**
	 * Function triggered when the page is loaded before render any content.
	 *
	 * @since 1.7.0
	 * @since 1.9.0  Moved here from init().
	 */
	public function on_load() {
		$this->tabs = array(
			'page_cache'   => __( 'Page Caching', 'wphb' ),
			'caching'      => __( 'Browser Caching', 'wphb' ),
			'gravatar'     => __( 'Gravatar Caching', 'wphb' ),
			'rss'          => __( 'RSS Caching', 'wphb' ),
			'integrations' => __( 'Integrations', 'wphb' ),
			'settings'     => __( 'Settings', 'wphb' ),
		);

		// Remove modules that are not used on subsites in a network.
		if ( is_multisite() && ! is_network_admin() ) {
			unset( $this->tabs['caching'] );
			unset( $this->tabs['gravatar'] );
			unset( $this->tabs['rss'] );
			unset( $this->tabs['integrations'] );
			unset( $this->tabs['settings'] );

			// Don't run anything else.
			return;
		}

		// We need to update the status on all pages, for the menu icons to function properly.
		$this->update_cache_status();
	}

	/**
	 * Execute an action for specified module.
	 *
	 * Action will execute if:
	 * - Both action and module vars are defined;
	 * - Action is available as a methods in a selected module.
	 *
	 * Currently used actions: enable, disable, disconnect.
	 * Currently supported modules: page_cache, caching, cloudflare, gravatar, rss.
	 *
	 * @since 1.9.0  Moved here from on_load().
	 */
	public function trigger_load_action() {
		parent::trigger_load_action();

		if ( ! isset( $_GET['action'] ) || ! isset( $_GET['module'] ) ) { // Input var ok.
			return;
		}

		check_admin_referer( 'wphb-caching-actions' );
		$action = sanitize_text_field( wp_unslash( $_GET['action'] ) ); // Input var ok.
		$module = sanitize_text_field( wp_unslash( $_GET['module'] ) ); // Input var ok.

		// If unsupported module - exit.
		$mod = Utils::get_module( $module );

		// Allow only supported actions.
		if ( ! $mod || ! in_array( $action, array( 'enable', 'disable', 'disconnect' ), true ) ) {
			return;
		}

		if ( method_exists( $mod, $action ) ) {
			call_user_func( array( $mod, $action ) );
		}

		// Cloudflare module is located on caching page.
		$module = 'cloudflare' === $module ? 'caching' : $module;

		$redirect_url = add_query_arg( array( 'view' => $module ), Utils::get_admin_menu_url( 'caching' ) );

		if ( 'enable' === $action && 'caching' === $module ) {
			$redirect_url = add_query_arg( array( 'enabled' => true ), $redirect_url );
		} elseif ( 'disable' === $action && 'caching' === $module ) {
			$redirect_url = add_query_arg( array( 'disabled' => true ), $redirect_url );
		}
		wp_safe_redirect( $redirect_url );
	}

	/**
	 * Hooks for caching pages.
	 *
	 * @since 1.9.0
	 */
	public function add_screen_hooks() {
		parent::add_screen_hooks();

		// Icons in the submenu.
		add_filter( 'wphb_admin_after_tab_' . $this->get_slug(), array( $this, 'after_tab' ) );

		// Redis notice text.
		add_filter( 'wphb_update_notice_text', array( $this, 'redis_notice_update_text' ) );
	}

	/**
	 * Overwrites parent class render_header method.
	 *
	 * Renders the template header that is repeated on every page.
	 * From WPMU DEV Dashboard
	 */
	public function render_header() {
		if ( filter_input( INPUT_GET, 'enabled' ) ) {
			$this->admin_notices->show_floating( __( 'Browser cache enabled. Your .htaccess file has been updated', 'wphb' ) );
		} elseif ( filter_input( INPUT_GET, 'disabled' ) ) {
			$this->admin_notices->show_floating( __( 'Browser cache disabled. Your .htaccess file has been updated', 'wphb' ) );
		}

		parent::render_header();
	}

	/**
	 * Init browser cache settings.
	 *
	 * @since 1.8.1
	 */
	private function update_cache_status() {
		$options = Settings::get_settings( 'caching' );

		$this->expires = array(
			'css'        => $options['expiry_css'],
			'javascript' => $options['expiry_javascript'],
			'media'      => $options['expiry_media'],
			'images'     => $options['expiry_images'],
		);

		/**
		 * Check Cloudflare status.
		 *
		 * If Cloudflare is active, we store the values of CLoudFlare caching settings to the report variable.
		 * Else - we store the local setting in the report variable. That way we don't have to query and check
		 * later on what report to show to the user.
		 */
		$cf_module = Utils::get_module( 'cloudflare' );

		$this->cf_server  = $cf_module->has_cloudflare();
		$this->cloudflare = $cf_module->is_connected() && $cf_module->is_zone_selected();

		if ( $this->cloudflare ) {
			$this->expiration = $cf_module->get_caching_expiration();
			// Fill the report with values from Cloudflare.
			$this->report = array_fill_keys( array_keys( $this->expires ), $this->expiration );
			// Save status.
			$this->cf_server = $cf_module->has_cloudflare();
			// Get number of issues.
			if ( YEAR_IN_SECONDS > $this->expiration ) {
				$this->issues = count( $this->report ) + 1; // One additional issue for Cloudflare.
			}
			return;
		}

		/*
		 * Remove no-background-image class on the meta box.
		 * We do it here, because register_meta_boxes() is fired before this code and there's no way to get CF status.
		 */
		$cf_notice = get_site_option( 'wphb-cloudflare-dash-notice' );
		if ( ! $cf_notice && 'dismissed' !== $cf_notice ) {
			$this->meta_boxes[ $this->get_slug() ]['caching']['caching-status']['args']['box_content_class'] = 'sui-box-body sui-upsell-items';
		}

		$mod = Utils::get_module( 'caching' );
		$mod->get_analysis_data();

		// Get latest local report.
		$this->report = $mod->status;

		// Get number of issues.
		$this->htaccess_written = Module_Server::is_htaccess_written( 'caching' );
		$this->issues           = Utils::get_number_of_issues( 'caching', $this->report );
	}

	/**
	 * We need to insert an extra label to the tabs sometimes
	 *
	 * @param string $tab Current tab.
	 */
	public function after_tab( $tab ) {
		if ( 'caching' === $tab ) {
			$issues = 0;
			if ( ! $this->cloudflare ) {
				$issues = Utils::get_number_of_issues( 'caching', $this->report );
			} elseif ( YEAR_IN_SECONDS > $this->expiration ) {
				$issues = count( $this->report );
				// Add an issue for the CloudFlare type.
				$issues++;
			}

			if ( 0 !== $issues ) {
				echo '<span class="sui-tag sui-tag-warning">' . absint( $issues ) . '</span>';
				return;
			}

			echo '<span class="sui-icon-check-tick sui-success" aria-hidden="true"></span>';
			return;
		}

		$tab = 'integrations' === $tab ? 'redis' : $tab;

		// Available modules.
		if ( ! in_array( $tab, array( 'gravatar', 'page_cache', 'rss', 'redis' ), true ) ) {
			return;
		}

		$module = Utils::get_module( $tab );

		if ( $module->is_active() && ( ! isset( $module->error ) || ! is_wp_error( $module->error ) ) ) {
			echo '<span class="sui-icon-check-tick sui-success" aria-hidden="true"></span>';
		} elseif ( isset( $module->error ) && is_wp_error( $module->error ) ) {
			echo '<span class="sui-icon-warning-alert sui-warning" aria-hidden="true"></span>';
		}
	}

	/**
	 * Check to see if caching is fully enabled.
	 *
	 * @access private
	 * @return bool
	 */
	private function is_caching_fully_enabled() {
		$result_sum  = 0;
		$recommended = Utils::get_module( 'caching' )->get_recommended_caching_values();

		foreach ( $this->report as $key => $result ) {
			if ( $result >= $recommended[ $key ]['value'] ) {
				$result_sum++;
			}
		}

		return count( $this->report ) === $result_sum;
	}

	/**
	 * *************************
	 * CACHING SUMMARY
	 *
	 * @since 1.9.1
	 ***************************/

	/**
	 * Caching summary meta box.
	 */
	public function caching_summary() {
		$this->view(
			'caching/summary-meta-box',
			array(
				'pc_active'       => Utils::get_module( 'page_cache' )->is_active(),
				'cached'          => Settings::get_setting( 'pages_cached', 'page_cache' ),
				'issues'          => $this->issues,
				'gravatar'        => Utils::get_module( 'gravatar' )->is_active(),
				'rss'             => Settings::get_setting( 'duration', 'rss' ),
				'preload_running' => get_transient( 'wphb-preloading' ),
				'preload_active'  => Settings::get_setting( 'preload', 'page_cache' ),
			)
		);
	}

	/**
	 * *************************
	 * PAGE CACHING
	 *
	 * @since 1.7.0
	 ***************************/

	/**
	 * Disabled page caching meta box.
	 */
	public function page_caching_disabled_metabox() {
		$this->view(
			'caching/page/disabled-meta-box',
			array(
				'activate_url' => wp_nonce_url(
					add_query_arg(
						array(
							'action' => 'enable',
							'module' => 'page_cache',
						)
					),
					'wphb-caching-actions'
				),
			)
		);
	}

	/**
	 * Page caching meta box.
	 */
	public function page_caching_metabox() {
		$module  = Utils::get_module( 'page_cache' );
		$options = $module->get_options();

		$common_args = array(
			'error'          => $module->error,
			'deactivate_url' => wp_nonce_url(
				add_query_arg(
					array(
						'action' => 'disable',
						'module' => 'page_cache',
					)
				),
				'wphb-caching-actions'
			),
			'minify_active'  => Utils::get_module( 'minify' )->is_active(),
		);

		if ( ( is_multisite() && is_network_admin() ) || ! is_multisite() ) {
			$custom_post_types = array();
			$settings          = $module->get_settings();
			if ( isset( $settings['custom_post_types'] ) ) {
				$custom_post_types = $settings['custom_post_types'];
			}
			$settings['custom_post_types'] = $custom_post_types;

			$log = WP_CONTENT_DIR . '/wphb-logs/page-caching-log.php';
			if ( ! file_exists( $log ) ) {
				$log = false;
			} else {
				$log = content_url() . '/wphb-logs/page-caching-log.php';
			}

			$opcache = Opcache::get_instance();

			$gzip = Utils::get_module( 'gzip' )->get_analysis_data();

			$args = array(
				'settings'           => $settings,
				'clear_interval'     => Utils::format_interval_hours( $settings['clear_interval']['interval'] ),
				'options'            => $options,
				'admins_can_disable' => 'blog-admins' === $options['enabled'],
				'blog_is_frontpage'  => 'posts' === get_option( 'show_on_front' ) && ! is_multisite(),
				'opcache_enabled'    => $opcache->is_enabled(),
				'pages'              => Page_Cache::get_page_types(),
				'can_compress'       => ! isset( $gzip['HTML'] ) || ! $gzip['HTML'],
				'custom_post_types'  => get_post_types(
					array(
						'public'   => true,
						'_builtin' => false,
					),
					'objects'
				),
				'logs_link'          => $log,
				'download_url'       => wp_nonce_url(
					add_query_arg(
						array(
							'logs'   => 'download',
							'module' => $module->get_slug(),
						)
					),
					'wphb-log-action'
				),
			);

			$this->view( 'caching/page/meta-box', wp_parse_args( $args, $common_args ) );
		} elseif ( is_super_admin() || 'blog-admins' === $options['enabled'] ) {
			$args = array(
				'can_deactivate' => 'blog-admins' === $options['enabled'],
			);

			$this->view( 'caching/page/subsite-meta-box', wp_parse_args( $args, $common_args ) );
		}
	}

	/**
	 * Page caching header meta box.
	 *
	 * @since 2.7.1
	 */
	public function page_caching_metabox_header() {
		$this->view( 'caching/page/meta-box-header', array( 'title' => __( 'Page Caching', 'wphb' ) ) );
	}

	/**
	 * Page caching footer meta box.
	 *
	 * @since 2.7.1
	 */
	public function page_caching_metabox_footer() {
		$this->view( 'caching/page/meta-box-footer', array() );
	}

	/**
	 * *************************
	 * BROWSER CACHING
	 *
	 * @since forever
	 ***************************/

	/**
	 * Display header for caching summary meta box.
	 */
	public function caching_summary_metabox_header() {
		$issues = 0;
		if ( ! $this->cloudflare ) {
			$issues = Utils::get_number_of_issues( 'caching', $this->report );
		} elseif ( YEAR_IN_SECONDS > $this->expiration ) {
			// Add an issue for the CloudFlare type.
			$issues = count( $this->report ) + 1;
		}

		$this->view(
			'caching/browser/meta-box-header',
			array(
				'title'  => __( 'Status', 'wphb' ),
				'issues' => $issues,
			)
		);
	}

	/**
	 * Render caching meta box.
	 */
	public function caching_summary_metabox() {
		// Defaults.
		$htaccess_issue = false;
		$show_cf_notice = false;

		// Check if .htaccess file has rules included.
		if ( $this->htaccess_written && in_array( false, $this->report, true ) ) {
			$htaccess_issue = true;
		}

		$cf_status = get_site_option( 'wphb-cloudflare-dash-notice' );
		$cf_module = Utils::get_module( 'cloudflare' );
		if ( ( ! $cf_module->is_connected() || ! $cf_module->is_zone_selected() ) && ! $cf_status && 'dismissed' !== $cf_status ) {
			$show_cf_notice = true;
		}
		$cf_notice = $this->cf_server ? __( 'Ahoi, we’ve detected you’re using CloudFlare!', 'wphb' ) : __( 'Using CloudFlare?', 'wphb' );

		$caching = Utils::get_module( 'caching' );

		$this->view(
			'caching/browser/meta-box',
			array(
				'htaccess_issue'        => $htaccess_issue,
				'results'               => $this->report,
				'issues'                => $this->issues,
				'human_results'         => array_map( array( 'Hummingbird\\Core\\Utils', 'human_read_time_diff' ), $this->report ),
				'recommended'           => $caching->get_recommended_caching_values(),
				'show_cf_notice'        => $show_cf_notice,
				'cf_notice'             => $cf_notice,
				'cf_server'             => $this->cf_server,
				'cf_active'             => $this->cloudflare,
				'caching_type_tooltips' => $caching->get_types(),
			)
		);
	}

	/**
	 * Display browser caching settings header meta box.
	 */
	public function caching_settings_metabox_header() {
		$this->view(
			'caching/browser/configure-meta-box-header',
			array(
				'title'     => __( 'Configure', 'wphb' ),
				'cf_active' => $this->cloudflare,
			)
		);
	}

	/**
	 * Display browser caching settings meta box.
	 */
	public function caching_settings_metabox() {
		$show_cf_notice    = false;
		$htaccess_writable = Module_Server::is_htaccess_writable();
		$server_type       = Module_Server::get_server_type();

		// Server code snippets.
		$snippets = array(
			'apache' => Module_Server::get_code_snippet( 'caching', 'apache' ),
			'nginx'  => Module_Server::get_code_snippet( 'caching', 'nginx' ),
			'iis'    => Module_Server::get_code_snippet( 'caching', 'iis' ),
		);

		// Default to show Cloudflare or Apache if set up.
		if ( $this->cloudflare ) {
			$server_type = 'cloudflare';
			// Clear cached status.
			Utils::get_module( 'caching' )->clear_cache();
		} elseif ( $this->cf_server ) {
			$server_type = 'cloudflare';
			$cf_module   = Utils::get_module( 'cloudflare' );
			if ( ! ( $cf_module->is_active() && $cf_module->is_connected() && $cf_module->is_zone_selected() ) ) {
				if ( get_site_option( 'wphb-cloudflare-dash-notice' ) && 'dismissed' === get_site_option( 'wphb-cloudflare-dash-notice' ) ) {
					$show_cf_notice = true;
				}
			}
		} elseif ( $htaccess_writable && $this->htaccess_written ) {
			$server_type = 'apache';
		}

		$labels = array(
			'javascript' => 'JavaScript',
			'images'     => 'Images',
			'css'        => 'CSS',
			'media'      => 'Media',
		);

		$this->view(
			'caching/browser/configure-meta-box',
			array(
				'results'            => $this->report,
				'labels'             => $labels,
				'human_results'      => array_map( array( 'Hummingbird\\Core\\Utils', 'human_read_time_diff' ), $this->report ),
				'expires'            => $this->expires,
				'different_expiry'   => 1 >= count( array_unique( array_values( $this->expires ) ) ),
				'server_type'        => $server_type,
				'snippets'           => $snippets,
				'htaccess_written'   => $this->htaccess_written,
				'htaccess_writable'  => $htaccess_writable,
				'already_enabled'    => $this->is_caching_fully_enabled() && ! $this->htaccess_written,
				'cf_active'          => $this->cloudflare,
				'cf_server'          => $this->cf_server,
				'cf_current'         => $this->expiration,
				'all_expiry'         => count( array_unique( $this->expires ) ) === 1,
				'show_cf_notice'     => $show_cf_notice,
				'recheck_expiry_url' => add_query_arg( 'run', 'true' ),
				'cf_disable_url'     => wp_nonce_url(
					add_query_arg(
						array(
							'action' => 'disconnect',
							'module' => 'cloudflare',
						)
					),
					'wphb-caching-actions'
				),
				'enable_link'        => wp_nonce_url(
					add_query_arg(
						array(
							'action' => 'enable',
							'module' => 'caching',
						)
					),
					'wphb-caching-actions'
				),
				'disable_link'       => wp_nonce_url(
					add_query_arg(
						array(
							'action' => 'disable',
							'module' => 'caching',
						)
					),
					'wphb-caching-actions'
				),
			)
		);
	}

	/**
	 * *************************
	 * GRAVATAR CACHING
	 *
	 * @since 1.5.0
	 ***************************/

	/**
	 * Disabled Gravatar caching meta box.
	 *
	 * @since 1.5.3
	 */
	public function caching_gravatar_disabled_metabox() {
		$this->view(
			'caching/gravatar/disabled-meta-box',
			array(
				'activate_url' => wp_nonce_url(
					add_query_arg(
						array(
							'action' => 'enable',
							'module' => 'gravatar',
						)
					),
					'wphb-caching-actions'
				),
			)
		);
	}

	/**
	 * Gravatar meta box.
	 */
	public function caching_gravatar_metabox() {
		$module = Utils::get_module( 'gravatar' );

		$this->view(
			'caching/gravatar/meta-box',
			array(
				'module_active'  => $module->is_active(),
				'error'          => $module->error,
				'deactivate_url' => wp_nonce_url(
					add_query_arg(
						array(
							'action' => 'disable',
							'module' => 'gravatar',
						)
					),
					'wphb-caching-actions'
				),
			)
		);
	}

	/**
	 * *************************
	 * RSS CACHING
	 *
	 * @since 1.8
	 ***************************/

	/**
	 * Display Rss caching meta box.
	 */
	public function caching_rss_metabox() {
		$active = Utils::get_module( 'rss' )->is_active();

		$args = array(
			'url' => wp_nonce_url(
				add_query_arg(
					array(
						'action' => $active ? 'disable' : 'enable',
						'module' => 'rss',
					)
				),
				'wphb-caching-actions'
			),
		);

		$meta_box = 'caching/rss/disabled-meta-box';
		if ( $active ) {
			$meta_box         = 'caching/rss/meta-box';
			$args['duration'] = Settings::get_setting( 'duration', 'rss' );
		}

		$this->view( $meta_box, $args );
	}

	/**
	 * *************************
	 * INTEGRATIONS
	 *
	 * @since 2.5.0
	 ***************************/

	/**
	 * Display integrations meta box.
	 */
	public function integrations_metabox() {
		$redis_mod  = Utils::get_module( 'redis' );
		$redis_vars = $redis_mod->get_status_related_vars();
		$this->view(
			'caching/integrations/meta-box',
			array(
				'redis_connected'       => $redis_vars['redis_connected'],
				'redis_enabled'         => $redis_vars['redis_enabled'],
				'is_redis_object_cache' => $redis_vars['is_redis_object_cache'],
				'disable_redis'         => $redis_vars['disable_redis'],
				'error'                 => $redis_vars['connection_error'],
			)
		);
	}

	/**
	 * Adjust Redis notice text (update/save changes) according to design.
	 *
	 * @param string $text  Current notice text.
	 *
	 * @return string
	 */
	public function redis_notice_update_text( $text ) {
		$updated = filter_input( INPUT_GET, 'updated', FILTER_SANITIZE_STRING );

		if ( 0 === strpos( $updated, 'redis' ) ) {
			return Utils::get_module( 'redis' )->get_update_notice( $updated );
		}

		return $text;
	}

	/**
	 * *************************
	 * SETTINGS
	 *
	 * @since 1.8.1
	 ***************************/

	/**
	 * Display settings meta box.
	 */
	public function settings_metabox() {
		$page_cache = Settings::get_settings( 'page_cache' );
		$this->view(
			'caching/settings/meta-box',
			array(
				'enabled'   => $page_cache['enabled'],
				'control'   => $page_cache['control'],
				'detection' => $page_cache['detection'],
			)
		);
	}

}