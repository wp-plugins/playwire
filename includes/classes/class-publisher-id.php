<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
* PlaywirePublisher class.
*/
class PlaywirePublisher extends Playwire {

	/**
	* 
	*
	* @var string
	*/
	public $pub_id = null;

	/**
	* Setup pub id related actions
	*
	* @access public
	* @return void
	*/
	public function __construct() {
		add_action( 'admin_notices', array( $this, 'login_success_sync' ) );
	}

	/**
	* Setup pub id related actions
	*
	* @access public
	* @return void
	*/
	public function login_success_sync(){

		global $pagenow;

		if ($pagenow == 'options-general.php' && $_GET['page'] ==
		'playwire-settings') { 
			    if ( (isset($_GET['updated']) && $_GET['updated'] == 'true') || (isset($_GET['settings-updated']) && $_GET['settings-updated'] == 'true') ) {

			   	// Get playwire
				$playwire = playwire();

				// Get the value
				$token = $playwire->options[ $playwire->token ];
				$sync  = isset($playwire->options[ $playwire->sync ]);

				if( $token ){

					//clear default update message "Settings Saved" 
			    	unset($_GET['settings-updated']);

			    	$video_url  = esc_url(admin_url( 'edit.php?post_type=playwire_videos' ));
			    	$galley_url = esc_url(admin_url( 'edit.php?post_type=playwire_playlists' ));

			    	// custom success message
			      	$update_message = "<h3 class='error-styles'>Your Playwire.com account was successfully linked!</br>To get started, <a href='$video_url'>Click Here</a> to view your Videos or <a href='$galley_url'>Click Here</a> to create Video Galleries.</h3>";
			      	add_settings_error('general', 'settings_updated', $update_message, 'green-warning animated pulse');

			      	//if token present, sync data from Playwire API
					PlaywireCrons::cron();

					// get publisher_id 
					self::pub_id_init(); 

		    	}

				if ( !$token ){
					//clear default update message "Settings Saved" 
			    	unset($_GET['settings-updated']);

			    	// custom success message
			      	$error_message = '<h3 class="error-styles">There was an error signing in. Please try signing in again.</h3>';
			      	add_settings_error('general', 'settings_updated', $error_message, 'playwire-error animated shake');
				}

			}
		}
	}

	/**
	* Setup Publisher ID wp_options playwire_publisher_id table
	*
	* @access public
	* @return void
	*/
	public function pub_id_init() {

		$has_videos = get_option('playwire_user_videos');
		$no_pub_id_url = esc_url('//phoenix.playwire.com/en/');
		
		// if videos present in wp_options table, set $pub_id to the id of the first video in array, else keep $pub_id set to null
		empty($has_videos) ? $pub_id = null : $pub_id = $has_videos[0]['creator']['publisher']['id'];

		if(empty($has_videos)){

			$pub_id_options = array(
				'publisher_id'         => $pub_id,
				'date_created'         => current_time( 'Y-m-d' ),
				'no_pub_id_url'        => $no_pub_id_url,
				'create_videos_url'    => '',
				'create_playlists_url' => ''
			);

			update_option( 'playwire_publisher_id', $pub_id_options );

		}else { 

			$create_videos_url = esc_url('//phoenix.playwire.com/en/publishers/' . rtrim($pub_id, '/' ) . '/videos/new');
			$create_playlists_url = esc_url('//phoenix.playwire.com/en/publishers/' . rtrim($pub_id, '/' ) . '/playlists/new');

			$pub_id_options = array(
				'publisher_id'         => $pub_id,
				'date_created'         => current_time( 'Y-m-d' ),
				'no_pub_id_url'        => $no_pub_id_url,
				'create_videos_url'    => $create_videos_url,
				'create_playlists_url' => $create_playlists_url
			);

			update_option( 'playwire_publisher_id', $pub_id_options );

		}

	}

	/**
	* Setup pub_id and urls for templates to use
	*
	* @access public
	* @return $pub_id
	*/
	public static function get_pub_id() {

		$get_pub_opt = get_option('playwire_publisher_id');
		$pub_id_int = $get_pub_opt['publisher_id'];

		$pub_id = (string)$pub_id_int;

		return $pub_id;
	}

	/**
	* Check to see if playlists are present in playwire_playlists wp_options table
	*
	* @access public
	* @return $get_playlists_opt
	*/
	public static function get_playlists() {

		return $get_playlists_opt = get_option('playwire_playlists');
		
	}

	/**
	* Setup pub_id and urls for templates to use
	*
	* @access public
	* @return $pub_id_url
	*/
	public static function pub_id_videos() {

		$get_pub_opt = get_option('playwire_publisher_id');
		$check_pub_id = $get_pub_opt['publisher_id'];
		$no_pub_id_url = $get_pub_opt['no_pub_id_url'];
		$create_videos_url = $get_pub_opt['create_videos_url'];

		if (empty( $check_pub_id )){

			return $pub_id_url = $no_pub_id_url;

		}else{

			return $pub_id_url = $create_videos_url;

		}

	}

	/**
	* Setup pub_id and urls for templates to use
	*
	* @access public
	* @return $pub_id_url
	*/
	public static function pub_id_playlists() {

		$get_pub_opt = get_option('playwire_publisher_id');
		$check_pub_id = $get_pub_opt['publisher_id'];
		$no_pub_id_url = $get_pub_opt['no_pub_id_url'];
		$create_playlists_url = $get_pub_opt['create_playlists_url'];

		if (empty($check_pub_id)){

			return $pub_id_url = $no_pub_id_url;

		}else{

			return $pub_id_url = $create_playlists_url;

		}

	}

}
