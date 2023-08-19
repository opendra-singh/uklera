<?php

class Miamimed_Telehealth_Public
{
	private $plugin_name;
	private $version;
	private $google_auth;

	public function __construct($plugin_name, $version)
	{
		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->google_auth = new GoogleAuthenticator;
	}

	public function enqueue_styles()
	{
		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/miamimed-telehealth-public.css', array(), $this->version, 'all');
	}

	public function enqueue_scripts()
	{
		wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/miamimed-telehealth-public.js', array('jquery'), $this->version, false);
		wp_localize_script($this->plugin_name, 'PatienSignUpObject', array('ajax_url' => admin_url('admin-ajax.php'), 'PatientPageNonce' => wp_create_nonce('patient-page-nonce')));
	}

	public function rest_api_callback()
	{
		register_rest_route(
			'Miamimed_Telehealth/v1',
			'login',
			[
				'methods' => 'POST',
				'callback' => [$this, 'Miamimed_Telehealth_receive_callback_login'],
				'permission_callback' => '__return_true',
			],
		);
		register_rest_route(
			'Miamimed_Telehealth/v1',
			'email_verification',
			[
				'methods' => 'GET',
				'callback' => [$this, 'Miamimed_Telehealth_receive_callback_email_verification'],
				'content_type' => 'text/html',
				'permission_callback' => '__return_true',
			],
		);
		register_rest_route(
			'Miamimed_Telehealth/v1',
			'reset_password',
			[
				'methods' => 'GET',
				'callback' => [$this, 'Miamimed_Telehealth_receive_callback_reset_password'],
				'content_type' => 'text/html',
				'permission_callback' => '__return_true',
			],
		);
		register_rest_route(
			'Miamimed_Telehealth/v1',
			'update_profile',
			[
				'methods' => 'POST',
				'callback' => [$this, 'Miamimed_Telehealth_receive_callback_update_profile'],
				'content_type' => 'text/html',
				'permission_callback' => '__return_true',
			],
		);
		register_rest_route(
			'Miamimed_Telehealth/v1',
			'send_forgot_password_mail',
			[
				'methods' => 'POST',
				'callback' => [$this, 'Miamimed_Telehealth_receive_callback_send_forgot_password_mail'],
				'content_type' => 'text/html',
				'permission_callback' => '__return_true',
			],
		);
		register_rest_route(
			'Miamimed_Telehealth/v1',
			'change_password',
			[
				'methods' => 'POST',
				'callback' => [$this, 'Miamimed_Telehealth_receive_callback_change_password'],
				'content_type' => 'text/html',
				'permission_callback' => '__return_true',
			],
		);
		register_rest_route(
			'Miamimed_Telehealth/v1',
			'get_profile_data',
			[
				'methods' => 'POST',
				'callback' => [$this, 'Miamimed_Telehealth_receive_callback_get_profile_data'],
				'content_type' => 'text/html',
				'permission_callback' => '__return_true',
			],
		);
		register_rest_route(
			'Miamimed_Telehealth/v1',
			'reset_password_api',
			[
				'methods' => 'POST',
				'callback' => [$this, 'Miamimed_Telehealth_receive_callback_reset_password_api'],
				'content_type' => 'text/html',
				'permission_callback' => '__return_true',
			],
		);
		register_rest_route(
			'Miamimed_Telehealth/v1',
			'get_patients_by_dermatologist_id',
			[
				'methods' => 'GET',
				'callback' => [$this, 'Miamimed_Telehealth_receive_callback_get_patients_by_dermatologist_id'],
				'content_type' => 'text/html',
				'permission_callback' => '__return_true',
			],
		);
		register_rest_route(
			'Miamimed_Telehealth/v1',
			'change_profile_image',
			[
				'methods' => 'POST',
				'callback' => [$this, 'Miamimed_Telehealth_receive_callback_change_profile_image'],
				'content_type' => 'text/html',
				'permission_callback' => '__return_true',
			],
		);
		register_rest_route(
			'Miamimed_Telehealth/v1',
			'delete_user_profile_image',
			[
				'methods' => 'POST',
				'callback' => [$this, 'Miamimed_Telehealth_receive_callback_delete_user_profile_image'],
				'content_type' => 'text/html',
				'permission_callback' => '__return_true',
			],
		);
		register_rest_route(
			'Miamimed_Telehealth/v1',
			'activate_deactivate_user_by_id',
			[
				'methods' => 'POST',
				'callback' => [$this, 'Miamimed_Telehealth_receive_callback_activate_deactivate_user_by_id'],
				'content_type' => 'text/html',
				'permission_callback' => '__return_true',
			],
		);
		register_rest_route(
			'Miamimed_Telehealth/v1',
			'logout_user_by_id',
			[
				'methods' => 'POST',
				'callback' => [$this, 'Miamimed_Telehealth_receive_callback_logout_user_by_id'],
				'content_type' => 'text/html',
				'permission_callback' => '__return_true',
			],
		);
		register_rest_route(
			'Miamimed_Telehealth/v1',
			'orders_by_user_id',
			[
				'methods' => 'GET',
				'callback' => [$this, 'Miamimed_Telehealth_receive_callback_orders_by_user_id'],
				'content_type' => 'text/html',
				'permission_callback' => '__return_true',
			],
		);
		register_rest_route(
			'Miamimed_Telehealth/v1',
			'questionaries_by_product_id',
			[
				'methods' => 'GET',
				'callback' => [$this, 'Miamimed_Telehealth_receive_callback_questionaries_by_product_id'],
				'content_type' => 'text/html',
				'permission_callback' => '__return_true',
			],
		);
		register_rest_route(
			'Miamimed_Telehealth/v1',
			'medical_history_by_user_id',
			[
				'methods' => 'GET',
				'callback' => [$this, 'Miamimed_Telehealth_receive_callback_medical_history_by_user_id'],
				'content_type' => 'text/html',
				'permission_callback' => '__return_true',
			],
		);
		register_rest_route(
			'Miamimed_Telehealth/v1',
			'get_orders_by_dermotologist_id',
			[
				'methods' => 'GET',
				'callback' => [$this, 'Miamimed_Telehealth_receive_callback_get_orders_by_dermotologist_id'],
				'content_type' => 'text/html',
				'permission_callback' => '__return_true',
			],
		);
		register_rest_route(
			'Miamimed_Telehealth/v1',
			'approve_order_by_id',
			[
				'methods' => 'POST',
				'callback' => [$this, 'Miamimed_Telehealth_receive_callback_approve_order_by_id'],
				'content_type' => 'text/html',
				'permission_callback' => '__return_true',
			],
		);
		register_rest_route(
			'Miamimed_Telehealth/v1',
			'notification_status',
			[
				'methods' => 'POST',
				'callback' => [$this, 'Miamimed_Telehealth_receive_callback_notification_status'],
				'content_type' => 'text/html',
				'permission_callback' => '__return_true',
			],
		);
		register_rest_route(
			'Miamimed_Telehealth/v1',
			'dashboard',
			[
				'methods' => 'POST',
				'callback' => [$this, 'Miamimed_Telehealth_receive_callback_dashboard'],
				'content_type' => 'text/html',
				'permission_callback' => '__return_true',
			],
		);
	}

	public function Miamimed_Telehealth_receive_callback_dashboard($request)
	{
		header('Access-Control-Allow-Origin: *');
		$headers = getallheaders();
		if (isset($headers['Authorization'])) {
			$auth_header = $headers['Authorization'];
			if (substr($auth_header, 0, 7) === 'Bearer ') {
				$token = substr($auth_header, 7);
				if ($token != get_option("miamimed_telehealth_access_token")) {
					return array(
						'status' => false,
						'message' => "401 Unauthorized: Invalid Access Token!",
					);
				} else {
					$id = $request->get_param('id');
					if (empty($id)) {
						return [
							"status" => false,
							"message" => "ID cannot be empty!",
						];
					}else{
						global $wpdb;
						$user = get_user_by('id', $id);
						if ($user) {
							$table = $wpdb->prefix . 'posts';
							$table2 = $wpdb->prefix . 'usermeta';
							$order_id_query = $wpdb->prepare("SELECT COUNT(*) FROM $table o JOIN $table2 um WHERE um.meta_key = 'dermatologist' AND um.meta_value = '%s' AND um.user_id = o.post_author AND o.post_type = 'shop_order' ORDER BY o.ID ASC", $user->data->user_nicename );
							$user_id_query = $wpdb->prepare('SELECT COUNT(*) FROM `'.$table2.'` WHERE meta_key = "dermatologist" AND meta_value = "%s" ORDER BY user_id ASC;', $user->data->user_nicename );
							$orders = $wpdb->get_var($order_id_query);
							$users = $wpdb->get_var($user_id_query);
							$status = [
								"wc-pending",
								"wc-processing",
								"wc-on-hold" ,
								"wc-completed",
								"wc-cancelled",
								"wc-refunded",
								"wc-failed",
								"wc-checkout-draft",
							];
							$data = [
								"Total_Orders" => $orders,
								"Total_Users" => $users,
							];
							foreach ($status as $single) {
								$order_id_query = $wpdb->prepare("SELECT COUNT(*) FROM $table o JOIN $table2 um WHERE um.meta_key = 'dermatologist' AND um.user_id = o.post_author AND um.meta_value = '%s' AND o.post_type = 'shop_order'  AND o.post_status = '%s' ORDER BY o.ID ASC", $user->data->user_nicename, $single );
								$orders = $wpdb->get_var($order_id_query);
								$data["Order_by_Status"][str_replace("-", "_", $single)] = $orders;
							}
							return [
								"status" => true,
								"message" => "Dermatologist Dashboard",
								"data" => $data,
							];
						}else{
							return [
								"status" => false,
								"message" => "Invalid ID!",
							];
						}
					}
				}
			} else {
				return array(
					'status' => false,
					'message' => "401 Unauthorized: Invalid Access Token!",
				);
			}
		} else {
			return array(
				'status' => false,
				'message' => "401 Unauthorized: Header cannot be empty!",
			);
		}
	}

	public function Miamimed_Telehealth_receive_callback_notification_status($request)
	{
		header('Access-Control-Allow-Origin: *');
		$headers = getallheaders();
		if (isset($headers['Authorization'])) {
			$auth_header = $headers['Authorization'];
			if (substr($auth_header, 0, 7) === 'Bearer ') {
				$token = substr($auth_header, 7);
				if ($token != get_option("miamimed_telehealth_access_token")) {
					return array(
						'status' => false,
						'message' => "401 Unauthorized: Invalid Access Token!",
					);
				} else {
					$order_id = $request->get_param('order_id');
					$status = $request->get_param('status');
					if (empty($order_id) || empty($status)) {
						return array(
							'status' => false,
							'message' => "Missing Parameters: Order ID or Status!",
						);
					} else {
						if ($status == true || $status == false) {
							update_post_meta($order_id, "miamimed_notificaiton_status", $status);
							$order = wc_get_order( $order_id );
							$meta = get_post_meta($order_id, "miamimed_notificaiton_status", true);
							$return_data = [
								"ID" => $order_id,
								"Total" => $order->get_total(),
								"Notification_Status" => !empty($meta) ? $meta : false,
							];
							if (is_array($order->get_items())) {
								foreach ( $order->get_items() as $item_id => $item ) {
									$product_id = $item->get_product_id();
									$return_data['products'][] = [
										"ID" => $product_id,
										"Name" => $item->get_name(),
										"Quantity" => $item->get_quantity(),
										"Subtotal" => $item->get_subtotal(),
										"Total" => $item->get_total(),
									];
								}
							}
							return [
								"status" => true,
								"message" => "Status Updated sucessfully!",
								"data" => $return_data,
							];
						}else{
							return [
								"status" => false,
								"message" => "Invalid status!",
							];
						}
					}
				}
			} else {
				return array(
					'status' => false,
					'message' => "401 Unauthorized: Invalid Access Token!",
				);
			}
		} else {
			return array(
				'status' => false,
				'message' => "401 Unauthorized: Header cannot be empty!",
			);
		}
	}

	public function Miamimed_Telehealth_receive_callback_approve_order_by_id($request)
	{
		header('Access-Control-Allow-Origin: *');
		$headers = getallheaders();
		if (isset($headers['Authorization'])) {
			$auth_header = $headers['Authorization'];
			if (substr($auth_header, 0, 7) === 'Bearer ') {
				$token = substr($auth_header, 7);
				if ($token != get_option("miamimed_telehealth_access_token")) {
					return array(
						'status' => false,
						'message' => "401 Unauthorized: Invalid Access Token!",
					);
				} else {
					$order_id = $request->get_param('order_id');
					$product_id = $request->get_param('product_id');
					$status = $request->get_param('status');
					if (empty($order_id) || empty($product_id)) {
						return array(
							'status' => false,
							'message' => "Missing Parameters: Order or Product ID!",
						);
					} else {
						$order = wc_get_order($order_id);
						if ('completed' == $order->get_status()) {
							return [
								"status" => false,
								"message" => "Order Already Completed!",
							];
						}else{
							$meta = json_decode(get_post_meta($order_id, "woodevz-stripe-all-products", true), true);
							if (in_array($product_id, array_keys($meta))) {
								if (empty($status)) {
									return [
										"status" => false,
										"message" => "status cannot be empty!",
									];
								}else{
									$products = [];
									if (is_array($order->get_items())) {
										foreach ( $order->get_items() as $item_id => $item ) {
											$products[$item->get_product_id()] = [
												"Quantity" => $item->get_quantity(),
												"Subtotal" => $item->get_subtotal(),
												"Total" => $item->get_total(),
											];
										}										
									}
									$message = "";
									if ($status == "approve") {
										$message .= "Product Approved Successfully!, "; 
										$meta[$product_id] = $status;
									}else if ($status == "disapprove"){
										$message .= "Product Disapproved Successfully!, ";
										$reason = $request->get_param('reason');
										if (isset($reason) && !empty($reason)) {
											$meta[$product_id] = [
												"status" => $status,
												"reason" => $reason,
											];
											$this->remove_product_from_order($order_id, $product_id);
										}else{
											return [
												"status" => false,
												"message" => "Enter a reason for disapproving the product.!",
											];
										}
									}else{
										return [
											"status" => false,
											"message" => "Invalid status!",
										];
									}
									update_post_meta($order_id, "woodevz-stripe-all-products", wp_json_encode($meta));
									$meta = json_decode(get_post_meta($order_id, "woodevz-stripe-all-products", true), true);
									$order = wc_get_order($order_id);
									$details = json_decode(get_post_meta($order_id, "woodevz-stripe-intent-details", true), true);
									$return_data =[
										'ID' => $order_id,
										'Total' => $order->get_total(),
										'user_details' => get_userdata($order->get_user_id())->data,
									];
									if (is_array($meta)) {
										foreach ($meta as $id => $status) {
											$product = wc_get_product($id);
											$return_data['products'][] = [
												'ID' => $id,
												'Name' => $product->get_name(),
												"Quantity" => $products[$id]['Quantity'],
												"Subtotal" => $products[$id]['Subtotal'],
												"Total" => $products[$id]['Total'],
												"Status" => $status,
											];
										}										
									}
									if (!in_array("pending", $meta)) {
										if (count($order->get_items())) {
											try {
												$login = get_user_meta($order->get_user_id(), "dermatologist", true);
												$name = !empty($login) ? $login : "Dr. Smith";
												$details['amount'] = floatval($order->get_total() + 20) * 100 ;
												\Stripe\Stripe::setApiKey(Secret_Key);
												// Call the Stripe API method by passing the data array as the first argument
												$paymentIntent = \Stripe\PaymentIntent::create($details);
												// Add metadata to the Payment Intent
												$paymentIntent->metadata = [
													'custom_charge_type' => 'dermatologist_fees',
													'custom_charge_description' => 'Consultation with ' . $name,
												];

												// Access the PaymentIntent object properties if needed
												$paymentIntentID = $paymentIntent->id;
												$client_secret = $paymentIntent->client_secret;
												update_post_meta($order_id, "woodevz-stripe-intent-details", wp_json_encode([
													"paymentIntentID" => $paymentIntentID,
													"client_secret" => $client_secret,
												]));

												$paymentIntent = \Stripe\PaymentIntent::retrieve($paymentIntentID);
			
												// Check if there was a last payment error
												if ($paymentIntent->last_payment_error) {
													$error = $paymentIntent->last_payment_error;
													return [
														"status" => false,
														"message" => "Payment Error: " . $error['message'],
													];
												}else{
													$order->update_status('completed');
													return [
														"status" => true,
														"messgae" => $message . "Payment completed successfully!",
														"data" => $return_data,
													];
												}
											} catch (\Stripe\Exception\ApiErrorException $e) {
												// Handle API errors
												return [
													"status" => false,
													"message" => $e->getMessage(),
												];
											}												
										}else{
											return [
												"status" => true,
												"messgae" => "Your Order has been disapproved!",
											];
										}

									}else{
										return [
											"status" => true,
											"messgae" => $message,
											"data" => $return_data,
										];
									}
								}
							}else{
								return [
									"status" => false,
									"message" => "The product you provided is not in the order. Please check your order and product IDs.",
								];
							}
						}
					}
				}
			} else {
				return array(
					'status' => false,
					'message' => "401 Unauthorized: Invalid Access Token!",
				);
			}
		} else {
			return array(
				'status' => false,
				'message' => "401 Unauthorized: Header cannot be empty!",
			);
		}
	}

