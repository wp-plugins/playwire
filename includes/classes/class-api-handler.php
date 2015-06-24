<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
* PlaywireAPIHandler class.
*
* If interfacing with the Playwire API then use this class to create very abstract methods
*
*/
class PlaywireAPIHandler extends Playwire {


	/**
	* Authenticate token (after options are loaded)
	*
	* @access private
	* @return void
	*/
	public function __construct() {
		add_action( 'admin_post_update_videos_post_data',        array( __CLASS__, 'update_videos_post_data'   ) );
	}


	/**
	* We want to update the data from the Playwire API without making too many
	* calls to their API. We will trigger the update on save_post
	* @access public
	* @static
	* @return void
	*/
	public static function update_api_data( $post_id ) {
		$playwire = playwire();
		$post_type = ( isset( $_POST['post_type'] ) ? $_POST['post_type'] : null ) ;
		if ( $playwire->playlists_post_type != $post_type ) {
			return;
		}
		PlaywireCrons::cron();
	}


	/**
	* Updates the Playwire options attach this function to a cron, hook, or
	* whatever to trigger.
	*
	* @access public
	* @param mixed $url
	* @param mixed $option_name
	* @return void
	*/
	public static function update_options( $url, $option_name ) {

		// Modify the headers with our token if it exists as a string not an
		// array
		$headers = self::add_token_to_headers( false );

		// Perform the request for the playlist
		//
		// Since an object is returned we are going to decode and then encode
		// the object so it can be prepared for saving as an option.
		$request = json_decode( json_encode( self::request( $url, $headers ) ), true );

		// Check if the request was sucessfully converted into an array
		if ( is_array( $request ) ) {
			update_option( $option_name, $request );
		}
	}


	/**
	* A general function to be used in all standard GET requests with the Playwire API.
	*
	* @access public
	* @param mixed $url
	* @return void
	*/
	public static function request( $url, $headers = array(), $post_data = array(), $method = 'GET' ) {
		if ( ! empty( $headers ) || ! empty( $post_data ) ) {

			// Set the arrguments for our login to Playwire these values are
			// taken from the main plugin settings.
			$args = array(
				'method'      => $method,
				'httpversion' => '1.0',
				'compress'    => true,
				'timeout'     => 22,
				'redirection' => 5,
				'body'        => $post_data,
				'headers'     => $headers,
			);

			//print_r($args);

			switch ( $method ) {
				case "GET":
					$response = wp_remote_request( esc_url_raw( $url ), $args );
					break;
				case "POST":
					$response = wp_remote_post( esc_url_raw( $url ), $args );
				break;
				case "PUT":
					$response = wp_remote_post( esc_url_raw( $url ), $args );
				break;
				case "DELETE":
					$response = wp_remote_post( esc_url_raw( $url ), $args );
				break;
			}
			// If no error in the request response we will continue and try to decode the response
			if ( ! is_wp_error( $response ) ) {
				$response = json_decode( wp_remote_retrieve_body( $response ) );
				return $response;
			}
		}

		// If no headers return false
		return false;
	}


	/**
	* Checks if a token exists in the plugin options and pushes it into the
	* headers array for authentication.
	*
	* Unless specified as a string it will return only the token string without
	* the supplemental headers.
	*
	* @access public
	* @return void
	*/
	public static function add_token_to_headers( $array = false ) {

		// Get the plugin options array from wp_options table.
		$options = playwire()->options;

		// Check if a user token already exists in the wp_options table.
		$token = self::check_user_token( $options );

		// Check if the token and the array are true
		if ( $token && $array ) {

			array_push( playwire()->headers, "Intergi-Access-Token:{$options[ playwire()->token ]}" );

			return playwire()->headers;

		} elseif ( $token ) {
			return "Intergi-Access-Token:{$options[ playwire()->token ]}";

		} else {
			return false;
		}
	}


