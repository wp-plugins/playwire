<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
* PlaywireVideoRadioCategories class.
*/
class PlaywireVideoRadioCategories extends Playwire {

	public $set;

	public function __construct() {

		remove_action( 'wp_ajax_add-' . $this->videos_taxonomy, '_wp_ajax_add_hierarchical_term' );

		add_filter( 'wp_terms_checklist_args',               array( $this, 'filter_terms_checklist_args' ) );
		add_action( 'save_post',                             array( $this, 'save_single_term'            ) );
		add_action( 'edit_attachment',                       array( $this, 'save_single_term'            ) );
		add_action( 'quick_edit_custom_box',                 array( $this, 'quick_edit_nonce'            ) );
	}


	/**
	* Tell checklist function to use our new Walker
	*
	* @access public
	* @param  array $args
	* @return array
	* @since 1.1.0
	*/
	public function filter_terms_checklist_args( $args ) {

		//Define Walker
		if ( isset( $args['taxonomy']) && $this->videos_taxonomy == $args['taxonomy'] ) {
			$args['walker'] = new PlaywireVideoCategoryWalker;
			$args['checked_ontop'] = false;
		}
		return $args;
	}


	public function get_terms( $terms, $taxonomies, $args ) {
		if ( in_array( $this->videos_taxonomy, ( array ) $taxonomies ) && ! in_array( 'category', $taxonomies  ) && isset( $args['fields'] ) && $args['fields'] == 'all' && $this->switch_terms_filter() === 1 ) {
			// remove filter after 1st run
			remove_filter( current_filter(), __FUNCTION__, 10, 3 );
			// turn the switch OFF
			$this->switch_terms_filter( 0 );
			$no_term       = sprintf( __( 'No %s', 'radio-buttons-for-taxonomies' ), 'video' );
			$uncategorized = (object) array( 'term_id' => '0', 'slug' => '0', 'name' => $no_term, 'parent' => '0' );
			array_push( $terms, $uncategorized );
		}
		return $terms;
	}


	/**
	* save_single_term
	*
	* @param mixed $post_id
	* @access public
	* @return void
	*/
	public function save_single_term( $post_id ) {
		//Autosave check
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}

		//Multisite Check
		if( function_exists( 'ms_is_switched' ) && ms_is_switched() ) {
			return $post_id;
		}

		//Check post type
		if ( isset( $_REQUEST['post_type'] ) && $_REQUEST['post_type'] != $this->videos_taxonomy ) {
			return $post_id;
		}

		//Verify nonce
		if ( isset( $_POST["_radio_nonce-{$this->videos_taxonomy}"] ) && ! wp_verify_nonce( $_REQUEST["_radio_nonce-{$this->videos_taxonomy}"], "radio_nonce-{$this->videos_taxonomy}" ) ) {
			return $post_id;
		}

		if ( isset( $_REQUEST["radio_tax_input"]["{$this->videos_taxonomy}"] ) ){

			$terms = (array) $_REQUEST["radio_tax_input"]["{$this->videos_taxonomy}"];

			// if category and not saving any terms, set to default
			if ( 'category' == $this->videos_taxonomy && empty( $terms ) ) {
				$single_term = intval( get_option( 'default_category' ) );
			}

			//Only save 1 term
			$single_term = intval( array_shift( $terms ) );

			//Get the term
			$tax = get_taxonomy( $this->videos_taxonomy );

			//Set the term
			$a = $single_term;
			if ( current_user_can( $tax->cap->assign_terms ) ) {
				wp_set_object_terms( $post_id, $single_term, $this->videos_taxonomy );
			}
		}

		return $post_id;
	}


	/**
	* Add nonces to quick edit and bulk edit
	*
	* @return HTML
	* @since 1.7.0
	*/
	public function quick_edit_nonce() {
		wp_nonce_field( 'radio_nonce-' . $this->videos_taxonomy, '_radio_nonce-' . $this->videos_taxonomy );
	}


}