	public function Miamimed_Telehealth_receive_callback_get_orders_by_dermotologist_id($request)
	{
		header('Access-Control-Allow-Origin: *');
		$headers = getallheaders();
		if (isset($headers['Authorization'])) {
			$auth_header = $headers['Authorization'];
			if (substr($auth_header, 0, 7) === 'Bearer ') {
				$token = substr($auth_header, 7);
				if ($token != get_option("miamimed_telehealth_access_token")) {
					return array(
						'status' => false,
						'message' => "401 Unauthorized: Invalid Access Token!",
					);
				} else {
					$parameters = $request->get_query_params();
					$id = $parameters['id'];
					if (!empty($id)) {
						$user = get_user_by('id', $id);
						if ($user) {
							global $wpdb;
							$query = $wpdb->prepare("SELECT DISTINCT o.ID FROM {$wpdb->prefix}posts o JOIN {$wpdb->prefix}usermeta um WHERE um.meta_key = 'dermatologist' AND um.meta_value = '%s' AND o.post_type = 'shop_order' ORDER BY o.ID ASC", $user->data->user_nicename );
							$results = $wpdb->get_results($query, ARRAY_A);
							$return_data = [];
							if (is_array($results) && !empty($results)) {
								$i = 0;
								foreach ($results as $single) {
									$order = wc_get_order( $single['ID'] );
									$meta = get_post_meta($single['ID'], "miamimed_notificaiton_status", true);
									$return_data[$i] =[
										'ID' => $single['ID'],
										'Total' => $order->get_total(),
										'Order_Status' => $order->get_status(),
										"Notification_Status" => !empty($meta) ? $meta : false,
										'user_details' => get_userdata($order->get_user_id())->data,
									];
									foreach ( $order->get_items() as $item_id => $item ) {
										$product_id = $item->get_product_id();
										$return_data[$i]['products'][] = [
											"ID" => $product_id,
											"Name" => $item->get_name(),
											"Quantity" => $item->get_quantity(),
											"Subtotal" => $item->get_subtotal(),
											"Total" => $item->get_total(),
										];
									}
									$i++;
								}
								return [
									'status' => true,
									'message' => "All Orders",
									'data' => $return_data,
								];
							}else{
								return [
									'status' => false,
									'message' => "No Orders Available!",
								];
							}
						}else{
							return [
								'status' => false,
								'message' => "Invalid Dermotologist ID!",
							];
						}
					}else{
						return [
							'status' => false,
							'message' => "Dermotologist ID cannot be empty!",
						];
					}
				}
			} else {
				return array(
					'status' => false,
					'message' => "401 Unauthorized: Invalid Access Token!",
				);
			}
		} else {
			return array(
				'status' => false,
				'message' => "401 Unauthorized: Header cannot be empty!",
			);
		}
	}

	public function Miamimed_Telehealth_receive_callback_medical_history_by_user_id($request)
	{ 
		header('Access-Control-Allow-Origin: *');
		$headers = getallheaders();
		if (isset($headers['Authorization'])) {
			$auth_header = $headers['Authorization'];
			if (substr($auth_header, 0, 7) === 'Bearer ') {
				$token = substr($auth_header, 7);
				if ($token != get_option("miamimed_telehealth_access_token")) {
					return array(
						'status' => false,
						'message' => "401 Unauthorized: Invalid Access Token!",
					);
				} else {
					$parameters = $request->get_query_params();
					$user_id = $parameters['user_id'];
					global $wpdb;
					if (!empty($user_id)) {
						$user = get_user_by('id', $user_id);
						if ($user) {
							$survey_id = get_option('medical_history');
							$table = $wpdb->prefix . "ayssurvey_submissions";
							$result = $wpdb->get_row("SELECT id, questions_ids FROM `$table` WHERE user_id = $user_id AND survey_id = $survey_id ORDER BY id DESC;", ARRAY_A);
							$question_ids = explode(",", $result['questions_ids']);
							$submission_id = $result['id'];
							$medical_history = [];
							if (is_array($question_ids)) {
								foreach ($question_ids as $id) {
									$table = $wpdb->prefix . "ayssurvey_questions";
									$result = $wpdb->get_row("SELECT question, section_id FROM `$table` WHERE id = $id;", ARRAY_A);
									$question = $result['question'];
									$table = $wpdb->prefix . "ayssurvey_submissions_questions";
									$result = $wpdb->get_row("SELECT id, answer_id, user_answer, type FROM `$table` WHERE submission_id = $submission_id AND question_id = $id AND section_id = ".$result['section_id']." AND survey_id = $survey_id AND user_id = $user_id;", ARRAY_A);
									$table = $wpdb->prefix . "ayssurvey_answers";
									switch ($result['type']) {
										case 'checkbox':
											$checkbox = [];
											foreach (explode(",", $result['user_answer']) as $single) {
												$checkbox[] = $wpdb->get_row("SELECT answer FROM `$table` WHERE id = $single", ARRAY_A)['answer'];
											}
											$answer = implode(", ", $checkbox);
											break;
										case 'radio':
											$answer = $wpdb->get_row("SELECT answer FROM `$table` WHERE id = " . $result['answer_id'], ARRAY_A)['answer'];
											break;
										case 'text':
											$answer = $result['user_answer'];
											break;
									}
									$medical_history[$question] = $answer;
								}
							}
							return [
								'status' => true,
								'message' => "Medical History",
								'data' => $medical_history,
							];
						}else{
							return [
								'status' => false,
								'message' => "Invalid User ID!",
							];
						}
					}else{
						return [
							'status' => false,
							'message' => "User ID cannot be empty!",
						];
					}
				}
			} else {
				return array(
					'status' => false,
					'message' => "401 Unauthorized: Invalid Access Token!",
				);
			}
		} else {
			return array(
				'status' => false,
				'message' => "401 Unauthorized: Header cannot be empty!",
			);
		}
	}

	public function Miamimed_Telehealth_receive_callback_questionaries_by_product_id($request)
	{ 
		header('Access-Control-Allow-Origin: *');
		$headers = getallheaders();
		if (isset($headers['Authorization'])) {
			$auth_header = $headers['Authorization'];
			if (substr($auth_header, 0, 7) === 'Bearer ') {
				$token = substr($auth_header, 7);
				if ($token != get_option("miamimed_telehealth_access_token")) {
					return array(
						'status' => false,
						'message' => "401 Unauthorized: Invalid Access Token!",
					);
				} else {
					$parameters = $request->get_query_params();
					$order_id = $parameters['order_id'];
					$product_id = $parameters['product_id'];
					$user_id = $parameters['user_id'];
					if (!empty($order_id) && !empty( $product_id ) && !empty( $user_id ) ) {

						$total_completed_survery = get_post_meta( $order_id, 'total_completed_survery', true );
						$total_survery = explode( ',', $total_completed_survery );

						if( is_array( $total_survery ) && !empty( $total_survery ) ) {

							if( in_array( get_post_meta( $product_id , 'miamimed_questionary_questions', true ) , $total_survery  ) ) {
								global $wpdb;
								$survey_id = array_search(get_post_meta( $product_id , 'miamimed_questionary_questions', true), $total_survery);
								
								$table = $wpdb->prefix . 'ayssurvey_submissions';
								$query = $wpdb->prepare("SELECT id FROM $table WHERE user_id = %d AND survey_id = %d ORDER BY end_date DESC", $user_id, $total_survery[$survey_id]);
								$resultat = $wpdb->get_row($query, ARRAY_A);

								if( isset( $resultat['id'] ) ) {
									$table1 = $wpdb->prefix . 'ayssurvey_submissions_questions';
									$query1 = $wpdb->prepare("SELECT * FROM $table1 WHERE submission_id = %d", $resultat['id']);
									$resultat1 = $wpdb->get_results($query1, ARRAY_A);

									if (is_array($resultat1)) {
										$data = [];
										foreach ($resultat1 as $key => $value) {
											$table2 = $wpdb->prefix . 'ayssurvey_surveys';
											$query2 = $wpdb->prepare("SELECT id, title FROM $table2 WHERE id = %d", $value['survey_id']);
											$resultat2 = $wpdb->get_row($query2, ARRAY_A);
					
											$table3 = $wpdb->prefix . 'ayssurvey_questions';
											$query3 = $wpdb->prepare("SELECT id, section_id,question FROM $table3 WHERE id = %d", $value['question_id']);
											$resultat3 = $wpdb->get_row($query3, ARRAY_A);
					
											$table4 = $wpdb->prefix . 'ayssurvey_answers';
											$query4 = $wpdb->prepare("SELECT id, answer, ordering FROM $table4 WHERE id = %d", $value['answer_id']);
											$resultat4 = $wpdb->get_row($query4, ARRAY_A);

											// $data['question'][] = $resultat3['question'];
					
											$new_ids = [];
											if( 'checkbox' == $value['type'] ) {
												$answer_ids = $value['user_answer'];
												$answer_ids_a = explode(',', $answer_ids);
												foreach( $answer_ids_a as $key => $val ) {
													$table13 = $wpdb->prefix . 'ayssurvey_answers';
													$query13 = $wpdb->prepare("SELECT answer FROM $table13 WHERE id = %d", $val);
													$resultat13 = $wpdb->get_row($query13, ARRAY_A);
													$new_ids[] = $resultat13['answer'];
												}
											} else {
												if (empty($value['answer_id'])) {
													$answer1 = $value['user_answer'];
												} else {
													$answer1 = $resultat4['answer'];
												}
											}
											if( isset( $new_ids ) && !empty( $new_ids ) && is_array( $new_ids ) ) {
												$answer1 = implode(',',$new_ids);
											}
											// $data['answer'][] = $answer1;

											$data[$resultat3['question']] = $answer1;
										}
										return [
											'status' => true,
											'messaage' => "Questions and Answers",
											'data' => $data
										];
									}
								} else {
									return array(
										'status' => false,
										'message' => "User Id or Order Id is not found",
									);
								}	

							} else {
								return array(
									'status' => false,
									'message' => "No Questionnaire found associated with this Product Id",
								);
							}
						}  else {
							return array(
								'status' => false,
								'message' => "No Questionnaire found associated with this Order id",
							);
						}
					} else {
						return array(
							'status' => false,
							'message' => "Required paramteres are missing",
						);
					}
				}
			} else {
				return array(
					'status' => false,
					'message' => "401 Unauthorized: Invalid Access Token!",
				);
			}
		} else {
			return array(
				'status' => false,
				'message' => "401 Unauthorized: Header cannot be empty!",
			);
		}
	}

	public function Miamimed_Telehealth_receive_callback_orders_by_user_id($request)
	{
		header('Access-Control-Allow-Origin: *');
		$headers = getallheaders();
		if (isset($headers['Authorization'])) {
			$auth_header = $headers['Authorization'];
			if (substr($auth_header, 0, 7) === 'Bearer ') {
				$token = substr($auth_header, 7);
				if ($token != get_option("miamimed_telehealth_access_token")) {
					return array(
						'status' => false,
						'message' => "401 Unauthorized: Invalid Access Token!",
					);
				} else {
					$parameters = $request->get_query_params();
					$id = $parameters['id'];
					if (!empty($id)) {
						$user = get_user_by("id", $id);
						if ($user) {
							// Retrieve order query
							$orders = wc_get_orders([
								'customer_id' => $id,
								'numberposts' => -1,
							]);
							$all_orders = [];
							$i = 0;
							if (is_array($orders) && !empty($orders)) {
								// Loop through the orders
								foreach ($orders as $order) {
									$order_id = $order->get_id(); // Order ID
									$order_date = $order->get_date_created(); // Order Date
									$meta = json_decode(get_post_meta($order_id, "woodevz-stripe-all-products", true), true);
									$all_orders[$i] = [
										'ID' => $order_id,
										'Number' => $order->get_order_number(),
										'Date' => $order_date->date('Y-m-d H:i:s'),
										'Status' => $order->get_status(),
										'total' => $order->get_total(),
										'products' => [],
									];

									if (is_array($meta) && !empty($meta)) {
										foreach ( $meta as $id => $status ) {
											$product_id = $id;
											$product = wc_get_product($product_id);
											$all_orders[$i]['products'][] = [
												'ID' => $product_id,
												'Name' => $product->get_name(),
												'Image' => wp_get_attachment_image_src( get_post_thumbnail_id( $product_id ), 'single-post-thumbnail' )[0],
												'Description' => get_post($product_id)->post_content,
												'Status' => $status,
											];
										}										
									}else{
										foreach ( $order->get_items() as $item_id => $item ) {
											$product_id = $item->get_product_id();
											$all_orders[$i]['products'][] = [
												'ID' => $product_id,
												'Name' => $item->get_name(),
												'Image' => wp_get_attachment_image_src( get_post_thumbnail_id( $product_id ), 'single-post-thumbnail' )[0],
												'Description' => get_post($item['product_id'])->post_content,
												'Status' => "",
											];
										}
									}

									$i++;
								}
								return [
									'status' => true,
									'messaage' => "All Orders",
									'data' => $all_orders,
								];
							}else{
								return [
									'status' => false,
									'messaage' => "No Orders found!",
								];
							}

						} else {
							return array(
								'status' => false,
								'message' => 'Invalid User ID!',
							);
						}
					} else {
						return array(
							'status' => false,
							'message' => "User ID cannot be empty!",
						);
					}
				}
			} else {
				return array(
					'status' => false,
					'message' => "401 Unauthorized: Invalid Access Token!",
				);
			}
		} else {
			return array(
				'status' => false,
				'message' => "401 Unauthorized: Header cannot be empty!",
			);
		}
	}

