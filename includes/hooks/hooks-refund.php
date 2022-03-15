<?php
/**
* EuPago Refund.
*/

// Refund request Ajax
add_action('wp_ajax_refund', 'refund_func');
add_action( 'wp_ajax_nopriv_refund', 'refund_func' );

function refund_func() {

   $endpoint   = get_option('eupago_endpoint');
   $trid       = get_post_meta( sanitize_text_field($_POST['refund_order']), '_transaction_id', true );

   if (!empty(sanitize_text_field($_POST['refund_name'])) && !empty(sanitize_text_field($_POST['refund_iban'])) && !empty(sanitize_text_field($_POST['refund_bic'])) && !empty(sanitize_text_field($_POST['refund_amount'])) && !empty(sanitize_text_field($_POST['refund_reason']))) {
      //Token
      $url = 'https://' . $endpoint . '.eupago.pt/api/auth/token';
      $args = array(
         'headers' => array(
               'Content-Type' => 'application/json',
         ),
         'body' => array(
               "grant_type"      => 'password',
               "client_id"       => get_option('eupago_client_id'),
               "client_secret"   => get_option('eupago_client_secret'),
               "username"        => get_option('eupago_user'),
               "password"        => get_option('eupago_password'),
         ),
         'timeout'     => '60',
      );
      
      $response = wp_remote_post( $url, $args );
      $body     = wp_remote_retrieve_body( $response );
      $access_token = $body['access_token'];


      //Refund request
      $url_refund = 'https://'. $endpoint . '.eupago.pt/api/management/v1.02/refund/' . $trid;
      $args_refund = array(
         'headers' => array(
               'Authorization' => 'Bearer ' . $access_token,
               'Content-Type' => 'application/json',
         ),
         'body' => array(
               'name'      => sanitize_text_field($_POST['refund_name']),
               'iban'      => sanitize_text_field($_POST['refund_iban']),
               'bic'       => sanitize_text_field($_POST['refund_bic']),
               'amount'    => floatval(sanitize_text_field($_POST['refund_amount'])),
               'reason'    => sanitize_text_field($_POST['refund_reason']),
         ),
         'timeout'     => '60',
      );
      
      $response_refund = wp_remote_post( $url_refund, $args_refund );
      $body_refund     = wp_remote_retrieve_body( $response_refund );
      $http_code = wp_remote_retrieve_response_code( $body_refund );
   
      if ($http_code == 'Success') {
         $output_class     = 'eupago-output-success';
         $output_request   = __('Request made successfully', 'eupago-gateway-for-woocommerce');
      } else {
         $output_class     = 'eupago-output-error';
         if ($http_code == 'IBAN_INVALID') {
               $output_request   = __('IBAN Invalid', 'eupago-gateway-for-woocommerce');
         } else if ($http_code == 'BIC_INVALID') {
               $output_request   = __('BIC Invalid', 'eupago-gateway-for-woocommerce');
         } else if ($http_code == 'AMOUNT_INVALID') {
               $output_request   = __('Amount Invalid', 'eupago-gateway-for-woocommerce');
         } else {
               $output_request   = __('Request error', 'eupago-gateway-for-woocommerce');
         }
      }
   } else {
      $output_class     = 'eupago-output-error';
      $output_request   = __('Fill all fields', 'eupago-gateway-for-woocommerce');
   }

   echo '<p class="' . esc_html($output_class) . '">' . esc_html($output_request) . '</p>';

   wp_die();
}


// Add meta box for refund order.
function eupago_refund() {
   add_meta_box(
      'woocommerce-order-refund',
      __('Refund Request', 'eupago-gateway-for-woocommerce'),
      'eupago_refund_content',
      'shop_order',
      'side',
      'default'
   );
}

add_action( 'add_meta_boxes', 'eupago_refund' );


// Refund request form
function eupago_refund_content() { ?>
   <div class="eupago-site-url"><?php echo site_url(); ?></div>
   <form method="POST" action="">
      <p><input class="eupago-field" type="text" name="refund_name" value="" placeholder="<?php esc_html_e('Name', 'eupago-gateway-for-woocommerce'); ?>"></p>
      <p><input class="eupago-field" type="text" name="refund_iban" value="" placeholder="<?php esc_html_e('IBAN', 'eupago-gateway-for-woocommerce'); ?>"></p>
      <p><input class="eupago-field" type="text" name="refund_bic" value="" placeholder="<?php esc_html_e('BIC', 'eupago-gateway-for-woocommerce'); ?>"></p>
      <p><input class="eupago-field" type="text" name="refund_amount" value="<?php echo esc_attr(get_post_meta( $_GET['post'], '_order_total', true )); ?>" placeholder="<?php esc_html_e('Amount', 'eupago-gateway-for-woocommerce'); ?>"></p>
      <p><input class="eupago-field" type="text" name="refund_reason" value="" placeholder="<?php esc_html_e('Reason', 'eupago-gateway-for-woocommerce'); ?>"></p>
      <div class="button button-primary eupago-refund-request"><?php esc_html_e('Request a refund', 'eupago-gateway-for-woocommerce'); ?></div>
   </form>

   <div class="eupago-refund-response"></div>
<?php  
}
?>