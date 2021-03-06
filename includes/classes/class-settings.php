<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
* PlaywireSettings class.
*/
class PlaywireSettings extends Playwire {

	/**
	* Nonce for dismissing the admin notice
	*
	* @var string
	*/
	protected static $nonce_key = 'playwire_dismissal';

	/**
	* User option key for dismissed playwire notices
	*
	* @var string
	*/
	protected static $user_option_key = 'playwire_dismiss_admin_notice';

	/**
	* Setup settings related actions
	*
	* @access public
	* @return void
	*/
	public function __construct() {
		add_action( 'admin_menu',    array( $this, 'add_plugin_page'      ) );
		add_action( 'admin_init',    array( $this, 'add_settings'         ) );
		add_action( 'admin_notices', array( $this, 'admin_notice'         ) );
		add_action( 'admin_init',    array( $this, 'dismiss_admin_notice' ) );
		add_action( 'admin_menu',    array( $this, 'menu_page'            ) );
		add_action( 'updated_option',array( $this, 'options_check'        ) );
		//add_action( 'admin_init',    array( $this, 'ajax_form'            ) );
	}

	/**
	* admin_notice function.
	*
	* @access public
	* @return void
	*/
	public function ajax_form() {
	   if ( is_admin() ){ // for Admin Dashboard Only
	      // Embed the Script on our Plugin's Option Page Only
	      if ( isset($_GET['page']) && $_GET['page'] == 'playwire-settings' ) {
	      	echo "here";
	      	wp_enqueue_script('jquery');
	        wp_enqueue_script( 'jquery-form' );
	      }
	   }
	}


	/**
	* admin_notice function.
	*
	* @access public
	* @return void
	*/
	public function options_check($option) {

		if($option == $this->plugin_options_name){
			$my_options = get_option($this->plugin_options_name, true);
			// print_r($my_options);
		}

	}

	/**
	* admin_notice function.
	*
	* @access public
	* @return void
	*/
	public function admin_notice() {
		global $current_user ;

		// Bail if already dismissed notice
		if ( get_user_option( $current_user->ID, self::$user_option_key ) ) {
			return;
		}

		// Get playwire
		$playwire = playwire();

		// Bail if token already exists
		$token = isset( $playwire->options[ $playwire->token ] ) ? $playwire->options[ $playwire->token ] : null;
		if ( null !== $token ) {
			return;
		}

		// Bail if already on the current screen
		if ( get_current_screen()->id === 'settings_page_playwire-settings' ) {
			return;
		}

		// Create the nonced URL
		$nonced_url   = wp_nonce_url( add_query_arg( self::$user_option_key, '1' ), self::$nonce_key );
		$settings_url = add_query_arg( 'page', 'playwire-settings', admin_url( 'options-general.php' ) ); ?>

		<div class="playwire-error">
			<h3 class="error-styles">
			<?php printf( esc_html__( 'Please connect your Playwire.com account to Wordpress by clicking here: %1$s. %2$s' ), '<strong><a href="' . esc_url( $settings_url ) . '">' . esc_html__( 'Settings > Playwire', 'playwire' ) . '</a></strong>', '<a href="' . esc_url( $nonced_url ) . '" style="float: right;">' . esc_html__( 'Hide This Notice', 'playwire' ) . '</a>' ); ?>
			</h3>
		</div>

		<?php
	}

	/**
	* dismiss_admin_notice function.
	*
	* @access public
	* @return void
	*/
	public function dismiss_admin_notice() {
		global $current_user;

		if ( current_user_can( 'manage_options' )  )  {
			// Bail if not trying to dismiss the playwire admin notice
			if ( ! isset( $_GET[ self::$user_option_key ] ) ) {
				return;
			}

			// Bail if not nonced
			check_admin_referer( self::$nonce_key );

			// Add user option
			update_user_option( $current_user->ID, self::$user_option_key, 'dismissed' );
		}
	}

	/**
	* menu_page function.
	*
	* @access public
	* @return void
	*/
	public function menu_page() {

		global $wp_version;

		if (version_compare($wp_version, '3.8', '>=')) {
			$menu_icon = 'dashicons-video-alt';
		}

		if (version_compare($wp_version, '3.8', '<')){
			$menu_icon = '';
		}

		add_menu_page( 'playwire', 'Playwire Video', 'publish_posts', $this->menu_page, '', $menu_icon, 21.123456789 ); //We will use an obscure decimal so that it doesn't override another menu item

		add_submenu_page( 'playwire', 'Playwire Settings', 'Playwire Settings', 'manage_options', 'options-general.php?page=playwire-settings');
	}

	/**
	* Add the plugin settings page
	*
	* @access public
	* @return void
	*/
	public function add_plugin_page() {
		add_options_page(
			__( 'Settings Admin', 'playwire' ),
			__( 'Playwire Settings',       'playwire' ),
			'manage_options',
			$this->settings_page,
			array( $this, 'settings_page' )
		);

	}

