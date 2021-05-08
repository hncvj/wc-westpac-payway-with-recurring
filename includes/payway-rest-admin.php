<?php
		
		$this -> form_fields = array(
                'enabled' => array(
                    'title' => __('Enable/Disable', 'woo_payway_recurring_net'),
                    'type' => 'checkbox',
                    'label' => __('Enable Wetpac Payway NET Payment Gateway.', 'woo_payway_recurring_net'),
                    'default' => 'no'),
                'title' => array(
                    'title' => __('Title:', 'woo_payway_recurring_net'),
                    'type'=> 'text',
                    'description' => __('This controls the title which user sees during checkout.', 'woo_payway_recurring_net'),
                    'default' => __('PayWay', 'woo_payway_recurring_net')),
                
				'description' => array(
                    'title' => __('Description:', 'woo_payway_recurring_net'),
                    'type' => 'textarea',
                    'description' => __('This controls the description which user sees during checkout.', 'woo_payway_recurring_net'),
                    'default' => __('Pay securely by Credit or Debit card through PayWay Secure Servers.', 'woo_payway_recurring_net')),
                
				'publishable_key' => array(
                    'title' => __('PayWay Publishable Key', 'woo_payway_recurring_net'),
                    'type' => 'text',
                    'description' => __('This key is available at Setup NET -> REST API in the  PayWay account."')),
                
				'secret_key' => array(
                    'title' => __('PayWay Secret Key', 'woo_payway_recurring_net'),
                    'type' => 'text',
                    'description' => __('This key is available at Setup NET -> REST API in the PayWay account."')),
				
				'customer-merchant' => array(
					'title' => __( 'Merchant ID', 'woo_payway_recurring_net' ),
					'type' => 'text',
					'description' => __( 'Either use TEST (for test transactions) or 08-digit live Westpac merchant ID, provided by PayWay.', 'woo_payway_recurring_net' ),
					'default' => 'TEST'
				),
				
				'woocommerce_notice_for_payway_recurring' => array(
					'title' => __( 'Notice for Users', 'woo_payway_recurring_net' ),
					'type' => 'text',
					'description' => __( 'Notice to be shown when a Payway Subscription Product is added to cart and cart is emptied. - Note: If kept empty, no notice will be displayed to customer.' ),
					'default' => ''
				),
				'automatic_email_receipts' => array(
                    'title' => __('Enable/Disable Sending Receipt', 'woo_payway_recurring_net'),
                    'type' => 'checkbox',
                    'label' => __('Automatic Email Receipts.', 'woo_payway_recurring_net'),
                    'default' => 'no'),
			
			
            );