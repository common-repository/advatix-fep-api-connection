<?php 
/** 
 *
 * @author Advatix
 * @copyright 2024 Advatix 
 * @license GPL-2.5.15-or-later 
 * 
 * Plugin Name: Advatix FEP API Connection 
 * Description: This plugin works with advatix fep API and it allows to send order details and receive order response.
 * Version: 2.5.15
 * Author: Advatix
 * Author URI: https://advatix.com/ 
 * Text Domain: advatix-fep
 * License: GPL v2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.5.15.txt 
 * 
 * */

/**
 * Check if WooCommerce is active
 */
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {

	include(WP_PLUGIN_DIR .'/'. plugin_basename( dirname(__FILE__) ) .'/includes/classes/adv-main.php');
	include(WP_PLUGIN_DIR .'/'. plugin_basename( dirname(__FILE__) ) .'/includes/functions/plugin-functions.php');
	include(WP_PLUGIN_DIR .'/'. plugin_basename( dirname(__FILE__) ) .'/includes/functions/ajax-functions.php');
	include(WP_PLUGIN_DIR .'/'. plugin_basename( dirname(__FILE__) ) .'/includes/functions/meta-boxes.php');
	include(WP_PLUGIN_DIR .'/'. plugin_basename( dirname(__FILE__) ) .'/includes/functions/wc-statuses.php');
	include(WP_PLUGIN_DIR .'/'. plugin_basename( dirname(__FILE__) ) .'/includes/functions/custom-schedules.php');
	include(WP_PLUGIN_DIR .'/'. plugin_basename( dirname(__FILE__) ) .'/includes/functions/scheduler.php');
	include(WP_PLUGIN_DIR .'/'. plugin_basename( dirname(__FILE__) ) .'/includes/functions/rest-templates.php');
	include(WP_PLUGIN_DIR .'/'. plugin_basename( dirname(__FILE__) ) .'/includes/functions/order-hooks.php');
	
	new ADVATIX_FEP_PLUGIN();

	// Helper function to use in your theme to return a theme option value
	function advatix_api_option( $id = '' ) {
		return ADVATIX_FEP_PLUGIN::get_theme_option( $id );
	}

	// Helper function to return order data for fep api
	function advatix_fep_order_data( $order_id = '' ) {
		return ADVATIX_FEP_PLUGIN::get_fep_order_data( $order_id );
	}
	
	// Helper function to return order data for omni api
	function advatix_omni_order_data( $order_id = '' ) {
		return ADVATIX_FEP_PLUGIN::get_omni_order_data( $order_id );
	}

}
?>
