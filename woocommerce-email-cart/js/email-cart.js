jQuery( function($){

	$( document ).ready( function() {
		// Toggle Action
		$(".postbox h3").on("click", function() {
			$(this).parent().toggleClass("closed");
		});
		
		// Validation
		$("#cart-sent-form").on("submit", function() {
			var error = false;
			if (!$('table.woocommerce_order_items tbody#order_items_list .item').length) {
				if (!$(".email-cart-validate-product").length) {
					$('<div class="inline error below-h2 email-cart-validate-product"><p><strong>Error</strong>: Please add at least one product.</p></div>').insertBefore($("#woocommerce-order-items"));
				}
				error = true;
			} else {
				$(".email-cart-validate-product").remove();
			}
			var email_address = $.trim($("#email_address").val());
			if (email_address == "") {
				if (!$(".email-cart-validate-email").length) {
					$('<div class="inline error below-h2 email-cart-validate-email"><p><strong>Error</strong>: Please enter at least one email address.</p></div>').insertBefore($("#woocommerce-order-items"));
				}
				error = true;
			} else {
				$(".email-cart-validate-email").remove();
			}
			if (!error) {
				return true;
			} else {
				window.scrollTo(0, 0);
				return false;
			}
		});
	
		// Add a line item
		$('.woocommerce-email-cart #woocommerce-order-items button.add_cart_item').click(function(){
	
			var add_item_ids = $('select#add_item_id').val();
			
			if ( add_item_ids ) {
				
				$('table.woocommerce_order_items').block({ message: null, overlayCSS: { background: '#fff url(' + woocommerce_email_cart_params.plugin_url + '/images/ajax-loader.gif) no-repeat center', opacity: 0.6 } });
	
				
				var data = {
					action: 		'woocommerce_add_email_cart_item',
					items_to_add: 	add_item_ids,
					security: 		woocommerce_email_cart_params.order_item_nonce
				};

				$.post( woocommerce_email_cart_params.ajax_url, data, function( response ) {

					$('table.woocommerce_order_items tbody#order_items_list').html( response );

					$('select#add_item_id, #add_item_id_chzn .chzn-choices').css('border-color', '').val('');
					jQuery(".tips").tipTip({
						'attribute' : 'data-tip',
						'fadeIn' : 50,
						'fadeOut' : 50,
						'delay' : 200
					});
					$('select#add_item_id').trigger("liszt:updated");
					$('select#add_item_id').trigger("chosen:updated");
					$('table.woocommerce_order_items').unblock();
				
					update_share_link();
					
				});
	
			} else {
				$('select#add_item_id, #add_item_id_chzn .chzn-choices').css('border-color', 'red');
			}
			
			return false;
		});
		
		$(".woocommerce-email-cart #woocommerce-order-items #landing_page").on("change", update_share_link);
		
		$(".woocommerce-email-cart #woocommerce-order-items button#update_cart").on("click", function() {
			update_cart();
		});

		$(".woocommerce-email-cart #woocommerce-order-items").on("click", ".item .remove", function() {
			$(this).parents(".item:eq(0)").remove();

			update_cart();
		});

	});

	function update_cart() {
			
		var products = new Array();

		if ($('table.woocommerce_order_items tbody#order_items_list .item').length) {
			
			$('table.woocommerce_order_items tbody#order_items_list .item').each(function() {
				var product_id = $(this).find(".order_item_id").val();
				var variation_id = $(this).find(".order_item_variation_id").val();

				var qty = parseFloat( $(this).find('.product-quantity input.qty').val() );
				if (isNaN(qty)) {
					qty = parseFloat( $(this).find('.mod-quantity-input').val() );
				}
			
				var product_qty = parseFloat(qty);
			
				if (product_qty > 0) {
					
					if (variation_id != "") {
						for ( var i = 0; i < product_qty; i++ ) {
							products[products.length] = variation_id;
						}
					} else {
						for ( var i = 0; i < product_qty; i++ ) {
							products[products.length] = product_id;
						}
					}

				}

			});
		}
		
		$('table.woocommerce_order_items').block({ message: null, overlayCSS: { background: '#fff url(' + woocommerce_email_cart_params.plugin_url + '/images/ajax-loader.gif) no-repeat center', opacity: 0.6 } });

		var data = {
			action: 		'woocommerce_add_email_cart_item',
			items_to_add: 	products,
			empty_cart: 	true,
			security: 		woocommerce_email_cart_params.order_item_nonce,
		};

		$.post( woocommerce_email_cart_params.ajax_url, data, function( response ) {

			$('table.woocommerce_order_items tbody#order_items_list').html( response );

			$('select#add_item_id, #add_item_id_chzn .chzn-choices').css('border-color', '').val('');
			jQuery(".tips").tipTip({
				'attribute' : 'data-tip',
				'fadeIn' : 50,
				'fadeOut' : 50,
				'delay' : 200
			});
			$('select#add_item_id').trigger("liszt:updated");
			$('select#add_item_id').trigger("chosen:updated");
			$('table.woocommerce_order_items').unblock();
		
			update_share_link();
			
		});
	}
	
	function update_share_link() {
		if ($('table.woocommerce_order_items tbody#order_items_list .item').length) {
			var link_products = "";
			$('table.woocommerce_order_items tbody#order_items_list .item').each(function() {
				var product_id = $(this).find(".order_item_id").val();
				var variation_id = $(this).find(".order_item_variation_id").val();
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