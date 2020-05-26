<?php

class APPMAKER_WC_General_hooks {
	/**
	 * Holds the values to be used in the fields callbacks
	 *
	 * @var object
	 */
	private $options;

	/**
	 * Start up
	 */
	public function __construct() {
		require_once( APPMAKER_WC::$root . '/lib/vendor/fcm/class-appmaker-wc-fcm-helper.php' );

		if ( ! empty( $_GET['from_app'] ) && ! empty( $_GET['key'] ) || isset( $_COOKIE['from_app_set'] ) ) {
			add_action( 'wp_head', array( $this, 'appmaker_wc_hide_header_and_footer' ) );
		}
		if ( ! empty( $_GET['app_mailchimp'] ) ) {
			add_action( 'wp_head', array( $this, 'appmaker_wc_style_mailchimp' ) );
		}

		if ( ! empty( $_GET['payment_from_app'] ) ) {
			add_action( 'wp_head', array( $this, 'hook_stripe_enable_headers' ) );
			add_action( 'wp_footer', array( $this, 'hook_payment_footer' ) );
		}
		
		if ( empty( $_GET['from_app'] ) ) {
			add_action( 'wp_head', array( $this, 'appmaker_widget_script' ) );
		}
		add_action( 'woocommerce_email_after_order_table', array( $this, 'appmaker_woocommerce_email_after_order_table' ), 1, 4 );
		add_action( 'woocommerce_email_footer', array( $this, 'appmaker_email_footer' ), 1, 1 );

		$order_statuses = wc_get_order_statuses();
		foreach ( $order_statuses as $status_id => $status ) {
			$order_status = str_replace( 'wc-', '', $status_id );
			add_action( 'woocommerce_order_status_' . $order_status, array( $this, 'appmaker_wc_order_status_changed' ), 10, 1 );
		}

		add_action( 'woocommerce_loaded', array( $this, 'load_persistent_cart' ), 10, 1 );
		$this->options = get_option( 'appmaker_wc_settings' );

		if ( empty( $this->options['project_id'] ) && ! ( isset( $_POST['appmaker_wc_settings'] ) && ! empty( $_POST['appmaker_wc_settings']['project_id'] ) ) && ! ( isset( $_GET['page'] ) && $_GET['page'] == 'appmaker-wc-admin' ) ) {
			add_action( 'admin_notices', array( $this, 'show_settings_admin_message' ) );
		}
		//woocommerce all in one currency converter
		if ( class_exists( 'WooCommerce_All_in_One_Currency_Converter_Frontend' ) ) {
			add_action( 'init', array( $this, 'currency_converter' ), 0 );
		}
		add_filter( 'locale', array( $this, 'set_my_locale' ), 1, 1 );
		add_action( 'woocommerce_update_order', array( $this, 'appmaker_order_details' ), 2, 1 );

	}

	/**
	 * @param $order_id
	 */
	public function appmaker_order_details( $order_id ) {
		if ( ! $order_id ) {
			return;
		} elseif ( ( isset( $_GET['from_app'] ) && ! empty( $_GET['from_app'] ) ) || isset( $_COOKIE['from_app_set'] ) ) {
			$order = wc_get_order( $order_id );
			if ( is_a( $order, 'WC_Order' ) ) {
				if ( ! get_post_meta( $order_id, 'from_app' ) ) {
					$order->add_order_note( __( 'Order from App', 'appmaker-woocommerce-mobile-app-manager' ) );
					add_post_meta( $order_id, 'from_app', true );
				}
				$key = method_exists( $order, 'get_order_key' ) ? $order->get_order_key() : $order->order_key;
				WC()->session->set( 'last_order_key', $key );
			}
		}
	}

	public  function set_my_locale( $lang ) {

		if ( APPMAKER_WC::$api->get_settings( 'locale_code', false ) ) {
			$locale_code = array(
				'en' => 'en_US',
				'fa' => 'fa_IR',
				'de' => 'de_DE',
				'fr' => 'fr_FR',
				'es' => 'es_ES',
				'it' => 'it_IT',
				'ku' => 'ckb',

			);
			if ( isset( $_GET['language'] ) ) {
				  $lang = $_GET['language'];
				if ( array_key_exists( $_GET['language'], $locale_code ) ) {
					$lang = $locale_code[ $_GET['language'] ];
				}
			}
			return $lang;

		} else {
			return $lang;
		}
	}

