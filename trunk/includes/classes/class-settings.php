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

		<div class="updated"><p>
			<?php printf( esc_html__( 'Please configure your Playwire account in %1$s. %2$s' ), '<strong><a href="' . esc_url( $settings_url ) . '">' . esc_html__( 'Settings > Playwire', 'playwire' ) . '</a></strong>', '<a href="' . esc_url( $nonced_url ) . '" style="float: right;">' . esc_html__( 'Hide This Notice', 'playwire' ) . '</a>' ); ?>
		</p></div>

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
		add_menu_page( 'playwire', 'Playwire', 'publish_posts', $this->menu_page, '', 'dashicons-video-alt', 21.123456789 ); //We will use an obscure decimal so that it doesn't override another menu item
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
			__( 'Playwire',       'playwire' ),
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
			__( 'Account Information', 'playwire' ),
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
			$this->token,
			__( 'API Token', 'playwire' ),
			array( $this, 'token_callback' ),
			$this->settings_page,
			$this->setting_section_id
		);

		add_settings_field(
			$this->sync,
			__( 'Sync Videos', 'playwire' ),
			array( $this, 'sync_callback' ),
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
			$token = PlaywireAPIHandler::request_token( sanitize_text_field( $input[ $this->password ] ) );
		}

		// If there's no token from the login info then we will throw a
		// WordPress error message.
		if ( empty( $input[ $this->token ] ) && empty( $token ) ) {
			self::error_with_login();
		}

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

		<p class="description"><?php esc_html_e( 'Connect WordPress to your Playwire account to start sharing your video playlists.', 'playwire' ); ?></p>
		<p class="description"><?php esc_html_e( 'Enter either your login information or an API token to get started.', 'playwire' ); ?></p>

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

		<input type="email" id="<?php echo esc_attr( $playwire->email_address ); ?>" name="<?php echo esc_attr( $playwire->plugin_options_name ); ?>[<?php echo esc_attr( $playwire->email_address ); ?>]" value="<?php echo esc_attr( $value ); ?>" autocomplete="off" /><?php
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

		<input type="password" id="<?php echo esc_attr( $playwire->password ); ?>" name="<?php echo esc_attr( $playwire->plugin_options_name ); ?>[<?php echo esc_attr( $playwire->password ); ?>]" value="<?php echo esc_attr( $value ); ?>" autocomplete="off" />
		<p class="description"><?php esc_html_e( 'Your password will <strong>not</strong> be stored.', 'playwire' ); ?></p>

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

		<input type="text" id="<?php echo esc_attr( $playwire->token ); ?>" name="<?php echo esc_attr( $playwire->plugin_options_name ); ?>[<?php echo esc_attr( $playwire->token ); ?>]" value="<?php echo esc_attr( $token ); ?>" autocomplete="off" <?php disabled( $token ); ?> />
		<p class="description"><?php esc_html_e( 'Your API token will be updated automatically according to your login info.', 'playwire' ); ?></p>

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

		<input type="checkbox" id="<?php echo esc_attr( $playwire->sync ); ?>" name=<?php echo esc_attr( $playwire->plugin_options_name ); ?>[<?php echo esc_attr( $playwire->sync ); ?>]" value="1" <?php checked( $sync, 1 ); ?> />
		<p class="description"><?php esc_html_e(  '<strong>Warning:</strong> This has potential to delete destroy or alter your Playwire Posts as it attempts to sync with the Playwire API',  'playwire'  ); ?></p>
		<?php
	}

}
