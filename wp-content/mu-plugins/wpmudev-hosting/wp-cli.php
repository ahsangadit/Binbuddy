<?php
/**
 * WPMU DEV Hosting WP CLI Functions
 *
 * Provides WP CLI functions that can be used inside the environment.
 */

if ( defined( 'WP_CLI' ) && WP_CLI ) {

	// Fixes network_*_url() always being http in PHP CLI
	if ( ! isset( $_SERVER['HTTPS'] ) ) {
		$_SERVER['HTTPS'] = 'on';
	}

	WP_CLI::add_command( 'wpmudev backup', 'WPMUDEV_Hosting_WPCLI_Backup' );
	WP_CLI::add_command( 'wpmudev staging', 'WPMUDEV_Hosting_WPCLI_Staging' );
	WP_CLI::add_command( 'wpmudev check', 'WPMUDEV_Hosting_WPCLI_Check' );
	WP_CLI::add_command( 'wpmudev doctor', 'WPMUDEV_Hosting_WPCLI_Doctor' );

	/**
	 * Gets the status of an action started by another command.
	 *
	 * ## OPTIONS
	 *
	 * <action_id>
	 * : The action_id as returned by backup or staging subcommand.
	 *
	 * [--field=<field>]
	 * : Prints the raw value of a single field for the action.
	 *
	 * [--fields=<fields>]
	 * : Limit the output to specific action object fields.
	 *
	 * [--format=<format>]
	 * : Render output in a particular format.
	 *   ---
	 *   default: table
	 *   options:
	 *     - table
	 *     - csv
	 *     - json
	 *     - yaml
	 *   ---
	 *
	 * ## AVAILABLE FIELDS
	 *
	 *  These fields will be displayed by default:
	 *
	 * * id
	 * * type
	 * * status
	 * * started_at
	 * * completed_at
	 *
	 * ## EXAMPLES
	 *
	 * # Check the action.
	 * $ wp wpmudev action 123
	 * +-----+--------+-----------+---------------------+---------------------+
	 * | id  | type   | status    | started_at          | completed_at        |
	 * +-----+--------+-----------+---------------------+---------------------+
	 * | 123 | backup | completed | 2018-11-14T07:30:45 | 2018-11-14T07:31:18 |
	 * +-----+--------+-----------+---------------------+---------------------+
	 *
	 *  # Check just the status, see possible responses.
	 *  $ wp wpmudev action 123 --field=status
	 *  in-progress|completed|errored
	 *
	 * @when after_wp_load
	 */
	$wpmudev_hosting_actions = function( $args, $assoc_args ) {
		$result = (array) wpmudev_hosting_api_call( sprintf( '%s/actions/%d', WPMUDEV_HOSTING_SITE_ID, $args[0] ), false, 'GET' );

		if ( isset( $assoc_args['field'] ) ) {
			if ( isset( $result[ $assoc_args['field'] ] ) ) {
				WP_CLI::line( $result[ $assoc_args['field'] ] );
				WP_CLI::halt( 0 );
			} else {
				WP_CLI::error( sprintf( "Invalid field '%s'. Must be one of 'id', 'type', 'status', 'started_at', 'completed_at'.", $assoc_args['field'] ) );
			}
		} elseif ( isset( $assoc_args['fields'] ) ) {
			$fields = $assoc_args['fields'];
		} else {
			$fields = array( 'id', 'type', 'status', 'started_at', 'completed_at' );
		}

		$format = isset( $assoc_args['format'] ) ? $assoc_args['format'] : 'table';
		WP_CLI\Utils\format_items( $format, array( $result ), $fields );
	};
	WP_CLI::add_command( 'wpmudev action', $wpmudev_hosting_actions );
}

/**
 * Makes an API call and returns the results.
 *
 * The remote_path can be either relative to the server_url or it can be
 * an absolute URL to any server.
 *
 * If remote_path is a relative path then the API-Key is automatically
 * added the URL.
 *
 * @since  4.0.0
 *
 * @param  string $remote_path The API function to call.
 * @param  array  $data        Optional. GET or POST data to send.
 * @param  string $method      Optional. GET or POST.
 * @param  array  $options     Optional. Array of request options.
 *
 * @return array Results of the wp_remote_get/post call.
 */