	/**
	* Add settings sections and fields
	*
	* @access public
	* @return void
	*/
	public function add_settings() {

		register_setting(
			$this->option_group,
			$this->plugin_options_name,
			array( $this, 'sanitize' )
		);

		add_settings_section(
			$this->setting_section_id,
			__( 'Connect WordPress to your Playwire account to start sharing your videos and playlists.', 'playwire' ),
			array( $this, 'account_callback' ),
			$this->settings_page
		);

		add_settings_field(
			$this->email_address,
			__( 'Email Address', 'playwire' ),
			array( $this, 'email_address_callback' ),
			$this->settings_page,
			$this->setting_section_id
		);

		add_settings_field(
			$this->password,
			__( 'Password', 'playwire' ),
			array( $this, 'password_callback' ),
			$this->settings_page,
			$this->setting_section_id
		);

		add_settings_field(
			$this->sync,
			__( '', 'playwire' ),
			array( $this, 'sync_callback' ),
			$this->settings_page,
			$this->setting_section_id
		);

		add_settings_field(
			$this->token,
			__( '', 'playwire' ),
			array( $this, 'token_callback' ),
			$this->settings_page,
			$this->setting_section_id
		);

	}

	/**
	* Trigger an error if Playwire API login is unsuccessful
	*
	* @access public
	* @return void
	*/
	public function error_with_login() {
		add_settings_error(
			$this->password,
			'settings_updated',
			__( 'Your login info appears to be incorrect. Please try again.', 'playwire' ),
			'error'
		);
	}

	/**
	* Output the settings page
	*
	* @access public
	* @return void
	*/
	public function settings_page() {

		include( PLAYWIRE_PATH . 'templates/template-form-settings.php' );
	}

	/**
	* sanitize function.
	*
	* @access public
	* @param mixed $input
	* @return void
	*/
	public function sanitize( $input = array() ) {

		$new_input = array();

		// Check if the email input is actually an email address.
		if ( is_email( $input[ $this->email_address ] ) ) {
			$new_input[ $this->email_address ] = sanitize_email( $input[ $this->email_address ] );
		}

		// Request a token from Playwire with the credentials entered.
		if ( isset( $input[ $this->password ] ) ) {
			$new_email = $new_input[ $this->email_address ];
			$token = PlaywireAPIHandler::request_token( sanitize_text_field( $input[ $this->password ] ), $new_email );
		}

		// Moved error messaging to PlaywirePublisher class
		// If there's no token from the login info then we will throw a
		// WordPress error message.
		// if ( empty( $input[ $this->token ] ) && empty( $token ) ) {
		// 	self::error_with_login();
		// }

		if ( isset( $input[ $this->token ] ) ) {
			$token                     = ( ! empty( $token ) ? $token : $input[ $this->token ] );
			$new_input[ $this->token ] = sanitize_text_field( $token );
		}

		if ( isset( $input[ $this->sync ] ) ) {
			$new_input[ $this->sync ] = absint( $input[ $this->sync ] );
		}

		// Finally return the input array
		return $new_input;
	}


