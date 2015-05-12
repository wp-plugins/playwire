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
			'all_items'            => esc_attr__( 'Video Galleries',                   'playwire' ),
			'name'                 => esc_attr__( 'Video Gallery',                     'playwire' ),
			'singular_name'        => esc_attr__( 'Video Gallery',                     'playwire' ),
			'add_new'              => esc_attr__( 'Add New Video Gallery',             'playwire' ),
			'add_new_item'         => esc_attr__( 'Add New Video Gallery',             'playwire' ),
			'edit_item'            => esc_attr__( 'Edit Video Galleries',              'playwire' ),
			'new_item'             => esc_attr__( 'New Video Gallery',                 'playwire' ),
			'view_item'            => esc_attr__( 'View Video Galleries',              'playwire' ),
			'search_items'         => esc_attr__( 'Search Video Galleries',            'playwire' ),
			'not_found'            => esc_attr__( "Oops, you haven't created any Video Galleries yet, click the 'Add New Video Gallery' button above to get started",          'playwire' ),
			'not_found_in_trash'   => esc_attr__( 'No Video Galleries found in trash', 'playwire' ),
			'parent_item_colon'    => esc_attr__( 'Parent Video Gallery:',             'playwire' ),
			'menu_name'            => esc_attr__( 'Playwire',                          'playwire' ),
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
			'description'          => esc_attr__( 'Video Gallery Description', 'playwire' ),
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
			$title = esc_attr__( 'Enter Video Gallery Name', 'playwire' );

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

		if ( ('edit.php' == $pagenow || 'post.php' == $pagenow || 'post-new.php' == $pagenow) && get_post_type() === "playwire_playlists" ) {

			$has_playlists = PlaywirePublisher::get_playlists();
			$url = PlaywirePublisher::pub_id_playlists();

			if (!$has_playlists) :  ?>
				<div class="error-container">
				<div class="playwire-error animated shake">
					<h3><span class="error-text"><span class="dashicons dashicons-flag"></span> NO PLAYLISTS AVAILABLE</span></h3>
					<h3 class="error-styles">You <u><strong>MUST</strong></u> create playlists on your Playwire.com account before you are able to create and embed Video Galleries</h3>

					<div class="half">
						<h3 class="error-styles">Click here to &roarr;<a href="<?php echo $url ?>" class="playwire-btn" target="_blank">Create Playlists on Playwire&nbsp;<span class="dashicons dashicons-format-video"></span></a>
						</h3>
					</div>

					<div class="half-r">
						<h3 class="error-styles">or Click here for &roarr;</span><a href="http://support.playwire.com" class="help-btn" target="_blank">Help Creating Playlists <span class="dashicons dashicons-editor-help"></span></span></a></h3>
					</div>
				</div>
				</div>

			<?php else : ?>
				<div class="error-container">
				<div class="playwire-warning animated bounce">
					<h3><span class="warning-text"><span class="dashicons dashicons-flag"></span> IMPORTANT REMINDER</span></h3>
					<h3 class="error-styles">You <u>MUST</u> create playlists on your Playwire.com account before you are able to create  and embed Video Galleries</h3>

					<div class="half">
						<h3 class="error-styles">Click here to &roarr;<a href="<?php echo $url ?>" class="playwire-btn" target="_blank">Create Playlists on Playwire&nbsp;<span class="dashicons dashicons-format-video"></span></a>
						</h3>
					</div>

					<div class="half-r">
						<h3 class="error-styles">or Click here for &roarr;</span><a href="http://support.playwire.com" class="help-btn" target="_blank">Help Creating Playlists&nbsp;<span class="dashicons dashicons-editor-help"></span></span></a></h3>
					</div>
				</div>
				</div>	

			<?php endif; ?>

			<?php
		}
	}

}
