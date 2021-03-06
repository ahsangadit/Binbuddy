<?php

namespace WP_Defender;

use Calotes\Helper\Array_Cache;
use Calotes\Helper\HTTP;
use Calotes\Model\Setting;

abstract class Controller extends \Calotes\Base\Controller {
	use \WP_Defender\Traits\IO;
	use \WP_Defender\Traits\User;

	protected $parent_slug = 'wp-defender';

	/**
	 * Queue mandatory assets
	 */
	public function enqueue_main_assets() {
		if ( $this->is_page_active() ) {
			wp_enqueue_script( 'clipboard' );
			wp_enqueue_style( 'defender' );
			wp_enqueue_script( 'wpmudev-sui' );
		}
	}

	/**
	 * This too check if the current page is active so we can queue right assets
	 *
	 * @return bool
	 */
	public function is_page_active() {
		$current = HTTP::get( 'page' );

		return $current === $this->slug;
	}

	/**
	 * Quick way for saving settings
	 *
	 * @param Setting $model
	 * @param $category
	 *
	 * @return bool
	 * @deprecated
	 */
	public function default_update_settings( Setting &$model, $category ) {
		if ( ! $this->check_permission() ) {
			return false;
		}
		if ( ! $this->verify_nonce( 'update_settings' . $category ) ) {
			return false;
		}

		$data = HTTP::post( 'data' );
		$data = apply_filters( 'defender_filtering_data_settings', $data );
		$model->import( $data );
		if ( $model->validate() ) {
			$model->save();

			//bind for submit data to DEV
			if ( ! wp_next_scheduled( 'defender_hub_sync' ) ) {
				wp_schedule_single_event( time(), 'defender_hub_sync' );
			}

			return true;
		}

		return false;
	}

	/**
	 * @return bool
	 */
	protected function check_permission() {
		if ( ! is_user_logged_in() ) {
			return false;
		}
		$cap = is_multisite() ? 'manage_network_options' : 'manage_options';

		return current_user_can( $cap );
	}

	/**
	 * Quick handler to check nonce
	 *
	 * @param $intention
	 * @param string $method
	 *
	 * @return bool
	 */
	protected function verify_nonce( $intention, $method = 'get' ) {
		$nonce = 'get' === $method ? HTTP::get( '_def_nonce' ) : HTTP::post( '_def_nonce' );
		if ( ! wp_verify_nonce( $nonce, $intention ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Remove all settings, configs generated by this controller
	 * @return mixed
	 */
	abstract function remove_settings();

	/**
	 * Remove all data
	 * @return mixed
	 */
	abstract function remove_data();
}