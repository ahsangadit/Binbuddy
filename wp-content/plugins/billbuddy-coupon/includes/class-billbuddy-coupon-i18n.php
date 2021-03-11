<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://hztech.biz
 * @since      1.0.0
 *
 * @package    Billbuddy_Coupon
 * @subpackage Billbuddy_Coupon/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Billbuddy_Coupon
 * @subpackage Billbuddy_Coupon/includes
 * @author     HZTECH <info@hztech.biz>
 */
class Billbuddy_Coupon_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'billbuddy-coupon',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
