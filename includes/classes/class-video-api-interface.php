<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * PlaywirePostTypeVideos class.
 */
class PlaywireVideoApiInterface extends Playwire {

	/**
	* __construct
	*
	* @access public
	* @return void
	*/
	public function __construct() {
		add_action( 'save_post',                            array( __CLASS__, 'post_video_to_playwire_api' ) );
		add_action( 'save_post',                            array( __CLASS__, 'compare_json'               ) );
		add_action( 'before_delete_post',                   array( __CLASS__, 'delete_video_post'          ) );
		add_action( 'wp_ajax_ajax_delete_post',             array( __CLASS__, 'ajax_delete_post'           ) );
		add_action( 'wp_ajax_ajax_update_post',             array( __CLASS__, 'ajax_update_post'           ) );
		add_action( 'wp_ajax_ajax_delete_post_meta',        array( __CLASS__, 'ajax_delete_post_meta'      ) );
	}

	/**
	* compare_json
	*
	* @param mixed $post_id
	* @static
	* @access public
	* @return void
	*/
	public static function compare_json( $post_id ) {
		$playwire = playwire();
		if ( ! is_admin() && playwire()->videos_post_type !== get_post_type( $post_id ) ) {
			return;
		}
		//GET the old json ( post_meta )
		$post_meta = get_post_meta( $post_id, $playwire->video_meta_name, true );

		//If no post_meta exists then ask:
		if ( ! $post_meta ) {
			return;//Ignore and continue because we can't associate the post with a playwire video
		} elseif ( $post_meta && is_array( $post_meta ) ) {
			//Request json from Playwire API
			$playwire->post_videos_arr['method']      = 'GET';
			$playwire->post_videos_arr['video']['id'] = ( isset( $post_meta['id'] ) ? $post_meta['id'] : 0 );
			$playwire->post_videos_arr['token']       = PlaywireAPIHandler::check_user_token(  $playwire->options  );

			$post_request = PlaywireAPIHandler::request( $playwire->api_endpoint . "/videos/{$playwire->post_videos_arr['video']['id']}", array(), $playwire->post_videos_arr, $playwire->post_videos_arr['method'] );
			if( is_object( $post_request ) ) {
				$post_request = json_decode( json_encode( $post_request ), true );
			}
		} else {
			$post_request = null;
		}

		//Compare the two arrays for instance local and remote and determine what's different
		if ( $post_request['state'] === 'removed' ) {
			return 'removed';
		} elseif ( $post_meta != $post_request ) {
			//If json is not the same
			return 'different';
		} elseif( $post_meta === $post_request ) {
			return 'same';
		} else {
			return;
		}

	}

	/**
	* ajax_update_post
	*
	* @static
	* @access public
	* @return void
	*/
	public static function ajax_update_post() {

		$playwire = playwire();
		//Verify nonce
		$nonce_check = check_ajax_referer( 'playwire_update_post_nonce', 'nonce', false );
		if ( $nonce_check === false || ! current_user_can( 'edit_posts' ) ) {
			die();
		} else {
			$post_meta                                = get_post_meta( $_REQUEST['id'], $playwire->video_meta_name, true );
			$playwire->post_videos_arr['method']      = 'GET';
			$playwire->post_videos_arr['video']['id'] = ( isset( $post_meta['id'] ) ? $post_meta['id'] : 0 );
			$playwire->post_videos_arr['token']       = PlaywireAPIHandler::check_user_token(  $playwire->options  );

			$post_request = PlaywireAPIHandler::request( $playwire->api_endpoint . "/videos/{$playwire->post_videos_arr['video']['id']}", array(), $playwire->post_videos_arr, $playwire->post_videos_arr['method'] );

			$args =
				array(
					'ID'           => intval( $_REQUEST['id'] ),
					'post_title'   => sanitize_text_field( $post_request->name ),
					'post_excerpt' => sanitize_text_field( $post_request->description ),
				);
			$post = wp_update_post( $args );

			if ( is_numeric( $post ) ) {

				//Set the featured image
				if ( isset( $post_request->thumbnail ) ) {
					PlaywireAPIHandler::handle_api_images( $post_request->thumbnail->{'320x240'}, $post );
				}
				//Set the term for the main category
				$playwire_term_id = get_term_by( 'name', $post_request->category->name, $playwire->videos_taxonomy );
				wp_set_post_terms( $post, ( int ) $playwire_term_id->term_id, $playwire->videos_taxonomy );
				//Set the new object meta for the video
				if ( is_object( $post_request ) && isset( $post_request->state ) && $post_request->state === 'approved' ) {
					update_post_meta( $post, $playwire->video_meta_name, json_decode( json_encode( $post_request ), true ) );
					if ( isset( $post_request->id ) && ! in_array( $post_request->id, $playwire->published_videos ) ) {
						array_push( $playwire->published_videos, $post_request->id );
						update_option( $playwire->published_videos_option_name, $playwire->published_videos );
					}
				}
				echo 'true';
			}

		}
		die();

	}

