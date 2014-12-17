<?php
/**
* Plugin Name: Playwire for WordPress
* Plugin URI:  http://wordpress.org/plugins/playwire
* Plugin Slug: playwire-for-wordpress
* Description: Playwire interfaces API with WordPress.
* Version:     1.0.0
* Author:      Playwire
* Author URI:  http://www.playwire.com
* License:     GPLv2+
* Text Domain: playwire
*/


/**
* Copyright (c) 2014 10up (email : 10up.com)
*
* This program is free software; you can redistribute it and/or modify
* it under the terms of the GNU General Public License, version 2 or, at
* your discretion, any later version, as published by the Free
* Software Foundation.
*
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with this program; if not, write to the Free Software
* Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
// Bail if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


/**
* The main Playwire class.
*/
class Playwire {

	/**
	* @var string The API endpoint for the playwire API
	* Currently you must specify http: otherwise Playwire will reject the request
	*/
	protected $api_endpoint = 'http://phoenix.playwire.com/api';

	/**
	* This array will set the default headers to be used with the Playwire API
	* and can be modified to included user tokens and the like.
	*
	* @var array
	*/
	protected $headers = array(
		'X-Playwire-Client:PHPPlaywireClient',
		'X-Playwire-Client-Version:0.1',
		'X-Playwire-Client-URL://phoenix.playwire.com'
	);

	/**
	* @var string Defines the playwire email address option key
	*/
	protected $email_address = 'playwire_email_address';

	/**
	* @var string Defines the playwire password option key
	*/
	protected $password = 'playwire_password';

	/**
	* @var string Defines the playwire sync option
	*/
	protected $sync = 'playwire_sync';

	/**
	* @var string Defines the playwire token key.
	*/
	protected $token = 'playwire_token';


	/**
	* @var string Defines the library id
	*/
	protected $library_id = 'playwire_library';

	/**
	* @var array Options
	*/
	protected $options = array();

	/**
	* @var array video options
	*/
	protected $videos = array();

	/**
	* @var string Registers the group that the Playwire options belong to
	*/
	protected $option_group = 'playwire_option_group';

	/**
	* @var array Defines the options for the aspect ratios of the video player
	*/
	protected $playwire_aspect_ratio = array();

	/**
	* @var string Nonce key
	*/
	protected $playwire_metabox_nonce = 'playwire_metabox_nonce';

	/**
	* @var string Defines the user playlists option name
	*/
	protected $playlists_option_name = 'playwire_playlists';

	/**
	* @var string Defines the user playlist option name
	*/
	protected $playlist_option_name = 'playwire_playlist_';

	/**
	* The main plugin options name stored in the wp_options table options are
	* stored as an array.
	*
	* @var string
	*/
	protected $plugin_options_name = 'playwire_plugin_options';

	/**
	* Stores an array of existing video id's from the Playwire API
	*
	* @var string
	*/
	protected $videos_options_name = 'playwire_videos';

	/**
	* Really just a placeholder for the thumbnail post meta but may change in the future so we're storing it here
	*
	* @var string
	*/
	protected $video_featured_image = '_thumbnail_id';

	/**
	* Stores an array of post meta for the videos CPT
	*
	* @var string
	*/
	protected $video_meta_name = 'playwire_video_meta';

	/**
	* Stores a string of the video id from the API
	*
	* @var string
	*/
	protected $video_meta_id_name = 'playwire_video_meta_id';

	/**
	* @var string Defines the section id for the main plugin options page.
	*/
	protected $setting_section_id = 'playwire_setting_section_id';

	/**
	* @var string The main Playwire plugins settings page.
	*/
	protected $settings_page = 'playwire-settings';

	/**
	* @var string Used for Videos CPT slug and ID
	*/
	protected $videos_post_type = 'playwire_videos';

	/**
	* @var string Used for playlists CPT slug and ID
	*/
	protected $playlists_post_type = 'playwire_playlists';

	/**
	* @var string Used for playlists taxonomy slug and ID
	*/
	protected $playlists_taxonomy = 'playwire_playlists';

