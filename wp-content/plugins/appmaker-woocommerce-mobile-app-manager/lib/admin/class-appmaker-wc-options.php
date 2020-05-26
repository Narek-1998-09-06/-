<?php

class APPMAKER_WC_Options {
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
		add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
		add_action( 'admin_init', array( $this, 'page_init' ) );
	}

	/**
	 * Add options page
	 */
	public function add_plugin_page() {
		// This page will be under "WooCommerce".
		add_submenu_page(
			'woocommerce',
			'Appmaker WooCommerce Mobile App Manager Settings',
			'Appmaker App Settings',
			'manage_options',
			'appmaker-wc-admin',
			array( $this, 'create_admin_page' )
		);
	}

	/**
	 * Options page callback
	 */
	public function create_admin_page() {
		$error = false;
		/** Moved saving from options.php to here to avoid logout issue of jetpack */
		if ( isset( $_POST['appmaker_wc_settings'] ) ) {
			$options = $this->sanitize( $_POST['appmaker_wc_settings'] );
			if ( is_array( $options ) && count( $options ) === 3 && ! empty( $options['project_id'] ) && ! empty( $options['api_key'] ) && ! empty( $options['api_secret'] ) ) {
				update_option( 'appmaker_wc_settings', $options, false );
				wp_redirect( 'admin.php?page=appmaker-wc-admin&tab=step3' );
			} else {
				 $error = true;
			}
		}
		$this->options = get_option( 'appmaker_wc_settings' );
		?>
		<div>
			<!--<h2>Appmaker Settings</h2>-->
			 <?php require_once dirname( __FILE__ ) . '/class-appmaker-wc-admin-page.php'; ?>
		</div>
		<?php
	}

	/**
	 * Register and add settings
	 */
	public function page_init() {
		add_action( 'admin_head-woocommerce_page_appmaker-wc-admin', array( $this, 'admin_hook_css' ) );
		register_setting(
			'appmaker_wc_key_options',
			'appmaker_wc_settings',
			array( $this, 'sanitize' )
		);

		// add_settings_section(
		// 	'appmaker_wc_setting_section',
		// 	__( 'Access Key', 'appmaker-woocommerce-mobile-app-manager' ),
		// 	array( $this, 'print_section_info' ),
		// 	'appmaker-wc-setting-admin'
		// );

		// add_settings_section(
		// 	'access_key',
		// 	'',
		// 	array( $this, 'access_key_callback' ),
		// 	'appmaker-wc-setting-admin',
		// 	'appmaker_wc_setting_section'
		// );

		add_settings_section(
			'appmaker_wc_setting_section',
			__( 'API Credentials', 'appmaker-woocommerce-mobile-app-manager' ),
			array( $this, 'print_section_info' ),
			'appmaker-wc-setting-admin'
		);

		add_settings_field(
			'project_id',
			__( 'Project ID', 'appmaker-woocommerce-mobile-app-manager' ),
			array( $this, 'project_id_callback' ),
			'appmaker-wc-setting-admin',
			'appmaker_wc_setting_section'
		);

		add_settings_field(
			'api_key',
			__( 'API Key', 'appmaker-woocommerce-mobile-app-manager' ),
			array( $this, 'api_key_callback' ),
			'appmaker-wc-setting-admin',
			'appmaker_wc_setting_section'
		);

		add_settings_field(
			'api_secret',
			__( 'API Secret', 'appmaker-woocommerce-mobile-app-manager' ),
			array( $this, 'api_secret_callback' ),
			'appmaker-wc-setting-admin',
			'appmaker_wc_setting_section'
		);

	}

	public function admin_hook_css() {
		?>
		<style>            
			html,
			body,
			div,
			span,
			h1,
			h2,
			h3,
			h4,
			h5,
			h6,
			p,
			a,
			b,
			u,
			i,
			ul,
			li,
			form {
				margin: 0;
				padding: 0;
				border: 0;
				vertical-align: baseline;
				background: transparent;
				font-weight: normal;
				text-decoration: none;
				outline: none;
			}

			* {
				-moz-box-sizing: border-box;
				-webkit-box-sizing: border-box;
				box-sizing: border-box;
			}

			ol,
			ul {
				list-style: none;
			}

			nav,
			footer {
				display: block;
			}

			a {
				color: #2268FF;
			}

			a:hover,
			.submit:hover {
				filter: alpha(opacity=85);
				-moz-opacity: 0.85;
				-khtml-opacity: 0.85;
				opacity: 0.85;
			}

			p {
				line-height: 1.6em;
				font-size: 16px;
			}

			.hidden {
				display: none !important;
			}

			.wrapper {
				width: 90%;
				margin: 0 auto;
				max-width: 1200px;
			}

			.appmaker-settings {
				font-family: "Poppins", sans-serif;
				box-sizing: border-box;
				background-color: #F5F7F9;
				width: 100%;
				padding-bottom:15px;
			}

			.appmaker-settings nav {
				background-color: #fff;
				display: flex;
				justify-content: space-between;
				padding: 16px 0;
			}

			.appmaker-settings nav>div {
				display: flex;
				justify-content: space-between;
			}

			.appmaker-settings h3 {
				margin: auto 0;
				line-height: 30px;
				display: flex;
			}

			.appmaker-settings h3 img {
				margin-right: 10px;
			}

			.appmaker-settings ul {
				display: flex;
				margin: auto 0;
			}

			.appmaker-settings li {
				margin-left: 15px;
			}

			.appmaker-button {
				text-transform: uppercase;
				padding: 8px 16px;
				border-radius: 6px;
				border: none;
				background-color: #113484;
				color: #fff;
				font-size: 16px;
			}

			.appmaker-button.border {
				border: 1px solid #113484 !important;
				background-color: unset !important;
				color: #113484 !important;
			}

			.connect-app-box {
				background-color: #fff;
				width: 50%;
				padding: 32px;
				border-radius: 8px;
				margin: 20px auto;
				display: block;
			}

			.connect-app-box h2 {
				color: #2268FF;
				font-weight: bold;
				text-transform: uppercase;
			}

			.connect-app-box p {
				margin-top: 16px;
			}

			.connect-app-box img {
				display: block;
				margin: 30px auto;
				width: 50%;
			}

			.text-center {
				text-align: center;
			}

			.connect-app-box .appmaker-button {
				background-color: #2268FF;
				display: block;
			}

			.connect-app-box button.appmaker-button {
				width: 100%;
				padding: 13px;
			}

			.connect-app-box form {
				width: 100%;
				margin: 20px 0;
			}

			.connect-app-box textarea,
			.connect-app-box input {
				width: 100%;
				padding: 8px;
				margin-bottom: 5px;
				border-radius: 6px;
				border: 1px solid #2268FF;
				font-size: 16px;
				resize: unset;
				overflow: auto
			}

			.connect-app-box input {
				padding: 4px 8px !important;
			}
			.text-danger {
				color: #ff0000;
			}
			.warning, .error, .updated, .update-nag, .notice {display:none !important;}
		</style>
		<?php
	}
	/**
	 * Sanitize each setting field as needed.
	 *
	 * @param array $input Contains all settings fields as array keys.
	 *
	 * @return array
	 */
	public function sanitize( $input ) {
		$new_input = array();
		if ( isset( $input['project_id'] ) ) {
			$new_input['project_id'] = sanitize_text_field( $input['project_id'] );
			if ( ! is_numeric( $new_input['project_id'] ) ) {
				$new_input['project_id'] = '';
			}
		}

		if ( isset( $input['api_key'] ) ) {
			$new_input['api_key'] = sanitize_text_field( $input['api_key'] );
		}

		if ( isset( $input['api_secret'] ) ) {
			$new_input['api_secret'] = sanitize_text_field( $input['api_secret'] );
		}

		if ( isset( $input['access_key'] ) ) {
			$access_key  = base64_decode( sanitize_text_field( $input['access_key'] ) );
			$input_array = explode( ':', $access_key );

			if ( isset( $input_array[0] ) && isset( $input_array[1] ) && isset( $input_array[1] ) && isset( $input_array[2] ) ) {
				$new_input['project_id'] = $input_array[0];
				if ( ! is_numeric( $new_input['project_id'] ) ) {
					$new_input['project_id'] = '';
				}
				$new_input['api_key']    = $input_array[1];
				$new_input['api_secret'] = $input_array[2];

			}
			// if(isset($input_array[1])){
			//     $new_input['api_key'] = $input_array[1];
			// }

			// if(isset($input_array[2])){
			//     $new_input['api_secret'] = $input_array[2];
			// }

		}

		return $new_input;
	}

	/**
	 * Print the Section text
	 */
	public function print_section_info() {
		printf( '<p>Enter the access key given from <a target="_blank" href="http://beta.manage.appmaker.xyz">Appmaker dashboard</a> to connect the store with app.</p>' );
	}

	/**
	 * Get the access key
	 */
	public function access_key_callback() {

		$project_id = isset( $this->options['project_id'] ) ? esc_attr( $this->options['project_id'] ) : '';
		$api_key    = isset( $this->options['api_key'] ) ? esc_attr( $this->options['api_key'] ) : '';
		$api_secret = isset( $this->options['api_secret'] ) ? esc_attr( $this->options['api_secret'] ) : '';

		$access_key         = $project_id . ':' . $api_key . ':' . $api_secret;
		$encoded_access_key = base64_encode( $access_key );
		printf(
			'<textarea  id="access_key" name="appmaker_wc_settings[access_key]" rows="4" placeholder="Paste/Enter access key" required autofocus>%s</textarea>',
			$encoded_access_key
		);
	}


	/**
	 * Get the settings option array and print one of its values
	 */
	public function project_id_callback() {
		printf(
			'<input type="text" id="project_id" name="appmaker_wc_settings[project_id]" value="%s" />',
			isset( $this->options['project_id'] ) ? esc_attr( $this->options['project_id'] ) : ''
		);
	}

	/**
	 * Get the settings option array and print one of its values
	 */
	public function api_key_callback() {
		printf(
			'<input type="text" id="api_key" name="appmaker_wc_settings[api_key]" value="%s" />',
			isset( $this->options['api_key'] ) ? esc_attr( $this->options['api_key'] ) : ''
		);
	}

	/**
	 * Get the settings option array and print one of its values
	 */
	public function api_secret_callback() {
		printf(
			'<input type="text" id="api_secret" name="appmaker_wc_settings[api_secret]" value="%s" />',
			isset( $this->options['api_secret'] ) ? esc_attr( $this->options['api_secret'] ) : ''
		);
	}

}

new APPMAKER_WC_Options();
