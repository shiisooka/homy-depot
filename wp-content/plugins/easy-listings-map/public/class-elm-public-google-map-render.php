<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The controller class for rendering Google Maps.
 *
 * @since      1.2.0
 * @package    Easy_Listings_Map
 * @subpackage Easy_Listings_Map/public
 * @author     Taher Atashbar <taher.atashbar@gmail.com>
 */

class ELM_Public_Google_Map_Render extends ELM_Public_Controller {

	/**
	 * Properties of the class.
	 *
	 * @since 1.2.0
	 * @var   array
	 */
	private $data = array(
		'listings'          => null,				// An instance of WP_Query class.
		'map_id'            => '',
		'output_map_div'    => true,
		'content'           => '',
		'map_style_height'  => 500,
		'default_latitude'  => '39.911607',
		'default_longitude' => '-100.853613',
		'zoom'              => 1,
		'zoom_events'       => 0,
		'cluster_size'      => -1,
		'map_types'         => array( 'ROADMAP' ),
		'default_map_type'  => 'ROADMAP',
		'auto_zoom'         => 1,
		'clustering'        => true,
		'map_styles'        => '',
	);

	/**
	 * Markers of listings that generated inside of the class.
	 *
	 * @since 1.2.0
	 * @var   string
	 */
	private $markers = '';

	/**
	 * Constructor.
	 *
	 * @since 1.2.0
	 * @param array               $data
	 * @param ELM_Properties|null $elm_properties
	 */
	public function __construct( array $data = array(), ELM_Public_Google_Map_Marker $google_map_marker = null ) {
		$this->google_map_marker = null === $google_map_marker ? new ELM_Public_Google_Map_Marker() : $google_map_marker;
		if ( count( $data ) ) {
			foreach ( $data as $key => $value ) {
				if ( array_key_exists( $key, $this->data ) ) {
					$this->data[ $key ] = $value;
				}
			}
		}
		if ( ! strlen( trim( $this->data['map_id'] ) ) ) {
			$this->data['map_id'] = 'elm_google_maps_' . current_time( 'timestamp' );
		}
		// Changing map_types to array
		if ( is_string( $this->data['map_types'] ) && strlen( trim( $this->data['map_types'] ) ) ) {
			$this->data['map_types'] = array_map( 'trim', explode( ',', $this->data['map_types'] ) );
		} else if ( ! is_array( $this->data['map_types'] ) ) {
			$this->data['map_types'] = array( 'ROADMAP' );
		}
	}

	/**
	 * Creating a map based on listings property of the object.
	 *
	 * @since  1.2.0
	 * @return void
	 */
	public function create_map() {
		$markers = array();
		if ( $this->data['listings'] instanceof WP_Query ) {
			// Checking post types of query against epl post types.
			$elm_properties = ELM_IOC::make( 'properties' );
			$query_post_type = $this->data['listings']->get( 'post_type' );
			$is_epl_post_type = true;
			if ( is_array( $query_post_type ) ) {
				foreach ( $query_post_type as $post_type ) {
					if ( ! $elm_properties->is_epl_post_type( $post_type ) ) {
						$is_epl_post_type = false;
						break;
					}
				}
			} else if ( ! $elm_properties->is_epl_post_type( $query_post_type ) ) {
				$is_epl_post_type = false;
			}

			if ( $is_epl_post_type && $this->data['listings']->have_posts() ) {
				while ( $this->data['listings']->have_posts() ) {
					$this->data['listings']->the_post();
					// Adding property marker with it's information to markers array.
					$this->google_map_marker->set_property_marker( $markers );
				}
				wp_reset_postdata();
			}
		}
		$this->draw_map( $markers );
	}

