=== Westpac PayWay NET Payment Gateway for WooCommerce (Recurring) ===
Contributors: hncvj
Donate link: https://www.upwork.com/fl/hncvj
Tags: woocommerce, payment, gateway
Requires at least: 4.9
Tested up to: 5.7.1
Stable tag: 2.1
Requires PHP: 5.6
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

This plugin extends WooCommerce and provides a secure Iframe based checkout method using Westpac PayWay. 

== Description ==

This plugin extends WooCommerce and provides a secure Iframe based checkout method for Westpac PayWay. The Iframe uses PayWay REST API and no credit card data is stored on your server. This means there is no need of PCI-DSS compliance so costs are reduced. This plugin works with AUD currency as well and support Recurring Payments.


== Features ==

- Westpac Payway Trusted Frame Based
- Recurring Billing Supported
- Multiple Number of Payments for a Subscription Supported
- Email Receipts (Via Payway)
- Auto Add/Update Customer
- Latest Woocommerce Compatible

== Installation ==

1. Search and Install the plugin through the WordPress plugins screen OR Upload the plugin files to the '/wp-content/plugins/woo-westpac-payway-payment-gateway-recurring' directory.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Use the Woocommerce > Settings > Payments > PayWay NET with Recurring > Manage for Setup.


== Setup ==
1. Navigate to Woocommerce > Settings > Payments > PayWay NET with Recurring > Manage
2. Add Publishable Key under "PayWay Publishable Key".
3. Add Secret Key under "PayWay Secret Key".
4. Add Merchant ID under "Merchant ID". If you are using TEST account then keep TEST there.
5. Check "Enable/Disable Sending Receipt" to send Email Receipts. Make sure to turn this ON in your Westpac Payway Account.

== Product Setup ==

1. Navigate to Woocommerce All Products Screen.
2. Edit any product you want to create Payway Subscription for.
3. Under "General" tab you'll see a Checkbox "Is it Payway Subscription?". Check that box.
4. Set value for "Total Number of Payments" you want to take. Like you want Subscription for 3 Months or 3 weeks then you'll enter 3 there.
5. Select "Frequency of Payments" Supported by Payway like Weekly, Fortnightly, Monthly, Quarterly, Six Monthly and Yearly.

== Contribute ==

You can contribute to this project on Github here: https://github.com/hncvj/woo-westpac-payway-payment-gateway-recurring

== Donation ==

If you liked my work and if this plugin fulfilled your needs, please donate via below link.

https://rzp.io/l/hncvj



