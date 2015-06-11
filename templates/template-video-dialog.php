<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>

<div id="single-video-posts-edit-dialog-different" title="<?php esc_attr_e( 'Notice', 'playwire' ); ?>">
<p id="single-video-posts-edit-dialog-different-data-element" data-id="<?php echo esc_attr( $post->ID ); ?>" data-nonce="<?php echo esc_attr( wp_create_nonce( 'playwire_update_post_nonce' ) ); ?>"> <?php esc_html_e( 'It appears that this video is out of sync with the one on Playwire. This means that the associated video either cannot be found or has changed since last editing this Video. How would you like to proceed?', 'playwire' );?> </p>
</div>

<div id="single-video-posts-edit-dialog-removed" title="<?php esc_attr_e( 'Warning', 'playwire' ); ?>">
	<p id="single-video-posts-edit-dialog-removed-data-element" data-id="<?php echo esc_attr( $post->ID ); ?>" data-nonce="<?php echo esc_attr( wp_create_nonce( 'playwire_delete_post_nonce' ) ); ?>"> <?php esc_html_e( 'This video has been removed from Playwire. Editing will create a new video on playwire.', 'playwire' );?> </p>
</div>