	/**
	* This will make the POST request for the playwire_token from the
	* playwire API and update the token option automatically if it
	* doesn't exist or is outdated. The token will be used in all
	* requests thereafter so the users credentials must be valid.
	*
	* @access public
	* @return void
	*/
	public static function request_token( $password = '', $new_email ) {

		$playwire = playwire();

		// Adds the login API endpoint for Playwire
		$url      = $playwire->api_endpoint . '/users/login';

		// Ternary check from main plugin options for the users email address
		$login    = ( isset( $playwire->options[ $playwire->email_address ] ) ? $playwire->options[ $playwire->email_address ] : $new_email );

		// Set the arguments for our login to Playwire these values are taken
		// from the main plugin settings
		$args = array(
			'method'      => 'POST',
			'timeout'     => 22,
			'redirection' => 5,
			'httpversion' => '1.0',
			'headers'     => $playwire->headers,
			'body'        => array(
			'login'       => $login,
			'password'    => $password
			)
		);
		//print_r($args);
		// Remote post to the playwire API our URL and arguments for login
		$response = wp_remote_post( esc_url_raw( $url ), $args );

		// If no error in the POST response we will continue and try to decode
		// the response.
		if ( ! is_wp_error( $response ) ) {

			try {

				$json = json_decode( $response['body'], true ); //wp_remote_retrieve_body doesn't seem to like it here


			} catch ( Exception $ex ) {


				$json = null;

			}

			// Check if a user token already exists in the wp_options table.
			$token = self::check_user_token( $playwire->options );

			// Ternary check to see if the json is included in the array.
			$json_token = ( isset( $json['token'] ) ? $json['token'] : null );

			// If the playwire_token already exists and is != to the new
			// playwire_token make a new one.
			if ( $token != $json_token && isset( $json_token ) ) {

				return $json['token'];

			}

		}

	}


	/**
	* Request the token option if it exists in the plugins options array.
	*
	* @access public
	* @return void
	*/
	public static function check_user_token( $options ) {

		$playwire = playwire();

		if ( is_array( $options ) && array_key_exists( $playwire->token, $options ) ) {
			return $options[ $playwire->token ];
		}

		return false;
	}


	/**
	* get_videos function.
	*
	* Returns a json object of the videos for this user
	*
	* @access public
	* @static
	* @return void
	*/
	public static function get_videos() {
		$playwire = playwire();
		//Get the api key
		$token = self::check_user_token( $playwire->options );
		//Add the key to the headers
		//Set the headers with the token
		$headers = self::add_token_to_headers( false );
		//Send the request
		$request = self::request( $playwire->api_endpoint . '/videos', $headers );

		return $request;
	}


	/**
	* update_video_post_data function.
	*
	* @access public
	* @static
	* @param mixed $post_data
	* @return void
	*/
	public static function update_videos_post_data( $post_data ) {
		if ( ! wp_verify_nonce( $_REQUEST['_wpnonce'], $playwire->update_video_post_data_nonce ) ) {
			die( __( 'Nonce did not verify', 'playwire' ) );
		} else {
			//Check for post_data
			if ( is_array( $post_data ) ) {
				//If post data array key exists in default array then update the value only if
				foreach ( $post_data as $key => $value ) {
					if ( array_key_exists( $key, playwire()->post_videos_arr ) ) {
						$post_data[$key] = $value;
					} else {
						//This will unset any post variables that aren't accepted by the API
						unset( $post_data[$key] );
					}
				}
				//Update
				if ( $post_data['video_method'] === 'update' ) {
					$post_request = self::request( $playwire->api_endpoint . '/videos', self::add_token_to_headers( true ), $post_data, 'PUT' );
				} elseif ( $post_data['video_method'] === 'create' ) {
					$post_request = self::request( $playwire->api_endpoint . '/videos', self::add_token_to_headers( true ), $post_data, 'POST' );
				}
				//If above gives a good response then return true or false
				if ( $post_request ) {
					return true;
				} else {
					return false;
				}

			} else {
				return false;
			}
		}
	}