	public function currency_converter() {
		if ( ! empty( $_REQUEST['currency'] ) ) {
			$_POST['wcaiocc_change_currency_code'] = $_REQUEST['currency'];
		}
	}

	function show_settings_admin_message() {
		?>
		<div class="notice notice-error" style="display: flex;">
				<a href="https://appmaker.xyz/woocommerce?utm_source=woocommerce-plugin&utm_medium=admin-notice&utm_campaign=after-plugin-install" class="logo" style="margin: auto;"><img src="https://storage.googleapis.com/stateless-appmaker-pages-wp/2019/04/10b81502-mask-group-141.png" alt="Appmaker.xyz"/></a>
				<div style="flex-grow: 1; margin: 15px 15px;">
					<h4 style="margin: 0;">Configure app to continue</h4>
					<p><?php echo __( 'Ouch!😓 It appears that your eCommerce App is not configured correctly. Kindly configure with correct Access key.', 'appmaker-woocommerce-mobile-app-manager' ); ?></p>
				</div>
				<a href="admin.php?page=appmaker-wc-admin&tab=step2" class="button button-primary" style="margin: auto 15px; background-color: #f16334; border-color: #f16334; text-shadow: none; box-shadow: none;">Take me there !</a>
		</div>
		<?php
	}

	public function appmaker_widget_script() {
		if ( APPMAKER_WC::$api->get_settings( 'smart_banner_app_store_enabled', false ) ) {

			$app_store_url  = APPMAKER_WC::$api->get_settings( 'smart_banner_app_store_url', false );
			$play_store_url = APPMAKER_WC::$api->get_settings( 'smart_banner_play_store_url', '' );
			$logo_url       = APPMAKER_WC::$api->get_settings( 'smart_banner_logo_url', '' );
			$title          = APPMAKER_WC::$api->get_settings( 'smart_banner_title', '' );
			$description    = APPMAKER_WC::$api->get_settings( 'smart_banner_description', '' );
			$cta_text       = APPMAKER_WC::$api->get_settings( 'smart_banner_cta_text', '' );

			if ( ! isset( $this->options ) || empty( $this->options ) ) {
				$this->options = get_option( 'appmaker_wc_settings' );
			}

			$project_id = isset( $this->options['project_id'] ) ? $this->options['project_id'] : '';

			$script  = '<script type="text/javascript">';
			$script .= '!function(e,t,n,a,o,p,r){e.AppmakerSmartBannerObject=o,e[o]=e[o]||function(){(e[o].q=e[o].q||[]).push(arguments)},e[o].l=1*new Date,p=t.createElement("script"),r=t.getElementsByTagName("script")[0],p.async=1,p.src="//cdn.mobgap.com/bundle.js?id=' . $project_id . '",r.parentNode.insertBefore(p,r)}(window,window.document,0,0,"appmakerSmartBanner"),window.appmakerSmartBanner("init",{appName:"' . $title . '",subText:"' . $description . '",showAfter:1500,CTAText:"' . $cta_text . '",appIcon:"' . $logo_url . '",urls:{android:"' . $play_store_url . '",ios:"' . $app_store_url . '"}});';
			$script .= '</script>';
			echo $script;
		}
	}

	public function appmaker_woocommerce_email_after_order_table( $order, $sent_to_admin, $plain_text, $email ) {
		$order_id = $order->get_id();
		if ( ! get_post_meta( $order_id, 'from_app' ) && ! $sent_to_admin ) {
			$GLOBALS['order_from_app'] = 'appmaker';
		}
	}

