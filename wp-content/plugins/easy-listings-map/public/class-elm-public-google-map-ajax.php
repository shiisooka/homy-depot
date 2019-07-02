<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The controller class responsible for ajax functionality of Google Maps.
 *
 * @since      1.2.0
 * @package    Easy_Listings_Map
 * @subpackage Easy_Listings_Map/public
 * @author     Taher Atashbar <taher.atashbar@gmail.com>
 */

class ELM_Public_Google_Map_Ajax extends ELM_Public_Controller {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.2.0
	 * @access   private
	 * @var      Easy_Listings_Map_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	private $loader;

	/**
	 * Constructor.
	 *
	 * @since 1.2.0
	 * @param Easy_Listings_Map_Loader $loader [description]
	 */
	public function __construct( Easy_Listings_Map_Loader $loader ) {
		$this->loader = $loader;
	}

	/**
	 * Defining hooks of the class.
	 *
	 * @since  1.2.0
	 * @return void
	 */
	public function define_hooks() {
		// Registering actions for loading markers in the map in ajax request.
		$this->loader->add_action( 'wp_ajax_load_map_markers', $this, 'load_map_markers' );
		$this->loader->add_action( 'wp_ajax_nopriv_load_map_markers', $this, 'load_map_markers' );
		// Registering ajax actions to respond listings search.
		$this->loader->add_action( 'wp_ajax_elm_search_listings', $this, 'search_listings' );
		$this->loader->add_action( 'wp_ajax_nopriv_elm_search_listings', $this, 'search_listings' );
	}

	/**
	 * Ajax Loading markers that are in map bounds.
	 *
	 * @since  1.2.0
	 * @return void
	 */
	public function load_map_markers() {
		// Checking nonce.
		if ( ! wp_verify_nonce( $_POST['nonce'], 'elm_bound_markers' ) ) {
			die( json_encode( array( 'success' => 0, 'message' => 'Security check!' ) ) );
		}
		$elm_properties = ELM_IOC::make( 'properties' );
		$south_west_lat = (float) $_POST['southWestLat'];
		$south_west_lng = (float) $_POST['southWestLng'];
		$north_east_lat = (float) $_POST['northEastLat'];
		$north_east_lng = (float) $_POST['northEastLng'];
		$cluster_size   = (int) $_POST['cluster_size'];
		$query_vars     = $_POST['query_vars'];
		// Checking Query Variables.
		if ( ! is_array( $query_vars ) || ! isset( $query_vars['post_type'] ) ) {
			die( json_encode( array( 'success' => 0, 'message' => 'Query vars error!' ) ) );
		}
		// Checking post_type of query vars.
		if ( is_array( $query_vars['post_type'] ) ) {
			$post_types = array();
			foreach ( $query_vars['post_type'] as $post_type ) {
				if ( $elm_properties->is_epl_post_type( $post_type ) ) {
					$post_types[] = $post_type;
				}
			}
			$query_vars['post_type'] = $post_types;
		} else if ( is_string( $query_vars['post_type'] ) && ! $elm_properties->is_epl_post_type( $query_vars['post_type'] ) ) {
			die( json_encode( array( 'success' => 0, 'message' => 'Query vars error!' ) ) );
		}
		/**
		 * Setting posts_per_page to -1 to getting all of listings.
		 * We should load all of listings in order to find only listings that are in bound.
		 * Because we can't use query to find only in bound listings.
		 */
		$query_vars['posts_per_page'] = -1;

		// Markers that should be returned.
		$markers = array();
		if ( ! empty( $query_vars['post_type'] ) && ! is_nan( $south_west_lat ) && ! is_nan( $south_west_lng )
			&& ! is_nan( $north_east_lat ) && ! is_nan( $north_east_lng ) ) {

			$properties = new WP_Query( $query_vars );
			if ( $properties->have_posts() ) {
				// Getting ELM_Location class.
				$elm_location      = ELM_IOC::make( 'location' );
				$google_map_marker = new ELM_Public_Google_Map_Marker();
				while ( $properties->have_posts() ) {
					$properties->the_post();
					$property_coordinates = $elm_properties->get_property_coordinates( get_the_ID() );
					if ( count( $property_coordinates ) ) {
						// Is property in bounds of map.
						if ( $elm_location->is_in_bound( $property_coordinates['latitude'], $property_coordinates['longitude'],
								$south_west_lat, $south_west_lng, $north_east_lat, $north_east_lng ) ) {
							// Adding property marker to markers.
							$google_map_marker->set_property_marker( $markers );
						}
					}
				}
				wp_reset_postdata();

				// Merging markers that are in same coordinates.
				if ( count( $markers ) ) {
					$markers = $google_map_marker->merge_markers( $markers );
				}
			}
		}

		die( json_encode( array( 'success' => 1, 'markers' => $markers ) ) );
	}

	/**
	 * Ajax loading listings search result of EPL.
	 *
	 * @since  1.2.0
	 * @return void
	 */
	public function search_listings() {
		// Checking nonce.
		if ( ! wp_verify_nonce( $_REQUEST['nonce'], 'elm_search_listings' ) ) {
			die( json_encode( array( 'success' => 0, 'message' => 'Security check!' ) ) );
		}
		$listings     = '';
		$markers      = array();
		$search_data  = wp_parse_args( $_REQUEST['data'] );
		$search_query = new WP_Query();
		epl_search( $search_query, $search_data, true );
		if ( $search_query->have_posts() ) {
			$google_map_marker = new ELM_Public_Google_Map_Marker();
			ob_start();
			while ( $search_query->have_posts() ) {
				$search_query->the_post();
				$google_map_marker->set_property_marker( $markers );
				do_action( 'epl_property_blog' );
			}
			$listings = ob_get_clean();
			wp_reset_postdata();

			// Merging markers that are in same coordinates.
			if ( count( $markers ) ) {
				$markers = $google_map_marker->merge_markers( $markers );
			}
		} else {
			// Default listings not found message.
			$listings = '<div class="bottom_sixty">' . apply_filters( 'epl_property_search_not_found_message' , __( 'Listing not found, expand your search criteria and try again.', 'epl' ) ) . '</div>';
		}

		die( json_encode( array( 'success' => 1, 'listings' => $listings, 'markers' => $markers ) ) );
	}

}
