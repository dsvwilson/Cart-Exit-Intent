<?php

/*
 * Plugin Name:       Cart Exit Intent
 * Description:       Adds dynamic cart data to OptinMonster Cart Exit campaigns and aids the data to be sent to AWeber.
 * Version:           1.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            National Financial Educators Council
 * Author URI:        https://www.financialeducatorscouncil.org/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 */


 if ( ! defined( 'ABSPATH' ) ) {
    die; // SECURITY - kill the operation if accessed directly
}

/*
----------------------------------------------------------------------------
NOTE - function names are prefixed with "cei" - the combination of the first letters of the plugin name.
----------------------------------------------------------------------------
*/



// gets relevant data of the product in cart (presently just the first product in cart) and stores it in the array $product_data which is returned by the function to be passed on to AWeber.

function cei_get_product_data() {
    foreach ( WC()->cart->get_cart() as $cart_item ) {
       $product_title = $cart_item['data']->get_title();
       $product_price = $cart_item['data']->get_price();
       $product_id = $cart_item['data']->get_id();

       $product_data = array(
        'Product Name' => $product_title,
        'Product Price' => '$' . $product_price . '.00',
        'Product ID' => $product_id
       );

       return $product_data;
    }
}

/*
defines a function that does a few things:
    1.  enqueues the /script.js file and assigns the handle "connect-script-js" (line 50)
    2.  calls the cei_get_product_data() function and saves it in the $product_data_arr variable (line 62)
    3.  defines an array for the data to be passed to the wp_localize_script function (lines 51-54)
    4.  calls the wp_localize_script function and passes in relevant information. The purpose of calling this function is to be able to use our PHP functions in our /script.js file (line 55).

    JavaScript Objects created using wp_localize_script:

    'URL' => the WordPress-generated URL to the /script.js file
    'getProductName' => the name of the product in cart
    'getProductID' => the unique ID of the product 
    'getProductPrice' => the price of the product
    'getCartTotal' => the total amount in the cart
*/

function cei_connect_script_js() {
    wp_enqueue_script( 'connect-script-js', plugins_url( '/script.js', __FILE__ ), array(), '1.0.0', true );
    wp_enqueue_style( 'connect-style-css', plugins_url( '/style.css', __FILE__ ), array(), '1.0.0' );
    $product_data_arr = cei_get_product_data(); // calling the function and saving the returned array data in a variable

    $csj_args = array (
        'url' => plugins_url( '/script.js', __FILE__),
        'adminURL' => admin_url( 'admin-ajax.php' ),
        'getProductName' => $product_data_arr[ 'Product Name' ],
        'getProductPrice' => $product_data_arr[ 'Product Price' ],
        'getProductID' => $product_data_arr[ 'Product ID' ],
        'getCartTotal' => '$' . WC()->cart->total
    );
    wp_localize_script( 'connect-script-js', 'connectJSFile', $csj_args );
}

// hooks the connect_script_js handle to WordPress

add_action( 'wp_enqueue_scripts', 'cei_connect_script_js' );


function add_to_cart_first_empty_cart() {
    
    $current_url = $_SERVER['REQUEST_URI'];

    if ( str_contains( $current_url, 'add-to-cart' ) && ! WC()->cart->is_empty() ) {
        WC()->cart->empty_cart();
    }
}

add_action( 'wp_loaded', 'add_to_cart_first_empty_cart' );


?>

