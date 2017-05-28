<?php
/*
Plugin Name: WooCommerce Eprocessing Network Payment Gateway
Plugin URI: http://www.smartsolutionpro.us/woocommerce/gateways/epn
Description: Extends WooCommerce with epn payment gateway.
Version: 0.1.1
Author: Ahmad Sajid
Author URI: http://www.smartsolutionpro.info/ahmadsajid
*/



add_action('plugins_loaded', 'epn_gateway', 0);
 
function epn_gateway() 
{
    class WC_Epn extends WC_Payment_Gateway{

        function __construct()
        {
            
            global $woocommerce;
			
         $this->id			= 'epn';
         $this->icon 		= apply_filters('woocommerce_epn_icon', $woocommerce->plugin_url() . '/assets/images/icons/epn.png');
       	 $this->has_fields 	= false;
       	 $this->liveurl 		= 'https://www.eprocessingnetwork.com/cgi-bin/dbe/order.pl';
		 $this->method_title     = __( 'EPN', 'woocommerce' );
            
  			// load form fields
            $this->init_form_fields();

            // initialise settings
            $this->init_settings();
            
           // Define user set variables
		$this->title 			= $this->settings['title'];
		$this->description 		= $this->settings['description'];
		$this->email 			= $this->settings['email'];
		
		$this->form_submission_method = ( isset( $this->settings['form_submission_method'] ) && $this->settings['form_submission_method'] == 'yes' ) ? true : false;
		

		// Logs
		

		// Actions
		add_action( 'init', array(&$this, 'check_ipn_response') );
		add_action('valid-epn-ipn-request', array(&$this, 'successful_request') );
		add_action('woocommerce_receipt_epn', array(&$this, 'receipt_page'));
		add_action('woocommerce_update_options_payment_gateways', array(&$this, 'process_admin_options'));

		if ( !$this->is_valid_for_use() ) $this->enabled = false;
                        
        } // end __construct
		
		
		 /**
     * Check if this gateway is enabled and available in the user's country
     *
     * @access public
     * @return bool
     */
    function is_valid_for_use() {
        if (!in_array(get_woocommerce_currency(), array('USD'))) return false;

        return true;
    }
        
        /**
         * Admin Panel Options 
         **/       
       public function admin_options() {

    	?>
    	<h3><?php _e('EPN', 'woocommerce'); ?></h3>
    	<p><?php _e('Eprocessing Network works by sending the user to EPN to enter their payment information.', 'woocommerce'); ?></p>
    	<table class="form-table">
    	<?php
    		if ( $this->is_valid_for_use() ) :

    			// Generate the HTML For the settings form.
    			$this->generate_settings_html();

    		else :

    			?>
            		<div class="inline error"><p><strong><?php _e( 'Gateway Disabled', 'woocommerce' ); ?></strong>: <?php _e( 'EPN does not support your store currency.', 'woocommerce' ); ?></p></div>
        		<?php

    		endif;
    	?>
		</table><!--/.form-table-->
    	<?php
    }
       /**
     * Initialise Gateway Settings Form Fields
     *
     * @access public
     * @return void
     */
    function init_form_fields() {

    	$this->form_fields = array(
			'enabled' => array(
							'title' => __( 'Enable/Disable', 'woocommerce' ),
							'type' => 'checkbox',
							'label' => __( 'Enable EPN', 'woocommerce' ),
							'default' => 'yes'
						),
			'title' => array(
							'title' => __( 'Title', 'woocommerce' ),
							'type' => 'text',
							'description' => __( 'This controls the title which the user sees during checkout.', 'woocommerce' ),
							'default' => __( 'EPN', 'woocommerce' )
						),
			'description' => array(
							'title' => __( 'Description', 'woocommerce' ),
							'type' => 'textarea',
							'description' => __( 'This controls the description which the user sees during checkout.', 'woocommerce' ),
							'default' => __("Pay via EPN; you can pay with your credit card.", 'woocommerce')
						),
			'email' => array(
							'title' => __( 'Eprocessing Network Email', 'woocommerce' ),
							'type' => 'text',
							'description' => __( 'Please enter your EPN email address; this is needed in order to take payment.', 'woocommerce' ),
							'default' => ''
						),
			'ePNAccount' => array(
							'title' => __( 'Eprocessing Network Account Number', 'woocommerce' ),
							'type' => 'text',
							'description' => __( 'Please enter your EPN account number; this is needed in order to take payment.', 'woocommerce' ),
							'default' => ''
						),
		
			'form_submission_method' => array(
							'title' => __( 'Submission method', 'woocommerce' ),
							'type' => 'checkbox',
							'label' => __( 'Use form submission method.', 'woocommerce' ),
							'description' => __( 'Enable this to post order data to EPN via a form instead of using a redirect/querystring.', 'woocommerce' ),
							'default' => 'no'
						),
			
			);

    }

        
       /**
	 * Get epn Args for passing to PP
	 *
	 * @access public
	 * @param mixed $order
	 * @return array
	 */
	function get_epn_args( $order ) {
		global $woocommerce;

		$order_id = $order->id;

		

		// epn Args
		$epn_args = 
			array(
				
				
				'ePNAccount' 			=> $this->ePNAccount,
				
				'RefNr' 				=> $order_id,
				'ID' 					=> $order_id,
				
				'ReturnApprovedURL' 	=> trailingslashit( home_url() ) . '?epnListener=result&epn=1&id='.$order->id,
				'ReturnDeclinedURL'		=> trailingslashit( home_url() ) . '?epnListener=result&epn=0&id='.$order->id,
				'cancel_return'			=> $order->get_cancel_order_url(),
				
				
				// Billing Address info
				'FirstName'				=> $order->billing_first_name,
				'LastName'				=> $order->billing_last_name,
				'Company'				=> $order->billing_company,
				'Address'				=> $order->billing_address_1 . $order->billing_address_2,
				'City'					=> $order->billing_city,
				'State'					=> $order->billing_state,
				'Zip'					=> $order->billing_postcode,
				'Country'				=> $order->billing_country,
				'EMail'					=> $order->billing_email
			
			
		);

		

		// If prices include tax or have order discounts, send the whole order as a single item
		if ( get_option('woocommerce_prices_include_tax')=='yes' || $order->get_order_discount() > 0 ) :

			// Discount
			$epn_args['discount_amount_cart'] = $order->get_order_discount();

			


			
			// Cart Contents
			$item_loop = 0;
			if (sizeof($order->get_items())>0) : foreach ($order->get_items() as $item) :
				if ($item['qty']) :

					$item_loop++;

					$product = $order->get_product_from_item($item);

					$item_name 	= $item['name'];

					$item_meta = new WC_Order_Item_Meta( $item['item_meta'] );
					if ($meta = $item_meta->display( true, true )) :
						$item_name .= ' ('.$meta.')';
					endif;

					$epn_args['item_name_'.$item_loop] = $item_name;
					if ($product->get_sku()) $epn_args['item_number_'.$item_loop] = $product->get_sku();
					$epn_args['quantity_'.$item_loop] = $item['qty'];
					$epn_args['amount_'.$item_loop] = $order->get_item_total( $item, false );

				endif;
			endforeach; endif;

			// Shipping Cost item - epn only allows shipping per item, we want to send shipping for the order
			if ($order->get_shipping()>0) :
				$item_loop++;
				$epn_args['item_name_'.$item_loop] = __('Shipping via', 'woocommerce') . ' ' . ucwords($order->shipping_method_title);
				$epn_args['quantity_'.$item_loop] = '1';
				$epn_args['amount_'.$item_loop] = number_format($order->get_shipping(), 2, '.', '');
			endif;

		endif;

		$epn_args = apply_filters( 'woocommerce_epn_args', $epn_args );

		return $epn_args;
	}


    /**
	 * Generate the epn button link
     *
     * @access public
     * @param mixed $order_id
     * @return string
     */
     function generate_epn_form( $order_id ) {
		global $woocommerce;

		$order = new WC_Order( $order_id );

		if ( $this->testmode == 'yes' ):
			$epn_adr = $this->testurl . '?test_ipn=1&';
		else :
			$epn_adr = $this->liveurl . '?';
		endif;

		$epn_args = $this->get_epn_args( $order );

		$epn_args_array = array();

		foreach ($epn_args as $key => $value) {
			$epn_args_array[] = '<input type="hidden" name="'.esc_attr( $key ).'" value="'.esc_attr( $value ).'" />';
		}

		$woocommerce->add_inline_js('
			jQuery("body").block({
					message: "<img src=\"' . esc_url( apply_filters( 'woocommerce_ajax_loader_url', $woocommerce->plugin_url() . '/assets/images/ajax-loader.gif' ) ) . '\" alt=\"Redirecting&hellip;\" style=\"float:left; margin-right: 10px;\" />'.__('Thank you for your order. We are now redirecting you to epn to make payment.', 'woocommerce').'",
					overlayCSS:
					{
						background: "#fff",
						opacity: 0.6
					},
					css: {
				        padding:        20,
				        textAlign:      "center",
				        color:          "#555",
				        border:         "3px solid #aaa",
				        backgroundColor:"#fff",
				        cursor:         "wait",
				        lineHeight:		"32px"
				    }
				});
			jQuery("#submit_epn_payment_form").click();
		');

		return '<form action="https://www.eProcessingNetwork.com/cgi-bin/dbe/order.pl" method="post" id="epn_payment_form" target="_top">
				<INPUT TYPE="HIDDEN" NAME="ePNAccount"
   VALUE="0712197">
   <INPUT TYPE="HIDDEN" NAME="Total" VALUE="'.$order->order_total.'">
   <INPUT TYPE="HIDDEN" NAME="FirstName" VALUE="'.$epn_args['FirstName'].'">
<INPUT TYPE="HIDDEN" NAME="LastName" VALUE="'.$epn_args['LastName'].'">
<INPUT TYPE="HIDDEN" NAME="Address" VALUE="'.$epn_args['Address'].'">
<input type="hidden" name="Company" value="'.$epn_args['Company'].'" />
<INPUT TYPE="HIDDEN" NAME="Zip" VALUE="'.$epn_args['Zip'].'">
<INPUT TYPE="HIDDEN" NAME="City" VALUE="'.$epn_args['City'].'">
<INPUT TYPE="HIDDEN" NAME="Country" VALUE="'.$epn_args['Country'].'">
<INPUT TYPE="HIDDEN" NAME="EMail" VALUE="'.$epn_args['EMail'].'">
<INPUT TYPE="HIDDEN" NAME="ID" VALUE="'.$order->id.'">
<INPUT TYPE="HIDDEN" NAME="ReturnApprovedURL"
   VALUE="'.$epn_args['ReturnApprovedURL'].'">
<INPUT TYPE="HIDDEN" NAME="ReturnDeclinedURL"
   VALUE="'.$epn_args['ReturnDeclinedURL'].'">
				<input type="submit" class="button-alt" id="submit_epn_payment_form" value="'.__('Pay via epn', 'woocommerce').'" /> <a class="button cancel" href="'.esc_url( $order->get_cancel_order_url() ).'">'.__('Cancel order &amp; restore cart', 'woocommerce').'</a>
			</form>';

	}
        
      /**
     * Process the payment and return the result
     *
     * @access public
     * @param int $order_id
     * @return array
     */
	function process_payment($order_id) {

		$order = new WC_Order($order_id);

		

		
			
			return array(
				'result' 	=> 'success',
				'redirect'	=> add_query_arg('order', $order->id, add_query_arg('key', $order->order_key, get_permalink(woocommerce_get_page_id('pay'))))
			);
			
			echo $_GET['epn'];
			

		}

	


    /**
     * Output for the order received page.
     *
     * @access public
     * @return void
     */
	function receipt_page( $order ) {

		echo '<p>'.__('Thank you for your order, please click the button below to pay with epn.', 'woocommerce').'</p>';

		echo $this->generate_epn_form( $order );

	}
           
        /**
         * Send debug email
         * 
         * @param string $msg
         **/
        function send_debug_email( $msg)
        {
            if($this->debug=='yes' AND $this->mode!=live AND !empty($this->debugemail)){
                // send debugemail
                wp_mail( $this->debugemail, __('Eprocessing Network Debug', 'woocommerce'), $msg );
            }
            
            
            
        } // end send_debug_email
		
		/**
	 * Check for epn IPN Response
	 *
	 * @access public
	 * @return void
	 */
	
	 
	function check_ipn_response()
	{
		global $woocommerce;

		 if (isset($_GET['epn']) AND $_GET['epn'] == '1')
		{
			$inv_id = $_GET['id'];
			$order = new WC_Order($inv_id);
			$order->update_status('on-hold', __('Payment Successfull', 'woocommerce'));
			$order->payment_complete();
			unset($_SESSION['order_awaiting_payment']);
			// Reduce stock levels
			$order->reduce_order_stock();
			$woocommerce->cart->empty_cart();
			wp_redirect(add_query_arg('key', $order->order_key, add_query_arg('order', $inv_id, get_permalink(get_option('woocommerce_thanks_page_id')))));
			exit;
		}
		else if (isset($_GET['epn']) AND $_GET['epn'] == '0')
		{
			$inv_id = $_GET['id'];
			$order = new WC_Order($inv_id);
			$order->update_status('failed', __('Payment Failed', 'woocommerce'));
			unset($_SESSION['order_awaiting_payment']);
			//$woocommerce->cart->empty_cart();
			wp_redirect($order->get_cancel_order_url());
			exit;
		}

		//echo add_query_arg('key', $order->order_key, add_query_arg('order', $inv_id, get_permalink(get_option('jigoshop_thanks_page_id'))));
	}


        
        /**
        * add sagepay parameters for later processing
        * 
        * @param string $param
        * @param mixed $value
        */
        public function add_param($param, $value) {
            $this->params[$param] = $value;   
        } // end add_param
    }    
} // end epn_init

function add_epn( $methods ) 
{
    $methods[] = 'WC_Epn'; 
    return $methods;
} // end add_epn


add_filter('woocommerce_payment_gateways', 'add_epn' );
?>