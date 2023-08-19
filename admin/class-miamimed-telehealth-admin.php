<?php

class Miamimed_Telehealth_Admin
{
	private $plugin_name;
	private $version;
	private $google_auth;

	public function __construct($plugin_name, $version)
	{
		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->google_auth = new GoogleAuthenticator;

		if (!wp_next_scheduled('miamimed_change_user_key_hook')) {
			wp_schedule_event(time(), 'miamimed_change_user_key_event', 'miamimed_change_user_key_hook');
		}
	}

	public function enqueue_styles($page_id)
	{
		if (in_array($page_id, [
			"toplevel_page_questionnaires",
			"toplevel_page_miamimed-telehealth-user-role",
			"admin_page_miamimed_login_security",
		])) {
			wp_enqueue_style('miamimed_login_security_css', plugin_dir_url(__FILE__) . 'css/miamimed-telehealth-login-2fa.css', array(), $this->version, 'all');
		}
		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/miamimed-telehealth-admin.css', array(), $this->version, 'all');
	}

	public function enqueue_scripts($page_id)
	{
		if (in_array($page_id, [
			"toplevel_page_questionnaires",
			"toplevel_page_miamimed-telehealth-user-role",
			"admin_page_miamimed_login_security",
		])) {
			wp_enqueue_script('miamimed-login-security', plugin_dir_url(__FILE__) . 'js/miamimed-telehealth-login-2fa.js', array('jquery'), $this->version, false);
			wp_localize_script(
				'miamimed-login-security',
				'ajax_object',
				array(
					'ajax_url' => admin_url('admin-ajax.php'),
					'nonce' => wp_create_nonce("miamimed-telehealth"),
				)
			);
		}
		wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/miamimed-telehealth-admin.js', array('jquery'), $this->version, false);
		wp_localize_script(
			$this->plugin_name,
			'ajax_object',
			array(
				'ajax_url' => admin_url('admin-ajax.php'),
				'nonce' => wp_create_nonce("miamimed-telehealth"),
			)
		);
	}

	public function miamimed_telehealth_activate_2fa()
	{
		if (isset($_POST['nonce']) && !empty($_POST['nonce'])) {
			if (wp_verify_nonce($_POST['nonce'], 'miamimed-telehealth')) {
				if (
					isset($_POST['secret']) && !empty($_POST['secret']) &&
					isset($_POST['user_id']) && !empty($_POST['user_id']) &&
					isset($_POST['code']) && !empty($_POST['code'])
				) {
					if ($this->google_auth->verifyCode($_POST['secret'], $_POST['code'])) {
						$meta = get_user_meta($_POST['user_id'], "miamimed_telehealth_2fa", true);
						$meta['enable'] = true;
						update_user_meta($_POST['user_id'], "miamimed_telehealth_2fa", $meta);
						$response['status'] = true;
						$response['message'] = 'Your Two Factor Authentication was setup successfully.';
					} else {
						$response['status'] = false;
						$response['message'] = 'Invalid authentication code. Please try again.';
					}
				} else {
					$response['status'] = false;
					$response['message'] = 'Incorrect or Invalid Data!';
				}
			} else {
				$response['status'] = false;
				$response['message'] = 'Nonce Verification Failed!';
			}
		} else {
			$response['status'] = false;
			$response['message'] = 'Nonce Failure!';
		}
		echo wp_json_encode($response);
		wp_die();
	}

	public function miamimed_telehealth_deactivate_2fa()
	{
		if (isset($_POST['nonce']) && !empty($_POST['nonce'])) {
			if (wp_verify_nonce($_POST['nonce'], 'miamimed-telehealth')) {
				if (
					isset($_POST['user_id']) && !empty($_POST['user_id'])
				) {
					$secret = $this->google_auth->createSecret(32);
					update_user_meta($_POST['user_id'], 'miamimed_telehealth_2fa', [
						'enable' => false,
						'secret' => $secret,
					]);
					$response['status'] = true;
					$response['message'] = 'Two Factor Authentication Deactivated Successfully!';
					$response['secret'] = $secret;
					$response['url'] = $this->google_auth->getQRCodeGoogleUrl(get_user_by('id', $_POST['id'])->user_login, $secret);
				} else {
					$response['status'] = false;
					$response['message'] = 'Incorrect or Invalid Data!';
				}
			} else {
				$response['status'] = false;
				$response['message'] = 'Nonce Verification Failed!';
			}
		} else {
			$response['status'] = false;
			$response['message'] = 'Nonce Failure!';
		}
		echo wp_json_encode($response);
		wp_die();
	}

