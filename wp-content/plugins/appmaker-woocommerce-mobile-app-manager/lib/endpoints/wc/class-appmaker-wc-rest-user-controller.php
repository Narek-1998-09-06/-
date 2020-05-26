<?php
/**
 * REST API User controller
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * REST API User controller class.
 *
 */
class APPMAKER_WC_REST_User_Controller extends APPMAKER_WC_REST_Controller {

	/**
	 * Endpoint namespace.
	 *
	 * @var string
	 */
	protected $namespace = 'appmaker-wc/v1';

	/**
	 * Route base.
	 *
	 * @var string
	 */
	protected $rest_base = 'user';


	/**
	 * Register the routes for products.
	 */
	public function register_routes() {
		register_rest_route(
			$this->namespace,
			'/meta',
			array(
				array(
					'methods'  => WP_REST_Server::READABLE,
					'callback' => array( $this, 'get_meta' ),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);

		register_rest_route(
			$this->namespace,
			'/meta/plugins',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_plugins_list' ),
					'permission_callback' => array( $this, 'api_permissions_check_plugin' ),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/register',
			array(
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'register' ),
					'permission_callback' => array( $this, 'api_permissions_check' ),
					'args'                => $this->get_register_args(),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/send_otp',
			array(
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'send_otp' ),
					'permission_callback' => array( $this, 'api_permissions_check' ),
					'args'                => $this->get_login_otp_args(),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/login',
			array(
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'login' ),
					'permission_callback' => array( $this, 'api_permissions_check' ),
					'args'                => $this->get_login_args(),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/login_with_otp',
			array(
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'login_with_otp' ),
					'permission_callback' => array( $this, 'api_permissions_check' ),
					'args'                => $this->get_login_otp_args(),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/reset_password',
			array(
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'reset_password' ),
					'permission_callback' => array( $this, 'api_permissions_check' ),
					'args'                => $this->get_reset_password_args(),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/login_with_provider',
			array(
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'login_with_provider' ),
					'permission_callback' => array( $this, 'api_permissions_check' ),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/redirect',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'redirect' ),
					'permission_callback' => array( $this, 'api_permissions_check' ),
					'args'                => $this->get_redirect_args(),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/account_page',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'account_page' ),
					'permission_callback' => array( $this, 'user_logged_in_check' ),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/downloads',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_downloads' ),
					'permission_callback' => array( $this, 'user_logged_in_check' ),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/logout',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'logout' ),
					'permission_callback' => array( $this, 'api_permissions_check' ),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);

		register_rest_route(
			$this->namespace,
			'/manage-login',
			array(
				array(
					'methods'  => WP_REST_Server::READABLE,
					'callback' => array( $this, 'manage_login' ),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);

		register_rest_route(
			$this->namespace,
			'/login-manage',
			array(
				array(
					'methods'  => WP_REST_Server::READABLE,
					'callback' => array( $this, 'login_to_manage' ),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);

	}

	/**
	 * Return plugin meta (Publicly accessible).
	 *
	 * @return array
	 */
	public function get_meta( $request ) {
		global $woocommerce;
		$option            = get_option( $this->plugin . '_settings', false );
		$plugin_configured = ( $option !== false ) && isset( $option['api_secret'] ) && ! empty( $option['api_secret'] ) &&
		isset( $option['api_key'] ) && ! empty( $option['api_key'] );

		if ( $plugin_configured && isset( $request['api_secret'] ) ) {
			$plugin_configured = $plugin_configured && ( $request['api_secret'] === $option['api_secret'] );
		}

		if ( $plugin_configured && isset( $request['api_key'] ) ) {
			$plugin_configured = $plugin_configured && ( $request['api_key'] === $option['api_key'] );
		}

		$return  = array(
			'version'             => APPMAKER_WC::$version,
			'woocommerce_version' => $woocommerce->version,
			'plugin_configured'   => $plugin_configured,
			'project_id'		  => isset( $option['project_id'] ) ? $option['project_id'] : false,
		);

		if ( ! isset( $request['validation'] ) && isset( $request['api_secret'] ) && ! empty( $option['api_secret'] ) && $option['api_secret'] === $request['api_secret'] ) {
			$sales  = APPMAKER_WC::$api->APPMAKER_WC_REST_BACKEND_Report_Controller->sales( $request );
			$return = array_merge( $return, $sales );
		}
		return $return;
	}

	/**
	 * Return plugin list (Publicly accessible).
	 *
	 * @return array
	 */
	public function get_plugins_list( $request ) {

		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		$all_plugins = get_plugins();
		$plugin_list = array();

		if ( $all_plugins ) {

			foreach ( $all_plugins as $plugin => $data ) {

				$all_plugins[ $plugin ]['id']        = $plugin;
				$all_plugins[ $plugin ]['Activated'] = 'No';

				if ( is_plugin_active( $plugin ) ) {
					$all_plugins[ $plugin ]['Activated'] = 'Yes';
				}
				$plugin_list[] = $all_plugins[ $plugin ];
			}
			return $plugin_list;
		} else {
			return new WP_Error( 'plugin_list_error', 'Plugin list not found' );
		}
	}

	public function api_permissions_check_plugin( $request, $method = '' ) {

		$options = get_option( $this->plugin . '_settings', false );

		if ( isset( $request['api_secret'] ) && ! empty( $options['api_secret'] ) && $options['api_secret'] == $request['api_secret'] ) {

			return true;
		}
		return new WP_Error( 'rest_forbidden_context', __( 'Sorry, you are not allowed to do this' ), array( 'status' => rest_authorization_required_code() ) );
	}

	public function manage_login( $request ) {
		$options = get_option( $this->plugin . '_settings' );
		if ( $this->force_login() && ! empty( $options ) && ! empty( $options['api_secret'] ) && ! empty( $request['url'] ) ) {
			include_once( APPMAKER_WC::$root . '/lib/vendor/jwt/JWT.php' );
			$jwt      = new JWT();
			$key      = $options['api_secret'];
			$now      = time();
			$user     = wp_get_current_user();
			$token    = array(
				'jti'              => md5( $now . rand() ),
				'iat'              => $now,
				'wordpress_email'  => $user->user_email,
				'wordpress_name'   => $user->user_nicename,
				'wordpress_userId' => '' . $user->ID,
			);
			$jwt      = $jwt->encode( $token, $key );
			$url      = isset( $request['url'] ) ? $request['url'] : '';
			$location = "$url/user/dashboard-jwt/?jwt=" . $jwt;
			if ( ! empty( $request['return_to'] ) ) {
				$location .= '&return_to=' . urlencode( $request['return_to'] );
			}
			wp_redirect( $location, 302 );
			exit();
		}
		wp_safe_redirect( get_home_url(), 302 );
		exit();
	}

	public function login_to_manage( $request ) {
		$this->manage_login( array( 'url' => 'http://manage.appmaker.xyz' ) );
	}

	public function force_login() {
		// Redirect unauthorized visitors
		if ( ! is_user_logged_in() ) {
			// Get URL
			$url  = isset( $_SERVER['HTTPS'] ) && 'on' === $_SERVER['HTTPS'] ? 'https' : 'http';
			$url .= '://' . $_SERVER['HTTP_HOST'];
			// port is prepopulated here sometimes
			if ( strpos( $_SERVER['HTTP_HOST'], ':' ) === false ) {
				$url .= in_array( $_SERVER['SERVER_PORT'], array( '80', '443' ) ) ? '' : ':' . $_SERVER['SERVER_PORT'];
			}
			$url .= $_SERVER['REQUEST_URI'];
			// Apply filters
			$bypass       = apply_filters( 'appmaker_wc_forcelogin_bypass', false );
			$whitelist    = apply_filters( 'appmaker_wc_forcelogin_whitelist', array() );
			$redirect_url = apply_filters( 'appmaker_wc_forcelogin_redirect', $url );
			// Redirect visitors
			if ( preg_replace( '/\?.*/', '', $url ) != preg_replace( '/\?.*/', '', wp_login_url() ) && ! in_array( $url, $whitelist ) && ! $bypass ) {
				wp_safe_redirect( wp_login_url( $redirect_url ), 302 );
				exit();
			}
		} else {
			if ( current_user_can( 'manage_options' ) ) {
				return true;
			} else {
				wc_add_notice( 'You are not authorised for this action' );
				wp_safe_redirect( get_home_url(), 302 );
				exit();
			}
		}

		return false;
	}

	/**
	 * Perform user registration
	 *
	 * @param WP_REST_Request $request request object.
	 *
	 * @return array|int|mixed|WP_Error
	 */
	public function register( $request ) {
		$return = array( 'status' => true );
		$return = apply_filters( 'appmaker_wc_registration_validate', $return, $request );

		if ( ! empty( $request['phone'] ) && WC()->countries->get_base_country() === 'IN' ) {
			if ( ! preg_match( '/^\d{10}$/', $request['phone'] ) ) {
				return new WP_Error( 'invalid_phone', 'Invalid phone number' );
			}
		}
		if ( ! is_wp_error( $return ) ) {
			if ( isset( $request['phone'] ) && $request['otp'] ) {
				$user_id = wc_create_new_customer( sanitize_email( $request['email'] ), wc_clean( $request['phone'] ), $request['password'] );
			} else {
				$user_id = wc_create_new_customer( sanitize_email( $request['email'] ), wc_clean( $request['username'] ), $request['password'] );
			}
			if ( is_wp_error( $user_id ) ) {

				return $user_id;
			}
			add_user_meta( $user_id, '_registered_from_app', 1 );

			if ( isset( $request['phone'] ) ) {
				update_user_meta( $user_id, 'billing_phone', trim( $request['phone'] ) );
				update_user_meta( $user_id, 'shipping_phone', trim( $request['phone'] ) );
			}
			update_user_meta( $user_id, 'appmaker_wc_user_login_from_app', true );
			do_action( 'appmaker_wc_user_registered', $user_id, $request );

			if ( apply_filters( 'appmaker_wc_login_after_register_required', true ) ) {
				 $return = $this->login( $request );
			}
			$register_datetime = current_time( 'mysql' );
			update_user_meta( $user_id, 'user_register_date_time', $register_datetime );
			return  apply_filters( 'appmaker_wc_registration_response', $return );

		}

		return $return;
	}
	/**
	 * Send otp on registration and login
	 */
	public function send_otp( $request ) {
		$return = apply_filters( 'appmaker_wc_send_otp', $request );
		$ret    = array(
			'type' => 'error',
			'msg'  => '',
		);
		if ( $return['type'] == 'success' ) {
			$ret['type'] = 'success';
			$ret['msg']  = 'OTP sent successfully';
			return $ret;
		} elseif ( $return['messages'][0]->status == 0 ) {
			$ret['type'] = 'success';
			$ret['msg']  = 'OTP sent successfully';
			return $ret;
		} else {
			return new WP_Error( 'cannot_send_otp', __( 'The system can\'t able to send OTP, try after some time', 'appmaker-woocommerce-mobile-app-manager' ) );
		}
	}

	/**
	 * Set Current User token and return user object
	 *
	 * @param WP_User $user User object.
	 *
	 * @return array|int|mixed|void|WP_Error
	 */
	public function set_current_user( $user ) {
		$access_token = apply_filters( 'appmaker_wc_set_user_access_token', $user->ID );
		update_user_meta( $user->ID, 'appmaker_wc_user_login_from_app', true );
		$return = array(
			'status'       => 1,
			'access_token' => $access_token,
			'user_id'      => $user->data->ID,
			'user'         => array(
				'id'           => $user->data->ID,
				'nicename'     => $user->data->user_nicename,
				'email'        => $user->data->user_email,
				'status'       => $user->data->user_status,
				'display_name' => $user->data->display_name,
				'avatar'       => $this->get_avatar( $user ),
			),
		);

		return $return;
	}

	/**
	 * @param WP_User $user User object.
	 *
	 * @return string
	 */
	public function get_avatar( $user ) {
		$avatar = get_user_meta( $user->data->ID, '_user_avatar', true );
		if ( ! empty( $avatar ) ) {
			return $avatar;
		} else {
			$avatar = get_avatar_url( $user->data->ID );
			/*if ( empty( $avatar ) ) {
			   $avatar = get_avatar_url( $user->data->ID, array( 'default' => 'retro', 'size' => 96 ) );
			}*/

			return $avatar;
		}
	}

	/**
	 * Perform user login
	 *
	 * @param WP_REST_Request $request request object.
	 *
	 * @return array|int|mixed|void|WP_Error
	 */
	public function login( $request ) {
		if ( function_exists( 'w3tc_dbcache_flush' ) ) {
			w3tc_dbcache_flush();
		}
		if ( isset( $request['phone'] ) && $request['otp'] ) {
			$user = get_user_by( 'login', $request['phone'] );
		} else {
			$user = get_user_by( 'login', $request['username'] );
		}
		if ( ! $user ) {
			$user = get_user_by( 'email', $request['username'] );
		}
		if ( $user && wp_check_password( $request['password'], $user->data->user_pass, $user->ID ) ) {
			//Saving date and time after user login
			$login_datetime = current_time( 'mysql' );
			update_user_meta( $user->ID, 'user_login_date_time', $login_datetime );
			return $this->set_current_user( $user );
		} else {
			$return = new WP_Error( 'invalid_login_details', __( 'Invalid username/password', 'appmaker-woocommerce-mobile-app-manager' ) );
		}

		return apply_filters( 'appmaker_wc_login_response', $return );
	}

	/**
	 * perform user login with otp
	 */
	public function login_with_otp( $request ) {
		$return = array( 'status' => true );
		$return = apply_filters( 'appmaker_wc_login_validate', $return, $request );
		if ( ! empty( $request['phone'] ) && WC()->countries->get_base_country() === 'IN' ) {
			if ( ! preg_match( '/^\d{10}$/', $request['phone'] ) ) {
				return new WP_Error( 'invalid_phone', 'Invalid phone number' );
			}
		}
		if ( ! is_wp_error( $return ) ) {
			$user = get_user_by( 'login', $request['phone'] );
			return $this->set_current_user( $user );
		}

		return $return;
	}

	public function logout() {
		wp_set_current_user( 0 );
		wp_clear_auth_cookie();
		wc_clear_notices();
		WC()->cart->empty_cart( false );
		WC()->session->set( 'cart', null );

		return array( 'status' => true );
	}

	/**
	 * Perform rest password
	 *
	 * @param WP_REST_Request $request request object.
	 *
	 * @return array|int|mixed|void|WP_Error
	 */
	public function reset_password( $request ) {
		$_POST['user_login'] = $request['email'];
		$return = apply_filters( 'appmaker_wc_forget_password', $request );
		if($return['status'] == true){
			$success = true;
		}else{
		$success             = WC_Shortcode_My_Account::retrieve_password();
		}
		if ( true != $success ) {
			$error = $this->get_wc_notices_errors();
			if ( false == $error ) {
				return new WP_Error( 'rest_error', __( 'The e-mail could not be sent', 'appmaker-woocommerce-mobile-app-manager' ) );
			} else {
				return $error;
			}
		} else {
			return array(
				'status'  => true,
				'message' => __( 'Link for password reset has been emailed to you. Please check your email.', 'appmaker-woocommerce-mobile-app-manager' ),
			);
		}

		return $return;
	}

	/**
	 * Perform auth cookie redirect
	 *
	 * @param WP_REST_Request $request request object.
	 */
	public function redirect( $request ) {
		
		$order_in_web = APPMAKER_WC::$api->get_settings( 'order_in_web', false );
		$url = base64_decode( $request['url'] );
		switch ( $url ) {
			case 'cart': {
				$url = apply_filters( 'woocommerce_get_cart_url', wc_get_page_permalink( 'cart' ) );
				break;
			}
			case 'checkout': {
				$url = wc_get_checkout_url();
				break;
			}
			case 'orders': {
				$url = wc_get_page_permalink( 'myaccount' ) . '/orders';
				break;
			}
			case 'order-detail': {
				$id = $request['id'];
				//echo "ID".$id; exit;
				$order       = wc_get_order( $id );
						$url = esc_url( $order->get_view_order_url() );
						break;
			}
			case 'payment': {
				if ( isset( $request['id'] ) ) {
					$order = wc_get_order( $request['id'] );
					//find_order_by_order_number
					if ( empty( $order ) && class_exists( 'WC_Seq_Order_Number' ) ) {
						$order_id = WC_Seq_Order_Number::instance()->find_order_by_order_number( $request['id'] );
						$order    = $order = wc_get_order( $order_id );
					}
					if ( empty( $order ) ) {
						return new WP_Error( 'invalid_order', 'Invalid order' );
					} else {
						$url = $order->get_checkout_payment_url();
					}
				} else {
					$my_account_page_id = get_option( 'woocommerce_myaccount_page_id' );
					if ( $my_account_page_id ) {
						$url = get_permalink( $my_account_page_id );
					}
				}
				break;
			}
		}
		$url = $this->ensure_absolute_link( $url );
		$url = add_query_arg( array( 'from_app' => true ), $url );
		if($order_in_web){
			$url = add_query_arg( array( 'app_order_in_webview' => true ), $url );
		}		
		$url = apply_filters( 'appmaker_wc_redirect_url', $url );
		if ( strpos( $url, 'from_app' ) !== false ) {
			$expire = time() + 60 * 60 * 24 * 30;
			wc_setcookie( 'from_app_set', 1, $expire, false );
		}

		$set_auth_cookie = APPMAKER_WC::$api->get_settings( 'set_auth_cookie_redirect', true );
		if ( $set_auth_cookie ) {

			wp_set_auth_cookie( get_current_user_id(), true );
			$user = wp_get_current_user();
			do_action( 'wp_login', $user->user_login, $user );

		}

		do_action( 'appmaker_wc_before_redirect', $url );
		// if ( !empty($request['platform']) && $request['platform'] === 'ios' ) {
		//     header('Content-Type: text/html; charset=utf-8');
		//     echo '<!DOCTYPE html><html><head>
		//                 <meta name="viewport" content="width=device-width, initial-scale=1" />
		//                 <meta http-equiv="refresh" content="0; url='.$url.'" />
		//                 </head>
		//                 <body>
		//                 <p>Please wait while redirecting...</p>
		//                 </body>
		//           </html>';
		// } else {
		//     wp_redirect( $url );
		// }

		wp_redirect( $url );
		do_action( 'appmaker_wc_after_redirect', $url );
		exit;
	}

	/**
	 * Perform social login
	 *
	 * @param WP_REST_Request $request request object.
	 *
	 * @return array|int|mixed|WP_Error
	 */
	public function login_with_provider( $request ) {
		try {
			$return          = array();
			$facebook_id     = APPMAKER_WC::$api->get_settings( 'facebook_id' );
			$facebook_secret = APPMAKER_WC::$api->get_settings( 'facebook_secret' );

			//echo "facebook id - ".$facebook_id."secret = ".$facebook_secret;
			//exit;
			if ( ! class_exists( 'Hybrid_Auth' ) ) {
				require_once( APPMAKER_WC::$root . '/lib/vendor/hybridauth/hybridauth/Hybrid/Auth.php' );
			}
			$config = array(
				'base_url'   => 'http://localhost/hybridauth-git/hybridauth/',
				'providers'  => array(
					'Facebook' => array(
						'enabled'        => true,
						'keys'           => array(
							'id'     => $facebook_id,
							'secret' => $facebook_secret,
						),
						'trustForwarded' => false,
					),
				),
				'debug_mode' => false,
			);

			$hybridauth = new Hybrid_Auth( $config );
			if ( 'google' === $request['provider'] ) {
				$hybridauth->storage()->set( 'hauth_session.google.is_logged_in', 1 );
				$hybridauth->storage()->set( 'hauth_session.google.token.access_token', $request['access_token'] );
				$provider = $hybridauth->getAdapter( 'Google' );
			} elseif ( 'facebook' === $request['provider'] ) {
				$hybridauth->storage()->set( 'hauth_session.facebook.is_logged_in', 1 );
				$hybridauth->storage()->set( 'hauth_session.facebook.token.access_token', $request['access_token'] );
				$provider = $hybridauth->getAdapter( 'Facebook' );
			} else {
				return new WP_Error( 'invalid_provider', __( 'Invalid Provider' ), 401 );
			}
			$profile = $provider->getUserProfile();
			if ( isset( $profile->emailVerified ) && ! empty( $profile->emailVerified ) && is_email( $profile->emailVerified ) ) {
				$user   = get_user_by( 'email', $profile->emailVerified );
				$return = apply_filters( 'appmaker_wc_login_with_provider_validate', $return, $request, $user );
				if ( is_wp_error( $return ) || ( isset( $return['status'] ) && $return['status'] === false ) ) {
					return $return;
				}
				if ( empty( $user ) ) {
					add_filter( 'option_woocommerce_registration_generate_username', array( $this, '_return_yes' ), 9999 );
					add_filter( 'option_woocommerce_registration_generate_password', array( $this, '_return_yes' ), 9999 );
					$user_id = wc_create_new_customer( $profile->emailVerified, false, false );
					if ( is_wp_error( $user_id ) ) {
						return $user_id;
					}
					wp_update_user(
						array(
							'ID'         => $user_id,
							'first_name' => $profile->firstName,
							'last_name'  => $profile->lastName,
						)
					);

					add_user_meta( $user_id, '_registered_from_app', 1 );
					if ( ! empty( $profile->photoURL ) ) {
						add_user_meta( $user_id, '_user_avatar', $profile->photoURL );
					}

					$user = get_user_by( 'id', $user_id );

					$return = $this->set_current_user( $user );
				} else {
					$return = $this->set_current_user( $user );
				}
			} else {
				$return = new WP_Error( 'token_error', __( 'Sorry, no verified email available on your facebook account' ), 401 );
			}
		} catch ( Exception $e ) {
			$return = new WP_Error( 'settings_error', __( 'Facebook login configuration error' ), 401 );
		}

		return $return;
	}

	/**
	 * Return account page menu
	 *
	 * @param WP_REST_Request $request request object.
	 *
	 * @return array|int|mixed|void|WP_Error
	 */
	public function account_page( $request ) {
		if ( function_exists( 'wc_get_account_menu_items' ) ) {
			$menu_items = wc_get_account_menu_items();
		} else {
			$menu_items = array(
				'dashboard'       => __( 'Dashboard', 'woocommerce' ),
				'orders'          => __( 'Orders', 'woocommerce' ),
				'downloads'       => __( 'Downloads', 'woocommerce' ),
				'edit-address'    => __( 'Addresses', 'woocommerce' ),
				'payment-methods' => __( 'Payment methods', 'woocommerce' ),
				'edit-account'    => __( 'Account details', 'woocommerce' ),
				'customer-logout' => __( 'Logout', 'woocommerce' ),
			);
		}
		$return               = array();
		$show_shipping_fields = APPMAKER_WC::$api->get_settings( 'show_shipping_address_fields', true );

		$enable_notifcation_history = APPMAKER_WC::$api->get_settings( 'enable_notifcation_history', false );
		if ( $enable_notifcation_history ) {
			$return['notifcation_history'] = array(
				'title'  => __( 'Notifications', 'woocommerce' ),
				'icon'   => array(
					'android' => 'bell',
					'ios'     => 'bell',
				),
				'action' => array(
					'type'   => 'OPEN_IN_APP_PAGE',
					'params' => array(
						'id' => 'dynamic/push-notification-history',
					),
				),
			);
		}
		$order_in_web = APPMAKER_WC::$api->get_settings( 'order_in_web', false );
		if ( 0 == $order_in_web) {
			$return['orders'] = array(
				'title'  => __( 'Orders', 'woocommerce' ),
				'icon'   => array(
					'android' => 'event-note',
					'ios'     => 'ios-copy-outline',
				),
				'action' => array(
					'type'   => 'LIST_ORDER',
					'params' => array(),
				),
			);
		}else{
			$option            = get_option( $this->plugin . '_settings', false );
			$base_url     = site_url();
			$url          = $base_url . '/my-account/orders/';
			$api_key      = $option['api_key'];
			$user_id      = get_current_user_id();
			$access_token = apply_filters( 'appmaker_wc_set_user_access_token', $user_id );
			$url          = base64_encode( $url );
			$url          = $base_url . '/?rest_route=/appmaker-wc/v1/user/redirect/&url=' . $url . '&api_key=' . $api_key . '&access_token=' . $access_token . '&user_id=' . $user_id;
			
			$return['orders'] = array(
                'title'  => __( 'Orders', 'woocommerce' ),
                'icon'   => array(
                    'android' => 'event-note',
                    'ios'     => 'ios-copy-outline',
                ),
                'action' => array(
                    'type' => 'OPEN_IN_WEB_VIEW',
                    'params' => array( 'title'=>'Orders','url' =>$url ),
                ),
            );
		}
		if ( ( isset( $menu_items['downloads'] ) ) && ( APPMAKER_WC::$api->get_settings( 'hide_downloads', 0 ) ) == 0 ) {
			$return['downloads'] = array(
				'title'  => __( 'Downloads', 'woocommerce' ),
				'icon'   => array(
					'android' => 'file-download',
					'ios'     => 'ios-download-outline',
				),
				'action' => array(
					'type' => 'OPEN_DOWNLOADS',
				),
			);
		}
		if ( isset( $menu_items['edit-address'] ) ) {

			$return['billing_address'] = array(
				'title'  => __( 'Billing address', 'woocommerce' ),
				'icon'   => array(
					'android' => 'credit-card',
					'ios'     => 'ios-card-outline',
				),
				'action' => array(
					'type'   => 'OPEN_DYNAMIC_FORM',
					'params' => array(
						'form'  => 'billing_address',
						'title' => __( 'Billing address', 'woocommerce' ),
					),
				),
			);

			if ( $show_shipping_fields ) {

				$return['shipping_address'] = array(
					'title'  => __( 'Shipping address', 'woocommerce' ),
					'icon'   => array(
						'android' => 'local-shipping',
						'ios'     => 'ios-paper-plane-outline',
					),
					'action' => array(
						'type'   => 'OPEN_DYNAMIC_FORM',
						'params' => array(
							'form'  => 'shipping_address',
							'title' => __( 'Shipping address', 'woocommerce' ),
						),
					),
				);

			}
		}
		if ( isset( $menu_items['edit-account'] ) ) {

			$return['account_details'] = array(
				'title'  => __( 'Account details', 'woocommerce' ),
				'icon'   => array(
					'android' => 'settings',
					'ios'     => 'ios-settings-outline',
				),
				'action' => array(
					'type'   => 'OPEN_DYNAMIC_FORM',
					'params' => array(
						'form'  => 'account_form',
						'title' => __( 'Account details', 'woocommerce' ),
					),
				),
			);
		}

		if ( ( isset( $request['show_language_chooser'] ) && ( $request['show_language_chooser'] == 1 || $request['show_language_chooser'] == true ) )
			&& ( APPMAKER_WC::$api->get_settings( 'hide_language_chooser', 0 ) ) == 0 ) {

			$return['language_switcher'] = array(
				'title'  => __( 'Change Language', 'appmaker-woocommerce-mobile-app-manager' ),
				'icon'   => array(
					'android' => 'language',
					'ios'     => 'ios-globe-outline',
				),
				'action' => array(
					'type' => 'OPEN_LANGUAGES_PAGE',
				),
			);
		}

		if ( class_exists( 'WooCommerce_All_in_One_Currency_Converter_Frontend' ) ) {
			$return['currency_switcher'] = array(
				'title'  => __( 'Change currency', 'appmaker-woocommerce-mobile-app-manager' ),
				'icon'   => array(
					'android' => 'credit-card',
					'ios'     => 'ios-card-outline',
				),
				'action' => array(
					'type' => 'OPEN_CURRENCY_PAGE',
				),
			);
		}

		$return['logout'] = array(
			'title'  => __( 'Logout', 'woocommerce' ),
			'icon'   => array(
				'android' => 'person',
				'ios'     => 'ios-log-out-outline',
			),
			'action' => array(
				'type' => 'LOGOUT',
			),
		);

		$return = apply_filters( 'appmaker_wc_account_page_response', $return );
		$return = array_values( $return );

		return $return;
	}

	/**
	 * Return downloads.
	 *
	 * @param WP_REST_Request $request Request Object.
	 *
	 * @return array
	 */
	public function get_downloads( $request ) {
		$user      = get_current_user_id();
		$downloads = wc_get_customer_available_downloads( $user );
		$return    = array(
			'items' => array(),
		);
		foreach ( $downloads as $download ) {
			$return['items'][] = array(
				'download_url'        => $download['download_url'],
				'download_name'       => $this->decode_html( $download['download_name'] ),
				'order_id'            => $download['order_id'],
				'downloads_remaining' => $download['downloads_remaining'],
				'access_expires'      => $download['access_expires'],
			);
		}

		return $return;
	}

	/**
	 * Return false sting for variation
	 *
	 * @return string
	 */
	public function _return_yes() {
		return 'yes';
	}

	/**
	 * Validate email.
	 *
	 * @param string $username Username.
	 *
	 * @return bool
	 */
	public function validate_username( $username ) {
		$username = trim( $username );

		return validate_username( $username );
	}
	/**
	 * Trim text.
	 *
	 * @param string $text Text.
	 *
	 * @return string
	 */
	public function trim( $text ) {
		return trim( $text );
	}

	/**
	 * Get the query params for collections of attachments.
	 *
	 * @return array
	 */
	public function get_register_args() {
		$params             = array();
		$params['username'] = array(
			'description'       => __( 'Username.', 'appmaker-woocommerce-mobile-app-manager' ),
			'type'              => 'string',
			'validate_callback' => array( $this, 'validate_username' ),
			'required'          => apply_filters( 'appmaker_wc_register_username_required', true ),
		);

		$params['email'] = array(
			'description'       => __( 'Email.', 'appmaker-woocommerce-mobile-app-manager' ),
			'type'              => 'string',
			'validate_callback' => array( 'WC_Validation', 'is_email' ),
			'required'          => apply_filters( 'appmaker_wc_register_email_required', false ),
		);

		$params['phone'] = array(
			'description'       => __( 'Mobile Number.', 'appmaker-woocommerce-mobile-app-manager' ),
			'type'              => 'string',
			'sanitize_callback' => array( $this, 'trim' ),
			'validate_callback' => array( 'WC_Validation', 'is_phone' ),
			'required'          => apply_filters( 'appmaker_wc_register_phone_required', false ),
		);

		$params['password'] = array(
			'description'       => __( 'Password.', 'appmaker-woocommerce-mobile-app-manager' ),
			'type'              => 'string',
			'sanitize_callback' => array( $this, 'trim' ),
			'validate_callback' => 'rest_validate_request_arg',
			'required'          => apply_filters( 'appmaker_wc_register_password_required', true ),
		);

		$params['otp'] = array(
			'description'       => __( 'One Time Password.', 'appmaker-woocommerce-mobile-app-manager' ),
			'type'              => 'string',
			'sanitize_callback' => array( $this, 'trim' ),
			'validate_callback' => array( 'WC_Validation', 'is_phone' ),
			//'required'          =>true,
		);

		return $params;
	}

	/**
	 * Get the query params for collections of attachments.
	 *
	 * @return array
	 */
	public function get_login_args() {
		$params             = array();
		$params['username'] = array(
			'description'       => __( 'Username.', 'appmaker-woocommerce-mobile-app-manager' ),
			'type'              => 'string',
			'validate_callback' => array( $this, 'validate_username' ),
			'required'          => apply_filters( 'appmaker_wc_login_username_required', true ),
		);

		$params['email'] = array(
			'description'       => __( 'Email.', 'appmaker-woocommerce-mobile-app-manager' ),
			'type'              => 'string',
			'validate_callback' => array( $this, 'is_email' ),
			'required'          => apply_filters( 'appmaker_wc_login_email_required', false ),
		);

		$params['password'] = array(
			'description'       => __( 'Password.', 'appmaker-woocommerce-mobile-app-manager' ),
			'type'              => 'string',
			'sanitize_callback' => array( $this, 'trim' ),
			'validate_callback' => 'rest_validate_request_arg',
			'required'          => true,
		);

		return $params;
	}
	/**
	 * Get the query params for collections of attachments.
	 */

	public function get_login_otp_args() {
		$params          = array();
		$params['phone'] = array(
			'description'       => __( 'Mobile Number.', 'appmaker-woocommerce-mobile-app-manager' ),
			'type'              => 'string',
			'sanitize_callback' => array( $this, 'trim' ),
			'validate_callback' => array( 'WC_Validation', 'is_phone' ),
			'required'          => true,
		);
		$params['otp']   = array(
			'description'       => __( 'One Time Password.', 'appmaker-woocommerce-mobile-app-manager' ),
			'type'              => 'string',
			'sanitize_callback' => array( $this, 'trim' ),
			'validate_callback' => array( 'WC_Validation', 'is_phone' ),
			// 'required'          =>true,
		);
		return $params;
	}

	/**
	 * Get the query params for collections of attachments.
	 *
	 * @return array
	 */
	public function get_reset_password_args() {
		$params = array();

		$params['email'] = array(
			'description' => __( 'Email.', 'appmaker-woocommerce-mobile-app-manager' ),
			'type'        => 'string',
			'required'    => apply_filters( 'appmaker_wc_login_email_required', false ),
		);

		return $params;
	}

	/**
	 * Get the query params for collections of attachments.
	 *
	 * @return array
	 */
	public function get_redirect_args() {
		$params = array();

		$params['url'] = array(
			'description' => __( 'Url.', 'appmaker-woocommerce-mobile-app-manager' ),
			'type'        => 'string',
			'required'    => true,
		);

		return $params;
	}
}
