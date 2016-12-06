<?php
/**
 * Plugin Name: Update Cart Items in Woocommerce
 * Plugin URI: https://github.com/Delgesh/Update-Cart-items-in-Woocommerce
 * Description: Update Cart items in Woocommerce using Sessions
 * Version: 1.0
 * Author: Delgesh Shahab
 * Author URI: https://github.com/Delgesh/
 * License: GPL2
 */

/*  Copyright 2016  Delgesh Shahab.  (email : delgeshshahab@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software

*/

# Update-Cart-items-in-Woocommerce

//Step 1: Add Data in a Custom Session, on ‘Add to Cart’ Button Click

//For those of you who have worked with WooCommerce might know that on the click of the ‘Add to Cart’ button the product page gets refreshed and the user data is lost. Hence, we should add the custom data from our product page to a custom session created using Ajax. This code is invoked before the WooCommerce session is created.


add_action('wp_ajax_del_add_user_custom_data_options', 'del_add_user_custom_data_options_callback');
add_action('wp_ajax_nopriv_del_add_user_custom_data_options', 'del_add_user_custom_data_options_callback');

function del_add_user_custom_data_options_callback()
{
    //Custom data - Sent Via AJAX post method
    $product_id = $_POST['id']; //This is product ID
    $user_custom_data_values =  $_POST['user_data']; //This is User custom value sent via AJAX
    session_start();
    $_SESSION['del_user_custom_data'] = $user_custom_data_values;
    die();
}
//Step 2: Add Custom Data in WooCommerce Session

//At this step, the WooCommerce session has been created and is now available for us to add our custom data. We use the following code to add the custom data from the session we have created into the WooCommerce session. At this step, our session is also unset since the data in it has been captured and it is not needed anymore.

add_filter('woocommerce_add_cart_item_data','del_add_item_data',1,2);

if(!function_exists('del_add_item_data'))
{
    function del_add_item_data($cart_item_data,$product_id)
    {
        /*Here, We are adding item in WooCommerce session with, del_user_custom_data_value name*/
        global $woocommerce;
        session_start();
        if (isset($_SESSION['del_user_custom_data'])) {
            $option = $_SESSION['del_user_custom_data'];
            $new_value = array('del_user_custom_data_value' => $option);
        }
        if(empty($option))
            return $cart_item_data;
        else
        {
            if(empty($cart_item_data))
                return $new_value;
            else
                return array_merge($cart_item_data,$new_value);
        }
        unset($_SESSION['del_user_custom_data']);
        //Unset our custom session variable, as it is no longer needed.
    }
}
//Step 3: Extract Custom Data from WooCommerce Session and Insert it into Cart Object

//At this stage, we have default product details along with the custom data in the WooCommerce session. The default data gets added to the cart object owing to the functionality provided by the plugin. However, we need to explicitly extract the custom data from the WooCommerce session and insert it into the cart object. This can be implemented with the following code.

add_filter('woocommerce_get_cart_item_from_session', 'del_get_cart_items_from_session', 1, 3 );
if(!function_exists('del_get_cart_items_from_session'))
{
    function del_get_cart_items_from_session($item,$values,$key)
    {
        if (array_key_exists( 'del_user_custom_data_value', $values ) )
        {
            $item['del_user_custom_data_value'] = $values['del_user_custom_data_value'];
        }
        return $item;
    }
}
//Step 4: Display User Custom Data on Cart and Checkout page

//Now that we have our custom data in the cart object all we need to do now is to display this data in the Cart and the Checkout page. This is how your cart page should look after the custom data has been added from the WooCommerce session to your Cart. My-Cart-Page
