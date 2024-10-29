<?php	
	/**
	 * Sync Bulk Orders Ajax
	 */
	add_action('wp_ajax_adv_sync_fep_all_order_details', 'adv_sync_fep_all_order_details_callback');
	add_action('wp_ajax_nopriv_adv_sync_fep_all_order_details', 'adv_sync_fep_all_order_details_callback');
	function adv_sync_fep_all_order_details_callback()
	{
		error_reporting(0);
		
		global $wpdb;
		
		$orders = sanitize_text_field($_POST['order_ids']);
		
		$base_url = advatix_api_option('input_api_url');
		$api_url = $base_url.'/order/createOrder';
		$api_key = advatix_api_option('input_api_key');
		$accountID = advatix_api_option('account_id');
		$api_settings = advatix_api_option('api_settings');
		
		
		foreach($orders as $order_id){

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

		}
		
		die();
	}


	/**
	 * Sync Single Order Ajax
	 */
	add_action('wp_ajax_adv_sync_fep_order_details', 'adv_sync_fep_order_details_callback');
	add_action('wp_ajax_nopriv_adv_sync_fep_order_details', 'adv_sync_fep_order_details_callback');
	function adv_sync_fep_order_details_callback()
	{
		error_reporting(0);
		
		global $wpdb;
		
		$order_id = sanitize_text_field($_POST['order_id']);
		
		$order = wc_get_order( $order_id );
		
		if ( !$order->has_status('on-hold') && !$order->has_status('checkout-draft') && !$order->has_status('cancelled') ) {
			
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

			
			$array = array(
						'responseCode' => $result_jd->responseStatusCode,
						'responseMessage' => $result_jd->responseMessage
					);
		}else{
			$array = array(
					'responseCode' => '201',
					'responseMessage' => 'Order is either on-hold, draft or cancelled'
				);
		}
		echo wp_json_encode($array);
		
		die();
	}

	/**
	 * Update Single Order Ajax
	 */
	add_action('wp_ajax_adv_update_fep_order_details', 'adv_update_fep_order_details_callback');
	add_action('wp_ajax_nopriv_adv_update_fep_order_details', 'adv_update_fep_order_details_callback');
	function adv_update_fep_order_details_callback()
	{
		error_reporting(0);
		
		/*global $wpdb;
		
		$order_id = sanitize_text_field($_POST['order_id']);
		
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
				'type' => 'updateOrder',
				'time' => current_time( 'mysql' ),
			)
		);

		
		
		$array = array(
					'responseCode' => $result_jd->responseStatusCode,
					'responseMessage' => $result_jd->responseMessage
				);
		
		// echo wp_json_encode($res['body']);
		echo wp_json_encode($array);
		
		die();*/
	}


	/**
	 * Fetch Single Order FEP API response Ajax
	 */
	add_action('wp_ajax_adv_get_fep_api_details', 'adv_get_fep_api_details_callback');
	add_action('wp_ajax_nopriv_adv_get_fep_api_details', 'adv_get_fep_api_details_callback');
	function adv_get_fep_api_details_callback()
	{
		error_reporting(0);
		
		global $wpdb;
		
		$table_name = $wpdb->base_prefix . 'fep_api_order_resp';
		$q = $wpdb->get_results('SELECT * FROM '.$table_name.' WHERE order_id="'.sanitize_text_field($_POST['order_id']).'" ORDER BY id DESC LIMIT 10');
		
		
		if(!empty($q)){
			foreach($q as $k=>$v){
				echo "<tr>";
				echo "<td>".date('M d, Y - H:iA', strtotime($v->time))."</td>";
				echo "<td>".$v->responseStatusCode." (<a href='javascript:void(0)' class='copy-request' data-request='".$v->requestJson."'>Copy Request</a>)</td>";
				if($v->responseMessage==''){
					echo "<td>-</td>";
				}else{
					echo "<td>".$v->responseMessage."</td>";
				}
				if($v->type==''){
					echo "<td>createOrder</td>";
				}else{
					echo "<td>".$v->type."</td>";
				}
				echo "</tr>";
			}
		}else{
			echo "<tr>";
			echo "<td colspan='4'>No data found.</td>";
			echo "</tr>";
		}
		
		
		die();
	}

	/**
	 * Validate API credentials for OMNI Layer
	 */
	add_action('wp_ajax_adv_validate_omni_api_details', 'adv_validate_omni_api_details_callback');
	add_action('wp_ajax_nopriv_adv_validate_omni_api_details', 'adv_validate_omni_api_details_callback');
	function adv_validate_omni_api_details_callback()
	{
		error_reporting(0);
		
		$accountId = $_POST['accountId'];
		$apiKey = $_POST['apiKey'];
		$apiUrl = $_POST['apiUrl'];
		$company_name = $_POST['company_name'];
		
		$headers = array(
			'Content-Type' => ' application/json',
			'Device-Type' => 'Web',
			'Ver' => '1.0',
			'AccountId' => $accountId,
			'ApiKey' => $apiKey,
		);
		
		$args = array(
				'headers' => $headers,
				'timeout' => 300000,
				'method' => 'GET'
			);
		
		$return = wp_remote_request($apiUrl.'/carrierManagement/getCarrierFilters', $args );
		
		if(is_string($return['body']) && is_array(json_decode($return['body'], true))){
			$return = json_decode($return['body']);
		}else{
			$return = simplexml_load_string($return['body']);
			$return = json_encode($return);
			$return = json_decode($return);
		}
		
		
		$responseStatus = $return->responseStatus;
		$responseStatusCode = $return->responseStatusCode;
		$responseObject = $return->responseObject;

		if(($responseStatus == true) && ($responseStatusCode == '200')){
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
			
			$channelName = $prefix;
			
			$channelName = str_replace ( ' ', '%20', $channelName );
			$url = $apiUrl . "/integration/findByChannel/".$channelName;
			
			$headers = array(
				'Content-Type' => ' application/json',
				'Device-Type' => 'Web',
				'Ver' => '1.0',
				'AccountId' => $accountId,
				'ApiKey' => $apiKey,
			);
			
			$args = array(
					'headers' => $headers,
					'timeout' => 300000,
					'method' => 'GET'
				);
			
			$return = wp_remote_request($url, $args );
			
			if(is_string($return['body']) && is_array(json_decode($return['body'], true))){
				$return = json_decode($return['body']);
			}else{
				$return = simplexml_load_string($return['body']);
				$return = json_encode($return);
				$return = json_decode($return);
			}
			
			$getStore = $return->responseObject;
			
			if(empty($getStore)){
				$data = array(
								'channelName' => $channelName,
								'communicationBrand' => ucfirst($channelName),
								'fepAccountId' => $accountId,
								'fepApiKey' => $apiKey,
								'channelUrl' => site_url(),
								'channelApiKey' => '',
								'channelType' => 3,
								'integrationType' => 1,
								'environment' => 'production',
								"checkMapping" => 0,
								'connectionStatus' =>0
							);
				
				$url = $apiUrl . "/integration/addChannel";
				$headers = array(
								"device-type" => "Web",
								"ver" => "1.0",
								"content-type" => "application/json",
								"ApiKey" => $apiKey,
								"AccountId" => $accountId,
							);

				$args = array(
						'headers' => $headers,
						'timeout' => 300000,
						'body' => wp_json_encode($data)
					);

				$return = wp_remote_post($url, $args );
				
				if(is_string($return['body']) && is_array(json_decode($return['body'], true))){
					$return = json_decode($return['body']);
				}else{
					$return = simplexml_load_string($return['body']);
					$return = json_encode($return);
					$return = json_decode($return);
				}
				
			}else{
				$data = array(
							'channelUrl' => site_url(),
							'fepApiKey' => $apiKey,
							'fepAccountId' => $accountId,
							'environment' => 'production',
							'channelType' => 3,
							'integrationType' => 1,
							'channelName' => $channelName,
							'communicationBrand' => ucfirst($channelName),
							'apiHost' => parse_url($apiUrl)['host'],
							'checkMapping' => 0
						);
				$url = $apiUrl . "/integration/updateChannel";
				
				$headers = array(
								"device-type" => "Web",
								"ver" => "1.0",
								"content-type" => "application/json",
								"ApiKey" => $apiKey,
								"AccountId" => $accountId,
							);

				$args = array(
						'headers' => $headers,
						'timeout' => 300000,
						'body' => wp_json_encode($data),
						'method' => 'PUT'
					);
				
				$return = wp_remote_request($url, $args );

				if(is_string($return['body']) && is_array(json_decode($return['body'], true))){
					$return = json_decode($return['body']);
				}else{
					$return = simplexml_load_string($return['body']);
					$return = json_encode($return);
					$return = json_decode($return);
				}
			}
			
		}else{
			echo "failed";
		}
		
		
		die();
	}

	/**
	 * Validate API credentials for FEP Layer
	 */
	add_action('wp_ajax_adv_validate_fep_api_details', 'adv_validate_fep_api_details_callback');
	add_action('wp_ajax_nopriv_adv_validate_fep_api_details', 'adv_validate_fep_api_details_callback');
	function adv_validate_fep_api_details_callback()
	{
		error_reporting(0);
		
		$accountId = $_POST['accountId'];
		$apiKey = $_POST['apiKey'];
		$apiUrl = $_POST['apiUrl'];
		
		$headers = array(
			'Content-Type' => 'application/json',
			'Device-Type' => 'Web',
			'Ver' => '1.0',
		);

		$data = array(
					'accountId' => $accountId,
					'apiKey' => $apiKey,
				);

		$args = array(
				'headers' => $headers,
				'timeout' => 300000,
				'body' => wp_json_encode($data)
			);

		$return = wp_remote_post($apiUrl.'/account/validateApiKey', $args );
		$return = json_decode($return['body']);
		
		$responseStatus = $return->responseStatus;
		$responseStatusCode = $return->responseStatusCode;
		$responseObject = $return->responseObject;

		if(($responseStatus == true) && ($responseStatusCode == '200')){
			
		}else{
			echo "failed";
		}
		
		
		die();
	}