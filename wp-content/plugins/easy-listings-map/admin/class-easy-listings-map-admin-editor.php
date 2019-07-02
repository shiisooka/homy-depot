<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The admin-facing editor manager of plugin.
 *
 * @package    Easy_Listings_Map
 * @subpackage Easy_Listings_Map/admin
 * @author     Taher Atashbar <taher.atashbar@gmail.com>
 */

class ELM_Admin_Editor extends ELM_Admin_Controller {

	/**
	 * Plugin admin-face.
	 *
	 * @since   1.0.0
	 * @var     Easy_Listings_Map_Public
	 */
	private $admin;

	public function __construct( Easy_Listings_Map_Admin $admin ) {
		$this->admin = $admin;
		$this->admin->get_loader()->add_action( 'admin_init', $this, 'define_shortcode_button' );
		if ( is_admin() ) {
			// Ajax action for loading content of shortcode button.
			$this->admin->get_loader()->add_action( 'wp_ajax_load_shortcode_content', $this, 'load_shortcode_content' );
		}
	}

	/**
	 * Defining shortcode button in editor by appropriate filters.
	 */
	public function define_shortcode_button() {
		/*
		 * Add a button for shortcodes to the WP editor.
		 */
		if ( current_user_can( 'edit_posts' ) && current_user_can( 'edit_pages' ) ) {
			add_filter( 'mce_buttons', array( $this, 'register_shortcode_button' ) );
			add_filter( 'mce_external_plugins', array( $this, 'add_shortcode_tinymce_plugin' ) );
		}
	}

	/**
	 * Register the shortcode button.
	 *
	 * @param   array   $buttons
	 *
	 * @return  array
	 */
	public function register_shortcode_button( $buttons ) {
		array_push( $buttons, '|', 'elm_shortcode_buttons' );
		return $buttons;
	}

	/**
	 * Add the shortcode button to TinyMCE
	 *
	 * @param   array   $plugins
	 *
	 * @return  array
	 */
	public function add_shortcode_tinymce_plugin( $plugins ) {
		// Use minified libraries if SCRIPT_DEBUG is turned off
		$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
		$plugins['EasyListingMapShortcodes'] = $this->admin->get_js_folder() . '/editor/editor-plugin' . $suffix . '.js';
		return $plugins;
	}

	/**
	 * Loading content of shortcode.
	 *
	 * @since   1.0.0
	 */
	public function load_shortcode_content() {
		// Loading location walker class.
		require_once $this->get_plugin_path() . 'includes/walkers/class-walker-location-checkbox.php';

		$properties = ELM_IOC::make( 'properties' );

		$this->render_view( 'editor.shortcode-content',
			array(
				'property_types'  => epl_get_active_post_types(),	// all of active property post types.
				'property_status' => $properties->get_all_status(), // all of property status.
				'location_walker' => new ELM_Walker_Location_Checkbox(),
				'includes_url'    => includes_url(),
				'css_url'		  => $this->admin->get_css_folder(),
				'js_url'		  => $this->admin->get_js_folder(),
				'suffix'		  => ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min'
			)
		);

		die(); // this is required to terminate immediately and return a proper response
	}

}
