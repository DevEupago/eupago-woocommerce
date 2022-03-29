<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly

/**
 * euPago - CofidisPay
 *
 * @since 0.1
 */
if (!class_exists('WC_EuPago_CofidisPay_WebAtual')) {
  class WC_EuPago_CofidisPay_WebAtual extends WC_Payment_Gateway
  {

    /**
     * Constructor for your payment class
     *
     * @access public
     * @return void
     */
    public function __construct()
    {

      global $woocommerce;
      $this->id = 'eupago_cofidispay';

      $this->icon = plugins_url('assets/images/cofidispay.png', dirname(__FILE__));
      $this->has_fields = true;
      $this->method_title = __('CofidisPay (EuPago)', 'eupago-for-woocommerce');

      //Plugin options and settings
      $this->init_form_fields();
      $this->init_settings();

      //User settings
      $this->title = $this->get_option('title');
      $this->description = $this->get_option('description');
      $this->instructions = $this->get_option('instructions');
      $this->only_portugal = $this->get_option('only_portugal');
      $this->only_above = $this->get_option('only_above');
      $this->only_below = $this->get_option('only_below');
      $this->stock_when = $this->get_option('stock_when');

      // Set the API.
      $this->client = new WC_EuPago_API($this);

      // Actions and filters
      add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
      if (function_exists('icl_object_id') && function_exists('icl_register_string')) add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'register_wpml_strings'));
      add_action('woocommerce_thankyou_' . $this->id, array($this, 'thankyou_page'));
      add_action('woocommerce_order_details_after_order_table', array($this, 'order_details_after_order_table'), 20);

      add_filter('woocommerce_available_payment_gateways', array($this, 'disable_unless_portugal'));
      add_filter('woocommerce_available_payment_gateways', array($this, 'disable_only_above_or_below'));
      add_filter('woocommerce_available_payment_gateways', array($this, 'change_title'), 99);

      // Customer Emails
      add_action('woocommerce_email_before_order_table', array($this, 'email_instructions'), 10, 2);

      // Filter to decide if payment_complete reduces stock, or not
      add_filter('woocommerce_payment_complete_reduce_order_stock', array($this, 'woocommerce_payment_complete_reduce_order_stock'), 10, 2);
    }


    /**
     * WPML compatibility
     */
    function register_wpml_strings()
    {
      //These are already registered by WooCommerce Multilingual
      /*$to_register=array('title','description',);*/
      $to_register = array();
      foreach ($to_register as $string) {
        icl_register_string($this->id, $this->id . '_' . $string, $this->settings[$string]);
      }
    }

    function init_form_fields()
    {
      $this->form_fields = array(
        'enabled' => array(
          'title' => __('Enable/Disable', 'woocommerce'),
          'type' => 'checkbox',
          'label' => __('Enable CofidisPay (using euPago)', 'eupago-for-woocommerce'),
          'default' => 'no'
        ),
        'title' => array(
          'title' => __('Title', 'woocommerce'),
          'type' => 'text',
          'description' => __('This controls the title which the user sees during checkout.', 'woocommerce'),
          'default' => __('Até 12 vezes sem juros', 'eupago-for-woocommerce')
        ),
        'instructions' => array(
          'title'       => __('Instructions', 'eupago-for-woocommerce'),
          'type'        => 'textarea',
          'description' => __('Instructions that will be added to the thank you page and email sent to customer.', 'eupago-for-woocommerce'),
        ),
        'only_portugal' => array(
          'title' => __('Only for Portuguese customers?', 'eupago-for-woocommerce'),
          'type' => 'checkbox',
          'label' => __('Enable only for customers whose address is in Portugal', 'eupago-for-woocommerce'),
          'default' => 'no'
        ),
        // 'only_above' => array(
        //   'title' => __('Only for orders above', 'eupago-for-woocommerce'),
        //   'type' => 'number',
        //   'description' => __('Enable only for orders above x &euro; (exclusive). Leave blank (or zero) to allow for any order value.', 'eupago-for-woocommerce') . ' <br/> ' . __('By design, Mulitibanco only allows payments from 1 to 999999 &euro; (inclusive). You can use this option to further limit this range.', 'eupago-for-woocommerce'),
        //   'default' => ''
        // ),
        // 'only_below' => array(
        //   'title' => __('Only for orders below', 'eupago-for-woocommerce'),
        //   'type' => 'number',
        //   'description' => __('Enable only for orders below x &euro; (exclusive). Leave blank (or zero) to allow for any order value.', 'eupago-for-woocommerce') . ' <br/> ' . __('By design, Mulitibanco only allows payments from 1 to 999999 &euro; (inclusive). You can use this option to further limit this range.', 'eupago-for-woocommerce'),
        //   'default' => ''
        // ),
        'stock_when' => array(
          'title' => __('Reduce stock', 'eupago-for-woocommerce'),
          'type' => 'select',
          'description' => __('Choose when to reduce stock.', 'eupago-for-woocommerce'),
          'default' => '',
          'options'  => array(
            ''    => __('when order is paid (requires active callback)', 'eupago-for-woocommerce'),
            'order'  => __('when order is placed (before payment)', 'eupago-for-woocommerce'),
          ),
        ),
      );
    }

    public function admin_options()
    {
      include 'views/html-admin-page.php';
    }

    /**
     * Icon HTML
     */
    public function get_icon()
    {
      $alt = (function_exists('icl_object_id') ? icl_t($this->id, $this->id . '_title', $this->title) : $this->title);
      $icon_html = '<img src="' . esc_attr($this->icon) . '" alt="' . esc_attr($alt) . '" />';
      return apply_filters('woocommerce_gateway_icon', $icon_html, $this->id);
    }

    function check_order_errors($order_id)
    {
      $order = new WC_Order($order_id);
      $order_total = version_compare(WC_VERSION, '3.0', '>=') ? $order->get_total() : $order->order_total;

      // A loja não está em Euros
      if (trim(get_woocommerce_currency()) != 'EUR') {
        return __('Configuration error. This store currency is not Euros (&euro;).', 'eupago-for-woocommerce');
      }

      //O valor da encomenda não é aceita
      if (($order_total < 1) || ($order_total >= 1000000)) {
        return __('It\'s not possible to use CofidisPay to pay values under 1&euro; or above 999999&euro;.', 'eupago-for-woocommerce');
      }

      if (!isset($_POST['cofidispay_vat_number']) || empty($_POST['cofidispay_vat_number'])) {
        return __('Por favor, insira um NIF válido para prosseguir com o pagamento', 'eupago-for-woocommerce');
      }

      return false;
    }

    /**
     * Thank You page message.
     *
     * @param  int    $order_id Order ID.
     *
     * @return string
     */
    public function thankyou_page($order_id)
    {
      $order = new WC_Order($order_id);
      $order_total = version_compare(WC_VERSION, '3.0', '>=') ? $order->get_total() : $order->order_total;
      $payment_method = version_compare(WC_VERSION, '3.0', '>=') ? $order->get_payment_method() : $order->payment_method;

      if ($payment_method == $this->id) {

        wc_get_template('payment-instructions.php', array(
          'method' => $payment_method,
          'payment_name' => (function_exists('icl_object_id') ? icl_t($this->id, $this->id . '_title', $this->title) : $this->title),
          'instructions' => isset($this->instructions) && !empty($this->instructions) ? $this->instructions : '',
          'referencia' => get_post_meta($order_id, '_eupago_cofidispay_referencia', true),
          'order_total' => $order_total,
        ), 'woocommerce/eupago/', WC_EuPago::get_templates_path());
      }
    }

    /**
     *
     * View Order detail payment reference.
     */
    function order_details_after_order_table($order)
    {
      if (is_wc_endpoint_url('view-order')) {
        $this->thankyou_page($order->get_id());
      }
    }

    /**
     * Email instructions
     */
    function email_instructions($order, $sent_to_admin, $plain_text = false)
    {
      $order_id = version_compare(WC_VERSION, '3.0', '>=') ? $order->get_id() : $order->id;
      $order_total = version_compare(WC_VERSION, '3.0', '>=') ? $order->get_total() : $order->order_total;
      $payment_method = version_compare(WC_VERSION, '3.0', '>=') ? $order->get_payment_method() : $order->payment_method;

      if ($sent_to_admin || !$order->has_status('on-hold') || $this->id !== $payment_method) {
        return;
      }

      if ($plain_text) {
        wc_get_template('emails/plain-instructions.php', array(
          'method' => $payment_method,
          'payment_name' => (function_exists('icl_object_id') ? icl_t($this->id, $this->id . '_title', $this->title) : $this->title),
          'instructions' => isset($this->instructions) && !empty($this->instructions) ? $this->instructions : '',
          'referencia' => get_post_meta($order_id, '_eupago_cofidispay_referencia', true),
          'order_total' => $order_total,
        ), 'woocommerce/eupago/', WC_EuPago::get_templates_path());
      } else {
        wc_get_template('emails/html-instructions.php', array(
          'method' => $payment_method,
          'payment_name' => (function_exists('icl_object_id') ? icl_t($this->id, $this->id . '_title', $this->title) : $this->title),
          'instructions' => isset($this->instructions) && !empty($this->instructions) ? $this->instructions : '',
          'referencia' => get_post_meta($order_id, '_eupago_cofidispay_referencia', true),
          'order_total' => $order_total,
        ), 'woocommerce/eupago/', WC_EuPago::get_templates_path());
      }
    }

    function payment_fields()
    {
      // if ($description = $this->get_description()) {
      //   echo wpautop(wptexturize($description));
      // }

      echo '<p>' . __('Será redirecionado para uma página segura a fim de efetuar o pagamento.<br />O pagamento das prestações será efetuado no cartão de débito ou crédito do cliente através de solução de pagamento assente em contrato de factoring entre a Cofidis e o Comerciante. Informe-se na <a href="https://www.cofidis.pt/cofidispay" target="_blank">Cofidis</a>.', 'eupago-for-woocommerce') . '</p>';

      $this->cofidispay_form();
    }

    function cofidispay_form()
    {
      ?>
      <fieldset id="wc-<?php echo esc_attr($this->id); ?>-cofidispay-form" class="wc-cofidispay-form wc-payment-form" style="background:transparent;">
        <p class="form-row form-row-first">
          <label for="cofidispay_vat_number"><?php esc_html_e('Número de identificação fiscal', 'eupago-for-woocommerce'); ?></label>
          <input type="text" id="cofidispay_vat_number" autocorrect="off" spellcheck="false" name="cofidispay_vat_number" class="input-text" aria-label="<?php _e('Número de identificação fiscal', 'eupago-for-woocommerce'); ?>" aria-placeholder="" aria-invalid="false" required />
        </p>
        <div class="clear"></div>
      </fieldset>
      <?php
    }


    /**
     * Process it
     */
    function process_payment($order_id)
    {
      global $woocommerce;
      $order = new WC_Order($order_id);
      $order_total = version_compare(WC_VERSION, '3.0', '>=') ? $order->get_total() : $order->order_total;
      $cofidispay_vat_number = isset($_POST['cofidispay_vat_number']) && !empty($_POST['cofidispay_vat_number']) ? $_POST['cofidispay_vat_number'] : '';
      
      update_post_meta($order_id, '_eupago_cofidis_vat_number', $cofidispay_vat_number);

      if ($error_message = $this->check_order_errors($order_id)) {
        wc_add_notice($error_message, 'error');
        return;
      }

      $pedidoCofidis = $this->client->cofidispay_create($order_id);

      if ($pedidoCofidis->transactionStatus != 'Success') {
        wc_add_notice(__('Payment error:', 'eupago-for-woocommerce') . ' Ocorreu um erro com o pedido de pagamento', 'error');
        return;
      }

      update_post_meta($order_id, '_eupago_cofidispay_transactionID', $pedidoCofidis->transactionID);
      update_post_meta($order_id, '_eupago_cofidispay_referencia', $pedidoCofidis->reference);
      update_post_meta($order_id, '_eupago_cofidispay_redirectUrl', $pedidoCofidis->redirectUrl);

      // Mark as on-hold
      // $order->update_status('pending', __('Awaiting CofidisPay payment.', 'eupago-for-woocommerce'));

      // Reduce stock levels
      if ($this->stock_when == 'order') $order->reduce_order_stock();

      // Remove cart
      $woocommerce->cart->empty_cart();

      // Empty awaiting payment session
      if (isset($_SESSION['order_awaiting_payment'])) unset($_SESSION['order_awaiting_payment']);

      // Return thankyou redirect
      return array(
        'result' => 'success',
        'redirect' => $pedidoCofidis->redirectUrl
      );
    }

    /**
     * Just for Portugal
     */
    function disable_unless_portugal($available_gateways)
    {
      if (!is_admin()) {
        if (isset(WC()->customer)) {
          $country = version_compare(WC_VERSION, '3.0', '>=') ? WC()->customer->get_billing_country() : WC()->customer->get_country();
          if (isset($available_gateways[$this->id])) {
            if ($available_gateways[$this->id]->only_portugal == 'yes' && trim($country) != 'PT') {
              unset($available_gateways[$this->id]);
            }
          }
        }
      }
      return $available_gateways;
    }

    /**
     * Just above/below certain amounts
     */
    function disable_only_above_or_below($available_gateways)
    {
      global $woocommerce;
      
      if (isset($available_gateways[$this->id])) {
        if (get_query_var('order-pay')) {
          //order-pay page
          $order = new WC_Order(get_query_var('order-pay'));
          $current_price = floatval(preg_replace('#[^\d.]#', '', $order->get_total()));
        } else {
          if (isset($woocommerce->cart)) {
            $current_price = floatval(preg_replace('#[^\d.]#', '', $woocommerce->cart->total));
          }
        }

        // CofidisPay apenas permite pagamentos entre 60 e 1000EUR
        if ($current_price < 60 || $current_price > 1000) {
          unset($available_gateways[$this->id]);
        }
        if (@floatval($available_gateways[$this->id]->only_above) > 0) {
          if ($current_price < floatval($available_gateways[$this->id]->only_above)) {
            unset($available_gateways[$this->id]);
          }
        }
        if (@floatval($available_gateways[$this->id]->only_below) > 0) {
          if ($current_price > floatval($available_gateways[$this->id]->only_below)) {
            unset($available_gateways[$this->id]);
          }
        }
      }
      return $available_gateways;
    }

    /* Payment complete - Stolen from PayPal method */
    function payment_complete($order, $txn_id = '', $note = '')
    {
      $order->add_order_note($note);
      $order->payment_complete($txn_id);
    }

    /* Reduce stock on 'wc_maybe_reduce_stock_levels'? */
    function woocommerce_payment_complete_reduce_order_stock($bool, $order_id)
    {
      $order = new WC_Order($order_id);
      if ($order->get_payment_method() == $this->id) {
        return (WC_EuPago::woocommerce_payment_complete_reduce_order_stock($bool, $order, $this->id, $this->stock_when));
      } else {
        return $bool;
      }
    }

    function change_title($available_gateways)
    {
      global $woocommerce;

      if (isset($available_gateways[$this->id])) {
        if (get_query_var('order-pay')) {
          //order-pay page
          $order = new WC_Order(get_query_var('order-pay'));
          $order_total = floatval(preg_replace('#[^\d.]#', '', $order->get_total()));
        } else {
          if (isset($woocommerce->cart)) {
            $order_total = floatval(preg_replace('#[^\d.]#', '', $woocommerce->cart->total));
          }
        }

        $this->title = 'Até ' . $this->get_numero_prestacoes( $order_total ) . 'x sem juros';
      }

      return $available_gateways;
    }

    function get_numero_prestacoes( $order_total ) {
      switch (true) {
        case $order_total >= 240:
          $number = 12;
          break;

        case $order_total >= 220:
          $number = 11;
          break;

        case $order_total >= 200:
          $number = 10;
          break;

        case $order_total >= 180:
          $number = 9;
          break;

        case $order_total >= 160:
          $number = 8;
          break;

        case $order_total >= 140:
          $number = 7;
          break;

        case $order_total >= 120:
          $number = 6;
          break;

        case $order_total >= 100:
          $number = 5;
          break;

        case $order_total >= 80:
          $number = 4;
          break;

        default:
          $number = 3;
          break;
      }

      return $number;
    }
  } // WC_CofidisPay_euPago_WebAtual
} // class_exists()
