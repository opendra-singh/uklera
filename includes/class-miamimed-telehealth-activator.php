<?php

class Miamimed_Telehealth_Activator {

	public static function activate() {
		global $wp_roles;
		if ( ! isset( $wp_roles ) )
			$wp_roles = new WP_Roles();

		// Renaming Administrator to Super Admin
		$wp_roles->roles['administrator']['name'] = 'Super Admin';
		$wp_roles->role_names['administrator'] = 'Super Admin';

		$google_auth = new GoogleAuthenticator;
		array_map(function($user) use ($google_auth) {
			$secret = $google_auth->createSecret(32);
			// Updaing 2FA Authentication meta key
			update_user_meta($user->ID, 'miamimed_telehealth_2fa', [
				'enable' => false,
				'secret' => $secret,
			]);
			// Updaing user activation meta key
			update_user_meta($user->ID, "miamimed_telehealth_activate_user", json_encode([
				'status' => true,
			]));
		}, get_users());

		function miamimed_telehealth_inser_page_error($error_message = ""){
			$error_message = !empty($error_message) ? $error_message : "Error Creating Page!";
			?>
			<div class="error notice">
				<p><?= $error_message;?></p>
			</div>
			<?php
		}

		$page_id = wp_insert_post([
			'post_title'    => "Questionaries",
			'post_content'  => "[miamimed_telehealth_questionaries_shortcode]",
			'post_status'   => 'publish',
			'post_type'     => 'page'
		]);

		if (is_wp_error($page_id)) {
			// An error occurred while creating the page
			$error_message = $page_id->get_error_message();
			unset( $_GET['activate'] );
			deactivate_plugins( plugin_basename( __FILE__ ) );
			add_action( 'admin_notices', miamimed_telehealth_inser_page_error($error_message) );
		} else {
			// Page created successfully
			update_option("miamimed_telehealth_questionaries_shortcode_page_id", $page_id);			
		}

		// Addin new role : Admin
		add_role("admin", "Admin", [
			'read' => true,
			'level_0' => true,
			'level_1' => true,
			'level_2' => true,
			'level_3' => true,
			'level_4' => true,
			'level_5' => true,
			'level_6' => true,
			'level_7' => true,
			'level_8' => true,
			'level_9' => true,
			'level_10' => true,
			'edit_posts' => true,
			'edit_pages' => true,
			'edit_shop_order' => true,
			'edit_others_posts' => true,
			'edit_others_pages' => true,
			'edit_published_posts' => true,
			'edit_published_pages' => true,
			'upload_files' => true,
			'manage_options' => false,
			'publish_posts' => true,
			'edit_private_posts' => true,
			'publish_pages' => true,
			'edit_private_pages' => true,
			'edit_products' => true,
			'edit_others_products' => true,
			'publish_products' => true,
			'edit_published_products' => true,
			'edit_private_products' => true,
			'edit_shop_orders' => true,
			'edit_others_shop_orders' => true,
			'publish_shop_orders' => true,
			'edit_published_shop_orders' => true,
			'edit_private_shop_orders' => true,
			'delete_posts' => true,
			'delete_others_posts' => true,
			'delete_published_posts' => true,
			'delete_private_posts' => true,
			'delete_pages' => true,
			'delete_others_pages' => true,
			'delete_published_pages' => true,
			'delete_private_pages' => true,
			'delete_products' => true,
			'delete_others_products' => true,
			'delete_published_products' => true,
			'delete_private_products' => true,
			'delete_shop_orders' => true,
			'delete_others_shop_orders' => true,
			'delete_published_shop_orders' => true,
			'delete_private_shop_orders' => true,
			'read_private_posts' => true,
			'read_private_pages' => true,
			'read_private_products' => true,
			'read_private_shop_orders' => true,
			'add_users' => true,
			'create_users' => true,
			'delete_users' => true,
			'edit_users' => true,
			'list_users' => true,
			'promote_users' => true,
			'remove_users' => true,
			'assign_shop_payment_terms' => true,
			'create_shop_orders' => true,
			'delete_others_shop_payments' => true,
			'delete_private_shop_payments' => true,
			'delete_published_shop_payments' => true,
			'delete_shop_payments' => true,
			'edit_private_shop_payments' => true,
			'edit_published_shop_payments' => true,
			'edit_shop_payments' => true,
			'edit_shop_payment_terms' => true,
			'export_shop_payments' => true,
			'publish_shop_payments' => true,
			'read_private_shop_payments' => true,
			'view_shop_payment_stats' => true,
			'delete_product' => true,
			'delete_shop_order' => true,
			'edit_product' => true,
			'read_product' => true,
			'read_shop_order' => true,
			'edit_others_shop_payments' => true,
			'import_shop_payments' => true,
			'manage_shop_payment_terms' => true,
			'manage_woocommerce' => true,
		]);

		// Addin new role : Patient
		add_role("patient", "Patient", [
			'edit_pages' => true,
			'delete_posts' => true,
			'delete_published_posts' => true,
			'edit_posts' => true,
			'edit_published_posts' => true,
			'publish_posts' => true,
			'read' => true,
			'upload_files' => true,
		]);

		// Addin new role : Dermatologist
		add_role("dermatologist", "Dermatologist", [
			'edit_pages' => true,
			'delete_posts' => true,
			'delete_published_posts' => true,
			'edit_posts' => true,
			'edit_published_posts' => true,
			'publish_posts' => true,
			'read' => true,
			'upload_files' => true,
		]);

		update_option("miamimed_telehealth_access_token", "UBKK1jw59g6Tgm1B5mhboFsdE2KRsa4VqD2m2fyG|bm9VsIkYlf5jDiNsPGiNKKk8nra6b22Gi0jj7Fva");
		
		update_option("miamimed_questionary_states", wp_json_encode([
			"Alabama",
			"Alaska",
			"Arizona",
			"Arkansas",
			"California",
			"Colorado",
			"Connecticut",
			"District of Columbia",
			"Delaware",
			"Florida",
			"Georgia",
			"Hawaii",
			"Idaho",
			"Illinois",
			"Indiana",
			"Iowa",
			"Kansas",
			"Kentucky",
			"Louisiana",
			"Maine",
			"Maryland",
			"Massachusetts",
			"Michigan",
			"Minnesota",
			"Mississippi",
			"Missouri",
			"Montana",
			"Nebraska",
			"Nevada",
			"New Hampshire",
			"New Jersey",
			"New Mexico",
			"New York",
			"North Carolina",
			"North Dakota",
			"Ohio",
			"Oklahoma",
			"Oregon",
			"Pennsylvania",
			"Rhode Island",
			"South Carolina",
			"South Dakota",
			"Tennessee",
			"Texas",
			"Utah",
			"Vermont",
			"Virginia",
			"Washington",
			"West Virginia",
			"Wisconsin",
			"Wyoming",
		]));
	}

}