	public function appmaker_email_footer( $email ) {
		if ( isset( $GLOBALS['order_from_app'] ) && 'appmaker' === $GLOBALS['order_from_app'] && APPMAKER_WC::$api->get_settings( 'email_footer_enabled', false ) ) {
			$app_store_url  = APPMAKER_WC::$api->get_settings( 'email_footer_app_store_url', false );
			$play_store_url = APPMAKER_WC::$api->get_settings( 'email_footer_play_store_url', '' );
			$logo_url       = APPMAKER_WC::$api->get_settings( 'email_footer_logo_url', '' );
			$title          = APPMAKER_WC::$api->get_settings( 'email_footer_title', '' );
			$description    = APPMAKER_WC::$api->get_settings( 'email_footer_description', '' );
			?>
		<div style="display: block; background-color: #f1f2f3; padding: 16px; text-align: center; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; border-radius: 12px;">
		<img src="<?php echo $logo_url; ?>" alt="" width="100px" height="100px" style="margin-top:16px;">
		<h2 style="display: block; margin: 10px 0 20px 0; font-size: 22px; color: #212121; text-align: center;"><?php echo $title; ?> </h2>
		<p style="display: block; margin: 10px 0; font-size: 16px; color: #666666;"><?php echo $description; ?></p>
		<div style="display: block; text-align: center;">
			<?php if ( ! empty( $play_store_url ) ) { ?>
			<a href="<?php echo $play_store_url; ?>">
				<img src="https://storage.googleapis.com/stateless-appmaker-pages-wp/2020/04/4ba0c3f6-google-play-badge.png"
					alt="playstore-link" width="170px" />
			</a>
		<?php } ?>
			<?php if ( ! empty( $app_store_url ) ) { ?>
			<a href="<?php echo $app_store_url; ?>">
				<img src="https://storage.googleapis.com/stateless-appmaker-pages-wp/2020/04/d0648a50-app-store-badge.png"
					alt="appstore-link" width="170px" />
			</a>
		<?php } ?>
		</div>
	   </div>
			<?php
		}
	}

	public function is_mobile() {
		$is_mobile = wp_is_mobile();

		if ( $is_mobile ) {
			return $is_mobile;
		}

		// returns true if the CloudFront assumes the browser is  smartphone
		if ( isset( $_SERVER['HTTP_CLOUDFRONT_IS_MOBILE_VIEWER'] ) && 'true' === $_SERVER['HTTP_CLOUDFRONT_IS_MOBILE_VIEWER'] ) {
			$is_mobile = true;
		}

		// returns true if the CloudFront assumes the browser is a tablet.
		// remove the following three lines to assume table as PC.
		if ( isset( $_SERVER['HTTP_CLOUDFRONT_IS_TABLET_VIEWER'] ) && 'true' === $_SERVER['HTTP_CLOUDFRONT_IS_TABLET_VIEWER'] ) {
			$is_mobile = true;
		}

		return $is_mobile;
	}
	public function appmaker_wc_hide_header_and_footer() {
		$output       = APPMAKER_WC_General_Helper::get_custom_html();
		$custom_style = base64_decode( APPMAKER_WC::$api->get_settings( 'custom_webview_header', $output ) );
		echo $custom_style;
	}

	public function appmaker_wc_style_mailchimp() {
		$output  = '<style>.mc4wp-form-fields input{width:100%;display:block;}';
		$output .= '.mc4wp-form-fields input[type=submit] {  color: white;background-color: #fb5c06;border: none;border-radius: 2px;padding: 15px 0;margin-top: 35px;text-transform: uppercase;font-size: 18px; }';
		$output .= '.woocommerce-breadcrumb,.site-header,.site-footer,.mf-navigation-mobile,.navigation-list{display:none !important;}';
		$output .= '</style>';
		echo $output;
	}
	/**
	 * Order change callback function
	 *
	 * @param int $order_id order id.
	 */
	public function appmaker_wc_order_status_changed( $order_id ) {
		$fcm_key = APPMAKER_WC::$api->get_settings( 'fcm_server_key' );
		$order   = new WC_Order( $order_id );
		if ( ! empty( $fcm_key ) && APPMAKER_WC::$api->get_settings( 'enable_order_push', true ) ) {
			$fcm               = new Appmaker_WC_FCM_Helper( $fcm_key );
			$user_id           = self::get_property( $order, 'user_id' );
			$show_order_number = APPMAKER_WC::$api->get_settings( 'show_order_number', false );
			if ( class_exists( 'WC_Seq_Order_Number' ) ) {
				$display_order_id = self::get_property( $order, 'order_number' );
			} elseif ( $show_order_number ) {
				$display_order_id = $order->get_order_number();
			} else {
				$display_order_id = self::get_id( $order );
			}

			$email      = WC()->mailer()->emails['WC_Email_Customer_On_Hold_Order'];
			$is_enabled = true;
			if ( 'on-hold' == $order->get_status() ) {
				$is_enabled = $email->is_enabled();
			}

			if ( ! empty( $user_id ) && get_user_meta( $user_id, 'appmaker_wc_user_login_from_app' ) && $is_enabled ) {
				sprintf( __( 'Order updated #%s', 'appmaker-woocommerce-mobile-app-manager' ), $display_order_id );
				$fcm->setTopic( "user-$user_id" )
					->setMessage(
						sprintf( __( 'Order updated #%s', 'appmaker-woocommerce-mobile-app-manager' ), $display_order_id ),
						sprintf( __( 'Order status changed to %s', 'appmaker-woocommerce-mobile-app-manager' ), $order->get_status() )
					)
					->setAction(
						array(
							'type'   => 'OPEN_ORDER',
							'params' => array(
								'orderId' => $order_id,
							),
						)
					)
					->send();
			}
		}
	}

