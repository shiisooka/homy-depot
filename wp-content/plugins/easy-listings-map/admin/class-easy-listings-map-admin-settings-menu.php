<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The admin-facing settings menu of the plugin.
 *
 * @package    Easy_Listings_Map
 * @subpackage Easy_Listings_Map/admin
 * @author     Taher Atashbar <taher.atashbar@gmail.com>
 */

class ELM_Admin_Settings_Menu extends ELM_Admin_Controller {


	private $plugin_admin;

	/**
	 * @since   1.0.0
	 * @var     ELM_Admin_Element
	 */
	private $html_element;

	/**
	 * Constructor function for settings menu.
	 *
	 * @since   1.0.0
	 *
	 * @param   Easy_Listings_Map_Admin $plugin_admin
	 * @param   ELM_Admin_Element $html_element
	 */
	public function __construct( Easy_Listings_Map_Admin $plugin_admin, ELM_Admin_Element $html_element ) {
		$this->plugin_admin = $plugin_admin;
		$this->html_element = $html_element;

		$this->plugin_admin->get_loader()->add_action( 'admin_init', $this, 'register_settings' );

		// Adding filter for sanitizing upload elements of settings page.
		$this->plugin_admin->get_loader()->add_filter( 'elm_settings_sanitize_upload', $this, 'elm_settings_sanitize_upload' );
	}

	/**
	 * Rendering settings menu content.
	 *
	 * @since   1.0.0
	 */
	public function create_menu() {
		$this->render_view( 'menu.settings-menu',
			array(
				'tabs'   => $this->get_settings_tabs(),
			)
		);
	}

	/**
	 * Retrieve settings tabs
	 *
	 * @since   1.0.0
	 * @return  array $tabs
	 */
	public function get_settings_tabs() {
		$tabs = array(
			'general' => __( 'General', 'elm' ),
			'markers' => __( 'Markers', 'elm' ),
		);

		return apply_filters( 'elm_settings_tabs', $tabs );
	}

	/**
	 * Add all settings sections and fields.
	 *
	 * @since   1.0.0
	 * @return  void
	 */
	public function register_settings() {
		if ( false == get_option( 'elm_settings' ) ) {
			add_option( 'elm_settings' );
		}

		foreach ( $this->get_registered_settings() as $tab => $settings ) {
			add_settings_section( 'elm_settings_' . $tab, null, '__return_false', 'elm_settings_' . $tab );

			foreach ( $settings as $option ) {
				$name = isset( $option['name'] ) ? $option['name'] : '';

				add_settings_field(
					'elm_settings[' . $option['id'] . ']',
					$name,
					method_exists( $this->html_element, $option['type'] ) ?
						array( $this->html_element, $option['type'] ) : array( $this->html_element, 'missing' ),
					'elm_settings_' . $tab,
					'elm_settings_' . $tab,
					array(
						'section'  => $tab,
						'id'       => isset( $option['id'] )      ? $option['id']      : null,
						'desc'     => ! empty( $option['desc'] )  ? $option['desc']    : '',
						'desc_tip' => isset( $option['desc_tip'] ) ? $option['desc_tip'] : false,
						'name'     => isset( $option['name'] )    ? $option['name']    : null,
						'size'     => isset( $option['size'] )    ? $option['size']    : null,
						'options'  => isset( $option['options'] ) ? $option['options'] : '',
						'std'      => isset( $option['std'] )     ? $option['std']     : '',
						'min'      => isset( $option['min'] )     ? $option['min']     : null,
						'max'      => isset( $option['max'] )     ? $option['max']     : null,
						'step'     => isset( $option['step'] )    ? $option['step']    : null,
					)
				);
			}
		}

		// Creates our settings in the options table
		register_setting( 'elm_settings', 'elm_settings', array( $this, 'elm_settings_sanitize' ) );
	}