	/**
	* handle_api_images
	*
	* @param mixed $url
	* @param mixed $post_id
	* @static
	* @access public
	* @return void
	*/
	public static function handle_api_images( $url, $post_id ) {
		$playwire = playwire();
		if ( ! $post_id ) {
			return false;
		}
		//Require files to make functions available to handle media uploads `media_handle_upload()` and `download_url()`
		require_once( ABSPATH . 'wp-admin' . '/includes/image.php' );
		require_once( ABSPATH . 'wp-admin' . '/includes/file.php'  );
		require_once( ABSPATH . 'wp-admin' . '/includes/media.php' );

		$meta = get_post_meta( $post_id, $playwire->video_featured_image, true );
		if ( $meta ) {
			$img_id = get_post_meta( $meta, '_wp_attached_file', true );
			$img_id = basename( $img_id );
			if ( $img_id != basename( $url ) ) {
				return false;
			}
		}

		$tmp      = download_url( $url );
		$file_arr =
			array(
				'name' => basename( $url ),
				'tmp_name'=> $tmp
			)
			;
		if (  is_wp_error( $tmp ) ) {
			@unlink( $file_arr['tmp_name'] );
			return $tmp;
		}

		$id = media_handle_sideload( $file_arr, 0 );
		if ( is_wp_error( $id ) ) {
			@unlink( $file_array['tmp_name'] );
			return $id;
		}

		update_post_meta( $post_id, $playwire->video_featured_image, $id );
		return true;

	}


	/**
	* sync_all_videos_from_api function.
	*
	* @access public
	* @return void
	*/
	public static function sync_all_videos_from_api() {
		global $wpdb;
		$playwire         = playwire();
		//print_r(get_option($playwire->videos_option_name, true));
		$playwire->videos = ( ( $playwire->videos ) ? $playwire->videos : get_option($playwire->videos_option_name, true) );
		//print_r($playwire->videos);
		//Get json object
		$videos = $playwire->videos;
		//Loop through the videos API object
		foreach ( $videos as $video ) {
			//Check if the id is in the current array of videos
			if ( ! in_array( $video['id'], $playwire->published_videos, true ) ) {
				//Create a video post

				$args =
					array(
						'post_type'     => $playwire->videos_post_type,
						'post_title'    => sanitize_text_field( $video['name'] ),
						'post_excerpt'  => sanitize_text_field( $video['description'] ),
						'post_status'   => 'publish'
					);
				$post = wp_insert_post( $args );

				if ( is_numeric( $post ) ) {
					//Update the featured image for the video post
					if ( isset( $video['thumbnail'] ) ) {
						self::handle_api_images( $video['thumbnail']['320x240'], $post );
					}
					//Set the term for the main category
					$playwire_term_id = get_term_by( 'name', $video['category']['name'], $playwire->videos_taxonomy );
					wp_set_post_terms( $post, ( int ) $playwire_term_id->term_id, $playwire->videos_taxonomy );
					update_post_meta( $post, $playwire->video_meta_name, $video );
					//The following `post_meta` can only be sent and not received
					update_post_meta( $post, $playwire->video_post_type_video_url, 'synced' );
					array_push( $playwire->published_videos, $video['id'] );
					update_option(  $playwire->published_videos_option_name,  $playwire->published_videos  );
				}

			} else {

				$like      = $wpdb->esc_like( $video['id'] );
				$like      = '%' . $like . '%';
				$meta_name = esc_sql( $playwire->video_meta_name );
				$sql       = "SELECT `post_id` FROM $wpdb->postmeta WHERE meta_key='%s' AND meta_value LIKE '%s' LIMIT 1;";
				$sql       = $wpdb->prepare( $sql, $meta_name, $like );
				$query     = $wpdb->get_var( $sql );

				if ( ! is_numeric( $query ) ) {
					return false;
				}

				unset( $args );
				$args =
					array(
						'ID'           => absint( $query ),
						'post_title'   => sanitize_text_field( $video['name'] ),
						'post_excerpt' => sanitize_text_field( $video['description'] )
					);
				$post = wp_update_post( $args );
				if ( $post ) {
					update_post_meta( $post, $playwire->video_meta_name, $video );
					self::handle_api_images( $video['thumbnail']['320x240'], $post );
				}
			}
		}
		// Update the options array if it's not empty
		if ( ! empty( $playwire->videos ) ) {
			update_option( $playwire->videos_option_name, $playwire->videos );
		}
	}


}