	public function Miamimed_Telehealth_receive_callback_logout_user_by_id($request)
	{
		header('Access-Control-Allow-Origin: *');
		$headers = getallheaders();
		if (isset($headers['Authorization'])) {
			$auth_header = $headers['Authorization'];
			if (substr($auth_header, 0, 7) === 'Bearer ') {
				$token = substr($auth_header, 7);
				if ($token != get_option("miamimed_telehealth_access_token")) {
					return array(
						'status' => false,
						'message' => "401 Unauthorized: Invalid Access Token!",
					);
				} else {
					$id = $request->get_param('id');
					if (!empty($id)) {
						$user = get_user_by("id", $id);
						if ($user) {
							// Load WordPress core files
							require_once(ABSPATH . 'wp-load.php');

							// Log out the user
							wp_logout($id);

							return array(
								'status' => true,
								'message' => "User has been Logged out Successfully!",
							);
						} else {
							return array(
								'status' => false,
								'message' => 'Invalid User ID!',
							);
						}
					} else {
						return array(
							'status' => false,
							'message' => "User ID cannot be empty!",
						);
					}
				}
			} else {
				return array(
					'status' => false,
					'message' => "401 Unauthorized: Invalid Access Token!",
				);
			}
		} else {
			return array(
				'status' => false,
				'message' => "401 Unauthorized: Header cannot be empty!",
			);
		}
	}

	public function Miamimed_Telehealth_receive_callback_activate_deactivate_user_by_id($request)
	{
		header('Access-Control-Allow-Origin: *');
		$headers = getallheaders();
		if (isset($headers['Authorization'])) {
			$auth_header = $headers['Authorization'];
			if (substr($auth_header, 0, 7) === 'Bearer ') {
				$token = substr($auth_header, 7);
				if ($token != get_option("miamimed_telehealth_access_token")) {
					return array(
						'status' => false,
						'message' => "401 Unauthorized: Invalid Access Token!",
					);
				} else {
					$id = $request->get_param('id');
					if (!empty($id)) {
						$user = get_user_by("id", $id);
						$status = $request->get_param('status');
						if (!empty($status)) {
							if ($user) {
								update_user_meta($id, "miamimed_telehealth_activate_user", json_encode([
									'status' => $status == "true" ? true : false,
								]));
								$message = $status == "true" ? "Activated" : "Deactivated";
								return array(
									'status' => true,
									'message' => "User ".$message." Successfully!",
								);
							} else {
								return array(
									'status' => false,
									'message' => 'Invalid User ID!',
								);
							}							
						}else{
							return array(
								'status' => false,
								'message' => 'Status cannot be empty!',
							);
						}
					} else {
						return array(
							'status' => false,
							'message' => "User ID cannot be empty!",
						);
					}
				}
			} else {
				return array(
					'status' => false,
					'message' => "401 Unauthorized: Invalid Access Token!",
				);
			}
		} else {
			return array(
				'status' => false,
				'message' => "401 Unauthorized: Header cannot be empty!",
			);
		}
	}

	public function Miamimed_Telehealth_receive_callback_delete_user_profile_image($request)
	{
		header('Access-Control-Allow-Origin: *');
		$headers = getallheaders();
		if (isset($headers['Authorization'])) {
			$auth_header = $headers['Authorization'];
			if (substr($auth_header, 0, 7) === 'Bearer ') {
				$token = substr($auth_header, 7);
				if ($token != get_option("miamimed_telehealth_access_token")) {
					return array(
						'status' => false,
						'message' => "401 Unauthorized: Invalid Access Token!",
					);
				} else {
					global $wpdb;
					$user_id = $request->get_param('id');
					if (!empty($user_id)) {
						if (get_user_meta($user_id, "miamimed_telehealth_user_profile_deleted", true)) {
							return [
								"status" => false,
								"message" => "User Profile Already Deleted!",
							];
						}else{
							$attachment_id = get_user_meta($user_id, $wpdb->prefix . 'user_avatar', true);
							if ($attachment_id) {
								wp_delete_attachment($attachment_id, true);
								if (delete_user_meta($user_id, $wpdb->prefix . 'user_avatar')) {
									update_user_meta($user_id, "miamimed_telehealth_user_profile_deleted", true);
									return array(
										'status' => true,
										'message' => 'User Profile deleted Successfully!',
									);								
								}
								return array(
									'status' => false,
									'message' => 'User Profile Already Deleted!',
								);
							} else {
								return array(
									'status' => false,
									'message' => 'Invalid User ID!',
								);
							}
						}
					} else {
						return array(
							'status' => false,
							'message' => "User ID cannot be empty!",
						);
					}
				}
			} else {
				return array(
					'status' => false,
					'message' => "401 Unauthorized: Invalid Access Token!",
				);
			}
		} else {
			return array(
				'status' => false,
				'message' => "401 Unauthorized: Header cannot be empty!",
			);
		}
	}

	public function Miamimed_Telehealth_receive_callback_change_profile_image($request)
	{
		header('Access-Control-Allow-Origin: *');
		$headers = getallheaders();
		if (isset($headers['Authorization'])) {
			$auth_header = $headers['Authorization'];
			if (substr($auth_header, 0, 7) === 'Bearer ') {
				$token = substr($auth_header, 7);
				if ($token != get_option("miamimed_telehealth_access_token")) {
					return array(
						'status' => false,
						'message' => "401 Unauthorized: Invalid Access Token!",
					);
				} else {
					$id = $request->get_param('id');
					if (!empty($id)) {
						if (!empty($_FILES)) {
							$user = get_user_by("id", $id);
							if ($user) {
								global $wpdb;

								// Include the necessary WordPress files
								require_once(ABSPATH . 'wp-admin/includes/image.php');
								require_once(ABSPATH . 'wp-admin/includes/file.php');
								require_once(ABSPATH . 'wp-admin/includes/media.php');

								// Prepare the file information
								$file_info = wp_handle_upload($_FILES['image'], array('test_form' => FALSE));

								if (!isset($file_info['error'])) {
									// Create the attachment post
									$attachment = array(
										'post_mime_type' => $file_info['type'],
										'post_title' => sanitize_file_name($file_info['file']),
										'post_content' => '',
										'post_status' => 'inherit'
									);

									// Insert the attachment post
									$attachment_id = wp_insert_attachment($attachment, $file_info['file']);

									// Generate metadata for the attachment
									$attachment_data = wp_generate_attachment_metadata($attachment_id, $file_info['file']);

									// Update the metadata for the attachment
									wp_update_attachment_metadata($attachment_id, $attachment_data);

									// Set the attachment as the user's profile picture
									update_user_meta($user->ID, $wpdb->prefix . 'user_avatar', $attachment_id);

									delete_user_meta($user->ID, "miamimed_telehealth_user_profile_deleted");

									return [
										"status" => true,
										"message" => "User Profile Image Changed Successfully!",
									];
								}else{
									return [
										"status" => false,
										"message" => $file_info['error'],
									];
								}
	
							} else {
								return array(
									'status' => false,
									'message' => 'Invalid User ID!',
								);
							}							
						}else{
							return array(
								'status' => false,
								'message' => "Please Select a file!",
							);
						}
					} else {
						return array(
							'status' => false,
							'message' => "User ID cannot be empty!",
						);
					}
				}
			} else {
				return array(
					'status' => false,
					'message' => "401 Unauthorized: Invalid Access Token!",
				);
			}
		} else {
			return array(
				'status' => false,
				'message' => "401 Unauthorized: Header cannot be empty!",
			);
		}
	}

	public function Miamimed_Telehealth_receive_callback_get_patients_by_dermatologist_id($request)
	{
		header('Access-Control-Allow-Origin: *');
		$headers = getallheaders();
		if (isset($headers['Authorization'])) {
			$auth_header = $headers['Authorization'];
			if (substr($auth_header, 0, 7) === 'Bearer ') {
				$token = substr($auth_header, 7);
				if ($token != get_option("miamimed_telehealth_access_token")) {
					return array(
						'status' => false,
						'message' => "401 Unauthorized: Invalid Access Token!",
					);
				} else {
					$parameters = $request->get_query_params();
					$id = $parameters['id'];

					$return_data = get_users([
						'role' => 'patient',
						'orderby' => 'user_nicename',
						'order' => 'ASC',
						// 'meta_query' => [
						// 	'key' => 'dermatologist',
						// 	'value' => $user->data->user_nicename,
						// ],
					]);
					// $nickname = $user->data->user_nicename;
					$return = [];

					foreach ($return_data as $user) {
						$meta = get_user_meta($user->ID, "dermatologist", true);
						// if ($meta == $nickname) {
						// }
						$user->data->first_name = get_user_meta($user->ID, "first_name", true);
						$user->data->last_name = get_user_meta($user->ID, "last_name", true);
						$return[] = [
							$user,
						];
					}

					return array(
						'status' => true,
						'message' => !empty($return) ? "List of all Patients." : "No Patients Available!",
						'data' => $return,
					);

					// if (!empty($id)) {
					// 	$user = get_user_by("id", $id);
					// 	if ($user) {
					// 		$return_data = get_users([
					// 			'role' => 'patient',
					// 			'orderby' => 'user_nicename',
					// 			'order' => 'ASC',
					// 			// 'meta_query' => [
					// 			// 	'key' => 'dermatologist',
					// 			// 	'value' => $user->data->user_nicename,
					// 			// ],
					// 		]);
					// 		$nickname = $user->data->user_nicename;
					// 		$return = [];

					// 		foreach ($return_data as $user) {
					// 			$meta = get_user_meta($user->ID, "dermatologist", true);
					// 			// if ($meta == $nickname) {
					// 			// }
					// 			$user->data->first_name = get_user_meta($user->ID, "first_name", true);
					// 			$user->data->last_name = get_user_meta($user->ID, "last_name", true);
					// 			$return[] = [
					// 				$user,
					// 			];
					// 		}

					// 		return array(
					// 			'status' => true,
					// 			'message' => !empty($return) ? "List of all Patients." : "No Patients Available!",
					// 			'data' => $return,
					// 		);
					// 	} else {
					// 		return array(
					// 			'status' => false,
					// 			'message' => 'Invalid User ID!',
					// 		);
					// 	}
					// } else {
					// 	return array(
					// 		'status' => false,
					// 		'message' => "User ID cannot be empty!",
					// 	);
					// }
				}
			} else {
				return array(
					'status' => false,
					'message' => "401 Unauthorized: Invalid Access Token!",
				);
			}
		} else {
			return array(
				'status' => false,
				'message' => "401 Unauthorized: Header cannot be empty!",
			);
		}
	}

	public function Miamimed_Telehealth_receive_callback_get_profile_data($request)
	{
		header('Access-Control-Allow-Origin: *');
		$headers = getallheaders();
		if (isset($headers['Authorization'])) {
			$auth_header = $headers['Authorization'];
			if (substr($auth_header, 0, 7) === 'Bearer ') {
				$token = substr($auth_header, 7);
				if ($token != get_option("miamimed_telehealth_access_token")) {
					return array(
						'status' => false,
						'message' => "401 Unauthorized: Invalid Access Token!",
					);
				} else {
					global $wpdb;
					$id = $request->get_param('id');
					if (!empty($id)) {
						$user = get_user_by("id", $id);
						if ($user) {
							$return_data = get_userdata($user->ID);
							foreach ($return_data->roles as $role) {
								$return_data->roles = [$role];
							}
							$activation = json_decode(get_user_meta($user->ID, "miamimed_telehealth_activate_user", true), true);
							$return_data->data->first_name = get_user_meta($user->ID, "first_name", true);
							$return_data->data->last_name = get_user_meta($user->ID, "last_name", true);
							$return_data->data->key = get_user_meta($user->ID, "miamimed_telehealth_user_key", true);
							$return_data->data->profile_image = wp_get_attachment_url(get_user_meta($user->ID, $wpdb->prefix . 'user_avatar', true));
							$return_data->data->activation_status = is_array($activation) && $activation['status'] ? true : false;
							return array(
								'status' => true,
								'message' => "User Profile Details!",
								'data' => $return_data,
							);
						} else {
							return array(
								'status' => false,
								'message' => 'Invalid User ID!',
							);
						}
					} else {
						return array(
							'status' => false,
							'message' => "User ID cannot be empty!",
						);
					}
				}
			} else {
				return array(
					'status' => false,
					'message' => "401 Unauthorized: Invalid Access Token!",
				);
			}
		} else {
			return array(
				'status' => false,
				'message' => "401 Unauthorized: Header cannot be empty!",
			);
		}
	}

	public function Miamimed_Telehealth_receive_callback_reset_password_api($request)
	{
		header('Access-Control-Allow-Origin: *');
		$headers = getallheaders();
		if (isset($headers['Authorization'])) {
			$auth_header = $headers['Authorization'];
			if (substr($auth_header, 0, 7) === 'Bearer ') {
				$token = substr($auth_header, 7);
				if ($token != get_option("miamimed_telehealth_access_token")) {
					return array(
						'status' => false,
						'message' => "401 Unauthorized: Invalid Access Token!",
					);
				} else {
					$id = $request->get_param('id');
					$new_password = $request->get_param('new_password');
					$confirm_password = $request->get_param('confirm_password');

					if (
						!empty($id) && !empty($new_password) && !empty($confirm_password)
					) {
						$user = get_user_by("id", $id);
						if ($user) {
							if ($new_password == $confirm_password) {
								wp_set_password($new_password, $user->ID);
								return array(
									'status' => true,
									'message' => 'Password Reset Successfully!',
								);
							} else {
								return array(
									'status' => false,
									'message' => "Your Passwords doesn't match!",
								);
							}
						} else {
							return array(
								'status' => false,
								'message' => 'Invalid User ID!',
							);
						}
					} else {
						return array(
							'status' => false,
							'message' => "Incompleted Data!",
						);
					}
				}
			} else {
				return array(
					'status' => false,
					'message' => "401 Unauthorized: Invalid Access Token!",
				);
			}
		} else {
			return array(
				'status' => false,
				'message' => "401 Unauthorized: Header cannot be empty!",
			);
		}
	}

