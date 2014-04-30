<?php
/*
Plugin Name: Jigoshop Banesto Gateway
Plugin URI: http://github.com/not-only-code/jigoshop-banesto-gateway
Description: Extends JigoShop providing a new gateway that works with Banesto banks
Version: 0.1
Author: Carlos Sanz García
Author URI: http://codingsomething.wordpress.com/
*/


/**
 * Adds news settings to Jigoshop
 *
 * @package		Jigoshop
 * @subpackage 	Jigoshop Banesto Gateway
 * @since 		0.1
 *
 **/
function jigoshop_banesto_access() {
    
    // gettext
    load_plugin_textdomain( 'jigoshop', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
    
	// dependeces
	$active_plugins_ = apply_filters( 'active_plugins', get_option( 'active_plugins' ) );
	if ( in_array( 'jigoshop/jigoshop.php', $active_plugins_ ) && JIGOSHOP_VERSION >= 1207160 ):
        
        include_once('jigoshop-banesto-gateway.class.php');
        
        add_filter( 'jigoshop_payment_gateways', 'add_banesto_gateway', 10 );
        
    else:

		if (is_admin())
            add_action( 'admin_notices', 'jigoshop_banesto_dependences');
        
    endif;
}
add_action('plugins_loaded', 'jigoshop_banesto_access');


 
/**
 * Add the gateway to JigoShop
 *
 * @package		Jigoshop
 * @subpackage 	Jigoshop Banesto Gateway
 * @since 		0.1
 *
**/
function add_banesto_gateway( $methods ) {
	$methods[] = 'banesto';
	return $methods;
}



/**
 * admin notice: depencendes
 *
 * @package		Jigoshop
 * @subpackage 	Jigoshop Banesto Gateway
 * @since 		0.1
 *
**/
function jigoshop_banesto_dependences() {
	global $current_screen;
		
    echo "<div class=\"error\">" . PHP_EOL;
	echo "<p><strong>Jigoshop Banesto Gateway:</strong></p>" . PHP_EOL;
	echo "<p>" . __('This plugin requires at least <strong>Jigoshop 1.3</strong> active.', 'jigoshop') . "</p>" . PHP_EOL;
    echo "</div>" . PHP_EOL;
}