	public function remove_admin_menu()
	{
		if (is_user_logged_in()) {
			$current_user = wp_get_current_user();
			$user_roles = $current_user->roles;

			// Get the first role of the user (assuming they only have one)
			$user_role = array_shift($user_roles);

			switch ($user_role) {
				case 'admin':
					add_action('admin_menu', [$this, 'custom_admin_menus']);
					break;
			}

			// if (current_user_can('manage_options')) {
			// 	//Super Admin
			// 	add_submenu_page("options-general.php", "Login Security", "Login Security", "manage_options", "miamimed_login_security", [$this, "miamimed_2fa_login"]);
			// }
		}
	}

	public function miamimed_2fa_login()
	{
		?>
		<div class="nk-header is-light p-3">
			<h4>Two-Factor Authentication</h4>
			<p>Two-Factor Authentication, or 2FA, significantly improves login security for your website. 2FA works with a number of TOTP-based apps like Google Authenticator, FreeOTP, and Authy.</p>
		</div>

		<div class="card-bordered card-preview">
			<div class="card-inner">
				<ul class="nav nav-tabs nav-tabs-s2 mt-n2" role="tablist">
					<li class="nav-item" role="presentation"><a id="miamimed-telehealth-login-2fa-activate" class="nav-link active" data-bs-toggle="tab" href="#miamimed_activate_tab_panel" aria-selected="true" role="tab">Activate</a></li>
					<li class="nav-item" role="presentation"><a id="miamimed-telehealth-login-2fa-deactivate" class="nav-link" data-bs-toggle="tab" href="#miamimed_deactivate_tab_panel" aria-selected="false" tabindex="-1" role="tab">Deactivate</a></li>
				</ul>
				<div class="tab-content text-center">
					<div class="tab-pane active" id="miamimed_activate_tab_panel" role="tabpanel">
						<div id="miamimed_login_security_activate_panel" class="container mt-5 card card-bordered">
							<div class="row">
								<div class="col-md-3 m-4">
									<h5 class="h5">1. Select User</h5>
									<div class="form-control-wrap my-5">
										<div class="form-control-select">
											<select class="form-control" id="miamimed_users_dropdown_head_activate">
												<?php
												array_map(function ($user) {
												?>
												<option class="miamimed_users_dropdown_activate" <?= get_current_user_id() == $user->ID ? "selected" : ''?> value="<?= $user->ID ?>"><?= $user->data->user_login?></option>
												<?php
												}, get_users());
												?>
											</select>
										</div>
									</div>
								</div>

								<div class="col-md-3 m-4">
									<h5 class="h5">2. Scan Code or Enter Key</h5>
									<?php
									array_map(function ($user) {
										$meta = get_user_meta($user->ID, "miamimed_telehealth_2fa", true);
										$secret = is_array($meta) && !empty($meta) ? $meta['secret'] : '';
										$class = get_current_user_id() != $user->ID ? "d-none" : '';
									?>
										<div id="<?= $user->ID; ?>" class="gallery mt-4 miamimed_login_qr_img <?= $class ?>">
											<a class="gallery-image popup-image" href="/demo4/images/stock/a.jpg">
												<img class="w-75 rounded-top" src="<?= $this->google_auth->getQRCodeGoogleUrl($user->user_login, $secret); ?>">
											</a>
											<div class="gallery-body card-inner align-center justify-between flex-wrap g-2">
												<input type="text" class="form-control" name="miamimed_login_2fa_secret_key" id="miamimed_login_2fa_secret_key" value="<?= $secret ?>" disabled="disabled">
											</div>
										</div>
									<?php
									}, get_users());
									?>
								</div>

								<div class="col-md-3 m-4 text-start">
									<h5 class="h5">3. Enter Code from Authenticator App</h5>
									<p class="mt-4">Enter the code from your authenticator app below to verify and activate two-factor authentication for the selected account.</p>
									<input class="form-control" type="number" name="miamimed_telehealth_2fa_verification_code" placeholder="123456" id="miamimed_telehealth_2fa_verification_code">
								</div>
							</div>
							<button id="miamimed_login_security_submit" class="btn btn-primary">Submit</button>
						</div>
					</div>

					<div class="tab-pane" id="miamimed_deactivate_tab_panel" role="tabpanel">
						<div id="miamimed_login_security_deactivate_panel" class="container mt-5 card card-bordered">
							<div class="row">
								<div class="col-md-3 m-4">
									<h5 class="h5">1. Select User</h5>

									<div class="form-control-wrap my-5">
										<div class="form-control-select">
											<select class="form-control" id="miamimed_users_dropdown_head_deactivate">
												<?php
												array_map(function ($user) {
												?>
												<option class="miamimed_users_dropdown_activate" <?= get_current_user_id() == $user->ID ? "selected" : ''?> value="<?= $user->ID ?>"><?= $user->data->user_login?></option>
												<?php
												}, get_users());
												?>
											</select>
										</div>
									</div>
								</div>

								<div class="col-md-3 m-4">
									<h5 class="h5">2. Deactivate</h5>
									<a id="miamimed_login_security_deactivate_submit" class="btn btn-outline-danger mt-4">Deactivate</a>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php
	}

