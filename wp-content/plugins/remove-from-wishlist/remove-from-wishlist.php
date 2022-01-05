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
//add_action( 'woocommerce_payment_complete_order_status_processing', 'rudr_complete_for_status' );
//add_action( 'woocommerce_payment_complete_order_status_completed', 'rudr_complete_for_status' );
add_action( 'woocommerce_payment_complete', 'wishlist_woocommerce_payment_complete' );
add_filter( 'woocommerce_add_cart_item_data', 'wishlist_woocommerce_add_wishlist_id__to_cart_item', 10, 3 );
add_action( 'woocommerce_add_order_item_meta','wishlist_woocommerce_add_values_to_order_item_meta',1,2);
//add_action( 'woocommerce_order_status_cancelled', 'wishlist_woocommerce_payment_complete_status_cancelled' );


/* Log to File
 */
function wishlist_woocommerce_log_to_file( )
{
    $error_dir = '/home/ey4736zo/logs/wishlist_test.log';
    
    error_log( "-------------------- \n", 3, $error_dir );
    
    // TODO: is a added to cart with wishlist data ??
    $msgpost = print_r( $_POST, true );
    $log = "test";
    error_log( $msgpost . "\n", 3, $error_dir );
    error_log ( "wishlist_id / " . $_POST['wishlist_id'] . "\n", 3, $error_dir);
    
    // --
    $msgcart = print_r( WC()->cart->cart_contents,true );
    error_log( $msgcart . "\n", 3, $error_dir );


    //
    $wishlist = YITH_WCWL_Wishlist_Factory::get_wishlist( $_POST['wishlist_id'] );
    error_log( "- GET TOKEN ------------------- \n", 3, $error_dir );
    error_log ( print_r( $wishlist, true ),  3, $error_dir);
    error_log ( $wishlist->get_token(),  3, $error_dir);
    
}



function wishlist_woocommerce_add_to_cart() {
  echo ''; //test
  wishlist_woocommerce_log_to_file ();
}


/**
 * Add wishlist_id to cart item.
 * 
 * Añadimos al carrito el id de la wishlist.
 *
 * @param array $cart_item_data
 * @param int   $product_id
 * @param int   $variation_id
 *
 * @return array
 */
function wishlist_woocommerce_add_wishlist_id__to_cart_item( $cart_item_data, $product_id, $variation_id ) {
	$wishlist_id = filter_input( INPUT_POST, 'wishlist_id' );

	if ( empty( $wishlist_id ) ) {
		return $cart_item_data;
	}

	$cart_item_data['wishlist_id'] = $wishlist_id;

	return $cart_item_data;
}


/**
 * Add wishlist metadata to line orders
 *
 * @param int   $item_id
 * @param array $values
 *
 */
function wishlist_woocommerce_add_values_to_order_item_meta($item_id, $values) {
    //global $woocommerce,$wpdb;
    
	if ( array_key_exists( 'wishlist_id', $values) ) {
	    $wishlist_id = $values['wishlist_id'];
		$wishlist = YITH_WCWL_Wishlist_Factory::get_wishlist( $wishlist_id );
	    wc_add_order_item_meta($item_id,'wishlist_id',$values['wishlist_id']);
  		wc_add_order_item_meta($item_id,'wishlist_token', $wishlist->get_token() );
	}

}


/**
 * Delete product from wishlist
 *
 * @param int   $order_id
 *
 */
function wishlist_woocommerce_payment_complete( $order_id ) {
	$error_dir = '/home/ey4736zo/logs/wishlist_test.log'; 
    error_log( "\n FIN Borrar producto de la wishlist  -------------------- \n", 3, $error_dir );

	// obtenemos el número de orden 
	$order = wc_get_order( $order_id );

	// iteramos sobre la orden de compra y recuperamos sus items
	foreach ( $order->get_items() as $item_id => $item ) {
	   /*
	   $variation_id = $item->get_variation_id();
	   $product = $item->get_product();
	   $quantity = $item->get_quantity();
	   $subtotal = $item->get_subtotal();
	   $total = $item->get_total();
	   $tax = $item->get_subtotal_tax();
	   $taxclass = $item->get_tax_class();
	   $taxstat = $item->get_tax_status();
	   $allmeta = $item->get_meta_data();
	   */
	   $product_id = $item->get_product_id();
	   $product_name = $item->get_name();
	   
	   // obtenemos el objeto wihslist a traves del parámetro guardado en los metadatos del carrito
	   $wishlist_id = $item->get_meta( 'wishlist_id', true );
	   $wishlist = YITH_WCWL_Wishlist_Factory::get_wishlist( $wishlist_id );

		if( $product_id != false ){
		   error_log( "\n Booramos el producto  ". $product_id . " de la wishlist " .$wishlist_id. " \n", 3, $error_dir );
		   
		   // obtenemos el usuario creador de la wishlist
		   $wishlist->get_user_id();

		   // borramos el producto de la wishlist
		   $wishlist->remove_product( $product_id );
		   $wishlist->save();
		   wp_cache_delete( 'wishlist-count-' . $wishlist->get_token(), 'wishlists' );
		}

	   /*	YITH_WCWL()->remove(  
			array(
					'remove_from_wishlist' => $product_id,
					'wishlist_id' => $wishlist_id,
					'user_id' => false
				)
		);*/

		error_log( "product_id:" . $product_id, 3, $error_dir );
		error_log( ";product_name:" . $product_name, 3, $error_dir );
		error_log( ";wishlist_id:" . $wishlist_id, 3, $error_dir );
	    error_log( "\n FIN Borrar producto de la wishlist  -------------------- \n", 3, $error_dir );
	}
}


//function wishlist_woocommerce_payment_complete_status_cancelled( $order_id ){
//}


?>