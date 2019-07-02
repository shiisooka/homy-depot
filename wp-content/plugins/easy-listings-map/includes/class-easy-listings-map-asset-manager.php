<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Asset manager for plugin in order to accessing asset folders of admin-facing and public-facing
 *
 * @since 1.0.0
 * @package    Easy_Listings_Map
 * @subpackage Easy_Listings_Map/includes
 * @author     Taher Atashbar <taher.atashbar@gmail.com>
 */

class ELM_Asset_Manager {

	private $plugin_public;
	private $plugin_admin;

	public function __construct( Easy_Listings_Map_Admin $plugin_admin, Easy_Listings_Map_Public $plugin_public ) {
		$this->plugin_admin = $plugin_admin;
		$this->plugin_public = $plugin_public;
	}

	/**
	 * Getting images folder url of admin-facing
	 * @return string
	 */
	public function get_admin_images() {
		return $this->plugin_admin->get_images_folder();
	}

	/**
	 * Getting images folder url of public-facing
	 * @return string
	 */
	public function get_public_images() {
		return $this->plugin_public->get_images_folder();
	}

}
