<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * PlaywirePostTypeVideos class.
 */
class PlaywirePostTypeVideos extends Playwire {

	/**
	* __construct function.
	*
	* @access private
	* @return void
	*/
	public function __construct() {
		add_action( 'init',                                         array( $this, 'posttype'                     )        );
		add_action( 'init',                                         array( $this, 'add_taxonomies'               ), 0     );
		add_action( 'admin_menu',                                   array( $this, 'reset_videos_excerpt_metabox' ), 0     );
		add_action( 'admin_menu',                                   array( $this, 'hide_add_new_custom_type'     )        );
		add_action( 'manage_playwire_videos_posts_custom_column',   array( $this, 'videos_columns'               ), 10, 2 );
		add_action( 'admin_notices',                                array( $this, 'video_helper_text'            )        );
		add_filter( 'enter_title_here',                             array( $this, 'filter_cpt_title'             ), 8  );
		add_filter( 'manage_playwire_videos_posts_columns',         array( $this, 'videos_head_columns'          ), 10 );
		add_filter( 'manage_edit-playwire_videos_sortable_columns', array( $this, 'videos_sortable_title'        )     );
		
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
			'all_items'            => esc_attr__( 'Videos',                   'playwire' ),
			'name'                 => esc_attr__( 'Video',                    'playwire' ),
			'singular_name'        => esc_attr__( 'Video',                    'playwire' ),
			'add_new'              => esc_attr__( 'Add New Video',            'playwire' ),
			'add_new_item'         => esc_attr__( 'Add New Video',            'playwire' ),
			'edit_item'            => esc_attr__( 'Edit Video',               'playwire' ),
			'new_item'             => esc_attr__( 'New Video',                'playwire' ),
			'view_item'            => esc_attr__( 'View Video',               'playwire' ),
			'search_items'         => esc_attr__( 'Search Video',             'playwire' ),
			'not_found'            => esc_attr__( "Oops, you don't have any Videos yet, click the 'Add New Video' button above or upload videos to your Playwire.com account to get started",          'playwire' ),
			'not_found_in_trash'   => esc_attr__( 'No videos found in trash', 'playwire' ),
			'parent_item_colon'    => esc_attr__( 'Parent Video:',            'playwire' ),
			'menu_name'            => esc_attr__( 'Playwire',                 'playwire' ),
		);

		// Rewrite rules
		$rewrite = array(
			'slug'       => $this->videos_post_type,
			'with_front' => false
		);

		// Set Up the CPT array
		$args = array(
			'labels'               => $labels,
			'hierarchical'         => false,
			'description'          => esc_attr__( 'Video Description', 'playwire' ),
			'supports'             => array( 'title', 'excerpt', 'slug' ),
			'taxonomies'           => array(), //Tags may be supported in the future but only categories for now
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
			'capability_type'      => 'post'
		);

