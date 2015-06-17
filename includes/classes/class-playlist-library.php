<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * PlaywirePlaylistLibrary class.
 */
class PlaywirePlaylistLibrary extends Playwire{

	/**
	* __construct function.
	*
	* @access public
	* @return void
	*/
	public function __construct() {
		add_action( 'media_upload_playwire_playlists', array( $this, 'init_media_menu' ) );
		add_filter( 'media_upload_tabs',               array( $this, 'add_playlist_tab' ) );
	}


	/**
	* add_playlist_tab
	*
	* @param mixed $tabs
	* @access public
	* @return void
	*/
	public function add_playlist_tab( $tabs ) {
		$tab = array( 'playwire_playlists' => __( 'Insert Playwire Video Gallery', 'playwire' ) );
		return array_merge( $tabs, $tab );
	}

	/**
	* init_media_menu
	*
	* @access public
	* @return void
	*/
	public function init_media_menu() {
		return wp_iframe( array( $this, 'media_playlist_library_display') );
	}

	/**
	* media_playlist_library_display
	*
	* @todo: Implement AJAX for the search as it will only be able to search on the current page that it's on using fast live filter
	* @access public
	* @return void
	*/
	public function media_playlist_library_display() {
		media_upload_header();

		$query = new WP_Query( array(
			'post_type'      => playwire()->playlists_post_type,
			'post_status'    => 'publish',
			//'offset'         => ( isset( $_REQUEST['pagenum'] ) ? $_REQUEST['pagenum'] : 1 ),
			'posts_per_page' => 50
		) );


		$page_links = paginate_links(  array(
			'base'      => add_query_arg(  'pagenum',  '%#%'  ),
			'format'    => '',
			'prev_text' => __(  '&laquo;',  'text-domain'  ),
			'next_text' => __(  '&raquo;',  'text-domain'  ),
			'total'     => $query->max_num_pages,
			'current'   => ( isset( $_REQUEST['pagenum'] ) ? $_REQUEST['pagenum'] : 1 ),
		) );

		include_once PLAYWIRE_PATH . 'templates/template-playlist-library.php';

	}

}
