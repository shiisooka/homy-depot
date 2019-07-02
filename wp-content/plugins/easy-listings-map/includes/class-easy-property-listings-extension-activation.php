<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * EPL Extension Activation Handler Class
 *
 * @since      1.0.0
 * @package    Easy_Listings_Map
 * @subpackage Easy_Listings_Map/includes
 * @author     Taher Atashbar <taher.atashbar@gmail.com>
 */
class EPL_Extension_Activation {

	private $plugin_name;
	private $has_epl;
	private $epl_base;

	/**
	 * Setup the activation class
	 *
	 * @access      public
	 * @since       1.0.0
	 * @return      void
	 */
	public function __construct( $plugin_basename ) {
		// We need plugin.php!
		require_once( ABSPATH . 'wp-admin/includes/plugin.php' );

		$plugins = get_plugins();

		// Set plugin name
		if ( isset( $plugins[ $plugin_basename ]['Name'] ) ) {
			$this->plugin_name = $plugins[ $plugin_basename ]['Name'];
		} else {
			$this->plugin_name = __( 'Easy Property Listings Extension', 'epl' );
		}

		// Is EPL installed?
		foreach ( $plugins as $plugin_path => $plugin ) {
			if ( $plugin['Name'] == 'Easy Property Listings' ) {
				$this->has_epl = true;
				$this->epl_base = $plugin_path;
				break;
			}
		}
	}

	/**
	 * Process plugin deactivation
	 *
	 * @access      public
	 * @since       1.0.0
	 * @return      void
	 */
	public function run() {
		// Display notice
		add_action( 'admin_notices', array( $this, 'missing_epl_notice' ) );
	}

	/**
	 * Display notice if EPL isn't installed/activated
	 *
	 * @access      public
	 * @since       1.0.0
	 * @return      string The notice to display
	 */
	public function missing_epl_notice() {
		if ( $this->has_epl ) {
			$url  = esc_url( wp_nonce_url( admin_url( 'plugins.php?action=activate&plugin=' . $this->epl_base ), 'activate-plugin_' . $this->epl_base ) );
			$link = '<a href="' . $url . '">' . __( 'activate it', 'epl-extension-activation' ) . '</a>';
		} else {
			$url  = esc_url( wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=easy-property-listings' ), 'install-plugin_easy-property-listings' ) );
			$link = '<a href="' . $url . '">' . __( 'install it', 'epl-extension-activation' ) . '</a>';
		}

		echo '<div class="error"><p>' . $this->plugin_name . sprintf( __( ' requires Easy Property Listings! Please %s to continue!', 'epl-extension-activation' ), $link ) . '</p></div>';
	}

}
