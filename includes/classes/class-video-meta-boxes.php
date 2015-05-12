<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
* PlaywireVideoMetaboxes
*
* @uses Playwire
* @package
*/
class PlaywireVideoMetaboxes extends Playwire {

	/**
	* __construct function.
	*
	* @access private
	* @return void
	*/
	public function __construct() {
		add_action( 'add_meta_boxes',     array( $this, 'action_add_meta_boxes' ) );
		add_action( 'save_post',          array( $this, 'action_save_post'      ) );
		add_action( 'post_edit_form_tag', array( $this, 'add_form_type_support' ) );
	}


	public function add_form_type_support( $post_id ) {
		if ( playwire()->videos_post_type == get_post_type( $post_id ) ) {
			echo ' enctype="multipart/form-data"';
		}
	}

	/**
	* action_save_post function.
	*
	* @access public
	* @param mixed $post_id
	* @return void
	*/
	public function action_save_post( $post_id = 0 ) {

		// Bail if not the correct post type
		if ( playwire()->videos_post_type !== get_post_type( $post_id ) ) {
			return;
		}

		// Bail if current user can't edit this post
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		// Bail if an autosave or a revision
		if ( wp_is_post_autosave( $post_id ) || wp_is_post_revision( $post_id ) ) {
			return;
		}

		// Bail if nonce check fails
		if ( empty( $_POST[ $this->update_video_post_data_nonce ] ) || ! wp_verify_nonce( $_POST[ $this->update_video_post_data_nonce ], 'playwire_save' ) ) {
			return;
		}

		/** Proceed with saving the post meta *********************************/

		// Video URL

		// Intend to share meta box option
		if ( ! empty( $_POST[$this->video_post_type_intend_to_share] ) ) {
			update_post_meta( $post_id, $this->video_post_type_intend_to_share, sanitize_text_field( $_POST[ $this->video_post_type_intend_to_share ] ) );
		} else {
			delete_post_meta( $post_id, $this->video_post_type_intend_to_share );
		}

	}


	/**
	* action_add_meta_boxes function.
	*
	* @access public
	* @return void
	*/
	public function action_add_meta_boxes() {
		global $post;
		$video_meta = get_post_meta( $post->ID, $this->video_meta_name );

		if ( empty( $video_meta ) ) {
			add_meta_box( 'video_url_meta_box',       __( 'Upload Video',  'playwire' ),     array( $this, 'video_url_meta_box'),        $this->videos_post_type, 'normal', 'high' );
		}
		add_meta_box( 'video_share_meta_box',     __( 'Intend to share?', 'playwire' ),  array( $this, 'video_share_meta_box' ),     $this->videos_post_type, 'side',   'core' );
		add_meta_box( 'video_preview_meta_box',   __( 'Video Preview', 'playwire' ),     array( $this, 'video_preview_meta_box' ),   $this->videos_post_type, 'normal', 'high' );
		add_meta_box( 'video_thumbnail_meta_box', __( 'Video Thumbnail', 'playwire' ),   array( $this, 'video_thumbnail_meta_box' ), $this->videos_post_type, 'side',   'core' );
	}


	/**
	* video_url_meta_box
	*
	* @param mixed $post
	* @access public
	* @return void
	*/
	public function video_url_meta_box( $post = false ) {
		wp_nonce_field( 'playwire_save', $this->update_video_post_data_nonce );
		$video_url = get_post_meta( $post->ID, $this->video_post_type_video_url, true );
		$video_url = ( isset( $video_url ) ? $video_url : '' );
		include( PLAYWIRE_PATH . 'templates/template-video-url-meta-box.php' );
	}


	/**
	* video_share_meta_box
	*
	* @param mixed $post
	* @access public
	* @return void
	*/
	public function video_share_meta_box( $post = false ) {
		wp_nonce_field( 'playwire_save', $this->update_video_post_data_nonce );
		$intend_to_share = get_post_meta( $post->ID, $this->video_post_type_intend_to_share, true );
		$intend_to_share = ( isset( $intend_to_share ) ? $intend_to_share : true );
		include( PLAYWIRE_PATH . 'templates/template-video-share-meta-box.php' );
	}


	/**
	* video_preview_meta_box
	*
	* @param mixed $post
	* @access public
	* @return void
	*/
	public function video_preview_meta_box( $post = false ) {

		// Gets the current pub id (string) for the Playlist
		$current_publisher_id = PlaywirePlayerPostHandler::get_current_publisher_id( $post->ID);

		$video_id = get_post_meta( $post->ID, $this->video_meta_name, true );
		$video_id = ( isset( $video_id['id'] ) ? $video_id['id'] : '' );
		include( PLAYWIRE_PATH . 'templates/template-video-preview-meta-box.php' );
		//This is the template for the modal dialog
		include( PLAYWIRE_PATH . 'templates/template-video-dialog.php' );
	}


	/**
	* video_thumbnail_meta_box
	*
	* @param mixed $post
	* @access public
	* @return void
	*/
	public function video_thumbnail_meta_box( $post = false ) {
		$video           = get_post_meta( $post->ID, $this->video_meta_name, true );
		$video_thumbnail = ( isset( $video['thumbnail']['320x240'] ) ? $video['thumbnail']['320x240'] : '//placehold.it/320x240/' . strtoupper(  dechex(  rand(  0,  10000000  )  )  ) . '/ffffff&amp;text=No&nbsp;Thumbnail' );
		$video           = ( isset( $video['id'] ) ? $video['id'] : '' );
		include( PLAYWIRE_PATH . 'templates/template-video-thumbnail-meta-box.php' );
	}

}
