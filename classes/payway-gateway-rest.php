<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
	/**
     * WC_Payway_Gateway_Rest_Recurring class.
     *
     * @extends WC_Payment_Gateway
     */
	
	class WC_Payway_Gateway_Rest_Recurring extends WC_Payment_Gateway
	{
		private static $log;
		private $redirect_page_id;
		private $debug;
		
	
		/**
         * __construct function.
         *
         * @access public
         * @return void
         */
		public function __construct()
		{
			$this -> id = 'paywaynetrecurring';
			$this -> method_title = __( 'PayWay NET with Recurring', 'woo_payway_recurring_net' );
			$this -> method_description   = __( 'PayWay NET with Recurring', 'woo_payway_recurring_net' );
			
			$this -> has_fields = false;
 
			$this -> init_form_fields();
			$this -> init_settings();
			
		
			// Default values
			$this->enabled				= isset( $this->settings['enabled'] ) && $this->settings['enabled'] == 'yes' ? 'yes' : $this->default_enabled;
			$this->title 				= sanitize_text_field($this->settings['title'], 'woo_payway_recurring_net' );
			$this->description  		= sanitize_text_field($this->settings['description'], 'woo_payway_recurring_net' );
			$this->order_button_text  	= __( 'Pay securely with PayWay', 'woo_payway_recurring_net' );
		 	$this->automatic_email_receipts = isset( $this->settings['automatic_email_receipts'] ) && $this->settings['automatic_email_receipts'] == 'yes' ? 'true' : 'false';
		
			add_action( 'woocommerce_receipt_paywaynetrecurring', array( $this, 'wc_payway_receipt_page' ) );
			
				
			// Supports
            $this->supports = array(
            						'products',
							);

            // Logs
			if ( defined('WP_DEBUG') && true === WP_DEBUG) {
				self::$log = new WC_Logger();
			}

			// WC version
			$this->wc_version = get_option( 'woocommerce_version' );
		

			// works only if WooCommerce verison is > 2.0.0
			if ( version_compare( WOOCOMMERCE_VERSION, '2.0.0', '>=' ) ) 
			{
				add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( &$this, 'process_admin_options' ) );
			} 
			else 
			{
				add_action('admin_notices', array(&$this, 'version_check'));
			}

			
			add_action( 'woocommerce_update_options_payment_gateways', array( &$this, 'process_admin_options' ) );
			
			 add_action( 'woocommerce_api_wc_payway_gateway_rest_recurring', array( $this, 'push_payway_recurring_rest_callback' ) );
	
			//Include the PayWay REST API
			wp_enqueue_script('Payway_script','https://api.payway.com.au/rest/v1/payway.js', false);
		}
   
		//Admin form functionality
		function init_form_fields()
		{
			include ( PAYWAYRESTRECURRINGPLUGINPATH . 'includes/payway-rest-admin.php' );
		}
		
		public function admin_options()
		{
			echo '<h3>'.__('PayWay NET Payment Gateway with Recurring Billing', 'woo_payway_recurring_net').'</h3>';
			echo '<p>'.__('Enter your PayWay details below.','woo_payway_recurring_net').'</p>';
			echo '<table class="form-table">';
			// Generate the HTML For the settings form.
			$this -> generate_settings_html();
			echo '</table>';
 
		}
		
		function process_payment($order_id)
		{
        
			$order = new WC_Order( $order_id );
			return array(
                'result'    => 'success',
            	'redirect'	=> $order->get_checkout_payment_url( true )
            );
		
		}
	
		public static function log( $message ) 
		{
			if ( empty( self::$log ) ) {
				self::$log = new WC_Logger();
			}

			self::$log->add( 'woo_payway_recurring_net', $message );

		}
	
		public function wc_payway_receipt_page($order)
		{
			do_action('before_payway_form');
			echo $this -> generate_PayWay_recurring_form($order);
            do_action('after_payway_form');
		}
	
		function generate_PayWay_recurring_form($order_id)
		{
 
			global $woocommerce;
			$order = new WC_Order($order_id);
			$this->log('Order ID: ' . $order_id);
			
			$txnid = $order_id.'_'.date("ymds");
			$this->log('TXN ID: ' . $txnid);
 
			$redirect_url = ($this -> redirect_page_id=="" || $this -> redirect_page_id==0)?get_site_url() . "/":get_permalink($this -> redirect_page_id);
	 
			$productinfo = "Order $order_id";

			$PayWay_args = array(
				'amount' => $order->get_total(),
				'order_id' => $order_id,
				'productinfo' => $productinfo,
				'firstname' => $order -> get_billing_first_name(),
				'lastname' => $order -> get_billing_last_name(),
				'address1' => $order -> get_billing_address_1(),
				'address2' => $order -> get_billing_address_2(),
				'city' => $order -> get_billing_city(),
				'state' => $order -> get_billing_state(),
				'country' => $order -> get_billing_country(),
				'zipcode' => $order -> get_billing_postcode(),
				'email' => $order -> get_billing_email(),
				'phone' => $order -> get_billing_phone(),
				'emailAddress' => $order -> get_billing_email(),
				'postalCode' => $order -> get_billing_postcode()
			);
			
			$this->log('Payway Args: ' . json_encode($PayWay_args));
 
			$PayWay_args_array = array();
			foreach($PayWay_args as $key => $value)
			{
				$PayWay_args_array[] = "<input type='hidden' name='$key' value='$value'/>";
			}
		
			return '<form action='.WC()->api_request_url( get_class( $this ) ).' method="post" 	id="PayWay_payment_form">
            ' . implode('', $PayWay_args_array) . '
            <div id="payway-credit-card"></div>
			<input type="hidden" name="action" value="push_payway_rest" />
			<input type="submit" class="button-alt"  disabled="true" id="submit_PayWay_payment_form" value="'.__('Pay via PayWay', 'payway').'" /> <a class="button cancel" href="'.$order->get_cancel_order_url().'">'.__('Cancel order &amp; restore cart', 'payway').'</a>
			</form>
            <script type="text/javascript">
			
				var submit = document.getElementById(\'submit_PayWay_payment_form\');
				payway.createCreditCardFrame({
				publishableApiKey: \''.$this->settings['publishable_key'].'\',
					onValid: function() { submit.disabled = false; },
				onInvalid: function() { submit.disabled = true; }
				});	
			</script>
            ';
				
			
		}
	/**
	* Send the Payment token to PayWay REST
	**/
	public function push_payway_recurring_rest_callback()
	{
		$singleUseTokenId 	= sanitize_text_field($_REQUEST['singleUseTokenId']);
		$order_id 			= sanitize_text_field($_REQUEST['order_id']);
		$merchantId 		= sanitize_text_field($this->settings['customer-merchant']);
		$secret_key			= sanitize_text_field($this->settings['secret_key']);

		//Checking valid order...
		
		if($order_id != '')
		{
			  
			$order = new WC_Order($order_id);
			$amount = sanitize_text_field($_REQUEST['amount']);

			$is_payway_sub = 'no';
			$payway_frequency = 'monthly';
			$no_of_payments = '3';

			// Get and Loop Over Order Items
			foreach ( $order->get_items() as $item_id => $item ) {
			  	$is_payway_sub = get_post_meta( $item->get_product_id(), '_is_payway_subscription',true );
			  	$payway_frequency = get_post_meta( $item->get_product_id(), '_payway_frequency',true );
				$no_of_payments = get_post_meta( $item->get_product_id(), '_no_of_payments',true );	
			}
			
			//Checking order total...
			if ($amount == $order->get_total())
			{
				//Add Subscription details against customer and store the card.
				if($is_payway_sub == 'yes'){

					$customer_url = 'https://api.payway.com.au/rest/v1/customers/'.$order->get_user_id();
					
					switch ($payway_frequency) {
						case "weekly":
							$nextPaymentDate = date("d M Y", strtotime("+1 week"));
							break;
						case "fortnightly":
							$nextPaymentDate = date("d M Y", strtotime("+2 weeks"));
							break;
						case "monthly":
							$nextPaymentDate = date("d M Y", strtotime("+1 month"));
							break;
						case "quarterly":
							$nextPaymentDate = date("d M Y", strtotime("+3 months"));
							break;
						case "six-monthly":
							$nextPaymentDate = date("d M Y", strtotime("+6 months"));
							break;
						case "yearly":
							$nextPaymentDate = date("d M Y", strtotime("+1 year"));
							break;
						default:
							$nextPaymentDate = date("d M Y", strtotime("+1 month"));
					}
					
					
					$custcurl_post_data = array(
						'singleUseTokenId' => $singleUseTokenId,
						'merchantId' => $merchantId,
						'frequency' => $payway_frequency,
						'regularPrincipalAmount' => $amount,
						'nextPaymentDate' => $nextPaymentDate,
						'numberOfPaymentsRemaining' => $no_of_payments - 1,
						'emailAddress' => $order -> get_billing_email(),
						'sendEmailReceipts' => $this->automatic_email_receipts,
						'postalCode' => $order -> get_billing_postcode()
					);
					
					$this->log('Customer Details Sent: ' . json_encode($custcurl_post_data));

					$customer_args = array(
						'headers' => array(
						    'Authorization' => 'Basic ' . base64_encode( $secret_key)
						),
						'method' => 'PUT',
						'sslverify' => false,
						'body' => $custcurl_post_data

					);
					$response = wp_remote_request( $customer_url,$customer_args);

					$this->log('Customer Response: ' . json_encode($response));
                
					if (wp_remote_retrieve_response_code($response) != 200) {
						wc_add_notice(__('Something Went Wrong. Please try again.'));
						wp_redirect ( $order->get_cancel_order_url());	

					} else {

						$order -> add_order_note('Customer Added to Payway with Payment Frequency '.$payway_frequency.' and Regular Principal Amount of '.$amount);
					}

				}
				
				//Proceed to transact with the customer
				$productinfo = "Order $order_id";
			
				$service_url = 'https://api.payway.com.au/rest/v1/transactions';
			
				$curl_post_data = array(
					'customerNumber' => $order->get_user_id(),
					'transactionType' => 'payment',
					'principalAmount' => $amount,
					'currency' =>	'aud',
					'orderNumber' => $order_id,
					'merchantId' => $merchantId
				);
				
				//Add SingleUseToken Only if Subscription product is not there.
				if($is_payway_sub == 'no' || $is_payway_sub == '' || empty($is_payway_sub)){$curl_post_data['singleUseTokenId'] = $singleUseTokenId;}
				
				$this->log('Transaction Details Sent: ' . json_encode($curl_post_data));
				$service_args = array(
					'headers' => array(
					    'Authorization' => 'Basic ' . base64_encode( $secret_key)
					),
                    'method'     => 'POST',
                    'timeout' => 30,
					'sslverify' => false,
					'body' => $curl_post_data

				);
				$response = wp_remote_request( $service_url,$service_args);

				$this->log('Transaction Response: ' . json_encode($response));

				if (wp_remote_retrieve_response_code($response) !== 200 && wp_remote_retrieve_response_code($response) !== 201) {
						wc_add_notice(__('Something Went Wrong. Please try again.'));
					wp_redirect ( $order->get_cancel_order_url());	

				}else
				{
                	$body = wp_remote_retrieve_body($response);
					$json = json_decode($body,true);
					if ($json['status'] == 'approved')
					{
							$order -> payment_complete();
							$order -> add_order_note('PayWay payment successful. Receipt # '.$json['receiptNumber'].'. Transaction Id'. $json['transactionId']);
							
							wp_redirect($order->get_checkout_order_received_url());
					}
					else
					{
						$order -> add_order_note('The transaction has been declined. Transaction Id : '. $json['transactionId'].'. Response Code: '.$json['responseCode'].' Resonse Text : '.$json['responseText']);

						wc_add_notice(__('The transaction has been declined. Transaction Id : '. $json['transactionId'].'. Response Code: '.$json['responseCode'].' Resonse Text : '.$json['responseText'].'. Please try again.'));

						wp_redirect ( $order->get_cancel_order_url());	
					}	
				}
			}			
		}
	}
}