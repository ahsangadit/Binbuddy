<?php
/*
 * Plugin Name: Booknetic
 * Description: WordPress Appointment Booking and Scheduling system
 * Version: 2.3.3
 * Author: FS-Code
 * Author URI: https://www.booknetic.com
 * License: Commercial
 * Text Domain: booknetic
 */

defined( 'ABSPATH' ) or exit;

require_once __DIR__ . '/vendor/autoload.php';

new \BookneticApp\Providers\Bootstrap();