		register_post_type( $this->videos_post_type, $args );
	}


	/**
	* hide_add_new_custom_type function.
	*
	* Note: this doesn't work in WP 4.0 release cycle menus must have changed
	* @access public
	* @return void
	*/
	public function hide_add_new_custom_type() {
		global $submenu;
		unset( $submenu["edit.php?post_type={$this->videos_post_type}"][10] );
	}


	/**
	* filter_cpt_title function.
	*
	* @access public
	* @param mixed $title
	* @return void
	*/
	public function filter_cpt_title( $title = '' ) {
		if ( get_post_type() === $this->videos_post_type ) {
			$title = esc_attr__( 'Enter Video Name', 'playwire' );
		}
		return $title;
	}


	/**
	* add_taxonomies function.
	*
	* @access public
	* @return void
	*/
	public function add_taxonomies() {

		$labels = array(
			'name'              => esc_attr_x( 'Video Categories',       'taxonomy general name',  'playwire' ),
			'singular_name'     => esc_attr_x( 'Video Category',         'taxonomy singular name', 'playwire' ),
			'search_items'      => esc_attr__( 'Search Video Categories',                          'playwire' ),
			'all_items'         => esc_attr__( 'All Video Categories',                             'playwire' ),
			'parent_item'       => esc_attr__( 'Parent Video Category',                            'playwire' ),
			'parent_item_colon' => esc_attr__( 'Parent Video Category:',                           'playwire' ),
			'edit_item'         => esc_attr__( 'Edit Video Category',                              'playwire' ),
			'update_item'       => esc_attr__( 'Update Video Category',                            'playwire' ),
			'add_new_item'      => esc_attr__( 'Add New Video Category',                           'playwire' ),
			'new_item_name'     => esc_attr__( 'New Video Category Name',                          'playwire' ),
			'menu_name'         => esc_attr__( 'Video Categories',                                 'playwire' ),
		);

		$rewrite = array(
			'slug'         => $this->videos_taxonomy,
			'with_front'   => false,
			'hierarchical' => true
		);

		$args = array(
			'hierarchical'       => true,
			'labels'             => $labels,
			'rewrite'            => $rewrite,
			'show_in_nav_menus'  => false,
			'show_ui'            => true
		);

		register_taxonomy( $this->videos_taxonomy, array( $this->videos_post_type ), $args );

		/*
		* This will create the default taxonomies for the videos post type categories
		* These will be passed to the playwire API
		* @note: as funky as it seems we're going to pass the key as the description for the
		* Taxonomy this will be used for the Playwire API to distinguish it's assignment
		*/
		if ( is_array( $this->post_videos_video_categories ) ) {
			foreach ( $this->post_videos_video_categories as $key => $value ) {
				wp_insert_term( $value, $this->videos_taxonomy, array( 'description' => $key ) );
			}
		}

	}


	/*
	* videos_head_columns function
	*
	* @access public
	* @return array()  $defaults
	*
	*/
	public function videos_head_columns( $defaults ) {
		$offset = 1;
		//Here we will insert new elements into the defaults columns array for the video post type
		$arr = array(
			'video_thumbnail'  => 'Thumbnail',
			'title'            => 'Video Title',
			'uploaded_on'      => 'Uploaded On',
			'video_categories' => 'Category',
			'video_share'      => 'Sandbox Share'
		);

		$defaults = array_slice( $defaults, 0, $offset, true ) + $arr + array_slice( $defaults, $offset, NULL, true );
		//This will remove the default columns that we don't want to display because we will override them with custom ones
		unset( $defaults['date'], $defaults['tags'] );
		return $defaults;
	}


	/*
	* video_columns function
	*
	* @access public
	* @return void
	*
	*/
	public function videos_columns( $column_name, $post_ID ) {
		global $wpdb, $post;
		switch ( $column_name ) {
			case 'video_thumbnail':
				$video_id        = get_post_meta( $post_ID, $this->video_meta_name, true );
				$video_thumbnail = ( isset( $video_id['thumbnail']['320x240'] ) ? $video_id['thumbnail']['320x240'] : '//placehold.it/320x240/' . strtoupper(  dechex(  rand(  0,  10000000  )  )  ) . '/ffffff&amp;text=No&nbsp;Thumbnail' );
				?>
				<img src="<?php echo esc_url( $video_thumbnail ); ?>">
				<?php
			break;
			case 'uploaded_on':
				$uploaded = get_the_date('M j, Y @ g:ia');
				echo $uploaded;
			break;
			case 'video_categories':
				$terms = wp_get_object_terms( $post_ID, $this->videos_taxonomy );
				if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
					foreach( $terms as $term ) {
						$term_names[] = $term->name;
					}
					echo implode( ', ', $term_names );
				}
			break;
			case 'video_share':
				//Get the post meta for the share toggle value
				$intend_to_share = get_post_meta( $post->ID, $this->video_post_type_intend_to_share, true );
				$intend_to_share = ( isset( $intend_to_share ) ? $intend_to_share : true );
				?>
				<a href="#" data-tooltip="<?php esc_html_e( 'Share your video on the Playwire platform to allow publishing partners to syndicate on their websites. Edit post to Enable/Disable', 'playwire' ); ?>">
					<input id="<?php echo esc_attr( $this->video_post_type_intend_to_share ); ?>" type="checkbox" name="<?php echo esc_attr( $this->video_post_type_intend_to_share ); ?>" value="1"  <?php checked( true, $intend_to_share, true ); ?> disabled="disabled"/>
				</a>
<?php
			break;
		}

	}


	/*
	* videos_sortable_title function
	*
	* @access public
	* @return array()  $columns
	*
	*/
	public function videos_sortable_title( $columns ) {
		$columns['video_title_description'] = 'post_title';
		$columns['uploaded_on'] = 'uploaded_on';
		return $columns;
	}



	/**
	 * reset_videos_excerpt_metabox
	 *
	 * @access public
	 * @return void
	 */
	public function reset_videos_excerpt_metabox( ) {
		remove_meta_box( 'postexcerpt', $this->videos_post_type, 'normal' );
		add_meta_box( 'postexcerpt', __( 'Video Description' ), 'post_excerpt_meta_box', $this->videos_post_type, 'normal', 'high' );
	}

	/**
	* video_helper_text
	*
	* @access public
	* @return void
	*/
	public function video_helper_text() {

		global $pagenow;

		$publisher_id = PlaywirePublisher::get_pub_id();
		$url = PlaywirePublisher::pub_id_videos();

		if ( ( 'post.php' == $pagenow || 'post-new.php' == $pagenow ) && get_post_type() === $this->videos_post_type  ) {

			if (empty($publisher_id)): ?>
				<div class="error-container">
					<div class="playwire-error animated shake">
						<h3><span class="error-text"><span class="dashicons dashicons-flag"></span> UNABLE TO USE PLAYWIRE PLUGIN</span></h3>
							<h3 class="error-styles">You must have at least one video on your Playwire account before using the plugin. </br><a href="<?php echo $url ?>" target="_blank">Click here</a> to upload video files from Playwire.com account or visit our <a href="<?php echo esc_url('http://support.playwire.com/wordpress-plugin-help/') ?>" target="_blank">Support Site</a> for help.</h3>
					</div>
				</div>

			<?php else: ?>
				<div class="error-container">
					<div class="playwire-warning animated flash">
						<h3><span class="warning-text"><span class="dashicons dashicons-flag"></span> IMPORTANT REMINDER</span></h3>
						<h3 class="error-styles">The upload file size limit with this plugin is approximately <b>20MB</b>. <a href="<?php echo $url ?>" target="_blank">Click here</a> to upload larger video files from your Playwire account or visit our <a href="<?php echo esc_url('http://support.playwire.com/wordpress-plugin-help/') ?>" target="_blank">Support Site</a> for instructions on increasing your Wordpress upload limit.</h3>
					</div>
				</div>

			<?php endif; ?>
			
			<?php
		}
	}


}