	public function Miamimed_Telehealth_receive_callback_change_password($request)
	{
		header('Access-Control-Allow-Origin: *');
		$headers = getallheaders();
		if (isset($headers['Authorization'])) {
			$auth_header = $headers['Authorization'];
			if (substr($auth_header, 0, 7) === 'Bearer ') {
				$token = substr($auth_header, 7);
				if ($token != get_option("miamimed_telehealth_access_token")) {
					return array(
						'status' => false,
						'message' => "401 Unauthorized: Invalid Access Token!",
					);
				} else {

					$id = $request->get_param('id');
					$old_password = $request->get_param('old_password');
					$new_password = $request->get_param('new_password');
					$confirm_password = $request->get_param('confirm_password');

					if (
						!empty($id) && !empty($old_password)
						&& !empty($new_password) && !empty($confirm_password)
					) {
						$user = get_user_by("id", $id);
						if ($user) {
							if (wp_check_password($old_password, $user->data->user_pass, $user->ID)) {
								if ($new_password == $confirm_password) {
									wp_set_password($new_password, $user->ID);
									return array(
										'status' => true,
										'message' => 'Password Changed Successfully!',
									);
								} else {
									return array(
										'status' => false,
										'message' => "Your Passwords doesn't match!",
									);
								}
							} else {
								return array(
									'status' => false,
									'message' => 'Incorrect Old Password!',
								);
							}
						} else {
							return array(
								'status' => false,
								'message' => 'Invalid User ID!',
							);
						}
					} else {
						return array(
							'status' => false,
							'message' => "Incompleted Data!",
						);
					}
				}
			} else {
				return array(
					'status' => false,
					'message' => "401 Unauthorized: Invalid Access Token!",
				);
			}
		} else {
			return array(
				'status' => false,
				'message' => "401 Unauthorized: Header cannot be empty!",
			);
		}
	}

	public function Miamimed_Telehealth_receive_callback_send_forgot_password_mail($request)
	{
		header('Access-Control-Allow-Origin: *');
		$headers = getallheaders();
		if (isset($headers['Authorization'])) {
			$auth_header = $headers['Authorization'];
			if (substr($auth_header, 0, 7) === 'Bearer ') {
				$token = substr($auth_header, 7);

				if ($token != get_option("miamimed_telehealth_access_token")) {
					return array(
						'status' => false,
						'message' => "401 Unauthorized: Invalid Access Token!",
					);
				} else {
					$email = $request->get_param('email');
					$message = $request->get_param('message');
					$subject = $request->get_param('subject');
					$url = $request->get_param('url');

					if (!empty($email) && !empty($message) && !empty($subject)) {
						if (email_exists($email)) {
							$user = get_user_by('email', $email);
							if ($user) {
								$headers = array('Content-Type: text/html; charset=UTF-8', 'From: ' . get_bloginfo() . ' <noreply@' . $_SERVER['SERVER_NAME'] . '.com>' . "\r\n");
								$mail = wp_mail($user->data->user_email, $subject, $message . '<a href="' . add_query_arg('id', $user->ID, $url) . '" target="_blank"> Click Here</a>', $headers);
								if ($mail) {
									return [
										'status' => true,
										'message' => 'Mail sent successfully.',
									];
								} else {
									return [
										'status' => false,
										'message' => 'Invalid User ID!',
									];
								}
							} else {
								return [
									'status' => false,
									'message' => 'Invalid User Email!',
								];
							}
						} else {
							return [
								'status' => false,
								'message' => "Email doesn't exists!",
							];
						}
					} else {
						return [
							'status' => false,
							'message' => 'Incomplete Data!',
						];
					}
				}
			} else {
				return array(
					'status' => false,
					'message' => "401 Unauthorized: Invalid Access Token!",
				);
			}
		} else {
			return array(
				'status' => false,
				'message' => "401 Unauthorized: Header cannot be empty!",
			);
		}
	}

	public function Miamimed_Telehealth_receive_callback_update_profile($request)
	{
		header('Access-Control-Allow-Origin: *');
		$headers = getallheaders();
		if (isset($headers['Authorization'])) {
			$auth_header = $headers['Authorization'];
			if (substr($auth_header, 0, 7) === 'Bearer ') {
				$token = substr($auth_header, 7);

				if ($token != get_option("miamimed_telehealth_access_token")) {
					return array(
						'status' => false,
						'message' => "401 Unauthorized: Invalid Access Token!",
					);
				} else {
					$id = $request->get_param('id');
					$email = $request->get_param('email');
					$role = $request->get_param('role');
					$first_name = $request->get_param('first_name');
					$last_name = $request->get_param('last_name');
					$nickname = $request->get_param('nickname');
					$user_url = $request->get_param('url');

					if (!empty($id)) {
						$user = get_user_by("id", $id);
						if ($user) {
							$update_data = ['ID' => $id,];

							if (!empty($email)) {
								$update_data['user_email'] = $email;
							}
							if (!empty($role)) {
								$update_data['role'] = $role;
							}
							if (!empty($first_name)) {
								$update_data['first_name'] = $first_name;
							}
							if (!empty($last_name)) {
								$update_data['last_name'] = $last_name;
							}
							if (!empty($nickname)) {
								$update_data['nickname'] = $nickname;
							}
							if (!empty($user_url)) {
								$update_data['user_url'] = $user_url;
							}

							$user_id = wp_update_user($update_data);

							if (is_wp_error($user_id)) {
								return array(
									'status' => false,
									'message' => $user_id->get_error_message(),
								);
							} else {
								$return_data = get_userdata($user_id);
								$return_data->data->first_name = get_user_meta($user_id, "first_name", true);
								$return_data->data->last_name = get_user_meta($user_id, "last_name", true);
								foreach ($return_data->roles as $role) {
									$return_data->roles = [$role];
								}
								return array(
									'status' => true,
									'message' => "User Profile Update Successfully!",
									'data' => $return_data,
								);
							}
						} else {
							return array(
								'status' => false,
								'message' => 'Invalid User ID!',
							);
						}
					} else {
						return array(
							'status' => false,
							'message' => "User ID cannot be empty!",
						);
					}
				}
			} else {
				return array(
					'status' => false,
					'message' => "401 Unauthorized: Invalid Access Token!",
				);
			}
		} else {
			return array(
				'status' => false,
				'message' => "401 Unauthorized: Header cannot be empty!",
			);
		}
	}

	public function Miamimed_Telehealth_receive_callback_login($request)
	{
		header('Access-Control-Allow-Origin: *');
		$headers = getallheaders();
		if (isset($headers['Authorization'])) {
			$auth_header = $headers['Authorization'];
			if (substr($auth_header, 0, 7) === 'Bearer ') {
				$token = substr($auth_header, 7);

				if ($token != get_option("miamimed_telehealth_access_token")) {
					return array(
						'status' => false,
						'message' => "401 Unauthorized: Invalid Access Token!",
					);
				} else {
					global $wpdb;
					$user_login = $request->get_param('email');
					$user_password = $request->get_param('password');

					if (!empty($user_login) && !empty($user_password)) {
						$user = wp_authenticate($user_login, $user_password);

						if (is_wp_error($user)) {
							return array(
								'status' => false,
								'message' => $user->get_error_message(),
							);
						} else {
							$activation = json_decode(get_user_meta($user->ID, "miamimed_telehealth_activate_user", true), true);

							$return_data = get_userdata($user->ID);
							$return_data->data->first_name = get_user_meta($user->ID, "first_name", true);
							$return_data->data->last_name = get_user_meta($user->ID, "last_name", true);
							$return_data->data->key = get_user_meta($user->ID, "miamimed_telehealth_user_key", true);
							$return_data->data->profile_image = wp_get_attachment_url(get_user_meta($user->ID, $wpdb->prefix . 'user_avatar', true));
							$return_data->data->activation_status = is_array($activation) && $activation['status'] ? true : false;
							foreach ($return_data->roles as $role) {
								$return_data->roles = [$role];
							}
							return array(
								'status' => true,
								'message' => "Authorization Successfull",
								'data' => $return_data,
							);
						}
					} else {
						return array(
							'status' => false,
							'message' => "Incomplete Data!",
						);
					}
				}
			} else {
				return array(
					'status' => false,
					'message' => "401 Unauthorized: Invalid Access Token!",
				);
			}
		} else {
			return array(
				'status' => false,
				'message' => "401 Unauthorized: Header cannot be empty!",
			);
		}
	}

	public function Miamimed_Telehealth_receive_callback_email_verification()
	{
		header('Access-Control-Allow-Origin: *');
		header("Content-Type: text/html");
		if (isset($_GET['email']) && isset($_GET['hash'])) {
			$user = get_user_by('email', base64_decode($_GET['email']));
			if ($user) {
				$hash = get_user_meta($user->ID, 'email_verification_hash', true);
				if ($_GET['hash'] === $hash) {
					update_user_meta($user->ID, 'email_verified', true);
					$response = '<p>Email Verified Successfully!</p><p>Now you can <a href="' . get_permalink(get_option('woocommerce_myaccount_page_id')) . '?user_login">Login</a></p>';
					die($response);
				}
			}
		}
		$response = '<p>Sorry, we were unable to verify your email address.</p>';
		die($response);
	}

	public function Miamimed_Telehealth_receive_callback_reset_password()
	{
		header('Access-Control-Allow-Origin: *');
		header("Content-Type: text/html");
		?>
		<!doctype html>
		<html lang="en">

		<head>
			<meta charset="utf-8">
			<meta name="viewport" content="width=device-width, initial-scale=1">
			<title>Reset Password</title>
			<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
		</head>

		<body>

			<div class="my-5 container w-25">

				<h5>Reset Password</h5>

				<div class="mb-3">

					<label for="new_password" class="form-label">New Password</label>

					<input type="password" class="form-control" id="new_password">

				</div>

				<div class="mb-3">

					<label for="confirm_new_password" class="form-label">Confirm New Password</label>

					<input type="password" class="form-control" id="confirm_new_password">

				</div>

				<button id="generate_new_password" class="btn btn-primary">Submit</button>

			</div>

			<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

			<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>

			<script>
				$(document).ready(function() {

					$("#generate_new_password").on("click", function() {

						const password = $("#new_password").val()

						const confirm_password = $("#confirm_new_password").val()

						if (password !== confirm_password) {

							alert("Passwords doesn't match!");

						} else {

							$.ajax({

								url: window.location.origin + '/wp-admin/admin-ajax.php',

								type: 'POST',

								data: {

									action: 'reset_password',

									key: 'reset_password_endpoint',

									nonce: window.location.search.split("&")[1].split("=")[1],

									email: window.location.search.split("&")[0].split("=")[1],

									password: password,

								},

								success: function(data) {

									data = JSON.parse(data);

									alert(data.message);

									if (data.status == 'success') {

										setTimeout(() => {

											window.location.href = data.url;

										}, 1000);

									}

								},

								error: function(data) {

									alert(data);

								}

							});

						}

					})

				})
			</script>

		</body>

		</html>
		<?php
	}

	public function Patient_send_otp()
	{
		if (isset($_POST['nonce']) & !empty($_POST['nonce'])) {
			if (wp_verify_nonce($_POST['nonce'], 'patient-page-nonce')) {
				if (
					isset($_POST['email']) && !empty($_POST['email']) &&
					isset($_POST['key']) && !empty($_POST['key'])
				) {
					$email = $_POST['email'];
					$otp = rand(100000, 999999);
					$user = get_user_by('email', $email);
					if ($user) {
						update_user_meta($user->ID, 'miamimed_telehealth_email_otp', $otp);
						// Send OTP to user
						$headers = array('Content-Type: text/html; charset=UTF-8', 'From: ' . get_bloginfo() . ' <noreply@' . $_SERVER['SERVER_NAME'] . '.com>' . "\r\n");
						$to = $email;
						$subject = "";
						$message = 'Your OTP is: ' . $otp;
						switch ($_POST['key']) {
							case 'email_verify':
								$subject = 'Email OTP Verification';
								break;
							case 'reset_pass':
								$subject = 'Reset Password';
								break;
							case 'password_expired':
								$subject = 'Reset Password';
								$message = "Click on the following link to reset your password. <br> <a href='" . home_url() . '/wp-json/Miamimed_Telehealth/v1/reset_password' . "' targer='_blank'>Reset Password</a>";
								break;
						}
						$mail = wp_mail($to, $subject, $message, $headers);
						if ($mail) {
							$response['status'] = "success";
							$response['message'] = 'OTP sent successfully.';
						} else {
							$response['status'] = 'error';
							$response['message'] = "Invalid Email!";
						}
					}else{
						$response['status'] = 'error';
						$response['message'] = "Email not registered!";
					}

				} else {
					$response['status'] = 'error';
					$response['message'] = "Invalid Data!";
				}
			} else {
				$response['status'] = 'error';
				$response['message'] = "Nonce verification failed!";
			}
		} else {
			$response['status'] = 'error';
			$response['message'] = "Nonce not available!";
		}
		echo json_encode($response);
		wp_die();
	}

	function user_has_role($user_id, $role_name)
	{
		$user_data = get_userdata($user_id);
		$user_roles = $user_data->roles;
		return in_array($role_name, $user_roles);
	}

