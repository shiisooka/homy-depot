<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The class responsible for plugin settigns.
 *
 * @link       http://www.asanaplugins.com/
 * @since      1.0.0
 *
 * @package    Easy_Listings_Map
 * @subpackage Easy_Listings_Map/includes
 * @author     Taher Atashbar <taher.atashbar@gmail.com>
 */

class ELM_Settings {

	/**
	 * Plugin settings
	 *
	 * @since 1.0.0
	 * @var array
	 */
	private $plugin_settings;

	public function __construct() {
		$this->init_settings();
	}

	/**
	 * Initialize plugin settings
	 *
	 * @since 1.0.0
	 * @return void
	 */
	private function init_settings() {
		$this->plugin_settings = get_option( 'elm_settings' );
		if ( empty( $this->plugin_settings ) ) {
			$this->plugin_settings = array();
		}
	}

	/**
	 * Getting plugin settings.
	 *
	 * @since 1.0.0
	 * @return array $plugin_settings
	 */
	public function get_settings() {
		return apply_filters( 'elm_get_settings', $this->plugin_settings );
	}

}
