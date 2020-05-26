<?php

namespace Leadin\wp;

/**
 * Static function that wraps the WordPress user functions.
 */
class User {
	/**
	 * Return the first role of the current user. If unauthenticated, return 'visitor'.
	 */
	public static function get_role() {
		global $current_user;

		if ( is_user_logged_in() ) {
			$user_roles = $current_user->roles;
			$user_role  = array_shift( $user_roles );
		} else {
			$user_role = 'visitor';
		}

		return $user_role;
	}

	/**
	 * Return true if the current user has the `manage_options` capability.
	 */
	public static function is_admin() {
		return current_user_can( 'manage_options' );
	}
}
