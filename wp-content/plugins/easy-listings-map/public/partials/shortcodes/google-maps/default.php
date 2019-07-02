<?php
/**
 * Deprecated use google-map-render/default.php instead of it.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @var string $content
 * @var string $id
 * @var int $height
 */

if ( strlen( $content ) ) {
	echo '<h2 class="elm listing-map-title">' . esc_html( $content ) . '</h2>';
}
?>
<div class="shortcode map_container" style="height: <?php echo absint( $height ) ? absint( $height ) : '500' ?>px;">
	<div class="elm google-maps" id="<?php echo esc_attr( $id ) ?>"
	style="height: <?php echo absint( $height ) ? absint( $height ) : '500' ?>px; padding: 0px; margin: 0px;"></div>
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
