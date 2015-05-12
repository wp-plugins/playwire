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
					<h3><span class="error-text"><span class="dashicons dashicons-flag"></span> NO VIDEOS TO EMBED</span></h3>
						<h3 class="error-styles">You must have at least one video on your Playwire.com account before using the plugin. </br><a href="<?php echo $url ?>" target="_blank">Click here</a> to upload video files from Playwire.com account or visit our <a href="<?php echo esc_url('http://support.playwire.com') ?>" target="_blank">Support Site</a> for help.</h3>
				</div>
			</div>

		<?php else : ?>

			<?php while ( $query->have_posts() ) : $query->the_post(); ?>
				<?php
					$video           = get_post_meta( $query->post->ID, $this->video_meta_name, true );
					$video_thumbnail = ( isset( $video['thumbnail']['160x120'] ) ? $video['thumbnail']['160x120'] : '//placehold.it/160x120/' . strtoupper(  dechex(  rand(  0,  10000000  )  )  ) . '/ffffff&amp;text=No&nbsp;Thumbnail' );
					$video           = ( isset( $video['id'] ) ? $video['id'] : '' );
	?>
					<li class="video"
						data-video-name="<?php echo esc_attr( $video['name'] ); ?>"
						data-video-id="<?php echo esc_attr( $video ); ?>"
						data-video-thumbnail="<?php echo esc_attr( $video_thumbnail ); ?>"
						data-video-post-id="<?php echo esc_attr( $query->post->ID ); ?>"
						data-video-name="<?php echo esc_attr( $query->post->post_title ); ?>">
							<img src="<?php echo esc_url( $video_thumbnail ); ?>">
							<div class="<?php echo esc_attr( $this->video_title ); ?>"><?php echo ( get_the_title() ? get_the_title() : 'Untitled' ); ?></div>
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

