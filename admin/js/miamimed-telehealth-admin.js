(function( $ ) {
	
	'use strict';
	
	const get_cookies_array = () => {
		const cookies = { };
		if (document.cookie && document.cookie != '') {
			const split = document.cookie.split(';');
			for (let i = 0; i < split.length; i++) {
				const name_value = split[i].split("=");
				name_value[0] = name_value[0].replace(/^ /, '');
				cookies[decodeURIComponent(name_value[0])] = decodeURIComponent(name_value[1]);
			}
		}
		return cookies;
	}

	$(document).ready(function(){

		$(document).find("#miamimed_questionaries_products").on("change", function() {
			const element = $(this);
			if (element.val() != 'select') {
				$(document).find(".miamimed_single_product_question")
			}
		})

		$(document).find(".miamimed-questionaries-repeater").on("click", function(){
			const element = $(this)
			$(element.siblings(".miamimed-questionaries-product-question"))
			$(document).find(".miamimed-questionaries-product-question")
		})

		if (window.location.href.includes("post_type=product")) {
			$(document).find("#post-search-input").attr("placeholder", "Search by Name");
		}

		if (window.location.href.includes("&action=edit")) {
			const product_categories = $(document).find("#miamimed_selected_product_categories").val();
			if (product_categories != undefined || product_categories != null) {
				const obj = JSON.parse(product_categories);
				$(document).find("#product_catchecklist li").each(function(_index, element) {
					const value = $(element).children("label").children("input").val();
					if (!Object.values(obj).includes(value)) {
						$(element).remove();
					}
				})
			}
		}

		$(document).find("table.users tbody tr").each(function(_index, element){
			$(element).find('td.username img').attr('src',$(element).find('td.activate .miamimed_telehealth_user_profile_url').val());
		});

		const url = get_cookies_array()['miamimed_telehealth_user_profile_url'];
		if (url != "") {
			$(document).find("#wp-admin-bar-my-account a img").attr("src", url);
			$(document).find("#wp-admin-bar-user-info a img").attr("src", url);
			$(document).find(".user-profile-picture td img").attr("src", url);
		}

		if (window.location.href.includes("profile.php")) {
			$(document).find(".nsl-container").remove();
		}

		if (window.location.href.includes("post_type=shop_order")) {
			$(document).find("#toplevel_page_shop_order").addClass("current menu-top");
		}

		if (window.location.href.includes("page=wc-settings&tab=checkout")) {
			$(document).find("#toplevel_page_wc-settings-tab-checkout").addClass("current menu-top");
		}

		if (window.location.href.includes("page=wc-settings&tab=shipping")) {
			$(document).find("#toplevel_page_wc-settings-tab-shipping").addClass("current menu-top");
		}

		if (window.location.href.includes("page=pp-capabilities-roles")){
			document.getElementsByTagName("footer")[0].remove();
		}

		$(document).find(".miamimed_telehealth_activate_product_cat").each((_index, element) => {
			$(element).on("change", () => {
				const key = $(element).is(":checked") ? "activate" : "deactivate";
				$.post(ajax_object.ajax_url, {action: 'miamimed_telehealth_activate_product_cat', nonce: ajax_object.nonce, id: $(element).attr("data-id"), key: key}, (response) => {
					const data = JSON.parse(response);
					alert(data.message);
					location.reload();
				})
			})
		})

		$(document).find(".miamimed_telehealth_activate_user_button").each((_index, element) => {
			$(element).on("change", () => {
				let name = $(element).attr("data-name");
				console.log(name);
				const key = $(element).is(":checked") ? "activate" : "deactivate";
				const data = 
				{
					action: 'miamimed_telehealth_activate_user',
					nonce: ajax_object.nonce,
					id: $(element).attr("data-user-id"),
					key: key,
				};
				if (name != undefined) {
					data["name"] = name;
				}
				$.post(ajax_object.ajax_url, data, (response) => {
					const data = JSON.parse(response);
					alert(data.message);
				})
			})
		})

		$(document).find("#toplevel_page_logout a").attr("href", window.location.origin + "/wp-login.php?action=logout&_wpnonce=" + $(document).find("#_wpnonce").val());

		$(document).find("#toplevel_page_shop_order a").attr("href", "edit.php?post_type=shop_order");

		$(document).find(".miamimed_users_dropdown_activate").each((_index, element) => {
			if ($(element).hasClass("active")) {
				$(document).find("#miamimed_users_dropdown_head_activate").html($(element).children("span").text() + '&nbsp; <em class="icon ni ni-chevron-down"></em>');
			}
		})

		$(document).find(".miamimed_users_dropdown_deactivate").each((_index, element) => {
			if ($(element).hasClass("active")) {
				$(document).find("#miamimed_users_dropdown_head_deactivate").html($(element).children("span").text() + '&nbsp; <em class="icon ni ni-chevron-down"></em>');
			}
		})

		$(document).find(".miamimed_users_dropdown_activate").each((_index, element) => {
			$(element).on("click", () => {
				$(document).find(".miamimed_users_dropdown_activate").removeClass("active");
				$(element).addClass("active");
				$(document).find(".miamimed_login_qr_img").addClass("d-none");
				$("#"+$(element).attr("data-id")).removeClass("d-none");
				$(document).find("#miamimed_users_dropdown_head_activate").html($(element).children("span").text() + '&nbsp; <em class="icon ni ni-chevron-down"></em>');
			});
		})

		$(document).find(".miamimed_users_dropdown_deactivate").each((_index, element) => {
			$(element).on("click", () => {
				$(document).find(".miamimed_users_dropdown_deactivate").removeClass("active");
				$(element).addClass("active");
				$(document).find("#miamimed_users_dropdown_head_deactivate").html($(element).children("span").text() + '&nbsp; <em class="icon ni ni-chevron-down"></em>');
			});
		})

		$(document).find("#miamimed_login_security_submit").on("click", () => {
			const id = $("#miamimed_users_dropdown_head_activate").val();
			const ele = $(this);
			const code = $(document).find("#miamimed_telehealth_2fa_verification_code").val();
			if (code != "") {
				const data = {
					action : 'miamimed_telehealth_activate_2fa',
					nonce : ajax_object.nonce,
					secret : $("#"+id).find('input[name="miamimed_login_2fa_secret_key"]').val(),
					user_id : id,
					code : code,
				}
				$.post(ajax_object.ajax_url, data, (response) => {
					ele.attr("disabled", false);
					const data = JSON.parse(response);
					if (data.status) {
						$(document).find("#miamimed-telehealth-login-2fa-deactivate")[0].click();
					}
					alert(data.message);
				})
			}else{
				alert("Please enter 2FA verification code!");
			}
		})

		$(document).find("#miamimed_login_security_deactivate_submit").on("click", () => {
			const ele = $(this);
			ele.attr("disabled", true);
			const data = {
				action : 'miamimed_telehealth_deactivate_2fa',
				nonce : ajax_object.nonce,
				user_id : $(document).find("#miamimed_users_dropdown_head_deactivate").val(),
			}
			$.post(ajax_object.ajax_url, data, (response) => {
				ele.attr("disabled", false);
				const data = JSON.parse(response);
				if (data.status) {
					$(document).find("#"+id).find("img").attr("src", data.url);
					$(document).find("#"+id).find("input[name='miamimed_login_2fa_secret_key']").val(data.secret);
					$(document).find("#miamimed-telehealth-login-2fa-activate")[0].click();
					$(document).find("#miamimed_telehealth_2fa_verification_code").val('');
				}
				alert(data.message);
			})
		})

		if (window.location.href.includes("page=prqfw")) {
			$.post(ajax_object.ajax_url, {action : "miamimed_get_products"}, (response) => {
				const products = JSON.parse(response);
				let select = '<select name="miamimed_questionary_products" id="miamimed_questionary_products">';
				products.forEach(element => {
					select +=  '<option value="'+element.ID+'">'+element.post_title+'</option>';
				});
				select += '</select>';
				$('#wpbody-content div.wrap p').after(select);
				// setTimeout(() => {
				// 	const iframe = $(document).find('iframe.prq-iframe');
				// 	console.log(iframe.contentWindow);
				// 	console.log(iframe[0].contentWindow);
				// 	console.log($(iframe).contentWindow);
				// 	$(iframe).on("load", function(event) {
				// 		console.log(event);
				// 	});
				// 	// console.log(iframe.contentDocument);
				// 	// console.log(iframe.contentWindow.document);
				// 	// console.log(iframe.contentDocument.find("div.dashboard-main"));
				// 	// console.log(iframe.contentDocument.find("div.dashboard-main div.el-row"));
				// 	// iframe.contentDocument.find("div.dashboard-main div.el-row").append(select);
				// }, 5000);
			});
		}

		$(document).find(".miamimed-questionary-repeater").on("click", function() {
			const element = $(this);
			console.log($(element.parent('div.preview-block')));
			console.log($($(element.parent('div.preview-block')).children("div.question")));
			$($(element.parent('div.preview-block')).children("div.question")).after($(element.siblings("div.question")));
			console.log($(element.siblings("div.question")));
		})
		
	})

})( jQuery );



