<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @var  array $view_args[
 *       @type ELM_Public_Google_Map_Render controller
 *       @type string 						content
 *       @type string 						map_id
 *       @type int    						height
 *       @type array  						map_types
 *       @type int    						zoom
 *       @type string 						js_url
 *       @type string 						css_url
 *       @type string 						images_url
 *       @type int    						cluster_size
 *       @type string 						default_latitude
 *       @type string 						default_longitude
 *       @type int    						auto_zoom
 * ]
 */

// Registering scripts and styles for Google Maps.
$view_args['controller']->register_scripts();
