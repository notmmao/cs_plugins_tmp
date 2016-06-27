jQuery( function($){

	$( document ).ready( function() {
		
		update_share_link();
		
		// Validation
		$(".email-cart-frontend-form").on("submit", function() {
			var error = false;
			
			var email_address = $.trim($("#email_address").val());
			if (email_address == "") {
				if (!$(".email-cart-validate-email").length) {
					$('<div class="inline error below-h2 email-cart-validate-email email-cart-front-end-error woocommerce-error"><strong>Oops</strong>: Please enter at least one email address.</div>').insertBefore($(".email-cart-row-email_address"));
				}
				error = true;
			} else {
				$(".email-cart-validate-email").remove();
			}
			if (!error) {
				return true;
			} else {
				//var position = $(".email-cart-validate-email").position();
				//$("html, body").animate({ scrollTop: position.top - 49 }, 1000);
				return false;
			}
			return false;			
		});
		
		$(".email-cart-frontend-form #landing_page").on("change", update_share_link);
		
		
		//Attach Open event to our link, and any user created links
		$('a[href="'+woocommerce_email_cart_params.cart_url+'#email-cart"], #email_cart_dropdown_btn, .email-cart-open').on("click", function(){
			open_cart();
			return false;
		});
		
		//Attach Close event for the close x button
		$('.email-cart-close').click(function(){
			close_cart();
			return false;
		});
		
		//Check if user has initiaed a deep link to our Email Cart
		if(window.location.hash == "#email-cart") open_cart() ;
		
	});
	
	//Deprecated: Toggle Email Cart block
	function toggle_cart() {
		if ( !$(".email-cart").hasClass('email-cart-state-open') ) {
			open_cart();
		}
		else {
			close_cart();
		}
	}
	
	//Open Email Cart block
	function open_cart() {
		$(".email-cart").slideDown(400, function() {
			$(".email-cart").addClass("email-cart-state-open");
			var position = $(".email-cart").position();
			$("html, body").animate({ scrollTop: position.top-30 }, 400);
			if(window.location.hash!="#emailcart") window.location.hash="#email-cart";
		});
	}
	
	//Close Email Cart block
	function close_cart() {
		$(".email-cart").slideUp(400, function() {
			$(".email-cart").removeClass("email-cart-state-open");
			if(window.location.hash=="#email-cart") window.location.hash="";
		});
	}
	
	
	function update_share_link() {
		
		if ($('.email-cart-frontend .item').length) {
			var link_products = "";
			$('.email-cart-frontend .item').each(function() {
				var product_id = $(this).find(".order_item_id").val();
				var variation_id = $(this).find(".order_item_variation_id").val();

				var qty = parseFloat( $(this).find('.product-quantity input.qty').val() );
				if (isNaN(qty)) {
					qty = parseFloat( $(this).find('.mod-quantity-input').val() );
				}
			
				var product_qty = parseFloat($(this).find(".item_quantity").val());
				
				if (product_qty > 0) {
					if (product_qty > 1) {
						link_products += product_qty + ".";
					}
					if (variation_id != "") {
						var attribute_length = $(this).find(".order_item_variation_attribute_name").length;
						
						if (attribute_length) {
							var attributes = "(";
							for (var j = 0; j < attribute_length; j++) {
								if (j == 0) {
									attributes += $(this).find(".order_item_variation_attribute_name:eq(" + j + ")").val() + "=" + $(this).find(".order_item_variation_attribute_value:eq(" + j + ")").val();
								} else {
									attributes += "+" + $(this).find(".order_item_variation_attribute_name:eq(" + j + ")").val() + "=" + $(this).find(".order_item_variation_attribute_value:eq(" + j + ")").val();
								}
							}
							attributes += ")";
							link_products += product_id + "_" + variation_id + attributes + ",";
						} else {
							link_products += product_id + "_" + variation_id + ",";
						}
					} else {
						link_products += product_id + ",";
					}
				}
			});
			if (link_products != "") {
				link_products = link_products.substring(0, link_products.length - 1);
				var landing_page = $("#landing_page").val();
				$("#share_link").val(woocommerce_email_cart_params.home_url + "?email_cart_products=" + link_products + "&landing_page=" + landing_page);
			} else {
				$("#share_link").val("");
			}
		} else {
			$("#share_link").val("");
		}
		
	}
	
});