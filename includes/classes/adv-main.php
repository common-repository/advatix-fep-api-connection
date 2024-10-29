<?php
	if ( ! class_exists( 'ADVATIX_FEP_PLUGIN' ) ) {

		class ADVATIX_FEP_PLUGIN {

			/**
			 * Start things up
			 *
			 * @since 2.5.15
			 */
			public function __construct() {

				// We only need to register the admin panel on the back-end
				if ( is_admin() ) {
					add_action( 'admin_menu', array( 'ADVATIX_FEP_PLUGIN', 'add_admin_menu' ) );
					add_action( 'admin_init', array( 'ADVATIX_FEP_PLUGIN', 'register_settings' ) );
				}

			}

			/**
			 * Returns all theme options
			 *
			 * @since 2.5.15
			 */
			public static function get_theme_options() {
				return get_option( 'theme_options' );
			}

			/**
			 * Returns single theme option
			 *
			 * @since 2.5.15
			 */
			public static function get_theme_option( $id ) {
				$options = self::get_theme_options();
				if ( isset( $options[$id] ) ) {
					return $options[$id];
				}
			}

			/**
			 * Returns order array data for fep
			 *
			 * @since 2.5.15
			 */
			public static function get_fep_order_data( $order_id ) {
				$order = wc_get_order( $order_id );
				$user      = $order->get_user(); // Get the WP_User object
				$order_total = $order->get_total();
				$payment_method = $order->get_payment_method(); // Get the payment method ID
				$payment_title = $order->get_payment_method_title(); // Get the payment method title
				$date_created  = date('Y-m-d H:i:s'); // Get date created (WC_DateTime object)
				$date_created = "$date_created";
				
				$items = array();
				foreach($order->get_items() as $k=>$v){
					$product = wc_get_product( $v->get_product_id() );
					
					$sku = get_post_meta( $v->get_variation_id(), '_sku', true );
					
					if(empty($sku)){
						$sku = $product->get_sku();
					}
					
					// $orderItems[] = array(
										// 'sku' => $sku,
										// 'quantity' => $v->get_quantity(),
										// // 'price' => $v->get_total(),
										// 'price' => $product->get_price(),
									// );
									
					$items[$sku] = array(
										'quantity' => $items[$sku]['quantity']+$v->get_quantity(),
										'price' => $product->get_price()
									);
				}
				
				$order_refunds = $order->get_refunds();
	
				$refunds = array();
				
				foreach( $order_refunds as $refund ){
					// Loop through the order refund line items
					foreach( $refund->get_items() as $item_id => $item ){

						$product = wc_get_product( $item->get_product_id() );
					
						$sku = get_post_meta( $item->get_variation_id(), '_sku', true );
						
						if(empty($sku)){
							$sku = $product->get_sku();
						}
						
						$refunds[$sku] = $refunds[$sku]+(abs($item->get_quantity()));
					}
				}
				
				// foreach($orderItems as $k=>$v){
					// $qt = $v['quantity']-$refunds[$v['sku']];
					// if($qt<1){
						// unset($orderItems[$k]);
					// }else{
						// $orderItems[$k]['quantity'] = $qt;
					// }
				// }

				foreach($items as $k=>$v){
					$qt = $v['quantity']-$refunds[$k];
					if($qt<1){
						unset($items[$k]);
					}else{
						$orderItems[] = array(
										'sku' => $k,
										'quantity' => $v['quantity']-$refunds[$k],
										'price' => $v['price'],
									);
					}
				}

				if(!empty($order->get_shipping_first_name())){
					$shipping_first_name  = $order->get_shipping_first_name();
				} else {
					$shipping_first_name  = "";
				}
				if(!empty($order->get_shipping_last_name())){
					$shipping_last_name  = $order->get_shipping_last_name();
				} else {
					$shipping_last_name  = "";
				}
				if(!empty($order->get_shipping_company())){
					$shipping_company  = $order->get_shipping_company();
				} else {
					$shipping_company  = "";
				}
				if(!empty($order->get_shipping_address_1())){
					$shipping_address_1  = $order->get_shipping_address_1();
				} else {
					$shipping_address_1  = "";
				}
				if(!empty($order->get_shipping_address_2())){
					$shipping_address_2  = $order->get_shipping_address_2();
				} else {
					$shipping_address_2  = "";
				}
				if(!empty($order->get_shipping_city())){
					$shipping_city  = $order->get_shipping_city();
				} else {
					$shipping_city  = "";
				}
				if(!empty($order->get_shipping_state())){
					$shipping_state  = $order->get_shipping_state();
				} else {
					$shipping_state  = "";
				}
				if(!empty($order->get_shipping_postcode())){
					$shipping_postcode  = $order->get_shipping_postcode();
				} else {
					$shipping_postcode  = "";
				}
				if(!empty($order->get_shipping_country())){
					$shipping_country  = $order->get_shipping_country();
				} else {
					$shipping_country  = "";
				}
				
				if($shipping_country == 'US'){
					$shipping_country = 'USA';
				}

				$billing_email     = $order->get_billing_email();
				$billing_phone     = $order->get_billing_phone();

				$billing_first_name  = $order->get_billing_first_name();
				$billing_last_name   = $order->get_billing_last_name();
				$billing_company     = $order->get_billing_company();
				$billing_address_1   = $order->get_billing_address_1();
				$billing_address_2   = $order->get_billing_address_2();
				$billing_city        = $order->get_billing_city();
				$billing_state       = $order->get_billing_state();
				$billing_postcode    = $order->get_billing_postcode();
				$billing_country     = $order->get_billing_country();

				if($billing_country == 'US'){
					$billing_country = 'USA';
				}
				
				$company_name = self::get_theme_option('company_name');
				$lob = self::get_theme_option('input_lob');
				
				if($lob == ''){
					$lob = 3;
				}
				
				$base_url = self::get_theme_option('input_api_url');
				$url = $base_url.'/order/createOrder';

				$accountID = self::get_theme_option('account_id');
				$api_key = self::get_theme_option('input_api_key');
				
				$order_id = "$order_id";
				$order_total = "$order_total";

				$url1 = explode('.',$_SERVER['SERVER_NAME']);
					
				if(count($url1) == 2){
					$prefix = $url1[0];
				}else if(count($url1) == 3){
					$prefix = $url1[1];
				}else if(count($url1) == 4){
					$prefix = $url1[1];
				}else if(count($url1) == 1){
					$prefix = $url1[0];
				}
				
				$prefix = substr($prefix, 0, 3);

				$data = array(
					"accountId" => $accountID,
					"referenceId" => $order_id,
					"orderNumber" => $prefix.'-'.$order_id,
					"orderType" => "6",
					"addressType" => "Residential",
					"shipToName" => $shipping_first_name.' '.$shipping_last_name,
					"shipToAddress" => $shipping_address_1,
					"shipToAddress2" => ($shipping_address_2 == '') ? "NA" : $shipping_address_2,
					"shipToCity" => $shipping_city,
					"shipToCountry" => $shipping_country,
					"shipToEmail" => $billing_email,
					"shipToMobile" => $billing_phone,
					"shipToState" => $shipping_state,
					"postalCode" => $shipping_postcode,
					"billToName" => $billing_first_name.' '.$billing_last_name,
					"billToAddress" => $billing_address_1,
					"billToAddress2" => ($billing_address_2 == '') ? "NA" : $billing_address_2,
					"billToCity" => $billing_city,
					"billToState" => $billing_state,
					"billToPostal" => $billing_postcode,
					"billToCountry" => $billing_country,
					"billToMobile" => $billing_phone,
					"billToEmail" => $billing_email,
					"addtionalCharges" => 0,
					"paymentMode" => ($payment_method == 'cod') ? 1 : 2,
					"paymentStatus" => ($payment_method == 'cod') ? 0 : 1,
					// "deliveryTargetDate" => "09-01-2021",
					"shipByDate" => date('m-d-Y'),
					"pickupDate" => date('m-d-Y'),
					"companyName" => $company_name,
					"cxPhone" => $billing_phone,
					"cxEmail" => $user->user_email,
					"beginDate" => $date_created,
					// "totalWeight" => "0.26235009178",
					"totalAmount" => $order_total,
					"notification" => true,
					"lob" => "$lob",
					"d2cOrder" => false,
					"orderItems" => array_values($orderItems),
					"orderChannel" => 1, // Order Channel set to 1 - Website
					"subscription" => "N", // Subscription set to N - no subscription
				);
				
				return $data;
			}
			
			/**
			 * Returns order array data for omni
			 *
			 * @since 2.5.15
			 */
			public static function get_omni_order_data( $order_id ) {
				$order = wc_get_order( $order_id );
				$user      = $order->get_user(); // Get the WP_User object
				$order_total = $order->get_total();
				$payment_method = $order->get_payment_method(); // Get the payment method ID
				$payment_title = $order->get_payment_method_title(); // Get the payment method title
				$date_created  = date('Y-m-d H:i:s'); // Get date created (WC_DateTime object)
				$date_created = "$date_created";
				
				
				$items = array();
				foreach($order->get_items() as $k=>$v){
					$product = wc_get_product( $v->get_product_id() );
					
					$sku = get_post_meta( $v->get_variation_id(), '_sku', true );
					
					if(empty($sku)){
						$sku = $product->get_sku();
					}
					
					// $orderItems[] = array(
										// 'sku' => $sku,
										// 'quantity' => $v->get_quantity(),
										// // 'price' => $v->get_total(),
										// 'price' => $product->get_price(),
									// );
									
					$items[$sku] = array(
										'quantity' => $items[$sku]['quantity']+$v->get_quantity(),
										'price' => $product->get_price()
									);
				}
				
				$order_refunds = $order->get_refunds();
	
				$refunds = array();
				
				foreach( $order_refunds as $refund ){
					// Loop through the order refund line items
					foreach( $refund->get_items() as $item_id => $item ){

						$product = wc_get_product( $item->get_product_id() );
					
						$sku = get_post_meta( $item->get_variation_id(), '_sku', true );
						
						if(empty($sku)){
							$sku = $product->get_sku();
						}
						
						$refunds[$sku] = $refunds[$sku]+(abs($item->get_quantity()));
					}
				}
				
				// foreach($orderItems as $k=>$v){
					// $qt = $v['quantity']-$refunds[$v['sku']];
					// if($qt<1){
						// unset($orderItems[$k]);
					// }else{
						// $orderItems[$k]['quantity'] = $qt;
					// }
				// }

				foreach($items as $k=>$v){
					$qt = $v['quantity']-$refunds[$k];
					if($qt<1){
						unset($items[$k]);
					}else{
						$orderItems[] = array(
										'sku' => $k,
										'quantity' => $v['quantity']-$refunds[$k],
										'price' => $v['price'],
									);
					}
				}


				if(!empty($order->get_shipping_first_name())){
					$shipping_first_name  = $order->get_shipping_first_name();
				} else {
					$shipping_first_name  = "";
				}
				if(!empty($order->get_shipping_last_name())){
					$shipping_last_name  = $order->get_shipping_last_name();
				} else {
					$shipping_last_name  = "";
				}
				if(!empty($order->get_shipping_company())){
					$shipping_company  = $order->get_shipping_company();
				} else {
					$shipping_company  = "";
				}
				if(!empty($order->get_shipping_address_1())){
					$shipping_address_1  = $order->get_shipping_address_1();
				} else {
					$shipping_address_1  = "";
				}
				if(!empty($order->get_shipping_address_2())){
					$shipping_address_2  = $order->get_shipping_address_2();
				} else {
					$shipping_address_2  = "";
				}
				if(!empty($order->get_shipping_city())){
					$shipping_city  = $order->get_shipping_city();
				} else {
					$shipping_city  = "";
				}
				if(!empty($order->get_shipping_state())){
					$shipping_state  = $order->get_shipping_state();
				} else {
					$shipping_state  = "";
				}
				if(!empty($order->get_shipping_postcode())){
					$shipping_postcode  = $order->get_shipping_postcode();
				} else {
					$shipping_postcode  = "";
				}
				if(!empty($order->get_shipping_country())){
					$shipping_country  = $order->get_shipping_country();
				} else {
					$shipping_country  = "";
				}
				
				if($shipping_country == 'US'){
					$shipping_country = 'USA';
				}

				$billing_email     = $order->get_billing_email();
				$billing_phone     = $order->get_billing_phone();

				$billing_first_name  = $order->get_billing_first_name();
				$billing_last_name   = $order->get_billing_last_name();
				$billing_company     = $order->get_billing_company();
				$billing_address_1   = $order->get_billing_address_1();
				$billing_address_2   = $order->get_billing_address_2();
				$billing_city        = $order->get_billing_city();
				$billing_state       = $order->get_billing_state();
				$billing_postcode    = $order->get_billing_postcode();
				$billing_country     = $order->get_billing_country();

				if($billing_country == 'US'){
					$billing_country = 'USA';
				}
				
				$customer_note = $order->get_customer_note();
				
				$company_name = self::get_theme_option('company_name');
				$client_name = self::get_theme_option('client_name');
				$lob = self::get_theme_option('input_lob');
				
				if($lob == ''){
					$lob = 3;
				}
				
				$base_url = self::get_theme_option('input_api_url');
				$url = $base_url.'/order/createOrder';

				$accountID = self::get_theme_option('account_id');
				$api_key = self::get_theme_option('input_api_key');
				
				$order_id = "$order_id";
				$order_total = "$order_total";

				$url1 = explode('.',$_SERVER['SERVER_NAME']);
					
				if(count($url1) == 2){
					$prefix = $url1[0];
				}else if(count($url1) == 3){
					$prefix = $url1[1];
				}else if(count($url1) == 4){
					$prefix = $url1[1];
				}else if(count($url1) == 1){
					$prefix = $url1[0];
				}
				
				$company_name = $prefix;
				
				$prefix = substr($prefix, 0, 3);

				$data = array(
					"accountId" => $accountID,
					"referenceId" => $order_id,
					"orderNumber" => $prefix.'-'.$order_id,
					"orderType" => "2",
					"shipToName" => $shipping_first_name.' '.$shipping_last_name,
					"shipToAddress" => $shipping_address_1,
					"shipToAddress2" => ($shipping_address_2 == '') ? "NA" : $shipping_address_2,
					"shipToCity" => $shipping_city,
					"shipToCountry" => $shipping_country,
					"shipToEmail" => $billing_email,
					"shipToMobile" => $billing_phone,
					"shipToState" => $shipping_state,
					"postalCode" => $shipping_postcode,
					"billToName" => $billing_first_name.' '.$billing_last_name,
					"billToAddress" => $billing_address_1,
					"billToAddress2" => ($billing_address_2 == '') ? "NA" : $billing_address_2,
					"billToCity" => $billing_city,
					"billToState" => $billing_state,
					"billToPostal" => $billing_postcode,
					"billToCountry" => $billing_country,
					"billToMobile" => $billing_phone,
					"billToEmail" => $billing_email,
					"addtionalCharges" => 0,
					"companyName" => $company_name,
					"clientName" => $client_name,
					"totalAmount" => $order_total,
					"lob" => $lob,
					"instructions" => $customer_note,
					"orderItems" => array_values($orderItems),
				);
				
				return $data;
			}

			/**
			 * Add sub menu page
			 *
			 * @since 2.5.15
			 */
			public static function add_admin_menu() {
				add_menu_page(
					esc_html__( 'Advatix', 'advatix-fep-plugin' ),
					esc_html__( 'Advatix', 'advatix-fep-plugin' ),
					'manage_options',
					'theme-settings',
					array( 'ADVATIX_FEP_PLUGIN', 'create_admin_page' )
				);
				
				add_submenu_page ("theme-settings", 
					esc_html__( 'Settings', 'textdomain' ),
					esc_html__( 'Settings', 'textdomain' ),
					'manage_options',
					'theme-settings',
					array( 'ADVATIX_FEP_PLUGIN', 'create_admin_page' ),
					10
				);
				
				add_submenu_page ("theme-settings", 
					esc_html__( 'Orders', 'textdomain' ),
					esc_html__( 'Orders', 'textdomain' ),
					'manage_options',
					'order-settings',
					array( 'ADVATIX_FEP_PLUGIN', 'fep_orders' ),
					10
				);
			}

			/**
			 * Register a setting and its sanitization callback.
			 *
			 * We are only registering 1 setting so we can store all options in a single option as
			 * an array. You could, however, register a new setting for each option
			 *
			 * @since 2.5.15
			 */
			public static function register_settings() {
				register_setting( 'theme_options', 'theme_options', array( 'ADVATIX_FEP_PLUGIN', 'sanitize' ) );
			}

			/**
			 * Sanitization callback
			 *
			 * @since 2.5.15
			 */
			public static function sanitize( $options ) {

				// If we have options lets sanitize them
				if ( $options ) {

					if ( ! empty( $options['input_api_key'] ) ) {
						$options['input_api_key'] = sanitize_text_field( $options['input_api_key'] );
					} else {
						unset( $options['input_api_key'] ); // Remove from options if empty
					}
		
					if ( ! empty( $options['input_api_url'] ) ) {
						$options['input_api_url'] = sanitize_text_field( $options['input_api_url'] );
					} else {
						unset( $options['input_api_url'] ); // Remove from options if empty
					}
		
					if ( ! empty( $options['account_id'] ) ) {
						$options['account_id'] = sanitize_text_field( $options['account_id'] );
					} else {
						unset( $options['account_id'] ); // Remove from options if empty
					}
					
					if ( ! empty( $options['company_name'] ) ) {
						$options['company_name'] = sanitize_text_field( $options['company_name'] );
					} else {
						unset( $options['company_name'] ); // Remove from options if empty
					}
					
					if ( ! empty( $options['client_name'] ) ) {
						$options['client_name'] = sanitize_text_field( $options['client_name'] );
					} else {
						unset( $options['client_name'] ); // Remove from options if empty
					}
					
					if ( ! empty( $options['input_lob'] ) ) {
						$options['input_lob'] = sanitize_text_field( $options['input_lob'] );
					} else {
						unset( $options['input_lob'] ); // Remove from options if empty
					}
					
					if ( ! empty( $options['sync_inventory'] ) ) {
						$options['sync_inventory'] = sanitize_text_field( $options['sync_inventory'] );
					} else {
						unset( $options['sync_inventory'] ); // Remove from options if empty
					}
					
					if ( ! empty( $options['warehouse_ids'] ) ) {
						$options['warehouse_ids'] = sanitize_text_field( $options['warehouse_ids'] );
					} else {
						unset( $options['warehouse_ids'] ); // Remove from options if empty
					}
					
					if ( ! empty( $options['update_api'] ) ) {
						$options['update_api'] = sanitize_text_field( $options['update_api'] );
					} else {
						unset( $options['update_api'] ); // Remove from options if empty
					}
					
					if ( ! empty( $options['api_settings'] ) ) {
						$options['api_settings'] = sanitize_text_field( $options['api_settings'] );
					} else {
						unset( $options['api_settings'] ); // Remove from options if empty
					}
				}

				// Return sanitized options
				return $options;

			}

			/**
			 * Settings page output
			 *
			 * @since 2.5.15
			 */
			public static function create_admin_page() {
				include(WP_PLUGIN_DIR .'/'. plugin_basename( dirname(__FILE__) ) .'/../templates/admin-settings.php');
			}
			
			
			/**
			 * Orders page output
			 *
			 * @since 2.5.15
			 */
			public static function fep_orders() {
				include(WP_PLUGIN_DIR .'/'. plugin_basename( dirname(__FILE__) ) .'/../templates/admin-orders.php');
			}
		}
	}