<?php	
	
	/**
	 * Register new woocommerce status
	 */
	function adv_fep_register_created_order_status() {
		register_post_status( 'wc-created', array(
			'label'                     => 'Created',
			'public'                    => true,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop( 'Created (%s)', 'Created (%s)' )
		) );
		
		register_post_status( 'wc-assigned', array(
			'label'                     => 'Assigned',
			'public'                    => true,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop( 'Assigned (%s)', 'Assigned (%s)' )
		) );
		
		register_post_status( 'wc-picked', array(
			'label'                     => 'Picked',
			'public'                    => true,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop( 'Picked (%s)', 'Picked (%s)' )
		) );
		
		register_post_status( 'wc-packed', array(
			'label'                     => 'Packed',
			'public'                    => true,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop( 'Packed (%s)', 'Packed (%s)' )
		) );
		
		register_post_status( 'wc-shipped', array(
			'label'                     => 'Shipped',
			'public'                    => true,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop( 'Shipped (%s)', 'Shipped (%s)' )
		) );
		
		register_post_status( 'wc-intransit', array(
			'label'                     => 'In Transit',
			'public'                    => true,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop( 'In Transit (%s)', 'In Transit (%s)' )
		) );
		
		register_post_status( 'wc-outfordelivery', array(
			'label'                     => 'Out for Delivery',
			'public'                    => true,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop( 'Out for Delivery (%s)', 'Out for Delivery (%s)' )
		) );
		
		register_post_status( 'wc-delivered', array(
			'label'                     => 'Delivered',
			'public'                    => true,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop( 'Delivered (%s)', 'Delivered (%s)' )
		) );
		
		register_post_status( 'wc-processing', array(
			'label'                     => 'Processing',
			'public'                    => true,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop( 'Processing (%s)', 'Processing (%s)' )
		) );
		
		register_post_status( 'wc-pending', array(
			'label'                     => 'Pending',
			'public'                    => true,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop( 'Pending (%s)', 'Pending (%s)' )
		) );
		
		register_post_status( 'wc-on-hold', array(
			'label'                     => 'On hold',
			'public'                    => true,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop( 'On hold (%s)', 'On hold (%s)' )
		) );
		
		register_post_status( 'wc-completed', array(
			'label'                     => 'Completed',
			'public'                    => true,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop( 'Completed (%s)', 'Completed (%s)' )
		) );
		
		register_post_status( 'wc-refunded', array(
			'label'                     => 'Refunded',
			'public'                    => true,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop( 'Refunded (%s)', 'Refunded (%s)' )
		) );
	}
	add_action( 'init', 'adv_fep_register_created_order_status' );


	/**
	 * Add to list of WC Order statuses
	 */
	function adv_fep_add_created_to_order_statuses( $order_statuses ) {
	 
		$new_order_statuses = array();
	 
		$new_order_statuses['wc-created'] = 'Created';
		$new_order_statuses['wc-assigned'] = 'Assigned';
		$new_order_statuses['wc-picked'] = 'Picked';
		$new_order_statuses['wc-packed'] = 'Packed';
		$new_order_statuses['wc-shipped'] = 'Shipped';
		$new_order_statuses['wc-intransit'] = 'In Transit';
		$new_order_statuses['wc-outfordelivery'] = 'Out for Delivery';
		$new_order_statuses['wc-delivered'] = 'Delivered';
		$new_order_statuses['wc-cancelled'] = 'Cancelled';
		$new_order_statuses['wc-processing'] = 'Processing';
		$new_order_statuses['wc-pending'] = 'Pending';
		$new_order_statuses['wc-on-hold'] = 'On hold';
		$new_order_statuses['wc-completed'] = 'Completed';
		$new_order_statuses['wc-refunded'] = 'Refunded';
			
		return $new_order_statuses;
	}
	add_filter( 'wc_order_statuses', 'adv_fep_add_created_to_order_statuses' );

	
	/**
	 * Custom Order Status Notification email
	 */
	
	add_filter( 'woocommerce_email_actions', 'custom_email_actions', 20, 1 );
	function custom_email_actions( $action ) {
		$actions[] = 'woocommerce_order_status_wc-created';
		$actions[] = 'woocommerce_order_status_wc-assigned';
		$actions[] = 'woocommerce_order_status_wc-picked';
		$actions[] = 'woocommerce_order_status_wc-packed';
		$actions[] = 'woocommerce_order_status_wc-shipped';
		$actions[] = 'woocommerce_order_status_wc-intransit';
		$actions[] = 'woocommerce_order_status_wc-outfordelivery';
		$actions[] = 'woocommerce_order_status_wc-delivered';
		$actions[] = 'woocommerce_order_status_wc-cancelled';
		
		return $actions;
	}

	add_action( 'woocommerce_order_status_wc-created', 'send_transactional_email' , 10, 1 );
	add_action( 'woocommerce_order_status_wc-assigned', 'send_transactional_email' , 10, 1 );
	add_action( 'woocommerce_order_status_wc-picked', 'send_transactional_email' , 10, 1 );
	add_action( 'woocommerce_order_status_wc-packed', 'send_transactional_email' , 10, 1 );
	add_action( 'woocommerce_order_status_wc-shipped', 'send_transactional_email' , 10, 1 );
	add_action( 'woocommerce_order_status_wc-intransit', 'send_transactional_email' , 10, 1 );
	add_action( 'woocommerce_order_status_wc-outfordelivery', 'send_transactional_email' , 10, 1 );
	add_action( 'woocommerce_order_status_wc-delivered', 'send_transactional_email' , 10, 1 );
	add_action( 'woocommerce_order_status_wc-cancelled', 'send_transactional_email' , 10, 1 );

	// Sending an email notification when order get 'created' status
	add_action('woocommerce_order_status_created', 'backorder_status_created_custom_notification', 20, 2);
	function backorder_status_created_custom_notification( $order_id, $order ) {
		// HERE below your settings
		$heading   = __('Thank you for your order','woocommerce');
		$subject   = '[{site_title}] Created order ({order_number}) - {order_date}';

		// Get WooCommerce email objects
		$mailer = WC()->mailer()->get_emails();
	  
		// Use one of the active emails e.g. "Customer_Completed_Order"
		// Wont work if you choose an object that is not active
		// Assign heading & subject to chosen object
		$mailer['WC_Email_Customer_Processing_Order']->heading = $heading;
		$mailer['WC_Email_Customer_Processing_Order']->settings['heading'] = $heading;
		$mailer['WC_Email_Customer_Processing_Order']->subject = $subject;
		$mailer['WC_Email_Customer_Processing_Order']->settings['subject'] = $subject;
	  
		// Send the email with custom heading & subject
		$mailer['WC_Email_Customer_Processing_Order']->trigger( $order_id );
	}
	
	// Sending an email notification when order get 'assigned' status
	add_action('woocommerce_order_status_assigned', 'backorder_status_assigned_custom_notification', 20, 2);
	function backorder_status_assigned_custom_notification( $order_id, $order ) {
		$heading   = __('Order Assigned','woocommerce');
		$subject = '[{site_title}] Assigned order ({order_number}) - {order_date}';
	  
		// Get WooCommerce email objects
		$mailer = WC()->mailer()->get_emails();
	  
		// Use one of the active emails e.g. "Customer_Completed_Order"
		// Wont work if you choose an object that is not active
		// Assign heading & subject to chosen object
		$mailer['WC_Email_Customer_Processing_Order']->heading = $heading;
		$mailer['WC_Email_Customer_Processing_Order']->settings['heading'] = $heading;
		$mailer['WC_Email_Customer_Processing_Order']->subject = $subject;
		$mailer['WC_Email_Customer_Processing_Order']->settings['subject'] = $subject;
	  
		// Send the email with custom heading & subject
		$mailer['WC_Email_Customer_Processing_Order']->trigger( $order_id );
	}

	// Sending an email notification when order get 'picked' status
	add_action('woocommerce_order_status_picked', 'backorder_status_picked_custom_notification', 20, 2);
	function backorder_status_picked_custom_notification( $order_id, $order ) {
		// HERE below your settings
		$heading   = __('Order Picked','woocommerce');
		$subject   = '[{site_title}] Picked order ({order_number}) - {order_date}';

		// Get WooCommerce email objects
		$mailer = WC()->mailer()->get_emails();
	  
		// Use one of the active emails e.g. "Customer_Completed_Order"
		// Wont work if you choose an object that is not active
		// Assign heading & subject to chosen object
		$mailer['WC_Email_Customer_Processing_Order']->heading = $heading;
		$mailer['WC_Email_Customer_Processing_Order']->settings['heading'] = $heading;
		$mailer['WC_Email_Customer_Processing_Order']->subject = $subject;
		$mailer['WC_Email_Customer_Processing_Order']->settings['subject'] = $subject;
	  
		// Send the email with custom heading & subject
		$mailer['WC_Email_Customer_Processing_Order']->trigger( $order_id );
	}

	// Sending an email notification when order get 'packed' status
	add_action('woocommerce_order_status_packed', 'backorder_status_packed_custom_notification', 20, 2);
	function backorder_status_packed_custom_notification( $order_id, $order ) {
		// HERE below your settings
		$heading   = __('Order Packed','woocommerce');
		$subject   = '[{site_title}] Packed order ({order_number}) - {order_date}';

		// Get WooCommerce email objects
		$mailer = WC()->mailer()->get_emails();
	  
		// Use one of the active emails e.g. "Customer_Completed_Order"
		// Wont work if you choose an object that is not active
		// Assign heading & subject to chosen object
		$mailer['WC_Email_Customer_Processing_Order']->heading = $heading;
		$mailer['WC_Email_Customer_Processing_Order']->settings['heading'] = $heading;
		$mailer['WC_Email_Customer_Processing_Order']->subject = $subject;
		$mailer['WC_Email_Customer_Processing_Order']->settings['subject'] = $subject;
	  
		// Send the email with custom heading & subject
		$mailer['WC_Email_Customer_Processing_Order']->trigger( $order_id );
	}

	// Sending an email notification when order get 'shipped' status
	add_action('woocommerce_order_status_shipped', 'backorder_status_shipped_custom_notification', 20, 2);
	function backorder_status_shipped_custom_notification( $order_id, $order ) {
		// HERE below your settings
		$heading   = __('Order Shipped','woocommerce');
		$subject   = '[{site_title}] Shipped order ({order_number}) - {order_date}';

		// Get WooCommerce email objects
		$mailer = WC()->mailer()->get_emails();
	  
		// Use one of the active emails e.g. "Customer_Completed_Order"
		// Wont work if you choose an object that is not active
		// Assign heading & subject to chosen object
		$mailer['WC_Email_Customer_Processing_Order']->heading = $heading;
		$mailer['WC_Email_Customer_Processing_Order']->settings['heading'] = $heading;
		$mailer['WC_Email_Customer_Processing_Order']->subject = $subject;
		$mailer['WC_Email_Customer_Processing_Order']->settings['subject'] = $subject;
	  
		// Send the email with custom heading & subject
		$mailer['WC_Email_Customer_Processing_Order']->trigger( $order_id );
	}

	// Sending an email notification when order get 'intransit' status
	add_action('woocommerce_order_status_intransit', 'backorder_status_intransit_custom_notification', 20, 2);
	function backorder_status_intransit_custom_notification( $order_id, $order ) {
		// HERE below your settings
		$heading   = __('Order In-Transit','woocommerce');
		$subject   = '[{site_title}] In-Transit order ({order_number}) - {order_date}';

		// Get WooCommerce email objects
		$mailer = WC()->mailer()->get_emails();
	  
		// Use one of the active emails e.g. "Customer_Completed_Order"
		// Wont work if you choose an object that is not active
		// Assign heading & subject to chosen object
		$mailer['WC_Email_Customer_Processing_Order']->heading = $heading;
		$mailer['WC_Email_Customer_Processing_Order']->settings['heading'] = $heading;
		$mailer['WC_Email_Customer_Processing_Order']->subject = $subject;
		$mailer['WC_Email_Customer_Processing_Order']->settings['subject'] = $subject;
	  
		// Send the email with custom heading & subject
		$mailer['WC_Email_Customer_Processing_Order']->trigger( $order_id );
	}

	// Sending an email notification when order get 'outfordelivery' status
	add_action('woocommerce_order_status_outfordelivery', 'backorder_status_outfordelivery_custom_notification', 20, 2);
	function backorder_status_outfordelivery_custom_notification( $order_id, $order ) {
		// HERE below your settings
		$heading   = __('Order out for delivery','woocommerce');
		$subject   = '[{site_title}] Out for Delivery order ({order_number}) - {order_date}';

		// Get WooCommerce email objects
		$mailer = WC()->mailer()->get_emails();
	  
		// Use one of the active emails e.g. "Customer_Completed_Order"
		// Wont work if you choose an object that is not active
		// Assign heading & subject to chosen object
		$mailer['WC_Email_Customer_Processing_Order']->heading = $heading;
		$mailer['WC_Email_Customer_Processing_Order']->settings['heading'] = $heading;
		$mailer['WC_Email_Customer_Processing_Order']->subject = $subject;
		$mailer['WC_Email_Customer_Processing_Order']->settings['subject'] = $subject;
	  
		// Send the email with custom heading & subject
		$mailer['WC_Email_Customer_Processing_Order']->trigger( $order_id );
	}

	// Sending an email notification when order get 'delivered' status
	add_action('woocommerce_order_status_delivered', 'backorder_status_delivered_custom_notification', 20, 2);
	function backorder_status_delivered_custom_notification( $order_id, $order ) {
		// HERE below your settings
		$heading   = __('Order Delivered','woocommerce');
		$subject   = '[{site_title}] Delivered order ({order_number}) - {order_date}';

		// Get WooCommerce email objects
		$mailer = WC()->mailer()->get_emails();
	  
		// Use one of the active emails e.g. "Customer_Completed_Order"
		// Wont work if you choose an object that is not active
		// Assign heading & subject to chosen object
		$mailer['WC_Email_Customer_Processing_Order']->heading = $heading;
		$mailer['WC_Email_Customer_Processing_Order']->settings['heading'] = $heading;
		$mailer['WC_Email_Customer_Processing_Order']->subject = $subject;
		$mailer['WC_Email_Customer_Processing_Order']->settings['subject'] = $subject;
	  
		// Send the email with custom heading & subject
		$mailer['WC_Email_Customer_Processing_Order']->trigger( $order_id );
	}
	
	// Sending an email notification when order get 'cancelled' status
	add_action('woocommerce_order_status_cancelled', 'backorder_status_cancelled_custom_notification', 20, 2);
	function backorder_status_cancelled_custom_notification( $order_id, $order ) {
		// HERE below your settings
		$heading   = __('Order Cancelled: #{order_number}','woocommerce');
		$subject   = '[{site_title}]: Order #{order_number} has been cancelled';
		
		$customer_email = $order->get_billing_email();

		// Get WooCommerce email objects
		$mailer = WC()->mailer()->get_emails();
	  
		// Use one of the active emails e.g. "Customer_Completed_Order"
		// Wont work if you choose an object that is not active
		// Assign heading & subject to chosen object
		$mailer['WC_Email_Cancelled_Order']->heading = $heading;
		$mailer['WC_Email_Cancelled_Order']->settings['heading'] = $heading;
		$mailer['WC_Email_Cancelled_Order']->subject = $subject;
		$mailer['WC_Email_Cancelled_Order']->settings['subject'] = $subject;
		
		$mailer['WC_Email_Cancelled_Order']->recipient = $customer_email;
	  
		// Send the email with custom heading & subject
		$mailer['WC_Email_Cancelled_Order']->trigger( $order_id );
	}