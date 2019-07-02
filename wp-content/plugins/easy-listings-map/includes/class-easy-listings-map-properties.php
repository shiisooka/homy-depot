<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * APIs that are common between property types.
 *
 * @since 	   1.0.0
 * @package    Easy_Listings_Map
 * @subpackage Easy_Listings_Map/includes
 * @author     Taher Atashbar <taher.atashbar@gmail.com>
 */

class ELM_Properties {

	/**
	 * Getting properties type associated with their status.
	 *
	 * @since 1.0.0
	 * @return array properties type associated with status.
	 */
	public function get_properties_status( $active_properties = false ) {
		$return_properties_status = array();

		$properties_status = apply_filters( 'epl_opts_property_status_filter', array(
			'current'	=>	__( 'Current', 'epl' ),
			'withdrawn'	=>	__( 'Withdrawn', 'epl' ),
			'offmarket'	=>	__( 'Off Market', 'epl' ),
			'sold'		=>	array(
				'label'		=>	__( 'Sold', 'epl' ),
				'exclude'	=>	array( 'rental' )
			),
			'leased'		=>	array(
				'label'		=>	__( 'Leased', 'epl' ),
				'include'	=>	array( 'rental', 'commercial', 'commercial_land', 'business' )
			)
		) );
		// Getting property_types for registering markers for each of them.
		if ( $active_properties ) {
			// Getting active property types
			$post_types = epl_get_active_post_types();
		} else {
			// Getting all of property types
			$post_types = epl_get_post_types();
		}

		if ( count( $post_types ) && count( $properties_status ) ) {
			foreach ( $post_types as $post_type_key => $post_type_value ) {
				$return_properties_status[ $post_type_key ] = array(
					'name'   => $post_type_value,
					'status' => array(),
				);
				foreach ( $properties_status as $properties_status_key => $properties_status_value ) {
					if ( is_string( $properties_status_value ) ) {
						$return_properties_status[ $post_type_key ]['status'][ $properties_status_key ] = $properties_status_value;
					} else if ( is_array( $properties_status_value ) ) {
						if ( isset( $properties_status_value['include'] ) && in_array( $post_type_key, $properties_status_value['include'] ) ) {
							$return_properties_status[ $post_type_key ]['status'][ $properties_status_key ] = $properties_status_value['label'];
						} else if ( isset( $properties_status_value['exclude'] ) && ! in_array( $post_type_key, $properties_status_value['exclude'] ) ) {
							$return_properties_status[ $post_type_key ]['status'][ $properties_status_key ] = $properties_status_value['label'];
						}
					}
				}
			}
		}

		return $return_properties_status;
	}

	/**
	 * Getting all of status defined in system
	 *
	 * @since 1.0.0
	 * @return array array of proeprties defined status
	 */
	public function get_all_status() {
		$properties_status = apply_filters( 'epl_opts_property_status_filter', array(
			'current'	=>	__( 'Current', 'epl' ),
			'withdrawn'	=>	__( 'Withdrawn', 'epl' ),
			'offmarket'	=>	__( 'Off Market', 'epl' ),
			'sold'		=>	array(
				'label'		=>	__( 'Sold', 'epl' ),
				'exclude'	=>	array( 'rental' )
			),
			'leased'		=>	array(
				'label'		=>	__( 'Leased', 'epl' ),
				'include'	=>	array( 'rental', 'commercial', 'commercial_land', 'business' )
			)
		) );
		$return_properties_status = array();
		if ( count( $properties_status ) ) {
			foreach ( $properties_status as $status => $status_name ) {
				if ( is_string( $status_name ) ) {
					$return_properties_status[ $status ] = $status_name;
				} else if ( is_array( $status_name ) ) {
					$return_properties_status[ $status ] = $status_name['label'];
				}
			}
		}
		return $return_properties_status;
	}