function wpmudev_hosting_api_call( $remote_path, $data = false, $method = 'GET', $options = array() ) {
	$link = 'https://premium.wpmudev.org/api/hosting/v1/' . $remote_path;

	// check prerequisites and that we have api key.
	if ( ! class_exists( 'WPMUDEV_Dashboard' ) ) {
		WP_CLI::error( 'The WPMU DEV Dashboard plugin must be installed and activated.' );
	}
	$api_key = WPMUDEV_Dashboard::$api->get_key();
	if ( empty( $api_key ) ) {
		WP_CLI::error( 'Please login to the WPMU DEV Dashboard plugin.' );
	}

	$options = wp_parse_args(
		$options,
		array(
			'timeout'    => 60,
			'user-agent' => 'WPMU DEV Hosting WP CLI Client/1.0 (+https://' . WPMUDEV_HOSTING_SITE_ID . '.wpmudev.host)',
		)
	);

	$options['headers']['Authorization'] = $api_key;

	WP_CLI::debug( 'API Url: ' . $link );
	WP_CLI::debug( 'API Options: ' . var_export( $options, true ) );

	if ( 'GET' === $method ) {
		if ( ! empty( $data ) ) {
			$link = add_query_arg( $data, $link );
		}
		$response = wp_remote_get( $link, $options );
	} elseif ( 'POST' === $method ) {
		$options['body'] = $data;
		$response        = wp_remote_post( $link, $options );
	}

	WP_CLI::debug( 'API Response: ' . var_export( $response, true ) );

	// check results.
	if ( is_wp_error( $response ) ) {
		WP_CLI::error( sprintf( 'API Error %s - %s', $response->get_error_code(), $response->get_error_message() ) );
	}

	$code = wp_remote_retrieve_response_code( $response );
	$body = json_decode( wp_remote_retrieve_body( $response ) );
	if ( '20' === substr( $code, 0, 2 ) && ! isset( $body->code ) ) {
		$response = $body;
	} elseif ( isset( $body->code ) ) {
		$message = isset( $body->message->message ) ? $body->message->message : $body->message;
		WP_CLI::error( sprintf( 'API Error %s - %s', $body->code, $message ) );
	} else {
		WP_CLI::error( sprintf( 'API Error %s - Unknown Error', $code ) );
	}

	return $response;
}

/**
 * Manage backups for this site.
 *
 * ## EXAMPLES
 *
 *  # Create a backup.
 *  $ wp wpmudev backup create
 *  Success: Backup initiated with action_id 123.
 *
 *  # List all completed backups.
 *  $ wp wpmudev backup list
 *
 *  # Restore a backup.
 *  $ wp wpmudev backup restore prod_incremental_20181104025401_cYuOV
 *  Success: Backup restore initiated with action_id 123.
 */
class WPMUDEV_Hosting_WPCLI_Backup {

	/**
	 * Initiates a backup snapshot of the site.
	 *
	 * ## OPTIONS
	 *
	 * [--action_id]
	 * : Prints the raw value of the action_id only.
	 *
	 * ## EXAMPLES
	 *
	 *  $ wp wpmudev backup create
	 *  Success: Backup initiated with action_id 123.
	 *
	 *  $ wp wpmudev backup create --action_id
	 *  123
	 *
	 * @when after_wp_load
	 */
	public function create( $args, $assoc_args ) {
		$result = wpmudev_hosting_api_call( sprintf( '%s/backups', WPMUDEV_HOSTING_SITE_ID ), false, 'POST' );
		if ( isset( $assoc_args['id'] ) ) {
			WP_CLI::line( $result->action_id );
		} else {
			WP_CLI::success( sprintf( "Backup initiated with action_id %d. You may monitor action status with 'wp wpmudev action %d'.", $result->action_id, $result->action_id ) );
		}
	}

