<style>
	.pass-container {
		position: relative;
	}
	.pass-icons {
		position: absolute;
		left: 325px;
		top: 5px;
		cursor: pointer;
	}
	.pass-container input {
		padding-right: 30px;
	}
	.settings-toggle {
		display: flex;
		justify-content: center;
		align-items: center;
		width: 50%;
	}
	.settings-toggle .toggle {
		padding: 10px;
		margin: 10px;
		background: #fff
	}
	.settings-toggle .toggle label {
		background: #fff;
		padding: 10px 30px;
	}
	
</style>
<div class="wrap">

	<h1><?php esc_html_e( 'Advatix Settings', 'advatix-fep-plugin' ); ?></h1><hr>

	<form method="post" action="options.php" id="settings-form" style="position:relative">
		
		<?php settings_fields( 'theme_options' ); ?>
		
		<?php $value = self::get_theme_option( 'api_settings' ); ?>

		<div class="settings-toggle">
			<div class="toggle">
				<input type="radio" name="theme_options[api_settings]" value="fep" id="fep" class="api_settings" required <?php echo $value=='fep'?'checked':''; ?> />
				<label for="fep">FEP Layer</label>
			</div>
			
			<div class="toggle">
				<input type="radio" name="theme_options[api_settings]" value="omni" id="omni" class="api_settings" required <?php echo $value=='omni'?'checked':''; ?> />
				<label for="omni">Omni Layer</label>
			</div>
		</div>

		<table class="form-table wpex-custom-admin-login-table">

			<tr valign="top">
				<th scope="row"><?php esc_html_e( 'Client Name', 'advatix-fep-plugin' ); ?></th>
				<td>
					<?php $value = self::get_theme_option( 'client_name' ); ?>
					<input class="regular-text" type="text" name="theme_options[client_name]" id="client_name" value="<?php echo esc_attr( $value ); ?>">
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php esc_html_e( 'Company Name', 'advatix-fep-plugin' ); ?></th>
				<td>
					<?php $value = self::get_theme_option( 'company_name' ); ?>
					<input class="regular-text" type="text" name="theme_options[company_name]" id="company_name" value="<?php echo esc_attr( $value ); ?>">
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php esc_html_e( 'LOB', 'advatix-fep-plugin' ); ?></th>
				<td>
					<?php $value = self::get_theme_option( 'input_lob' ); ?>
					<?php $value = self::get_theme_option( 'input_lob' ); ?>
					<input class="regular-text" type="text" name="theme_options[input_lob]" id="input_lob" value="<?php echo esc_attr( $value ); ?>">
					<!--<select class="regular-text" name="theme_options[input_lob]">
						<option value="">-- Select LOB --</option>
						<option value="3" <?php //echo esc_attr( $value )==3?'selected':''; ?>>D2C</option>
						<option value="9" <?php //echo esc_attr( $value )==9?'selected':''; ?>>B2B</option>
						<option value="2" <?php //echo esc_attr( $value )==2?'selected':''; ?>>Micro kitchen</option>
					</select>-->
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php esc_html_e( 'API Key', 'advatix-fep-plugin' ); ?></th>
				<td>
					<div class="pass-container">
						<?php $value = self::get_theme_option( 'input_api_key' ); ?>
						<input class="regular-text" type="password" name="theme_options[input_api_key]" id="apiKey" value="<?php echo esc_attr( $value ); ?>">
						<span class="dashicons dashicons-visibility pass-icons"></span>
					</div>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php esc_html_e( 'API Base Url', 'advatix-fep-plugin' ); ?></th>
				<td>
					<?php $value = self::get_theme_option( 'input_api_url' ); ?>
					<input class="regular-text" type="text" placeholder="https://xyz.xpdel.com" id="apiUrl" name="theme_options[input_api_url]" value="<?php echo esc_attr( $value ); ?>">
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php esc_html_e( 'Account ID', 'advatix-fep-plugin' ); ?></th>
				<td>
					<?php $value = self::get_theme_option( 'account_id' ); ?>
					<input class="regular-text" type="text" name="theme_options[account_id]" id="accountId" value="<?php echo esc_attr( $value ); ?>">
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php esc_html_e( 'Sync Inventory', 'advatix-fep-plugin' ); ?></th>
				<td>
					<?php $value1 = self::get_theme_option( 'sync_inventory' ); ?>
					<select class="regular-text" name="theme_options[sync_inventory]" onchange="changeSyncInventory(this.value)">
						<option value="0" <?php echo esc_attr( $value1 )==0?'selected':''; ?>>No</option>
						<option value="1" <?php echo esc_attr( $value1 )==1?'selected':''; ?>>Yes</option>
					</select>
				</td>
			</tr>
			<tr valign="top" id="warehouse_row" <?php if($value1!=1){ ?>style="display:none"<?php } ?>>
				<th scope="row"><?php esc_html_e( 'Warehouse IDs', 'advatix-fep-plugin' ); ?></th>
				<td>
					<?php $value = self::get_theme_option( 'warehouse_ids' ); ?>
					<input class="regular-text" id="warehouse_ids" type="text" name="theme_options[warehouse_ids]" placeholder="Enter Comma separated IDs" value="<?php echo esc_attr( $value ); ?>" <?php if($value1==1){ ?>required<?php } ?>>
				</td>
			</tr>
			<!-- <tr valign="top">
				<th scope="row"><?php esc_html_e( 'Order Update API', 'advatix-fep-plugin' ); ?></th>
				<td>
					<?php $value = self::get_theme_option( 'update_api' ); ?>
					<select class="regular-text" name="theme_options[update_api]">
						<option value="0" <?php echo esc_attr( $value )==0?'selected':''; ?>>No</option>
						<option value="1" <?php echo esc_attr( $value )==1?'selected':''; ?>>Yes</option>
					</select>
				</td>
			</tr> -->

		</table><hr>

		<?php submit_button(); ?>
		
		<span id="validating" style="display:none;position: absolute;bottom: 26px;left: 115px;">Validating API Credentials ...</span>

	</form>

