<?php
/**
 * Plugin Name: SM - Order Status Updater for WoOCommerce
 * Plugin URI: https://smartystudio.net
 * Description: Updates order statuses in WooCommerce based on updates from an external Laravel system.
 * Version: 1.0.0
 * Author:      Smarty Studio | Martin Nestorov
 * Author URI:  https://smartystudio.net
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
	die;
}

// Define your secret token here. Make sure this is a strong, unique value.
define( 'SMARTY_ORDER_STATUS_UPDATER_SECRET_TOKEN', 'your_secret_token_here' );

add_action( 'rest_api_init', function () {
    register_rest_route( 'smarty-order-status-updater/v1', '/update-status/', array(
        'methods' => 'POST',
        'callback' => 'smarty_order_status_updater_callback',
        'permission_callback' => 'smarty_order_status_updater_permissions_check'
    ) );
} );

if (!function_exists('smarty_order_status_updater_permissions_check')) {
    function smarty_order_status_updater_permissions_check( WP_REST_Request $request ) {
        $token = $request->get_header( 'x-auth-token' );

        if ( $token !== WOOCOMMERCE_ORDER_STATUS_UPDATER_SECRET_TOKEN ) {
            return new WP_Error( 'forbidden_access', 'Access denied', array( 'status' => 403 ) );
        }

        return true;
    }
}

if (!function_exists('smarty_order_status_updater_callback')) {
    function smarty_order_status_updater_callback( WP_REST_Request $request ) {
        $params = $request->get_json_params();
        $order_id = $params['order_id'] ?? '';
        $new_status = $params['status'] ?? '';

        if ( empty( $order_id ) || empty( $new_status ) ) {
            return new WP_Error( 'missing_parameters', 'Missing order ID or new status.', array( 'status' => 400 ) );
        }

        $order = wc_get_order( $order_id );
        if ( ! $order ) {
            return new WP_Error( 'invalid_order', 'Order not found.', array( 'status' => 404 ) );
        }

        // Assuming 'status' is a valid WooCommerce status
        $order->update_status( $new_status, 'Order status updated from external system.', true );

        return new WP_REST_Response( array( 'success' => true, 'message' => 'Order status updated.' ), 200 );
    }
}
