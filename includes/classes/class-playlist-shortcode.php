<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
* PlaylistMetaboxes class.
*/
class PlaywirePlaylistShortcode extends Playwire {


	/**
	* __construct function.
	*
	* @access private
	* @return void
	*/
	public function __construct() {
		add_shortcode( 'playwire_playlist', array( $this, 'add_playlist_shortcode' ) );
	}


	/**
	* add_playlist_shortcode function.
	*
	* @access public
	* @param mixed $atts
	* @return void
	*/
	public function add_playlist_shortcode( $atts = array() ) {

		// Extract the $atts into usable variables for instance $playlist_post_id.
		$atts = shortcode_atts( $this->shortcode_atts, $atts );
		// Use the shortcode attribute to get the playlist template by $post_id ( $playlist_post_id ).
		if ( ! empty( $atts['playlist_post_id'] ) ) {
			ob_start();
			PlaywirePlayerPostHandler::setup_playlist_template( absint( $atts['playlist_post_id'] ), false );
			$content = ob_get_clean();
			return $content;
		}
	}

}
