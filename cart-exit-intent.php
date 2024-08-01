<?php

/*
 * Plugin Name:       Cart Exit Intent
 * Description:       Adds dynamic cart data to OptinMonster Cart Exit campaigns and aids the data to be sent to AWeber.
 * Version:           1.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            National Financial Educators Council
 * Author URI:        https://www.financialeducatorscouncil.org/
 */


 if ( ! defined( 'ABSPATH' ) ) {
    die; // SECURITY - kill the operation if accessed directly
}



/*
    ----------------------------------------------------------------------------
    NOTE - all function and variable names are prefixed with "cei" - the 
    combination of the first letters of the plugin name.
    ----------------------------------------------------------------------------
*/



// Wrapping the file in 'cei_main()' to hook it to 'woocommerce_init'.

add_action( 'woocommerce_init', 'cei_main' );

function cei_main() {   // Start of code



    // hooking the plugin files that will eventually be localised to WP 'init'

    add_action( 'init', 'cei_plugin_url' );
    
    

    /* 
        define the function that holds and returns an array of links to plugin 
        files using 'plugins_url()'
    */

    function cei_plugin_url() {
        $cei_plugin_url = array (
            'getScriptJs' => plugins_url( '/public/script.js', __FILE__ ),
            'getStyleCss' => plugins_url( '/public/style.css', __FILE__ )
        );

        return $cei_plugin_url;
    }



    // hooks the 'cei_connect-files()' to WordPress for enqueuing

    add_action( 'wp_enqueue_scripts', 'cei_connect_files' );



    /*

    */

    function cei_connect_files() {

        // calls 'cei_plugin_url()' and saves it in a variable. 'cei_plugin_url_arr' now
        // holds an associative array of links to plugin files as defined by the function.

        $cei_plugin_url_arr = cei_plugin_url();


        // enqueues the script and style files

        wp_enqueue_script( 'connect-script-js', $cei_plugin_url_arr['getScriptJs'], array(), '1.0.0', true );
        wp_enqueue_style( 'connect-style-css', $cei_plugin_url_arr['getStyleCss'], array(), '1.0.0' );

    }


    /* 
        'cei_get_product_data()' gets relevant data of the product in cart 
        (presently just the first product in cart) and stores it in the array 
        $product_data which is returned by the function to be passed on to AWeber.
    */

    function cei_get_product_data() {

        // loops through each item in the cart, saves each cart item (which is an 
        // associative array) to the 'cart_item' variable, fetches the value of
        // the 'data' key and stores it to the 'product" variable.

        foreach ( WC()->cart->get_cart() as $cei_cart_item ) {
            $cei_product = $cei_cart_item[ 'data' ];


            // checks if the product in the 'product" variable is a subscription
            // and if true, then it gets the sign up fee through the 'get_meta()'
            // method of the product object. The sign up fee is saved to the 
            // 'product_sign_up_fee' variable.

            if ( $cei_product->is_type( 'subscription' ) ) {
                $cei_product_sign_up_fee = $cei_product->get_meta( '_subscription_sign_up_fee', true );
            }


            // gets the name, price and ID of the product through the 'get_title()',
            // 'get_price()', and 'get_id()' methods of the product object respectively.
            // Then it stores the data in the 'product_title', 'product_price', and 
            // 'product_id' variables respecively.  

            $cei_product_title = $cei_product->get_title();
            $cei_product_price = $cei_product->get_price();
            $cei_product_id = $cei_product->get_id();
            
            
            // the 'product_data' variable holds an associative array that accesses
            // the previously defined product data variables and links them to keys.

            $cei_product_data = array(
                'Product Name' => $cei_product_title,
                // uses the PHP 'strip_tags' to remove HTML markup returned by 'wc_price'
                'Product Price' => strip_tags( wc_price( $cei_product_price ) ),
                'Product ID' => $cei_product_id,
                // uses the PHP 'strip_tags' to remove HTML markup returned by 'wc_price
                'Sign Up Fee' => strip_tags( wc_price( $cei_product_sign_up_fee )),
            );  

            return $cei_product_data; // final output is an associative array of product data
        
        }
    }



    /*
        Localizing the 'public/script.js' file so as to create JavaScript objects 
        that can be used in the file to reference PHP data. 'cei_localize_script()'
        is used.


        JavaScript Objects created using wp_localize_script:

        'getProductName' => the name of the product in cart
        'getProductPrice' => the price of the product
        'getProductID' => the unique ID of the product 
        'getSignUpFee' => the sign up fee of the product (applicable to subscription products)
        'getCartTotal' => the total amount in the cart
    */

    function cei_localize_script() {

        // calling 'cei_get_product_data()' and saving it in a variable.
        // the 'product_data_arr' now holds the array of product data as earlier
        // defined by the function. 

        $cei_product_data_arr = cei_get_product_data(); 
        

        // saving the product data in the 'csj_args' associative array to be localized
        // and passed on to the '/public/script.js' file through the 'connect-script-js'
        // handle earlier defined during enqueuing.

        $csj_args = array (
            'getProductName' => $cei_product_data_arr['Product Name'],
            'getProductPrice' => $cei_product_data_arr['Product Price'],
            'getProductID' => $cei_product_data_arr['Product ID'],
            'getSignUpFee' => $cei_product_data_arr ['Sign Up Fee'],
            'getCartTotal' => strip_tags( wc_price( WC()->cart->total ) ), // introduces cart total 
        );


        // localizes the '/public/script.js' file and uses 'connectJSFile' as object name.

        wp_localize_script( 'connect-script-js', 'connectJSFile', $csj_args );
    }

    

    // hooks 'cei_add_to_cart_first_empty_cart()' to 'wp_loaded' to ensure it
    // executes after WordPress loads.

    add_action( 'wp_loaded', 'cei_add_to_cart_first_empty_cart' );


    /*
     'cei_add_to_cart_first_empty_cart()' checks if the user used the WooCommerce
     'add-to-cart' function link to directly add the product to cart. Since this
     is the link that will be sent as the cart link in the email from AWeber, we
     want to be sure that the cart is cleared and only has the product that their
     link contains. This prevents them from buying more than they intend to.
    */

    function cei_add_to_cart_first_empty_cart() {
        
        // fetches the current URL from the server environment and saves it
        // to a variable.

        $cei_current_url = $_SERVER[ 'REQUEST_URI' ];

        // checks if the URL contains the 'add-to-cart' string and also if 
        // the cart has items in it.

        if ( str_contains( $cei_current_url, 'add-to-cart' ) && ! WC()->cart->is_empty() ) {
            WC()->cart->empty_cart(); // if both conditions are true, the cart is emptied.
        }
    }

}