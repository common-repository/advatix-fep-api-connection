<?php

	/**
	 * Process order create hook
	 */
	// add_action('woocommerce_new_order', function ($order_id, $order) {
	add_action('woocommerce_order_status_processing', function ($order_id) {
		global $wpdb, $woocommerce;

		$order_id = sanitize_text_field($order_id);
		
		$base_url = advatix_api_option('input_api_url');
		$api_url = $base_url.'/order/createOrder';

		$api_key = advatix_api_option('input_api_key');
		$accountID = advatix_api_option('account_id');
		$api_settings = advatix_api_option('api_settings');
		
		$headers = array(
			'Content-Type' => 'application/json',
			'Device-Type' => 'Web',
			'Ver' => '1.0',
			'ApiKey' => $api_key
		);
		
		if($api_settings == 'omni'){
			$headers['AccountId'] = $accountID;
			$data = advatix_omni_order_data( $order_id );
		}else{
			$data = advatix_fep_order_data( $order_id );
		}

		
		
		$args = array(
			'headers' => $headers,
			'timeout' => 300000,
			'body' => wp_json_encode($data)
		);

		$res = wp_remote_post($api_url, $args );

		if(simplexml_load_string($res['body'])){
			$result_jd = json_decode(json_encode((array)simplexml_load_string($res['body'])));
		}else{
			$result_jd = json_decode($res['body']);
		}
		
		$table_name = $wpdb->base_prefix . 'fep_api_order_resp';
		$wpdb->insert(
			$table_name,
			array(
				'requestJson' => wp_json_encode($data),
				'order_id' => $order_id,
				'responseMessage' => $result_jd->responseMessage,
				'responseStatus' => $result_jd->responseStatus,
				'responseStatusCode' => $result_jd->responseStatusCode,
				'responseObject' => $result_jd->responseObject,
				'type' => 'createOrder',
				'time' => current_time( 'mysql' ),
			)
		);

	}, 10, 1);
	
	
	/**
	 * Update order hook
	 */
	/*add_action( 'woocommerce_after_order_object_save', 'some_order_action', 10, 1 );
	function some_order_action( $order ) {
		global $wpdb;
			
		$order_id = sanitize_text_field($order->get_id());
		
		$table_name = $wpdb->base_prefix . 'fep_api_order_resp';
		$q = $wpdb->get_results('SELECT * FROM '.$table_name.' WHERE order_id="'.$order_id.'" ORDER BY id DESC');
		
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
				}
			}
		}
		
		if($synced){
			sleep(5);
		
			$base_url = advatix_api_option('input_api_url');
			

			$api_key = advatix_api_option('input_api_key');
			$accountID = advatix_api_option('account_id');
			$api_settings = advatix_api_option('api_settings');
			
			
			$headers = array(
				'Content-Type' => 'application/json',
				'Device-Type' => 'Web',
				'Ver' => '1.0',
				'ApiKey' => $api_key
			);
			
			if($api_settings == 'omni'){
				$headers['AccountId'] = $accountID;
				$data = advatix_omni_order_data( $order_id );
				
				$api_url = $base_url.'/order/createOrder';
				
				$args = array(
					'headers' => $headers,
					'timeout' => 300000,
					'body' => wp_json_encode($data),
					'method' => 'POST'
				);

			}else{
				$data = advatix_fep_order_data( $order_id );
				
				$api_url = $base_url.'/order/updateOrder';
				
				$args = array(
					'headers' => $headers,
					'timeout' => 300000,
					'body' => wp_json_encode($data),
					'method' => 'PUT'
				);
			}

			$res = wp_remote_request($api_url, $args );
			
			if(!is_wp_error($res)){
				$result_jd = json_decode($res['body']);
			
				$table_name = $wpdb->base_prefix . 'fep_api_order_resp';
				$wpdb->insert(
					$table_name,
					array(
						'requestJson' => wp_json_encode($data),
						'order_id' => $order_id,
						'responseMessage' => $result_jd->responseMessage,
						'responseStatus' => $result_jd->responseStatus,
						'responseStatusCode' => $result_jd->responseStatusCode,
						'responseObject' => $result_jd->responseObject,
						'type' => 'updateOrder',
						'time' => current_time( 'mysql' ),
					)
				);
			}
			
		}
	}*/
	
	
	/**
	 * Cancelled order hook
	 */
	add_action( 'woocommerce_order_status_cancelled', 'adv_fep_order_cancelled', 21, 1 );
	function adv_fep_order_cancelled( $order_id ) {
		// $update_api = advatix_api_option('update_api');

		// if ($update_api == 1) {
			
			global $wpdb;
			
			$order_id = sanitize_text_field($order_id);
			
			$table_name = $wpdb->base_prefix . 'fep_api_order_resp';
			$q = $wpdb->get_results('SELECT * FROM '.$table_name.' WHERE order_id="'.$order_id.'" ORDER BY id DESC');
			
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
					}
				}
			}
			
			if($synced){
				
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
				
				$order_number = $prefix.'-'.$order_id;
				
				$api_settings = advatix_api_option('api_settings');
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
					
					$api_url = $base_url . '/order/cancelOrder?reason=Order_Cancelled_Website';

					$data = array($order_number);

					$args = array(
						'headers' => $headers,
						'timeout' => 300000,
						'body' => wp_json_encode($data),
					);

					$res = wp_remote_post($api_url, $args);
					
					$result_jd = json_decode($res['body']);
					
					if ( is_wp_error( $res ) || wp_remote_retrieve_response_code( $res ) != 200 ) {
						// return false;
						$table_name = $wpdb->base_prefix . 'fep_api_order_resp';
						$wpdb->insert(
							$table_name,
							array(
								'requestJson' => wp_json_encode($data),
								'order_id' => $order_id,
								'responseMessage' => 'Order Cancelation Failed',
								'responseStatus' => $result_jd->responseStatus,
								'responseStatusCode' => $result_jd->responseStatusCode,
								'responseObject' => $result_jd->responseObject,
								'type' => 'cancelOrder',
								'time' => current_time( 'mysql' ),
							)
						);
					}else{
						$table_name = $wpdb->base_prefix . 'fep_api_order_resp';
						$wpdb->insert(
							$table_name,
							array(
								'requestJson' => wp_json_encode($data),
								'order_id' => $order_id,
								'responseMessage' => 'Order Cancelled',
								'responseStatus' => $result_jd->responseStatus,
								'responseStatusCode' => $result_jd->responseStatusCode,
								'responseObject' => $result_jd->responseObject,
								'type' => 'cancelOrder',
								'time' => current_time( 'mysql' ),
							)
						);
					}

				}else{
					$api_url = $base_url . '/order/cancelOrder/'.$order_number.'?reason=Order_Cancelled_Website';

					$args = array(
						'headers' => $headers,
						'timeout' => 300000
					);

					$res = wp_remote_get($api_url, $args);
					
					if ( is_wp_error( $res ) || wp_remote_retrieve_response_code( $res ) != 200 ) {
						$table_name = $wpdb->base_prefix . 'fep_api_order_resp';
						$wpdb->insert(
							$table_name,
							array(
								// 'requestJson' => wp_json_encode($data),
								'requestJson' => '',
								'order_id' => $order_id,
								'responseMessage' => 'Order Cancelation Failed',
								'responseStatus' => wp_remote_retrieve_response_code( $res ),
								'responseStatusCode' => wp_remote_retrieve_response_code( $res ),
								'responseObject' => '-',
								'type' => 'cancelOrder',
								'time' => current_time( 'mysql' ),
							)
						);
					}else{
						$table_name = $wpdb->base_prefix . 'fep_api_order_resp';
						$wpdb->insert(
							$table_name,
							array(
								// 'requestJson' => wp_json_encode($data),
								'requestJson' => '',
								'order_id' => $order_id,
								'responseMessage' => 'Order Cancelled',
								'responseStatus' => '200',
								'responseStatusCode' => '200',
								'responseObject' => '-',
								'type' => 'cancelOrder',
								'time' => current_time( 'mysql' ),
							)
						);
					}
				}
				
			}
		// }
	}