	/**
	 * Retrieve the array of plugin settings
	 *
	 * @since   1.0.0
	 * @return  array
	 */
	public function get_registered_settings() {
		// Markers for each property type_status
		$post_type_markers = array();
		// Getting property_types for registering markers for each of them.
		$properties = ELM_IOC::make( 'properties' );
		$properties_status = $properties->get_properties_status();
		if ( count( $properties_status ) ) {
			foreach ( $properties_status as $property_type_key => $property_type_value ) {
				if ( count( $property_type_value['status'] ) ) {
					foreach ( $property_type_value['status'] as $status_key => $status_value ) {
						$post_type_markers[ $property_type_key . '_' . $status_key ] = array(
							'id'       => $property_type_key . '_' . $status_key . '_marker',
							'name'     => $status_value . ' ' . $property_type_value['name'] . ' ' . __( 'Marker', 'elm' ),
							'type'     => 'marker_upload',
							'std'      => $properties->get_property_marker( $property_type_key, $status_key ),
							'desc'     => sprintf( __( 'Marker for %s %s', 'elm' ), $status_key, $property_type_key ),
							'desc_tip' => true,
						);
					}
				}
			}
		}

		$elm_settings = array(
			/** General Settings */
			'general' => apply_filters( 'elm_settings_general',
				array(
					'map_styles' => array(
						'id'   => 'map_styles',
						'name' => __( 'Map styles', 'elm' ),
						'desc' => __( sprintf( 'Get map styles from %s and paste it here', '<a href="https://snazzymaps.com/" target="_blank"><strong>snazzymaps</strong></a>' ), 'elm' ),
						'type' => 'textarea',
					),
					'single_page_map_settings' => array(
						'id' => 'single_page_map_settings',
						'name' => '<strong>' . __( 'Single listing page map settings', 'elm' ) . '</strong>',
						'desc' => __( 'Configure single listing page map options', 'elm' ),
						'type' => 'header'
					),
					'map_in_single_page'     => array(
						'id'      => 'map_in_single_page',
						'name'    => __( 'Display map in single listing page', 'elm' ),
						'desc'    => __( 'This option will shows the map in single listing page in enabled mode.', 'elm' ),
						'type'    => 'radio',
						'std'     => 'enabled',
						'options' => array(
							'enabled'  => __( 'Enabled', 'elm' ),
							'disabled' => __( 'Disabled', 'elm' )
						)
					),
					'single_page_map_zoom'	 => array(
						'id'      => 'single_page_map_zoom',
						'name'    => __( 'Single listing page map zoom level', 'elm' ),
						'desc'    => __( 'This option will controls map zoom level in single listing page', 'elm' ),
						'type'    => 'select',
						'std'     => '17',
						'options' => array(
							'0'  => '0',
							'1'  => '1',
							'2'  => '2',
							'3'  => '3',
							'4'  => '4',
							'5'  => '5',
							'6'  => '6',
							'7'  => '7',
							'8'  => '8',
							'9'  => '9',
							'10' => '10',
							'11' => '11',
							'12' => '12',
							'13' => '13',
							'14' => '14',
							'15' => '15',
							'16' => '16',
							'17' => '17',
							'18' => '18',
						)
					),
					'single_page_map_height' => array(
						'id'   => 'single_page_map_height',
						'name' => __( 'Height of single page map', 'elm' ),
						'desc' => __( 'Single listing page map height in pixels', 'elm' ),
						'type' => 'number',
						'size' => 'small',
						'std'  => '400',
					),
					'single_page_map_types' => array(
						'id'      => 'single_page_map_types',
						'name'    => __( 'Map display type', 'elm' ),
						'desc'    => __( 'If more than one type selected user can switch between map types by map controls.', 'elm' ),
						'type'    => 'multicheck',
						'options' => apply_filters('elm_single_page_map_types', array(
								array(
									'id'   => 'ROADMAP',
									'name' => __( 'Roadmap', 'elm' ),
									'std'  => '1',
								),
								array(
									'id'   => 'SATELLITE',
									'name' => __( 'Sattelite', 'elm' ),
									'std'  => '1',
								),
								array(
									'id'   => 'HYBRID',
									'name' => __( 'Hybrid', 'elm' ),
									'std'  => '1',
								),
								array(
									'id'   => 'TERRAIN',
									'name' => __( 'Terrain', 'elm' ),
									'std'  => '1',
								),
							)
						),
					),
					'single_page_map_default_type' => array(
						'id'   => 'single_page_map_default_type',
						'name' => __( 'Map default display type', 'elm' ),
						'desc' => __( 'Default display type of the map', 'elm' ),
						'type' => 'select',
						'std'  => 'ROADMAP',
						'options' => apply_filters( 'single_page_map_default_type_options',
							array(
								'ROADMAP'   => __( 'Roadmap', 'elm' ),
								'SATELLITE' => __( 'Sattelite', 'elm' ),
								'HYBRID'    => __( 'Hybrid', 'elm' ),
								'TERRAIN'   => __( 'Terrain', 'elm' ),
							)
						),
					),
					'single_page_map_styles' => array(
						'id'   => 'single_page_map_styles',
						'name' => __( 'Map styles', 'elm' ),
						'desc' => __( sprintf( 'Get map styles from %s and paste it here', '<a href="https://snazzymaps.com/" target="_blank"><strong>snazzymaps</strong></a>' ), 'elm' ),
						'type' => 'textarea',
					),
				)
			),
			'markers' => apply_filters( 'elm_settings_markers',
				array_merge(
					array(
						'map_multiple_marker' => array(
							'id'       => 'map_multiple_marker',
							'name'     => __( 'Map multiple marker', 'elm' ),
							'type'     => 'marker_upload',
							'std'      => $this->plugin_admin->get_images_folder() . 'markers/multiple.png',
							'desc'     => __( 'Marker for listings that have same coordinates', 'elm' ),
							'desc_tip' => true,
						)
					),
					$post_type_markers
				)
			),
		);

		return apply_filters( 'elm_registered_settings', $elm_settings );
	}

