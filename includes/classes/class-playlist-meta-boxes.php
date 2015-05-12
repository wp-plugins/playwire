<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
* PlaylistMetaboxes class.
*/
class PlaywirePlaylistMetaboxes extends Playwire {

	/**
	* __construct function.
	*
	* @access private
	* @return void
	*/
	public function __construct() {
		add_action( 'add_meta_boxes',                  array( $this, 'action_add_meta_boxes'            ) );
		add_action( 'save_post',                       array( $this, 'action_save_post'                 ) );
		add_action( 'wp_ajax_update_playlist_preview', array( $this, 'update_playlist_preview_callback' ) );
		add_action( 'wp_ajax_ajax_preview', array( $this, 'ajax_preview' ) );
	}


	/**
	* Ajax callback for the preview metabox
	*
	* @access private
	* @return void
	*/
	public function ajax_preview() {
		if ( empty( $_POST[ $this->playwire_metabox_nonce ] ) || ! wp_verify_nonce( $_POST[ $this->playwire_metabox_nonce ], 'playwire_save' ) ) {
			return;
		} else {
			global $wpdb;
			PlaywirePlayerPostHandler::setup_playlist_template( null, false );
			die();
		}
	}


	/**
	* Ajax callback for the preview metabox
	*
	* @access private
	* @return void
	*/
	public function update_playlist_preview_callback() {
		$data = self::playlist_settings_meta_box( get_the_ID() );
		die();
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
		if ( playwire()->playlists_post_type !== get_post_type( $post_id ) ) {
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
		if ( empty( $_POST[ $this->playwire_metabox_nonce ] ) || ! wp_verify_nonce( $_POST[ $this->playwire_metabox_nonce ], 'playwire_save' ) ) {
			return;
		}

		/** Proceed with saving the post meta *********************************/

		// Playlist
		if ( !empty( $_POST[ $this->video_playlist ] ) ) {
			update_post_meta( $post_id, $this->video_playlist, sanitize_text_field( $_POST[ $this->video_playlist ] ) );
		} else {
			delete_post_meta( $post_id, $this->video_playlist );
		}

		// Layout
		if ( !empty( $_POST[ $this->video_layout ] ) ) {
			update_post_meta( $post_id, $this->video_layout, sanitize_text_field( $_POST[ $this->video_layout ] ) );
		} else {
			delete_post_meta( $post_id, $this->video_layout );
		}

		// Single?
		if ( !empty( $_POST[ $this->single_video ] ) ) {
			update_post_meta( $post_id, $this->single_video, sanitize_text_field( $_POST[ $this->single_video ] ) );
		} else {
			delete_post_meta( $post_id, $this->single_video );
		}

		// Aspect ratio
		if ( isset( $_POST[ playwire()->playwire_aspect_ratio['option_name'] ] ) ) {
			update_post_meta( $post_id, playwire()->playwire_aspect_ratio['option_name'], sanitize_text_field( $_POST[ playwire()->playwire_aspect_ratio['option_name'] ] ) );
		} else {
			delete_post_meta( $post_id, playwire()->playwire_aspect_ratio['option_name'] );
		}

		// Aspect ratio 2
		if ( !empty( $_POST[ playwire()->playwire_aspect_ratio['select_option_name'] ] ) ) {
			update_post_meta( $post_id, playwire()->playwire_aspect_ratio['select_option_name'], sanitize_text_field( $_POST[ playwire()->playwire_aspect_ratio['select_option_name'] ] ) );
		} else {
			delete_post_meta( $post_id, playwire()->playwire_aspect_ratio['select_option_name'] );
		}

		// Custom height
		if ( !empty( $_POST[ playwire()->playwire_aspect_ratio['height_option_name'] ] ) ) {
			update_post_meta( $post_id, playwire()->playwire_aspect_ratio['height_option_name'], sanitize_text_field( $_POST[ playwire()->playwire_aspect_ratio['height_option_name'] ] ) );
		} else {
			delete_post_meta( $post_id, playwire()->playwire_aspect_ratio['height_option_name'] );
		}

		// Custom width
		if ( !empty( $_POST[ playwire()->playwire_aspect_ratio['width_option_name'] ] ) ) {
			update_post_meta( $post_id, playwire()->playwire_aspect_ratio['width_option_name'], sanitize_text_field( $_POST[ playwire()->playwire_aspect_ratio['width_option_name'] ] ) );
		} else {
			delete_post_meta( $post_id, playwire()->playwire_aspect_ratio['width_option_name'] );
		}

		// Aspect ratio 3
		if ( ! empty( $_POST[ playwire()->playwire_aspect_ratio['maintain_option_name'] ] ) ) {
			update_post_meta( $post_id, playwire()->playwire_aspect_ratio['maintain_option_name'], sanitize_text_field( $_POST[ playwire()->playwire_aspect_ratio['maintain_option_name'] ] ) );
		} else {
			delete_post_meta( $post_id, playwire()->playwire_aspect_ratio['maintain_option_name'] );
		}

		if ( ! empty( $_POST[ playwire()->gallery_pagination_options['option_name'] ] ) ) {
			update_post_meta( $post_id, playwire()->gallery_pagination_options['option_name'], sanitize_text_field( $_POST[ playwire()->gallery_pagination_options['option_name'] ] ) );
		} else {
			delete_post_meta( $post_id, playwire()->gallery_pagination_options['option_name'] );
		}
	}


	/**
	* action_add_meta_boxes function.
	*
	* @access public
	* @return void
	*/
	public function action_add_meta_boxes() {
		add_meta_box( 'playlist_settings_meta_box', __( 'Video Gallery Options',  'playwire' ), array( $this, 'playlist_settings_meta_box' ), $this->playlists_post_type, 'normal', 'core'    );
		add_meta_box( 'playlist_preview_meta_box',  __( 'Video Gallery Preview',  'playwire' ), array( $this, 'playlist_preview_meta_box'  ), $this->playlists_post_type, 'normal', 'default' );
	}


	/**
	* playlist_settings_meta_box function.
	*
	* @access public
	* @param mixed $post
	* @TODO: Refactor this it's getting a bit hard to understand.
	* @return void
	*/
	public function playlist_settings_meta_box( $post = false ) {

		wp_nonce_field( 'playwire_save', $this->playwire_metabox_nonce );

		// Return the option from wp_options for all playlists
		$playlists                    = get_option( $this->playlists_option_name );

		// Return the option from wp_options for all videos
		$videos                       = get_option( $this->videos_option_name );

		// Returns the current single video
		$current_single_video         = PlaywirePlayerPostHandler::get_current_single_video( $post->ID );

		// Return the playlist id for the playlist id ( $post_id ) for the currently selected playlist.
		$current_playlist             = PlaywirePlayerPostHandler::get_current_playlist( $post->ID );

		// Return the video layout ( string ) for the playlist id ( $post_id ) of the currently selected layout.
		$current_video_layout         = PlaywirePlayerPostHandler::get_current_video_layout( $post->ID );

		// Gets the current playlist ratio option ( string ) for the Playlist
		$current_ratio                = PlaywirePlayerPostHandler::get_current_ratio( $post->ID );

		// Gets the current playlist ratio option ( string ) for the Playlist
		$current_ratio_height         = PlaywirePlayerPostHandler::get_current_ratio_height( $post->ID );

		// Gets the current playlist ratio option ( string ) for the Playlist
		$current_ratio_width          = PlaywirePlayerPostHandler::get_current_ratio_width( $post->ID );

		// Gets the current select box ratio option ( string ) for the Playlist
		$current_ratio_select         = PlaywirePlayerPostHandler::get_current_ratio_select( $post->ID );

		// Gets the option about mantaining the aspect ratio (string) for the Playlist
		$maintain_aspect_ratio        = PlaywirePlayerPostHandler::get_current_maintain_ratio( $post->ID );

		$current_gallery_pagination   = PlaywirePlayerPostHandler::get_current_gallery_pagination_type( $post->ID );

		// Gets the current pub id (string) for the Playlist
		$current_playlist_publisher_id = PlaywirePlayerPostHandler::get_current_playlist_publisher_id( $post->ID);

		// Override for templates without saving/updating the post meta
		$template             = ( isset( $_POST['template'] )             ? $_POST['template']             : 'native'              );
		$current_playlist     = ( isset( $_POST['current_playlist'] )     ? $_POST['current_playlist']     : $current_playlist     );
		$current_single_video = ( isset( $_POST['current_single_video'] ) ? $_POST['current_single_video'] : $current_single_video );
		$current_ratio        = ( isset( $_POST['current_ratio'] )        ? $_POST['current_ratio']        : $current_ratio        );
		$current_ratio_select = ( isset( $_POST['current_ratio_select'] ) ? $_POST['current_ratio_select'] : $current_ratio_select );


		include( PLAYWIRE_PATH . 'templates/template-playlist-settings-meta-box.php' );

	}


	/**
	* playlist_preview_meta_box function.
	*
	* @access public
	* @param WP_Post $post
	* @return void
	*/
	public function playlist_preview_meta_box( $post = false ) {
		PlaywirePlayerPostHandler::setup_playlist_template( $post->ID, false );
	}

}