	/**
	* Output header area helper text
	*
	* @access public
	* @return void
	*/
	public function account_callback() {

	?>
		<a class="help-click">OPEN HELP SECTION</a>
		<p>Click the button above for step-by-step instructional videos to help you use the Playwire Wordpress Plugin</p>

		<div id="help-section">

			<div class="row-holder">
			<div class='success-instructions'>
			<h3>View and Upload Videos</h3>
			<h4>*PLEASE NOTE - <em>You can upload videos from Playwire.com <strong>and</strong> from this Plugin.</em></h4><h4>If you have already have videos on Playwire, all your videos should be synced here already: <a href='<?php echo admin_url('/edit.php?post_type=playwire_videos'); ?>'>Playwire Video &gt;&nbsp;Videos</a></h4><h4>If you have no videos on Playwire, watch the tutorial video to learn how to upload your videos to Playwire.com from the Playwire Video Wordpress Plugin.</h4>
			</div>

			<div class='success-video'>
			<script data-id="help_1" data-config='//config.playwire.com/1006036/videos/v2/3670323/zeus.json' data-css='//cdn.playwire.com/bolt/js/zeus/skins/default.css' data-height='100%' data-width='100%' src='//cdn.playwire.com/bolt/js/zeus/embed.js' data-autoload="false" type='text/javascript'></script>
			</div>

			</div>

			<hr class='success-hr'>
			
			<div class="row-holder">
			<div class='success-instructions'>
			<h3>Create Video Galleries from Playlists on Playwire</h3>
			<h4>*PLEASE NOTE* - <em>Playwire Playlists are <strong>NOT</strong> the same as Video Galleries you create with this Plugin</em></h4><h4>You can easily create custom Video Galleries to embed in your pages with this plugin. Watch the tutorial video to learn how create Video Galleries from your Playwire Playlists.</h4><h4>After watching the tutorial video, <a href='<?php echo admin_url('/edit.php?post_type=playwire_playlists'); ?>'>click here</a> to create your first Video Gallery.</h4>
			</div>

			<div class='success-video'>
			<script data-id="help_2" data-config='//config.playwire.com/1006036/videos/v2/3670306/zeus.json' data-css='//cdn.playwire.com/bolt/js/zeus/skins/default.css' data-height='100%' data-width='100%' src='//cdn.playwire.com/bolt/js/zeus/embed.js' data-autoload="false" type='text/javascript'></script>
			</div>

			</div>

			<hr class='success-hr'>

			<div class="row-holder">
			<div class='success-instructions'>
			<h3>Embedding Videos and Video Galleries in Your Page or Post</h3>
			<h4>You can easily embed Videos and Video Galleries in your Wordpress Pages and Posts.</h4>
			<h4>After uploading videos to Playwire and creating Video Galleries, you are now able to embed your Videos and Video Gallery. Watch the tutorial video to learn how.</h4>
			</div>

			<div class='success-video'>

			<script data-id="help_3" data-config='//config.playwire.com/1006036/videos/v2/3670274/zeus.json' data-css='//cdn.playwire.com/bolt/js/zeus/skins/default.css' data-height='100%' data-width='100%' src='//cdn.playwire.com/bolt/js/zeus/embed.js' data-autoload="false" type='text/javascript'></script>
			</div>

			</div>

			<hr class='success-hr'>
		</div>

		<img class="metabox-image" src="<?php echo PLAYWIRE_URL . "assets/banner-772x250.png";?>" />

		<div class="social-holder">

			<a class="no-ul" href="https://www.facebook.com/Playwire?fref=ts">
				<span class="big dashicons dashicons-facebook-alt"></span>
			</a>

			<a class="no-ul" href="https://twitter.com/playwire">
				<span class="big dashicons dashicons-twitter"></span>
			</a>

			<a class="no-ul" href="https://plus.google.com/110280384583795584987/about">
				<span class="big dashicons dashicons-googleplus"></span>
			</a>

		</div>

		<p class="description"><?php esc_html_e( 'Enter the same login information you use to login into your Playwire.com account', 'playwire' ); ?></p>

	<?php
	}

	/**
	* Output the email address field
	*
	* @access public
	* @return void
	*/
	public function email_address_callback() {

		// Get playwire
		$playwire = playwire();

		// Get the value
		$value = isset( $playwire->options[ $playwire->email_address ] ) ? $playwire->options[ $playwire->email_address ] : null; ?>

		<input type="email" size="80" id="<?php echo esc_attr( $playwire->email_address ); ?>" name="<?php echo esc_attr( $playwire->plugin_options_name ); ?>[<?php echo esc_attr( $playwire->email_address ); ?>]" value="<?php echo esc_attr( $value ); ?>" autocomplete="off" /><?php
	}

	/**
	* Output the password field
	*
	* @access public
	* @return void
	*/
	public function password_callback() {

		// Get playwire
		$playwire = playwire();

		// Get the value
		$value = isset( $playwire->options[ $playwire->password ] ) ? $playwire->options[ $playwire->password ] : null; ?>

		<input type="password" size="80" id="<?php echo esc_attr( $playwire->password ); ?>" name="<?php echo esc_attr( $playwire->plugin_options_name ); ?>[<?php echo esc_attr( $playwire->password ); ?>]" value="<?php echo esc_attr( $value ); ?>" autocomplete="off" />
		<p class="description">Your password will <strong>not</strong> be stored.</p>

		<?php
	}

	/**
	* Output the token field
	*
	* @access public
	* @return void
	*/
	public function token_callback() {

		// Get playwire
		$playwire = playwire();

		// Get the value
		$token = isset( $playwire->options[ $playwire->token ] ) ? $playwire->options[ $playwire->token ] : null; ?>

		<input type="text" class="no-select" size="80" id="<?php echo esc_attr( $playwire->token ); ?>" name="<?php echo esc_attr( $playwire->plugin_options_name ); ?>[<?php echo esc_attr( $playwire->token ); ?>]" value="<?php echo esc_attr( $token ); ?>" autocomplete="off" <?php disabled( $token ); ?> />

		<?php
	}

	/**
	* Output the sync checkbox
	*
	* @access public
	* @return void
	*/
	public function sync_callback() {

		// Get playwire
		$playwire = playwire();

		$sync= isset( $playwire->options[$playwire->sync] ) ? $playwire->options[$playwire->sync] : null; ?>

		<input type="checkbox" class="no-select" id="<?php echo esc_attr( $playwire->sync ); ?>" name=<?php echo esc_attr( $playwire->plugin_options_name ); ?>[<?php echo esc_attr( $playwire->sync ); ?>] value="1" <?php checked( $sync, 1 ); ?> checked/>

		<?php
	}

}
