<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Single listing page map.
 *
 * @since      1.0.0
 * @package    Easy_Listings_Map
 * @subpackage Easy_Listings_Map/public
 * @author     Taher Atashbar<taher.atashbar@gmail.com>
 */

class ELM_Public_Single_Map extends ELM_Public_Controller {

	/**
	 * Plugin public-face.
	 *
	 * @since   1.0.0
	 * @var     Easy_Listings_Map_Public
	 */
	private $plugin_public;
	/**
	 * Settings of map like styles and etc.
	 *
	 * @since   1.0.0
	 * @var     array
	 */
	private $settings;
	/**
	 * Data of map that will send to map js file.
	 *
	 * @since   1.0.0
	 * @var     array
	 */
	private $data;

	public function __construct( Easy_Listings_Map_Public $plugin_public ) {
		$this->plugin_public = $plugin_public;

		$elm_settings = ELM_IOC::make( 'settings' )->get_settings();
		$single_map_enabled = ! empty( $elm_settings['map_in_single_page'] ) ? $elm_settings['map_in_single_page'] : 'enabled';
		if ( $single_map_enabled == 'enabled' ) {
			if ( remove_action( 'epl_property_map', 'epl_property_map_default_callback' ) ) {
				// Setting map specific settings.
				$this->settings['map_height']   = ! empty( $elm_settings['single_page_map_height'] ) ? $elm_settings['single_page_map_height'] : '400';
				// Setting map specific data.
				$this->data['zoom']             = ! empty( $elm_settings['single_page_map_zoom'] ) ? trim( $elm_settings['single_page_map_zoom'] ) : '17';
				$this->data['map_id']           = 'elm-singular-map';
				$this->data['map_types']        = ! empty( $elm_settings['single_page_map_types'] ) ? array_values( $elm_settings['single_page_map_types'] ) : array( 'ROADMAP' );
				$this->data['default_map_type'] = ! empty( $elm_settings['single_page_map_default_type'] ) ? $elm_settings['single_page_map_default_type'] : 'ROADMAP';
				$this->data['map_styles']       = isset( $elm_settings['single_page_map_styles'] ) ? trim( $elm_settings['single_page_map_styles'] ) : '';
				// Adding action for showing map.
				$this->plugin_public->get_loader()->add_action( 'epl_property_map', $this, 'display_single_listing_map' );
			}
		}
	}

	/**
	 * action-callback for displaying map in single listing page.
	 *
	 * @since  1.0.0
	 */
	public function display_single_listing_map() {
		$address_coordinates = get_post_meta( get_the_ID(), 'property_address_coordinates', true );
		if ( strlen( $address_coordinates ) > 0 ) {
			$coordinates = explode( ',', $address_coordinates, 2 );
			// Checking coordinates for lat and lon
			if ( isset( $coordinates[0] ) && isset( $coordinates[1] ) ) {
				$this->data['latitude'] = trim( $coordinates[0] );
				$this->data['longitude'] = trim( $coordinates[1] );
				$this->draw_map();
			}
		}
	}

	/**
	 * Registering scripts of map.
	 *
	 * @since 1.0.0
	 */
	public function register_scripts() {
		$api_key  = epl_get_option( 'epl_google_api_key' );
		$api_key  = empty( $api_key ) ? 'AIzaSyCBpgWp8d61yCDUgJVcy1-MOUogxbSzVRI' : $api_key;

		$protocol = is_ssl() ? 'https' : 'http';
		// Use minified libraries if SCRIPT_DEBUG is turned off
		$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
		// Register the script first.
		wp_register_script( 'elm-google-map-v-3', $protocol . '://maps.googleapis.com/maps/api/js?v=3.exp' . ( ! empty( $api_key ) ? '&key=' . esc_attr( $api_key ) : '' ) );
		wp_enqueue_script( 'elm_singular_google_map', $this->get_js_url() . 'maps/elm-singular-google-map' . $suffix . '.js',
			array( 'jquery', 'elm-google-map-v-3' ), $this->plugin_public->get_version(), true );
		wp_localize_script( 'elm_singular_google_map', 'elm_singular_map', $this->data );

		wp_enqueue_style( 'elm-google-maps', $this->get_css_url() . 'elm-google-maps' . $suffix . '.css' );
	}

	/**
	 * Drawing map element content.
	 *
	 * @since 1.0.0
	 */
	protected function draw_map() {
		$this->render_view( 'maps.singular-map-content', array(
			'controller' => $this,
			'map_height' => $this->settings['map_height'],
			'map_id'     => $this->data['map_id'],
		) );
	}

	/**
	 * Getting data of singular map.
	 *
	 * @since 1.0.0
	 * @return array array of data
	 */
	public function get_data() {
		return $this->data;
	}

	/**
	 * Getting settings of singular map
	 *
	 * @since 1.0.0
	 * @return array array of settings
	 */
	public function get_settings() {
		return $this->settings;
	}

}
