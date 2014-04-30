<?php
/**
 * Banesto gateway class
 *
 * @package		Jigoshop
 * @subpackage 	Jigoshop Banesto Gateway
 * @since 		0.1
 *
**/
class banesto extends jigoshop_payment_gateway {
	
	public function __construct() {
		
		parent::__construct();
		
		$this->id			    = 'banesto';
        $this->icon 		    = plugins_url( '/assets/images/icon-banesto.png', __FILE__ );
		$this->has_fields 	    = false;
	  	$this->enabled		    = Jigoshop_Base::get_options()->get_option('jigoshop_banesto_enabled');
		$this->title 		    = Jigoshop_Base::get_options()->get_option('jigoshop_banesto_title');
        $this->commerce_name    = Jigoshop_Base::get_options()->get_option('jigoshop_banesto_commerce_name');
        $this->message_ok       = Jigoshop_Base::get_options()->get_option('jigoshop_banesto_message_ok');
        $this->message_ko       = Jigoshop_Base::get_options()->get_option('jigoshop_banesto_message_ko');
		$this->description      = Jigoshop_Base::get_options()->get_option('jigoshop_banesto_description');

		$this->testurl 		= 'http://pruebas.pagoseguro.com.es/cgi-bin/totalizacion';
		$this->liveurl 		= 'https://servidordepagos.com/Banesto/totalizacion.exe';
		$this->testmode     = Jigoshop_Base::get_options()->get_option('jigoshop_banesto_testmode');

		add_action( 'init', array(&$this, 'check_tpv_response'), 640 );
        add_filter( 'jigoshop_thankyou_message', array(&$this, 'thankyou_message'));
        add_action( 'thankyou_banesto', array(&$this, 'thankyou_page'));
		add_action( 'receipt_banesto', array(&$this, 'receipt_page') );
	}
    
    
	/**
	 * Default Option settings for WordPress Settings API using the Jigoshop_Options class
	 *
	 * These will be installed on the Jigoshop_Options 'Payment Gateways' tab by the parent class 'jigoshop_payment_gateway'
	 *
	 */	
	function thankyou_page() {
        
        if (!isset($_GET['return']) || empty($_GET['return']) ) return;
        
        // result message
        switch ($_GET['return']) {
            case 'ok':
                echo wpautop(wptexturize($this->message_ok));
                break;
            
            case 'ko':
                echo wpautop(wptexturize($this->message_ko));
                break;
        }
        
		if ($this->description) ;
	}
    
    
    
	/**
	 * Default Option settings for WordPress Settings API using the Jigoshop_Options class
	 *
	 * These will be installed on the Jigoshop_Options 'Payment Gateways' tab by the parent class 'jigoshop_payment_gateway'
	 *
	 */	    
    public function thankyou_message($thankyou_message) {
        
        if (!isset($_GET['return']) || empty($_GET['return']) ) return $thankyou_message;
        
        // result message
        switch ($_GET['return']) {
            case 'ok':
                $thankyou_message = __('<div class="jigoshop_message">Thanks, payment was successfully.</div><br>', 'jigoshop');
                break;
            
            case 'ko':
                $thankyou_message = __('<div class="jigoshop_error">Sorry, the payment has been denied by the entity.</div><br>', 'jigoshop');
                break;
        }
        
        // order
        
        if (isset($_GET['referencia']) && !empty($_GET['referencia'])) {
            global $wpdb;
            
            $order_id = $wpdb->get_var("SELECT post_id FROM $wpdb->postmeta WHERE meta_key = 'order_key' AND meta_value = '".$_GET['referencia']."'");
            
            if (!$order_id) {
                wp_safe_redirect(home_url());
                exit;
            }
            
            $_GET['order'] = $order_id;
            $_GET['key'] = $_GET['referencia'];
        }
        
        return $thankyou_message; 
    }
    
    
    
