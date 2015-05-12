<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 
	$publisher_id = PlaywirePublisher::get_pub_id();
?>

<div id="<?php echo esc_attr( $id ); ?>">	<?php $count = 0; ?>
	<?php foreach ( $playlist['videos'] as $key => $value ) : ?>
			<?php if ( 0 == $count % 3 ) : ?>
				<div class="clear"></div>
			<?php endif; ?>
			<div id="<?php echo esc_attr( $value['id'] ); ?>" style="display:none">

				<script data-config="<?php echo esc_url( '//config.playwire.com/' . rtrim($publisher_id, '/' ) . '/videos/v2/' . rtrim( $value['id'], '/' ) . '/zeus.json' ); ?>" data-height="<?php echo absint( $current_ratio_height ); ?>" data-width="<?php echo absint( $current_ratio_width ); ?>" data-css="//cdn.playwire.com/bolt/js/zeus/skins/default.css" src="//cdn.playwire.com/bolt/js/zeus/embed.js" type="text/javascript"></script>
			</div>
			<a class="thickbox" title="<?php echo esc_attr( $value['name'] ); ?>" href="#TB_inline?height=<?php echo absint( $current_ratio_height ); ?>&amp;width=<?php echo absint( $current_ratio_width ); ?>&amp;inlineId=<?php echo esc_attr( $value['id'] ); ?>">
				<div class="vertical-video-alignment">
					<?php include PLAYWIRE_PATH . 'templates/play.svg'; ?>
						<?php if ( $current_ratio == 'widescreen' ) : ?>
							<img src="<?php echo esc_url( $value['thumbnail']['320x240'] ); ?>" width="320" height="240" onError="this.onerror=null;this.src='<?php echo esc_url(  '//placehold.it/320x240/' . strtoupper(  dechex(  rand(  0,  10000000  )  )  ) . '/ffffff&amp;text=No&nbsp;Thumb'  ); ?>';"/>
						<?php else: ?>
							<img src="<?php echo esc_url( $value['thumbnail']['96x96'] ); ?>" width="96" height="96" onError="this.onerror=null;this.src='<?php echo esc_url(  '//placehold.it/96x96/' . strtoupper(  dechex(  rand(  0,  10000000  )  )  ) . '/ffffff&amp;text=No&nbsp;Thumb'  ); ?>';"/>
						<?php endif; ?>
				</div>
				<h3><?php echo esc_html( $value['name'] ); ?></h3>
			</a>
			<?php $count++; ?>
	<?php endforeach; ?>
</div>