</div>
<script>
	jQuery(document).ready(function(){
		jQuery('#settings-form').submit(function(e){
			e.preventDefault();
		});
		
		jQuery('#submit').click(function(){
			var api_settings = jQuery('.api_settings:checked').val();
			var apiUrl = jQuery('#apiUrl').val();
			var accountId = jQuery('#accountId').val();
			var apiKey = jQuery('#apiKey').val();
			var company_name = jQuery('#company_name').val();
			
			if(api_settings == 'fep'){
				jQuery('#validating').show();
				jQuery.ajax({
					type: "post",
					url: "<?php echo admin_url( 'admin-ajax.php' ); ?>",
					data: {action: "adv_validate_fep_api_details", accountId: accountId, apiKey: apiKey, apiUrl: apiUrl },
					success: function(res){
						console.log(res);
						
						if(res == 'failed'){
							alert('Invalid API Credentials');
							jQuery('#validating').hide();
						}else{
							// alert('Valid API Credentials');
							jQuery('#settings-form').unbind('submit').submit();
							jQuery('#submit').trigger('click');
							jQuery('#submit').prop('disabled', true);
						}
					},
					error: function(jqXHR, textStatus, errorThrown) {
						console.log(textStatus, errorThrown);
						alert('It seems something not right. Please try again later.');
						setTimeout(function(){ location.reload(); }, 1000);
					}
				});
			}else{
				jQuery('#validating').show();
				jQuery.ajax({
					type: "post",
					url: "<?php echo admin_url( 'admin-ajax.php' ); ?>",
					data: {action: "adv_validate_omni_api_details", accountId: accountId, apiKey: apiKey, apiUrl: apiUrl, company_name: company_name },
					success: function(res){
						// console.log(res);return;
						
						if(res == 'failed'){
							alert('Invalid API Credentials');
							jQuery('#validating').hide();
						}else{
							// alert('Valid API Credentials');
							jQuery('#settings-form').unbind('submit').submit();
							jQuery('#submit').trigger('click');
							jQuery('#submit').prop('disabled', true);
						}
					},
					error: function(jqXHR, textStatus, errorThrown) {
						console.log(textStatus, errorThrown);
						alert('It seems something not right. Please try again later.');
						setTimeout(function(){ location.reload(); }, 1000);
					}
				});
			}
		});
		
		jQuery('.pass-icons').click(function(){
			if(jQuery(this).hasClass('dashicons-visibility')){
				jQuery(this).removeClass('dashicons-visibility');
				jQuery(this).addClass('dashicons-hidden');
				jQuery('input[name="theme_options[input_api_key]"]').attr('type','text');
			}else{
				jQuery(this).removeClass('dashicons-hidden');
				jQuery(this).addClass('dashicons-visibility');
				jQuery('input[name="theme_options[input_api_key]"]').attr('type','password');
			}
		});
	});
	
	function changeSyncInventory(value){
		if(value == 1){
			jQuery('#warehouse_row').slideDown('slow');
			jQuery('#warehouse_ids').prop('required',true);
		}else{
			jQuery('#warehouse_row').slideUp('slow');
			jQuery('#warehouse_ids').prop('required',false);
		}
	}
</script>