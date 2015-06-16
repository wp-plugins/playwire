<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
* VideoMetaboxes class.
*/
class PlaywireVideoShortcode extends Playwire {


	/**
	* __construct function.
	*
	* @access private
	* @return void
	*/
	public function __construct() {
		add_shortcode( 'playwire_video', array( $this, 'add_video_shortcode' ) );
	}


	/**
	* add_video_shortcode function.
	*
	* @access public
	* @param mixed $atts
	* @return void
	*/
	public function add_video_shortcode( $atts = array() ) {

		// Use the shortcode attribute to get the video template by $post_id ( $video_post_id ).
		if ( ! empty( $atts['video_post_id'] ) ) {
			ob_start();
			PlaywirePlayerPostHandler::setup_playlist_template( absint( $atts['video_post_id'] ), false );
			$content = ob_get_clean();
			return $content;
		}
	}

}
