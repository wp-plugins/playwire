<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * PlaywirePlayerPostHandler class.
 */
class PlaywirePlayerPostHandler extends Playwire {

	/**
	 * setup_playlist_template function.
	 *
	 * @access public
	 * @param int $post_id
	 * @todo: Refactor this it's getting a bit hard to understand.
	 * @todo: Add caching to the templates on the front-end side of things.
	 @return void
	 */
	public static function setup_playlist_template( $post_id = 0, $array = false ) {
		$post_id = ( $post_id ? $post_id : 0 );
		// Gets the current playlist for the Playlist
		$current_playlist                 = self::get_current_playlist( $post_id );

		// Gets the current video layout for the Playlist
		$current_video_layout             = self::get_current_video_layout( $post_id );

		// Loaded here, used in templates and metabox
		$current_single_video             = self::get_current_single_video( $post_id );

		// Gets the current playlist ratio option ( string ) for the Playlist
		$current_ratio                    = self::get_current_ratio( $post_id );

		$current_ratio_select             = self::get_current_ratio_select( $post_id );

		$current_gallery_pagination       = self::get_current_gallery_pagination_type( $post_id );

		// Ternary check to see if the option exists in the $template_types array as defined in the Playwire class/
		$template = ( isset( $current_video_layout ) ? array_search( $current_video_layout, playwire()->template_types ) : 'sorry' );

		// Override for templates without saving/updating the post meta
		$template             = ( isset( $_POST['template'] )             ? $_POST['template']             : $template             );
		$current_playlist     = ( isset( $_POST['current_playlist'] )     ? $_POST['current_playlist']     : $current_playlist     );
		$current_single_video = ( isset( $_POST['current_single_video'] ) ? $_POST['current_single_video'] : $current_single_video );
		$current_ratio        = ( isset( $_POST['current_ratio'] )        ? $_POST['current_ratio']        : $current_ratio        );
		$current_ratio_select = ( isset( $_POST['current_ratio_select'] ) ? $_POST['current_ratio_select'] : $current_ratio_select );

		// Creates an id to use in the Playlist template
		$id = playwire()->prefix . $template . '_' . $post_id . '_' . $current_gallery_pagination;

		if ( isset( playwire()->playwire_aspect_ratio['ratios'][$current_ratio]['size'][$current_ratio_select] ) ) {
			$current_size_array = playwire()->playwire_aspect_ratio['ratios'][$current_ratio]['size'][$current_ratio_select];
		} else {
			$current_size_array = array(
				'width'  => 512,
				'height' => 288,
			);
		}

		// Get height/width, and setup aspect ratio
		$original_ratio_height = ( self::get_current_ratio_height( $post_id ) ? self::get_current_ratio_height( $post_id ) : $current_size_array['height'] );
		$current_ratio_width   = ( self::get_current_ratio_width( $post_id )  ? self::get_current_ratio_width( $post_id )  : $current_size_array['width']  );
		$maintain_aspect_ratio = self::get_current_maintain_ratio( $post_id );
		$current_ratio_height  = ( $maintain_aspect_ratio == '1' ? ( $current_ratio_width / 1.7 ) : $original_ratio_height );

		// Gets the current playlist by id ( array ) from the options table
		$playlist    = get_option( playwire()->playlist_option_name . $current_playlist );
		$video_count = ( empty( $playlist['videos'] ) ? '' : count( $playlist['videos'] ) );

		// Defines the template filename
		$file = PLAYWIRE_PATH . "templates/template-playlist-preview-{$template}.php";

		// Include the template that is used throughout the plugin to display the Playwire shortcode
		if ( false === $array ) {
			add_thickbox();
			// Bail if single template & no single video
			if ( 'single' === $template ) {
				if ( empty( $current_single_video ) || ( 'undefined' === $current_single_video ) ) {
					$file = PLAYWIRE_PATH . "templates/template-playlist-preview-none.php";
				}

			// Bail if no playlist
			} elseif ( empty( $playlist ) ) {
				$file = PLAYWIRE_PATH . "templates/template-playlist-preview-none.php";
			}

			// Attempt to include the template
			if ( file_exists( $file ) ) {
				include( $file );
			}

		// Return an array
		} else {
			return array(
				'current_playlist'              => $current_playlist,
				'current_video_layout'          => $current_video_layout,
				'current_ratio'                 => $current_ratio,
				'current_ratio_height'          => $current_ratio_height,
				'current_ratio_width'           => $current_ratio_width,
				'maintain_aspect_ratio'         => $maintain_aspect_ratio,
				'current_gallery_pagination'    => $current_gallery_pagination,
				'file'                          => $file,
				'id'                            => $id,
				'playlist'                      => $playlist,
				'template'                      => $template
			);
		}
	}


