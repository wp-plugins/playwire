<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * PlaywireVideoSingleLibrary class.
 */
class PlaywireVideoSingleLibrary extends Playwire{

	/**
	* __construct function.
	*
	* @access public
	* @return void
	*/
	public function __construct() {
		add_action( 'media_upload_playwire_video', array( $this, 'init_media_menu' ) );
		add_filter( 'media_upload_tabs',            array( $this, 'add_video_tab' ) );
	}


	/**
	* add_video_tab
	*
	* @param mixed $tabs
	* @access public
	* @return void
	*/
	public function add_video_tab( $tabs ) {
		$tab = array( 'playwire_video' => __( 'Insert Playwire Video', 'playwire' ) );
		return array_merge( $tabs, $tab );
	}


	/**
	* init_media_menu
	*
	* @access public
	* @return void
	*/
	public function init_media_menu() {
		return wp_iframe( array( $this, 'media_video_library_display') );
	}


	/**
	* media_video_library_display
	*
	* @todo: Implement AJAX for the search as it will only be able to search on the current page that it's on using fast live filter
	* @access public
	* @return void
	*/
	public function media_video_library_display() {
		media_upload_header();
		$query = new WP_Query( array(
			'post_type'      => playwire()->videos_post_type,
			'post_status'    => 'publish',
			'posts_per_page' => 26,
			'paged'          => ( isset( $_REQUEST['pagenum'] ) ? $_REQUEST['pagenum'] : 1 ),
		) );

		$page_links = paginate_links(  array(
			'base'      => add_query_arg( 'pagenum', '%#%' ),
			'format'    => '',
			'prev_text' => __( '&laquo;', 'text-domain' ),
			'next_text' => __( '&raquo;', 'text-domain' ),
			'total'     => $query->max_num_pages,
			'current'   => ( isset( $_REQUEST['pagenum'] ) ? $_REQUEST['pagenum'] : 1 ),
		) );

		include_once PLAYWIRE_PATH . 'templates/template-video-single-library.php';

	}

}