	/**
	* ajax_delete_post_meta
	*
	* @static
	* @access public
	* @return void
	*/
	public static function ajax_delete_post_meta()  {
		$nonce_check = check_ajax_referer( 'playwire_delete_post_nonce', 'nonce', false );
		if ( $nonce_check === false || ! current_user_can( 'edit_posts' ) ) {
			die();
		} else {
			$playwire = playwire();
			delete_post_meta( absint( $_REQUEST['id'] ), $playwire->video_meta_name );
			echo 'true';
		}
		die();
	}

	/**
	* ajax_delete_post
	*
	* @static
	* @access public
	* @return void
	*/
	public static function ajax_delete_post()  {
		$nonce_check = check_ajax_referer( 'playwire_delete_post_nonce', 'nonce', false );
		if ( $nonce_check === false || ! current_user_can( 'edit_posts' ) ) {
			die();
		} else {
			wp_delete_post( absint( $_REQUEST['id'] ) );
			echo 'true';
		}
		die();
	}

	/**
	* delete_video_post
	*
	* @param mixed $post_id
	* @static
	* @access public
	* @return void
	*/
	public static function delete_video_post( $post_id ) {
		$playwire = playwire();
		if ( playwire()->videos_post_type !== get_post_type( $post_id ) || ! current_user_can( 'edit_posts' ) )  {
			return;
		}

		$post_meta                                = get_post_meta( $post_id, $playwire->video_meta_name, true );
		$playwire->post_videos_arr['method']      = 'DELETE';
		$playwire->post_videos_arr['video']['id'] = ( isset( $post_meta['id'] ) ? $post_meta['id'] : 0 );
		$playwire->post_videos_arr['token']       = PlaywireAPIHandler::check_user_token(  $playwire->options  );

		$post_request = PlaywireAPIHandler::request( $playwire->api_endpoint . "/videos/{$playwire->post_videos_arr['video']['id']}", array(), $playwire->post_videos_arr, $playwire->post_videos_arr['method'] );
		if ( in_array( @$post_meta['id'], $playwire->published_videos ) ) {
			if ( ( $key = array_search( $post_meta['id'], $playwire->published_videos ) ) !== false ) {
				unset( $playwire->published_videos[$key] );
			}
			update_option( $playwire->videos_option_name, $playwire->published_videos );
		}

	}


