<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 
	$publisher_id = PlaywirePublisher::get_pub_id();
?>
<div id="<?php echo esc_attr( $id ); ?>" style="max-width:<?php echo absint( $current_ratio_width ); ?>px;">

	<script data-config="<?php echo esc_url( '//config.playwire.com/' . rtrim($publisher_id, '/' ) . '/playlists/v2/' . rtrim( $current_playlist, '/' ) . '/zeus.json' ); ?>" data-css="http://cdn.playwire.com/bolt/js/zeus/skins/default.css" src="//cdn.playwire.com/bolt/js/zeus/embed.js" type="text/javascript"></script>

</div>
