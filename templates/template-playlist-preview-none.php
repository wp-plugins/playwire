<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 
	global $pagenow;
	$url = PlaywirePublisher::pub_id_playlists();
?>

<?php if ( $pagenow == "edit.php" || $pagenow == "post.php" || 'post-new.php' == $pagenow ) : 

	$has_playlists = PlaywirePublisher::get_playlists();

	if ( $has_playlists ) : ?>

		<div class="playwire-warning">
			<p><?php esc_html_e( 'Preview unavailable until you select a playlist in the dropdown above', 'playwire' ); ?></p>
		</div>

	<?php else: ?>

		<div class="playwire-error">
			<h3 class="error-styles"><span class="dashicons dashicons-flag redski"></span><?php esc_html_e( 'You must create playlists on Playwire.com account before you can create Video Galleries', 'playwire' ); ?></br></br>&roarr;
			<a href="<?php echo $url ?>" target="_blank">Click here to Create Playlists on Playwire</a></h3>
		</div>

	<?php endif; ?>

<?php endif; ?>
