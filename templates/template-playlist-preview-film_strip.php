<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly  ?>

<div id="<?php echo esc_attr( $id ); ?>_container" style="max-width:<?php echo absint( $current_ratio_width ); ?>px;">

	<div id="<?php echo esc_attr( $id ); ?>" class="flexslider">

		<ul class="slides">

			<?php foreach ( $playlist['videos'] as $key => $value ) : ?>

				<li>

					<script data-config="<?php echo esc_url( '//phoenix.playwire.com/videos/' . rtrim( $value['id'], '/' ) . '/new_player.json' ); ?>" data-height="<?php echo absint( $current_ratio_height ); ?>" data-width="<?php echo absint( $current_ratio_width ); ?>" src="//cdn.playwire.com/bolt/js/embed.min.js" type="text/javascript"></script>

				</li>

			<?php endforeach; ?>

		</ul>

	</div>

	<div id="<?php echo esc_attr( $id ); ?>_control" class="flexslider flexslider-control">

		<ul class="slides">
			<?php $slide_max = 0; ?>
			<?php foreach ( $playlist['videos'] as $key => $value ) : ?>

				<li>

					<?php include PLAYWIRE_PATH . 'templates/play.svg'; ?>

					<img src="<?php echo esc_url( $value['thumbnail']['320x240'] ); ?>" width="320" height="240" onError="this.onerror=null;this.src='<?php echo esc_url( '//placehold.it/320x240/' . strtoupper( dechex( rand( 0, 10000000 ) ) ) . '/ffffff&amp;text=No&nbsp;Thumb' ); ?>';"/>


				</li>
				<?php $slide_max++; ?>
			<?php endforeach; ?>

		</ul>

	</div>

</div>

<script type="text/javascript">
jQuery( document ).ready(function( $ ) {

	var ratioWidth = <?php echo esc_js( ( int ) $current_ratio_width / 4.2 ); ?>

	$( "#<?php echo esc_js( $id ); ?>_control" ).flexslider( {
		animation: "slide",
		animationLoop: false,
		slideshow: false,
		itemWidth: ratioWidth,
		itemMargin: 5,
		maxItems: <?php echo esc_js( $slide_max ); ?>,
		asNavFor: '#<?php echo esc_js( $id ); ?>'
	} );

	$( "#<?php echo esc_js( $id ); ?>" ).flexslider( {
		animation: "slide",
		useCSS: false, /*Fixes potential problem with some webkit browsers for video players*/
		controlNav: false,
		animationLoop: false,
		slideshow: false,
		sync: "#<?php echo esc_js( $id ); ?>_control",
		before: function( slider ) {
			/*Pause the bolt player*/
		}
	} );

	$( '.flexslider-control .slides li, .flexslider-control .slides li img' ).each( function() {
		var ratio       = '<?php echo (string) esc_js( $current_ratio ); ?>';
		var thumbRatio  = ( ratio == "widescreen" ) ? 1.777 : 1.333;
		var slideHeight =  ratioWidth / thumbRatio;
		$( this ).css( 'height', slideHeight );
	} );

} );
</script>
