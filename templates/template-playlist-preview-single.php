<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<div id="<?php echo esc_attr( $id ); ?>" style="max-width:<?php echo absint( $current_ratio_width ); ?>px;">

    <script data-config="<?php echo esc_url( '//phoenix.playwire.com/videos/' . rtrim( $current_single_video, '/' ) . '/new_player.json' ); ?>" data-height="<?php echo absint( $current_ratio_height ); ?>" data-width="<?php echo absint( $current_ratio_width ); ?>" src="//cdn.playwire.com/bolt/js/embed.min.js" type="text/javascript"></script>

</div>
