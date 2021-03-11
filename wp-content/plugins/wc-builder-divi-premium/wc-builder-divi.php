<?php

/*
Plugin Name: WooCommerce Builder For Divi
Plugin URI:  https://www.divikingdom.com
Description: Build amazing WooCommerce pages using Divi builder.
Version:     3.2.0
Author:      Abdelfatah Aboelghit | DiviKingdom.Com
Author URI:  https://www.divikingdom.com
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: wc-builder-divi
Domain Path: /languages
WC requires at least: 3.2.0
WC tested up to: 4.8.0
*/
// exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
    exit;
}
/* Freemius SDK Start */
if ( file_exists( dirname( __FILE__ ) . '/freemius/start.php' ) ) {
    
    if ( !function_exists( 'wcbd_fs' ) ) {
        // Create a helper function for easy SDK access.
        function wcbd_fs()
        {
            global  $wcbd_fs ;
            
            if ( !isset( $wcbd_fs ) ) {
                // Activate multisite network integration.
                if ( !defined( 'WP_FS__PRODUCT_2590_MULTISITE' ) ) {
                    define( 'WP_FS__PRODUCT_2590_MULTISITE', true );
                }
                // Include Freemius SDK.
                require_once dirname( __FILE__ ) . '/freemius/start.php';
                $wcbd_fs = fs_dynamic_init( array(
                    'id'               => '2590',
                    'slug'             => 'wc-builder-divi',
                    'type'             => 'plugin',
                    'public_key'       => 'pk_533ce5ac16349b4c01d8e3be4176f',
                    'is_premium'       => true,
                    'is_premium_only'  => true,
                    'has_addons'       => false,
                    'has_paid_plans'   => true,
                    'is_org_compliant' => false,
                    'has_affiliation'  => 'selected',
                    'menu'             => array(
                    'slug'        => 'wc-builder-divi',
                    'contact'     => false,
                    'support'     => false,
                    'affiliation' => false,
                ),
                    'is_live'          => true,
                ) );
            }
            
            return $wcbd_fs;
        }
        
        // Init Freemius.
        wcbd_fs();
        // Signal that SDK was initiated.
        do_action( 'wcbd_fs_loaded' );
    }

}
/* Freemius SDK End */
if ( !defined( 'WCBD_PLUGIN_NAME' ) ) {
    define( 'WCBD_PLUGIN_NAME', 'WooCommerce Builder' );
}
if ( !defined( 'WCBD_PLUGIN_PATH' ) ) {
    define( 'WCBD_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
}
if ( !defined( 'WCBD_PLUGIN_URL' ) ) {
    define( 'WCBD_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}
if ( !defined( 'WCBD_PLUGIN_FILE' ) ) {
    define( 'WCBD_PLUGIN_FILE', __FILE__ );
}
if ( !defined( 'WCBD_PRODUCT_URL' ) ) {
    define( 'WCBD_PRODUCT_URL', 'https://www.divikingdom.com/product/woocommerce-builder-divi/' );
}
if ( !defined( 'WCBD_AUTHOR' ) ) {
    define( 'WCBD_AUTHOR', 'DiviKingdom.Com' );
}
if ( !defined( 'DIVIKINGDOM_URL' ) ) {
    define( 'DIVIKINGDOM_URL', 'https://www.divikingdom.com' );
}
if ( !defined( 'WCBD_PRODUCT_LAYOUT_KEY' ) ) {
    define( 'WCBD_PRODUCT_LAYOUT_KEY', '_single_product_divi_layout' );
}
if ( !defined( 'WCBD_ARCHIVES_POST_TYPE' ) ) {
    define( 'WCBD_ARCHIVES_POST_TYPE', 'wcbd_archive_layout' );
}
// helper functions
require WCBD_PLUGIN_PATH . 'includes/classes/helpers.class.php';
require WCBD_PLUGIN_PATH . 'includes/functions/main-functions.php';
// load classes
require WCBD_PLUGIN_PATH . 'includes/classes/init.class.php';
require WCBD_PLUGIN_PATH . 'includes/classes/settings.class.php';
require WCBD_PLUGIN_PATH . 'includes/classes/metabox.class.php';
require WCBD_PLUGIN_PATH . 'includes/classes/shortcodes.class.php';

if ( !function_exists( 'wcbd_initialize_extension' ) ) {
    /**
     * Creates the extension's main class instance.
     *
     * @since 2.0.0
     */
    function wcbd_initialize_extension()
    {
        require_once plugin_dir_path( __FILE__ ) . 'includes/WcBuilderDivi.php';
    }
    
    add_action( 'divi_extensions_init', 'wcbd_initialize_extension' );
}