	public function Patient_login_user()
	{
		if (isset($_POST['nonce']) & !empty($_POST['nonce'])) {
			if (wp_verify_nonce($_POST['nonce'], 'patient-page-nonce')) {
				if (
					isset($_POST['username']) && !empty($_POST['username']) &&
					isset($_POST['password']) && !empty($_POST['password'])
				) {
					$username = $_POST['username'];
					$password = $_POST['password'];
					$user = get_user_by("email", $username);
					if ($user) {
						if ($this->user_has_role($user->data->ID, "patient")) {
							if (get_user_meta($user->data->ID, "email_verified", true)) {
								$user = wp_authenticate($username, $password);
								if (is_wp_error($user)) {
									$response['status'] = 'error';
									$response['message'] = $user->get_error_message();
								} else {
									$no_of_days = round((time() - get_user_meta($user->ID, "miamimed_telehealth_password_expiry", true)) / (60 * 60 * 24));
									if ($no_of_days < 60) {
										$two_factor_auth_meta = json_decode(get_user_meta($user->ID, "miamimed_telehealth_2fa", true), true);
										if ($two_factor_auth_meta['enable']) {
											$headers = array('Content-Type: text/html; charset=UTF-8', 'From: ' . get_bloginfo() . ' <noreply@' . $_SERVER['SERVER_NAME'] . '.com>' . "\r\n");
											$mail = wp_mail($username, "Two-Factor Authentication", "Your 2FA code is : " . $two_factor_auth_meta['secret'] , $headers); // Send the email
											if ($mail) {
												$response['status'] = "success";
												$response['message'] = "2FA Enabled!";
											} else {
												$response['status'] = 'error';
												$response['message'] = "Invalid Email!";
											}
										} else {
											$activation = json_decode(get_user_meta($user->ID, "miamimed_telehealth_activate_user", true), true);
											if ($activation['status']) {
												wp_set_current_user($user->ID);
												wp_set_auth_cookie($user->ID);
												do_action('wp_login', $user->user_login, $user);
												$response['status'] = "success";
												$response['message'] = home_url();
											} else {
												$response['status'] = false;
												$response['message'] = "Your account has been deactivated. Please ask the " . get_bloginfo() . " Administrator to activate your account!";
											}
										}
									} else {
										$headers = array('Content-Type: text/html; charset=UTF-8', 'From: ' . get_bloginfo() . ' <noreply@' . $_SERVER['SERVER_NAME'] . '.com>' . "\r\n");
										$mail = wp_mail($username, "Reset Password", "Click on the following link to reset your password. <br> <a href='" . home_url() . '/wp-json/Miamimed_Telehealth/v1/reset_password?email=' . base64_encode($username) . "&nonce=" . $_POST['nonce'] . "' targer='_blank'>Reset Password</a>", $headers);
										if ($mail) {
											$response['status'] = 'error';
											$response['message'] = "Your Password has expired. We have sent a reset password link on your registered email.";
										} else {
											$response['status'] = 'error';
											$response['message'] = "Invalid Email!";
										}
									}
								}
							} else {
								$response['status'] = 'error';
								$response['message'] = "Please Verify your email first!";
							}							
						}else{
							$response['status'] = 'error';
							$response['message'] = "Sorry! You're not allowed to Login!";
						}
					}else{
						$response['status'] = 'error';
						$response['message'] = "Invalid Credentials!";
					}
				} else {
					$response['status'] = 'error';
					$response['message'] = "Incomplete Credentials!";
				}
			} else {
				$response['status'] = 'error';
				$response['message'] = "Nonce verification failed!";
			}
		} else {
			$response['status'] = 'error';
			$response['message'] = "Nonce not available!";
		}
		echo json_encode($response);
		wp_die();
	}

	function Patient_send_verfication_mail($user_id)
	{
		if (!empty($user_id)) {
			$user = get_user_by('id', $user_id);
			$email = $user->user_email;
			$hash = md5($email . time()); // Generate a unique hash
			update_user_meta($user_id, 'email_verification_hash', $hash); // Store the hash in user meta
			$subject = 'Verify your email address';
			$message = 'Please click on the following link to verify your email address: <a href="' . home_url() . '/wp-json/Miamimed_Telehealth/v1/email_verification?email=' . base64_encode($email) . '&hash=' . $hash . '" targer="_blank">Verify Email</a>';
			$headers = array('Content-Type: text/html; charset=UTF-8', 'From: ' . get_bloginfo() . ' <noreply@' . $_SERVER['SERVER_NAME'] . '.com>' . "\r\n");
			$mail = wp_mail($email, $subject, $message, $headers); // Send the email
			if ($mail) {
				$response['status'] = "success";
				$response['message'] = 'Verification link sent successfully.';
			} else {
				$response['status'] = 'error';
				$response['message'] = "Invalid Email!";
			}
		} else {
			if (isset($_POST['nonce']) & !empty($_POST['nonce'])) {
				if (wp_verify_nonce($_POST['nonce'], 'patient-page-nonce')) {
					if (
						isset($_POST['email']) && !empty($_POST['email']) &&
						isset($_POST['key']) && !empty($_POST['key'])
					) {
						$email = $_POST['email'];
						$userid = get_user_by("email", $email)->data->ID;
						$hash = md5($email . time()); // Generate a unique hash
						update_user_meta($userid, 'email_verification_hash', $hash); // Store the hash in user meta
						switch ($_POST['key']) {
							case 'reset_password':
								$subject = 'Reset Password';
								$message = "Click on the following link to reset your password. <br> <a href='" . home_url() . '/wp-json/Miamimed_Telehealth/v1/reset_password?email=' . base64_encode($_POST['email']) . "' targer='_blank'>Reset Password</a>";
								$success = "Reset Password Link sent successfully!";
								break;
							case 'send_verification_mail':
								$subject = 'Verify your email address';
								$message = 'Please click on the following link to verify your email address: <a href="' . home_url() . '/wp-json/Miamimed_Telehealth/v1/email_verification?email=' . base64_encode($email) . '&hash=' . $hash . '" targer="_blank">Verify Email</a>';
								$success = "Verification Link sent successfully!";
								break;
						}
						$headers = array('Content-Type: text/html; charset=UTF-8', 'From: ' . get_bloginfo() . ' <noreply@' . $_SERVER['SERVER_NAME'] . '.com>' . "\r\n");
						$mail = wp_mail($email, $subject, $message, $headers); // Send the email
						if ($mail) {
							$response['status'] = "success";
							$response['message'] = $success;
						} else {
							$response['status'] = 'error';
							$response['message'] = "Invalid Email!";
						}
					} else {
						$response['status'] = 'error';
						$response['message'] = "Please provide your email!";
					}
				} else {
					$response['status'] = 'error';
					$response['message'] = "Nonce Verification Failed!";
				}
			} else {
				$response['status'] = 'error';
				$response['message'] = "Nonce Not Avaliable!";
			}
		}

		if (empty($user_id)) {
			echo json_encode($response);
		} else {
			return json_encode($response);
		}
		wp_die();
	}

	public function miamimed_login_resend_2fa_code() {
		if (isset($_POST['nonce']) & !empty($_POST['nonce'])) {
			if (wp_verify_nonce($_POST['nonce'], 'patient-page-nonce')) {
				if (
					isset($_POST['email']) && !empty($_POST['email'])
				) {
					$user = get_user_by("email", $_POST['email']);
					if ($user) {
						$secret = rand(100000, 999999);
						update_user_meta($user->ID, 'miamimed_telehealth_2fa', wp_json_encode([
							'enable' => false,
							'secret' => $secret,
						]));
						$headers = array('Content-Type: text/html; charset=UTF-8', 'From: ' . get_bloginfo() . ' <noreply@' . $_SERVER['SERVER_NAME'] . '.com>' . "\r\n");
						$message = 'Your code is: ' . $secret;
						$mail = wp_mail($_POST['email'], "2FA Verification Code", $message, $headers);
						if ($mail) {
							$response['status'] = true;
							$response['message'] = "Verificaiton Code Sent Successfully!";
						}else{
							$response['status'] = false;
							$response['message'] = "Invalid Email!";
						}
					}else{
						$response['status'] = false;
						$response['message'] = "Invalid Email!";
					}
				} else {
					$response['status'] = false;
					$response['message'] = "Invalid Data!";
				}
			} else {
				$response['status'] = false;
				$response['message'] = "Nonce verification failed!";
			}
		} else {
			$response['status'] = false;
			$response['message'] = "Nonce not available!";
		}
		echo json_encode($response);
		wp_die();
	}

	public function miamimed_login_resend_2fa_code_verify() {
		if (isset($_POST['nonce']) & !empty($_POST['nonce'])) {
			if (wp_verify_nonce($_POST['nonce'], 'patient-page-nonce')) {
				if (
					isset($_POST['code']) && !empty($_POST['code']) &&
					isset($_POST['email']) && !empty($_POST['email'])
				) {
					$user = get_user_by("email", $_POST['email']);
					if ($user) {
						$secret = json_decode(get_user_meta($user->ID, 'miamimed_telehealth_2fa', true), true)['secret'];
						if ($secret == $_POST['code']) {
							wp_set_current_user($user->ID);
							wp_set_auth_cookie($user->ID);
							do_action('wp_login', $user->user_login, $user);
							$response['status'] = true;
							$response['url'] = get_permalink(get_option('woocommerce_myaccount_page_id'));
							$response['message'] = "Verificaiton Successfull!";
						}else{
							$response['status'] = false;
							$response['message'] = "Invalid Verificaiton Code!";
						}
					}else{
						$response['status'] = false;
						$response['message'] = "Invalid Email!";
					}
				} else {
					$response['status'] = false;
					$response['message'] = "Invalid Data!";
				}
			} else {
				$response['status'] = false;
				$response['message'] = "Nonce verification failed!";
			}
		} else {
			$response['status'] = false;
			$response['message'] = "Nonce not available!";
		}
		echo json_encode($response);
		wp_die();
	}

	public function Patient_signup_user()
	{
		if (isset($_POST['nonce']) & !empty($_POST['nonce'])) {
			if (wp_verify_nonce($_POST['nonce'], 'patient-page-nonce')) {
				if (
					isset($_POST['PatientSignUpFName']) && !empty($_POST['PatientSignUpFName']) &&
					isset($_POST['PatientSignUpLName']) && !empty($_POST['PatientSignUpLName']) &&
					isset($_POST['PatientSignUpEmail']) && !empty($_POST['PatientSignUpEmail']) &&
					isset($_POST['PatientSignUpUsername']) && !empty($_POST['PatientSignUpUsername']) &&
					isset($_POST['PatientSignUpPassword']) && !empty($_POST['PatientSignUpPassword'])
				) {
					$username = $_POST['PatientSignUpUsername'];
					$first_name  = $_POST['PatientSignUpFName'];
					$last_name  = $_POST['PatientSignUpLName'];
					$email = $_POST['PatientSignUpEmail'];
					$password = $_POST['PatientSignUpPassword'];

					$user_id = wp_insert_user([
						'user_login' => $username,
						'first_name' => $first_name,
						'last_name' => $last_name,
						'user_pass' => $password,
						'user_email' => $email,
						'role' => 'patient',
					]);

					if (is_wp_error($user_id)) {
						$response['status'] = 'error';
						$response['message'] = $user_id->get_error_message();
					} else {
						$otp = rand(100000, 999999);
						// $secret = $this->google_auth->createSecret(32);
						$secret = rand(100000, 999999);
						update_user_meta($user_id, 'miamimed_telehealth_email_otp', $otp);
						update_user_meta($user_id, 'miamimed_telehealth_password_expiry', time());
						update_user_meta($user_id, 'miamimed_telehealth_user_key', base64_encode($username));
						update_user_meta($user_id, 'miamimed_telehealth_2fa', wp_json_encode([
							'enable' => false,
							'secret' => $secret,
						]));

						// Send OTP to user
						$headers = array('Content-Type: text/html; charset=UTF-8', 'From: ' . get_bloginfo() . ' <noreply@' . $_SERVER['SERVER_NAME'] . '.com>' . "\r\n");
						$to = $email;
						$subject = 'Email OTP Verification';
						$message = 'Your OTP is: ' . $otp;
						$mail = wp_mail($to, $subject, $message, $headers);
						// $mail = mail($to, $subject, $message, $headers);
						if ($mail) {
							$response['status'] = "success";
							$response['message'] = 'OTP sent successfully.';
						} else {
							$response['status'] = 'error';
							$response['message'] = "Invalid Email!";
						}
					}
				} else {
					$response['status'] = 'error';
					$response['message'] = "Invalid Data!";
				}
			} else {
				$response['status'] = 'error';
				$response['message'] = "Nonce verification failed!";
			}
		} else {
			$response['status'] = 'error';
			$response['message'] = "Nonce not available!";
		}
		echo json_encode($response);
		wp_die();
	}

	public function Patient_verify_otp()
	{
		if (isset($_POST['nonce']) & !empty($_POST['nonce'])) {
			if (wp_verify_nonce($_POST['nonce'], 'patient-page-nonce')) {
				if (
					isset($_POST['email']) && !empty($_POST['email']) &&
					isset($_POST['otp']) && !empty($_POST['otp'])
				) {
					$email = $_POST['email'];
					$otp = $_POST['otp'];
					$user = get_user_by('email', $email);
					$saved_otp = get_user_meta($user->ID, 'miamimed_telehealth_email_otp', true);
					if ($otp == $saved_otp) {
						if (isset($_POST['key']) && $_POST['key'] == "forgot_password_verify") {
							$response['status'] = "success";
							$response['message'] = 'OTP verification Successfull.';
						} else {
							$response = json_decode($this->Patient_send_verfication_mail($user->ID), true);
						}
					} else {
						$response['status'] = 'error';
						$response['message'] = 'OTP verification failed.';
					}
				} else {
					$response['status'] = 'error';
					$response['message'] = "Invalid Data!";
				}
			} else {
				$response['status'] = 'error';
				$response['message'] = "Nonce verification failed!";
			}
		} else {
			$response['status'] = 'error';
			$response['message'] = "Nonce not available!";
		}
		echo json_encode($response);
		wp_die();
	}

	public function Patient_reset_password()
	{
		if (isset($_POST['nonce']) & !empty($_POST['nonce'])) {
			if (wp_verify_nonce($_POST['nonce'], 'patient-page-nonce')) {
				if (
					isset($_POST['email']) && !empty($_POST['email']) &&
					isset($_POST['password']) && !empty($_POST['password'])
				) {
					$email = isset($_POST['key']) && $_POST['key'] == "reset_password_endpoint" ? base64_decode($_POST['email']) : $_POST['email'];
					$user = get_user_by('email', $email);
					$password = $_POST['password'];
					if (is_object($user)) {
						wp_set_password($password, $user->data->ID);
						update_user_meta($user->data->ID, "miamimed_telehealth_password_expiry", time());
						$response['status'] = "success";
						$response['message'] = 'Password Reset successfully.';
						$response['url'] = get_permalink(get_option('woocommerce_myaccount_page_id'));
					} else {
						$response['status'] = 'error';
						$response['message'] = "User doesn't exist.";
					}
				} else {
					$response['status'] = 'error';
					$response['message'] = "Invalid Data!";
				}
			} else {
				$response['status'] = 'error';
				$response['message'] = "Nonce verification failed!!";
			}
		} else {
			$response['status'] = 'error';
			$response['message'] = "Nonce not available!";
		}
		echo json_encode($response);
		wp_die();
	}

