<?php

function wpmu_add_new_interval() {
return array(
    'every_fifteen_minutes' => array('interval' => 15 * 60, 'display' => 'Every 15 minutes'),
);
}
add_filter('cron_schedules', 'wpmu_add_new_interval');

add_action( 'init', 'register_reminders_event');

function register_reminders_event() {
	if( !wp_next_scheduled( 'send_reminders_event' ) ) {
		wp_schedule_event( time(), 'every_fifteen_minutes', 'send_reminders_event' );
	}
}

function send_reminders_event() {
	file_get_contents("https://binbuddy.co.nz/wp-cron.php?doing_wp_cron");
}
add_action('send_reminders_event', 'send_reminders_event');