	/**
	 * Lists the backup snapshots of the site.
	 *
	 * ## OPTIONS
	 *
	 * [--field=<field>]
	 * : Prints the value of a single field for each backup snapshot.
	 *
	 * [--fields=<fields>]
	 * : Limit the output to specific backup snapshot fields.
	 *
	 * [--format=<format>]
	 * : Render output in a particular format.
	 *   ---
	 *   default: table
	 *   options:
	 *     - table
	 *     - csv
	 *     - ids
	 *     - json
	 *     - count
	 *     - yaml
	 *   ---
	 *
	 * ## AVAILABLE FIELDS
	 *
	 *  These fields will be displayed by default for each backup:
	 *
	 * * id
	 * * creation_time
	 * * context
	 * * domain
	 * * wp_version
	 *
	 *  These fields are optionally available:
	 *
	 * * wp_posts
	 * * wp_pages
	 * * wp_comments
	 * * wp_uploads
	 * * wp_plugins
	 * * wp_themes
	 * * wp_sites
	 * * wp_users
	 *
	 * * all - display all fields
	 *
	 * ## EXAMPLES
	 *
	 *  $ wp wpmudev backup list
	 *
	 * @subcommand list
	 */
	public function list_backups( $args, $assoc_args ) {
		$raw_backups = wpmudev_hosting_api_call( WPMUDEV_HOSTING_SITE_ID . '/backups' );

		$backups = array();
		foreach ( $raw_backups as $key => $backup ) {
			$backups[ $key ]     = $backup;
			$backups[ $key ]->id = substr( $backup->Key, strpos( $backup->Key, '@' ) + 1 );
			unset( $backups[ $key ]->Key );
			unset( $backups[ $key ]->size );
		}

		if ( isset( $assoc_args['field'] ) ) {
			$fields = array( $assoc_args['field'] );
		} elseif ( isset( $assoc_args['fields'] ) && 'all' === $assoc_args['fields'] ) {
			$fields = array( 'id', 'creation_time', 'context', 'domain', 'wp_version', 'wp_posts', 'wp_pages', 'wp_comments', 'wp_uploads', 'wp_plugins', 'wp_themes', 'wp_sites', 'wp_users' );
		} elseif ( isset( $assoc_args['fields'] ) ) {
			$fields = $assoc_args['fields'];
		} else {
			$fields = array( 'id', 'creation_time', 'context', 'domain', 'wp_version' );
		}

		$format = isset( $assoc_args['format'] ) ? $assoc_args['format'] : 'table';
		WP_CLI\Utils\format_items( $format, $backups, $fields );
	}

	/**
	 * Initiates the restore of a backup snapshot of the site.
	 *
	 * A snapshot will be made before restoring just in case.
	 *
	 * ## OPTIONS
	 *
	 * <id>
	 * : The id of the backup snapshot to restore as returned the list command.
	 *
	 * [--action_id]
	 * : Prints the raw value of the action_id only.
	 *
	 * ## EXAMPLES
	 *
	 *  $ wp wpmudev backup restore prod_incremental_20181104025401_cYuOV
	 *  Success: Backup restore initiated with action_id 123.
	 *
	 *  $ wp wpmudev backup restore prod_incremental_20181104025401_cYuOV --action_id
	 *  123
	 *
	 * @when after_wp_load
	 */
	public function restore( $args, $assoc_args ) {

		if ( ! isset( $args[0] ) ) {
			WP_CLI::error( 'Invalid backup id: ' . $args[0] );
		} else {
			$result = wpmudev_hosting_api_call( sprintf( '%s/backups/%s/restore', WPMUDEV_HOSTING_SITE_ID, $args[0] ), false, 'POST' );
			if ( isset( $assoc_args['action_id'] ) ) {
				WP_CLI::line( $result->action_id );
			} else {
				WP_CLI::success( sprintf( "Backup restore initiated with action_id %d. You may monitor action status with 'wp wpmudev action %d'.", $result->action_id, $result->action_id ) );
			}
		}
	}

	/**
	 * Initiates the export of a backup snapshot of the site and sends a download link to the provided email address.
	 *
	 * ## OPTIONS
	 *
	 * <id>
	 * : The id of the backup snapshot to restore as returned the list command.
	 *
	 * [--email=<email>]
	 * : The optional email address to send a download link to. Defaults to WPMU DEV account email.
	 *
	 * ## EXAMPLES
	 *
	 *     wp wpmudev backup export prod_incremental_20181104025401_cYuOV john@example.com
	 *
	 * @when after_wp_load
	 */
	/*public function export( $args, $assoc_args ) {
		$body = false;
		if ( ! isset( $args[0] ) ) {
			WP_CLI::error( 'Invalid backup id: ' . $args[0] );
		} elseif ( isset( $assoc_args['email'] ) && ! is_email( $assoc_args['email'] ) ) {
			WP_CLI::error( 'Invalid email: ' . $assoc_args['email'] );
		} else {
			if ( isset( $assoc_args['email'] ) ) {
				$body = array( 'email' => $assoc_args['email'] );
			}
			$result = wpmudev_hosting_api_call( sprintf( '%s/backups/%s/export', WPMUDEV_HOSTING_SITE_ID, $args[0] ), $body, 'POST' );
			WP_CLI::success( 'Backup export initiated.' );
		}
	}*/
}