	/**
	 * Default Option settings for WordPress Settings API using the Jigoshop_Options class
	 *
	 * These will be installed on the Jigoshop_Options 'Payment Gateways' tab by the parent class 'jigoshop_payment_gateway'
	 *
	 */	
	protected function get_default_options() {
	
		$defaults = array();
		
		// Define the Section name for the Jigoshop_Options
		$defaults[] = array( 
            'name' => __('Banesto', 'jigoshop'),
            'type' => 'title',
            'desc' => __('Banesto works by sending the user to <a href="http://www.banesto.com/">Banesto TPV</a> to enter their payment information.', 'jigoshop')
        );
		
		// List each option in order of appearance with details
		$defaults[] = array(
			'name'		=> __('Enable Banesto','jigoshop'),
			'desc' 		=> '',
			'tip' 		=> '',
			'id' 		=> 'jigoshop_banesto_enabled',
			'std' 		=> 'yes',
			'type' 		=> 'checkbox',
			'choices'	=> array(
				'no'			=> __('No', 'jigoshop'),
				'yes'			=> __('Yes', 'jigoshop')
			)
		);
		
		$defaults[] = array(
			'name'		=> __('Method Title','jigoshop'),
			'desc' 		=> '',
			'tip' 		=> __('This controls the title which the user sees during checkout.','jigoshop'),
			'id' 		=> 'jigoshop_banesto_title',
			'std' 		=> __('Banesto','jigoshop'),
			'type' 		=> 'text'
		);
		
		$defaults[] = array(
			'name'		=> __('Description','jigoshop'),
			'desc' 		=> '',
			'tip' 		=> __('This controls the description which the user sees during checkout.','jigoshop'),
			'id' 		=> 'jigoshop_banesto_description',
			'std' 		=> __("Pay via Banesto; you can pay with your credit card in Banesto TPV platform.", 'jigoshop'),
			'type' 		=> 'longtext'
		);
        
		$defaults[] = array(
			'name'		=> __('Enable testmode','jigoshop'),
			'desc' 		=> __('Turn on to enable the Banesto for testing.','jigoshop'),
			'tip' 		=> '',
			'id' 		=> 'jigoshop_banesto_testmode',
			'std' 		=> 'no',
			'type' 		=> 'checkbox',
			'choices'	=> array(
				'no'			=> __('No', 'jigoshop'),
				'yes'			=> __('Yes', 'jigoshop')
			)
		);

		$defaults[] = array(
			'name'		=> __('Commerce Name','jigoshop'),
			'desc' 		=> '',
			'tip' 		=> __('If product totals are free and shipping is also free (excluding taxes), this will force 0.01 to allow paypal to process payment. Shop owner is responsible for refunding customer.','jigoshop'),
			'id' 		=> 'jigoshop_banesto_commerce_name',
			'type' 		=> 'text',
		);
        
		$defaults[] = array(
			'name'		=> __('Message OK','jigoshop'),
			'desc' 		=> 'Mensaje extra en la pagina de resultado cuando se ha completado el pago con éxito.',
			'id' 		=> 'jigoshop_banesto_message_ok',
			'type' 		=> 'longtext',
            'std'       => 'Revista tu correo electrónico donde te habrá llegado la confirmación del pago.'
		);
        
		$defaults[] = array(
			'name'		=> __('Message KO','jigoshop'),
			'desc' 		=> 'Mensaje extra en la pagina de resultado cuando el pago se ha denegado por la entidad.',
			'id' 		=> 'jigoshop_banesto_message_ko',
			'type' 		=> 'longtext',
            'std'       => 'Vuelve a intentarlo con otra tarjeta de crédito. Si no funciona ponte en contacto con nosotros por correo electrónico.'
		);

		return $defaults;
	}
    
    
	
	/**
	 * There are no payment fields for paypal, but we want to show the description if set.
	 **/
	function payment_fields() {
		if ($this->description) echo wpautop(wptexturize($this->description));
	}
    
    
    
