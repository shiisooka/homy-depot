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

if ( strlen( $view_args['content'] ) ) {
	echo '<h2 class="elm listing-map-title">' . esc_html( $view_args['content'] ) . '</h2>';
}
?>
<div class="shortcode map_container" style="height: <?php echo absint( $view_args['height'] ) ? absint( $view_args['height'] ) : '500' ?>px;">
	<div class="elm google-maps" id="<?php echo esc_attr( $view_args['map_id'] ) ?>"
	style="height: <?php echo absint( $view_args['height'] ) ? absint( $view_args['height'] ) : '500' ?>px; padding: 0px; margin: 0px;"></div>
	<div id="gmap-loading">
		<?php _e( 'Loading Map', 'elm' ); ?>
  		<div class="spinner map_loader" id="listing_loader_maps">
     		<div class="rect1"></div>
     		<div class="rect2"></div>
     		<div class="rect3"></div>
     		<div class="rect4"></div>
     		<div class="rect5"></div>
 		</div>
	</div>
</div>
