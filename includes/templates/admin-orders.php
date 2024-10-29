<?php
	error_reporting(0);

	global $wpdb, $woocommerce;
	
	$folder = stat(__DIR__);
	
	$orders1 = get_posts(apply_filters('woocommerce_my_account_my_orders_query', array(
		'numberposts' => -1,
		'post_type' => wc_get_order_types('view-orders'),
		'post_status' => array_keys(wc_get_order_statuses())
	)));
	$total_records = count($orders1);
	$posts_per_page = 20;
	$total_pages = ceil($total_records / $posts_per_page);
	$paged = ( sanitize_text_field($_GET['page_no']) ) ? sanitize_text_field($_GET['page_no']) : 1;
	$orders = get_posts(array(
		'post_type' => wc_get_order_types('view-orders'),
		'posts_per_page' => $posts_per_page,
		'paged' => $paged,
		'post_status' => array_keys(wc_get_order_statuses())
	));
	
	// $update_api = advatix_api_option('update_api');
	?>
	<style>
		.pagination {
			float: right;
			padding: 20px 0;
		}
		.pagination .page-numbers {
			padding: 5px 10px;
			background: #fff;
			border: 1px solid;
			border-radius: 2px;
		}
		
		.fep-status{
			color: #fff;
			padding: 5px;
			font-size: 11px;
			border-radius: 3px;
		}
		
		.fep-status.success{
			background-color: #4CAF50;
		}
		.fep-status.warning{
			background-color: #ffa500;
		}
		.fep-status.failed{
			background-color: #f44336;
		}
		
		.bulk-action {
			padding: 10px 0;
		}
		
		/* The Modal (background) */
		.modal {
		  display: none; /* Hidden by default */
		  position: fixed; /* Stay in place */
		  z-index: 9999; /* Sit on top */
		  padding-top: 100px; /* Location of the box */
		  left: 0;
		  top: 0;
		  width: 100%; /* Full width */
		  height: 100%; /* Full height */
		  overflow: auto; /* Enable scroll if needed */
		  background-color: rgb(0,0,0); /* Fallback color */
		  background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
		}

		/* Modal Content */
		.modal-content {
		  background-color: #fefefe;
		  margin: auto;
		  padding: 20px;
		  border: 1px solid #888;
		  width: 60%;
		}

		/* The Close Button */
		.close {
		  color: #aaaaaa;
		  float: right;
		  font-size: 28px;
		  font-weight: bold;
		}

		.close:hover,
		.close:focus {
		  color: #000;
		  text-decoration: none;
		  cursor: pointer;
		}
		
		.loader {
		  border: 4px solid #f3f3f3;
		  border-radius: 50%;
		  border-top: 4px solid;
		  border-bottom: 4px solid;
		  width: 40px;
		  height: 40px;
		  -webkit-animation: spin 2s linear infinite;
		  animation: spin 2s linear infinite;
		}

		@-webkit-keyframes spin {
		  0% { -webkit-transform: rotate(0deg); }
		  100% { -webkit-transform: rotate(360deg); }
		}

		@keyframes spin {
		  0% { transform: rotate(0deg); }
		  100% { transform: rotate(360deg); }
		}
	</style>
	<div class="wrap">

		<h1><?php esc_html_e( 'Orders Sync', 'advatix-fep-plugin' ); ?></h1><hr>
		<!--<div class="bulk-action">
			<button onclick="syncAllOrderFep()" class="button button-primary">Sync Orders</button>
		</div>-->
		<table class="wp-list-table widefat fixed striped table-view-list posts">
			<thead>
				<tr>
					<!--<th style="width: 1%;" scope="col" class="manage-column column-order_number column-primary">
						<input style="margin:0" type="checkbox" name="checkall" class="checkall" value="" />
					</th>-->
					<th scope="col" class="manage-column column-order_number column-primary">
						Order
					</th>
					<th scope="col" class="manage-column column-order_number column-primary">
						Date
					</th>
					<th scope="col" class="manage-column column-order_number column-primary">
						API Status
					</th>
					<th scope="col" class="manage-column column-order_number column-primary">
						API Response
					</th>
					<th scope="col" class="manage-column column-order_number column-primary">
						Action
					</th>
				</tr>
			</thead>

			<tbody id="the-list">
				<?php foreach($orders as $k=>$v){ ?>
				
				<?php $order = wc_get_order( $v->ID ); ?>
				<?php
					if ( !is_a( $order, 'WC_Order_Refund' ) ) {
						// $order = wc_get_order( $order->get_parent_id() );
					// }
				?>
				<?php
					$table_name = $wpdb->base_prefix . 'fep_api_order_resp';
					$q = $wpdb->get_results('SELECT * FROM '.$table_name.' WHERE order_id="'.$order->get_id().'" ORDER BY id DESC');
					
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
					
				?>
				<tr class="">
					<!--<td>
						<?php if($synced == false && $warning == false){ ?>
						<input type="checkbox" name="order_ids[]" class="order_ids" value="<?php echo esc_html_e($v->ID, 'advatix-fep-plugin'); ?>" />
						<?php } ?>
					</td>-->
					<td><?php echo esc_html_e('#'.$v->ID.' '.$order->get_billing_first_name().' '.$order->get_billing_last_name(), 'advatix-fep-plugin'); ?></td>
					<td><?php echo date('M d, Y', strtotime($v->post_date)); ?></td>
					<td>
						<?php if($synced){ ?>
						<span class="fep-status <?php echo $warning==true?'warning':'success'; ?>">Synced</span>
						<?php }else{ ?>
						<span class="fep-status failed">Not Synced</span>
						<?php } ?>
					</td>
					<td><a href="javascript:viewDetails('<?php echo esc_html_e($v->ID, 'advatix-fep-plugin'); ?>')" >View Response</a></td>
					<td>
						<?php if($synced == true || $warning == true){ ?>
						
							<!--<button onclick="syncUpdateOrderFep('<?php echo esc_html_e($v->ID, 'advatix-fep-plugin'); ?>')" class="button button-primary">Update Order</button>-->
						
						<?php }else{ ?>
							<button onclick="syncOrderFep('<?php echo esc_html_e($v->ID, 'advatix-fep-plugin'); ?>')" class="button button-primary">Sync Order</button>
						<?php } ?>
					</td>
				</tr>
				<?php } } ?>
			</tbody>

		</table>
		<div class="pagination">
			<?php
				$args = array(
					'base' => @add_query_arg('page_no', '%#%'),
					'total' => $total_pages,
					'current' => $paged,
					'show_all' => False,
					'end_size' => 2,
					'mid_size' => 2,
					'prev_next' => True,
					'prev_text' => __('&laquo; Previous'),
					'next_text' => __('Next &raquo;'),
					'type' => 'plain',
					'add_args' => False,
					'add_fragment' => ''
				);
				echo paginate_links($args);
			?>
		</div>
		
		<div id="detailsModal" class="modal">

			<div class="modal-content">
				<span class="close">&times;</span>
				<h3>Order <span id="order-id"></span> - API Status</h3>
				<table class="wp-list-table widefat fixed striped table-view-list posts">
					<thead>
						<tr>
							<th scope="col" class="manage-column column-order_number column-primary">
								Date
							</th>
							<th scope="col" class="manage-column column-order_number column-primary">
								Response Status Code
							</th>
							<th scope="col" class="manage-column column-order_number column-primary">
								Response Message
							</th>
							<th scope="col" class="manage-column column-order_number column-primary">
								Type
							</th>
						</tr>
					</thead>

					<tbody id="fep-status-list">
						<tr>
							<td colspan="4" align="center"><div class="loader"></div></td>
						</tr>
					</tbody>

				</table>
			</div>

		</div>
		
		<div id="syncModal" class="modal">

			<div class="modal-content">
				<span class="close" style="display:none">&times;</span>
				<h3>Syncing Order <span id="order-id1"></span></h3>
				<table class="wp-list-table widefat fixed striped table-view-list posts">
					
					<tbody id="fep-sync-status">
						<tr>
							<td align="center"><div class="loader"></div></span></td>
						</tr>
					</tbody>

				</table>
			</div>

		</div>
		
		<div id="syncAllModal" class="modal">

			<div class="modal-content">
				<span class="close" style="display:none">&times;</span>
				<h3>Syncing Orders</h3>
				<table class="wp-list-table widefat fixed striped table-view-list posts">
					
					<tbody id="fep-sync-status2">
						<tr>
							<td align="center"><div class="loader"></div></span></td>
						</tr>
					</tbody>

				</table>
			</div>

		</div>
	</div>
	
	<script>
		jQuery(".checkall").click(function(){
			jQuery('input:checkbox').not(this).prop('checked', this.checked);
		});
	
		function syncAllOrderFep(){
			var order_count = jQuery('.order_ids').filter(':checked').length;
			
			if(order_count < 1){
				alert('Please select any order');
			}else{
				var order_ids = jQuery('.order_ids').filter(':checked').map(function(){return jQuery(this).val();}).get();
				
				jQuery('#syncAllModal').show();
				jQuery.ajax({
					type: "post",
					url: "<?php echo admin_url( 'admin-ajax.php' ); ?>",
					data: {action: "adv_sync_fep_all_order_details", order_ids: order_ids },
					success: function(res){
						
						jQuery('#fep-sync-status2').html('<tr><td align="center">Orders Synced successfully.</td></tr>');
						
						setTimeout(function(){ location.reload(); }, 2000);
					}
				});
			}
		}
		
		function syncOrderFep(order_id){
			jQuery('#order-id1').html('#'+order_id);
			jQuery('#syncModal').show();
			jQuery.ajax({
				type: "post",
				url: "<?php echo admin_url( 'admin-ajax.php' ); ?>",
				data: {action: "adv_sync_fep_order_details", order_id: order_id },
				success: function(res){
					// console.log(res);return;
					var rss = JSON.parse(res);
					
					if(rss.responseCode == '200'){
						jQuery('#fep-sync-status').html('<tr><td align="center">Order Synced successfully.</td></tr>');
					}else{
						jQuery('#fep-sync-status').html('<tr><td align="center">'+rss.responseMessage+'</td></tr>');
					}
					
					setTimeout(function(){ location.reload(); }, 2000);
				},
				error: function(jqXHR, textStatus, errorThrown) {
					console.log(textStatus, errorThrown);
					alert('It seems something not right. Please try again later.');
					setTimeout(function(){ location.reload(); }, 1000);
				}
			});
			
		}
		
		function syncUpdateOrderFep(order_id){
			jQuery('#order-id1').html('#'+order_id);
			jQuery('#syncModal').show();
			jQuery.ajax({
				type: "post",
				url: "<?php echo admin_url( 'admin-ajax.php' ); ?>",
				data: {action: "adv_update_fep_order_details", order_id: order_id },
				success: function(res){
					// console.log(res);return;
					var rss = JSON.parse(res);
					
					if(rss.responseCode == '200'){
						jQuery('#fep-sync-status').html('<tr><td align="center">Order Updated successfully.</td></tr>');
					}else{
						jQuery('#fep-sync-status').html('<tr><td align="center">'+rss.responseMessage+'</td></tr>');
					}
					
					setTimeout(function(){ location.reload(); }, 2000);
				},
				error: function(jqXHR, textStatus, errorThrown) {
					console.log(textStatus, errorThrown);
					alert('It seems something not right. Please try again later.');
					setTimeout(function(){ location.reload(); }, 1000);
				}
			});
			
		}
		
		function viewDetails(order_id){
			jQuery('#order-id').html('#'+order_id);
			jQuery('#fep-status-list').html('<tr><td colspan="4" align="center"><div class="loader"></div></td></tr>');
			jQuery.ajax({
				type: "post",
				url: "<?php echo admin_url( 'admin-ajax.php' ); ?>",
				data: {action: "adv_get_fep_api_details", order_id: order_id },
				success: function(res){
					jQuery('#fep-status-list').html(res);
				}
			});
			
			jQuery('#detailsModal').show();
		}
		
		jQuery(document).ready(function(){
			jQuery('body').on('click','.copy-request', function(){
				var req = jQuery(this).attr('data-request');
				
				var $temp = jQuery("<input>");
				jQuery("body").append($temp);
				$temp.val(req).select();
				document.execCommand("copy");
				$temp.remove();
				
				jQuery(this).html('Copied!');
				setTimeout(function(){
					jQuery(this).html('Copy Request');
				}, 1000);
			})
		});
	
		// Get the modal
		var modal = document.getElementById("detailsModal");

		// Get the <span> element that closes the modal
		var span = document.getElementsByClassName("close")[0];

		// When the user clicks on <span> (x), close the modal
		span.onclick = function() {
			modal.style.display = "none";
		}

		// When the user clicks anywhere outside of the modal, close it
		window.onclick = function(event) {
			if (event.target == modal) {
				modal.style.display = "none";
			}
		}
		
	</script>