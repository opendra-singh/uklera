<?php
class Miamimed_Telehealth
{
	protected $loader;
	protected $plugin_name;
	protected $version;

	public function __construct()
	{
		if (defined('MIAMIMED_TELEHEALTH_VERSION')) {
			$this->version = MIAMIMED_TELEHEALTH_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'miamimed-telehealth';
		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}

	private function load_dependencies()
	{
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-miamimed-telehealth-loader.php';
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-miamimed-telehealth-i18n.php';
		require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-miamimed-telehealth-admin.php';
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-miamimed-telehealth-public.php';
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-google-authenticator.php';
		$this->loader = new Miamimed_Telehealth_Loader();
	}

	private function set_locale()
	{
		$plugin_i18n = new Miamimed_Telehealth_i18n();
		$this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
	}

	private function define_admin_hooks()
	{
		$plugin_admin = new Miamimed_Telehealth_Admin($this->get_plugin_name(), $this->get_version());
		$this->loader->add_action('init', $plugin_admin, 'miamimmed_register_product_taxonomy');
		$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
		$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
		$this->loader->add_action('wp_ajax_miamimed_telehealth_activate_2fa', $plugin_admin, 'miamimed_telehealth_activate_2fa');
		$this->loader->add_action('wp_ajax_miamimed_telehealth_deactivate_2fa', $plugin_admin, 'miamimed_telehealth_deactivate_2fa');
		$this->loader->add_action('wp_ajax_miamimed_telehealth_activate_user', $plugin_admin, 'miamimed_telehealth_activate_user');
		$this->loader->add_action('wp_ajax_miamimed_telehealth_activate_product_cat', $plugin_admin, 'miamimed_telehealth_activate_product_cat');
		$this->loader->add_action('wp_ajax_miamimed_get_products', $plugin_admin, 'miamimed_get_products');
		$this->loader->add_action('woocommerce_coupon_options', $plugin_admin, 'miamimed_add_custom_coupon_field');
		$this->loader->add_action('woocommerce_coupon_options_save', $plugin_admin, 'miamimed_save_custom_coupon_fields');
		$this->loader->add_action('login_message', $plugin_admin, 'enqueue_styles');
		$this->loader->add_action('miamimed_change_user_key_hook', $plugin_admin, 'miamimed_change_user_key_hook_callback');
		// $this->loader->add_action('authenticate', $plugin_admin, 'miamimed_restrict_admin_login_attempts');
		$this->loader->add_action('admin_menu', $plugin_admin, 'miamimed_user_roles_admin_menu');
		$this->loader->add_action('user_register', $plugin_admin, 'miamimed_user_registered');
		$this->loader->add_action('wp_loaded', $plugin_admin, 'remove_admin_menu');
		$this->loader->add_action('edit_user_profile', $plugin_admin, 'miamimed_custom_option_on_edit_user');
		$this->loader->add_action('edit_user_profile_update', $plugin_admin, 'miamimed_custom_option_update_on_edit_user');
		$this->loader->add_action('manage_product_cat_custom_column', $plugin_admin, 'miamimed_display_custom_product_category_columns', 10, 3);
		$this->loader->add_filter('woocommerce_product_filters', $plugin_admin, 'miamimed_custom_product_filter_dropdown');
		$this->loader->add_filter('manage_users_columns', $plugin_admin, 'miamimed_activate_deactivate_user_column');
		$this->loader->add_filter('manage_users_custom_column', $plugin_admin, 'miamimed_activate_deactivate_user_column_data', 10, 3);
		$this->loader->add_filter('manage_edit-product_cat_columns', $plugin_admin, 'miamimed_custom_product_category_columns', 10, 3);
		$this->loader->add_filter('wp_terms_checklist_args', $plugin_admin, 'miamimed_show_selected_product_categories', 99, 2);
		$this->loader->add_filter('cron_schedules', $plugin_admin, 'miamimed_custom_cron_schedules');
	}

	private function define_public_hooks()
	{
		$plugin_public = new Miamimed_Telehealth_Public($this->get_plugin_name(), $this->get_version());
		$this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
		$this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');
		$this->loader->add_action('rest_api_init', $plugin_public, 'rest_api_callback');
		// $this->loader->add_action('wp_login_failed', $plugin_public, 'miamimed_restrict_login_attempts');
		$this->loader->add_action('woocommerce_before_checkout_form', $plugin_public, 'miamimed_questionaries');
		$this->loader->add_action('plugins_loaded', $plugin_public, 'miamimed_telehealth_questionaries_shortcode');
		$this->loader->add_action('logout_redirect', $plugin_public, 'miamimed_custom_logout_redirect');
		$this->loader->add_action('woocommerce_thankyou', $plugin_public, 'miammimed_after_thankyou_woocommerce');
		$this->loader->add_action('wp_ajax_login_user', $plugin_public, 'Patient_login_user');
		$this->loader->add_action('wp_ajax_nopriv_login_user', $plugin_public, 'Patient_login_user');
		$this->loader->add_action('wp_ajax_signup_user', $plugin_public, 'Patient_signup_user');
		$this->loader->add_action('wp_ajax_nopriv_signup_user', $plugin_public, 'Patient_signup_user');
		$this->loader->add_action('wp_ajax_send_otp', $plugin_public, 'Patient_send_otp');
		$this->loader->add_action('wp_ajax_nopriv_send_otp', $plugin_public, 'Patient_send_otp');
		$this->loader->add_action('wp_ajax_verify_otp', $plugin_public, 'Patient_verify_otp');
		$this->loader->add_action('wp_ajax_nopriv_verify_otp', $plugin_public, 'Patient_verify_otp');
		$this->loader->add_action('wp_ajax_reset_password', $plugin_public, 'Patient_reset_password');
		$this->loader->add_action('wp_ajax_nopriv_reset_password', $plugin_public, 'Patient_reset_password');
		$this->loader->add_action('wp_ajax_miamimed_questionaries_login_resend', $plugin_public, 'miamimed_questionaries_login_resend');
		$this->loader->add_action('wp_ajax_nopriv_miamimed_questionaries_login_resend', $plugin_public, 'miamimed_questionaries_login_resend');
		$this->loader->add_action('wp_ajax_render_reshortcode', $plugin_public, 'Patient_render_reshortcode');
		$this->loader->add_action('wp_ajax_nopriv_render_reshortcode', $plugin_public, 'Patient_render_reshortcode');
		$this->loader->add_action('wp_ajax_miamimed_update_questionary_question', $plugin_public, 'Patient_miamimed_update_questionary_question');
		$this->loader->add_action('wp_ajax_nopriv_miamimed_update_questionary_question', $plugin_public, 'Patient_miamimed_update_questionary_question');
		$this->loader->add_action('wp_ajax_send_verification_mail', $plugin_public, 'Patient_send_verfication_mail');
		$this->loader->add_action('wp_ajax_nopriv_send_verification_mail', $plugin_public, 'Patient_send_verfication_mail');
		$this->loader->add_action('wp_ajax_miamimed_login_resend_2fa_code', $plugin_public, 'miamimed_login_resend_2fa_code');
		$this->loader->add_action('wp_ajax_nopriv_miamimed_login_resend_2fa_code', $plugin_public, 'miamimed_login_resend_2fa_code');
		$this->loader->add_action('wp_ajax_miamimed_login_resend_2fa_code_verify', $plugin_public, 'miamimed_login_resend_2fa_code_verify');
		$this->loader->add_action('wp_ajax_nopriv_miamimed_login_resend_2fa_code_verify', $plugin_public, 'miamimed_login_resend_2fa_code_verify');
		$this->loader->add_action('wp_ajax_patient_two_factor_verification', $plugin_public, 'patient_two_factor_verification');
		$this->loader->add_action('wp_ajax_nopriv_patient_two_factor_verification', $plugin_public, 'patient_two_factor_verification');
		$this->loader->add_action('wp_ajax_miamimed_continue_to_purchase', $plugin_public, 'miamimed_continue_to_purchase');
		$this->loader->add_action('wp_ajax_nopriv_miamimed_continue_to_purchase', $plugin_public, 'miamimed_continue_to_purchase');
		$this->loader->add_action('wp_ajax_miamimed_questionaries_state_panel', $plugin_public, 'miamimed_questionaries_state_panel');
		$this->loader->add_action('wp_ajax_nopriv_miamimed_questionaries_state_panel', $plugin_public, 'miamimed_questionaries_state_panel');
		$this->loader->add_action('wp_ajax_miamimed_questionaries_dermatologist_panel', $plugin_public, 'miamimed_questionaries_dermatologist_panel');
		$this->loader->add_action('wp_ajax_nopriv_miamimed_questionaries_dermatologist_panel', $plugin_public, 'miamimed_questionaries_dermatologist_panel');
		$this->loader->add_action('wp_ajax_miamimed_questionaries_doctor_visit_panel', $plugin_public, 'miamimed_questionaries_doctor_visit_panel');
		$this->loader->add_action('wp_ajax_nopriv_miamimed_questionaries_doctor_visit_panel', $plugin_public, 'miamimed_questionaries_doctor_visit_panel');
		$this->loader->add_action('init', $plugin_public, 'session_start');
		$this->loader->add_action('after_setup_theme', $plugin_public, 'miamimed_hide_admin_bar_for_patient');
	}

	public function run()
	{
		$this->loader->run();
	}

	public function get_plugin_name()
	{
		return $this->plugin_name;
	}

	public function get_loader()
	{
		return $this->loader;
	}

	public function get_version()
	{
		return $this->version;
	}
}