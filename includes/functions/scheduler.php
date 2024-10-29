<?php
	/**
	 * Every Midnight Cron
	 */
	add_action('wp_ajax_adv_fep_daily_at_midnight_actions', 'adv_fep_daily_at_midnight_actions_func');
	add_action('wp_ajax_nopriv_adv_fep_daily_at_midnight_actions', 'adv_fep_daily_at_midnight_actions_func');
	add_action( 'adv_fep_daily_at_midnight_actions', 'adv_fep_daily_at_midnight_actions_func' );
	function adv_fep_daily_at_midnight_actions_func()
	{
		//Sync mis-match inventory - Every Midnight Cron
		$api_settings = advatix_api_option('api_settings');
		$sync_inventory = advatix_api_option('sync_inventory');
		$warehouse_ids = advatix_api_option('warehouse_ids');
		
		if(empty($warehouse_ids)){
			$warehouse_ids = 0;
		}else{
			$warehouse_ids = explode(',',$warehouse_ids);
		}

		if ($sync_inventory == 1) {
			global $wpdb;

			$base_url = advatix_api_option('input_api_url');
			$api_key = advatix_api_option('input_api_key');
			$accountID = advatix_api_option('account_id');

			$headers = array(
				'Content-Type' => 'application/json',
				'Device-Type' => 'Web',
				'Ver' => '1.0',
				'ApiKey' => $api_key
			);
			
			if($api_settings == 'omni'){
				$headers['AccountId'] = $accountID;
			}

			$query = new WP_query(
				array(
					'post_type' => 'product',
					'posts_per_page' => -1
				)
			);

			while ($query->have_posts()) {
				$query->the_post();
				$product = wc_get_product(get_the_ID());
				
				if ( $product->is_type( "variable" ) ) {

					foreach ( $product->get_children( false ) as $child_id ) {
						// get an instance of the WC_Variation_product Object
						$variation = wc_get_product( $child_id ); 

						if ( ! $variation || ! $variation->exists() ) {
							continue;
						}
						
						$sku = $variation->get_sku();

						if ($sku != '') {
							$inventory = 0;
							foreach($warehouse_ids as $id){
								$api_url = $base_url . '/inventory/getAvailableQuantityForProduct?accountId='.$accountID.'&skuNumber='.$sku.'&warehouseLocation='.trim($id);

								$cont = array(
									array('key' => 'SKU', 'value' => $sku),
									array('key' => 'ACCOUNT_ID', 'value' => $accountID),
								);

								$args = array(
									'headers' => $headers,
									'timeout' => 300000,
									// 'body' => wp_json_encode($cont)
								);

								$res = wp_remote_get($api_url, $args);

								$return = json_decode($res['body']);
								$inv = $return->responseObject;

								if (!empty($inv)) {
									$inventory = $inventory + $inv[0]->availableToPromise;
								}
								// sleep(500);
							}
							// echo "<pre>";
							// print_r($sku.' --- '.$inventory);
							// echo "</pre>";
							update_post_meta($variation->get_id(), '_manage_stock', 'yes');
							update_post_meta($variation->get_id(), '_stock', $inventory);
						}
					}

				} else {

					$sku = $product->get_sku();

					if ($sku != '') {
						$inventory = 0;
						foreach($warehouse_ids as $id){

							$api_url = $base_url . '/inventory/getAvailableQuantityForProduct?accountId='.$accountID.'&skuNumber='.$sku.'&warehouseLocation='.trim($id);

							$cont = array(
								array('key' => 'SKU', 'value' => $sku),
								array('key' => 'ACCOUNT_ID', 'value' => $accountID),
							);

							$args = array(
								'headers' => $headers,
								'timeout' => 300000,
								// 'body' => wp_json_encode($cont)
							);

							$res = wp_remote_get($api_url, $args);

							$return = json_decode($res['body']);
							$inv = $return->responseObject;

							if (!empty($inv)) {
								$inventory = $inventory + $inv[0]->availableToPromise;
							}
							// sleep(500);
							// echo "<pre>";
							// print_r($sku.' --- '.$inventory);
							// echo "</pre>";
						}
						update_post_meta($product->get_id(), '_manage_stock', 'yes');
						update_post_meta($product->get_id(), '_stock', $inventory);
					}

				}
				
			}
		}
		
		//Remove Order Logs - Every Midnight Cron
		// $wpdb->query(
		  // "DELETE FROM " . $wpdb->prefix . "fep_api_order_resp
		   // WHERE time < DATE_SUB(CURDATE(),INTERVAL 30 DAY)"
		// );
	}
	
	/**
	 * Retry Sync last 100 orders - Every Thirty Minutes Cron
	 */
	add_action( 'adv_fep_isa_add_every_thirty_minute', 'adv_fep_every_thirty_minute_event_func' );
	function adv_fep_every_thirty_minute_event_func() {
		global $wpdb, $woocommerce;
		
		$orders = get_posts(array(
			'post_type' => wc_get_order_types('view-orders'),
			'posts_per_page' => 100,
			'post_status' => array_keys(wc_get_order_statuses()),
			'order_by' => 'ID',
			'order' => 'DESC'
		));
		
		foreach($orders as $k=>$v){
			$order = wc_get_order( $v->ID );
			if ( is_a( $order, 'WC_Order_Refund' ) ) {
				$order = wc_get_order( $order->get_parent_id() );
			}
			
 
				$table_name = $wpdb->base_prefix . 'fep_api_order_resp';
				$q = $wpdb->get_results('SELECT * FROM '.$table_name.' WHERE order_id="'.$order->get_id().'" AND (type="createOrder" OR type IS NULL) ORDER BY id DESC');
				
				$synced = false;
				$warning = false;
				
				foreach($q as $kk=>$vv){
					if($vv->responseStatusCode == 200){
						$synced = true;
					}else{					
						if ( (strpos(strtolower($vv->responseMessage), 'order already exists with order number') !== false)
							||(strpos(strtolower($vv->responseMessage), 'products are not available at fc') !== false)
							||(strpos(strtolower($vv->responseMessage), 'ordered products are not available') !== false)
							||(strpos(strtolower($vv->responseMessage), '{error.message.order.items.sku.not.null}') !== false)
							||(strpos(strtolower($vv->responseMessage), 'order items empty cannot continue') !== false)
							||(strpos(strtolower($vv->responseMessage), 'incorrect zip code') !== false)
							||(strpos(strtolower($vv->responseMessage), 'Shipping Address not found for') !== false) )
						{
							$synced = true;
							$warning = true;
						}
					}
				}
				
				if($synced == false && $warning == false){
					if ( !$order->has_status('on-hold') && !$order->has_status('checkout-draft') && !$order->has_status('cancelled') ) {
						
						$base_url = advatix_api_option('input_api_url');
						$api_url = $base_url.'/order/createOrder';

						$api_settings = advatix_api_option('api_settings');
						$api_key = advatix_api_option('input_api_key');
						$accountID = advatix_api_option('account_id');
						
						$headers = array(
							'Content-Type' => 'application/json',
							'Device-Type' => 'Web',
							'Ver' => '1.0',
							'ApiKey' => $api_key
						);
						
						if($api_settings == 'omni'){
							$headers['AccountId'] = $accountID;
							$data = advatix_omni_order_data( $order->get_id() );
						}else{
							$data = advatix_fep_order_data( $order->get_id() );
						}
						
						$args = array(
							'headers' => $headers,
							'timeout' => 300000,
							'body' => wp_json_encode($data)
						);

						$res = wp_remote_post($api_url, $args );

						$result_jd = json_decode($res['body']);
						
						$table_name = $wpdb->base_prefix . 'fep_api_order_resp';
						$wpdb->insert(
							$table_name,
							array(
								'requestJson' => wp_json_encode($data),
								'order_id' => $order->get_id(),
								'responseMessage' => $result_jd->responseMessage,
								'responseStatus' => $result_jd->responseStatus,
								'responseStatusCode' => $result_jd->responseStatusCode,
								'responseObject' => $result_jd->responseObject,
								'type' => 'createOrder',
								'time' => current_time( 'mysql' ),
							)
						);
					}
				}
				
				/*$table_name = $wpdb->base_prefix . 'fep_api_order_resp';
				$q = $wpdb->get_results('SELECT * FROM '.$table_name.' WHERE order_id="'.$order->get_id().'" AND type="updateOrder" ORDER BY id DESC  LIMIT 1');
				
				if(!empty($q)){
					$updated = false;
				
					foreach($q as $kk=>$vv){
						if($vv->responseStatusCode == 200){
							$updated = true;
						}else{					
							if ( (strpos(strtolower($vv->responseMessage), 'order already exists with order number') !== false)
								||(strpos(strtolower($vv->responseMessage), 'products are not available at fc') !== false)
								||(strpos(strtolower($vv->responseMessage), 'ordered products are not available') !== false)
								||(strpos(strtolower($vv->responseMessage), '{error.message.order.items.sku.not.null}') !== false)
								||(strpos(strtolower($vv->responseMessage), 'order items empty cannot continue') !== false)
								||(strpos(strtolower($vv->responseMessage), 'incorrect zip code') !== false)
								||(strpos(strtolower($vv->responseMessage), 'Shipping Address not found for') !== false) )
							{
								$updated = true;
							}
						}
					}
					
					if($updated == false){
						
						
						$api_settings = advatix_api_option('api_settings');
						$base_url = advatix_api_option('input_api_url');
						
						$accountID = advatix_api_option('account_id');

						$api_key = advatix_api_option('input_api_key');
						
						if($api_settings == 'omni'){
							$headers['AccountId'] = $accountID;
							$data = advatix_omni_order_data( $order->get_id() );
							$api_url = $base_url.'/order/createOrder';
							
							$args = array(
								'headers' => $headers,
								'timeout' => 300000,
								'body' => wp_json_encode($data),
								'method' => 'POST'
							);

						}else{
							$data = advatix_fep_order_data( $order->get_id() );
							$api_url = $base_url.'/order/updateOrder';
							
							$args = array(
								'headers' => $headers,
								'timeout' => 300000,
								'body' => wp_json_encode($data),
								'method' => 'PUT'
							);
						}

						$res = wp_remote_request($api_url, $args );

						$result_jd = json_decode($res['body']);
						
						$table_name = $wpdb->base_prefix . 'fep_api_order_resp';
						$wpdb->insert(
							$table_name,
							array(
								'requestJson' => wp_json_encode($data),
								'order_id' => $order->get_id(),
								'responseMessage' => $result_jd->responseMessage,
								'responseStatus' => $result_jd->responseStatus,
								'responseStatusCode' => $result_jd->responseStatusCode,
								'responseObject' => $result_jd->responseObject,
								'type' => 'updateOrder',
								'time' => current_time( 'mysql' ),
							)
						);
					}
				}*/
				
			
		}
	}