	/**
	* @var string Used for videos taxonomy slug and ID
	*/
	protected $videos_taxonomy = 'playwire_videos';

	/**
	* @var string Plugin Prefix
	*/
	protected $prefix = 'playwire_';

	/**
	* @var string Menu page
	*/
	protected $menu_page = 'playwire';

	/**
	* @var string Menu slug
	*/
	protected $menu_slug = 'playwire_menu_slug';

	/**
	* @var string Defines the search input for the fastLiveFilter
	*/
	protected $search_input = 'search_input';

	/**
	* @var string Defines the search list for the fastLiveFilter
	*/
	protected $search_list = 'search_list';

	/**
	* The shortcode_atts will be used for not only the shortcode but also the
	* metabox preview so make sure the variables match the array for the
	* template.
	*
	* @var array
	*/
	protected $shortcode_atts = array(
		'playlist_post_id' => 'playlist_post_id',
	);

	/**
	* Setup array to use for template types to choose from this can be used in
	* templates or localized to js for easy output on the front-end.
	*
	* Furthermore it can be used for class names and anything else to keep
	* things DRY.
	*
	* @var array
	*/
	protected $template_types = array();

	/**
	* @var string Theater title key
	*/
	protected $playlist_title = 'playlist_title';

	/**
	* @var string Defines the user categories option name.
	*/
	protected $categories_option_name = 'playwire_categories';

	/**
	* @var string The plugin version
	*/
	protected $version = '1.0.1';

	/**
	* @var string Defines the post_meta for the video layout.
	*/
	protected $video_layout = 'playwire_video_layout';

	/**
	* @var string Defines the post_meta for the video playlist.
	*/
	protected $video_playlist = 'playwire_video_playlist';

	/**
	* @var string Defines the post_meta for the video a single video.
	*/
	protected $single_video = 'playwire_single_video';

	/**
	* @var string Defines the user videos option name.
	*/
	protected $videos_option_name = 'playwire_user_videos';

	/**
	* @var string Video title class title key
	*/
	protected $video_title = 'video_title';

	/**
	* @var string Defines the published videos option name.
	*/
	protected $published_videos_option_name = 'playwire_published_user_videos';

	/**
	* @var int Defines the gallery limit for pagination
	*/
	protected $gallery_limit = 3;

	/**
	* update_video_post_data_nonce
	*
	* (default value: 'update_video_post_data_nonce')
	*
	* @var string
	* @access protected
	*/
	protected $update_video_post_data_nonce = 'update_video_post_data_nonce';

	/**
	* @var string Defines the post_meta for a video post type video url
	*/
	protected $video_post_type_video_url = 'playwire_post_type_video_url';

	/**
	* @var string Defines the post_meta for a video post type intend to share option
	*/
	protected $video_post_type_intend_to_share = 'playwire_post_type_intend_to_share';

	//This needs to be hardcoded for now because the categories aren't available
	//via the Playwire API. The key is the ID on playwire and the value is the category
	//Use this array to create taxonomies upon plugin activation
	protected	$post_videos_video_categories =
		array(
			"1"  => "Games",
			"2"  => "Sports",
			"3"  => "Money/Finance",
			"4"  => "Religion",
			"5"  => "Food",
			"6"  => "Health/Fitness",
			"7"  => "Nature",
			"8"  => "Animation",
			"9"  => "Auto",
			"10" => "Celebrity",
			"11" => "News",
			"12" => "Home &amp; Living",
			"13" => "Travel",
			"14" => "Music",
			"15" => "Kids",
			"16" => "Movies",
			"17" => "Business",
			"18" => "TV",
			"19" => "Comedy",
			"20" => "Foreign &amp; International",
			"21" => "Technology",
			"22" => "Science",
			"23" => "Entertainment",
			"24" => "Fashion",
		);

	/* Magic methods */

	/**
	* Dummy Constructor
	*
	* @access private
	* @return void
	*/
	public function __construct() { /* Intentionally do nothing here*/ }

