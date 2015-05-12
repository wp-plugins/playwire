<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
* PlaywireCron class
*/
class PlaywireCrons extends Playwire {

	/**
	* __construct function.
	*
	* @access public
	* @return void
	* @todo find a way to add minute schedule interval to WP cron.php file through plugin
	*/
	public function __construct() {

		add_action( 'update_playwire_data', array( __CLASS__, 'cron' ) );
		if ( ! wp_next_scheduled( 'update_playwire_data' ) ) {
			wp_schedule_event( time(), 'hourly', 'update_playwire_data' );
		}
	}

	/**
	* cron function.
	*
	* @access private
	* @return void
	*/
	public static function cron() {

		// Get playwire
		$playwire  = playwire();

		// Update the local playlists option
		PlaywireAPIHandler::update_options( $playwire->api_endpoint . '/playlists', $playwire->playlists_option_name );

		// Update the local videos option
		$page        = 1;
		$request_arr = array();

		do {
			$headers = PlaywireAPIHandler::add_token_to_headers( false );
			$request = PlaywireAPIHandler::request( $playwire->api_endpoint . "/videos.json?per=200&page={$page}", $headers );
			foreach( $request as $video ) {
				$request_arr[] = $video;
			}
			$page++;
		} while ( ! empty( $request ) );

		// Check if the request was sucessfully converted into an array
		if ( is_array( $request_arr ) ) {
			update_option( $playwire->videos_option_name, json_decode( json_encode( $request_arr ), true ) );
		}

		// Update individual playlists
		$playlists = get_option( $playwire->playlists_option_name );
		if ( ! empty( $playlists ) ) {
			foreach ( $playlists as $playlist ) {
				PlaywireAPIHandler::update_options( $playwire->api_endpoint . '/playlists/' . $playlist['id'], $playwire->playlist_option_name . $playlist['id'] );
			}
		}

		if ( isset( $playwire->options[ $playwire->sync ] ) ) {
			PlaywireAPIHandler::sync_all_videos_from_api();
		}
	}
}