	/**
	 * Getting property marker based on it's type and status
	 *
	 * @since  1.0.0
	 * @param  string $property_type   type of post or property
	 * @param  string $property_status status of property
	 * @return string                  marker of property
	 */
	public function get_property_marker( $property_type, $property_status ) {
		// Getting saved marker based on property type and status.
		$elm_settings = ELM_IOC::make( 'settings' )->get_settings();
		if ( ! empty( $elm_settings[ $property_type . '_' . $property_status . '_marker' ] ) ) {
			return esc_url( trim( $elm_settings[ $property_type . '_' . $property_status . '_marker' ] ) );
		}

		// Getting default marker based on property type and status.
		$plugin_admin = ELM_IOC::make( 'plugin_admin' );

		$status_folder = $property_status;
		if ( 'leased' === $property_status ) {
			$status_folder = 'sold';
		} else if ( 'withdrawn' === $property_status ) {
			$status_folder = 'offmarket';
		}

		if ( file_exists( $plugin_admin->get_path() . 'images/markers/' . $status_folder . '/' . $property_type . '.png' ) ) {
			// Using specific type_status std marker.
			$marker = $plugin_admin->get_images_folder() . 'markers/' . $status_folder . '/' . $property_type . '.png';
		} else {
			// Using default marker.
			$marker = $plugin_admin->get_images_folder() . 'markers/' . 'default-marker.png';
		}

		return $marker;
	}

	/**
	 * Getting coordinates( latitude and longitude ) of property.
	 *
	 * @since 1.0.0
	 * @param  int $property_id
	 * @return array           an array of property coordinates or empty array.
	 */
	public function get_property_coordinates( $property_id ) {
		$address_coordinates = get_post_meta( $property_id, 'property_address_coordinates', true );
		if ( strlen( trim( $address_coordinates ) ) ) {
			$coordinates = explode( ',', $address_coordinates, 2 );
			// Checking coordinates for lat and lon
			if ( trim( $coordinates[0] ) && trim( $coordinates[1] ) ) {
				return array(
					'latitude'  => (float) trim( $coordinates[0] ),
					'longitude' => (float) trim( $coordinates[1] )
				);
			}
		}

		return array();
	}

	/**
	 * Getting property bedrooms
	 *
	 * @since 1.0.0
	 * @param  int $property_id    when null it uses current post id.
	 * @param  string $return_type
	 * @return string
	 */
	public function get_property_bed( $property_id = null, $return_type = 'i' ) {
		// Use current post ID as property_id
		$property_id       = ! is_null( $property_id ) ? $property_id : get_the_ID();
		$property_bedrooms = get_post_meta( $property_id, 'property_bedrooms', true );
		if ( ! strlen( trim( $property_bedrooms ) ) ) {
			return '';
		}

		if ( 'i' === $return_type ) {
			return '<span title="' . __( 'Bedrooms', 'epl' ) . '" class="icon beds"><span class="icon-value">' . $property_bedrooms . '</span></span>';
		} else if ( 'd' === $return_type ) {
			return $property_bedrooms . ' ' . __( 'bed', 'epl' ). ' ';
		} else if ( 'l' === $return_type ) {
			return '<li class="bedrooms">' . $property_bedrooms . ' '. __( 'bed', 'epl' ) . '</li>';
		}
		return '';
	}

	/**
	 * Getting property bathrooms
	 *
	 * @since 1.0.0
	 * @param  int $property_id    when null it uses current post id.
	 * @param  string $return_type
	 * @return string
	 */
	public function get_property_bath( $property_id = null, $return_type = 'i' ) {
		// Use current post ID as property_id
		$property_id        = ! is_null( $property_id ) ? $property_id : get_the_ID();
		$property_bathrooms = get_post_meta( $property_id, 'property_bathrooms', true );
		if ( ! strlen( trim( $property_bathrooms ) ) ) {
			return '';
		}

		if ( 'i' === $return_type ) {
			return '<span title="' . __( 'Bathrooms', 'epl' ) . '" class="icon bath"><span class="icon-value">' . $property_bathrooms . '</span></span>';
		} else if ( 'd' === $return_type ) {
			return $property_bathrooms . ' ' . __( 'bath', 'epl' ) . ' ';
		} else if ( 'l' === $return_type ) {
			return '<li class="bathrooms">' . $property_bathrooms . ' ' . __( 'bath', 'epl' ) . '</li>';
		}
		return '';
	}