	/**
	* Singletons walk alone
	*
	* @access public
	* @return void
	*/
	public function __clone() { _doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'playwire' ), null ); }

	/**
	* Singletons like to sleep
	*
	* @access public
	* @return void
	*/
	public function __wakeup() { _doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'playwire' ), null ); }

	/** Private Methods*******************************************************/


	/**
	* Define some constants used through-out the plugin.
	*
	* @access private
	* @return void
	*/
	private function defines() {
		define( 'PLAYWIRE_URL',  plugin_dir_url( __FILE__ ) );
		define( 'PLAYWIRE_PATH', trailingslashit( dirname( __FILE__ ) ) );
	}


	/**
	* Include the required files.
	*
	* @access private
	* @return void
	*/
	private function includes() {
		require_once( PLAYWIRE_PATH . 'includes/classes/class-api-handler.php'            );
		require_once( PLAYWIRE_PATH . 'includes/classes/class-player-post-handler.php'    );
		require_once( PLAYWIRE_PATH . 'includes/classes/class-playlist-library.php'       );
		require_once( PLAYWIRE_PATH . 'includes/classes/class-video-single-library.php'   );
		require_once( PLAYWIRE_PATH . 'includes/classes/class-playlist-meta-boxes.php'    );
		require_once( PLAYWIRE_PATH . 'includes/classes/class-playlist-shortcode.php'     );
		require_once( PLAYWIRE_PATH . 'includes/classes/class-playlists-cpt.php'          );
		require_once( PLAYWIRE_PATH . 'includes/classes/class-playwire-crons.php'         );
		require_once( PLAYWIRE_PATH . 'includes/classes/class-settings.php'               );
		require_once( PLAYWIRE_PATH . 'includes/classes/class-video-api-interface.php'    );
		require_once( PLAYWIRE_PATH . 'includes/classes/class-video-category-walker.php'  );
		require_once( PLAYWIRE_PATH . 'includes/classes/class-video-meta-boxes.php'       );
		require_once( PLAYWIRE_PATH . 'includes/classes/class-video-radio-categories.php' );
		require_once( PLAYWIRE_PATH . 'includes/classes/class-video-shortcode.php'        );
		require_once( PLAYWIRE_PATH . 'includes/classes/class-videos-cpt.php'             );
	}


	/**
	* Pull in the dependent instances
	*/
	private function instantiator() {
		new PlaywireAPIHandler();
		new PlaywireCrons();
		new PlaywirePlaylistShortcode();
		new PlaywireVideoShortcode();
		// Admin only instances
		if ( is_admin() ) {
			new PlaywirePlaylistLibrary();
			new PlaywirePlaylistMetaboxes();
			new PlaywireVideoSingleLibrary();
			new PlaywirePostTypePlaylists();
			new PlaywirePostTypeVideos();
			new PlaywireSettings();
			new PlaywireVideoApiInterface();
			new PlaywireVideoMetaboxes();
			new PlaywireVideoRadioCategories();
		}
	}


	/**
	* Hooks class methods into WordPress actions
	*
	* @access private
	* @return void
	*/
	private function actions() {
		add_action( 'init',                  array( $this, 'playwire_init'           ), 2 );
		add_action( 'admin_enqueue_scripts', array( $this, 'playwire_scripts_styles' ), 0 );
		add_action( 'wp_enqueue_scripts',    array( $this, 'playwire_scripts_styles' ), 0 );
	}


	/**
	* Default initialization for the plugin:
	* - Registers the default textdomain.
	*
	* @access public
	* @return void
	*/
	public function playwire_init() {

		// Setup aspect ratios
		$this->playwire_aspect_ratio =
			array(
				'option_name'          => 'playwire_aspect_ratio',
				'maintain_option_name' => 'playwire_maintain_aspect_ratio',
				'select_option_name'   => 'playwire_aspect_ratio_select',
				'height_option_name'   => 'playwire_aspect_ratio_height',
				'width_option_name'    => 'playwire_aspect_ratio_width',
				'ratios'               => array(
					'widescreen' => array(
						'name' => __( '16x9 (Widescreen)', 'playwire' ),
						'size' => array(
							'small'   => array(
							'label'   => '320x180',
							'width'   => '320',
							'height'  => '180'
							),
							'medium'  => array(
							'label'   => '512x288',
							'width'   => '512',
							'height'  => '288'
							),
							'large'   => array(
							'label'   => '768x432',
							'width'   => '768',
							'height'  => '432'
							)
						),
					),
					'classic' => array(
						'name' => __( '4:3 (Classic)', 'playwire' ),
						'size' => array(
							'small'     => array(
							'label'     => '336x252',
							'width'     => '336',
							'height'    => '252'
							),
							'medium'    => array(
							'label'     => '480x360',
							'width'     => '480',
							'height'    => '360'
							),
							'large'  => array(
							'label'  => '640x480',
							'width'  => '640',
							'height' => '480'
							)
						)
					),
					'custom' => array(
						'name' => __( 'Custom (User Defined)', 'playwire' ),
						'size' => null
					)
				)
			);

		// Setup translatable template types
		$this->template_types =
			array(
				'native'     => __( 'Playwire Native Playlist', 'playwire' ),
				'film_strip' => __( 'Film Strip',               'playwire' ),
				'gallery'    => __( 'Gallery',                  'playwire' ),
				'single'     => __( 'Single Video',             'playwire' )
			);

		// Setup translatable template types
		$this->gallery_pagination_options =
			array(
				'option_name' => 'playwire_gallery_pagination_options',
				'options'     =>
					array(
						'none'         => __( 'None',         'playwire' ),
						'more'         => __( 'More Button',  'playwire' ),
						'pagination'   => __( 'Pagination',   'playwire' )
					)
			);

		// Setup the default videos Array for GET, POST, PUT to playwire API
		$this->post_videos_arr =
			array(
				"video_method" => '',   //Does not exist on playwire API defaults should be update, create
				"token"        => '',   //Hash / String
				"video"        =>
					array(
						"id"          => ( int ) 0,
						"source_url"  => ( string ) '',
						"name"        => ( string ) '',
						"description" => ( string ) '',
						"duration"    => ( int ) 0,
						"created_at"  => ( string ) '',
						"state"       => ( string ) '',
						"category_id" => ( array ) array(), //Accepts Video category array
						"thumbnail"   => ( array ) array(), //Accepts Video still array
						"creator"     => ( array ) array(), //Accepts Video user array
					)
			);

		$this->post_videos_video_category =
			array(
				"id"           => '',
				"name"         => '',
				"youtube_term" => ''
			);

		$this->post_videos_video_still =
			array(
				"320x240" => '',
				"160x120" => '',
				"80x60"   => '',
				"96x96"   => ''
			);

		$this->post_videos_video_user =
			array(
				"id"        => '',
				"name"      => '',
				"publisher" => '',
			);

		$this->post_videos_video_publisher =
			array(
				"id"   => '',
				"name" => ''
			);


		// Load the options
		$this->options = get_option( $this->plugin_options_name );

		// Load option of all videos that currently exist
		$this->videos = ( get_option( $this->videos_option_name ) ? get_option( $this->videos_option_name ) : array( ) );

		// Load option of all published videos that currently exist
		$this->published_videos = get_option( 'playwire_published_user_videos', array() );

		// Load the translations
		load_plugin_textdomain( 'playwire' );

		if ( function_exists( 'add_image_size' ) ) {
			add_image_size( 'playwire-large',    320, 240, true );
			add_image_size( 'playwire-medium',   160, 120, true );
			add_image_size( 'playwire-small',    80, 60, true   );
			add_image_size( 'playwire-small-sq', 96, 96, true   );
		}

	}


	/**
	* playwire_scripts_styles function.
	*
	* @access public
	* @return void
	*/
	public function playwire_scripts_styles() {
		global $post, $pagenow;
		// Get the version
		$version = $this->version;

		// Scripts
		wp_register_script( 'fast-live-filter', PLAYWIRE_URL . 'assets/js/vendor/jquery-fastLiveFilter/jquery.fastLiveFilter.js', array(),                                                         $version, true );
		wp_register_script( 'flexslider',       PLAYWIRE_URL . 'assets/js/vendor/flexslider/jquery.flexslider-min.js',            array(),                                                         $version, true );
		wp_register_script( 'playwire-scripts', PLAYWIRE_URL . 'assets/js/playwire.min.js',                                       array( 'jquery', 'jquery-ui-dialog',  'thickbox', 'fast-live-filter', 'flexslider' ), $version, true );

		$post_id = ( isset( $post->ID ) ? $post->ID : 0 );
		$localized_vars = array( 'adminurl' => esc_url( admin_url( "edit.php?post_type={$this->videos_post_type}" ) ), 'ajaxurl' => esc_url( admin_url( 'admin-ajax.php' ) ), 'compare_json' => PlaywireVideoApiInterface::compare_json( $post_id ) );

		//Set $*_edit_screen variables to false by default
		$is_edit_screen   = false;
		$edit_screen_url  = false;
		$edit_screen_text = false;

		if ( get_post_type() === $this->videos_post_type || $pagenow == "post.php?post_type={$this->videos_post_type}" ) {
			$edit_screen_url  = admin_url( "post-new.php?post_type={$this->playlists_post_type}" );
			$edit_screen_text = esc_html( 'Add New Playlist', 'playwire' );
			$is_edit_screen   = true;
		}

		if ( get_post_type() === $this->playlists_post_type || $pagenow == "post.php?post_type={$this->playlists_post_type}" ) {
			$edit_screen_url  = admin_url( "post-new.php?post_type={$this->videos_post_type}" );
			$edit_screen_text = esc_html( 'Add New Video', 'playwire' );
			$is_edit_screen   = true;
		}


		wp_localize_script( 'playwire-scripts', 'PlaywireObject', $localized_vars );
		wp_enqueue_script( 'playwire-scripts' );

		// Pass variables to our js to reduce name confusion.
		wp_localize_script( 'playwire-scripts', 'playwire_object', array(
			'edit_screen_url'    => $edit_screen_url,
			'edit_screen_text'   => $edit_screen_text,
			'is_edit_screen'     => $is_edit_screen,
			'template_types'     => $this->template_types,
			'video_playlist'     => $this->video_playlist,
			'gallery_limit'      => $this->gallery_limit,
			$this->search_input  => $this->search_input,
			$this->search_list   => $this->search_list
		) );

		// Styles
		wp_register_style( 'playwire-styles', PLAYWIRE_URL . 'assets/css/playwire.min.css', array(), $version, 'all' );
		wp_enqueue_style( 'wp-jquery-ui-dialog' );
		wp_enqueue_style( 'playwire-styles' );
	}


	/**
	* Main Playwire Instance
	*
	* Ensures that only one instance of Playwire exists in memory at any one
	* time. Also prevents needing to define globals all over the place.
	*
	* @access public
	* @static
	* @return void
	*/
	public static function instance() {

		// Store the instance locally to avoid private static replication
		static $instance = null;

		// Only run these methods if they haven't been ran previously
		if ( null === $instance ) {
			$instance = new Playwire;
			$instance->defines();
			$instance->includes();
			$instance->instantiator();
			$instance->actions();
		}

		return $instance;
	}
}


/**
* The main function responsible for returning the one true Playwire Instance
* to functions everywhere.
*
* Use this function like you would a global variable, except without needing
* to declare the global.
*
* Example: <?php $playwire = playwire(); ?>
*
* @return The one true Playwire Instance
*/
function playwire() {
	return Playwire::instance();
}


/**
* Hook Playwire early onto the 'plugins_loaded' action.
*
* This gives all other plugins the chance to load before Playwire, to get their
* actions, filters, and overrides setup without Playwire being in the way.
*/
if ( defined( 'PLAYWIRE_LATE_LOAD' ) ) {
	add_action( 'plugins_loaded', 'playwire', (int) PLAYWIRE_LATE_LOAD );

// "A man must have a code."
} else {
	playwire();
}
