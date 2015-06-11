<?php
/*
* @see Walker
* @see wp_category_checklist()
* @see wp_terms_checklist()
* @since 2.5.1
*/
class PlaywireVideoCategoryWalker extends Walker {

	var $tree_type = 'category';
	var $db_fields = array ( 'parent' => 'parent', 'id' => 'term_id' );


	/**
	 * Starts the list before the elements are added.
	 *
	 * @see Walker:start_lvl()
	 *
	 * @since 2.5.1
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param int    $depth  Depth of category. Used for tab indentation.
	 * @param array  $args   An array of arguments. @see wp_terms_checklist()
	 */
	function start_lvl( &$output, $depth = 0, $args = array() ) {
		$indent = str_repeat("\t", $depth);
		$output .= "$indent<ul class='children'>\n";
	}


	/**
	 * Ends the list of after the elements are added.
	 *
	 * @see Walker::end_lvl()
	 *
	 * @since 2.5.1
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param int    $depth  Depth of category. Used for tab indentation.
	 * @param array  $args   An array of arguments. @see wp_terms_checklist()
	 */
	function end_lvl( &$output, $depth = 0, $args = array() ) {
		$indent = str_repeat("\t", $depth);
		$output .= "$indent</ul>\n";
	}


	/**
	 * Start the element output.
	 *
	 * @see Walker::start_el()
	 *
	 * @since 2.5.1
	 *
	 * @param string $output   Passed by reference. Used to append additional content.
	 * @param object $category The current term object.
	 * @param int    $depth    Depth of the term in reference to parents. Default 0.
	 * @param array  $args     An array of arguments. @see wp_terms_checklist()
	 * @param int    $id       ID of the current term.
	 */
	function start_el( &$output, $category, $depth = 0, $args = array(), $id = 0 ) {
		extract( $args );
		if ( empty( $taxonomy ) ) {
			$taxonomy = 'category';
		}

		$name         = 'radio_tax_input['.$taxonomy.']';
		$current_term = ! empty( $selected_cats ) && ! is_wp_error( $selected_cats ) ? array_pop( $selected_cats ) : false;
		$current_id   = ( $current_term ) ? $current_term : 0;
		$value        = $category->term_id;
		$class        = '';

		$output .= sprintf( "\n" . '<li id="%1$s-%2$s" %3$s><label class="selectit"><input id="%4$s" type="radio" name="%5$s" value="%6$s" %7$s %8$s/> %9$s</label>' ,
			$taxonomy,
			$value,
			$class,
			"in-{$taxonomy}-{$category->term_id}",
			$name . '[]',
			esc_attr( trim( $value ) ),
			checked( $current_id, $category->term_id, false ),
			disabled( empty( $args['disabled'] ), false, false ),
			esc_html( apply_filters( 'the_category', $category->name ) )
		);

	}


	/**
	 * Ends the element output, if needed.
	 *
	 * @see Walker::end_el()
	 *
	 * @since 2.5.1
	 *
	 * @param string $output   Passed by reference. Used to append additional content.
	 * @param object $category The current term object.
	 * @param int    $depth    Depth of the term in reference to parents. Default 0.
	 * @param array  $args     An array of arguments. @see wp_terms_checklist()
	 */
	function end_el( &$output, $term, $depth = 0, $args = array() ) {
		$output .= "</li>\n";
	}
}