	/**
	 * Generate the paypal button link
	 **/
	public function generate_banesto_form( $order_id ) {

		$order = new jigoshop_order( $order_id );
        
        $subtotal = (float)(Jigoshop_Base::get_options()->get_option('jigoshop_prices_include_tax') == 'yes' ? (float)$order->order_subtotal + (float)$order->order_tax : $order->order_subtotal);
        $shipping_total = (float)(Jigoshop_Base::get_options()->get_option('jigoshop_prices_include_tax') == 'yes' ? (float)$order->order_shipping + (float)$order->order_shipping_tax : $order->order_shipping);
        
		// banesto_adr
		if ( $this->testmode == 'yes' ):
			$banesto_adr = $this->testurl;
		else :
			$banesto_adr = $this->liveurl;
		endif;
		

		$shipping_name = explode(' ', $order->shipping_method);

		if (in_array($order->billing_country, array('US','CA'))) :
			$order->billing_phone = str_replace(array('(', '-', ' ', ')'), '', $order->billing_phone);
			$phone_args = array(
				'night_phone_a' => substr($order->billing_phone,0,3),
				'night_phone_b' => substr($order->billing_phone,3,3),
				'night_phone_c' => substr($order->billing_phone,6,4),
				'day_phone_a' 	=> substr($order->billing_phone,0,3),
				'day_phone_b' 	=> substr($order->billing_phone,3,3),
				'day_phone_c' 	=> substr($order->billing_phone,6,4)
			);
		else :
			$phone_args = array(
				'night_phone_b' => $order->billing_phone,
				'day_phone_b' 	=> $order->billing_phone
			);
		endif;

		// filter redirect page
		$checkout_redirect = apply_filters( 'jigoshop_get_checkout_redirect_page_id', jigoshop_get_page_id('thanks') ) ;
        $checkout_redirect_url = add_query_arg(array('key' => $order->order_key, 'order' => $order_id),  get_permalink($checkout_redirect) );
            
		$banesto_args = array_merge(
			array(
				'moneda'         		=> Jigoshop_Base::get_options()->get_option('jigoshop_currency'),
				'charset' 				=> 'UTF-8',
                'nombre_comercio'       => $this->commerce_name,
                
                // URLs
                /*
				'return' 				=> add_query_arg(array('key' => $order->order_key, 'order' => $order_id),  $checkout_redirect_url ),
				'url_ko'		    	=> add_query_arg(array('key' => $order->order_key, 'order' => $order_id),  $checkout_redirect_url ),
                'url_ok'    			=> add_query_arg(array('key' => $order->order_key, 'order' => $order_id), $checkout_redirect_url ),
                'url_post'              => add_query_arg(array('banestolistener' => 'banesto_standard_tpv', 'key' => $order->order_key, 'order' => $order_id), home_url()),
                */

				// Address info
				'first_name'			=> $order->billing_first_name,
				'last_name'				=> $order->billing_last_name,
				'company'				=> $order->billing_company,
				'address1'				=> $order->billing_address_1,
				'address2'				=> $order->billing_address_2,
				'city'					=> $order->billing_city,
				'state'					=> $order->billing_state,
				'zip'					=> $order->billing_postcode,
				'country'				=> $order->billing_country,
				'email'					=> $order->billing_email,

				// Payment Info
                'referencia'            => $order->order_key,
				'coste' 				=> $order->order_total,
			),
			$phone_args
		);

		$banesto_args_array = array();

		foreach ($banesto_args as $key => $value) {
			$banesto_args_array[] = '<input type="hidden" name="'.esc_attr($key).'" value="'.esc_attr($value).'" />';
		}

		return '<form action="'.$banesto_adr.'" method="post" id="banesto_payment_form">
				' . implode('', $banesto_args_array) . '
                <p class="clearfix">
                <a class="tag great black" href="'.esc_url($order->get_cancel_order_url()).'">'.__('Cancel order &amp; restore cart', 'jigoshop').'</a>
				<button type="submit" class="button great" id="submit_banesto_payment_form">'.__('Pay via Banesto', 'jigoshop').'</button>
                </p>
			</form>';
            
        /*
		<script type="text/javascript">
			jQuery(function(){
				jQuery("body").block(
					{
						message: "<img src=\"'.jigoshop::assets_url().'/assets/images/ajax-loader.gif\" alt=\"Redirecting...\" />'.__('Thank you for your order. We are now redirecting you to Banesto to make payment.', 'jigoshop').'",
						overlayCSS:
						{
							background: "#fff",
							opacity: 0.6
						},
						css: {
							padding:		20,
							textAlign:	  "center",
							color:		  "#555",
							border:		 "3px solid #aaa",
							backgroundColor:"#fff",
							cursor:		 "wait"
						}
					});
				jQuery("#submit_banesto_payment_form").click();
			});
		</script>
        */
	}
    
    
    
