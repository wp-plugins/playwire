<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>

<?php if ( $video_id ) : ?>
    <script data-config="<?php echo esc_url( '//phoenix.playwire.com/videos/' . rtrim( $video_id, '/' ) . '/new_player.json' ); ?>" data-height="480" data-width="640" src="//cdn.playwire.com/bolt/js/embed.min.js" type="text/javascript"></script>

<?php else: ?>
	<p><?php esc_html_e( 'No preview available', 'playwire' ); ?></p>
<?php endif; ?>
