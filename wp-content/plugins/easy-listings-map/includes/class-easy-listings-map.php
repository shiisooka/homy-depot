<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://www.asanaplugins.com/
 * @since      1.0.0
 *
 * @package    Easy_Listings_Map
 * @subpackage Easy_Listings_Map/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Easy_Listings_Map
 * @subpackage Easy_Listings_Map/includes
 * @author     Taher Atashbar <taher.atashbar@gmail.com>
 */
class Easy_Listings_Map {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Easy_Listings_Map_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->plugin_name = 'easy-listings-map';
		$this->version     = '1.2.4';

		$this->load_vendors();
		$this->load_dependencies();
		$this->define_globals();
		// Setting locale of plugin.
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Easy_Listings_Map_Loader. Orchestrates the hooks of the plugin.
	 * - Easy_Listings_Map_i18n. Defines internationalization functionality.
	 * - Easy_Listings_Map_Admin. Defines all hooks for the admin area.
	 * - Easy_Listings_Map_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-easy-listings-map-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-easy-listings-map-i18N.php';

		/**
		 * The class responsible for inversion of control (IOC) of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-easy-listings-map-ioc.php';

		/**
		 * Base controller class of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-easy-listings-map-controller.php';

		/**
		 * The class responsible for plugin settigns.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-easy-listings-map-settings.php';

		/**
		 * The class responsible for accessing assets of public-facing and admin-facing of plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-easy-listings-map-asset-manager.php';

		/**
		 * The class responsible for functions related to locations.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-easy-listings-map-location.php';

		/**
		 * The class responsible for functions related to properties.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-easy-listings-map-properties.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-easy-listings-map-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-easy-listings-map-public.php';

		$this->loader = new Easy_Listings_Map_Loader();

	}

	/**
	 * Loading third party libraries.
	 *
	 * @since 1.1.1
	 * @return void
	 */
	private function load_vendors() {
		/**
		 * The class responsible for geometry functions.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'vendor/spherical-geometry/spherical-geometry.class.php';
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Easy_Listings_Map_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Easy_Listings_Map_i18n();
		$plugin_i18n->set_domain( $this->get_plugin_name() );
		ELM_IOC::bind( 'plugin_i18n', $plugin_i18n );

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		/**
		 * All of admin hooks are defined in define_hooks() of Easy_Listings_Map_Admin.
		 */
		$plugin_admin = ELM_IOC::make( 'plugin_admin' );
		$plugin_admin->define_hooks();

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		/**
		 * All of public hooks are defined in define_hooks() of Easy_Listings_Map_Admin.
		 */
		$plugin_public = ELM_IOC::make( 'plugin_public' );
		$plugin_public->define_hooks();

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Easy_Listings_Map_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * Defining objects that should be accessed globally.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	private function define_globals() {
		ELM_IOC::bind( 'settings', new ELM_Settings() );
		ELM_IOC::bind( 'plugin_admin', new Easy_Listings_Map_Admin( $this->plugin_name, $this->version, $this->loader ) );
		ELM_IOC::bind( 'plugin_public', new Easy_Listings_Map_Public( $this->plugin_name, $this->version, $this->loader ) );
		ELM_IOC::bind( 'location', new ELM_Location() );
		ELM_IOC::bind( 'properties', new ELM_Properties() );
		ELM_IOC::bind( 'asset_manager', new ELM_Asset_Manager( ELM_IOC::make( 'plugin_admin' ), ELM_IOC::make( 'plugin_public' ) ) );
	}

}
