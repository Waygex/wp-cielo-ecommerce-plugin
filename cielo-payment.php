<?php

/**
 * Plugin Name: Cielo eCommerce 3.0 Gateway
 * Plugin URI: https://waygex.com/cielo-ecommerce-wp-plugin
 * Description: Gateway de pagamento integrado à API Cielo eCommerce 3.0 - suporta Cartão de Crédito e Pix
 * Version: 1.1.1
 * Author: Waygex Solutions
 * Author URI: https://waygex.com
 * Text Domain: cielo-ecommerce
 * Domain Path: /languages
 * Requires at least: 5.0
 * Tested up to: 6.4
 * PHP Version: 8.3
 * WC requires at least: 3.0
 * WC tested up to: 8.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Main plugin initialization
 */
add_action('plugins_loaded', 'cielo_ecommerce_init', 11);

function cielo_ecommerce_init()
{
    if (!class_exists('WC_Payment_Gateway')) {
        return;
    }

    // Load webhook routes
    // require_once plugin_dir_path(__FILE__) . 'includes/webhook/class-wc-cielo-pix-webhook.php';

    // Load payment gateway classes
    require_once plugin_dir_path(__FILE__) . 'includes/class-wc-cielo-credit-card-gateway.php';
    require_once plugin_dir_path(__FILE__) . 'includes/class-wc-cielo-pix-gateway.php';
}

/**
 * Add Cielo payment gateways to WooCommerce
 *
 * @param array $gateways
 * @return array
 */
function add_cielo_gateway_class($gateways)
{
    // Add Credit Card gateway
    $gateways[] = 'WC_Cielo_Credit_Card_Gateway';

    // Add Pix gateway
    $gateways[] = 'WC_Cielo_Pix_Gateway';

    return $gateways;
}
add_filter('woocommerce_payment_gateways', 'add_cielo_gateway_class');

/**
 * Register "Check Payment" custom order action
 */
add_filter('woocommerce_order_actions', 'cielo_register_check_payment_order_action');

function cielo_register_check_payment_order_action($actions)
{
    $actions['cielo_check_payment'] = __('Verificar Pagamento Pix (Cielo)', 'cielo-ecommerce');
    return $actions;
}

/**
 * Handle "Check Payment" custom order action
 */
add_action('woocommerce_order_action_cielo_check_payment', 'cielo_handle_check_payment_order_action');

function cielo_handle_check_payment_order_action($order)
{
    $gateway = new WC_Cielo_Pix_Gateway();
    $gateway->check_payment_by_order_id($order->get_id());
}

/**
 * Register cron schedule (every 5 minutes)
 */
add_filter('cron_schedules', 'cielo_add_cron_interval');

function cielo_add_cron_interval($schedules)
{
    if (!isset($schedules['every_5_minutes'])) {
        $schedules['every_5_minutes'] = array(
            'interval' => 300,
            'display'  => __('A cada 5 minutos', 'cielo-ecommerce'),
        );
    }
    return $schedules;
}

/**
 * Schedule cron on plugin load
 */
add_action('init', 'cielo_schedule_payment_check_cron');

function cielo_schedule_payment_check_cron()
{
    if (!wp_next_scheduled('cielo_check_pending_payments_cron')) {
        wp_schedule_event(time(), 'every_5_minutes', 'cielo_check_pending_payments_cron');
    }
}

/**
 * Clear cron on plugin deactivation
 */
register_deactivation_hook(__FILE__, 'cielo_clear_payment_check_cron');

function cielo_clear_payment_check_cron()
{
    $timestamp = wp_next_scheduled('cielo_check_pending_payments_cron');
    if ($timestamp) {
        wp_unschedule_event($timestamp, 'cielo_check_pending_payments_cron');
    }
}

/**
 * Cron job: check payment status for all pending Cielo Pix orders
 */
add_action('cielo_check_pending_payments_cron', 'cielo_process_pending_pix_orders');

function cielo_process_pending_pix_orders()
{
    $orders = wc_get_orders(array(
        'payment_method' => 'cielo_pix',
        'status'         => array('wc-on-hold', 'wc-pending'),
        'limit'          => 50,
        'orderby'        => 'date',
        'order'          => 'ASC',
    ));

    if (empty($orders)) {
        return;
    }

    $gateway = new WC_Cielo_Pix_Gateway();

    foreach ($orders as $order) {
        $gateway->check_payment_by_order_id($order->get_id());
    }
}