/**
 * Manage your staging environment for this site.
 *
 * ## EXAMPLES
 *
 *  # Sync the production site to staging.
 *  $ wp wpmudev staging sync
 *  Success: Staging site sync from production has been initiated with action_id 123.
 *
 *  # Deploy just files to production.
 *  $ wp wpmudev staging deploy
 *  Success: Staging deploy files to production initiated with action_id 123.
 *
 *  # Deploy files and database to production.
 *  $ wp wpmudev staging deploy --type=all
 *  Success: Staging deploy files and database to production initiated with action_id 123.
 */
class WPMUDEV_Hosting_WPCLI_Staging {

	/**
	 * Syncs the production site to staging environment.
	 *
	 * All staging changes will be overwritten.
	 *
	 * ## OPTIONS
	 *
	 * [--action_id]
	 * : Prints the raw value of the action_id only.
	 *
	 * ## EXAMPLES
	 *
	 *  $ wp wpmudev staging sync
	 *  Success: Staging site sync from production has been initiated with action_id 123.
	 *
	 *  $ wp wpmudev staging sync --action_id
	 *  123
	 *
	 * @alias create
	 * @when after_wp_load
	 */
	public function sync( $args, $assoc_args ) {
		$result = wpmudev_hosting_api_call( sprintf( '%s/staging/sync', WPMUDEV_HOSTING_SITE_ID ), false, 'POST' );
		if ( isset( $assoc_args['action_id'] ) ) {
			WP_CLI::line( $result->action_id );
		} else {
			WP_CLI::success( sprintf( "Staging site sync from production has been initiated with action_id %d. You may monitor action status with 'wp wpmudev action %d'.", $result->action_id, $result->action_id ) );
		}
	}

	/**
	 * Deploys staging site to production.
	 *
	 * Will first create a backup of production for safety.
	 *
	 * ## OPTIONS
	 *
	 * [--type=<type>]
	 * : What to deploy. Just files (default) or both files and database.
	 *   ---
	 *   default: files
	 *   options:
	 *     - files
	 *     - all
	 *   ---
	 *
	 * [--action_id]
	 * : Prints the raw value of the action_id only.
	 *
	 * ## EXAMPLES
	 *
	 *  # Deploy just files to production.
	 *  $ wp wpmudev staging deploy
	 *  Success: Staging deploy files to production initiated with action_id 123.
	 *
	 *  # Deploy files and database to production.
	 *  $ wp wpmudev staging deploy --type=all
	 *  Success: Staging deploy files and database to production initiated with action_id 123.
	 *
	 * @when after_wp_load
	 */
	public function deploy( $args, $assoc_args ) {
		if ( isset( $assoc_args['type'] ) ) {
			if ( ! in_array( $assoc_args['type'], array( 'files', 'all' ), true ) ) {
				WP_CLI::error( 'Invalid type parameter, should be "files" or "all": ' . $assoc_args['type'] );
			} else {
				$result = wpmudev_hosting_api_call( sprintf( '%s/staging/promote/%s', WPMUDEV_HOSTING_SITE_ID, $assoc_args['type'] ), false, 'POST' );
				if ( isset( $assoc_args['action_id'] ) ) {
					WP_CLI::line( $result->action_id );
				} else {
					WP_CLI::success( sprintf( "Staging deploy files and database to production initiated with action_id %d. You may monitor action status with 'wp wpmudev action %d'.", $result->action_id, $result->action_id ) );
				}
			}
		} else {
			$result = wpmudev_hosting_api_call( sprintf( '%s/staging/promote/%s', WPMUDEV_HOSTING_SITE_ID, 'files' ), false, 'POST' );
			if ( isset( $assoc_args['action_id'] ) ) {
				WP_CLI::line( $result->action_id );
			} else {
				WP_CLI::success( sprintf( "Staging deploy files to production initiated with action_id %d. You may monitor action status with 'wp wpmudev action %d'.", $result->action_id, $result->action_id ) );
			}
		}
	}
}

