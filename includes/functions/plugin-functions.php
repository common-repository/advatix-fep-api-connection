<?php
	/**
	 * Update plugin database
	 */
	function plugin_update() {
		global $plugin_version;

		// if ( get_site_option( 'plugin_version' ) != $plugin_version )
			plugin_updates();


	}
	add_action( 'plugins_loaded', 'plugin_update' );
	function plugin_updates() {
		global $wpdb, $plugin_version;

		$table_name = $wpdb->base_prefix.'fep_api_order_resp';
		$query = $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $table_name ) );

		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE $table_name (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			order_id varchar(100) DEFAULT NULL,
			requestJson longtext DEFAULT NULL,
			responseMessage longtext DEFAULT NULL,
			responseStatus varchar(200) DEFAULT NULL,
			responseStatusCode varchar(200) DEFAULT NULL,
			responseObject longtext DEFAULT NULL,
			type longtext DEFAULT NULL,
			time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
			PRIMARY KEY  (id)
		) $charset_collate;";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
		
		// Create FEP Tracking API requests table
		$table_name = $wpdb->base_prefix.'fep_shipment_tracking';
		$query = $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $table_name ) );

		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE $table_name (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			order_id varchar(100) DEFAULT NULL,
			requestJson longtext DEFAULT NULL,
			responseMessage longtext DEFAULT NULL,
			responseStatus varchar(200) DEFAULT NULL,
			responseStatusCode varchar(200) DEFAULT NULL,
			responseObject longtext DEFAULT NULL,
			orderItems longtext DEFAULT NULL,
			trackingNumber longtext DEFAULT NULL,
			trackingUrl longtext DEFAULT NULL,
			carrierName longtext DEFAULT NULL,
			time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
			PRIMARY KEY  (id)
		) $charset_collate;";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
		
		// Schedule an action if it's not already scheduled
		if ( ! wp_next_scheduled( 'adv_fep_isa_add_every_thirty_minute' ) ) {
			wp_schedule_event( time(), 'every_thirty_minute', 'adv_fep_isa_add_every_thirty_minute' );
		}
		
		if ( ! wp_next_scheduled( 'adv_fep_daily_at_midnight_actions' ) ) {
			$local_time_to_run = date('Y-m-d 00:00:00', strtotime('+1day'));
			// $timestamp = strtotime( $local_time_to_run ) - ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS );
			$timestamp = strtotime( $local_time_to_run );
			wp_schedule_event(
				$timestamp,
				'every_day_midnight',
				'adv_fep_daily_at_midnight_actions'
			);
		}
	}
	
	/**
	 * Plugin activation hook
	 */
	function adv_fep_install() {
		global $wpdb;
		$table_name = $wpdb->base_prefix.'fep_api_order_resp';
		$query = $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $table_name ) );

		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE $table_name (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			order_id varchar(100) DEFAULT NULL,
			requestJson longtext DEFAULT NULL,
			responseMessage longtext DEFAULT NULL,
			responseStatus varchar(200) DEFAULT NULL,
			responseStatusCode varchar(200) DEFAULT NULL,
			responseObject longtext DEFAULT NULL,
			type longtext DEFAULT NULL,
			time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
			PRIMARY KEY  (id)
		) $charset_collate;";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
		
		// Create FEP Tracking API requests table
		$table_name = $wpdb->base_prefix.'fep_shipment_tracking';
		$query = $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $table_name ) );

		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE $table_name (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			order_id varchar(100) DEFAULT NULL,
			requestJson longtext DEFAULT NULL,
			responseMessage longtext DEFAULT NULL,
			responseStatus varchar(200) DEFAULT NULL,
			responseStatusCode varchar(200) DEFAULT NULL,
			responseObject longtext DEFAULT NULL,
			orderItems longtext DEFAULT NULL,
			trackingNumber longtext DEFAULT NULL,
			trackingUrl longtext DEFAULT NULL,
			carrierName longtext DEFAULT NULL,
			time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
			PRIMARY KEY  (id)
		) $charset_collate;";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
		
		// Schedule an action if it's not already scheduled
		if ( ! wp_next_scheduled( 'adv_fep_isa_add_every_thirty_minute' ) ) {
			wp_schedule_event( time(), 'every_thirty_minute', 'adv_fep_isa_add_every_thirty_minute' );
		}
		
		if ( ! wp_next_scheduled( 'adv_fep_daily_at_midnight_actions' ) ) {
			$local_time_to_run = date('Y-m-d 00:00:00', strtotime('+1day'));
			// $timestamp = strtotime( $local_time_to_run ) - ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS );
			$timestamp = strtotime( $local_time_to_run );
			wp_schedule_event(
				$timestamp,
				'every_day_midnight',
				'adv_fep_daily_at_midnight_actions'
			);
		}
		
	}
	register_activation_hook( __FILE__, 'adv_fep_install' );

	/**
	 * Plugin Deactivation hook
	 */
	function adv_fep_my_deactivation() {
		wp_clear_scheduled_hook( 'adv_fep_isa_add_every_thirty_minute' );
		wp_clear_scheduled_hook( 'adv_fep_daily_at_midnight_actions' );
	}
	register_deactivation_hook( __FILE__, 'adv_fep_my_deactivation' );
