<?php

namespace Leadin\admin\api;

use Leadin\admin\Connection;
use Leadin\utils\RequestUtils;

/**
 * Disconnect Api, used to clean portal id and domain from the WordPress options.
 */
class DisconnectApi {
	/**
	 * Disconnect API constructor. Adds the ajax hooks.
	 */
	public function __construct() {
		add_action( 'wp_ajax_leadin_disconnect_ajax', 'Leadin\admin\api\ApiMiddlewares::validate_nonce', 1 );
		add_action( 'wp_ajax_leadin_disconnect_ajax', 'Leadin\admin\api\ApiMiddlewares::admin_only', 2 );
		add_action( 'wp_ajax_leadin_disconnect_ajax', array( $this, 'run' ), 3 );
	}

	/**
	 * Disconnect Api runner. Removes portal id and domain from the WordPress options.
	 */
	public function run() {
		if ( get_option( 'leadin_portalId' ) ) {
			Connection::disconnect();
			RequestUtils::send_message( 'Success' );
		} else {
			RequestUtils::send_message( 'No leadin_portal_id found, cannot disconnect' );
		}
	}
}
