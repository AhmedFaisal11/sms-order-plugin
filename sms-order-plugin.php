<?php

/**
 * Plugin Name: sms-notifier-plugin
 * Author: Ahmed Faisal
 * Description: Woocomerce plugin for sending sms on order status change 
 * Version: 1.0.2
 */

// Event of a new Email for the order - change in status
// Event of the order note 

add_action( 'woocommerce_order_status_changed', 'thecodifica_send_sms_on_new_order_status' , 10 ,4);
add_action( 'woocommerce_order_status_changed', 'thecodifica_send_sms_to_admin' , 10 ,4);


add_action( 'woocommerce_new_customer_note_notification' , 'thecodifica_send_sms_on_new_order_note' , 10 , 1);



// this method will call an external api for sending message

function send_sms_to_customer($phone, $defaultmessage){
    if('NULL' === $phone){
        return;
    }

    // $url = 'https://brandyourtext.com/sms/api/send?username=ezshifa&password=C3@cu.w7~9FfO1-N&mask=EZSHIFA&mobile='.$phone.'&message'.$defaultmessage;

    $url = 'https://brandyourtext.com/sms/api/send?username=ezshifa&password=C3@cu.w7~9FfO1-N&mask=EZSHIFA&mobile='.$phone.'&message='.$defaultmessage;

    $arguments =array(
        'method' => 'get',
    );

    $response = wp_remote_get( $url, $arguments );

    // echo "console.log('$response')";

    if(is_wp_error( $response )){
        $error_message = $response->get_error_message();
        return "Something went wrong: $error_message";
    }
}


// function for sending sms on status changed

function thecodifica_send_sms_on_new_order_status($order_id , $old_status , $new_status , $order){
    $my_order = wc_get_order($order_id);
    $orderId = $my_order->get_order_number();

    $first_name = $my_order->get_billing_first_name();

    $today = date("D M j G:i:s T Y");
    $phone_number = $my_order->get_billing_phone();

    // $shop_name = get_option( 'woocommerce_email_from_name' );

    if($my_order->status == 'processing'){
        $default_message = "Thank you $first_name for shopping with EZPharmacy. Your order number $orderId is in processing";
    }elseif($my_order->status == 'on-hold'){
        $default_message = "Thank you for shopping, $first_name. Your order status has been set to on-hold. We'll get back to you shortly";
    }elseif($my_order->status == 'completed'){
        $default_message = "Thank you for shopping with us $first_name. Your Order $orderId has been set as completed. Make sure to give your review on our website! https://phar.ezshifa.com/";
    }elseif($my_order->status == 'shipped'){
        $default_message = "Thank you for shopping with us $first_name. Your order has been picked up by on $today . Your track code is ";
    }


    send_sms_to_customer($phone_number , $default_message );

}

// fucntion for sending sms on new order note

function thecodifica_send_sms_on_new_order_note($email_args){

    $order = wc_get_order($email_args['order_id']);
    $note = $email_args['customer_note'];

    $phone = $order->get_billing_phone();

    send_sms_to_customer($phone , $note);
}

function thecodifica_send_sms_to_admin($order_id , $old_status , $new_status , $order){
    $my_order = wc_get_order($order_id);

    $first_name = $my_order->get_billing_first_name();

    $new_first_name = $my_order->get_shipping_first_name();
    
    // $trackingnumber = '';

    $tracking_id = get_post_meta($my_order->get_order_number(), 'Tracking_ID', true);

    $phone_number = '03115380875';

    $shop_name = get_option( 'woocommerce_email_from_name' );

    if($my_order->status == 'processing'){
        $default_message = "Thank you $first_name for shopping with EZPharmacy. Your order number $order_id is in processing";
    }elseif($my_order->status == 'on-hold'){
        $default_message = "Thank you for shopping, $first_name. Your order status has been set to on-hold. We'll get back to you shortly";
    }elseif($my_order->status == 'completed'){
        $default_message = "Thank you for shopping with us $first_name. Your Order $order_id has been set as completed. Make sure to give your review on our website! https://phar.ezshifa.com/";
    }elseif($my_order->status == 'shipped'){
        $default_message = "Thank you for shopping with us $first_name. Your order has been picked up by Lepord on 21/05/2021 . Your track code is $tracking_id";
    }
    send_sms_to_customer($phone_number , $default_message );

}




    // $new_first_name = $my_order->get_shipping_first_name();
    
    // $trackingnumber = '';

    // $carrier_name = $my_order->get_meta('carrier_name');
    // $tracking_id = $my_order->get_order_number();

    // $customer = new WC_Customer();
    // $shippers     =  $customer->get_shipping();
	 // $wcot_shipper = get_post_meta( $order_id, 'wcot_shipper', true );
	// $wcot_number  = get_post_meta( $order_id, 'wcot_number', true );





?>