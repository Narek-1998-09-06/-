<?php

namespace Leadin\admin;

/**
 * Handles portal connection to the plugin.
 */
class Connection {
	/**
	 * Connect portal id, domain, name to WordPress options and HubSpot email to user meta data.
	 *
	 * @param Number $portal_id     HubSpot account id.
	 * @param String $portal_name   HubSpot account name.
	 * @param String $portal_domain HubSpot account domain.
	 * @param String $hs_user_email HubSpot user email.
	 */
	public static function connect( $portal_id, $portal_name, $portal_domain, $hs_user_email ) {
		self::disconnect();

		add_option( 'leadin_portalId', $portal_id );
		add_option( 'leadin_account_name', $portal_name );
		add_option( 'leadin_portal_domain', $portal_domain );
		$wp_user    = wp_get_current_user();
		$wp_user_id = $wp_user->ID;
		add_user_meta( $wp_user_id, 'leadin_email', $hs_user_email );
	}

	/**
	 * Removes portal id and domain from the WordPress options.
	 */
	public static function disconnect() {
		delete_option( 'leadin_portalId' );
		delete_option( 'leadin_account_name' );
		delete_option( 'leadin_portal_domain' );
		$users = get_users( array( 'fields' => array( 'ID' ) ) );
		foreach ( $users as $user ) {
			delete_user_meta( $user->ID, 'leadin_email' );
		}

		add_option( 'leadin_did_disconnect', true );
	}
}
