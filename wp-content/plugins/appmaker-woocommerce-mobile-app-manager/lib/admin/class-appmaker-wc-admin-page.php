<div class="appmaker-settings">    
	<nav>
		<div class="wrapper">
			<h3>
				<img src="https://storage.googleapis.com/stateless-appmaker-pages-wp//2020/02/appmaker-logo-blue.svg"
					alt="" />Settings
			</h3>
			<ul>
				<li><a target="_blank" href="https://appmaker.xyz/woocommerce?utm_source=woocommerce-plugin&utm_medium=top-bar&utm_campaign=after-plugin-install" class="appmaker-button">Explore features</a></li>
				<li><a target="_blank" href="http://appmaker.xyz/book-a-demo/?ref=plugin-install" class="appmaker-button border">Book a demo</a></li>
			</ul>
		</div>
	</nav>
	<!-- First step. show after activating plugin -->
	<?php

	$this->options = get_option( 'appmaker_wc_settings' );
	if ( ! empty( $this->options['project_id'] ) ) {
		$auto_login = false;
		$button_name     = 'Manage App';
		$manage_url_base = 'https://beta.manage.appmaker.xyz';
		$manage_url      = $manage_url_base . '/apps/' . $this->options['project_id'] . '/?utm_source=woocommerce-plugin&utm_medium=side-bar&utm_campaign=after-plugin-install';
		// $manage_url = site_url( '?rest_route=/appmaker-wc/v1/manage-login&url=' . $manage_url_base . '&return_to=' . '/apps/' . $this->options['project_id'] );
		ob_start();
		?>
			<div class="wrapper" style="text-align: center;"> 
						<div class="connect-app-box">
							<h2>Your store is now in sync with app</h2>
							<img src="https://storage.googleapis.com/stateless-appmaker-pages-wp//2020/02/Group-604.svg" alt="" />
							<div class="text-center">
								<a target="_blank" href="<?php echo $manage_url; ?>" class="appmaker-button">Go to app dashboard</a>
								<p><a href="admin.php?page=appmaker-wc-admin&tab=step2">Edit Access Key</a></p>
							</div>
						</div>
			</div>
		<?php
		$html = ob_get_clean();

	}

	if ( empty( $this->options['project_id'] ) && ( empty( $_GET['tab'] ) || 'step3' === $_GET['tab'] ) ) {
		?>
			<div class="wrapper" style="text-align:center">
				<div class="connect-app-box">
					<h2>Thank you for choosing us</h2>
					<p></p>
					<img src="https://storage.googleapis.com/stateless-appmaker-pages-wp/2020/02/29366dd3-c2cb0d96-woocommerce-hero-4-1024x846-1.png"
						alt="" />
					<div class="text-center">
						<a href="admin.php?page=appmaker-wc-admin&tab=step2" class="appmaker-button">connect store with app</a>
						or 
						<a target="_blank" href="https://create.appmaker.xyz/?utm_source=woocommerce-plugin&utm_medium=button&utm_campaign=after-plugin-install" class="appmaker-button">Create new app</a>
						<p>Know more about <a target="_blank" href="https://appmaker.xyz/woocommerce?utm_source=woocommerce-plugin&utm_medium=top-bar&utm_campaign=after-plugin-install">appmaker.xyz</a></p>
					</div>
				</div>
			</div>
			<!--Second step. on clicking button above will redirect to app dashboard to get access key and also advance to this step -->
		<?php
	} elseif ( ! empty( $this->options['project_id'] ) && ( empty( $_GET['tab'] ) || 'step3' === $_GET['tab'] ) ) {
						echo $html;
	}
	if ( ! empty( $_GET['tab'] ) && 'step2' === $_GET['tab'] ) {
		?>

			<div class="wrapper">

						<div class="connect-app-box">                        
						<form  method="post"  action="admin.php?page=appmaker-wc-admin&tab=step2">                        
			<?php
			// This prints out all hidden setting fields.
			settings_fields( 'appmaker_wc_key_options' );
			do_settings_sections( 'appmaker-wc-setting-admin' );
			if ( $error ) {
				printf( '<div class="text-danger"> You must fill in all of the fields. </div>' );
				// printf( '<div class="text-danger"> Incorrect Access Key </div>' );
			}
			submit_button( 'Activate' );
			?>
						</form>
					</div>
				</div>
	<!--Final step/Success screen. Button will redirect to corresponding app-->
			<?php
	}
	?>
</div>