	/**
	 * get_current_playlist function.
	 *
	 * @access public
	 * @param mixed $post_id
	 * @return void
	 */
	public static function get_current_playlist( $post_id = 0 ) {
		return get_post_meta( $post_id, playwire()->video_playlist, true );
	}

	/**
	* get_current_video_layout function.
	*
	* @access public
	* @param mixed $post_id
	* @return void
	*/
	public static function get_current_video_layout( $post_id = 0 ) {
		$retval = get_post_meta( $post_id, playwire()->video_layout, true );
		if ( empty( $retval ) ) {
			$retval = __( 'Playwire Native Playlist', 'playwire' );
		}
		return $retval;
	}


	/**
	* get_current_single_video function.
	*
	* @access public
	* @param mixed $post_id
	* @return void
	*/
	public static function get_current_single_video( $post_id = 0 ) {
		return get_post_meta( $post_id, playwire()->single_video, true );
	}


	/**
	* get_current_ratio function.
	*
	* @access public
	* @param mixed $post_id
	* @return void
	*/
	public static function get_current_ratio( $post_id = 0 ) {
		$retval = get_post_meta( $post_id, playwire()->playwire_aspect_ratio['option_name'], true );
		if ( empty( $retval ) ) {
			$retval = 'widescreen';
		}
		return $retval;
	}


	/**
	* get_current_ratio_height function.
	*
	* @access public
	* @param mixed $post_id
	* @return void
	*/
	public static function get_current_ratio_height( $post_id = 0 ) {
		return get_post_meta( $post_id, playwire()->playwire_aspect_ratio['height_option_name'], true );
	}


	/**
	* get_current_ratio_width function.
	*
	* @access public
	* @param mixed $post_id
	* @return void
	*/
	public static function get_current_ratio_width( $post_id = 0 ) {
		return get_post_meta( $post_id, playwire()->playwire_aspect_ratio['width_option_name'], true );
	}


	/**
	* get_current_ratio_select function.
	*
	* @access public
	* @param mixed $post_id
	* @return void
	*/
	public static function get_current_ratio_select( $post_id = 0 ) {
		$retval = get_post_meta( $post_id, playwire()->playwire_aspect_ratio['select_option_name'], true );
		if ( empty( $retval ) ) {
			$retval = 'medium';
		}
		return $retval;
	}


	/**
	* get_current_ratio_select function.
	*
	* @access public
	* @param mixed $post_id
	* @return void
	*/
	public static function get_current_maintain_ratio( $post_id ) {
		return get_post_meta( $post_id, playwire()->playwire_aspect_ratio['maintain_option_name'], true );
	}


	/**
	* get_current_gallery_pagination_type function.
	*
	* @access public
	* @static
	* @param mixed $post_id
	* @return void
	*/
	public static function get_current_gallery_pagination_type( $post_id ) {
		return get_post_meta( $post_id, playwire()->gallery_pagination_options['option_name'], true );
	}


	/**
	* get_current_video_data function.
	*
	* @access public
	* @static
	* @return void
	*/
	public static function get_current_video_data() {
		return get_option( playwire()->videos_option_name );
	}

}