	public function patient_two_factor_verification()
	{
		if (isset($_POST['nonce']) & !empty($_POST['nonce'])) {
			if (wp_verify_nonce($_POST['nonce'], 'patient-page-nonce')) {
				if (
					isset($_POST['email']) && !empty($_POST['email']) &&
					isset($_POST['code']) && !empty($_POST['code'])
				) {
					$user = get_user_by('email', $_POST['email']);
					$secret = json_decode(get_user_meta($user->ID, "miamimed_telehealth_2fa", true), true)['secret'];
					if ($secret == $_POST['code']) {
						wp_set_current_user($user->ID);
						wp_set_auth_cookie($user->ID);
						do_action('wp_login', $user->user_login, $user);
						$response['status'] = "success";
						$response['message'] = get_permalink(get_option("woocommerce_myaccount_page_id"));
					}else{
						$response['status'] = false;
						$response['message'] = 'Invalid authentication code. Please try again.';
					}
					// if ($this->google_auth->verifyCode($secret, $_POST['code'])) {
					// 	wp_set_current_user($user->ID);
					// 	wp_set_auth_cookie($user->ID);
					// 	do_action('wp_login', $user->user_login, $user);
					// 	$response['status'] = "success";
					// 	$response['message'] = get_permalink(get_option("woocommerce_myaccount_page_id"));
					// } else {
					// 	$response['status'] = false;
					// 	$response['message'] = 'Invalid authentication code. Please try again.';
					// }
				} else {
					$response['status'] = 'error';
					$response['message'] = "Invalid Data!";
				}
			} else {
				$response['status'] = 'error';
				$response['message'] = "Nonce verification failed!!";
			}
		} else {
			$response['status'] = 'error';
			$response['message'] = "Nonce not available!";
		}
		echo json_encode($response);
		wp_die();
	}

	public function miamimed_telehealth_questionaries_shortcode(){	
		add_shortcode("miamimed_telehealth_questionaries_shortcode", [$this, "miamimed_telehealth_questionaries_shortcode_callback"]);
	}

	public function call_ays_shortcode_html(){
		return do_shortcode('[ays_survey id="'.$_GET['id'].'"]');
	}

	public function miamimed_telehealth_questionaries_shortcode_callback() {
		$states = json_decode(get_option("miamimed_questionary_states", []), true);
		ob_start();
		if (is_user_logged_in()) {
			global $wpdb;
			$table = $wpdb->prefix . 'ayssurvey_submissions';
			$sql = "SELECT id FROM `$table` WHERE user_id = " . get_current_user_id() . " and survey_id = ". get_option('medical_history');
			$result = $wpdb->get_row($sql, ARRAY_A);
			if (!empty($result['id'])) {
				$_SESSION['survey_completed'] = get_option('medical_history');
				$html1 = '<section class="reviewPage"><div class="container"><div class="row"><div class="col pageTitle"><h2>Review Your Questionnaire</h2><p>Make sure to review questions marked in red with a<span>?</span> icon before continuing.</p></div></div><div class="row">';
				$query = $wpdb->prepare("SELECT id FROM $table WHERE user_id = %d AND survey_id = %d ORDER BY end_date DESC", get_current_user_id(), get_option('medical_history'));
				$resultat = $wpdb->get_row($query, ARRAY_A);
				$table1 = $wpdb->prefix . 'ayssurvey_submissions_questions';
				$query1 = $wpdb->prepare("SELECT * FROM $table1 WHERE submission_id = %d", $resultat['id']);
				$resultat1 = $wpdb->get_results($query1, ARRAY_A);
				if (is_array($resultat1)) {
					$one_loop = '';
					$i = 0;
					foreach ($resultat1 as $key => $value) {
						$table2 = $wpdb->prefix . 'ayssurvey_surveys';
						$query2 = $wpdb->prepare("SELECT id, title FROM $table2 WHERE id = %d", get_option('medical_history'));
						$resultat2 = $wpdb->get_row($query2, ARRAY_A);

						$table3 = $wpdb->prefix . 'ayssurvey_questions';
						$query3 = $wpdb->prepare("SELECT id, section_id,question FROM $table3 WHERE id = %d", $value['question_id']);
						$resultat3 = $wpdb->get_row($query3, ARRAY_A);

						$table4 = $wpdb->prefix . 'ayssurvey_answers';
						$query4 = $wpdb->prepare("SELECT id, answer, ordering FROM $table4 WHERE id = %d", $value['answer_id']);
						$resultat4 = $wpdb->get_row($query4, ARRAY_A);

						$new_ids = [];
						if( 'checkbox' == $value['type'] ) {
							$answer_ids = $value['user_answer'];
							$answer_ids_a = explode(',', $answer_ids);
							foreach( $answer_ids_a as $key => $val ) {
								$table13 = $wpdb->prefix . 'ayssurvey_answers';
								$query13 = $wpdb->prepare("SELECT answer FROM $table13 WHERE id = %d", $val);
								$resultat13 = $wpdb->get_row($query13, ARRAY_A);
								$new_ids[] = $resultat13['answer'];
							}
						} else {
							if (empty($value['answer_id'])) {
								$answer1 = $value['user_answer'];
							} else {
								$answer1 = $resultat4['answer'];
							}
						}
						if( isset( $new_ids ) && !empty( $new_ids ) && is_array( $new_ids ) ) {
							$answer1 = implode(',',array_filter($new_ids));
						}
						$answer = '<div class="col-12 Question">';
						$question = '<h3 data-survey-id="' . $resultat2['id'] . '" >'.$resultat2['title'].'</h3>';
						$i == 0 ? $answer .= $question : '';
						$ordering = isset($resultat4['ordering']) ? $resultat4['ordering'] : '';
						$id = isset($resultat4['id']) ? $resultat4['id'] : '';
						$answer .= '
							<div class="EditBox">
								<h4 class="questionTitle" data-question-id="' . $resultat3['id'] . '" data-section-id="' . $resultat3['section_id'] . '">
									'.$resultat3['question'].'
								</h4>
								<a class="edit_current_question" href="#" data-survey-id="' . $resultat2['id'] . '">Edit</a>
							</div>
							<p class="answer" data-answer-id="' . $id . '" data-answer-ordering="' . $ordering . '">'.$answer1.'</p>
						';
						$answer .= '</div>';
						$one_loop .= $answer;
						$i++;
					}
				}
				$html1 .= $one_loop;
				$html1 .= '</div><div class="row"><div class="col-12"><h3 class="noteText">All information must be <span>accurate</span> and<span> correspond to the patient under treatment.</span>
				</h3><button class="btn btn-main w-100 mt-3 rounded miamimed_questionaries_continue_to_purchase">Confirm & Continue</button></div></div></div></section>';
				echo $html1;
			} else{
				$user_id = get_current_user_id();
				$user = get_user_by("id", $user_id);
				$meta = get_user_meta($user_id, "dermatologist", true);
				$dermatologist = get_user_by("login", $meta);
				$avatar_url = get_the_author_meta('avatar', $user_id);
				$first_name = get_user_meta($user_id, "first_name", true);
				$last_name = get_user_meta($user_id, "last_name", true);
				$dob = get_user_meta($user_id, "dob", true);
				$gender = get_user_meta($user_id, "gender", true);
				?>
				<div class="modal fade" id="miamimed-telehealth-state-panel-modal" tabindex="-1" aria-labelledby="miamimed-telehealth-state-panel-modal-title" aria-hidden="true">
					<div class="modal-dialog modal-dialog-centered">
						<div class="modal-content">
						<div class="modal-header">
							<h1 class="modal-title fs-5 fw-bold" id="miamimed-telehealth-state-panel-modal-title">Consent to Telehealth Services</h1>
							<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
						</div>
						<div class="modal-body fw-semibold">
							<p>
							Telemedicine involves the delivery of health care services using electronic communications, information technology, or other means between a health care provider and a patient who are not in the same physical location. Telemedicine may be used for diagnosis, treatment, follow-up and/or related patient education, and other necessary medical services.
							</p>
							<p>
							The electronic systems used in the Services will incorporate network and software security protocols to protect the privacy and security of health information and imaging data, and will include measures to safeguard the data to ensure its integrity against intentional or unintentional corruption.
							</p>
							<p>
							For Uklera's full Telehealth Terms and Conditions please visit the <u>terms page</u> or click on the link provided below. By consenting to Telehealth you are agreeing to our full terms and conditions.
							</p>
						</div>
						</div>
					</div>
				</div>

				<div id="miamimed-questionaries-state-panel" class="container card w-50">
					<section class="addtocart">
						<div class="container">
							<div class="row">
							<div class="col">
								<div class="content">
								<h4>STEP 1 OF 3</h4>
								<h1>Find My Doctor</h1>
								<p>
									Let's connect with our doctors in your home state.
									<span>Enter your home state:</span>
								</p>
								<form action="">
									<div class="stepField my-4">
									<input
										type="text"
										name="miamimed-questionaries-states"
										id="miamimed-questionaries-states"
										class="form-control"
										placeholder="Enter your home state"
									/>
									</div>
								</form>
								<div class="designatedDoctor">
									<h4>Meet your designated doctor!</h4>
									<div class="drProfile">
									<img src="<?= !empty($avatar_url) ? $avatar_url : "http://2.gravatar.com/avatar/8f3eb3412dbc4f98d13b89187ffb522e?s=96&d=mm&r=g" ?>" alt="profile" />
									</div>
	
									<h4><?= $dermatologist->data->display_name ?></h4>
									<p>Board Certified Dermatologist</p>
									<p>Doctor credentials: <?= $meta ?></p>
								</div>
								<div class="form-check my-5">
									<input
									class="form-check-input"
									type="checkbox"
									id="miamimed-questionaries-states-checkbox"
									/>
									<label class="form-check-label" for="miamimed-questionaries-states-checkbox">
									I agree to the terms of <a data-bs-toggle="modal" data-bs-target="#miamimed-telehealth-state-panel-modal" href="#">Telehealth Consent.</a>
									</label>
								</div>
								<button class="btn btn-main w-100 rounded" id="miamimed-questionaries-state-panel-btn">Next</button>
	
								<div class="sponcers">
									<img src="<?= get_template_directory_uri() . '/assets/img/logo.svg'?>" alt="" />
									<img src="<?= get_template_directory_uri() . '/assets/img/logo.svg'?>" alt="" />
								</div>
								<p>
									Protected by HIPAA-Compliant Medical <br />
									System Secured by LegitScript
								</p>
								</div>
							</div>
							</div>
						</div>
					</section>
					<!-- <div class="card-body">
						<div class="row heading m-3">
							<div class="col-md-12">
								<h3 class="text-center">Welcome to FaceRx!</h3>
							</div>
						</div>
						<div class="row question m-3">
							<div class="col-md-12">
								<p>Which state are you located in?</p>
							</div>
						</div>
						<div class="row options m-3">
							<div class="col-md-12">
								<select class="form-select miamimed-questionaries-states">
								<option value="select">Select</option>
								<?php
								if (is_array($states) && !empty($states)) {
									foreach ($states as $state) {
										?> <option value="<?= str_replace(" ", "_", strtolower($state));?>"><?= $state;?></option> <?php
									}
								}						
								?>
								</select>
							</div>
						</div>
						<div class="row checkbox m-3">
							<div class="col-md-12">
								<div class="form-check">
									<input class="form-check-input" type="checkbox" value="" id="flexCheckDefault">
									<label class="form-check-label" for="flexCheckDefault">
									I consent to <u id="miamimed-questionaries-states-consent-modal-open" data-bs-toggle="modal" data-bs-target="#miamimed-telehealth-state-panel-modal">Telehealth</u>
									</label>
								</div>
							</div>
						</div>
						<div class="row next m-3">
							<div class="col-md-12">
								<button type="button" id="miamimed-questionaries-state-panel-btn" class="btn btn-primary w-100 rounded">
								Next
								</button>
							</div>
						</div>
						<div class="row my-3">
							<div class="col-md-12 text-center">
								<img class="w-25 m-3" src="<?= get_template_directory_uri() . '/assets/img/logo.svg'?>" alt="Hippa Compliant">
							</div>
						</div>
						<div class="row m-3">
							<div class="col-md-12">
								<span>
									Your information is secure and will be reviewed by a board-certified dermatologist soon after the questionnaire is complete.
								</span>
							</div>
						</div>
					</div> -->
				</div>

				<div id="miamimed-questionaries-dermatologist-panel" class="container card d-none w-50">
					<section class="addtocart">
						<div class="container">
							<div class="row">
							<div class="col">
								<div class="content">
								<h4>STEP 2 OF 3</h4>
								<h1>Verify Your Email</h1>
								<h4><?= $user->data->user_email; ?></h4>
								<form id="verificaiton_mail_form" class="my-5">
									<div class="stepField mb-3">
									<div class="resend">
										<p class="mb-0">Verification code</p>
										<a class="miamimed-questionaries-login-resend-verification-code" href="">Resend code</a>
									</div>
									<input
										type="number"
										class="form-control"
										placeholder="Enter code"
										id="miamimed-questionaries-login-verification-code"
									/>
									</div>

									<button class="btn btn-main w-100 miamimed-questionaries-dermatologist-panel-btn">Verify My Email</button>
									<div class="instructions">
									<p class="mt-5">
										Not getting the code still? Try these two things:
									</p>
									<ol>
										<li>Check your spam folder.</li>
										<li>Double check the email you provided is correct.</li>
									</ol>
									</div>
								</form>
								</div>
							</div>
							</div>
						</div>
					</section>
				</div>

				<div id="miamimed-questionaries-doctor-visit-panel" class="container card d-none w-50">
					<section class="addtocart">
						<div class="container">
							<div class="row">
							<div class="col">
								<div class="content">
								<h4>STEP 2 OF 3</h4>
								<h1>Doctor Visit Questionnaire</h1>

								<div class="note">
									<p>Visit Date: <span>6/6/2023</span></p>
									<p>Physician: <span><?= $meta ?></span></p>

									<p class="notice">
									All information must be accurate and correspond to the patient
									under treatment.
									</p>
								</div>

								<form action="">
									<h4 class="text-start">Patient Profile:</h4>

									<div class="row">
									<div class="col-md-6">
										<div class="stepField">
										<label for="miamimed-questionaries-doctor-visit-form-first-name" class="mb-0">Legal First Name</label>
										<input
											type="text"
											class="form-control"
											placeholder="Legal First Name"
											id="miamimed-questionaries-doctor-visit-form-first-name"
											value="<?= $first_name ?>"
										/>
										</div>
									</div>
									<div class="col-md-6">
										<div class="stepField">
										<label for="miamimed-questionaries-doctor-visit-form-last-name" class="mb-0"> Legal Last Name</label>
										<input
											type="text"
											class="form-control"
											placeholder="Legal Last Name"
											id="miamimed-questionaries-doctor-visit-form-last-name"
											value="<?= $last_name ?>"
										/>
										</div>
									</div>
									</div>
									<div class="row">
									<div class="col">
										<div class="stepField">
										<label for="date" class="mb-0"
											>Date of Birth
											<i
											class="fa-solid fa-question"
											data-bs-toggle="tooltip"
											data-bs-placement="top"
											title="DOB provides additional verification and assures no duplicate accounts are created."
											></i
										></label>
										<input type="date" class="form-control" id="miamimed-questionaries-login-dob" value="<?= $dob ?>" />
										</div>
									</div>
									</div>
									<div class="row">
									<div class="col">
										<div class="stepField">
										<label for="date" class="mb-0"
											>Biological Sex
											<i
											class="fa-solid fa-question"
											data-bs-toggle="tooltip"
											data-bs-placement="top"
											title="This is important to provide the best customized care."
											></i
										></label>
										<div class="gender">
											<div class="form-check form-check-inline border-end">
											<input
												class="form-check-input"
												type="radio"
												name="miamimed-questionaries-login-form-gender"
												<?= "male" == $gender ? "checked='checked'" : "" ?>
												id="inlineRadio1"
												value="male"
											/>
											<label class="form-check-label" for="inlineRadio1"
												><i class="fa-solid fa-venus border-0"></i>
												Female</label
											>
											</div>
											<div class="form-check form-check-inline">
											<input
												class="form-check-input"
												type="radio"
												name="miamimed-questionaries-login-form-gender"
												<?= "female" == $gender ? "checked='checked'" : "" ?>
												id="inlineRadio2"
												value="female"
											/>
											<label class="form-check-label" for="inlineRadio2">
												<i class="fa-solid fa-mars border-0"></i>
												Male</label
											>
											</div>
										</div>
										</div>
									</div>
									</div>
									<div class="form-check mt-3 mb-5">
									<input
										class="form-check-input"
										type="checkbox"
										id="miamimed-questionaries-doctor-visit-form-confirm-checkbox"
									/>
									<label class="form-check-label" for="miamimed-questionaries-doctor-visit-form-confirm-checkbox">
										I understand incorrect personal information may lead to
										delays in prescription.
									</label>
									</div>

									<button class="btn btn-main w-100 rounded" id="miamimed-questionaries-doctor-visit-panel-btn">Next</button>
								</form>
								</div>
							</div>
							</div>
						</div>
					</section>
				</div>

				<div id="miamimed-questionaries-default-questions-panel" class="container d-none w-50">
					<div class="card-body">
						<div class="row">
							<div class="col-md-12">
								<div class="ays-survey-container">
									<?php
									$_SESSION['survey_remaning'] = [get_option('medical_history')];
									?><input type="hidden" id="redirect_product_questionary" value="<?= home_url("product-questionaries/");?>"><?php
									?>
									<div class="ays-survey-section"></div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<?php
			}
		}else{
			wp_redirect(home_url("my-account/?user_login"));
		}
		return ob_get_clean();
	}

