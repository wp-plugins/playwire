<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>

<div class="wrap">

	<form method="post" id="settings-form" action="<?php echo esc_url ( admin_url( 'options.php' ) ) ?>" autocomplete="off">
		<h2><?php esc_html_e( 'Playwire Settings', 'playwire' ); ?></h2>
		<?php settings_fields( playwire()->option_group ); ?>
		<?php do_settings_sections( playwire()->settings_page ); ?>
		<?php submit_button(); ?>
	</form>

	<div id="saveResult"></div>

	<script type="text/javascript">
	jQuery(document).ready(function() {
	   jQuery('#submit').click(function() { 
		jQuery('#wpbody-content').html("<div class='loading-jawn-big'>Please Wait, Syncing Account with Playwire ...</div>");
	   
	   });
	});
	</script>

</div>
