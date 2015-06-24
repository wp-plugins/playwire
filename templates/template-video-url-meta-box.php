<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 
	$publisher_id = PlaywirePublisher::get_pub_id();
	$url = PlaywirePublisher::pub_id_videos();
?>

<p class="playwire-upload-placeholder"></p>

<div class="playwire-error animated shake hide-me">
	Your video is larger than <b>20MB</b>. Please select a smaller video or <a href="<?php echo $url ?>" target="_blank">Click Here</a> to upload larger video files from your Playwire.com account or visit our <a href="<?php echo esc_url('http://support.playwire.com') ?>" target="_blank">Support Site</a> for help increasing your Wordpress file upload limit.
</div>

<div class="playwire-upload drag-drop">
	<div id="drag-drop-area" style="position: relative;">
		<div class="drag-drop-inside">
			<p class="drag-drop-info">
				<?php esc_html_e( 'Drop Video File Here', 'playwire' ); ?>
			</p>
			<p>
				<?php esc_html_e( 'or', 'playwire' ); ?>
			</p>
			<p class="drag-drop-buttons">
				<input id="plupload-browse-button" type="button" value="<?php esc_attr_e( 'Select File', 'playwire' ); ?>" class="button" style="position: relative; z-index: 1;">
			</p>
			<input id="playwire_video_url" type="file" name="<?php echo esc_attr( $this->video_post_type_video_url ); ?>" value="" size="25" />
		</div>
	</div>
</div>

<script type="text/javascript">

	var playwire_upload_placeholder = document.getElementsByClassName( 'playwire-upload-placeholder' );
	var input = jQuery('#playwire_video_url');

	// hide publish until the right size video is uploaded
	jQuery('#major-publishing-actions').css('visibility','hidden').append('<div class="publish-blocker">Publishing disabled until you upload a video under 20MB</div>');

	// client side check of video upload size 
	jQuery( '.playwire-upload input[type=file]' ).on( 'change', function() {

		var fileSize = this.files[0].size;

		if (fileSize > 20971520 ) {
			input.replaceWith(input.val('').clone(true));
			jQuery( playwire_upload_placeholder ).addClass('hide-me');
			jQuery('.playwire-error').removeClass('hide-me');
			jQuery('#major-publishing-actions').css('visibility','hidden');
			jQuery('.publish-blocker').fadeIn();

		}else{
			jQuery( playwire_upload_placeholder ).removeClass('hide-me');
			jQuery('.playwire-error').addClass('hide-me');
			//Replace the default fakepath that's given to file upload names for display purposes only

			var swapPath = jQuery( playwire_upload_placeholder ).text( jQuery( this ).val().replace( /C:\\fakepath\\/i, 'Your Video: ' ) );

			jQuery( playwire_upload_placeholder ).append(' was added successfully, click Publish to finish uploading your video.</br>*(Depending on your connection speed, it may take a few seconds)').addClass('green-warning animated pulse');

			// swap publish blocker with original publish block 
			jQuery('.publish-blocker').fadeOut();
			jQuery('#major-publishing-actions').css('visibility','visible');
		}

	});

</script>

