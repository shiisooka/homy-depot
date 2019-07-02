<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The controller class responsible for markers functionality.
 *
 * @since      1.2.0
 * @package    Easy_Listings_Map
 * @subpackage Easy_Listings_Map/public
 * @author     Taher Atashbar <taher.atashbar@gmail.com>
 */

class ELM_Public_Google_Map_Marker extends ELM_Public_Controller {

	/**
	 * An instance of ELM_Properties class.
	 *
	 * @since 1.2.0
	 * @var   ELM_Properties
	 */
	private $elm_properties;

	/**
	 * Constructor.
	 *
	 * @since 1.2.0
	 * @param ELM_Properties|null $elm_properties
	 */
	public function __construct( ELM_Properties $elm_properties = null ) {
		$this->elm_properties = null === $elm_properties ? ELM_IOC::make( 'properties' ) : $elm_properties;
	}

	/**
	 * Adding property coordinates ( latitude and longitude ) and other information about property to markers.
	 *
	 * @since   1.2.0
	 * @param   array $markers
	 */
	public function set_property_marker( array & $markers, $listing = null ) {
		if ( null === $listing ) {
			$listing_id = get_the_ID();
		} else if ( $listing instanceof WP_Post ) {
			$listing_id = $listing->ID;
		} else {
			$listing_id = absint( $listing );
		}
		$property_coordinates = $this->elm_properties->get_property_coordinates( $listing_id );
		if ( count( $property_coordinates ) ) {
			// Getting extra info about property, like it's image and etc.
			$image_url = '';
			if ( has_post_thumbnail() ) {
				$image_url = wp_get_attachment_image_src( get_post_thumbnail_id(), 'epl-image-medium-crop' );
			}
			$image_url = ! empty( $image_url ) ? $image_url[0] : $this->get_images_url() . 'map/default-infowindow-image.png';
			/**
			 * Setting property marker icon.
			 * Using marker that set in settings or use default marker for it.
			 */
			$property_status = get_post_meta( get_the_ID(), 'property_status', true );
			$marker_icon     = $this->elm_properties->get_property_marker( get_post_type(), $property_status );
			$title           = get_the_title();

			$markers[] = apply_filters( 'elm_marker_info',
				array(
					'listing_id'      => get_the_ID(),
					'latitude'        => $property_coordinates['latitude'],
					'longitude'       => $property_coordinates['longitude'],
					'image_url'       => $image_url,
					'url'             => esc_url( get_permalink() ),
					'title'           => substr( $title, 0, 25 ) . ( strlen( $title ) > 25 ? '...' : '' ),
					'tab_title'       => substr( $title, 0, 10 ) . ( strlen( $title ) > 10 ? '...' : '' ),
					'icons'           => $this->elm_properties->get_property_icons(),
					// 'price'        => epl_get_property_price(),
					'marker_icon'     => esc_url( $marker_icon ),
					'property_status' => $property_status,
					'property_type'   => get_post_type(),
				),
				$listing_id
			);
		}
	}

	/**
	 * Merging markers if they are in same coordinates.
	 *
	 * @since   1.2.0
	 * @param   array $markers
	 * @return  array
	 */
	public function merge_markers( array $markers ) {
		$merged_markers = array();
		if ( count( $markers ) ) {
			// Getting multiple marker icon.
			$elm_settings    = ELM_IOC::make( 'settings' )->get_settings();
			$multiple_marker = ! empty( $elm_settings['map_multiple_marker'] ) ? trim( $elm_settings['map_multiple_marker'] ) :
				ELM_IOC::make( 'asset_manager' )->get_admin_images() . 'markers/multiple.png';

			for ( $i = 0; $i < count( $markers ); $i++ ) {
				// Did merged current marker already so don't use it again.
				if ( isset( $markers[ $i ]['merged'] ) ) {
					continue;
				}
				$merged_marker = array(
					'listing_id'  => array( $markers[ $i ]['listing_id'] ),
					'latitude'    => $markers[ $i ]['latitude'],
					'longitude'   => $markers[ $i ]['longitude'],
					'marker_icon' => $markers[ $i ]['marker_icon'],
					'info'        => array(
						$markers[ $i ],
					),
				);

				for ( $j = 0; $j < count( $markers ); $j++ ) {
					if ( $i == $j ) {
						continue;
					}
					if ( $markers[ $i ]['latitude'] == $markers[ $j ]['latitude'] && $markers[ $i ]['longitude'] == $markers[ $j ]['longitude'] ) {
						// Merging details of markers that are in same coordinates.
						$merged_marker['info'][]       = $markers[ $j ];
						// Adding listing id to merged marker.
						$merged_marker['listing_id'][] = $markers[ $j ]['listing_id'];
						// Setting marker icon to multiple property icon.
						$merged_marker['marker_icon']  = esc_url( $multiple_marker );
						// Marker that is in position j are merged so don't use it again.
						$markers[ $j ]['merged']       = true;
					}
				}
				$merged_markers[] = $merged_marker;
			}
		}
		return $merged_markers;
	}

}
