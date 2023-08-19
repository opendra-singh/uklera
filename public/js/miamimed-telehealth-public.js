(function ($) {

	'use strict';

	const EmailValidation = () => {
		if ($(document).find('#PatientSignUpEmail').val() == '') {
			$(document).find('#PatientSignUpEmailErr').addClass('signup_error_msg_display');
			return;
		} else {
			var testEmail = /^[A-Z0-9._%+-]+@([A-Z0-9-]+\.)+[A-Z]{2,4}$/i;
			if (testEmail.test($(document).find('#PatientSignUpEmail').val())) {
				$(document).find('#PatientSignUpEmailErr').removeClass('signup_error_msg_display');
			}
			else {
				$(document).find('#PatientSignUpEmailErr').addClass('signup_error_msg_display');
				return;
			}
		}
	}

	const UsernameValidation = () => {
		if ($(document).find('#PatientSignUpUsername').val() == '') {
			$(document).find('#PatientSignUpUsernameErr').addClass('signup_error_msg_display');
			return;
		} else {
			$(document).find('#PatientSignUpUsernameErr').removeClass('signup_error_msg_display');
		}
	}

	const PatientPasword = () => {
		if ($(document).find('#PatientSignUpPassword').val() == '') {
			$(document).find('#PatientSignUpPasswordErr').addClass('signup_error_msg_display');
			return;
		} else {
			$(document).find('#PatientSignUpPasswordErr').removeClass('signup_error_msg_display');
		}
	}

	const ConfirmPassword = () => {
		if ($(document).find('#PatientSignUpConfirmPassword').val() == '') {
			$(document).find('#PatientSignUpConfirmPasswordErr').addClass('signup_error_msg_display');
			return;
		} else if ($(document).find('#PatientSignUpConfirmPassword').val() === $(document).find('#PatientSignUpPassword').val()) {
			$(document).find('#PatientSignUpConfirmPasswordErr').removeClass('signup_error_msg_display');
		} else {
			$(document).find('#PatientSignUpConfirmPasswordErr').addClass('signup_error_msg_display');
			return;
		}
	}

	const LegalFirstName = () => {
		if ($(document).find('#PatientSignUpFName').val() == '') {
			$(document).find('#PatientSignUpFNameErr').addClass('signup_error_msg_display');
			return;
		} else {
			$(document).find('#PatientSignUpFNameErr').removeClass('signup_error_msg_display');
		}
	}

	const LegalLastName = () => {
		if ($(document).find('#PatientSignUpLName').val() == '') {
			$(document).find('#PatientSignUpLNameErr').addClass('signup_error_msg_display');
			return;
		} else {
			$(document).find('#PatientSignUpLNameErr').removeClass('signup_error_msg_display');
		}
	}

	$(document).ready(function ($) {

		$(document).on('click focus', '#PatientSignUpUsername', function () {
			EmailValidation();
		});

		$(document).on('click focus', '#PatientSignUpPassword', function () {
			EmailValidation();
			UsernameValidation();
		});

		$(document).on('click focus', '#PatientSignUpConfirmPassword', function () {
			EmailValidation();
			UsernameValidation();
			PatientPasword();
		});

		$(document).on('click focus', '#PatientSignUpFName', function () {
			EmailValidation();
			UsernameValidation();
			PatientPasword();
			ConfirmPassword();
		});

		$(document).on('click focus', '#PatientSignUpLName', function () {
			EmailValidation();
			UsernameValidation();
			PatientPasword();
			ConfirmPassword();
			LegalFirstName();
		})

		$(document).find("#PatientVerify").on("click", function (e) {
			e.preventDefault();
			const code = $(document).find("#PatientSignUpVerification").val();
			if (code == "") {
				alert("Please Enter Verification Code!");
			}else{
				$.ajax({
					url: PatienSignUpObject.ajax_url,
					type: 'POST',
					data: {
						action: 'verify_otp',
						nonce: PatienSignUpObject.PatientPageNonce,
						email: $(document).find('#PatientSignUpEmail').val(),
						otp: code,
					},
					success: function (data) {
						data = JSON.parse(data);
						$(document).find(".tacbox").removeClass("d-none");
						$(document).find(".tacbox input[type='checkbox']").addClass("d-none");
						$(document).find(".tacbox span").addClass("d-none");
						$(document).find('#tacbox_checkboxErr').removeClass('d-none');
						if (data.status == "success") {
							$(document).find("#PatientSignUpVerificationText").addClass("d-none");
							$(document).find("#PatientSignUpVerification").addClass("d-none");
							$(document).find("#PatientVerify").addClass("d-none");
							$(document).find('#tacbox_checkboxErr').removeClass('signup_error_msg_display');
							$(document).find('#tacbox_checkboxErr').addClass('signup_error_success_msg_display');
							$(document).find('#PatientSignUpVerificationResend').addClass("d-none");
							$(document).find('#tacbox_checkboxErr').css({"text-align":"left"});
							$(document).find('#tacbox_checkboxErr').text('We have send an verification link to your email open that link to verify your email.');
						} else {
							$(document).find('#tacbox_checkboxErr').removeClass('signup_error_success_msg_display');
							$(document).find('#tacbox_checkboxErr').addClass('signup_error_msg_display');
							$(document).find('#tacbox_checkboxErr').text("Invalid Verification Code!");
						}
					},
					error: function (data) {
						alert(data);
					}
				});
			}
		});

		$(document).find('#Patient-login-form').submit(function (e) {
			e.preventDefault();
			$(document).find("#patient_login_error_message").removeClass("d-none");
			$.ajax({
				url: PatienSignUpObject.ajax_url,
				type: 'POST',
				data: {
					action: 'login_user',
					nonce: PatienSignUpObject.PatientPageNonce,
					username: $(document).find('#patient_login_username').val(),
					password: $(document).find('#patient_login_password').val(),
				},
				success: function (response) {
					if (response.includes("login attempts")) {
						$(document).find("#patient_login_error_message").css({"color":"red"});
						$(document).find("#patient_login_error_message").text(response);
						$(document).find("#patient_login_error_message").removeClass("d-none");
					}
					const data = JSON.parse(response);
					if (data.status == "success") {
						if (data.message == "2FA Enabled!") {
							$(document).find("#two-fa-verification-container").removeClass("d-none");
							$(document).find("#patient_login_2fa_resend").removeClass("d-none");
							$(document).find("#Patient-login-form").addClass("d-none");
						}else{
							window.location.href = data.message;
						}
					}else{
						$(document).find("#patient_login_error_message").removeClass("d-none");
						const htmlRegex = new RegExp("<([A-Za-z][A-Za-z0-9]*)\\b[^>]*>(.*?)</\\1>");
						if (htmlRegex.test(data.message)) {
							$(document).find("#patient_login_error_message").html("Incorrect Username or Password.");
							$(document).find("#patient_login_error_message").css({"color":"red"});
						}else{
							$(document).find("#patient_login_resend").removeClass("d-none");
							$(document).find("#patient_login_error_message").text(data.message);
							if (data.status == "success") {
								$(document).find("#patient_login_error_message").css({"color":"green"});
							}else{
								$(document).find("#patient_login_error_message").css({"color":"red"});
							}
						}
					}
				},
				error: function (data) {
					alert(data);
				}
			});
		});

		$(document).find("#patient_login_resend_2fa_code").on("click", function(e) {
			e.preventDefault();
			const email = $(document).find("#patient_login_username").val();
			$.post(PatienSignUpObject.ajax_url, {action : "miamimed_login_resend_2fa_code", nonce: PatienSignUpObject.PatientPageNonce, email : email}, function(response) {
				const result = JSON.parse(response);
				alert(result.message);
			});
		})

		$(document).find("#patient_login_resend_2fa_verify").on("click", function(e) {
			e.preventDefault();
			const email = $(document).find("#patient_login_username").val();
			const code = $(document).find("#patient_login_2fa").val();
			if (code != "") {
				$.post(PatienSignUpObject.ajax_url, {action : "miamimed_login_resend_2fa_code_verify", nonce: PatienSignUpObject.PatientPageNonce, email : email, code : code}, function(response) {
					const result = JSON.parse(response);
					alert(result.message);
					if (result.status) {
						window.location.href = result.url;
					}
				});				
			}else{
				alert("Please Enter Verification code!");
			}
		})

		$(document).find('#PatientSignUp').on('click', function (e) {
			e.preventDefault();
			EmailValidation();
			PatientPasword();
			ConfirmPassword();
			LegalFirstName();
			LegalLastName();

			if ($(document).find('#tacbox_checkbox').is(':checked')) {
				$(document).find('#tacbox_checkboxErr').removeClass('signup_error_msg_display');
				if (!$('.signup_error_msg').hasClass("signup_error_msg_display")) {
					$.ajax({
						url: PatienSignUpObject.ajax_url,
						type: 'post',
						data: {
							action: 'signup_user',
							nonce: PatienSignUpObject.PatientPageNonce,
							PatientSignUpEmail: $(document).find('#PatientSignUpEmail').val(),
							PatientSignUpUsername: $(document).find('#PatientSignUpUsername').val(),
							PatientSignUpPassword: $(document).find('#PatientSignUpPassword').val(),
							PatientSignUpConfirmPassword: $(document).find('#PatientSignUpConfirmPassword').val(),
							PatientSignUpFName: $(document).find('#PatientSignUpFName').val(),
							PatientSignUpLName: $(document).find('#PatientSignUpLName').val()
						},
						success(data) {
							data = JSON.parse(data);
							if (data.status == "success") {
								$(document).find('#tacbox_checkboxErr').removeClass('signup_error_msg_display');
								$(document).find(".tacbox").addClass("d-none");
								$(document).find("#PatientSignUp").addClass("d-none");
								$(document).find("#PatientSignUpVerificationText").removeClass("d-none");
								$(document).find("#PatientSignUpVerification").removeClass("d-none");
								$(document).find("#PatientSignUpVerificationResend").removeClass("d-none");
								$(document).find(".agreement").addClass("d-none");
								$(document).find(".verification").removeClass("d-none");
								$(document).find("#PatientVerify").removeClass("d-none");
								$(document).find("#Patient-signup-form").children(".inputField").addClass("d-none");
								$(document).find("#Patient-signup-form").children(".row").addClass("d-none");
							} else {
								$(document).find('#tacbox_checkboxErr').removeClass('signup_error_success_msg_display');
								$(document).find('#tacbox_checkboxErr').addClass('signup_error_msg_display');
								$(document).find('#tacbox_checkboxErr').text(data.message);
							}
						},
						error: function (data) {
							alert(data);
						}
					});
				}
			} else {
				$(document).find('#tacbox_checkboxErr').removeClass('signup_error_success_msg_display');
				$(document).find('#tacbox_checkboxErr').addClass('signup_error_msg_display');
				$(document).find('#tacbox_checkboxErr').text('Please agree terms of use and privacy policy');
				return;
			}
		});

		$(document).find("#PatientSignUpVerificationResend").on("click", function(e) {
			e.preventDefault();
			const error_msg = $(document).find("#patient_login_error_message").val();
			let key = "";
			switch (error_msg) {
				case "Your Password has expired. We have sent a reset password link on your registered email.":
					key = "password_expired";
					break;
				case "Please Verify your email first!":
					key = "email_verify";
					break;
			}
			$.ajax({
				url: PatienSignUpObject.ajax_url,
				type: 'POST',
				data: {
					action: 'send_otp',
					nonce: PatienSignUpObject.PatientPageNonce,
					key: 'email_verify',
					email: $(document).find('#PatientSignUpEmail').val(),
				},
				success: function (data) {
					data = JSON.parse(data);
					alert(data.message);
				},
				error: function (data) {
					alert(data);
				}
			});
		})

		$(document).find("#patient_login_forgot_password").on("click", function(e){
			e.preventDefault();
			$(document).find(".loginData").addClass("d-none")
			$(document).find(".forgotPasswordData").removeClass("d-none")
			$(document).find(".authPage .forgotPasswordData h1").text("Forgot Password");
		})

		$(document).find('#patient_forgot_pasword_submit').on('click', function (e) {
			e.preventDefault();
			const element = $(this);
			$.ajax({
				url: PatienSignUpObject.ajax_url,
				type: 'POST',
				data: {
					action: 'send_otp',
					nonce: PatienSignUpObject.PatientPageNonce,
					key: 'reset_pass',
					email: $(document).find('#patient_forgot_pasword_email').val(),
				},
				success: function (data) {
					data = JSON.parse(data);
					if (data.status == 'success') {
						$(document).find("#patient_forgot_pasword_email").addClass("d-none");
						$(document).find("#patient_forgot_pasword_otp").removeClass("d-none");
						$(document).find("#patient_forgot_pasword_otp_text").removeClass("d-none");
						$(document).find("#patient_forgot_pasword_verify").removeClass("d-none");
						element.addClass("d-none");
					}
					alert(data.message);
				},
				error: function (data) {
					alert(data);
				}
			});

		});

		$(document).find("#patient_forgot_pasword_verify").on("click", function(e){
			e.preventDefault();
			$.ajax({
				url: PatienSignUpObject.ajax_url,
				type: 'POST',
				data: {
					action: 'verify_otp',
					key: 'forgot_password_verify',
					nonce: PatienSignUpObject.PatientPageNonce,
					email: $(document).find('#patient_forgot_pasword_email').val(),
					otp: $(document).find("#patient_forgot_pasword_otp").val(),
				},
				success: function (data) {
					data = JSON.parse(data);
					if (data.status == "success") {
						$(document).find("#patient_forgot_pasword_otp").addClass("d-none");
						$(document).find("#patient_forgot_pasword_otp_text").addClass("d-none");
						$(document).find("#patient_forgot_pasword_verify").addClass("d-none");
						$(document).find("#patient_forgot_pasword_reset").removeClass("d-none");
						$(document).find("#patient_forgot_pasword_password").removeClass("d-none");
						$(document).find("#patient_forgot_pasword_confirm_password").removeClass("d-none");
					}
					alert(data.message);
				},
				error: function (data) {
					alert(data);
				}
			});
		})

		$(document).find("#patient_forgot_pasword_reset").on("click", function(e){
			e.preventDefault();
			const password = $(document).find("#patient_forgot_pasword_password").val();
			const confirmPassword = $(document).find("#patient_forgot_pasword_confirm_password").val();
			if (password !== confirmPassword) {
				alert("Your Passwords doesn't match!");
			}else{
				$.ajax({
					url: PatienSignUpObject.ajax_url,
					type: 'POST',
					data: {
						action: 'reset_password',
						nonce: PatienSignUpObject.PatientPageNonce,
						email: $(document).find('#patient_forgot_pasword_email').val(),
						password: $(document).find("#patient_forgot_pasword_password").val(),
					},
					success: function (data) {
						data = JSON.parse(data);
						alert(data.message);
						if (data.status == "success") {
							setTimeout(() => {
								window.location.href = data.url;
							}, 1000);
						}
					},
					error: function (data) {
						alert(data);
					}
				});
			}
		})

		$(document).find("#patient_login_resend").on("click", function(e){
			e.preventDefault();
			const message = $(document).find("#patient_login_error_message").text();
			let key = '';
			switch (message) {
				case 'Your Password has expired. We have sent a reset password link on your registered email.':
					key = 'reset_password';
					break;
				case "Please Verify your email first!":
					key = 'send_verification_mail';
					break;
			}

			$.ajax({
				url: PatienSignUpObject.ajax_url,
				type: 'POST',
				data: {
					action: 'send_verification_mail',
					key: key,
					nonce: PatienSignUpObject.PatientPageNonce,
					email: $(document).find("#patient_login_username").val(),
				},
				success: function (response) {
					const data = JSON.parse(response);
					$(document).find("#patient_login_error_message").text(data.message);
					if (data.status == "success") {
						$(document).find("#patient_login_error_message").css({"color":"green"});
					}else{
						$(document).find("#patient_login_error_message").css({"color":"red"});
					}
				},
				error: function (data) {
					alert(data);
				}
			});
		})

		$(document).find("#Patient-forgot-password-back-btn").on("click", function(e){
			e.preventDefault();
			$(document).find(".forgot-password-container").addClass("d-none");
			$(document).find(".sign-in-container").removeClass("d-none");	
		})

		$(document).find("#Patient-2fa-verification-back-btn").on("click", function(e) {
			e.preventDefault();
			$(document).find(".two-fa-verification-container").addClass("d-none");
			$(document).find(".sign-in-container").removeClass("d-none");
		})

		$(document).find("#patient-2fa-verification-submit").on("click", function(e) {
			e.preventDefault();
			const code = $(document).find("#paitient_verification_code").val();
			if (code == "") {
				alert("Please Enter Verification Code first!");
			}else{
				$.ajax({
					url: PatienSignUpObject.ajax_url,
					type: 'POST',
					data: {
						action: 'patient_two_factor_verification',
						nonce: PatienSignUpObject.PatientPageNonce,
						code: code,
						email: $(document).find("#patient_login_username").val(),
					},
					success: function (response) {
						const data = JSON.parse(response);
						if (data.status == "success") {
							window.location.href = data.message;
						}else{
							$(document).find("#patient_login_2fa_error_message").removeClass("d-none");
							$(document).find("#patient_login_2fa_error_message").css({"color":"red"});
							$(document).find("#patient_login_2fa_error_message").text(data.message);
						}
					},
					error: function (data) {
						alert(data);
					}
				});
			}
		})

		$(document).find("#miamimed-questionaries-state-panel-btn").on("click", function(e){
			e.preventDefault();
			const element = $(this);
			const checkbox = $(document).find("#miamimed-questionaries-states-checkbox").is(":checked");
			const state = $(document).find("#miamimed-questionaries-states").val();
			if (state == "") {
				alert("Please Enter your State first!");
			}else if (!checkbox) {
				alert("Please consent to our Telehealth!");
			}else{
				element.attr("disabled", true);
				$.ajax({
					url: PatienSignUpObject.ajax_url,
					type: 'POST',
					data: {
						action: 'miamimed_questionaries_state_panel',
						nonce: PatienSignUpObject.PatientPageNonce,
						state : state,
					},
					success: function (response) {
						element.attr("disabled", true);
						const data = JSON.parse(response);
						if (data.status) {
							$(document).find("#miamimed-questionaries-state-panel").addClass("d-none");
							$(document).find("#miamimed-questionaries-doctor-visit-panel").removeClass("d-none");
						}else{
							alert(data.message);
						}
					},
					error: function (data) {
						alert(data);
					}
				});
			}
		})

		$(document).find(".miamimed-questionaries-dermatologist-panel-btn").on("click", function(e){
			e.preventDefault();
			const element = $(this);
			const code = $("#miamimed-questionaries-login-verification-code").val();
			if (
				code == "" 
			) {
				alert("Please provide Verification code!");
			}else {
				element.attr("disabled", true);
				$.ajax({
					url: PatienSignUpObject.ajax_url,
					type: 'POST',
					data: {
						action: 'miamimed_questionaries_dermatologist_panel',
						nonce: PatienSignUpObject.PatientPageNonce,
						code : code,
					},
					success: function (response) {
						element.attr("disabled", false);
						const data = JSON.parse(response);
						if (data.status) {
							$(document).find("#miamimed-questionaries-dermatologist-panel").addClass("d-none");
							$(document).find("#miamimed-questionaries-doctor-visit-panel").removeClass("d-none");
						}
						alert(data.message);
					},
					error: function (data) {
						alert(data);
					}
				});
			}
		})

		$(document).find(".miamimed-questionaries-login-resend-verification-code").on("click", function(e){
			e.preventDefault();
			const element = $(this);
			element.attr("disabled", true);
			$.ajax({
				url: PatienSignUpObject.ajax_url,
				type: 'POST',
				data: {
					action: 'miamimed_questionaries_login_resend',
					nonce: PatienSignUpObject.PatientPageNonce,
				},
				success: function (response) {
					element.attr("disabled", false);
					const data = JSON.parse(response);
					alert(data.message);
				},
				error: function (data) {
					alert(data);
				}
			});
		})

		$(document).find("#miamimed-questionaries-states-consent-modal-open").on("click", function(){
			$(document).find("#miamimed-telehealth-state-panel-modal").css({"z-index":"999999"});
		})

		$(document).find("#miamimed-questionaries-doctor-visit-panel-btn").on("click", function(e){
			e.preventDefault();
			const element = $(this);
			const first_name = $("#miamimed-questionaries-doctor-visit-form-first-name").val();
			const last_name = $("#miamimed-questionaries-doctor-visit-form-last-name").val();
			const dob = $("#miamimed-questionaries-login-dob").val();
			const gender = $("input[name='miamimed-questionaries-login-form-gender']:checked").val()
			const checkbox = $("#miamimed-questionaries-doctor-visit-form-confirm-checkbox").is(":checked");

			if (first_name == "" || last_name == ""  || dob == "" || gender == undefined) {
				alert("All details are mandatory!");
			}else if (!checkbox) {
				alert("Please tick the checkbox!");
			}else{
				element.attr("disabled", true);
				$.ajax({
					url: PatienSignUpObject.ajax_url,
					type: 'POST',
					data: {
						action: 'miamimed_questionaries_doctor_visit_panel',
						nonce: PatienSignUpObject.PatientPageNonce,
						first_name : first_name,
						last_name : last_name,
						dob : dob,
						gender : gender,
					},
					success: function (response) {
						element.attr("disabled", false);
						const data = JSON.parse(response);
						if (data.status) {
							$(document).find("#miamimed-questionaries-doctor-visit-panel").addClass("d-none");
							$(document).find("#miamimed-questionaries-default-questions-panel").removeClass("d-none");
							window.location.href = $(document).find("#redirect_product_questionary").val();
						}
						alert(data.message);
					},
					error: function (data) {
						alert(data);
					}
				});
			}
		})

		$(document).on('click', '.edit_current_question',function(){
            const element = $(this);
            const currentSurveyId = element.attr('data-survey-id');
            const data_answer_id = element.parent("div.EditBox").siblings("p.answer").attr("data-answer-id");
            const data_answer_ordering = element.parent("div.EditBox").siblings("p.answer").attr("data-answer-ordering");
            const data_question_id = element.siblings('h4').attr('data-question-id');
            const data_section_id = element.siblings('h4').attr('data-section-id');
			const data = {};
			data.action = 'render_reshortcode';
			data.currentSurveyId = currentSurveyId;
			data.data_answer_id = data_answer_id;
			data.data_answer_ordering = data_answer_ordering;
			data.data_question_id = data_question_id;
			data.data_section_id = data_section_id;
            $.ajax({
                url: PatienSignUpObject.ajax_url,
                type: 'POST',
				async: false,
                data: data,
                success: function(response){
                    $(document).find('.miamimed-theme-container').html(response);
                }
            })
        })

        $(document).on('click', '.miamimed_update_questionaries_btn',function(){
            const element = $(this);
            const submission_id = element.attr('data-submission-id');
            const question_type = element.attr("data-question-type");
            let answer_id = "";
            let answer =  "";
            switch (question_type) {
                case "text":
                    answer = $(document).find('.ays-survey-question').find('.ays-survey-answer textarea.ays-survey-question-input-textarea').val();
                    break;
                case "yesorno":
                    answer_id = $($('.ays-survey-question-content').find('input[type="radio"]:checked')[0]).val();
                    break;
                case "radio":
                    const radio = $('.ays-survey-question-content').find('input[type="radio"]')
                    if (radio.length) {
                        radio.each(function(_index, element) {
                            if ($(element).is(":checked")) {
                                answer_id = $(element).val()
                            }
                        });                        
                    }
                    break;
                case "checkbox":
                    answer_id = [];
                    const checkbox = $('.ays-survey-question-content input[type="checkbox"]')
                    if (checkbox.length) {
                        checkbox.each(function(_index, element) {
                            if ($(element).is(":checked")) {
                                answer_id[_index] = $(element).val()
                            }
                        });
                    }
                    break;
            }
            const data = {};
            data.action = 'miamimed_update_questionary_question';
            data.submission_id = submission_id;
            data.answer_id = answer_id;
            data.answer = answer;
			if (window.location.href.includes("product-questionaries")) {
				data.key = "all";
			}
            $.ajax({
                url: PatienSignUpObject.ajax_url,
                type: 'post',
				async: false,
                data: data,
                success: function(response){
                    $(document).find('.miamimed-theme-container').html(response);
                }
            })
        })

		$(document).on("click", ".miamimed_questionaries_continue_to_purchase", function(e) {
			e.preventDefault();
            $.ajax({
                url: PatienSignUpObject.ajax_url,
                type: 'post',
                data: {action : "miamimed_continue_to_purchase"},
                success: function(response){
					if (response != "") {
						window.location.href = response;
					}
                }
            })
		})

	});

})(jQuery);



