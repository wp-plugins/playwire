<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
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
			<p class="playwire-upload-placeholder"></p>
		</div>
	</div>
</div>