/**
 * Various checks against the WP installation for WPMU DEV Hosting.
 *
 * $ wp wpmudev check autoload
 *
 * Tests the _options tables for autoloaded options and shows if they exceed the 800kb limit.
 * If the 800kb limit is exceeded it will show the first 10 options and their sizes as well as the necessary SQL queries to set them to autoload=no.
 *
 * $ wp wpmudev check options
 *
 * Tests the _options tables for the option value sizes and shows if they exceed the 800kb limit.
 * If the 800kb limit is exceeded it will show the options and their sizes.
 */
class WPMUDEV_Hosting_WPCLI_Check {
	/**
	 * SQL LIMIT Number var.
	 */
	private static $_sql_limit = 10;

	/**
	 * Multisite _blogs count var.
	 */
	private static $_blogs_count = 0;

	/**
	 * Constructor.
	 */
	public function __construct() {
		if ( is_multisite() ) {
			self::helper_count_blogs();
		}
	}

	/**
	 * $ wp wpmudev check autoload
	 *
	 * Tests the _options tables for autoloaded options and shows if they exceed the 800kb limit.
	 * If the 800kb limit is exceeded it will show the first 10 options and their sizes as well as the necessary SQL queries to set them to autoload=no.
	 */
	public function autoload() {
		global $wpdb;

		$data = $wpdb->get_results(
			"SELECT 'Total size' as 'Name', ROUND( SUM( LENGTH( option_value ) ) / 1024 ) as 'Size in KB'
			FROM {$wpdb->base_prefix}options WHERE autoload='yes'
			UNION ( SELECT option_name as 'Name', ROUND( LENGTH( option_value ) / 1024 ) as 'Size in KB'
			FROM {$wpdb->base_prefix}options WHERE autoload='yes'
			ORDER BY LENGTH( option_value )
			DESC LIMIT 5 );",
			ARRAY_A
		);

		if ( 800 < $data[0]['Size in KB'] ) {
			WP_CLI::warning( "{$wpdb->base_prefix}options" );

			WP_CLI\Utils\format_items( 'table', $data, array( 'Name', 'Size in KB' ) );

			foreach ( $data as $result ) {
				if ( 'Total size' !== $result['Name'] ) {
					WP_CLI::line( "UPDATE {$wpdb->base_prefix}options SET autoload='no' WHERE option_name='{$result['Name']}';" );
				}
			}
		} else {
			WP_CLI::success( "{$wpdb->base_prefix}options" );
		}

		if ( is_multisite() ) {
			self::helper_paginate( 'for_autoload' );
		}
	}

	/**
	 * $ wp wpmudev check options
	 *
	 * Tests the _options tables for the option value sizes and shows if they exceed the 800kb limit.
	 * If the 800kb limit is exceeded it will show the options and their sizes.
	 */
	public function options() {
		global $wpdb;

		$data = $wpdb->get_results(
			"SELECT option_name as 'Name', ROUND( LENGTH( option_value ) / 1024 ) as 'Size in KB'
			FROM {$wpdb->base_prefix}options
			WHERE ROUND( LENGTH( option_value ) / 1024 ) > 800
			ORDER BY 'Size in KB'
			DESC LIMIT 5;",
			ARRAY_A
		);

		if ( ! empty( $data ) ) {
			WP_CLI::warning( "{$wpdb->base_prefix}options" );

			WP_CLI\Utils\format_items( 'table', $data, array( 'Name', 'Size in KB' ) );
		} else {
			WP_CLI::success( "{$wpdb->base_prefix}options" );
		}

		if ( is_multisite() ) {
			self::helper_paginate( 'for_options' );
		}
	}

	/**
	 * Creates a user input for pagination purposes.
	 */
	private static function helper_paginate( $for ) {
		$round = ceil( self::$_blogs_count / self::$_sql_limit );
		$pages = '';

		for ( $i = 1; $i <= $round; $i++ ) {
			$pages .= $i . ', ';
		}

		$pages = rtrim( $pages, ', ' );

		WP_CLI::success( "Aditional pages: {$pages}" );

		$page = cli\prompt( 'Enter page number (or c to cancel)' );

		if ( 'c' === $page ) {
			exit;
		}

		if ( 'for_autoload' === $for ) {
			self::helper_subsite_autoload( $page );
		} elseif ( 'for_options' === $for ) {
			self::helper_subsite_options( $page );
		}
	}

