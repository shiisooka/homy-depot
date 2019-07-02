<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://www.asanaplugins.com/
 * @since      1.0.0
 *
 * @package    Easy_Listings_Map
 * @subpackage Easy_Listings_Map/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Easy_Listings_Map
 * @subpackage Easy_Listings_Map/admin
 * @author     Taher Atashbar <taher.atashbar@gmail.com>
 */
class Easy_Listings_Map_Admin {

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
	 * The menu that's responsible for maintaining and registering all of plugin menus.
	 *
	 * @since 1.0.0
	 * @access private
	 * @var ELM_Admin_Menu	$menu
	 */
	private $menu;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 *
	 * @param      string $plugin_name The name of this plugin.
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
	 * Load dependencies required in admin area.
	 *
	 * @since   1.0.0
	 */
	protected function load_dependencies() {
		/**
		 * The controller class of admin area.
		 */
		require_once $this->get_path() . 'class-easy-listings-map-admin-controller.php';
		/**
		 * The class responsible for outputting html elements in pages.
		 */
		require_once $this->get_path() . 'class-easy-listings-map-admin-element.php';
		/**
		 * The class responsible for defining all actions that related to tiny mce editor of admin area.
		 */
		require_once $this->get_path() . 'class-easy-listings-map-admin-editor.php';
		/**
		 * The class responsible for creating all admin menus of the plugin.
		 */
		require_once $this->get_path() . 'class-easy-listings-map-admin-menu.php';
		/**
		 * The class responsible for show
		 */
		require_once $this->get_path() . 'class-easy-listings-map-admin-notices.php';
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function define_hooks() {
		// Editor hooks.
		new ELM_Admin_Editor( $this );
		// Menu hooks.
		$this->menu = new ELM_Admin_Menu( $this );
		// Admin notices.
		new ELM_Admin_Notices( $this->loader );

		// Changing upload directory of the plugin.
		$this->loader->add_filter( 'upload_dir', $this, 'upload_dir' );
		// Rate us on wordpress.org.
		$this->loader->add_filter( 'admin_footer_text', $this, 'rate_us' );
		// Plugin links.
		$this->loader->add_filter( 'plugin_row_meta', $this, 'plugin_row_meta_links', 10, 2 );

		$this->loader->add_action( 'admin_enqueue_scripts', $this, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $this, 'enqueue_scripts' );
	}

	/**
	 * Determines whether the current admin page is an ELM admin page.
	 *
	 * Only works after the `wp_loaded` hook, & most effective
	 * starting on `admin_menu` hook.
	 *
	 * @since 1.0.0
	 * @return boolean
	 */
	public function is_admin_page() {
		if ( ! is_admin() || ! did_action( 'wp_loaded' ) ) {
			return false;
		}

		$return = false;
		$screen = get_current_screen();

		$pages = apply_filters( 'elm_admin_pages', $this->menu->get_menus() );
		if ( in_array( $screen->id, $pages ) ) {
			$return = true;
		}

		// Welcome pages of the plugin.
		if ( 'dashboard_page_elm-getting-started' === $screen->id ) {
			$return = true;
		}

		return (bool) apply_filters( 'elm_is_admin_page', $return );
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 * @param string $hook Page hook
	 */
	public function enqueue_styles( $hook ) {
		if ( ! apply_filters( 'elm_load_admin_scripts', $this->is_admin_page(), $hook ) ) {
			return;
		}

		global $wp_version;

		// Use minified libraries if SCRIPT_DEBUG is turned off
		$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

		wp_enqueue_style( $this->plugin_name, $this->get_css_folder() . 'elm-admin' . $suffix . '.css', array(), $this->version, 'all' );
		wp_enqueue_style( 'elm_admin_notices', $this->get_css_folder() . 'elm-admin-notices' . $suffix . '.css', array(), $this->version, 'all' );
		if ( ! function_exists( 'wp_enqueue_media' ) || version_compare( $wp_version, '3.5', '<' ) ) {
			wp_enqueue_style( 'thickbox' );
		}
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since 1.0.0
	 * @param string $hook Page hook
	 */
	public function enqueue_scripts( $hook ) {
		if ( ! apply_filters( 'elm_load_admin_scripts', $this->is_admin_page(), $hook ) ) {
			return;
		}

		global $wp_version;

		// Use minified libraries if SCRIPT_DEBUG is turned off
		$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

		// Including media libraries based on Wordpress version.
		if ( function_exists( 'wp_enqueue_media' ) && version_compare( $wp_version, '3.5', '>=' ) ) {
			//call for new media manager
			wp_enqueue_media();
		} else {
			wp_enqueue_script( 'media-upload' );
			wp_enqueue_script( 'thickbox' );
		}
		wp_register_script( 'jquery-tiptip', $this->get_js_folder() . 'jquery-tiptip/jquery.tipTip' . $suffix . '.js', array( 'jquery' ), $this->version, true );
		wp_enqueue_script( $this->plugin_name, $this->get_js_folder() . 'elm-admin' . $suffix . '.js', array( 'jquery', 'jquery-tiptip' ), $this->version, true );
		wp_localize_script( $this->plugin_name, 'elm_vars', array(
			'use_this_file'    => __( 'Use This File', 'elm' ),
			'add_new_download' => __( 'Add New Download', 'elm' ),
			'subscribe_nonce'  => wp_create_nonce( 'elm_subscribe_email_send' ),
		) );
	}

	/**
	 * Filter the directory for uploads.
	 *
	 * @since   1.0.0
	 * @param   array   $upload
	 *
	 * @return  array   $upload
	 */
	public function upload_dir( $upload ) {
		global $pagenow;
		// Changing upload folder of easy-listings-map-settings-menu
		if ( ( ( isset( $_REQUEST['referrer_page'] ) && 'elm-settings' == $_REQUEST['referrer_page'] ) ||
				strpos( wp_get_referer(), 'referrer_page=elm-settings' ) !== false )
				&& ( 'async-upload.php' == $pagenow || 'media-upload.php' == $pagenow ) ) {
			// Override the year / month being based on the post publication date, if year/month organization is enabled
			if ( get_option( 'uploads_use_yearmonth_folders' ) ) {
				// Generate the yearly and monthly dirs
				$time = current_time( 'mysql' );
				$y = substr( $time, 0, 4 );
				$m = substr( $time, 5, 2 );
				$upload['subdir'] = "/$y/$m";
			}
			$upload['subdir'] = '/ELM' . $upload['subdir'];
			$upload['path']   = $upload['basedir'] . $upload['subdir'];
			$upload['url']    = $upload['baseurl'] . $upload['subdir'];
		}

		return $upload;
	}

	/**
	 * Add rating links to the admin dashboard
	 *
	 * @since	    1.0.0
	 * @param       string $footer_text The existing footer text
	 * @return      string
	 */
	public function rate_us( $footer_text ) {
		// Checking of Easy Listings Map admin pages.
		if ( ! $this->is_admin_page() ) {
			return $footer_text;
		}

		$rate_text = sprintf( __( 'Thank you for using <a href="%1$s" target="_blank">Easy Listings Map</a>! Please <a href="%2$s" target="_blank">rate us</a> on <a href="%2$s" target="_blank">WordPress.org</a>', 'elm' ),
			'http://www.asanaplugins.com/',
			'https://wordpress.org/support/view/plugin-reviews/easy-listings-map?filter=5#postform'
		);

		return str_replace( '</span>', '', $footer_text ) . ' | ' . $rate_text . '</span>';
	}

	/**
	 * Plugin row meta links
	 * This function adds additional links below the plugin in admin plugins page.
	 *
	 * @since  1.0.0
	 * @param  array  $links 	The array having default links for the plugin.
	 * @param  string $file 	The name of the plugin file.
	 * @return array  $links 	Plugin default links and specific links.
	 */
	public function plugin_row_meta_links( $links, $file ) {
		if ( false !== strpos( $file, 'easy_listings_map.php' ) ) {
			$plugin_links = array(
				'<a href="https://asanaplugins.freshdesk.com/support/solutions/16000037719" target="_blank">' . esc_html__( 'Getting Started', 'elm' ) . '</a>',
			);
			$links = array_merge( $links, $plugin_links );
		}

		return $links;
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
	 * Getting path of admin-facing
	 *
	 * @since   1.0.0
	 * @return string
	 */
	public function get_path() {
		return plugin_dir_path( __FILE__ );
	}

	/**
	 * Getting css folder in admin-facing
	 *
	 * @since   1.0.0
	 * @return  string
	 */
	public function get_css_folder() {
		return $this->get_url() . 'css/';
	}

	/**
	 * Getting js folder in admin-facing
	 *
	 * @since   1.0.0
	 * @return  string
	 */
	public function get_js_folder() {
		return $this->get_url() . 'js/';
	}

	/**
	 * Getting images folder in admin-facing
	 *
	 * @since   1.0.0
	 * @return  string
	 */
	public function get_images_folder() {
		return $this->get_url() . 'images/';
	}

}
