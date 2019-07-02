<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Google maps shortcode of the plugin.
 *
 * @package    Easy_Listings_Map
 * @subpackage Easy_Listings_Map/public/shortcodes
 * @author     Taher Atashbar <taher.atashbar@gmail.com>
 */

class ELM_Shortcode_Google_Maps extends ELM_Public_Controller {

	/**
	 * Attributes of the shortcode combined with default values.
	 *
	 * @since 1.0.0
	 * @var   array
	 */
	private $attributes = array();

	/**
	 * Content inside shortcode.
	 *
	 * @since 1.0.0
	 * @var   string
	 */
	private $content;

	/**
	 * Outputting content of maps shortcode.
	 *
	 * @since   1.0.0
	 * @param   $atts array of shortcode attributes.
	 * @return  string content of shortcode
	 */
	public function output( $atts, $content = '' ) {
		$this->content  = $content;
		$property_types = epl_get_active_post_types();
		if ( ! empty( $property_types ) ) {
			$property_types = array_keys( $property_types );
		}
		// @todo adding transient support for shortcode.
		$this->attributes = shortcode_atts(
			array(
				'post_type'         => $property_types,
				'status'            => array( 'current', 'sold', 'leased' ),
				'page_properties'   => false, // Show only properties of current page in the map
				'clustering'        => true, // Showing clusters in map.
				'limit'             => -1,	 // Show all of posts.
				'orderby'           => 'date',
				'order'             => 'DESC',
				'location'          => '',	// Location slug. Should be a name like sorrento
				'default_latitude'  => '39.911607',
				'default_longitude' => '-100.853613',
				'zoom'              => 1,
				'zoom_events'       => 0,	 // Should map load markers when zoom changes
				'map_id'            => '',
				'output_map_div'    => true, // if == false, map will output to a div that already specified, so map_id should be sent to shortcode.
				'map_style_height'  => '500',
				'cluster_size'      => -1,
				'map_types'         => array( 'ROADMAP' ),
				'default_map_type'  => 'ROADMAP',
				'auto_zoom'         => 1,
			), $atts
		);

		// Changing post_type attribute to array
		if ( is_string( $this->attributes['post_type'] ) && trim( $this->attributes['post_type'] ) ) {
			$this->attributes['post_type'] = explode( ',', $this->attributes['post_type'] );
			array_map( 'trim', $this->attributes['post_type'] );
		}

		// If post_type is not array or has not any element return.
		if ( ! is_array( $this->attributes['post_type'] ) || ! count( $this->attributes['post_type'] ) ) {
			return '';
		}

		// Changing status attribute to array
		if ( ! empty( $this->attributes['status'] ) && is_string( $this->attributes['status'] ) ) {
			$this->attributes['status'] = explode( ',', $this->attributes['status'] );
			array_map( 'trim', $this->attributes['status'] );
		}

		// If status is not array or has not any element return.
		if ( ! is_array( $this->attributes['status'] ) || ! count( $this->attributes['status'] ) ) {
			return '';
		}

		// Changing location attribute to array.
		if ( ! empty( $this->attributes['location'] ) && ! is_array( $this->attributes['location'] ) ) {
			$this->attributes['location'] = array_map( 'trim', explode( ',', $this->attributes['location'] ) );
		}

		// Changing map_types to array
		if ( is_string( $this->attributes['map_types'] ) && strlen( trim( $this->attributes['map_types'] ) ) ) {
			$this->attributes['map_types'] = array_map( 'trim', explode( ',', $this->attributes['map_types'] ) );
		} else if ( ! is_array( $this->attributes['map_types'] ) ) {
			$this->attributes['map_types'] = array( 'ROADMAP' );
		}

		// Showing only current page listings.
		if ( $this->attributes['page_properties'] ) {
			$this->current_page_properties_map();
			return '';
		}

		return $this->create_map();
	}

	/**
	 * Creating map based on post type and conditions passed to shortcode.
	 *
	 * @since   1.0.0
	 * @return  string
	 */
	protected function create_map() {
		$args = array(
			'post_type'      => $this->attributes['post_type'],
			'posts_per_page' => (int) $this->attributes['limit'],
			'orderby'        => 'date',
			'order'          => $this->attributes['order'],
			'meta_query'     => array(
				array(
					'key'     => 'property_status',
					'value'   => $this->attributes['status'],
					'compare' => 'IN',
				),
			),
		);
		// Adding locations to query.
		if ( ! empty( $this->attributes['location'] ) ) {
			$args['tax_query'][] = array(
				'taxonomy' => 'location',
				'field'    => 'slug',
				'terms'    => $this->attributes['location'],
			);
		}

		// Getting settings.
		$elm_settings = ELM_IOC::make( 'settings' )->get_settings();
		$map_styles   = isset( $elm_settings['map_styles'] ) ? trim( $elm_settings['map_styles'] ) : '';

		$elm_google_maps_render = new ELM_Public_Google_Map_Render(
			array(
				'listings'          => new WP_Query( $args ),
				'map_id'            => trim( $this->attributes['map_id'] ) ? trim( $this->attributes['map_id'] ) : 'elm_google_maps_' . current_time( 'timestamp' ),
				'output_map_div'    => $this->attributes['output_map_div'],
				'content'           => $this->content,
				'map_style_height'  => $this->attributes['map_style_height'],
				'default_latitude'  => $this->attributes['default_latitude'],
				'default_longitude' => $this->attributes['default_longitude'],
				'zoom'              => absint( $this->attributes['zoom'] ),
				'zoom_events'       => absint( $this->attributes['zoom_events'] ),
				'cluster_size'      => (int) $this->attributes['cluster_size'],
				'map_types'         => $this->attributes['map_types'],
				'default_map_type'  => trim( $this->attributes['default_map_type'] ),
				'auto_zoom'         => absint( $this->attributes['auto_zoom'] ),
				'clustering'        => true,
				'map_styles'        => $map_styles,
			)
		);
		ob_start();
		$elm_google_maps_render->create_map();
		return ob_get_clean();
	}

	/**
	 * Creating a map based on listings in the current page.
	 *
	 * @since  1.2.0
	 * @return void
	 */
	public function current_page_properties_map() {
		// Getting settings.
		$elm_settings = ELM_IOC::make( 'settings' )->get_settings();
		$map_styles   = isset( $elm_settings['map_styles'] ) ? trim( $elm_settings['map_styles'] ) : '';

		$elm_google_maps_render = new ELM_Public_Google_Map_Render(
			array(
				'listings'          => $GLOBALS['wp_query'],
				'map_id'            => trim( $this->attributes['map_id'] ) ? trim( $this->attributes['map_id'] ) : 'elm_google_maps_' . current_time( 'timestamp' ),
				'output_map_div'    => $this->attributes['output_map_div'],
				'content'           => $this->content,
				'map_style_height'  => $this->attributes['map_style_height'],
				'default_latitude'  => $this->attributes['default_latitude'],
				'default_longitude' => $this->attributes['default_longitude'],
				'zoom'              => absint( $this->attributes['zoom'] ),
				'zoom_events'       => absint( $this->attributes['zoom_events'] ),
				'cluster_size'      => (int) $this->attributes['cluster_size'],
				'map_types'         => $this->attributes['map_types'],
				'default_map_type'  => trim( $this->attributes['default_map_type'] ),
				'auto_zoom'         => absint( $this->attributes['auto_zoom'] ),
				'clustering'        => true,
				'map_styles'        => $map_styles,
			)
		);
		ob_start();
		$elm_google_maps_render->create_map();
		echo ob_get_clean();
	}

}
