<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Fired during plugin activation
 *
 * @link       http://www.asanaplugins.com/
 * @since      1.0.0
 *
 * @package    Easy_Listings_Map
 * @subpackage Easy_Listings_Map/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Easy_Listings_Map
 * @subpackage Easy_Listings_Map/includes
 * @author     Taher Atashbar <taher.atashbar@gmail.com>
 */
class Easy_Listings_Map_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		// Setting Asana Free Active Plugin.
		$asana_plugin = get_option( 'asana_active_free_plugin', '' );
		if ( empty( $asana_plugin ) ) {
			update_option( 'asana_active_free_plugin', 'easy-listings-map' );
		}

		// Add the transient to redirect
		set_transient( '_elm_activation_redirect', true, 30 );
	}

}
