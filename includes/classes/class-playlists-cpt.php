<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * PlaywirePostTypePlaylists class.
 */
class PlaywirePostTypePlaylists extends Playwire {

	/**
	* __construct function.
	*
	* @access private
	* @return void
	*/
	public function __construct() {
		add_action( 'init',             array( $this, 'posttype'                 )    );
		add_filter( 'enter_title_here', array( $this, 'filter_cpt_title'         ), 8 );
		add_action( 'admin_menu',       array( $this, 'hide_add_new_custom_type' )    );
		add_action( 'admin_notices',    array( $this, 'playlist_helper_text'     )    );
	}


	/**
	* posttype function.
	*
	* @access public
	* @return void
	*/
	public function posttype() {

		// Post Type Labels
		$labels = array(
			'all_items'            => esc_attr__( 'Playlists',                   'playwire' ),
			'name'                 => esc_attr__( 'Playlist',                    'playwire' ),
			'singular_name'        => esc_attr__( 'Playlist',                    'playwire' ),
			'add_new'              => esc_attr__( 'Add New Playlist',            'playwire' ),
			'add_new_item'         => esc_attr__( 'Add New Playlist',            'playwire' ),
			'edit_item'            => esc_attr__( 'Edit Playlist',               'playwire' ),
			'new_item'             => esc_attr__( 'New Playlist',                'playwire' ),
			'view_item'            => esc_attr__( 'View Playlist',               'playwire' ),
			'search_items'         => esc_attr__( 'Search Playlist',             'playwire' ),
			'not_found'            => esc_attr__( 'No playlists found',          'playwire' ),
			'not_found_in_trash'   => esc_attr__( 'No playlists found in trash', 'playwire' ),
			'parent_item_colon'    => esc_attr__( 'Parent Playlist:',            'playwire' ),
			'menu_name'            => esc_attr__( 'Playwire',                    'playwire' ),
		);

		// Rewrite rules
		$rewrite = array(
			'slug'       => $this->playlists_post_type,
			'with_front' => false
		);

		// Set Up the CPT array
		$args = array(
			'labels'               => $labels,
			'hierarchical'         => false,
			'description'          => esc_attr__( 'Playlist Description', 'playwire' ),
			'supports'             => array( 'title' ),
			'taxonomies'           => array(),
			'public'               => false,
			'show_ui'              => true,
			'show_in_menu'         => $this->menu_page,
			'menu_icon'            => 'dashicons-video-alt',
			'show_in_nav_menus'    => false,
			'publicly_queryable'   => false,
			'exclude_from_search'  => false,
			'has_archive'          => false,
			'query_var'            => false,
			'can_export'           => true,
			'rewrite'              => $rewrite,
			'capability_type'      => 'post',
		);

		register_post_type( $this->playlists_post_type, $args );

	}


	/**
	* hide_add_new_custom_type function.
	*
	* @access public
	* @return void
	*/
	public function hide_add_new_custom_type() {
		global $submenu;
		unset( $submenu["edit.php?post_type={$this->playlists_post_type}"][10] );
	}


	/**
	* filter_cpt_title function.
	*
	* @access public
	* @param mixed $title
	* @return void
	*/
	public function filter_cpt_title( $title = '' ) {

		if ( get_post_type() === $this->playlists_post_type ) {
			$title = esc_attr__( 'Enter Playlist Name', 'playwire' );

		}

		return $title;
	}


	/**
	* playlist_helper_text
	*
	* @access public
	* @return void
	*/
	public function playlist_helper_text() {
		global $pagenow;
		if ( ( 'post.php' == $pagenow || 'post-new.php' == $pagenow ) && get_post_type() === $this->playlists_post_type  ) {
			?>
			<div class="updated">
				<p><?php esc_html_e( 'Create playlists on your Playwire.com account. Select your playlist and configure your desired layout below to embed on your website.', 'playwire' ); ?></p>
			</div>
			<?php
		}
	}

}
