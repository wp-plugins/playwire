<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 
	global $pagenow;
	$publisher_id = PlaywirePublisher::get_pub_id();
?>

<?php if ( $pagenow == "edit.php" || $pagenow == "post.php" || $pagenow == "post-new.php" ) : ?>

	<?php if ( $video_id ) : ?>

		<p class="green-warning btm-20">Check back shortly if your video is not loading in the preview area below, it might still be encoding on Playwire.com. If you are not seeing a preview there might be an error, try to <a href="/wp-admin/post-new.php?post_type=playwire_videos">upload a new video</a> or visit our <a href="//support.playwire.com">Support Site</a> for help uploading videos.</p>

		<script data-config="<?php echo esc_url( '//config.playwire.com/' . rtrim($publisher_id, '/' ) . '/videos/v2/' . rtrim( $video_id, '/' ) . '/zeus.json' ); ?>" data-css="http://cdn.playwire.com/bolt/js/zeus/skins/default.css" src="//cdn.playwire.com/bolt/js/zeus/embed.js" type="text/javascript"></script>

	<?php else: ?>

			<div class="playwire-warning warning-text">
				<span class="dashicons dashicons-flag warning-text"></span>
				<?php esc_html_e( 'Preview unavailable until you finish uploading a video', 'playwire' ); ?></br></br>
					<a href="http://support.playwire.com/article/wordpress-plugin/" target="_blank"> &Rightarrow; Click here for help uploading videos</a>
			</div>

	<?php endif; ?>

<?php endif; ?>