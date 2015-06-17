<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>

<input id="<?php echo esc_attr( $this->video_post_type_intend_to_share ); ?>" type="checkbox" name="<?php echo esc_attr( $this->video_post_type_intend_to_share ); ?>" value="1"  <?php checked( true, $intend_to_share, true ); ?> />

<label for="<?php echo esc_attr( $this->video_post_type_intend_to_share ); ?>"><?php esc_html_e( 'Allow your video to be shared in the Playwire Sandbox', 'playwire' ) ;?></label>
