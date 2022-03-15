<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
* euPago - Credit Card
*
* @since 1.1
*/
if ( !class_exists( 'WC_EuPago_CC_WebAtual' ) ) {
  class WC_EuPago_CC_WebAtual extends WC_Payment_Gateway {

    /**
    * Constructor for your payment class
    *
    * @access public
    * @return void
    */
    public function __construct() {

      global $woocommerce;
      $this->id = 'eupago_cc';

      $this->icon = plugins_url('assets/images/cc_icon.jpg', dirname(__FILE__));
      $this->has_fields = false;
      $this->method_title = __('Credit Card (EuPago)', 'eupago-gateway-for-woocommerce');

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
      $this->client = new WC_EuPago_API( $this );

      // Actions and filters
      add_action('woocommerce_update_options_payment_gateways_'.$this->id, array($this, 'process_admin_options'));
      if (function_exists('icl_object_id') && function_exists('icl_register_string')) add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'register_wpml_strings'));
      add_action('woocommerce_thankyou_'.$this->id, array($this, 'thankyou_page'));
      add_action('woocommerce_order_details_after_order_table', array( $this, 'order_details_after_order_table' ), 20 );

      add_filter('woocommerce_available_payment_gateways', array($this, 'disable_unless_portugal'));
      add_filter('woocommerce_available_payment_gateways', array($this, 'disable_only_above_or_below'));

      // Customer Emails
      add_action('woocommerce_email_before_order_table', array($this, 'email_instructions'), 10, 2);

      // Filter to decide if payment_complete reduces stock, or not
      add_filter('woocommerce_payment_complete_reduce_order_stock', array($this, 'woocommerce_payment_complete_reduce_order_stock'), 10, 2);

    }

    /**
    * WPML compatibility
    */
    function register_wpml_strings() {
      //These are already registered by WooCommerce Multilingual
      /*$to_register=array('title','description',);*/
      $to_register = array();
      foreach($to_register as $string) {
        icl_register_string($this->id, $this->id.'_'.$string, $this->settings[$string]);
      }
    }

    /**
    * Initialise Gateway Settings Form Fields
    */
    function init_form_fields() {
      $this->form_fields = array(
        'enabled' => array(
          'title' => __('Enable/Disable', 'eupago-gateway-for-woocommerce'),
          'type' => 'checkbox',
          'label' => __( 'Enable Credit Card (using euPago)', 'eupago-gateway-for-woocommerce'),
          'default' => 'no'
        ),
        'title' => array(
          'title' => __('Title', 'eupago-gateway-for-woocommerce' ),
          'type' => 'text',
          'description' => __('This controls the title which the user sees during checkout.', 'eupago-gateway-for-woocommerce'),
          'default' => __('Credit Card', 'eupago-gateway-for-woocommerce')
        ),
        'description' => array(
          'title' => __('Description', 'eupago-gateway-for-woocommerce' ),
          'type' => 'textarea',
          'description' => __('This controls the description which the user sees during checkout.', 'eupago-gateway-for-woocommerce' ),
          'default' => __('Pay with credit card', 'eupago-gateway-for-woocommerce')
        ),
        'logo_url' => array(
          'title' => __('Logo', 'eupago-gateway-for-woocommerce'),
          'type' => 'text',
          'description' => __('Shop Logo for payment page', 'eupago-gateway-for-woocommerce'),
          'default' => ''
        ),
        'instructions' => array(
          'title'       => __( 'Instructions', 'eupago-gateway-for-woocommerce' ),
          'type'        => 'textarea',
          'description' => __( 'Instructions that will be added to the thank you page and email sent to customer.', 'eupago-gateway-for-woocommerce' ),
        ),
        'only_portugal' => array(
          'title' => __('Only for Portuguese customers?', 'eupago-gateway-for-woocommerce'),
          'type' => 'checkbox',
          'label' => __( 'Enable only for customers whose address is in Portugal', 'eupago-gateway-for-woocommerce'),
          'default' => 'no'
        ),
        'only_above' => array(
          'title' => __('Only for orders above', 'eupago-gateway-for-woocommerce'),
          'type' => 'number',
          'description' => __( 'Enable only for orders above x &euro; (exclusive). Leave blank (or zero) to allow for any order value.', 'eupago-gateway-for-woocommerce').' <br/> '.__( 'By design, Mulitibanco only allows payments from 1 to 999999 &euro; (inclusive). You can use this option to further limit this range.', 'eupago-gateway-for-woocommerce'),
          'default' => ''
        ),
        'only_below' => array(
          'title' => __('Only for orders below', 'eupago-gateway-for-woocommerce'),
          'type' => 'number',
          'description' => __( 'Enable only for orders below x &euro; (exclusive). Leave blank (or zero) to allow for any order value.', 'eupago-gateway-for-woocommerce').' <br/> '.__( 'By design, Mulitibanco only allows payments from 1 to 999999 &euro; (inclusive). You can use this option to further limit this range.', 'eupago-gateway-for-woocommerce'),
          'default' => ''
        ),
        'stock_when' => array(
          'title' => __('Reduce stock', 'eupago-gateway-for-woocommerce'),
          'type' => 'select',
          'description' => __( 'Choose when to reduce stock.', 'eupago-gateway-for-woocommerce'),
          'default' => '',
          'options'	=> array(
            ''		=> __('when order is paid (requires active callback)', 'eupago-gateway-for-woocommerce'),
            'order'	=> __('when order is placed (before payment)', 'eupago-gateway-for-woocommerce'),
          ),
        ),
      );
    }

    public function admin_options() {
      include 'views/html-admin-page.php';
    }

    /**
    * Icon HTML
    */
    public function get_icon() {
      $alt = (function_exists('icl_object_id') ? icl_t($this->id, $this->id . '_title', $this->title) : $this->title);
      $icon_html = '<img src="' . esc_attr($this->icon) . '" alt="' . esc_attr($alt) . '" />';
      return apply_filters('woocommerce_gateway_icon', $icon_html, $this->id);
    }

    /**
    * Thank You page message.
    *
    * @param  int    $order_id Order ID.
    *
    * @return string
    */
    public function thankyou_page( $order_id ) {
      $order = new WC_Order($order_id);
      $order_total = version_compare( WC_VERSION, '3.0', '>=' ) ? $order->get_total() : $order->order_total;
      $payment_method = version_compare( WC_VERSION, '3.0', '>=' ) ? $order->get_payment_method() : $order->payment_method;

      if ( $payment_method == $this->id ) {

        wc_get_template( 'payment-instructions.php', array(
          'method' => $payment_method,
          'payment_name' => (function_exists('icl_object_id') ? icl_t($this->id, $this->id.'_title', $this->title) : $this->title),
          'referencia' => get_post_meta($order_id, '_eupago_cc_referencia', true),
          'order_total' => $order_total,
        ), 'woocommerce/eupago/', (new WC_EuPago())->get_templates_path() );

      }
    }

    /**
    *
    * View Order detail payment reference.
    */
    function order_details_after_order_table( $order ) {
      if ( is_wc_endpoint_url( 'view-order' ) ) {
        $this->thankyou_page( $order->get_id() );
      }
    }

    /**
    * Email instructions
    */
    function email_instructions($order, $sent_to_admin, $plain_text = false) {
      $order_id = version_compare( WC_VERSION, '3.0', '>=' ) ? $order->get_id() : $order->id;
      $order_total = version_compare( WC_VERSION, '3.0', '>=' ) ? $order->get_total() : $order->order_total;
      $payment_method = version_compare( WC_VERSION, '3.0', '>=' ) ? $order->get_payment_method() : $order->payment_method;

      if ($sent_to_admin || !$order->has_status( 'on-hold' ) || $this->id !== $payment_method ) {
        return;
      }

      if ($plain_text) {
        wc_get_template( 'emails/plain-instructions.php', array(
          'method' => $payment_method,
          'payment_name' => (function_exists('icl_object_id') ? icl_t($this->id, $this->id.'_title', $this->title) : $this->title),
          'instructions' => isset( $this->instructions ) && !empty( $this->instructions ) ? $this->instructions : '',
          'referencia' => get_post_meta($order_id, '_eupago_cc_referencia', true),
          'order_total' => $order_total,
        ), 'woocommerce/eupago/', (new WC_EuPago())->get_templates_path() );
      } else {
        wc_get_template( 'emails/html-instructions.php', array(
          'method' => $payment_method,
          'payment_name' => (function_exists('icl_object_id') ? icl_t($this->id, $this->id.'_title', $this->title) : $this->title),
          'instructions' => isset( $this->instructions ) && !empty( $this->instructions ) ? $this->instructions : '',
          'referencia' => get_post_meta($order_id, '_eupago_cc_referencia', true),
          'order_total' => $order_total,
        ), 'woocommerce/eupago/', (new WC_EuPago())->get_templates_path() );
      }
    }

    function check_order_errors($order_id) {
      $order = new WC_Order($order_id);
      $order_total = version_compare( WC_VERSION, '3.0', '>=' ) ? $order->get_total() : $order->order_total;


      // A loja não está em Euros
      if ( trim(get_woocommerce_currency()) != 'EUR' ) {
        return __('Configuration error. This store currency is not Euros (&euro;).', 'eupago-gateway-for-woocommerce');
      }

      //O valor da encomenda não é aceite
      if ( ($order_total <= 2) || ($order_total >= 1000000) ) {
        return __('It\'s not possible to use credit card to pay values under 2&euro; or above 999999&euro;.', 'eupago-gateway-for-woocommerce');
      }
      return false;
    }

    /**
    * Process it
    */
    function process_payment($order_id) {
      global $woocommerce;
      $order = wc_get_order( $order_id );
      $order_total = version_compare( WC_VERSION, '3.0', '>=' ) ? $order->get_total() : $order->order_total;
      $payment_method = version_compare( WC_VERSION, '3.0', '>=' ) ? $order->get_payment_method() : $order->payment_method;


      if ($error_message = $this->check_order_errors($order_id)) {
        wc_add_notice( __('Payment error:', 'eupago-gateway-for-woocommerce') . ' ' . $error_message, 'error' );
        return;
      }

      $lang = get_post_meta( $order_id, 'wpml_language', true );
      if ( empty( $lang ) ) {
        $lang = 'pt-pt';
      }

      $eupagoCC = $this->client->pedidoCC( $order, $order_total, $this->get_option( 'logo_url' ), $this->get_return_url( $order ), $lang, $this->get_comment_table( $order, $order_total ) );

      if ( $eupagoCC->estado != 0 ) {
        $error_message = $eupagoCC->resposta;
        wc_add_notice( __('Payment error:', 'eupago-gateway-for-woocommerce') . ' ' . $error_message, 'error' );
        return;
      }

      update_post_meta ($order_id, '_eupago_cc_referencia', $eupagoCC->referencia);

      // Mark as on-hold
      $order->update_status('pending', __('Awaiting Credit Card payment.', 'eupago-gateway-for-woocommerce'));

      // Reduce stock levels
      if ($this->stock_when == 'order') $order->reduce_order_stock();

      // Remove cart
      $woocommerce->cart->empty_cart();

      // Empty awaiting payment session
      if (isset($_SESSION['order_awaiting_payment'])) unset($_SESSION['order_awaiting_payment']);

      // Return thankyou redirect
      return array(
        'result' => 'success',
        'redirect' => $eupagoCC->url
      );
    }

    function get_comment_table( $order, $order_total ) {
      $products = $order->get_items();

      $total_produtos = 0;
      $comentario = "<ul style='margin:0; padding:0; font-size:0.75em; color:#333; '>";

      foreach ($products as $product) {
        $total_produtos += $product['line_total'];
        $comentario .= "<li style='list-style: none;'><span style='margin:0; font-size:9px; margin-bottom:5px; padding:0;' class='large-7 columns left'>".$product['name']."</span><span style='margin:0; padding:0; text-align:center;' class='large-2 columns'>x ".$product['qty']."</span><span style='margin:0; padding:0; text-align:right' class='large-3 columns right'>".$product['line_total']." €</span></li>";
      }
      $envio_e_taxas = ($order_total - $total_produtos);
      $comentario .= "<li style='list-style: none; padding-top: 5px; border-top: 1px solid #ddd; display: inline-block; font-size:9px; width: 100%;'><span style='margin:0; padding:0;' class='large-7 columns left'>Envio e taxas:</span><span style='margin:0; padding:0; text-align:center;' class='large-2 columns'></span><span style='margin:0; padding:0; text-align:right' class='large-3 columns right'>".$envio_e_taxas." €</span></li></ul>";

      return $comentario;
    }

    /**
     * Just for Portugal
     */
    function disable_unless_portugal($available_gateways)
    {
      if (!is_admin()) {
        if (isset($available_gateways[$this->id])) {
          if (isset(WC()->customer)) {
            $country = version_compare(WC_VERSION, '3.0', '>=') ? WC()->customer->get_billing_country() : WC()->customer->get_country();
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
    function disable_only_above_or_below($available_gateways) {
      global $woocommerce;
      if (isset($available_gateways[$this->id])) {
        if (@floatval($available_gateways[$this->id]->only_above) > 0) {
          if ( $woocommerce->cart->total < floatval($available_gateways[$this->id]->only_above) ) {
            unset($available_gateways[$this->id]);
          }
        }
        if ( @floatval($available_gateways[$this->id]->only_below) > 0 ) {
          if ( $woocommerce->cart->total > floatval($available_gateways[$this->id]->only_below) ) {
            unset($available_gateways[$this->id]);
          }
        }
      }
      return $available_gateways;
    }

    function payment_complete( $order, $txn_id = '', $note = '' ) {
      $order->add_order_note( $note );
      $order->payment_complete( $txn_id );
    }

    /* Reduce stock on 'wc_maybe_reduce_stock_levels'? */
    function woocommerce_payment_complete_reduce_order_stock( $bool, $order_id ) {
      $order = new WC_Order( $order_id );
      if ( $order->get_payment_method() == $this->id ) {
        return ( (new WC_EuPago())->woocommerce_payment_complete_reduce_order_stock( $bool, $order, $this->id, $this->stock_when ) );
      } else {
        return $bool;
      }
    }

  } // WC_EuPago_CC_WebAtual
} // class_exists()