	/**
	 * Process the payment and return the result
	 **/
    function tvp_message($code) {
        
        switch ($code) {
            
            case 01180:
                $message = 'Tarjeta inválida';
                break;
                
            case 01190:
                $message = 'Denegada (normalmente disponible insuficiente)';
                break;
                
            case 01101:
                $message = 'Denegada, tarjeta caducada';
                break;
                
            case 01202:
                $message = 'Tarjeta en lista negra';
                break;
            
            default:
                $message = 'Caducidad incorrecta, CVV2 incorrecto, Límites de operativa.';
                break;
        }
        
        return $message;
    }
    
    

	/**
	 * Process the payment and return the result
	 **/
	function process_payment( $order_id ) {

		$order = new jigoshop_order( $order_id );

		return array(
			'result' 	=> 'success',
			'redirect'	=> add_query_arg('order', $order->id, add_query_arg('key', $order->order_key, get_permalink(jigoshop_get_page_id('pay'))))
		);
	}
    
    
    
	/**
	 * receipt_page
	 **/
	function receipt_page( $order ) {
        
		echo '<p>'.__('Thank you for your order, please click the button below to pay with Banesto.', 'jigoshop').'</p>';
		echo $this->generate_banesto_form( $order );
	}
    
    
    
	/**
	 * Check for Banesto TPV Response
	 **/
	function check_tpv_response() {

		if (isset($_GET['banestolistener']) && $_GET['banestolistener'] == 'banesto_standard_tpv'):

			$_POST = stripslashes_deep($_POST);
            
            $this->successful_request($_POST);

	   	endif;

	}


	/**
	 * Successful Payment!
	 **/
	function successful_request( $posted ) {

		// Custom holds post ID
		if ( !empty($posted['referencia']) && !empty($posted['RESULTADO']) ) {
            
            global $wpdb;
            
            $order_id = $wpdb->get_var("SELECT post_id FROM $wpdb->postmeta WHERE meta_key = 'order_key' AND meta_value = '".$posted['referencia']."'");
                
            if (!$order_id) exit;

			$order = new jigoshop_order( $order_id );

			if ($order->status !== 'completed') :
				// We are here so lets check status and do actions
				switch ($posted['RESULTADO']) :
					case 'OK' :
						// Payment completed
						$order->add_order_note( __('Banesto TPV payment completed', 'jigoshop') );
						unset( jigoshop_session::instance()->order_awaiting_payment );
                        $order->update_status('completed');
                        do_action( 'jigoshop_payment_complete', $order_id );
					break;
					case 'KO' :
                        $order->cancel_order( __('Banesto TPV denied the payment.', 'jigoshop') );
					break;
					default:
					    // Do nothing
					break;
				endswitch;
			endif;

			exit;

		}
	}
    
    public function process_gateway($subtotal, $shipping_total, $discount = 0) {
        
        $ret_val = false;
        if (!(isset($subtotal) && isset($shipping_total))) return $ret_val;
        
        // check for free (which is the sum of all products and shipping = 0) Tax doesn't count unless prices
        // include tax
        if (($subtotal <= 0 && $shipping_total <= 0) || (($subtotal + $shipping_total) - $discount) == 0) :
            // true when force payment = 'yes'
            $ret_val = ($this->force_payment == 'yes');
        elseif(($subtotal + $shipping_total) - $discount < 0) :
            // don't process paypal if the sum of the product prices and shipping total is less than the discount
            // as it cannot handle this scenario
            $ret_val = false;
        else :
            $ret_val = true;
        endif;
        
        return $ret_val;
        
    }

}
