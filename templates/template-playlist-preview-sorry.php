<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 

	global $pagenow;
	$url = PlaywirePublisher::pub_id_playlists();

?>

<?php if ( $pagenow == "edit.php" || $pagenow == "post.php" || $pagenow == "post-new.php" ) : ?>

	<div class="playlist-error animated shake">
		<span class="dashicons dashicons-flag"></span><?php esc_html_e( 'You must create playlists on Playwire.com account before you can create Video Galleries', 'playwire' ); ?></br></br>&roarr;
		<a href="<?php echo $url ?>" target="_blank">Click here to Create Playlists on Playwire</a>
	</div>		

<?php endif; ?>