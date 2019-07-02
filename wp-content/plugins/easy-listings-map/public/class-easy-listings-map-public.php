<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://www.asanaplugins.com/
 * @since      1.0.0
 *
 * @package    Easy_Listings_Map
 * @subpackage Easy_Listings_Map/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Easy_Listings_Map
 * @subpackage Easy_Listings_Map/public
 * @author     Taher Atashbar<taher.atashbar@gmail.com>
 */
class Easy_Listings_Map_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Easy_Listings_Map_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	private $loader;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 *
	 * @param      string $plugin_name The name of the plugin.
	 * @param      string $version The version of this plugin.
	 * @param      Easy_Listings_Map_Loader $loader
	 */
	public function __construct( $plugin_name, $version, Easy_Listings_Map_Loader $loader ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;
		$this->loader      = $loader;
		$this->load_dependencies();
	}

	/**
	 * Load dependencies required in public area.
	 *
	 * @since   1.0.0
	 */
	protected function load_dependencies() {
		/**
		 * The controller class of public area.
		 */
		require_once plugin_dir_path( __FILE__ ) . 'class-easy-listings-map-public-controller.php';
		/**
		 * The controller class responsible for marker functionalities.
		 */
		require_once plugin_dir_path( __FILE__ ) . 'class-elm-public-google-map-marker.php';
		/**
		 * The controller class for rendering Google Maps by listings send to it.
		 */
		require_once plugin_dir_path( __FILE__ ) . 'class-elm-public-google-map-render.php';
		/**
		 * The class responsible for displaying map in single listings page.
		 */
		require_once plugin_dir_path( __FILE__ ) . 'class-easy-listings-map-public-single-map.php';
		/**
		 * The class responsible for creating maps shortcode.
		 */
		require_once plugin_dir_path( __FILE__ ) . 'shortcodes/class-elm-shortcode-google-maps.php';
		/**
		 * The class responsible for ajax functionalities of Google Maps.
		 */
		require_once plugin_dir_path( __FILE__ ) . 'class-elm-public-google-map-ajax.php';
	}

	/**
	 * Defining hooks of plugin public-facing.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function define_hooks() {
		$this->loader->add_action( 'wp_enqueue_scripts', $this, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $this, 'enqueue_scripts' );
		$this->loader->add_action( 'wp_print_scripts', $this, 'dequeue_scripts', 100 );

		// Hook for single listing map.
		new ELM_Public_Single_Map( $this );
		// Hooks for ajax functionality of Google Maps.
		$google_map_ajax = new ELM_Public_Google_Map_Ajax( $this->loader );
		$google_map_ajax->define_hooks();
		// Shortcodes.
		// Registering google maps listings shortcode.
		$this->loader->add_shortcode( 'elm_google_maps', new ELM_Shortcode_Google_Maps(), 'output' );
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Easy_Listings_Map_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Easy_Listings_Map_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		// Use minified libraries if SCRIPT_DEBUG is turned off
		$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/elm-public' . $suffix . '.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Easy_Listings_Map_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Easy_Listings_Map_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		// Use minified libraries if SCRIPT_DEBUG is turned off
		$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

		// Loading public globals js
		wp_enqueue_script( 'elm-public-globals', $this->get_js_folder() . 'elm-public-globals' . $suffix . '.js',
			array(),  $this->version, true );
		wp_localize_script( 'elm-public-globals', 'elmGlobals', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );

		/*wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/elm-public' . $suffix . '.js',
			array( 'jquery' ), $this->version, true );*/

	}

	/**
	 * Dequeue scripts.
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public function dequeue_scripts() {
		wp_dequeue_script( 'google-map-v-3' );
	}

	/**
	 * Getting version.
	 *
	 * @since   1.0.0
	 * @return  string
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * @return Easy_Listings_Map_Loader
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Getting url of admin area.
	 *
	 * @since   1.0.0
	 * @return  string
	 */
	public function get_url() {
		return plugin_dir_url( __FILE__ );
	}

	/**
	 * Getting path of admin area.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_path() {
		return plugin_dir_path( __FILE__ );
	}

	/**
	 * Getting css folder in public-facing
	 *
	 * @since   1.0.0
	 * @return  string
	 */
	public function get_css_folder() {
		return plugin_dir_url( __FILE__ ) . 'css/';
	}

	/**
	 * Getting js folder in public-facing
	 *
	 * @since   1.0.0
	 * @return  string
	 */
	public function get_js_folder() {
		return plugin_dir_url( __FILE__ ). 'js/';
	}

	/**
	 * Getting images folder in public-facing
	 *
	 * @since   1.0.0
	 * @return  string
	 */
	public function get_images_folder() {
		return plugin_dir_url( __FILE__ ). 'images/';
	}

}