	/**
	 * Settings Sanitization
	 *
	 * Adds a settings error (for the updated message)
	 * At some point this will validate input
	 *
	 * @since 1.0.0
	 *
	 * @param array $input The value inputted in the field
	 *
	 * @return string $input Sanitizied value
	 */
	public function elm_settings_sanitize( $input = array() ) {
		$elm_settings = ELM_IOC::make( 'settings' )->get_settings();

		if ( empty( $_POST['_wp_http_referer'] ) ) {
			return $input;
		}

		parse_str( $_POST['_wp_http_referer'], $referrer );

		$settings = $this->get_registered_settings();
		$tab      = isset( $referrer['tab'] ) ? $referrer['tab'] : 'general';

		$input = apply_filters( 'elm_settings_' . $tab . '_sanitize', $input );
		// Loop through each setting being saved and pass it through a sanitization filter
		foreach ( $input as $key => $value ) {

			// Get the setting type (checkbox, select, etc)
			$type = isset( $settings[ $tab ][ $key ]['type'] ) ? $settings[ $tab ][ $key ]['type'] : false;

			if ( $type ) {
				// Field type specific filter
				$input[ $key ] = apply_filters( 'elm_settings_sanitize_' . $type, $value, $key );
			}

			// General filter
			$input[ $key ] = apply_filters( 'elm_settings_sanitize', $input[ $key ], $key );
		}

		// Loop through the whitelist and unset any that are empty for the tab being saved
		if ( ! empty( $settings[ $tab ] ) ) {
			foreach ( $settings[ $tab ] as $key => $value ) {

				// settings used to have numeric keys, now they have keys that match the option ID. This ensures both methods work
				if ( is_numeric( $key ) ) {
					$key = $value['id'];
				}

				if ( empty( $input[ $key ] ) && isset( $elm_settings[ $key ] ) ) {
					unset( $elm_settings[ $key ] );
				}
			}
		}

		// Merge our new settings with the existing
		$output = array_merge( $elm_settings, $input );

		add_settings_error( 'elm-notices', '', __( 'Settings updated.', 'elm' ), 'updated' );

		return $output;
	}

	/**
	 * Sanitizing upload url.
	 *
	 * @since   1.0.0
	 * @param   $input
	 *
	 * @return  string
	 */
	public function elm_settings_sanitize_upload( $input ) {
		return esc_url_raw( $input );
	}

	/**
	 * Sanitizing marker_upload url.
	 *
	 * @since   1.0.0
	 * @param   $input
	 *
	 * @return  string
	 */
	public function elm_settings_sanitize_marker_upload( $input ) {
		return $this->elm_settings_sanitize_upload( $input );
	}

}
