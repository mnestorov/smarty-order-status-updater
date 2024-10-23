<?php
/**
 * Plugin Name: SM - Order Status Updater for WooCommerce
 * Plugin URI:  https://github.com/mnestorov/smarty-order-status-updater
 * Description: Updates order statuses in WooCommerce based on updates from an external Laravel system.
 * Version:     1.0.0
 * Author:      Smarty Studio | Martin Nestorov
 * Author URI:  https://github.com/mnestorov
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
	die;
}

// Define your secret token here. Make sure this is a strong, unique value.
define('SMARTY_ORDER_STATUS_UPDATER_SECRET_TOKEN', 'your_secret_key');

// Register REST API route
add_action('rest_api_init', function () {
    register_rest_route('smarty-order-status-updater/v1', '/update-status/', array(
        'methods' => 'POST',
        'callback' => 'smarty_order_status_updater_callback',
        'permission_callback' => 'smarty_order_status_updater_permissions_check'
    ));
});

if (!function_exists('smarty_order_status_updater_permissions_check')) {
    /**
     * Checks if the request to the REST API endpoint is authorized.
     * 
     * This function validates the incoming request by checking for a specific
     * 'x-auth-token' header. It ensures that the request contains a valid token
     * that matches the predefined secret token. If the token does not match,
     * the function denies access by returning a WP_Error object with a 403 status code.
     * 
     * @param WP_REST_Request $request The request object containing headers sent to the endpoint.
     * @return true|WP_Error Returns true if the token is valid, otherwise returns WP_Error object for unauthorized access.
     */
    function smarty_order_status_updater_permissions_check(WP_REST_Request $request) {
        $token = $request->get_header('x-auth-token');
        //$token = $request->get_param('secret'); // Getting token from URL query parameter

        if ($token !== SMARTY_ORDER_STATUS_UPDATER_SECRET_TOKEN) {
            return new WP_Error('forbidden_access', 'Access denied', array('new_status' => 403));
        }

        return true;
    }
}

if (!function_exists('smarty_order_status_updater_callback')) {
    /**
     * Handles the order status update request from an external system.
     * 
     * This function processes the incoming POST request containing the order ID
     * and the new order status. It first validates the presence of required parameters.
     * If any parameter is missing, it returns a WP_Error object with a 400 status code.
     * Then, it attempts to retrieve the order by ID and update its status to the new value.
     * If the order cannot be found, it returns a WP_Error object with a 404 status code.
     * 
     * @param WP_REST_Request $request The request object containing JSON parameters sent to the endpoint.
     * @return WP_REST_Response|WP_Error Returns WP_REST_Response object if the order status is successfully updated, otherwise returns WP_Error object.
     */
    function smarty_order_status_updater_callback(WP_REST_Request $request) {
        $params = $request->get_json_params();
        $order_id = $params['order_id'] ?? '';
        $new_status = $params['new_status'] ?? '';

        if (empty($order_id) || empty($new_status)) {
            return new WP_Error('missing_parameters', 'Missing order ID or new status.', array('new_status' => 400));
        }

        $order = wc_get_order($order_id);
        if (!$order) {
            return new WP_Error('invalid_order', 'Order not found.', array('new_status' => 404));
        }

        // Assuming 'status' is a valid WooCommerce status
        $order->update_status($new_status, 'Order status updated from external system.', true);

        return new WP_REST_Response(array('success' => true, 'message' => 'Order status updated.'), 200);
    }
}

if (!function_exists('smarty_order_status_updater_activate')) {
    /**
     * Flush rewrite rules on plugin activation to ensure our custom endpoint is available.
     */
    function smarty_order_status_updater_activate() {
        flush_rewrite_rules();
    }
    register_activation_hook(__FILE__, 'smarty_order_status_updater_activate');
}

if (!function_exists('smarty_order_status_updater_deactivate')) {
    /**
     * Flush rewrite rules on plugin deactivation.
     */
    function smarty_order_status_updater_deactivate() {
        flush_rewrite_rules();
    }
    register_deactivation_hook(__FILE__, 'smarty_order_status_updater_deactivate');
}
