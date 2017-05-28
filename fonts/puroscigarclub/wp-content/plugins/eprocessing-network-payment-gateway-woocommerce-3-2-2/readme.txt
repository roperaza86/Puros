=== Plugin Name ===
Eprocessing Network Payment Gateway For WooCommerce
Contributors: Pledged Plugins
Tags: woocommerce Eprocessing Network, Eprocessing Network, payment gateway, woocommerce, woocommerce payment gateway
Plugin URI: http://pledgedplugins.com/products/eprocessing-network-payment-gateway-woocommerce/
Requires at least: 3.0.1
Tested up to: 4.2.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This Payment Gateway For WooCommerce extends the functionality of WooCommerce to accept payments from credit/debit cards using the Eprocessing Network payment gateway. Since customers will be entering credit cards directly on your store you should sure that your checkout pages are protected by SSL.

== Description ==

<h3>Eprocessing Network Payment Gateway for WooCommerce</h3> allows you to accept credit cards directly on your WooCommerce store by utilizing the Eprocessing Network payment gateway.

= Features =

1. Accept Credit Cards directly on your website by using the Eprocessing Network gateway.
2. No redirecting your customer back and forth.
3. Very easy to install and configure. Ready in Minutes!
4. Cusotmizable transaction success and failure messages.
5. Safe and secure method to process credit cards using the Eprocessing Network (AIM) method.
6. Internally processes credit cards, safer, quicker, and more secure!


If you need any assistance with this or any of our other plugins, please visit our support portal:
http://www.pledgedplugins.com/support

== Installation ==

Easy steps to install the plugin:

1. Upload `eprocessing-network-payment-gateway-woocommerce` folder/directory to the `/wp-content/plugins/` directory
2. Activate the plugin (Wordpress -> Plugins).
3. Go to the WooCommerce settings page (Wordpress -> WooCommerce -> Settings) and select the Checkout tab.
4. Under the Checkout tab, you will find all the available payment gateways. Find the 'Eprocessing Network Payment Gateway' row and click on the 'Settings' button.
5. On this page you wil find all of the configuration options for this payment gateway.
6. Enable the method by using the checkbox.
7. Enter the Eprocessing Network account details (Login ID, password)

That's it! You are ready to accept credit cards with your Eprocessing Network payment gateway now connected to WooCommerce.

Description: This will appear on checkout page as description for this payment gateway

Login ID: This is the account number provided by Eprocessing Network. See your merchant account rep for additional information.

Password: This is the RestrictKey provided by Eprocessing Network. See your merchant account rep for additional information.

Transaction Success Message: This message will appear upon successful transaction. You can customize this message as per your need.

Transaction Failed Message: This message will appear when transaction will get failed/declined at payment gateway.

== Frequently Asked Questions ==
= Is SSL Required to use this plugin? =
Not required to function, however it is highly recommended.

== Special Account Instructions ==
Special EPN Instructions:
You must enable Authnet emulation and find your Restrict Key. You can do this by logging in to your EPN account:

Log in. Choose ‘Processing Control’ from the dropdown and click Go.
Under Disabled Integrations you will need to UNCHECK the Authorize.net emulation to unblock it.
Click the ‘Save Disabled Integrations’ button.
IMPORTANT: This starts a security timer — Wait 15 minutes to process any transactions.
Scroll to the ‘Advanced’ section. The Restrict Key will be in the textbox to the right of a button labeled ‘Generate Restrict Key’. You can simply copy the key that is there.

== Changelog ==
3.2.2
Added support for TLS 1.2 for PCI compliance as required by EPN by 9/1/15. 
Tested to WooCommerce 2.4.3
Tested to WordPress 4.2.4

3.2.1
Compatible to WooCommerce 2.3.x
Compatible to WordPress 4.x

3.0
Compatible to WooCommerce 2.2.2
Compatible to WordPress 4.0

2.0
Compatible to WooCommerce 2.1.1