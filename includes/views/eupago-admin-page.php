<?php
/**
* EuPago Admin Page.
*/

function eupago_admin_menu() {
	add_submenu_page(
		'woocommerce',
		__( 'EuPago', 'eupago-gateway-for-woocommerce' ),
		__( 'EuPago', 'eupago-gateway-for-woocommerce' ),
		'manage_options',
		'eupago',
		'eupago_page_content',
		3
	);
}

add_action( 'admin_menu', 'eupago_admin_menu' );


function eupago_page_content() {
	if (sanitize_text_field(isset($_POST['eupago_save']))) {
		$channel 					= sanitize_text_field($_POST['channel']);
		$api_key 					= sanitize_text_field($_POST['api_key']);
		$endpoint 					= sanitize_text_field($_POST['endpoint']);
		$reminder 					= sanitize_text_field(isset($_POST['reminder'])) ? sanitize_text_field($_POST['reminder']) : '0';
		$debug 						= sanitize_text_field(isset($_POST['debug'])) ? sanitize_text_field($_POST['debug']) : 'no';
		$client_id 					= sanitize_text_field($_POST['client_id']);
		$client_secret 				= sanitize_text_field($_POST['client_secret']);
		$user_eupago 				= sanitize_text_field($_POST['user_eupago']);
		$password_eupago 			= sanitize_text_field($_POST['password_eupago']);
		$sms_enable 				= sanitize_text_field(isset($_POST['sms_enable'])) ? sanitize_text_field($_POST['sms_enable']) : 'no';
		$sms_payment_hold 			= sanitize_text_field(isset($_POST['sms_payment_hold'])) ? sanitize_text_field($_POST['sms_payment_hold']) : 'no';
		$sms_payment_confirmation 	= sanitize_text_field(isset($_POST['sms_payment_confirmation'])) ? sanitize_text_field($_POST['sms_payment_confirmation']) : 'no';
		$sms_order_confirmation 	= sanitize_text_field(isset($_POST['sms_order_confirmation'])) ? sanitize_text_field($_POST['sms_order_confirmation']) : 'no';
		$sms_intelidus_id 			= sanitize_text_field($_POST['sms_intelidus_id']);
		$sms_intelidus_api 			= sanitize_text_field($_POST['sms_intelidus_api']);
		$intelidus_sender 			= sanitize_text_field($_POST['intelidus_sender']);

		

		if (empty(get_option('eupago_channel'))) {
			delete_option('eupago_channel');
			add_option('eupago_channel', $channel, '', 'no');
		} else {
			update_option('eupago_channel', $channel);
		}

		if (empty(get_option('eupago_api_key'))) {
			delete_option('eupago_api_key');
			add_option('eupago_api_key', $api_key, '', 'yes');
		} else {
			update_option('eupago_api_key', $api_key);
		}

		if (empty(get_option('eupago_endpoint'))) {
			delete_option('eupago_endpoint');
			add_option('eupago_endpoint', $endpoint, '', 'yes');
		} else {
			update_option('eupago_endpoint', $endpoint);
		}

		if (empty(get_option('eupago_reminder'))) {
			delete_option('eupago_reminder');
			add_option('eupago_reminder', $reminder, '', 'yes');
		} else {
			update_option('eupago_reminder', $reminder);
		}

		if (empty(get_option('eupago_debug'))) {
			delete_option('eupago_debug');
			add_option('eupago_debug', $debug, '', 'yes');
		} else {
			update_option('eupago_debug', $debug);
		}

		if (empty(get_option('eupago_client_id'))) {
			delete_option('eupago_client_id');
			add_option('eupago_client_id', $client_id, '', 'yes');
		} else {
			update_option('eupago_client_id', $client_id);
		}

		if (empty(get_option('eupago_client_secret'))) {
			delete_option('eupago_client_secret');
			add_option('eupago_client_secret', $client_secret, '', 'yes');
		} else {
			update_option('eupago_client_secret', $client_secret);
		}

		if (empty(get_option('eupago_user'))) {
			delete_option('eupago_user');
			add_option('eupago_user', $user_eupago, '', 'yes');
		} else {
			update_option('eupago_user', $user_eupago);
		}

		if (empty(get_option('eupago_password'))) {
			delete_option('eupago_password');
			add_option('eupago_password', $password_eupago, '', 'yes');
		} else {
			update_option('eupago_password', $password_eupago);
		}

		if (empty(get_option('eupago_sms_enable'))) {
			delete_option('eupago_sms_enable');
			add_option('eupago_sms_enable', $sms_enable, '', 'yes');
		} else {
			update_option('eupago_sms_enable', $sms_enable);
		}

		if (empty(get_option('eupago_sms_payment_hold'))) {
			delete_option('eupago_sms_payment_hold');
			add_option('eupago_sms_payment_hold', $sms_payment_hold, '', 'yes');
		} else {
			update_option('eupago_sms_payment_hold', $sms_payment_hold);
		}

		if (empty(get_option('eupago_sms_payment_confirmation'))) {
			delete_option('eupago_sms_payment_confirmation');
			add_option('eupago_sms_payment_confirmation', $sms_payment_confirmation, '', 'yes');
		} else {
			update_option('eupago_sms_payment_confirmation', $sms_payment_confirmation);
		}

		if (empty(get_option('eupago_sms_order_confirmation'))) {
			delete_option('eupago_sms_order_confirmation');
			add_option('eupago_sms_order_confirmation', $sms_order_confirmation, '', 'yes');
		} else {
			update_option('eupago_sms_order_confirmation', $sms_order_confirmation);
		}


		if (empty(get_option('eupago_sms_intelidus_id'))) {
			delete_option('eupago_sms_intelidus_id');
			add_option('eupago_sms_intelidus_id', $sms_intelidus_id, '', 'yes');
		} else {
			update_option('eupago_sms_intelidus_id', $sms_intelidus_id);
		}

		if (empty(get_option('eupago_sms_intelidus_api'))) {
			delete_option('eupago_sms_intelidus_api');
			add_option('eupago_sms_intelidus_api', $sms_intelidus_api, '', 'yes');
		} else {
			update_option('eupago_sms_intelidus_api', $sms_intelidus_api);
		}

		if (empty(get_option('eupago_intelidus_sender'))) {
			delete_option('eupago_intelidus_sender');
			add_option('eupago_intelidus_sender', $intelidus_sender, '', 'yes');
		} else {
			update_option('eupago_intelidus_sender', $intelidus_sender);
		}
	}

	$reminder_checked 					= '';
	$debug_checked 						= '';
	$sms_enable_checked 				= '';
	$sms_payment_hold_checked 			= '';
	$sms_payment_confirmation_checked 	= '';
	$sms_order_confirmation_checked 	= '';

	if (get_option('eupago_reminder') == '1' ) {
		$reminder_checked = 'checked';
	}
	if (get_option('eupago_debug') == 'yes' ) {
		$debug_checked = 'checked';
	}
	if (get_option('eupago_sms_enable') == 'yes' ) {
		$sms_enable_checked = 'checked';
	}
	if (get_option('eupago_sms_payment_hold') == 'yes' ) {
		$sms_payment_hold_checked = 'checked';
	}
	if (get_option('eupago_sms_payment_confirmation') == 'yes' ) {
		$sms_payment_confirmation_checked = 'checked';
	}
	if (get_option('eupago_sms_order_confirmation') == 'yes' ) {
		$sms_order_confirmation_checked = 'checked';
	}
	?>

	<div class="eupago_header">
		<div>
			<img src="<?php echo esc_attr(plugins_url( 'images/dekas_home.png', __FILE__ )); ?>" alt="Dekas">
		</div>
		<div>
			<h1><?php esc_html_e( 'EuPago', 'eupago-gateway-for-woocommerce' ); ?></h1>
			<p><?php esc_html_e( 'EuPago services integration.', 'eupago-gateway-for-woocommerce' ); ?></p>
		</div>
	</div>
	<?php if ( isset( $_POST['eupago_save'] )) { ?>
		<div class="eupago-notice notice notice-success"> 
			<p><strong><?php esc_html_e( 'Settings saved.', 'eupago-gateway-for-woocommerce' ); ?></strong></p>
		</div>
	<?php } ?>

	<div class="eupago-settings">
		<form name="eupago-settings" method="POST" action="">
			<table class="form-table" role="presentation">
				<tbody>
					<tr>
						<th scope="row"><label for="channel"><?php esc_html_e( 'Channel Name:', 'eupago-gateway-for-woocommerce' ); ?></label></th>
						<td>
							<input class="regular-text" type="text" name="channel" value="<?php echo esc_attr(get_option( 'eupago_channel' )); ?>">
							<h4><?php esc_html_e('What is a channel?', 'eupago-gateway-for-woocommerce'); ?></h4>
							<p><?php esc_html_e("Each account has at least a channel. Each channel has an API Key that identifies your euPago's account.", 'eupago-gateway-for-woocommerce'); ?></p>
							<p>
								<?php esc_html_e("In order to find your API Key and channel name please follow this guide on our", 'eupago-gateway-for-woocommerce'); ?> 
								<a href="https://eupago.atlassian.net/servicedesk/customer/portal/2/article/224297034?src=1875300770" target="_BLANK"><?php esc_html_e("Help Center", 'eupago-gateway-for-woocommerce'); ?></a>.
							</p>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="api_key"><?php esc_html_e( 'API Key:', 'eupago-gateway-for-woocommerce' ); ?></label></th>
						<td>
							<input class="regular-text" type="text" name="api_key" value="<?php echo esc_attr(get_option( 'eupago_api_key' )); ?>">
							<p>
								<?php 
								echo sprintf( __( 'Please activate callback to this url on euPago dashboard: <code>%s</code>', 'eupago-gateway-for-woocommerce' ), (get_option('permalink_structure') == '' ? home_url( '/' ) . '?wc-api=WC_euPago' : home_url( '/' ) . 'wc-api/WC_euPago/' ) ); 
								?>
							</p>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="endpoint"><?php esc_html_e( 'Endpoint:', 'eupago-gateway-for-woocommerce' ); ?></label></th>
						<td>
							<select name="endpoint">
								<?php if (get_option('eupago_endpoint') == 'clientes') { ?>
									<option value="clientes" selected><?php esc_html_e( 'Live', 'eupago-gateway-for-woocommerce' ); ?></option>
								<?php } else { ?>
									<option value="clientes"><?php esc_html_e( 'Live', 'eupago-gateway-for-woocommerce' ); ?></option>
								<?php } ?>
								<?php if (get_option('eupago_endpoint') == 'sandbox') { ?>
									<option value="sandbox" selected><?php esc_html_e( 'Sandbox', 'eupago-gateway-for-woocommerce' ); ?></option>
								<?php } else { ?>
									<option value="sandbox"><?php esc_html_e( 'Sandbox', 'eupago-gateway-for-woocommerce' ); ?></option>
								<?php } ?>
							</select>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="reminder"><?php esc_html_e( 'Reminder(Failover):', 'eupago-gateway-for-woocommerce' ); ?></label></th>
						<td>
							<input type="checkbox" name="reminder" value="1" <?php echo $reminder_checked; ?>><?php esc_html_e( 'Enable', 'eupago-gateway-for-woocommerce' ); ?>
							<p>
								<?php esc_html_e( 'Do you want to send a reminder to your customer to inform him he has a pending order? Activate this option. Read more about this reminder', 'eupago-gateway-for-woocommerce' ); ?> 
								<a href="https://eupago.atlassian.net/servicedesk/customer/portal/2/article/652967937" target="_BLANK"><?php esc_html_e( 'here', 'eupago-gateway-for-woocommerce' ); ?></a>
							</p>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="debug"><?php esc_html_e( 'Debug Log:', 'eupago-gateway-for-woocommerce' ); ?></label></th>
						<td>
							<input type="checkbox" name="debug" value="yes" <?php echo $debug_checked; ?>><?php esc_html_e( 'Enable logging', 'eupago-gateway-for-woocommerce' ); ?>
							<p>
								<?php esc_html_e( 'Log plugin events, such as callback requests, inside', 'eupago-gateway-for-woocommerce' ); ?>
								<?php $uploads = wp_upload_dir(); ?>
								<code><?php echo wp_basename( $uploads['baseurl'] ) . '/wc-logs/'; ?></code>
							</p>
						</td>
					</tr>
				</tbody>
			</table>

			<h3><?php esc_html_e('Refund', 'eupago-gateway-for-woocommerce'); ?></h3>
			<table class="form-table" role="presentation">
				<tbody>
					<tr>
						<th scope="row"><label for="client_id"><?php esc_html_e( 'Client ID:', 'eupago-gateway-for-woocommerce' ); ?></label></th>
						<td><input class="regular-text" type="text" name="client_id" value="<?php echo esc_attr(get_option( 'eupago_client_id' )); ?>"></td>
					</tr>
					<tr>
						<th scope="row"><label for="client_secret"><?php esc_html_e( 'Client Secret:', 'eupago-gateway-for-woocommerce' ); ?></label></th>
						<td><input class="regular-text" type="text" name="client_secret" value="<?php echo esc_attr(get_option( 'eupago_client_secret' )); ?>"></td>
					</tr>
					<tr>
						<th scope="row"><label for="user_eupago"><?php esc_html_e( 'User:', 'eupago-gateway-for-woocommerce' ); ?></label></th>
						<td><input class="regular-text" type="text" name="user_eupago" value="<?php echo esc_attr(get_option( 'eupago_user' )); ?>"></td>
					</tr>
					<tr>
						<th scope="row"><label for="password_eupago"><?php esc_html_e( 'Password:', 'eupago-gateway-for-woocommerce' ); ?></label></th>
						<td><input class="regular-text" type="password" name="password_eupago" value="<?php echo esc_attr(get_option( 'eupago_password' )); ?>"></td>
					</tr>
				</tbody>
			</table>

			<h3><?php esc_html_e('SMS Intelidus360', 'eupago-gateway-for-woocommerce'); ?></h3>
			<table class="form-table" role="presentation">
				<tbody>
					<tr >
						<th scope="row"><label for="sms_enable"><?php esc_html_e( 'SMS Notifications:', 'eupago-gateway-for-woocommerce' ); ?></label></th>
						<td><input type="checkbox" name="sms_enable" value="yes" <?php echo $sms_enable_checked; ?>><?php esc_html_e( 'Enable', 'eupago-gateway-for-woocommerce' ); ?></td>
					</tr>
					<?php
					if (!empty(get_option('eupago_sms_enable')) && get_option('eupago_sms_enable') == 'yes') {
						$sms_enabled = 'eupago-sms-notifications active';
					} else {
						$sms_enabled = 'eupago-sms-notifications';
					}
					?>
					<tr class="<?php echo esc_html($sms_enabled); ?>">
						<th scope="row"><label for="sms_payment_hold"><?php esc_html_e( 'SMS Payment On Hold:', 'eupago-gateway-for-woocommerce' ); ?></label></th>
						<td><input type="checkbox" name="sms_payment_hold" value="yes" <?php echo $sms_payment_hold_checked; ?>><?php esc_html_e( 'Enable', 'eupago-gateway-for-woocommerce' ); ?></td>
					</tr>
					<tr class="<?php echo esc_html($sms_enabled); ?>">
						<th scope="row"><label for="sms_payment_confirmation"><?php esc_html_e( 'SMS Payment Confirmation:', 'eupago-gateway-for-woocommerce' ); ?></label></th>
						<td><input type="checkbox" name="sms_payment_confirmation" value="yes" <?php echo $sms_payment_confirmation_checked; ?>><?php esc_html_e( 'Enable', 'eupago-gateway-for-woocommerce' ); ?></td>
					</tr>
					<tr class="<?php echo esc_html($sms_enabled); ?>">
						<th scope="row"><label for="sms_order_confirmation"><?php esc_html_e( 'SMS Order Confirmation:', 'eupago-gateway-for-woocommerce' ); ?></label></th>
						<td><input type="checkbox" name="sms_order_confirmation" value="yes" <?php echo $sms_order_confirmation_checked; ?>><?php esc_html_e( 'Enable', 'eupago-gateway-for-woocommerce' ); ?></td>
					</tr>
					<tr class="<?php echo esc_html($sms_enabled); ?>">
						<th scope="row"><label for="sms_intelidus_id"><?php esc_html_e( 'SMS Intelidus 360 ID:', 'eupago-gateway-for-woocommerce' ); ?></label></th>
						<td><input class="regular-text" type="text" name="sms_intelidus_id" value="<?php echo esc_attr(get_option( 'eupago_sms_intelidus_id' )); ?>"></td>
					</tr>
					<tr class="<?php echo esc_html($sms_enabled); ?>">
						<th scope="row"><label for="sms_intelidus_api"><?php esc_html_e( 'SMS Intelidus 360 API:', 'eupago-gateway-for-woocommerce' ); ?></label></th>
						<td><input class="regular-text" type="text" name="sms_intelidus_api" value="<?php echo esc_attr(get_option( 'eupago_sms_intelidus_api' )); ?>"></td>
					</tr>
					<tr class="<?php echo esc_html($sms_enabled); ?>">
						<th scope="row"><label for="intelidus_sender"><?php esc_html_e( 'Intelidus 360 Sender:', 'eupago-gateway-for-woocommerce' ); ?></label></th>
						<td><input class="regular-text" type="text" name="intelidus_sender" value="<?php echo esc_attr(get_option( 'eupago_intelidus_sender' )); ?>"></td>
					</tr>
				</tbody>
			</table>
			<p>
				<input class="button button-primary" type="submit" name="eupago_save" value="<?php esc_html_e( 'Save Changes', 'eupago-gateway-for-woocommerce' ); ?>">
			</p>
		</form>
		<div class="eupago-sidebar">
			<img src="<?php echo esc_attr(plugins_url( 'images/eupago_logo.png', __FILE__ )); ?>" alt="Eupago Logo">
			<p><?php esc_html_e("In order to use euPago's plugin for WooCommerce you must have an euPago's account.", 'eupago-gateway-for-woocommerce'); ?></p>
			<p><?php esc_html_e('Do you need an account? You may sign up at', 'eupago-gateway-for-woocommerce'); ?> <a href="https://www.eupago.pt/registo" target="_BLANK">https://www.eupago.pt/registo</a>.</p>
			<p><?php esc_html_e('Do you already have a demo account and need to finish your real account? Please reach out by email:', 'eupago-gateway-for-woocommerce'); ?>  <a href="mailto:comercial@eupago.pt">comercial@eupago.pt</a>.</p>
		</div>
	</div>
<?php
}

?>