	/**
	 * Getting property parking
	 *
	 * @since 1.0.0
	 * @param  int $property_id    when null it uses current post id.
	 * @param  string $return_type
	 * @return string
	 */
	public function get_property_parking( $property_id = null, $return_type = 'i' ) {
		// Use current post ID as property_id
		$property_id      = ! is_null( $property_id ) ? $property_id : get_the_ID();
		$property_garage  = get_post_meta( $property_id, 'property_garage', true );
		$property_carport = get_post_meta( $property_id, 'property_carport', true );
		if ( ! strlen( trim( $property_garage ) ) && ! strlen( trim( $property_carport ) ) ) {
			return '';
		}

		$property_parking = (int) $property_garage + (int) $property_carport;
		if ( 'i' === $return_type ) {
			return '<span title="' . __( 'Parking Spaces', 'epl' ) . '" class="icon parking"><span class="icon-value">' . $property_parking . '</span></span>';
		} else if ( 'd' === $return_type ) {
			return $property_parking . ' '. __( 'Parking Spaces', 'epl' ) . ' ';
		} else if ( 'l' === $return_type ) {
			return '<li class="parking">' . $property_parking . ' ' . __( 'Parking Spaces', 'epl' ) . '</li>';
		}
		return '';
	}

	/**
	 * Getting property air conditioning
	 *
	 * @since 1.0.0
	 * @param  int $property_id    when null it uses current post id.
	 * @param  string $return_type
	 * @return string
	 */
	public function get_property_air_conditioning( $property_id = null, $return_type = 'i' ) {
		// Use current post ID as property_id
		$property_id               = ! is_null( $property_id ) ? $property_id : get_the_ID();
		$property_air_conditioning = get_post_meta( $property_id, 'property_air_conditioning', true );
		if ( in_array( $property_air_conditioning, array( '1', 'yes' ) ) ) {
			if ( 'i' === $return_type ) {
				return '<span title="' . __( 'Air Conditioning', 'epl' ) . '" class="icon air"></span>';
			} else if ( 'l' === $return_type ) {
				return '<li class="air">' . __( 'Air conditioning', 'epl' ) . '</li>';
			}
		}
		return '';
	}

	/**
	 * Getting property pool
	 *
	 * @since 1.0.0
	 * @param  int $property_id    when null it uses current post id.
	 * @param  string $return_type
	 * @return string
	 */
	public function get_property_pool( $property_id = null, $return_type = 'i' ) {
		// Use current post ID as property_id
		$property_id   = ! is_null( $property_id ) ? $property_id : get_the_ID();
		$property_pool = get_post_meta( $property_id, 'property_pool', true );
		if ( in_array( $property_pool, array( '1', 'yes' ) ) ) {
			if ( 'i' === $return_type ) {
				return '<span title="' . __( 'Pool', 'epl' ) . '" class="icon pool"></span>';
			} else if ( 'l' === $return_type ) {
				return '<li class="pool">' . __( 'Pool', 'epl' ) . '</li>';
			}
		}
		return '';
	}

	/**
	 * Getting property icons.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_property_icons() {
		if ( function_exists( 'epl_get_property_icons' ) ) {
			return epl_get_property_icons();
		}

		return $this->get_property_bed() .
			$this->get_property_bath() .
			$this->get_property_parking() .
			$this->get_property_air_conditioning() .
			$this->get_property_pool();
	}

	/**
	 * Checking is a post type Easy Property Listings type.
	 *
	 * @since  1.2.0
	 * @param  string  $post_type
	 * @return boolean
	 */
	public function is_epl_post_type( $post_type ) {
		$epl_posts  = epl_get_active_post_types();
		$epl_posts	= apply_filters( 'epl_additional_post_types', array_keys( $epl_posts ) );
		return in_array( $post_type, $epl_posts );
	}

}