	public function miamimed_questionaries()
	{
		if (!str_contains($_SERVER['HTTP_REFERER'], 'questionaries')) {
			if (is_user_logged_in()) {
				$total_survey = array();
				$total_survey[0] = get_option('medical_history');
				$cart = WC()->cart->get_cart();
				if (is_array($cart)) {
					foreach($cart  as $cart_item ){
						$total_survey[] = get_post_meta($cart_item['product_id'],'miamimed_questionary_questions',true);
					}					
					$_SESSION['total_survey'] = implode(",",$total_survey);
				}
				wp_redirect(get_permalink(get_option("miamimed_telehealth_questionaries_shortcode_page_id", true)));
			} else {
				wp_redirect(home_url("my-account/?user_login"));
			}
		}
	}

	public function Patient_render_reshortcode()
    {
        ob_start();
		global $wpdb;
		$table5 = $wpdb->prefix . 'ayssurvey_surveys';
		$query5 = $wpdb->prepare("SELECT title FROM $table5 WHERE id = %d", $_POST['currentSurveyId']);
		$result5 = $wpdb->get_row($query5, ARRAY_A);
		$table6 = $wpdb->prefix . 'ayssurvey_questions';
		$query6 = $wpdb->prepare("SELECT question, type FROM $table6 WHERE id = %d AND section_id = %d", $_POST['data_question_id'], $_POST['data_section_id']);
		$resultat6 = $wpdb->get_row($query6, ARRAY_A);
		$table7 = $wpdb->prefix . 'ayssurvey_answers';
		$query7 = $wpdb->prepare("SELECT id, ordering, answer FROM $table7 WHERE question_id = %d", $_POST['data_question_id']);
		$resultat7 = $wpdb->get_results($query7, ARRAY_A);
		$table8 = $wpdb->prefix . 'ayssurvey_submissions_questions';
		$query8 = $wpdb->prepare("SELECT id FROM $table8 WHERE question_id = %d and section_id = %d and user_id  = %d and answer_id  = %d ORDER BY `id` DESC", $_POST['data_question_id'], $_POST['data_section_id'], get_current_user_id(), $_POST['data_answer_id']);
		$resultat8 = $wpdb->get_row($query8, ARRAY_A);
		$query9 = $wpdb->prepare("SELECT id, user_answer FROM $table8 WHERE question_id = %d and section_id = %d and user_id = %d and survey_id = %d and type = 'text' ORDER BY `id` DESC", $_POST['data_question_id'], $_POST['data_section_id'], get_current_user_id(), $_POST['currentSurveyId']);
		$resultat9 = $wpdb->get_row($query9, ARRAY_A);
		$query10 = $wpdb->prepare("SELECT id, user_answer FROM $table8 WHERE question_id = %d and section_id = %d and user_id = %d and survey_id = %d and type = 'checkbox' ORDER BY `id` DESC", $_POST['data_question_id'], $_POST['data_section_id'], get_current_user_id(), $_POST['currentSurveyId']);
		$resultat10 = $wpdb->get_row($query10, ARRAY_A);
		$query11 = $wpdb->prepare("SELECT answer, id FROM $table7 WHERE `question_id` = %d", $_POST['data_question_id']);
		$resultat11 = $wpdb->get_results($query11, ARRAY_A);
		?><div class="w-50 mx-auto"><h3><?= $result5['title']; ?></h3><?php
		if ($resultat6['type'] == "yesorno" || $resultat6['type'] == "radio") {
			?>
			<div class="ays-survey-section-content">
				<div class="ays-survey-section-questions">
					<div class="ays-survey-question" data-required="false" data-type="radio" data-id="<?= $_POST['data_question_id'];?>" data-is-min="false">
						<div class="ays-survey-question-wrap-expanded-action">
							<div class="ays-survey-question-header">
								<div class="ays-survey-question-header-content">
									<div class="ays-survey-question-title">
										<p><?= $resultat6['question']; ?></p>
									</div>
									<div class="ays-survey-question-description"></div>
								</div>
							</div>
							<div class="ays-survey-question-content">
								<div class="ays-survey-question-answers d-flex">
									<?php
									if (is_array($resultat7)) {
										foreach ($resultat7 as $single) {
											?>
											<div class="ays-survey-answer m-3"><label class="ays-survey-answer-label form-check" tabindex="0"><input class="form-check-input" type="radio" <?= $_POST['data_answer_id'] == $single['id'] ? "checked='checked'" : ""; ?> name="ays-survey-answers-64a56f565bac4<?= $_POST['data_question_id'];?>][answer]" value="<?php echo $single['id'] ?>">
													<div class="ays-survey-answer-label-content">
														<div class="ays-survey-answer-icon-content">
															<div class="ays-survey-answer-icon-ink"></div>
															<div class="ays-survey-answer-icon-content-1">
																<div class="ays-survey-answer-icon-content-2">
																	<div class="ays-survey-answer-icon-content-3"></div>
																</div>
															</div>
														</div><span data-ordering="<?= $single['ordering']; ?>" class="fw-bold"><?= $single['answer']; ?></span>
													</div>
												</label>
											</div>
											<?php
										}
									}
									?>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php            
		} else if ($resultat6['type'] == "text") {
			?>
			<div class="ays-survey-section-content">
				<div class="ays-survey-section-questions">
					<div class="ays-survey-question" data-required="false" data-type="text" data-id="<?= $_POST['data_question_id'];?>" data-is-min="false">
						<div class="ays-survey-question-wrap-expanded-action">
							<div class="ays-survey-question-header">
								<div class="ays-survey-question-header-content">
									<div class="ays-survey-question-title">
										<p><?= $resultat6['question'];?></p>
									</div>
									<div class="ays-survey-question-description"></div>
								</div>
							</div>
							<div class="ays-survey-question-content">
								<div class="ays-survey-question-answers  ">
									<div class="ays-survey-answer">
										<div class="ays-survey-question-box ays-survey-question-type-text-box ">
											<div class="ays-survey-question-input-box"><textarea class="ays-survey-remove-default-border ays-survey-question-input-textarea ays-survey-question-input ays-survey-input  ays-survey-answer-text-inputs my-3 p-3 w-50 border-2" type="text" style="min-height: 24px; overflow: hidden; overflow-wrap: break-word; height: 100px;" placeholder="Your answer" name="ays-survey-answers-64a66594e4b38[<?= $_POST['data_question_id'];?>][answer]"><?= $resultat9['user_answer'];?></textarea>
												<div class="ays-survey-input-underline"></div>
												<div class="ays-survey-input-underline-animation"></div>
											</div>
										</div>
									</div><input type="hidden" name="ays-survey-questions-64a66594e4b38[<?= $_POST['data_question_id'];?>][section]" value="<?= $_POST['data_section_id'];?>"><input type="hidden" name="ays-survey-questions-64a66594e4b38[<?= $_POST['data_question_id'];?>][questionType]" value="<?= $resultat6['type'];?>"><input type="hidden" class="ays-survey-question-id" name="ays-survey-questions-64a66594e4b38[<?= $_POST['data_question_id'];?>][questionId]" value="<?= $_POST['data_question_id'];?>]">
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php
		} else if($resultat6['type'] == "checkbox") {
			?>
			<div class="ays-survey-section-content">
				<div class="ays-survey-section-questions">
					<div class="ays-survey-question" data-required="false" data-type="checkbox" data-id="<?= $_POST['data_question_id'];?>" data-is-min="false">
						<div class="ays-survey-question-wrap-expanded-action">
							<div class="ays-survey-question-header">
								<div class="ays-survey-question-header-content">
									<div class="ays-survey-question-title">
										<p><?= $resultat6['question'];?></p>
									</div>
									<div class="ays-survey-question-description"></div>
								</div>
							</div>
							<div class="ays-survey-question-content">
								<div class="ays-survey-question-answers row">
								<?php
								if (is_array($resultat11)) {
									foreach ($resultat11 as $key => $single) {
										?>
										<div class="ays-survey-answer m-3">
											<label class="ays-survey-answer-label form-check" tabindex="<?= $key;?>">
												<input type="checkbox" <?= in_array($single['id'], explode(",", $resultat10['user_answer'])) ? "checked" : '';?>
												name="ays-survey-answers-64a6b146611a7[<?= $_POST['data_question_id'];?>][answer][]" class="form-check-input" value="<?= $single['id']?>">
												<div class="ays-survey-answer-label-content">
												<div class="ays-survey-answer-icon-content">
													<div class="ays-survey-answer-icon-ink"></div>
													<div class="ays-survey-answer-icon-content-1">
														<div class="ays-survey-answer-icon-content-2">
															<div class="ays-survey-answer-icon-content-3"></div>
														</div>
													</div>
												</div>
												<span class="fw-bold"><?= $single['answer']?></span>
												</div>
											</label>
										</div>
										<?php
									}
								}
								?>
								<input type="hidden" name="ays-survey-questions-64a6b146611a7[<?= $_POST['data_question_id'];?>][section]" value="<?= $_POST['data_section_id'];?>"><input type="hidden" name="ays-survey-questions-64a6b146611a7[<?= $_POST['data_question_id'];?>][questionType]" value="<?= $resultat6['type'];?>"><input type="hidden" class="ays-survey-question-id" name="ays-survey-questions-64a6b146611a7[<?= $_POST['data_question_id'];?>][questionId]" value="<?= $_POST['data_question_id'];?>">
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php
		}
		?><button data-section-id="<?= $_POST['data_section_id'];?>" data-question-type="<?= $resultat6['type'];?>" data-question-id="<?= $_POST['data_question_id']; ?>" data-submission-id="<?= $resultat8['id']; ?>" data-survey-id="<?= $_POST['currentSurveyId'] ?>" data-answer-id="<?= $_POST['data_answer_id'];?>" class="btn btn-primary miamimed_update_questionaries_btn rounded">Update</button></div><?php
        ob_get_contents();
		wp_die();
    }

