<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>

<div id="<?php echo esc_attr( $this->library_id ); ?>">

	<input type="search" id="<?php echo esc_attr( $this->search_input ); ?>" placeholder="<?php esc_attr_e( 'Type to filter', 'playwire' ); ?>">

	<ul id="<?php echo esc_attr( $this->search_list ); ?>">

		<?php if (!$query->have_posts() ) : 

			$has_playlists = PlaywirePublisher::get_playlists();
			$url = PlaywirePublisher::pub_id_playlists();

		?>

			<div class="error-container">
				<div class="playwire-error animated shake">
					<h3><span class="error-text"><span class="dashicons dashicons-flag"></span> NO PLAYLISTS AVAILABLE</span></h3>
					<h3 class="error-styles">You <u><strong>MUST</strong></u> create playlists on your Playwire.com account before you are able to create and embed Video Galleries</h3>

					<div class="half">
						<h3 class="error-styles">To get started,&nbsp;<a href="<?php echo $url ?>" target="_blank">Create Playlists on Playwire&nbsp;</a></h3>
						</h3>
					</div>

					<div class="half-r">
						<h3 class="error-styles">or visit our Support Site for&nbsp;<a href="http://support.playwire.com" target="_blank">Help Creating Playlists</a></h3>
					</div>
				</div>
			</div>

		<?php else : ?>

		<? while ( $query->have_posts() ) : $query->the_post(); ?>

			<?php $data = PlaywirePlayerPostHandler::setup_playlist_template( $query->post->ID, true ); ?>
				<li class="playlist"
					data-playlist-name="<?php echo esc_attr( $data['playlist']['name'] ); ?>"
					data-playlist-id="<?php echo esc_attr( $data['playlist']['id'] ); ?>"
					data-playlist-thumbnail="<?php echo esc_attr( $data['playlist']['thumbnail']['160x120'] ); ?>"
					data-playlist-post-id="<?php echo esc_attr( $query->post->ID ); ?>"
					data-playlist-name="<?php echo esc_attr( $query->post->post_title ); ?>">

					<?php if ( has_post_thumbnail( $query->post->ID ) ) : ?>
						<?php echo get_the_post_thumbnail( $query->post->ID, 'playwire-thumb' ); ?>
					<?php else : ?>
						<img alt="Ffffff&amp;text=<?php echo esc_attr( ( isset( $data['playlist']['name'] ) ? $data['playlist']['name'] : get_the_title() ) ); ?>" src="<?php echo esc_url( '//placehold.it/160x120/' . strtoupper( dechex( rand( 0, 10000000 ) ) ) . '/ffffff&amp;text=' . esc_attr( ( isset( $data['playlist']['name'] ) ? $data['playlist']['name'] : get_the_title() )) ); ?>" onError="this.onerror=null;this.src='<?php echo esc_url(  '//placehold.it/160x120/' . strtoupper(  dechex(  rand(  0,  10000000  )  )  ) . '/ffffff&amp;text=No&nbsp;Thumb'  ); ?>';"/>
					<?php endif; ?>
						<div class="<?php echo esc_attr( $this->playlist_title ); ?>"><?php echo ( get_the_title() ? get_the_title() : 'Untitled' ); ?></div>
				</li>

		<?php endwhile; ?>

		<?php wp_reset_postdata(); ?>

		<?php endif; ?>

	</ul>

	<?php if ( $page_links ) : ?>
		<div class="tablenav">
			<div class="tablenav-pages">
				<?php echo $page_links; ?>
			</div>
		</div>
	<?php endif; ?>

</div>