	/**
	* post_video_to_playwire_api
	*
	* @param mixed $post_id
	* @static
	* @access public
	* @return void
	*/
	public static function post_video_to_playwire_api( $post_id ) {

		$playwire = playwire();

		//print_r(get_post_meta($post_id, $playwire->video_meta_name, true));


		// Bail if not the correct post type
		if ( playwire()->videos_post_type !== get_post_type( $post_id ) ) {
			return;
		}

		if ( @$_REQUEST['action'] === 'trash' || @$_REQUEST['action'] === 'untrash' ) {
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

		if(get_post_status($post_id) == 'auto-draft'){
			//This is the beginning of a new post. WP automatically saves an Auto Draft when a new post is created. Thus firing off the saved_post action.
			return;
		}

		//Get post meta for the videos json array
		$post_meta = get_post_meta( $post_id, $playwire->video_meta_name, true );
		//If the meta doesn't exist then we know that we are probably creating a new video
		if ( $post_meta ) {
		//If the meta does exist then we want to update the respective video by it's id
			$playwire->post_videos_arr['video_method'] = 'PUT';
			$playwire->post_videos_arr['video']['id']  = $post_meta['id'];
		} else {
			//If the post meta doesn't exist then we are creating a new video. No need to continue yet. Wordpress fires off
			//saved_post action when creating new post because it creates a "Auto Draft" post in the DB. Thus saving.
			$playwire->post_videos_arr['video_method'] = 'POST';
			unset( $playwire->post_videos_arr['video']['id'] );

		}

		$category_id = term_description( ( int ) @$_POST['radio_tax_input'][$playwire->videos_taxonomy][0], $playwire->videos_taxonomy );
		//This will remove any <p> tags and whitespace that would definitely interfere with posting the right id
		$category_id = wp_strip_all_tags( $category_id, true );
		if ( ! is_numeric( $category_id ) || $category_id <= 0 ) {
			$category_id = 1;//We must have a category to submit to the API so if one isn't assigned then we must assign one by default
		}


		//Build the video array that will be posted to the Playwire API
		$playwire->post_videos_arr['video']['name']            = sanitize_text_field( @$_POST['post_title'] );
		$playwire->post_videos_arr['video']['description']     = sanitize_text_field( wp_strip_all_tags( preg_replace( '/&nbsp;/', '', @$_POST['post_excerpt'] ), true ) );
		if ( isset( $_FILES[$playwire->video_post_type_video_url]['name'] ) ) {
			$upload = wp_upload_bits( $_FILES[$playwire->video_post_type_video_url]['name'], null, file_get_contents( $_FILES[$playwire->video_post_type_video_url]['tmp_name'] ) );
			$upload = ( isset( $upload['url'] ) ? $upload['url'] : '/' );
			$playwire->post_videos_arr['video']['source_url'] = $upload;
		} else {
			unset( $playwire->post_videos_arr['video']['source_url'] );
		}

		$playwire->post_videos_arr['video']['category_id']     = absint( $category_id );
		$playwire->post_videos_arr['video']['intend_to_share'] = ( @$_POST[$playwire->video_post_type_intend_to_share] == 1 ? true : false );
		$playwire->post_videos_arr['token']                    = PlaywireAPIHandler::check_user_token( $playwire->options );

		if ( has_post_thumbnail( $post_id ) ) {
			$thumb_size_arr = array( '320x240' => 'playwire-large', '160x120' => 'playwire-medium', '80x60' => 'playwire-small', '96x96' => 'playwire-small-sq' );
			$thumb_id       = get_post_thumbnail_id( $post_id );
			foreach( $thumb_size_arr as $key => $value ) {
				$thumb_url = wp_get_attachment_image_src( $thumb_id, $value, true );
				$thumb_url = $thumb_url[0];
				$playwire->post_videos_arr['video']['thumbnail'][$key] = $thumb_url;
			}
		}
		$headers = PlaywireAPIHandler::add_token_to_headers( false );
		if ( isset( $playwire->post_videos_arr['video']['id'] ) ) {
			$post_request = PlaywireAPIHandler::request( $playwire->api_endpoint . "/videos/{$playwire->post_videos_arr['video']['id']}", $headers, $playwire->post_videos_arr, $playwire->post_videos_arr['video_method'] );

		} else {
			$post_request = PlaywireAPIHandler::request( $playwire->api_endpoint . '/videos', $headers, $playwire->post_videos_arr, $playwire->post_videos_arr['video_method'] );

		}

		if ( is_object( $post_request ) && isset( $post_request->state ) && $post_request->state === 'approved' ) {
			update_post_meta( $post_id, $playwire->video_meta_name, json_decode( json_encode( $post_request ), true ) );
			if ( isset( $post_request->id ) && ! in_array( $post_request->id, $playwire->published_videos ) ) {
				array_push( $playwire->published_videos, $post_request->id );
				update_option( $playwire->published_videos_option_name, $playwire->published_videos );
			}
		}

	}

}