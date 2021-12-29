<?php

/**
* Plugin Name: Remove From WishList
* Plugin URI: https://www.eurekatop.com
* Description: This plugin does some stuff with WordPress
* Version: 0.0.1
* Author: rfranr
* Author URI: https://github.com/rfranr
* License: GPL2
*/

add_action( 'woocommerce_add_to_cart', 'wishlist_woocommerce_add_to_cart' );
add_action( 'woocommerce_payment_complete', 'rudr_complete' );
add_action( 'woocommerce_payment_complete_order_status_processing', 'rudr_complete_for_status' );
add_action( 'woocommerce_payment_complete_order_status_completed', 'rudr_complete_for_status' );


function wishlist_woocommerce_add_to_cart() {
  echo ''; //test
}


function rudr_complete( $order_id ) {
	
	//TODO: how to get wishlist id from order ?

	$order = wc_get_order( $order_id );
	// do anything
	
}


function rudr_complete_for_status( $order_id ){
	
	// do anything
	
}
?>