	/**
	 * Drawing a map in front-end based on markers sent to it.
	 *
	 * @since   1.2.0
	 * @param   array 	$markers
	 * @return  string
	 */
	public function draw_map( array $markers ) {
		// Merging markers that are in same coordinates.
		$markers       = $this->google_map_marker->merge_markers( $markers );
		// Adding markers to markers property of the class.
		$this->markers = json_encode( $markers );
		$data          = array(
			'controller'        => $this,
			'content'           => trim( $this->data['content'] ),
			'map_id'            => $this->data['map_id'],
			'map_types'         => $this->data['map_types'],
			'default_map_type'  => trim( $this->data['default_map_type'] ),
			'zoom'              => (int) $this->data['zoom'],
			'zoom_events'       => absint( $this->data['zoom_events'] ),
			'height'            => $this->data['map_style_height'],
			'js_url'            => $this->get_js_url(),
			'css_url'           => $this->get_css_url(),
			'images_url'        => $this->get_images_url(),
			'cluster_size'      => (int) $this->data['cluster_size'],
			'default_latitude'  => $this->data['default_latitude'],
			'default_longitude' => $this->data['default_longitude'],
			'auto_zoom'         => $this->data['auto_zoom'],
			'markers'           => $this->markers,
			'query_vars'        => $this->data['listings'] instanceof WP_Query ? $this->data['listings']->query_vars : '',
			'map_styles'		=> trim( $this->data['map_styles'] ),
		);
		/*
		 * if $output_map_div == 0 don't output map div. In other words developer wants
		 * to output map to else where by specifying map output_div and it's id.
		 */
		if ( $this->data['output_map_div'] ) {
			$this->render_view( 'google-map-render.default', $data );
		} else {
			$this->render_view( 'google-map-render.scripts', $data );
		}
	}

	/**
	 * Registering scripts ans styles of Google Maps.
	 *
	 * @since  1.2.0
	 * @return void
	 */
	public function register_scripts() {
		$api_key    = epl_get_option( 'epl_google_api_key' );
		$api_key    = empty( $api_key ) ? 'AIzaSyCBpgWp8d61yCDUgJVcy1-MOUogxbSzVRI' : $api_key;

		// Registering scripts.
		$protocol   = is_ssl() ? 'https' : 'http';
		// Use minified libraries if SCRIPT_DEBUG is turned off
		$suffix     = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
		$js_url     = $this->get_js_url();
		$css_url    = $this->get_css_url();
		$images_url = $this->get_images_url();
		wp_register_script( 'elm-google-map-v-3', $protocol . '://maps.googleapis.com/maps/api/js?v=3.exp' . ( ! empty( $api_key ) ? '&key=' . esc_attr( $api_key ) : '' ) );
		wp_register_script( 'google-maps-clusters', $js_url . 'maps/markerclusterer' . $suffix . '.js',
			array(), false, true );
		wp_register_script( 'google-maps-infobubble', $js_url . 'maps/infobubble' . $suffix . '.js',
			array(), false, true );
		wp_enqueue_script( 'elm_google_maps', $js_url . 'maps/elm-google-maps' . $suffix . '.js',
			array( 'jquery', 'elm-google-map-v-3', 'google-maps-clusters', 'google-maps-infobubble' ), false, true );
		$elm_google_maps = array(
			'nonce'             => wp_create_nonce( 'elm_bound_markers' ),
			'query_vars'        => $this->data['listings'] instanceof WP_Query ? $this->data['listings']->query_vars : '',
			'markers'           => $this->markers,
			'default_latitude'  => $this->data['default_latitude'],
			'default_longitude' => $this->data['default_longitude'],
			'auto_zoom'         => $this->data['auto_zoom'],
			'map_id'            => $this->data['map_id'],
			'map_types'         => $this->data['map_types'],
			'default_map_type'  => $this->data['default_map_type'],
			'zoom'              => (int) $this->data['zoom'],
			'zoom_events'       => absint( $this->data['zoom_events'] ),
			'cluster_size'      => (int) $this->data['cluster_size'],
			'info_window_close' => $images_url . 'map/info-window-close-button.png',
			'map_styles'        => trim( $this->data['map_styles'] ),
			'cluster_style'     => array(
				(object) array(
					'url'       => $images_url . 'map/m1.png',
					'height'    => 53,
					'width'     => 53,
				),
				(object) array(
					'url'       => $images_url . 'map/m2.png',
					'height'    => 56,
					'width'     => 56,
				),
				(object) array(
					'url'       => $images_url . 'map/m3.png',
					'height'    => 66,
					'width'     => 66,
				),
				(object) array(
					'url'       => $images_url . 'map/m4.png',
					'height'    => 78,
					'width'     => 78,
				),
				(object) array(
					'url'       => $images_url . 'map/m5.png',
					'height'    => 90,
					'width'     => 90,
				),
			),
		);
		wp_localize_script( 'elm_google_maps', 'elm_google_maps', $elm_google_maps );
		// Registering styles.
		wp_enqueue_style( 'elm-google-maps', $css_url . 'elm-google-maps' . $suffix . '.css' );
	}

}
