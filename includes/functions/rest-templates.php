<?php

	/**
	 * Register Advatix Rest API URLs
	 */
	add_action( 'rest_api_init', function () {
	  register_rest_route( 'advatix-fep-plugin/v1', '/updateOrder', array(
		'methods' => 'POST',
		'callback' => 'adv_update_order',
	  ) );
	  
	  register_rest_route('advatix-fep-plugin/v1', '/updateInventory', array(
			'methods' => 'POST',
			'callback' => 'adv_update_inventory',
		));
	  
	  register_rest_route('advatix-fep-plugin/v1', '/getProducts', array(
			'methods' => 'GET',
			'callback' => 'adv_get_products',
		));
	} );

	/**
	 * Advatix Rest API Hook for Order status update
	 */
	function adv_update_order($request)
	{
		global $wpdb;
		
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
		
		$site_key = $request->get_header('site-key');
		
		$header_check = false;
		if($site_key != null){
			if($site_key == $prefix){
				$header_check = true;
			}
		}else{
			$header_check = true;
		}
		
		$parameters = $request->get_json_params();

		if ($header_check == false) {
			return new WP_Error('invalid_header', 'Invalid site-key header', array('status' => 401));
		}

		if (empty($parameters)) {
			return new WP_Error('invalid_data', 'Invalid json body', array('status' => 404));
		}

		if (empty($parameters['orderReferenceNumber'])) {
			return new WP_Error('invalid_order', 'Invalid order id', array('status' => 404));
		}
		
		if($prefix != explode("-",$parameters['orderNumber'])[0]){
			return new WP_Error('invalid_order', 'Invalid order id', array('status' => 404));
		}

		$order = wc_get_order($parameters['orderReferenceNumber']);

		if (empty($order)) {
			return new WP_Error('invalid_order', 'Invalid order id', array('status' => 404));
		}

		if (strtolower($parameters['orderStatusDesc']) == 'created') {
			$order->update_status('created');
		}
		if (strtolower($parameters['orderStatusDesc']) == 'assigned') {
			$order->update_status('assigned');
		}
		if (strtolower($parameters['orderStatusDesc']) == 'picked') {
			$order->update_status('picked');
		}
		if (strtolower($parameters['orderStatusDesc']) == 'packed') {
			$order->update_status('packed');
		}
		if (strtolower($parameters['orderStatusDesc']) == 'cancelled') {
			$order->update_status('cancelled');
		}
		if (strtolower($parameters['orderStatusDesc']) == 'shipped') {
			
			foreach($parameters['subOrdersList'] as $k=>$v){
				
				$table_name = $wpdb->base_prefix . 'fep_shipment_tracking';
				$q = $wpdb->get_results('SELECT * FROM '.$table_name.' WHERE order_id="'.$parameters['orderReferenceNumber'].'" AND trackingNumber="'.$v['carrierTrackingId'].'"');
				
				if ( empty( $q ) ) {
					
					$table_name = $wpdb->base_prefix . 'fep_shipment_tracking';
					$wpdb->insert(
						$table_name,
						array(
							'order_id' => $parameters['orderReferenceNumber'],
							'orderItems' => json_encode($v['orderItems']),
							'trackingNumber' => $v['carrierTrackingId'],
							'trackingUrl' => $v['trackingUrl'],
							'carrierName' => $v['carrier'],
							'time' => current_time( 'mysql' ),
						)
					);
					
				}
				
			}
			$order->update_status('shipped');
		}
		if (strtolower($parameters['orderStatusDesc']) == 'out for delivery') {
			$order->update_status('outfordelivery');
		}
		if (strtolower($parameters['orderStatusDesc']) == 'delivered') {
			$order->update_status('completed');
		}

		return $order->get_data();
	}
	
	/**
	 * Advatix Rest API Hook for inventory update
	 */
	function adv_update_inventory($request)
	{
		$parameters = $request->get_json_params();

		if (empty($parameters)) {
			return new WP_Error('invalid_data', 'Invalid json body', array('status' => 404));
		}

		$sku_notfound = array();
		$sku_found = array();
		
		foreach($parameters as $k=>$v){
			$sku = $v['skuNumber'];
		
			// Get product_id from SKU — returns null if not found
			$product_id = wc_get_product_id_by_sku( $sku );

			// Process if product found
			if ( $product_id != null ) {
				
				// Set up WooCommerce product object
				$product = wc_get_product( $product_id );
				
				update_post_meta($product->get_id(), '_manage_stock', 'yes');
				update_post_meta($product->get_id(), '_stock', $v['availableToPromise']);
				
				$sku_found[] = $sku;

			}else{
				$sku_notfound[] = $sku;
			}
		}
		
		if(empty($sku_notfound)){
			return array( 'success' => array( 'message' => 'Sku numbers updated', 'skus' => $sku_found ) );
		}else{
			return array('error' => array( 'message' => 'No product found with sku numbers', 'skus' => $sku_notfound ), 'success' => array( 'message' => 'Sku numbers updated', 'skus' => $sku_found ));
		}

	}
	
	
	/**
	 * Advatix Rest API Hook for get products
	 */
	function adv_get_products($request)
	{
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
		
		$opAccountID = advatix_api_option('account_id');
		
		$AccountId = $request->get_header('AccountId');
		
		$header_check = false;
		if($AccountId != null){
			if($AccountId == $opAccountID){
				$header_check = true;
			}
		}

		if ($header_check == false) {
			return new WP_Error('invalid_header', 'Invalid AccountId', array('status' => 401));
		}
		
		$args = array(
			'post_type'      => 'product',
			'posts_per_page' => -1,
			'post_status'    => 'publish'
		);

		$loop = new WP_Query( $args );

		$products = array();

		while ( $loop->have_posts() ) : $loop->the_post();
			global $product;

			$products[] = array(
								'title' => get_the_title(),
								'description' => get_the_content(),
								'sku' => $product->get_sku(),
								'companyName' => $channelName
							); 
		endwhile;

		wp_reset_query();
		
		
		return $products;
	}