	/**
	 * Return subsite autoload values.
	 */
	private static function helper_subsite_autoload( $page ) {
		global $wpdb;

		$actual_page = $page - 1;
		$from        = $actual_page * self::$_sql_limit;

		$blogs = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT blog_id FROM {$wpdb->base_prefix}blogs LIMIT %d, %d;",
				$from,
				self::$_sql_limit
			)
		);

		foreach ( $blogs as $blog ) {

			$blog_id = intval( $blog->blog_id );

			if ( intval( BLOG_ID_CURRENT_SITE ) !== $blog_id ) {
				$data = $wpdb->get_results(
					$wpdb->prepare(
						"SELECT 'Total size' as 'Name', ROUND( SUM( LENGTH( option_value ) ) / 1024 ) as 'Size in KB'
						FROM $wpdb->base_prefix%d_options WHERE autoload='yes'
						UNION ( SELECT option_name as 'Name', ROUND( LENGTH( option_value ) / 1024 ) as 'Size in KB'
						FROM $wpdb->base_prefix%d_options WHERE autoload='yes'
						ORDER BY LENGTH( option_value )
						DESC LIMIT 5 );",
						$blog_id,
						$blog_id
					),
					ARRAY_A
				);

				if ( 800 < $data[0]['Size in KB'] ) {
					WP_CLI::warning( "{$wpdb->base_prefix}{$blog_id}_options" );

					WP_CLI\Utils\format_items( 'table', $data, array( 'Name', 'Size in KB' ) );

					foreach ( $data as $result ) {
						if ( 'Total size' !== $result['Name'] ) {
							WP_CLI::line( "UPDATE {$wpdb->base_prefix}{$blog_id}_options SET autoload='no' WHERE option_name='{$result['Name']}';" );
						}
					}
				} else {
					WP_CLI::success( "{$wpdb->base_prefix}{$blog_id}_options" );
				}
			}
		}

		self::helper_paginate( 'for_autoload' );
	}

	/**
	 * Return subsite options sizes.
	 */
	private static function helper_subsite_options( $page ) {
		global $wpdb;

		$actual_page = $page - 1;
		$from        = $actual_page * self::$_sql_limit;

		$blogs = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT blog_id FROM {$wpdb->base_prefix}blogs LIMIT %d, %d;",
				$from,
				self::$_sql_limit
			)
		);

		foreach ( $blogs as $blog ) {

			$blog_id = intval( $blog->blog_id );

			if ( intval( BLOG_ID_CURRENT_SITE ) !== $blog_id ) {
				$data = $wpdb->get_results(
					$wpdb->prepare(
						"SELECT option_name as 'Name', ROUND( LENGTH( option_value ) / 1024 ) as 'Size in KB'
						FROM {$wpdb->base_prefix}%d_options
						WHERE ROUND( LENGTH( option_value ) / 1024 ) > 800
						ORDER BY 'Size in KB'
						DESC LIMIT 5;",
						$blog_id
					),
					ARRAY_A
				);

				if ( ! empty( $data ) ) {
					WP_CLI::warning( "{$wpdb->base_prefix}{$blog_id}_options" );

					WP_CLI\Utils\format_items( 'table', $data, array( 'Name', 'Size in KB' ) );
				} else {
					WP_CLI::success( "{$wpdb->base_prefix}{$blog_id}_options" );
				}
			}
		}

		self::helper_paginate( 'for_options' );
	}

	/**
	 * Counts _blogs if this is a Multisite.
	 */
	private static function helper_count_blogs() {
		global $wpdb;

		self::$_blogs_count = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->base_prefix}blogs" );
	}
}

class WPMUDEV_Hosting_WPCLI_Doctor {
	public function __invoke() {
		WP_CLI::runcommand( 'doctor check --all --config=' . ABSPATH . 'wp-content/mu-plugins/wpmudev-hosting/wpmudev-wp-doctor.yml' );
	}
}
