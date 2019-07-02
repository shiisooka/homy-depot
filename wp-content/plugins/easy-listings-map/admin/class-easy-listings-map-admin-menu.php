<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The admin-facing menu creator of the plugin.
 *
 * @package    Easy_Listings_Map
 * @subpackage Easy_Listings_Map/admin
 * @author     Taher Atashbar <taher.atashbar@gmail.com>
 */

class ELM_Admin_Menu {

	private $settings_menu;
	private $admin;
	private $menus;		// Array of easy-listings-map menus

	public function __construct( Easy_Listings_Map_Admin $admin ) {
		$this->admin = $admin;
		$this->menus = array();
		$this->load_dependencies();

		// Deffining settings menu.
		$this->settings_menu = new ELM_Admin_Settings_Menu( $this->admin, new ELM_Admin_Element( $this->admin ) );

		// Actions for creating menus of plugin
		$this->admin->get_loader()->add_action( 'admin_menu', $this, 'settings_menu' );
	}

	/**
	 * Loading dependencies of class.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	protected function load_dependencies() {
		require_once $this->admin->get_path() . 'class-easy-listings-map-admin-settings-menu.php';
	}

	/**
	 * Creating plugin settings menu.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function settings_menu() {
		$settings_menu = add_submenu_page( 'epl-general', __( 'Easy Listings Map Settings', 'elm' ), __( 'Easy Listings Map Settings', 'elm' ),
			'manage_options', 'elm-settings', array( $this->settings_menu, 'create_menu' ) );
		// Adding settings menu to easy-listings-map menus
		$this->menus[] = $settings_menu;
	}

	/**
	 * Getting all of admin-face menus of plugin.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public function get_menus() {
		return $this->menus;
	}

}
