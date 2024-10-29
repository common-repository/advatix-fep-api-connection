<?php
	/**
	 * Custom Cron Schedules
	 */
	add_filter( 'cron_schedules', 'adv_fep_isa_add_every_thirty_minute' );
	function adv_fep_isa_add_every_thirty_minute( $schedules ) {
		$schedules['every_thirty_minute'] = array(
				'interval'  => 1800,
				'display'   => __( 'Advatix - Every Thirty Minutes', 'textdomain' )
		);
		
		$schedules['every_day_midnight'] = array(
				'interval'  => 86400,
				'display'   => __( 'Advatix - Daily Midnight', 'textdomain' )
		);
		return $schedules;
	}