	public function custom_admin_menus()
	{
		?>
		<style>
			#toplevel_page_woocommerce {
				display: none;
			}
		</style>
		<?php
		add_menu_page(
			'Orders',    // Page title
			'Orders',    // Menu title
			'manage_woocommerce',  // Capability required to access the menu item
			'shop_order',    // Menu slug (should be unique)
			function () {
			},  // Function that will display the menu page
			'dashicons-cart',  // Icon URL or Dashicons class name
			57                  // Position of the menu item
		);
		add_menu_page(
			'Payment',    // Page title
			'Payment',    // Menu title
			'manage_woocommerce',  // Capability required to access the menu item
			'wc-settings&tab=checkout',    // Menu slug (should be unique)
			function () {
			},  // Function that will display the menu page
			'dashicons-money-alt',  // Icon URL or Dashicons class name
			58                  // Position of the menu item
		);
		add_menu_page(
			'Shipping',    // Page title
			'Shipping',    // Menu title
			'manage_woocommerce',  // Capability required to access the menu item
			'wc-settings&tab=shipping',    // Menu slug (should be unique)
			function () {
			},  // Function that will display the menu page
			'dashicons-airplane',  // Icon URL or Dashicons class name
			58	                  // Position of the menu item
		);
		add_menu_page(
			'Questionnaires',    // Page title
			'Questionnaires',    // Menu title
			'manage_woocommerce',  // Capability required to access the menu item
			'questionnaires',    // Menu slug (should be unique)
			[$this, "miamimed_questionaries_menu_content"],  // Function that will display the menu page
			'dashicons-editor-help',  // Icon URL or Dashicons class name
			75                  // Position of the menu item
		);
		add_menu_page(
			'Reports',    // Page title
			'Reports',    // Menu title
			'manage_woocommerce',  // Capability required to access the menu item
			'reports',    // Menu slug (should be unique)
			function () {
			},  // Function that will display the menu page
			'dashicons-media-document',  // Icon URL or Dashicons class name
			75                  // Position of the menu item
		);
		add_menu_page(
			'Logout',    // Page title
			'Logout',    // Menu title
			'manage_woocommerce',  // Capability required to access the menu item
			'logout',    // Menu slug (should be unique)
			'',  // Function that will display the menu page
			'dashicons-exit',  // Icon URL or Dashicons class name
			99                  // Position of the menu item
		);
		add_menu_page(
			'FAQ',    // Page title
			'FAQ',    // Menu title
			'manage_woocommerce',  // Capability required to access the menu item
			'faq',    // Menu slug (should be unique)
			function () {
			},  // Function that will display the menu page
			'dashicons-feedback',  // Icon URL or Dashicons class name
			75                  // Position of the menu item
		);
	}

	public function miamimed_questionaries_menu_content()
	{

		$products = get_posts([
			'post_type' => 'product',
			'post_per_page' => -1,
		]);

		?>
		<div class="nk-header is-light p-3">
			<h4>Product Questionaries for Woocommerce</h4>
		</div>

		<div class="container">
			<div class="card card-bordered">
				<div class="card-inner border-bottom">
					<div class="card-title-group">
						<div class="card-title">
							<h6 class="title">Questions</h6>
						</div>
					</div>
				</div>
				<div class="card-inner">
					<div class="preview-block position-relative">
						<span class="preview-title-lg overline-title">Product Wise</span>
						<div class="card card-bordered question">
							<div class="row gy-4 miamimed-questionaries-product-question">
								<div class="col-sm-3">
									<div class="form-group"><label class="form-label" for="default-06">Select Product</label>
										<div class="form-control-wrap ">
											<div class="form-control-select">
												<select class="form-control" name="miamimed_questionaries_products" id="miamimed_questionaries_products">
													<option value="select">Select</option>
													<?php
													if (is_array($products) && !empty($products)) {
														foreach ($products as $product) {
															$product = wc_get_product($product->ID);
															?>
															<option value="<?= $product->get_id();; ?>"><?= $product->get_name() ?></option>
															<?php
														}
													}
													?>
												</select>
											</div>
										</div>
									</div>
								</div>
								<div class="col-sm-3">
									<div class="form-group"><label class="form-label" for="miamimed_single_product_question">Ask Question</label>
										<div class="form-control-wrap"><input type="text" class="form-control" id="miamimed_single_product_question" placeholder="What's your name?" disabled></div>
									</div>
								</div>
								<div class="col-sm-3">
									<div class="form-group"><label class="form-label" for="default-06">Select Type</label>
										<div class="form-control-wrap ">
											<div class="form-control-select">
												<select class="form-control" name="miamimed_questionaries_products" id="miamimed_questionaries_products">
													<option value="select">Select</option>
													<option value="yes_no">Yes | No</option>
													<option value="paragraph">Multiline Text</option>
												</select>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<img class="my-2 miamimed-questionary-repeater" width="20" src="<?= plugin_dir_url(__DIR__) . 'assets/images/add.png'?>">
					</div>
				</div>
				<div class="card-inner">
					<div class="preview-block position-relative">
						<span class="preview-title-lg overline-title">Default Questions</span>
						<div class="card card-bordered question">
								<div class="row gy-4 miamimed-questionaries-product-question">
									<div class="col-sm-3">
										<div class="form-group"><label class="form-label" for="miamimed_single_product_question">Ask Question</label>
											<div class="form-control-wrap"><input type="text" class="form-control" id="miamimed_single_product_question" placeholder="What's your name?"></div>
										</div>
									</div>
									<div class="col-sm-3">
										<div class="form-group"><label class="form-label" for="default-06">Select Type</label>
											<div class="form-control-wrap ">
												<div class="form-control-select">
													<select class="form-control" name="miamimed_questionaries_products" id="miamimed_questionaries_products">
														<option value="select">Select</option>
														<option value="yes_no">Yes | No</option>
														<option value="paragraph">Multiline Text</option>
													</select>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<img class="my-2 miamimed-questionary-repeater" width="20" src="<?= plugin_dir_url(__DIR__) . 'assets/images/add.png'?>">
				</div>
			</div>
		</div>
		<?php
	}

	public function miamimed_activate_deactivate_user_column($column)
	{
		$column['activate'] = 'Activate';
		$column['2fa'] = '2FA';
		return $column;
	}

	public function miamimed_activate_deactivate_user_column_data($output, $column_name, $user_id)
	{
		global $wpdb;
		if ('activate' == $column_name) {
			$meta = get_user_meta($user_id, "miamimed_telehealth_activate_user", true);
			if (!empty($meta) && gettype($meta) == "string") {
				$meta = json_decode($meta, true);
			}
			$checked = "";
			if (is_array($meta) && $meta['status']) {
				$checked = 'checked="checked"';
			}
			return __('<input type="hidden" class="miamimed_telehealth_user_profile_url" value="' . wp_get_attachment_url(get_user_meta($user_id, $wpdb->prefix . 'user_avatar', true)) . '"><label class="miamimed-telehealth-activate-user-switch"><input class="miamimed_telehealth_activate_user_button" data-user-id="' . $user_id . '" type="checkbox" ' . $checked . ' ><span class="miamimed-telehealth-activate-user-slider miamimed-telehealth-activate-user-round"></span></label>', 'miamimed-telehealth');
		}else if('2fa' == $column_name){
			$meta = get_user_meta($user_id, "miamimed_telehealth_2fa", true);
			if (!empty($meta) && gettype($meta) == "string") {
				$meta = json_decode($meta, true);
			}
			$checked = "";
			if (is_array($meta) && $meta['enable']) {
				$checked = 'checked="checked"';
			}
			return __('<label class="miamimed-telehealth-activate-user-switch"><input class="miamimed_telehealth_activate_user_button" data-name="2fa" data-user-id="' . $user_id . '" type="checkbox" ' . $checked . ' ><span class="miamimed-telehealth-activate-user-slider miamimed-telehealth-activate-user-round"></span></label>', 'miamimed-telehealth');
		}
		return $output;
	}

	public function miamimed_telehealth_activate_user()
	{
		if (isset($_POST['nonce']) && !empty($_POST['nonce'])) {
			if (wp_verify_nonce($_POST['nonce'], 'miamimed-telehealth')) {
				if (
					isset($_POST['id']) && !empty($_POST['id']) &&
					isset($_POST['key']) && !empty($_POST['key'])
				) {
					switch ($_POST['key']) {
						case 'activate':
							$status = true;
							$message = "Activated";
							break;
						case 'deactivate':
							$status = false;
							$message = "Deactivated";
							break;
					}
					if (isset($_POST['name']) && $_POST['name'] == "2fa") {
						update_user_meta($_POST['id'], "miamimed_telehealth_2fa", wp_json_encode([
							'enable' => $status,
							'secret' => rand(100000, 999999),
						]));
						$response['status'] = true;
						$response['message'] = '2FA ' . $message . ' Successfully!';
					}else{
						update_user_meta($_POST['id'], "miamimed_telehealth_activate_user", wp_json_encode([
							'status' => $status,
						]));
						$response['status'] = true;
						$response['message'] = 'User ' . $message . ' Successfully!';
					}
				} else {
					$response['status'] = false;
					$response['message'] = 'Incorrect or Invalid Data!';
				}
			} else {
				$response['status'] = false;
				$response['message'] = 'Nonce Verification Failed!';
			}
		} else {
			$response['status'] = false;
			$response['message'] = 'Nonce Failure!';
		}
		echo wp_json_encode($response);
		wp_die();
	}

	public function miamimed_user_roles_admin_menu()
	{
		add_menu_page(
			'Roles',    // Page title
			'Roles',    // Menu title
			'manage_options',  // Capability required to access the menu item
			'pp-capabilities-roles',    // Menu slug (should be unique)
			function () {
			},  // Function that will display the menu page
			'dashicons-admin-network',  // Icon URL or Dashicons class name
			73                  // Position of the menu item
		);
	}

	public function miamimed_user_registered($user_id)
	{
		update_user_meta($user_id, "miamimed_telehealth_activate_user", json_encode([
			'status' => true,
		]));
		update_user_meta($user_id, 'miamimed_telehealth_user_key', base64_encode(get_user_by('id', $user_id)->data->user_login));
	}

	public function miamimed_custom_option_on_edit_user()
	{
		global $wpdb;
		$user = get_user_by('id', $_GET['user_id']);

		setcookie("miamimed_telehealth_user_profile_url", wp_get_attachment_url(get_user_meta($_GET['user_id'], $wpdb->prefix . 'user_avatar', true)), time() + 60);

		if ($user->roles[0] == "patient") {
			$meta = get_user_meta($_GET['user_id'], "dermatologist", true);
		?>
			<h2>Select Dermatologist</h2>
			<table class="form-table" role="presentation">
				<tbody>
					<tr class="user-dermatologist-wrap">
						<th><label for="dermatologist">Dermatologist</label></th>
						<td>
							<select name="select_dermatologist" id="dermatologist">
								<?php
								array_map(function ($dermatologist) use ($meta) {
								?> <option <?= $meta == $dermatologist->data->user_nicename ? 'selected' : "" ?> value="<?= $dermatologist->data->user_nicename ?>"><?= $dermatologist->data->user_nicename ?></option> <?php
																																																					}, get_users([
																																																						'role' => 'dermatologist',
																																																						'orderby' => 'user_nicename',
																																																						'order' => 'ASC',
																																																					]));
																																																						?>
							</select>
						</td>
					</tr>
				</tbody>
			</table>
		<?php
		}
	}

	public function miamimed_custom_option_update_on_edit_user($user_id)
	{
		if (current_user_can('edit_user', $user_id)) {
			update_user_meta($user_id, 'dermatologist', $_POST['select_dermatologist']);
		}
	}

	public function miamimed_custom_product_category_columns($columns)
	{
		$columns['miamimed_activate_product_category'] = 'Activate';
		return $columns;
	}

	public function miamimed_display_custom_product_category_columns($content, $column_name, $term_id)
	{
		if ($column_name === 'miamimed_activate_product_category') {
			$data = json_decode(get_option("miamimed_product_category_activate", []), true);
			$value = in_array($term_id, $data) ? "checked" : "";
			return __('<input type="hidden" class="miamimed_telehealth_user_profile_url" value=""><label class="miamimed-telehealth-activate-user-switch"><input class="miamimed_telehealth_activate_product_cat" ' . $value . ' data-id="' . $term_id . '" type="checkbox"><span class="miamimed-telehealth-activate-user-slider miamimed-telehealth-activate-user-round"></span></label>', 'miamimed-telehealth');
		}
		return $content;
	}

	public function miamimed_telehealth_activate_product_cat()
	{
		if (isset($_POST['nonce']) && !empty($_POST['nonce'])) {
			if (wp_verify_nonce($_POST['nonce'], 'miamimed-telehealth')) {
				if (
					isset($_POST['id']) && !empty($_POST['id']) &&
					isset($_POST['key']) && !empty($_POST['key'])
				) {
					$data = json_decode(get_option("miamimed_product_category_activate", []), true);
					switch ($_POST['key']) {
						case 'activate':
							$message = "Activated";
							if (is_array($data) && !empty($data)) {
								if (!in_array($_POST['id'], $data)) {
									$data[] = $_POST['id'];
								}
							} else {
								$data = [$_POST['id']];
							}
							break;
						case 'deactivate':
							if (is_array($data) && !empty($data)) {
								$index = array_search($_POST['id'], $data);
								unset($data[$index]);
							} else {
								$data = [];
							}
							$message = "Deactivated";
							break;
					}
					update_option("miamimed_product_category_activate", wp_json_encode($data));
					$response['status'] = true;
					$response['message'] = get_term_by('id', $_POST['id'], 'product_cat')->name . ' ' . $message . ' Successfully!';
				} else {
					$response['status'] = false;
					$response['message'] = 'Incorrect or Invalid Data!';
				}
			} else {
				$response['status'] = false;
				$response['message'] = 'Nonce Verification Failed!';
			}
		} else {
			$response['status'] = false;
			$response['message'] = 'Nonce Failure!';
		}
		echo wp_json_encode($response);
		wp_die();
	}

	public function miamimed_show_selected_product_categories($args, $post_id)
	{
		// Check if the current post type is 'product'
		if (get_post_type($post_id) === 'product') {
			// Get the selected categories for the current product
			$selected_categories = json_decode(get_option("miamimed_product_category_activate", []), true);

			?>
			<input type="hidden" id="miamimed_selected_product_categories" name="miamimed_selected_product_categories" value='<?= wp_json_encode($selected_categories); ?>'>
			<?php

			// Modify the taxonomy query to include only selected categories
			$args['include'] = implode(',', $selected_categories);
		}

		return $args;
	}

	public function miamimmed_register_product_taxonomy()
	{

		$custom_taxonomy = [
			'type' => [
				'hierarchical'      => true,
				'labels'            => [
					'name'                       => _x('Types', 'taxonomy general name', 'text-domain'),
					'singular_name'              => _x('Type', 'taxonomy singular name', 'text-domain'),
					'search_items'               => __('Search Types', 'text-domain'),
					'popular_items'              => __('Popular Types', 'text-domain'),
					'all_items'                  => __('All Types', 'text-domain'),
					'parent_item'                => null,
					'parent_item_colon'          => null,
					'edit_item'                  => __('Edit Type', 'text-domain'),
					'update_item'                => __('Update Type', 'text-domain'),
					'add_new_item'               => __('Add New Type', 'text-domain'),
					'new_item_name'              => __('New Type Name', 'text-domain'),
					'separate_items_with_commas' => __('Separate types with commas', 'text-domain'),
					'add_or_remove_items'        => __('Add or remove types', 'text-domain'),
					'choose_from_most_used'      => __('Choose from the most used types', 'text-domain'),
					'menu_name'                  => __('Type', 'text-domain'),
				],
				'show_ui'           => true,
				'show_admin_column' => true,
				'query_var'         => true,
				'rewrite'           => array('slug' => 'type'),
			],
			'treatments' => [
				'hierarchical'      => true,
				'labels'            => [
					'name'                       => _x('Treatments', 'taxonomy general name', 'text-domain'),
					'singular_name'              => _x('Treatment', 'taxonomy singular name', 'text-domain'),
					'search_items'               => __('Search Treatments', 'text-domain'),
					'popular_items'              => __('Popular Treatments', 'text-domain'),
					'all_items'                  => __('All Treatments', 'text-domain'),
					'parent_item'                => null,
					'parent_item_colon'          => null,
					'edit_item'                  => __('Edit Treatment', 'text-domain'),
					'update_item'                => __('Update Treatment', 'text-domain'),
					'add_new_item'               => __('Add New Treatment', 'text-domain'),
					'new_item_name'              => __('New Treatment Name', 'text-domain'),
					'separate_items_with_commas' => __('Separate treatments with commas', 'text-domain'),
					'add_or_remove_items'        => __('Add or remove treatments', 'text-domain'),
					'choose_from_most_used'      => __('Choose from the most used treatments', 'text-domain'),
					'menu_name'                  => __('Treatments', 'text-domain'),
				],
				'show_ui'           => true,
				'show_admin_column' => true,
				'query_var'         => true,
				'rewrite'           => array('slug' => 'treatments'),
			],
			'sub_treatments' => [
				'hierarchical'      => true,
				'labels'            => [
					'name'                       => _x('Sub Treatments', 'taxonomy general name', 'text-domain'),
					'singular_name'              => _x('Sub Treatment', 'taxonomy singular name', 'text-domain'),
					'search_items'               => __('Search Sub Treatments', 'text-domain'),
					'popular_items'              => __('Popular Sub Treatments', 'text-domain'),
					'all_items'                  => __('All Sub Treatments', 'text-domain'),
					'parent_item'                => null,
					'parent_item_colon'          => null,
					'edit_item'                  => __('Edit Sub Treatment', 'text-domain'),
					'update_item'                => __('Update Sub Treatment', 'text-domain'),
					'add_new_item'               => __('Add New Sub Treatment', 'text-domain'),
					'new_item_name'              => __('New Sub Treatment Name', 'text-domain'),
					'separate_items_with_commas' => __('Separate sub treatments with commas', 'text-domain'),
					'add_or_remove_items'        => __('Add or remove sub treatments', 'text-domain'),
					'choose_from_most_used'      => __('Choose from the most used sub treatments', 'text-domain'),
					'menu_name'                  => __('Sub Treatments', 'text-domain'),
				],
				'show_ui'           => true,
				'show_admin_column' => true,
				'query_var'         => true,
				'rewrite'           => array('slug' => 'sub-treatments'),
			]
		];

		foreach ($custom_taxonomy as $name => $value) {
			register_taxonomy($name, 'product', $value);
		}
	}

	public function miamimed_custom_product_filter_dropdown($output)
	{

		$terms = [
			'type',
			'treatments',
			'sub_treatments',
		];

		foreach ($terms as $single_term) {
			$data = get_terms([
				'taxonomy' => $single_term,
				'post_per_page' => -1,
			]);

			if (is_array($data)) {
				$output .= '<select name="' . $single_term . '">';
				$output .= '<option value="">' . esc_html__('All ' . str_replace("_", " ", ucfirst($single_term)) . '', 'miamimed-telehealth') . '</option>';
				foreach ($data as $term) {
					$output .= '<option value="' . esc_attr($term->slug) . '">' . esc_html($term->name) . '</option>';
				}
				$output .= '</select>';
			}
		}

		return $output;
	}

	public function miamimed_get_products(){
		$products = get_posts([
			'post_type' => 'product',
			'post_per_page' => -1,
		]);

		echo wp_json_encode($products);
		exit;
	}

	// public function miamimed_restrict_admin_login_attempts($username) {
	// 	$max_attempts = 3;  // Set the maximum number of login attempts allowed
	// 	$lockout_duration = 5 * MINUTE_IN_SECONDS;  // Set the lockout duration in seconds
	// 	$failed_attempts = get_transient('login_lock_admin_' . $username) ?: 0;
	// 	if ($failed_attempts >= $max_attempts) {
	// 		wp_die('Too many login attempts. Please try again after ' . $lockout_duration / MINUTE_IN_SECONDS . ' minutes.');
	// 	}
	// 	set_transient('login_lock_admin_' . $username, $failed_attempts + 1, $lockout_duration);
	// }

	public function miamimed_add_custom_coupon_field() {
		global $post;
		$is_one_time = get_post_meta($post->ID, 'miamimed_wc_custom_coupon_one_time', true);
		$status = get_post_meta($post->ID, 'miamimed_wc_coupon_activation', true);
		?>
		<p class="form-field one_time_field">
			<label for="miamimed_wc_custom_coupon_one_time"><?php esc_html_e('One-time Coupon', 'miamimed-telehealth'); ?></label>
			<input type="checkbox" id="miamimed_wc_custom_coupon_one_time" name="miamimed_wc_custom_coupon_one_time" <?php checked($is_one_time, 'yes'); ?> value="one_time" />
			<span class="description"><?php esc_html_e('Check this box if the coupon should be one-time use only.', 'miamimed-telehealth'); ?></span>
		</p>
		<p class="form-field activate_field">
			<label for="miamimed_wc_coupon_activation"><?php esc_html_e('Coupon Activation', 'miamimed-telehealth'); ?></label>
			<input type="checkbox" id="miamimed_wc_coupon_activation" <?php checked($status, 'yes'); ?> name="miamimed_wc_coupon_activation" value="activated" />
			<span class="description"><?php esc_html_e('Check this box to activate the coupon immediately.', 'miamimed-telehealth'); ?></span>
		</p>
		<?php
	}

	public function miamimed_save_custom_coupon_fields($post_id) {
		if (isset($_POST['miamimed_wc_coupon_activation'])) {
			update_post_meta($post_id, 'miamimed_wc_coupon_activation', "yes");
			wp_update_post(array(
				'ID' => $post_id,
				'post_status' => 'publish',
			));
		}else{
			update_post_meta($post_id, 'miamimed_wc_coupon_activation', "no");
			wp_update_post(array(
				'ID' => $post_id,
				'post_status' => 'private',
			));
		}
		if (isset($_POST['miamimed_wc_custom_coupon_one_time'])){
			update_post_meta($post_id, 'miamimed_wc_custom_coupon_one_time', 'yes');
		}else{
			update_post_meta($post_id, 'miamimed_wc_custom_coupon_one_time', 'no');
		}
	}

	public function miamimed_custom_cron_schedules($schedules) {
		$schedules['miamimed_change_user_key_event'] = array(
			// 'interval' => 86400, // 24 hours in seconds
			'interval' => 5, // 24 hours in seconds
			'display' => __('Once Daily Custom')
		);
		return $schedules;
	}

	public function miamimed_change_user_key_hook_callback() {

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'https://shashwat.requestcatcher.com/test');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, "Hello World!");
		$headers = array();
		$headers[] = 'Content-Type: application/x-www-form-urlencoded';
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		$result = curl_exec($ch);
		if (curl_errno($ch)) {
			echo 'Error:' . curl_error($ch);
		}
		curl_close($ch);

	}

	function debug($data)
	{
		echo "<pre>";
		print_r($data);
		die;
	}
}
