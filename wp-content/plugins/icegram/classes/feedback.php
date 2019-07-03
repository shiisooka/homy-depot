<?php

/**
 * Get additional system & plugin specific information for feedback
 *
 */
if ( ! function_exists( 'ig_get_additional_info' ) ) {

	/**
	 * Get TLWP specific information
	 *
	 * @param $additional_info
	 * @param bool $system_info
	 *
	 * @return mixed
	 *
	 * @since 1.5.17
	 */
	function ig_get_additional_info( $additional_info, $system_info = false ) {
		global $icegram;
		$additional_info['version'] = $icegram->version;

		return $additional_info;

	}

}

add_filter( 'ig_additional_feedback_meta_info', 'ig_get_additional_info', 10, 2 );
