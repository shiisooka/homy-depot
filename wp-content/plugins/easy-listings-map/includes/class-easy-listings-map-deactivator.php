<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Fired during plugin deactivation
 *
 * @link       http://www.asanaplugins.com/
 * @since      1.0.0
 *
 * @package    Easy_Listings_Map
 * @subpackage Easy_Listings_Map/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Easy_Listings_Map
 * @subpackage Easy_Listings_Map/includes
 * @author     Taher Atashbar <taher.atashbar@gmail.com>
 */
class Easy_Listings_Map_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
		// Delete when this is Asana active free plugin.
		$asana_plugin = get_option( 'asana_active_free_plugin', '' );
		if ( 'easy-listings-map' === $asana_plugin ) {
			delete_option( 'asana_active_free_plugin' );
		}
	}

}