	public function hook_stripe_enable_headers() {
		$output  = '<style> .stripe_checkout_app { height: 580px !important; }';
		$output .= '#tpbr_topbar,footer,.breadcrumbs,.payment_method_paypal,.payment_method_stripe label::after,.payment_method_stripe_alipay,.payment_method_stripe label::before,.shop_table,.site-footer,.site-header{display:none!important}';
		$output .= '.payment_method_stripe{padding:0!important;border-top:unset!important}';
		$output .= 'button[type=submit]{width:100%!important}';
		$output .= '</style>';
		$output .= '<meta name="mobile-web-app-capable" content="yes">';
		$output .= '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
		echo $output;
	}

	public function hook_payment_footer() {
		$gateway = isset( $_GET['payment_gateway'] ) ? $_GET['payment_gateway'] : '';
		$output  = '
				<script type="text/javascript">
				window.onload = function() { 
					setTimeout(function(){
				';
		if ( ! empty( $gateway ) ) {
			$output .= "\n\t\t" . 'document.getElementById("payment_method_' . $gateway . '").checked = true;';
			$output .= "\n\t\t" . 'document.getElementById("payment_method_' . $gateway . '").click();';
		}
		$output .= "\n\t\t" . 'if(document.getElementById("terms") != null ) {';
		$output .= "\n\t\t" . 'document.getElementById("terms").checked = true;
			}
		';
		if ( isset( $gateway ) && ! in_array( $gateway, apply_filters( 'appmaker_wc_checkout_skip_click', array( 'square', 'eway_payments', 'payfort', 'stripe' ) ) ) ) {
			$output .= "\n\t\t" . '
			setTimeout(function(){
			if(document.getElementById("CBAWidgets1") != null){
				document.getElementById("CBAWidgets1").click();			
			} else {
				document.getElementById("place_order").click();
			}			
			},1000);
			';
		} else {
			$output .= "\n" . 'document.getElementById("payment_method_' . $gateway . '").scrollIntoView();';
		}
		$output .= '
					},500);
			';
		$output .= '
			}
			</script>
		  ';

		echo $output;
	}

	/**
	 * Load the persistent cart make cart sync with app
	 *
	 * @return void|bool
	 */
	public function load_persistent_cart() {
		global $current_user;

		if ( ! $current_user ) {
			return false;
		}

		$saved_cart = get_user_meta( $current_user->ID, '_woocommerce_persistent_cart', true );

		if ( $saved_cart && is_array( $saved_cart ) && isset( $saved_cart['cart'] ) ) {
			WC()->session->set( 'cart', $saved_cart['cart'] );
		}

		return true;
	}

	public static function get_id( $object ) {
		if ( method_exists( $object, 'get_id' ) ) {
			return $object->get_id();
		} else {
			return $object->id;
		}
	}

	static function get_property( $object, $property ) {
		if ( method_exists( $object, 'get_' . $property ) ) {
			return call_user_func( array( $object, 'get_' . $property ) );
		} else {
			return $object->{$property};
		}
	}
}

new APPMAKER_WC_General_hooks();