	public function Patient_miamimed_update_questionary_question()
    {
		global $wpdb;
		$table = $wpdb->prefix . 'ayssurvey_submissions_questions';
		$submission_id = intval($_POST['submission_id']); // Sanitize the input
		$user_answer = sanitize_text_field($_POST['answer']); // Sanitize the input
		if (!empty($user_answer)) {
			$wpdb->update(
				$table,
				array('user_answer' => $user_answer),
				array('id' => $submission_id),
				array('%s'),
				array('%d')
			);
		} else {
			if (is_array($_POST['answer_id'])) {
				$ans = implode(",", array_filter($_POST['answer_id']));
				$wpdb->update(
					$table,
					array('user_answer' => $ans),
					array('id' => $submission_id),
					array('%s'),
					array('%d')
				);
			} else {
				$answer_id = intval($_POST['answer_id']); // Sanitize the input
				$wpdb->update(
					$table,
					array('answer_id' => $answer_id),
					array('id' => $submission_id),
					array('%d'),
					array('%d')
				);
			}
		}
		$html1 = '<section class="reviewPage"><div class="container"><div class="row"><div class="col pageTitle"><h2>Review Your Questionnaire</h2><p>Make sure to review questions marked in red with a<span>?</span> icon before continuing.</p></div></div>';
		$survey_completed = explode(",", $_SESSION['total_survey']);
		$x = false;
		if(isset($_POST['key']) && $_POST['key'] == "all"){
			$x = true;
			if (is_array($survey_completed) && !empty($survey_completed)) {
				foreach ($survey_completed as $survey_id) {
					$table = $wpdb->prefix . 'ayssurvey_submissions';
					$html1 .= '<div class="row">';
					$query = $wpdb->prepare("SELECT id FROM $table WHERE user_id = %d AND survey_id = %d ORDER BY end_date DESC", get_current_user_id(), $survey_id);
					$resultat = $wpdb->get_row($query, ARRAY_A);
					$table1 = $wpdb->prefix . 'ayssurvey_submissions_questions';
					$query1 = $wpdb->prepare("SELECT * FROM $table1 WHERE submission_id = %d", $resultat['id']);
					$resultat1 = $wpdb->get_results($query1, ARRAY_A);
					if (is_array($resultat1)) {
						$one_loop = '';
						$i = 0;
						foreach ($resultat1 as $key => $value) {
							$table2 = $wpdb->prefix . 'ayssurvey_surveys';
							$query2 = $wpdb->prepare("SELECT id, title FROM $table2 WHERE id = %d", $survey_id);
							$resultat2 = $wpdb->get_row($query2, ARRAY_A);
			
							$table3 = $wpdb->prefix . 'ayssurvey_questions';
							$query3 = $wpdb->prepare("SELECT id, section_id,question FROM $table3 WHERE id = %d", $value['question_id']);
							$resultat3 = $wpdb->get_row($query3, ARRAY_A);
			
							$table4 = $wpdb->prefix . 'ayssurvey_answers';
							$query4 = $wpdb->prepare("SELECT id, answer, ordering FROM $table4 WHERE id = %d", $value['answer_id']);
							$resultat4 = $wpdb->get_row($query4, ARRAY_A);
			
							$new_ids = [];
							if( 'checkbox' == $value['type'] ) {
								$answer_ids = $value['user_answer'];
								$answer_ids_a = explode(',', $answer_ids);
								foreach( $answer_ids_a as $key => $val ) {
									$table13 = $wpdb->prefix . 'ayssurvey_answers';
									$query13 = $wpdb->prepare("SELECT answer FROM $table13 WHERE id = %d", $val);
									$resultat13 = $wpdb->get_row($query13, ARRAY_A);
									$new_ids[] = $resultat13['answer'];
								}
							} else {
								if (empty($value['answer_id'])) {
									$answer1 = $value['user_answer'];
								} else {
									$answer1 = $resultat4['answer'];
								}
							}
							if( isset( $new_ids ) && !empty( $new_ids ) && is_array( $new_ids ) ) {
								$answer1 = implode(',',array_filter($new_ids));
							}
							$answer = '<div class="col-12 Question">';
							$question = '<h3 data-survey-id="' . $resultat2['id'] . '" >'.$resultat2['title'].'</h3>';
							$i == 0 ? $answer .= $question : '';
							$ordering = isset($resultat4['ordering']) ? $resultat4['ordering'] : '';
							$id = isset($resultat4['id']) ? $resultat4['id'] : '';
							$answer .= '
								<div class="EditBox">
									<h4 class="questionTitle" data-question-id="' . $resultat3['id'] . '" data-section-id="' . $resultat3['section_id'] . '">
										'.$resultat3['question'].'
									</h4>
									<a class="edit_current_question" href="#" data-survey-id="' . $resultat2['id'] . '">Edit</a>
								</div>
								<p class="answer" data-answer-id="' . $id . '" data-answer-ordering="' . $ordering . '">'.$answer1.'</p>
							';
							$answer .= '</div>';
							$one_loop .= $answer;
							$i++;
						}
					}
					$html1 .= $one_loop;
					$html1 .= '</div>';
				}
			}
		} else{
			$table = $wpdb->prefix . 'ayssurvey_submissions';
			$html1 .= '<div class="row">';
			$query = $wpdb->prepare("SELECT id FROM $table WHERE user_id = %d AND survey_id = %d ORDER BY end_date DESC", get_current_user_id(), get_option('medical_history'));
			$resultat = $wpdb->get_row($query, ARRAY_A);
			$table1 = $wpdb->prefix . 'ayssurvey_submissions_questions';
			$query1 = $wpdb->prepare("SELECT * FROM $table1 WHERE submission_id = %d", $resultat['id']);
			$resultat1 = $wpdb->get_results($query1, ARRAY_A);
			if (is_array($resultat1)) {
				$one_loop = '';
				$i = 0;
				foreach ($resultat1 as $key => $value) {
					$table2 = $wpdb->prefix . 'ayssurvey_surveys';
					$query2 = $wpdb->prepare("SELECT id, title FROM $table2 WHERE id = %d", get_option('medical_history'));
					$resultat2 = $wpdb->get_row($query2, ARRAY_A);
	
					$table3 = $wpdb->prefix . 'ayssurvey_questions';
					$query3 = $wpdb->prepare("SELECT id, section_id,question FROM $table3 WHERE id = %d", $value['question_id']);
					$resultat3 = $wpdb->get_row($query3, ARRAY_A);
	
					$table4 = $wpdb->prefix . 'ayssurvey_answers';
					$query4 = $wpdb->prepare("SELECT id, answer, ordering FROM $table4 WHERE id = %d", $value['answer_id']);
					$resultat4 = $wpdb->get_row($query4, ARRAY_A);
	
					$new_ids = [];
					if( 'checkbox' == $value['type'] ) {
						$answer_ids = $value['user_answer'];
						$answer_ids_a = explode(',', $answer_ids);
						foreach( $answer_ids_a as $key => $val ) {
							$table13 = $wpdb->prefix . 'ayssurvey_answers';
							$query13 = $wpdb->prepare("SELECT answer FROM $table13 WHERE id = %d", $val);
							$resultat13 = $wpdb->get_row($query13, ARRAY_A);
							$new_ids[] = $resultat13['answer'];
						}
					} else {
						if (empty($value['answer_id'])) {
							$answer1 = $value['user_answer'];
						} else {
							$answer1 = $resultat4['answer'];
						}
					}
					if( isset( $new_ids ) && !empty( $new_ids ) && is_array( $new_ids ) ) {
						$answer1 = implode(',',array_filter($new_ids));
					}
					$answer = '<div class="col-12 Question">';
					$question = '<h3 data-survey-id="' . $resultat2['id'] . '" >'.$resultat2['title'].'</h3>';
					$i == 0 ? $answer .= $question : '';
					$ordering = isset($resultat4['ordering']) ? $resultat4['ordering'] : '';
					$id = isset($resultat4['id']) ? $resultat4['id'] : '';
					$answer .= '
						<div class="EditBox">
							<h4 class="questionTitle" data-question-id="' . $resultat3['id'] . '" data-section-id="' . $resultat3['section_id'] . '">
								'.$resultat3['question'].'
							</h4>
							<a class="edit_current_question" href="#" data-survey-id="' . $resultat2['id'] . '">Edit</a>
						</div>
						<p class="answer" data-answer-id="' . $id . '" data-answer-ordering="' . $ordering . '">'.$answer1.'</p>
					';
					$answer .= '</div>';
					$one_loop .= $answer;
					$i++;
				}
			}
			$html1 .= $one_loop;
			$html1 .= '</div>';
		}
		$btn = $x ? '<a class="btn btn-main w-100 mt-3" href="'.wc_get_checkout_url().'">Proceed to Checkout</a>' : '<button class="btn btn-main w-100 mt-3 rounded miamimed_questionaries_continue_to_purchase">Confirm & Continue</button>';
		$html1 .= '<div class="row"><div class="col-12"><h3 class="noteText">All information must be <span>accurate</span> and<span> correspond to the patient under treatment.</span>
		</h3>'.$btn.'</div></div></div></section>';
		exit($html1);
    }

	public function miamimed_continue_to_purchase(){
		if (isset($_SESSION['total_survey'])) {
            $total_survey = $_SESSION['total_survey'];
            $total_survey_array = explode(",", $total_survey);
            if (isset($_SESSION['survey_completed']) && !empty($_SESSION['survey_completed'])) {
                $survey_completed = $_SESSION['survey_completed'];
                $survey_completed_array = explode(",", $survey_completed);
            }
            $total_survey_array1 = array_unique($total_survey_array);
            $survey_completed_array1 = array_unique($survey_completed_array);
            $survey_remaning = array_diff($total_survey_array1, $survey_completed_array1);
            if (isset($survey_remaning) && !empty($survey_remaning) && is_array($survey_remaning)) {
                $_SESSION['survey_remaning'] = [];
                foreach ($survey_remaning as $value) {
                    $_SESSION['survey_remaning'][] = $value;
                }
                exit(home_url("product-questionaries/"));
            }
        }
	}

	public function miamimed_questionaries_state_panel() {
		if (isset($_POST['nonce']) & !empty($_POST['nonce'])) {
			if (wp_verify_nonce($_POST['nonce'], 'patient-page-nonce')) {
				if (isset($_POST['state']) && !empty($_POST['state'])) {

					update_user_meta(get_current_user_id(), 'billing_state', $_POST['state']);
					$response['status'] = true;

					// $user = get_user_by('ID', $user_id);
					// $email = $user->data->user_email;
					// $headers = array('Content-Type: text/html; charset=UTF-8', 'From: ' . get_bloginfo() . ' <noreply@' . $_SERVER['SERVER_NAME'] . '.com>' . "\r\n");
					// $otp = rand(100000, 999999);
					// $subject = 'Email OTP Verification';
					// $message = 'Your OTP is: ' . $otp;
					// update_user_meta($user_id, "miamimed_questionaries_email_verification", $otp);
					// $mail = wp_mail($email, $subject, $message, $headers);
					// if ($mail) {
					// 	$response['status'] = true;
					// 	$response['message'] = "State changed successfully!";
					// }else{
					// 	$response['status'] = false;
					// 	$response['message'] = "Invalid email!";
					// }
				} else {
					$response['status'] = false;
					$response['message'] = "Invalid Data!";
				}
			} else {
				$response['status'] = false;
				$response['message'] = "Nonce verification failed!";
			}
		} else {
			$response['status'] = false;
			$response['message'] = "Nonce not available!";
		}
		echo json_encode($response);
		wp_die();
	}

	public function miamimed_questionaries_dermatologist_panel() {
		if (isset($_POST['nonce']) & !empty($_POST['nonce'])) {
			if (wp_verify_nonce($_POST['nonce'], 'patient-page-nonce')) {
				if (
					isset($_POST['code']) && !empty($_POST['code']) 
					) {
					$user_id = get_current_user_id();
					$code = get_user_meta($user_id, "miamimed_questionaries_email_verification", true);
					if ($code == $_POST['code']) {
						$response['status'] = true;
						$response['message'] = "Verification Successfull!";
					}else{
						$response['status'] = false;
						$response['message'] = "Invalid Verification Code!";
					}
				} else {
					$response['status'] = false;
					$response['message'] = "Invalid Data!";
				}
			} else {
				$response['status'] = false;
				$response['message'] = "Nonce verification failed!";
			}
		} else {
			$response['status'] = false;
			$response['message'] = "Nonce not available!";
		}
		echo json_encode($response);
		wp_die();
	}

	public function miamimed_questionaries_login_resend() {
		if (isset($_POST['nonce']) & !empty($_POST['nonce'])) {
			if (wp_verify_nonce($_POST['nonce'], 'patient-page-nonce')) {
				$user_id = get_current_user_id();
				$user = get_user_by('ID', $user_id);
				$email = $user->data->user_email;
				$headers = array('Content-Type: text/html; charset=UTF-8', 'From: ' . get_bloginfo() . ' <noreply@' . $_SERVER['SERVER_NAME'] . '.com>' . "\r\n");
				$otp = rand(100000, 999999);
				$subject = 'Email OTP Verification';
				$message = 'Your OTP is: ' . $otp;
				update_user_meta($user_id, "miamimed_questionaries_email_verification", $otp);
				$mail = wp_mail($email, $subject, $message, $headers);
				if ($mail) {
					$response['status'] = true;
					$response['message'] = "Verification code sent successfully!";
				}else{
					$response['status'] = false;
					$response['message'] = "Invalid email!";
				}
			} else {
				$response['status'] = false;
				$response['message'] = "Nonce verification failed!";
			}
		} else {
			$response['status'] = false;
			$response['message'] = "Nonce not available!";
		}
		echo json_encode($response);
		wp_die();
	}

	public function miamimed_questionaries_doctor_visit_panel() {
		if (isset($_POST['nonce']) & !empty($_POST['nonce'])) {
			if (wp_verify_nonce($_POST['nonce'], 'patient-page-nonce')) {
				if (
					isset($_POST['first_name']) && !empty($_POST['first_name']) &&
					isset($_POST['last_name']) && !empty($_POST['last_name']) &&
					isset($_POST['gender']) && !empty($_POST['gender']) &&
					isset($_POST['dob']) && !empty($_POST['dob'])
					) {
					$updated = wp_update_user([
						"ID" => get_current_user_id(),
						"first_name" => $_POST['first_name'],
						"last_name" => $_POST['last_name'],
					]);
					update_user_meta(get_current_user_id(), "gender", $_POST['gender']);
					update_user_meta(get_current_user_id(), "dob", $_POST['dob']);
					if (is_wp_error($updated)) {
						$response['status'] = false;
						$response['message'] = $updated->get_error_message();
					}else{
						$response['status'] = true;
						$response['message'] = "User Details Updated!";
					}
				} else {
					$response['status'] = false;
					$response['message'] = "Invalid Data!";
				}
			} else {
				$response['status'] = false;
				$response['message'] = "Nonce verification failed!";
			}
		} else {
			$response['status'] = false;
			$response['message'] = "Nonce not available!";
		}
		echo json_encode($response);
		wp_die();
	}

	// public function miamimed_restrict_login_attempts() {
	// 	$max_attempts = 3;  // Set the maximum number of login attempts allowed
	// 	$lockout_duration = 5 * MINUTE_IN_SECONDS;  // Set the lockout duration in seconds
	// 	$user = wp_get_current_user();
	// 	$login = $_POST['log'];
	// 	if ($user && $user->ID == 0 && !empty($login)) {
	// 		$failed_attempts = get_transient('login_lock_' . $login) ?: 0;
	// 		if ($failed_attempts >= $max_attempts) {
	// 			wp_die('Too many login attempts. Please try again after ' . $lockout_duration / MINUTE_IN_SECONDS . ' minutes.');
	// 		}
	// 		set_transient('login_lock_' . $login, $failed_attempts + 1, $lockout_duration);
	// 	}
	// }

	public function miammimed_after_thankyou_woocommerce($order_id) {
		global $wpdb;
		$wpdb->update(
			$wpdb->posts,
			array('post_author' => get_current_user_id()),
			array('ID' => $order_id),
			array('%d'),
			array('%d')
		);
		update_post_meta( $order_id, 'total_completed_survery', $_SESSION['survey_completed'] );
		update_post_meta( $order_id, 'miamimed_notificaiton_status', false );
	}

	function remove_product_from_order($order_id, $product_id) {
		// Get the order object
		$order = wc_get_order($order_id);

		// Loop through the order items to find the product to remove
		foreach ($order->get_items() as $item_id => $item_data) {
			if ($item_data->get_product_id() == $product_id) {
				// Remove the item from the order
				$order->remove_item($item_id);
				$order->calculate_totals(); // Recalculate order totals
				$order->save(); // Save changes
				return true;
			}
		}
	
		// If the product is not found in the order, return false
		return false;
	}

	public function session_start() {
		session_start();
	}

	public function miamimed_hide_admin_bar_for_patient() {
		if (current_user_can('patient')) {
			show_admin_bar(false);
		}
	}

	public function miamimed_custom_logout_redirect($logout_redirect ) {
		$logout_redirect = home_url('my-account/?user_login'); // Change this to your desired redirect URL
		return $logout_redirect;
	}

	function debug($data)
	{
		echo "<pre>";
		print_r($data);
		die;
	}

}
