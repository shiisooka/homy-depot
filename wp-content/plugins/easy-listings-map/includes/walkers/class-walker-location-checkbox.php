<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Checkbox Walker for location taxonomy.
 *
 * @since      1.2.0
 * @package    Easy_Listings_Map
 * @subpackage Easy_Listings_Map/includes/walkers
 * @author     Taher Atashbar <taher.atashbar@gmail.com>
 */
class ELM_Walker_Location_Checkbox extends Walker {

	public $tree_type = 'location';
	public $db_fields = array(
		'parent' => 'parent',
		'id' => 'term_id',
		'slug' => 'slug',
	);

	/**
	 * @see Walker::start_lvl()
	 * @since 1.2.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param int $depth Depth of category. Used for tab indentation.
	 * @param array $args Will only append content if style argument value is 'list'.
	 */
	public function start_lvl( &$output, $depth = 0, $args = array() ) {
		$indent = str_repeat( "\t", $depth );
		$output .= "$indent<ul class='children'>\n";
	}

	/**
	 * @see Walker::end_lvl()
	 * @since 1.2.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param int $depth Depth of category. Used for tab indentation.
	 * @param array $args Will only append content if style argument value is 'list'.
	 */
	public function end_lvl( &$output, $depth = 0, $args = array() ) {
		$indent = str_repeat( "\t", $depth );
		$output .= "$indent</ul>\n";
	}

	/**
	 * @see Walker::start_el()
	 * @since 1.2.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param stdClass	$category
	 * @param int $depth Depth of category in reference to parents.
	 * @param integer $current_object_id
	 */
	public function start_el( &$output, $category, $depth = 0, $args = array(), $current_object_id = 0 ) {
		$name = 'listing_location';
		if ( ! empty( $args['name'] ) ) {
			$name = trim( $args['name'] );
		}
		// Using category slug as value.
		$value = 'slug';
		if ( ! empty( $args['value'] ) && 'id' === $args['value'] ) {
			// Use category term_id as value.
			$value = 'term_id';
		}

		$output .= "\n<li id='location-{$category->term_id}'>" .
		'<label class="selectit">' .
		'<input value="' . esc_attr( $category->{$value} ) . '" data-id="' . esc_attr( $category->term_id ) . '" type="checkbox" name="'.$name.'[]" id="in-location-' . $category->term_id . '"' . ' /> ' .
		esc_html( apply_filters( 'the_category', $category->name ) ) . '</label>';
	}

	/**
	 * @see Walker::end_el()
	 * @since 1.2.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param int $depth Depth of category. Not used.
	 * @param array $args Only uses 'list' for whether should append to output.
	 */
	public function end_el( &$output, $cat, $depth = 0, $args = array() ) {
		$output .= "</li>\n";
	}

}
