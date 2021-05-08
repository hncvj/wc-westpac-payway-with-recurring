<?php
/*
 * Plugin Name: Westpac PayWay NET Payment Gateway for WooCommerce (Recurring)
 * Plugin URI: https://www.upwork.com/fl/hncvj
 * Description: The plugin gives the functionality of processing Credit and Debit Cards on WooCommerce using Westpac PayWay NET along with Recurring Payments.
 * Version: 1.0
 * Author: Spanrig Technologies
 * Author URI: https://www.upwork.com/fl/hncvj
 * License: GPL v2 or later
 * Text Domain:       woo_payway_recurring_net
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

//intialize the plugin
add_action('plugins_loaded', 'woocommerce_payway_gateway_rest_recurring_init', 0);

function woocommerce_payway_gateway_rest_recurring_init()
{
	
	if(!class_exists('WC_Payment_Gateway')) return;
 	
	/**
     * Defines
     */
    define( 'PAYWAYRESTRECURRINGSUPPORTURL' , 'https://www.upwork.com/fl/hncvj' );
    define( 'PAYWAYRESTRECURRINGDOCSURL' , 'https://www.upwork.com/fl/hncvj');
    define( 'PAYWAYRESTRECURRINGPLUGINPATH', plugin_dir_path( __FILE__ ) );
    define( 'PAYWAYRESTRECURRINGPLUGINURL', plugin_dir_url( __FILE__ ) );


	include('classes/payway-gateway-rest.php');

	/**
	* Add the Gateway to WooCommerce
	**/
	function woocommerce_add_payway_gateway_rest_recurring($methods) 
	{
		$methods[] = 'WC_Payway_Gateway_Rest_Recurring';
		return $methods;
	}
 
	add_filter('woocommerce_payment_gateways', 'woocommerce_add_payway_gateway_rest_recurring' );
	
	/**
	 * Empty Cart When a Payway Subscription Product is added to Cart.
	 * @rules
	 * @1. Two Subscription Products Cannot be added together
	 * @2. One Subscription and One Normal Product cannot be added together.
	 * @3. When a Subscription Product is added Cart is emptied and only that product is kept in cart.
	 * @4. When a Normal Product is added all Susbcription Products are Removed from cart.
	 * Note: Subscription above represents Payway Susbcription.
	 */
	add_filter( 'woocommerce_add_cart_item_data', 'payway_empty_cart', 10,  3);
	function payway_empty_cart( $cart_item_data, $product_id, $variation_id ) 
	{

		global $woocommerce;
		$is_payway_sub = get_post_meta( $product_id, '_is_payway_subscription',true );
		if(!empty($is_payway_sub) && $is_payway_sub == 'yes'){
			$woocommerce->cart->empty_cart();
			$paywayrest_payment_settings = WC()->payment_gateways->payment_gateways()['paywaynetrecurring'];
			if(!empty($paywayrest_payment_settings->woocommerce_notice_for_payway_recurring)){
				wc_add_notice(__($paywayrest_payment_settings->woocommerce_notice_for_payway_recurring, 'woo_payway_recurring_net' ));
			}			
		}else{
			foreach ( $woocommerce->cart->get_cart() as $cart_item_key => $cart_item ) {
				$is_payway_sub = get_post_meta( $cart_item['product_id'], '_is_payway_subscription',true );
				if(!empty($is_payway_sub) && $is_payway_sub == 'yes'){
					$woocommerce->cart->remove_cart_item( $cart_item_key );
				}
			}
		}
		return $cart_item_data;
	}

		
	/**
	 * Payway Custom product Fields
	 */
	function payway_custom_product_fields() {
		woocommerce_wp_checkbox( array(
				'id'            => '_is_payway_subscription',
				'label'         => __( 'Is it Payway Subscription?', 'woocommerce' ),
				'description'   => __( 'Enable this if you want this product to be a Payway Subscription product.', 'woocommerce' ),
			)
		);
		
  
		woocommerce_wp_text_input( array(
			'id' => '_no_of_payments',
			'label' => __( 'Total Number of Payments', 'textdomain' ),
			'desc_tip' => true,
			'description'   => __( 'Total number of payments to be taken from customer including 1st payment.', 'woocommerce' ),
			'type' => 'number',
			'custom_attributes' => array(
				'step' => '1',
				'min' => '0'
			)) 
		);
		
		woocommerce_wp_select( array(
			'id' => '_payway_frequency',
			'label' => __( 'Select Frequency of Payments', 'woocommerce' ),
			'options' => array(
			  'weekly' => __( 'Weekly', 'woocommerce' ),
			  'fortnightly' => __( 'Fortnightly', 'woocommerce' ),
			  'monthly' => __( 'Monthly', 'woocommerce' ),
			  'quarterly' => __( 'Quarterly', 'woocommerce' ),
			  'six-monthly' => __( 'Six Monthly', 'woocommerce' ),
			  'yearly' => __( 'Yearly', 'woocommerce' )
			)
		  )
		);
	}
	add_action( 'woocommerce_product_options_general_product_data', 'payway_custom_product_fields' );


	function payway_custom_fields_save( $post_id ){

		//Save is Subscription
		$is_payway_subscription = isset( $_POST['_is_payway_subscription'] ) ? 'yes' : 'no';
		update_post_meta( $post_id, '_is_payway_subscription', $is_payway_subscription );
		
		//Save No of Payments
		$no_of_payments = $_POST['_no_of_payments'];
		if (!empty($no_of_payments)){
			update_post_meta($post_id, '_no_of_payments', esc_attr($no_of_payments));
		}else{
			update_post_meta($post_id, '_no_of_payments', '3');
		}
		//Save Frequency
		$frequency  = $_POST['_payway_frequency'];
		if( !empty( $frequency ) )
		  update_post_meta( $post_id, '_payway_frequency', esc_attr( $frequency ) );
		else {
		  update_post_meta( $post_id, '_payway_frequency',  'monthly' );
		}
	}
	add_action( 'woocommerce_process_product_meta', 'payway_custom_fields_save' );

}