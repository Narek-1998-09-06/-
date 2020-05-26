<?php

namespace Leadin\admin\api;

use Leadin\admin\Connection;
use Leadin\utils\Validator;
use Leadin\utils\RequestUtils;

/**
 * Registration API, used to store the portal id and the domain as a WordPress option.
 */
class RegistrationApi {
	/**
	 * Registration API constructor. Adds the ajax hooks.
	 */
	public function __construct() {
		add_action( 'wp_ajax_leadin_registration_ajax', 'Leadin\admin\api\ApiMiddlewares::validate_nonce', 1 );
		add_action( 'wp_ajax_leadin_registration_ajax', 'Leadin\admin\api\ApiMiddlewares::admin_only', 2 );
		add_action( 'wp_ajax_leadin_registration_ajax', array( $this, 'run' ), 3 );
	}

	/**
	 * Registration API runner. It validates the portal id and domain and stores them as WordPress options.
	 */
	public function run() {
		$request_body  = file_get_contents( 'php://input' );
		$data          = json_decode( $request_body, true );
		$portal_id     = intval( $data['portalId'] );
		$portal_domain = $data['domain'];
		$portal_name   = $data['accountName'];
		$hs_user_email = $data['userEmail'];

		if ( empty( $portal_id ) ) {
			RequestUtils::send_error( 'Registration missing required fields' );
		}

		if ( empty( $portal_domain ) || ! Validator::is_valid_domain( $portal_domain ) ) {
			RequestUtils::send_error( 'Invalid domain in submission' );
		}

		Connection::connect( $portal_id, $portal_name, $portal_domain, $hs_user_email );

		RequestUtils::send_message( 'Success' );
	}
}
