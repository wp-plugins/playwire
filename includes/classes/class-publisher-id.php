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
			      	$update_message = "<div class='green-warning animated pulse'><h2 class='error-styles'>Your Playwire.com account was successfully linked!</h2><h3>Watch the videos below for help getting started with the Playwire Wordpress Plugin</h3></div><div class='row-holder'><div class='success-instructions'><h2>View and Upload Videos</h2><h4>*PLEASE NOTE - <em>You can upload videos from Playwire.com <strong>and</strong> from this Plugin.</em></h4><h4>If you have already have videos on Playwire, all your videos should be synced here already: <a href='/wp-admin/edit.php?post_type=playwire_videos'><br>Playwire Video &gt;&nbsp;Videos</a></h4><h4>If you have no videos on Playwire, watch the tutorial video to learn how to upload your videos to Playwire.com from the Playwire Video Wordpress Plugin.</h4></div><div class='success-video'><script data-config='//config.playwire.com/1000830/videos/v2/3670323/zeus.json' data-css='//cdn.playwire.com/bolt/js/zeus/skins/default.css' data-height='100%' data-width='100%' src='//cdn.playwire.com/bolt/js/zeus/embed.js' type='text/javascript'></script></div></div><hr class='success-hr'><div class='row-holder'><div class='success-instructions'><h3>Create Video Galleries from Playlists on Playwire</h3><h4>*PLEASE NOTE* - <em>Playwire Playlists are <strong>NOT</strong> the same as Video Galleries you create with this Plugin</em></h4><h4>You can easily create custom Video Galleries to embed in your pages with this plugin. Watch the tutorial video to learn how create Video Galleries from your Playwire Playlists.</h4><h4>After watching the tutorial video, <a href='/wp-admin/edit.php?post_type=playwire_playlists'>click here</a> to create your first Video Gallery.</h4></div><div class='success-video'><script data-config='//config.playwire.com/1000830/videos/v2/3670306/zeus.json' data-css='//cdn.playwire.com/bolt/js/zeus/skins/default.css' data-height='100%' data-width='100%' src='//cdn.playwire.com/bolt/js/zeus/embed.js' type='text/javascript'></script></div></div><hr class='success-hr'><div class='row-holder'><div class='success-instructions'><h3>Embedding Videos and Video Galleries in Your Page or Post</h3><h4>You can easily embed Videos and Video Galleries in your Wordpress Pages and Posts. Watch the tutorial video to learn how.</h4></div><div class='success-video'><script data-config='//config.playwire.com/1000830/videos/v2/3670274/zeus.json' data-css='//cdn.playwire.com/bolt/js/zeus/skins/default.css' data-height='100%' data-width='100%' src='//cdn.playwire.com/bolt/js/zeus/embed.js' type='text/javascript'></script></div></div><hr class='success-hr'>";
			      	add_settings_error('general', 'settings_updated', $update_message, 'successs');

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
