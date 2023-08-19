<?php

class Miamimed_Telehealth_Deactivator {

	public static function deactivate() {
		global $wp_roles;
		if ( ! isset( $wp_roles ) )
			$wp_roles = new WP_Roles();

		$wp_roles->roles['administrator']['name'] = 'Administrator';
		$wp_roles->role_names['administrator'] = 'Administrator';

		array_map(function($user) {
			// Updaing 2FA Authentication meta key
			delete_user_meta($user->ID, 'miamimed_telehealth_2fa');
			// Updaing user activation meta key
			delete_user_meta($user->ID, "miamimed_telehealth_activate_user");
		}, get_users());

		// Delete the page
		wp_delete_post(get_option("miamimed_telehealth_questionaries_shortcode_page_id", true), true); // Set the second parameter to true to force delete the page

		remove_role("admin");
		remove_role("patient");
		remove_role("dermatologist");

		delete_option("miamimed_telehealth_access_token");
		delete_option("miamimed_telehealth_questionaries_shortcode_page_id");

	}
}
