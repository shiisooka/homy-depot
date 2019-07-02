<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://www.asanaplugins.com/
 * @since             1.0.0
 * @package           Easy_Listings_Map
 *
 * @wordpress-plugin
 * Plugin Name:       Easy Listings Map
 * Plugin URI:        http://www.asanaplugins.com/
 * Description:       Easy to use and advanced map extension for Easy Property Listings plugin
 * Version:           1.2.4
 * Author:            Asana Plugins
 * Author URI:        http://www.asanaplugins.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       elm
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-easy-listings-map-activator.php
 */
function activate_easy_listings_map() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-easy-listings-map-activator.php';
	Easy_Listings_Map_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-easy-listings-map-deactivator.php
 */
function deactivate_easy_listings_map() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-easy-listings-map-deactivator.php';
	Easy_Listings_Map_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_easy_listings_map' );
register_deactivation_hook( __FILE__, 'deactivate_easy_listings_map' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-easy-listings-map.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_easy_listings_map() {
	if ( ! class_exists( 'Easy_Property_Listings' ) ) {
		if ( ! class_exists( 'EPL_Extension_Activation' ) ) {
			require_once plugin_dir_path( __FILE__ ) . 'includes/class-easy-property-listings-extension-activation.php';
		}
		$activation = new EPL_Extension_Activation( plugin_basename( __FILE__ ) );
		$activation->run();
	} else {
		$plugin = new Easy_Listings_Map();
		$plugin->run();
	}
}
add_action( 'plugins_loaded', 'run_easy_listings_map' );
