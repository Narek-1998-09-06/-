<?php
/*
Plugin Name: WooCommerce Ameria Payment Gateway Pretty With Exchange
Plugin URI: https://github.com/upgernaut/WooCommerce-Ameria-Payment-Gateway-Pretty
Description: WooCommerce payment gateway using Ameriabank third-party platform (on ARCA)
Author: Aram Khachikyan
Author URI: https://aramkhachikyan.com
Version: 2.0.0
Requires at least: WP 5.2.2
Tested up to: WP 5.2.2
Text Domain: woocommerce-ameria-payment-gateway-pretty
Domain Path: /languages
Forum URI: #
 */

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

add_action('plugins_loaded', 'wc_ameria_payment_gateway_pretty_init', 11);

function wc_ameria_payment_gateway_pretty_init()
{

    class WC_Ameria_Payment_Gateway_Pretty extends WC_Payment_Gateway
    {
        public function __construct()
        {

            $this->id                 = 'WC_Ameria_Payment_Gateway_Pretty';
            $this->has_fields         = false;
            $this->method_title       = 'Ameriabank Payment Gateway';
            $this->method_description = "Payment via Ameriabank third party payment system.";
            $this->notify_url         = str_replace('https:', 'http:', add_query_arg('wc-api', 'WC_Ameria_Payment_Gateway_Pretty', home_url('/')));
            $this->init_form_fields();
            $this->init_settings();
            $this->title             = $this->settings['title'];
            $this->description       = $this->settings['description'];
            $this->order_button_text = __($this->settings['buttontext'], 'wc_ameria_payment_gateway_pretty');
            add_action('woocommerce_api_wc_ameria_payment_gateway_pretty', array($this, 'wapgp_response'));

            $this->testmode = ($this->get_option('testmode') == "yes") ? true : false;

            if ($this->testmode) {
                $this->payment_url = 'https://servicestest.ameriabank.am/VPOS/';
            } else {
                $this->payment_url = 'https://services.ameriabank.am/VPOS/';
            }

            add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
			
			$this->refund = ($this->get_option('refund') == "yes") ? true : false;
			
			if ($this->refund) {
				$this->supports = array(
					'refunds'
				);
			} 

        }
		
		public function process_refund( $order_id, $amount = null, $reason = '' ) {
            // URL for cURL request
            $url = $this->payment_url . 'api/VPOS/RefundPayment';
			
			$ameria_payment_id = get_post_meta($order_id, 'ameria_payment_id', true );
		
            $parms['paymentfields']['PaymentID'] = $ameria_payment_id ;
            $parms['paymentfields']['Username']  = $this->get_option('username');
            $parms['paymentfields']['Password']  = $this->get_option('password');
            $parms['paymentfields']['Amount']  = $amount;

            /* Send cURL GetPaymentDetails request and get result */

            //url-ify the data for the POST
            $fields_string = '';
            foreach ($parms['paymentfields'] as $key => $value) {$fields_string .= $key . '=' . $value . '&';}
            rtrim($fields_string, '&');

            //open connection
            $ch = curl_init();

            //set the url, number of POST vars, POST data
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, count($parms['paymentfields']));
            curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);

            //execute post
            $result = curl_exec($ch);

            //close connection
            curl_close($ch);

            /* Check if response code is 1 then go further if not, false return */
            $result_json_decoded = json_decode($result);
			
            if ($result_json_decoded->ResponseCode !== '00') {
				return new WP_Error( 'error', __( $result_json_decoded->ResponseMessage, "wc_ameria_payment_gateway_pretty" ) );

                die;
            }			
			
			return true;
		}
		
		public function get_icon() {

			$icon_html = '<img style="margin-right: 10px;
			float: none;
			margin-top: 0;
			
			border-left: 1px solid #666;
			padding-left: 10px;
			border-radius: 0;
			display: inline-block;
			vertical-align: middle;
			margin-left: 10px;
			height: 20px;
			width: auto;
			max-height: none;" src="' . plugins_url( 'logo.jpg' , __FILE__ ) . '">';

			return apply_filters( 'woocommerce_gateway_icon', $icon_html, $this->id );
		}				

        public function wapgp_response($param)
        {

            global $woocommerce;

            $order_id          = $_SESSION['order_id'];
            $order_description = $_SESSION['order_description'];
            $cart_total        = $_SESSION['cart_total'];
            $payment_id        = $_SESSION['payment_id'];

            // Unset session variables for more safety
            unset($_SESSION['order_id']);
            unset($_SESSION['order_description']);
            unset($_SESSION['cart_total']);
            unset($_SESSION['payment_id']);

            // URL for cURL request
            $url = $this->payment_url . 'api/VPOS/GetPaymentDetails';

            $parms['paymentfields']['PaymentID'] = $payment_id;
            $parms['paymentfields']['Username']  = $this->get_option('username');
            $parms['paymentfields']['Password']  = $this->get_option('password');

            /* Send cURL GetPaymentDetails request and get result */

            //url-ify the data for the POST
            $fields_string = '';
            foreach ($parms['paymentfields'] as $key => $value) {$fields_string .= $key . '=' . $value . '&';}
            rtrim($fields_string, '&');

            //open connection
            $ch = curl_init();

            //set the url, number of POST vars, POST data
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, count($parms['paymentfields']));
            curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);

            //execute post
            $result = curl_exec($ch);

            //close connection
            curl_close($ch);

            /* Check if response code is 1 then go further if not, false return */
            $result_json_decoded = json_decode($result);

            if ($result_json_decoded->ResponseCode !== '00') {
			
				wc_add_notice(__($result_json_decoded->Description, 'wc_ameria_payment_gateway_pretty'), 'error');
                $cart_url = $woocommerce->cart->get_cart_url();
                wp_redirect($cart_url);

                return false;
                die;
            }

            /* / Confirm operation */
			add_post_meta( $order_id, 'ameria_payment_id', $payment_id );
            $order = wc_get_order($order_id);
            $order->payment_complete();
            $order->reduce_order_stock();
            WC()->cart->empty_cart();
            $thankyou = $this->get_return_url($order);

            wp_redirect($thankyou);die;

        }

        public function init_form_fields()
        {
            $this->form_fields = apply_filters('wc_ameria_payment_gateway_pretty_form_fields', array(
                'enabled'         => array(
                    'title'   => __('Enable/Disable', 'wc_ameria_payment_gateway_pretty'),
                    'type'    => 'checkbox',
                    'label'   => __('Enable Ameriabank Payment', 'wc_ameria_payment_gateway_pretty'),
                    'default' => 'yes',
                ),
                'testmode'        => array(
                    'title'    => __('Test mode', 'wc_ameria_payment_gateway_pretty'),
                    'type'     => 'checkbox',
                    'label'    => __('If enabled you can test', 'wc_ameria_payment_gateway_pretty'),
                    'desc_tip' => true,
                    'default'  => 'yes',
                ),
                'exchange'        => array(
                    'title'    => __('Automatic Exchange with Central Bank Armenia rates', 'wc_ameria_payment_gateway_pretty'),
                    'type'     => 'checkbox',
                    'label'    => __('If enabled amount of order will be automatically exchanged (requres <strong>SOAP</strong>)', 'wc_ameria_payment_gateway_pretty'),
                    'desc_tip' => true,
                    'default'  => 'no',
                ),
                'refund'        => array(
                    'title'    => __('Refund', 'wc_ameria_payment_gateway_pretty'),
                    'type'     => 'checkbox',
                    'label'    => 'Check if refund is turned on',
                    'desc_tip' => true,
                    'default'  => 'no',
                ),
                'title'           => array(
                    'title'       => __('Title', 'wc_ameria_payment_gateway_pretty'),
                    'type'        => 'text',
                    'description' => __('This controls the title for the payment method the customer sees during checkout.', 'wc_ameria_payment_gateway_pretty'),
                    'default'     => __('Ameriabank Payment ', 'wc_ameria_payment_gateway_pretty'),
                    'desc_tip'    => true,
                ),
                'buttontext'      => array(
                    'title'       => __('Button Text', 'wc_ameria_payment_gateway_pretty'),
                    'type'        => 'text',
                    'description' => __('This controls the title for the button, during checkout.', 'wc_ameria_payment_gateway_pretty'),
                    'default'     => __('Pay with visa, mastercard, arca', 'wc_ameria_payment_gateway_pretty'),
                    'desc_tip'    => true,
                ),
                'description'     => array(
                    'title'       => __('Description', 'wc_ameria_payment_gateway_pretty'),
                    'type'        => 'textarea',
                    'description' => __('Payment method description that the customer will see on your checkout.', 'wc_ameria_payment_gateway_pretty'),
                    'default'     => __('Thank you for using our website.', 'wc_ameria_payment_gateway_pretty'),
                    'desc_tip'    => true,
                ),
                'ameria_order_id' => array(
                    'title'       => __('Starting Order Id', 'wc_ameria_payment_gateway_pretty'),
                    'type'        => 'text',
                    'description' => __('Starting Order Id must be unique in every single order. And increment after every order.', 'wc_ameria_payment_gateway_pretty'),
                    'default'     => '',
                    'desc_tip'    => true,
                ),

                'client_id'       => array(
                    'title'       => __('Client ID', 'wc_ameria_payment_gateway_pretty'),
                    'type'        => 'text',
                    'description' => __('This is clinet ID setting for using Ameriabank vpos service.', 'wc_ameria_payment_gateway_pretty'),
                    'desc_tip'    => true,
                ),
                'username'        => array(
                    'title'       => __('Username', 'wc_ameria_payment_gateway_pretty'),
                    'type'        => 'text',
                    'description' => __('This is username setting for using Ameriabank vpos service.', 'wc_ameria_payment_gateway_pretty'),
                    'desc_tip'    => true,
                ),
                'password'        => array(
                    'title'       => __('Password', 'wc_ameria_payment_gateway_pretty'),
                    'type'        => 'password',
                    'description' => __('This is password setting for using Ameriabank vpos service.', 'wc_ameria_payment_gateway_pretty'),
                    'desc_tip'    => true,
                ),
            )
            );
        }

        public function process_payment($order_id)
        {

            $order = wc_get_order($order_id);

            $last_insert_id = $this->get_option('ameria_order_id');

            $opt_array = get_option('woocommerce_' . $this->id . '_settings');

            $opt_array['ameria_order_id'] += 1;

            update_option($this->get_option_key(), $opt_array);

            $this->paymentAmount = $order->get_total();

            if ($this->get_option('exchange') == "yes") {
                $rate = $this->get_rate_cba(get_woocommerce_currency());

                if ($rate) {
                    $this->paymentAmount = (int)round($order->get_total() * $rate);
                }
            }

            $_SESSION['cart_total']        = $this->paymentAmount;
            $_SESSION['order_description'] = $order_description = $this->get_option('description');
            $_SESSION['order_id']          = (int) $order_id;

            $url = $this->payment_url . 'api/VPOS/InitPayment';

            $parms['paymentfields']['ClientID']    = $this->get_option('client_id');
            $parms['paymentfields']['Description'] = $order_description;
            $parms['paymentfields']['OrderID']     = $last_insert_id + 1;
            $parms['paymentfields']['Username']    = $this->get_option('username');
            $parms['paymentfields']['Password']    = $this->get_option('password');
            $parms['paymentfields']['Amount']      = $this->paymentAmount;
            $parms['paymentfields']['backURL']     = $this->notify_url;

            /* Send cURL initiating request and get result */

            //url-ify the data for the POST
            $fields_string = '';
            foreach ($parms['paymentfields'] as $key => $value) {$fields_string .= $key . '=' . $value . '&';}
            rtrim($fields_string, '&');

            //open connection
            $ch = curl_init();

            //set the url, number of POST vars, POST data
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, count($parms['paymentfields']));
            curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);

            //execute post
            $result = curl_exec($ch);

            //close connection
            curl_close($ch);

            /* Check if response code is 1 then go further if not, false return */
            $result_json_decoded = json_decode($result);

            if ($result_json_decoded->ResponseCode !== 1) {				
				wc_add_notice(__($result_json_decoded->ResponseMessage, 'wc_ameria_payment_gateway_pretty'), 'error');				
                return false;
            }

            $lang = explode('_', get_locale())[0];

            // Save payment_id for later use in response
            $_SESSION['payment_id'] = $result_json_decoded->PaymentID;

            $ameriaRedirectUri = $this->payment_url . "Payments/Pay?id={$result_json_decoded->PaymentID}&lang={$lang}";

            return array(
                'result'   => 'success',
                'redirect' => $ameriaRedirectUri,
            );
        }

        public function get_rate_cba($currency)
        {

            // if rate is already there you can just take
            if (get_option('ameriabank_pg_rate_cba_' . $currency)) {

                $timestamp = get_option('ameriabank_pg_rate_cba_' . $currency)['timestamp'];

                if (date('Ymd') == date('Ymd', $timestamp)) {
                    return get_option('ameriabank_pg_rate_cba_' . $currency)['rate'];
                }
            }

            // Execute SOAP request
            // $client = new SoapClient("http://api.cba.am/exchangerates.asmx?wsdl", array('soap_version' => SOAP_1_2));
            // $webService = $client->ExchangeRatesLatest($parms);
            // out($webService); die;

            $options = array(
                'soap_version'    => SOAP_1_1,
                'exceptions'      => true,
                'trace'           => 1,
                'wdsl_local_copy' => true,
            );

            $client     = new SoapClient("http://api.cba.am/exchangerates.asmx?wsdl", $options);
            $parms      = array();
            $webService = $client->ExchangeRatesLatest($parms);

            $rates = $webService->ExchangeRatesLatestResult->Rates->ExchangeRate;

            $arr_rate = array();

            foreach($rates as $rate) {
              $arr_rate[$rate->ISO] = $rate->Rate;
            }


            // If couldn't get result try to retrieve the last option
            if (empty($arr_rate)) {

                if (get_option('ameriabank_pg_rate_cba_' . $currency)) {
                    return get_option('ameriabank_pg_rate_cba_' . $currency)['rate'];
                }
                return false;
            }

            // Saving or updating option get
            if (get_option('ameriabank_pg_rate_cba_' . $currency)) {
                update_option('ameriabank_pg_rate_cba_' . $currency, array(
                    'rate'      => $arr_rate[$currency],
                    'timestamp' => time(),
                ));
            } else {
                add_option('ameriabank_pg_rate_cba_' . $currency, array(
                    'rate'      => $arr_rate[$currency],
                    'timestamp' => time(),
                ));
            }


            // Returning just got data
            return $arr_rate[$currency];            

        }

        public function get_rate($currency)
        {

            if (get_option('ameriabank_pg_rate_' . $currency)) {

                $timestamp = get_option('ameriabank_pg_rate_' . $currency)['timestamp'];

                if (date('Ymd') == date('Ymd', $timestamp)) {
                    return get_option('ameriabank_pg_rate_' . $currency)['rate'];
                }

            }

            $url = "http://cb.am/latest.json.php";

            $fields = array();

            //url-ify the data for the POST
            $fields_string = '';
            foreach ($fields as $key => $value) {$fields_string .= $key . '=' . $value . '&';}
            rtrim($fields_string, '&');

            //open connection
            $ch = curl_init();

            //set the url, number of POST vars, POST data
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, count($fields));
            curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);

            //execute post
            $result = curl_exec($ch);

            if (!$result) {

                if (get_option('ameriabank_pg_rate_' . $currency)) {
                    return get_option('ameriabank_pg_rate_' . $currency)['rate'];
                }
                return false;
            }

            //close connection
            curl_close($ch);

            $result_decoded = json_decode($result);

            if (get_option('ameriabank_pg_rate_' . $currency)) {
                update_option('ameriabank_pg_rate_' . $currency, array(
                    'rate'      => $result_decoded->$currency,
                    'timestamp' => time(),
                ));
            } else {
                add_option('ameriabank_pg_rate_' . $currency, array(
                    'rate'      => $result_decoded->$currency,
                    'timestamp' => time(),
                ));
            }

            return $result_decoded->$currency;

        }

    } // Class end

} // Function end
function wc_ameria_payment_gateway_method($gateways)
{
    $gateways[] = 'WC_Ameria_Payment_Gateway_Pretty';
    return $gateways;
}
add_filter('woocommerce_payment_gateways', 'wc_ameria_payment_gateway_method');
