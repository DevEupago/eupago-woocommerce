<?php
/**
* WC EuPago API Class.
*/
class WC_EuPago_API {
  /**
  * Constructor.
  *
  * @param WC_EuPago_API
  */
  /*public function __construct() {
    $this->integration = new WC_EuPago_Integration;
  }*/

  public function get_url() {
    if (get_option('eupago_endpoint') == 'sandbox') {
      return 'https://sandbox.eupago.pt/replica.eupagov20.wsdl';
    } else {
      return 'https://clientes.eupago.pt/eupagov20.wsdl';
    }
  }

  public function get_cofidis_url()
  {
    if (get_option('eupago_endpoint') == 'sandbox') {
      return 'https://sandbox.eupago.pt/api/v1.02/cofidis/create';
    } else {
      return 'https://clientes.eupago.pt/api/v1.02/cofidis/create';
    }
  }

  public function get_api_key() {
    return get_option('eupago_api_key');
  }

  public function get_failover() {
    return get_option('eupago_reminder');
  }

  /**
  * Money format.
  *
  * @param  int/float $value Value to fix.
  *
  * @return float            Fixed value.
  */
  protected function money_format( $value ) {
    return number_format( $value, 2, '.', '' );
  }

  public function getReferenciaMB($order_id, $valor, $per_dup = 0, $deadline = null) {
    if (extension_loaded('soap')) {
      $get_order = wc_get_order($order_id);
      $email = $get_order->get_billing_email();
      $phone = $get_order->get_billing_phone();

      $client = @new SoapClient($this->get_url(), array('cache_wsdl' => WSDL_CACHE_NONE));
      $args = array(
        'chave' => $this->get_api_key(),
        'valor' => $this->money_format( $valor ),
        'id' => $order_id,
        'per_dup' => $per_dup,
        "failOver" => (int)$this->get_failover(),
        'email'   => $email,
        'contacto' => (int)$phone
      );
  
      if ( isset( $deadline ) && !empty( $deadline ) ) {
        $args['data_inicio'] = date('Y-m-d');
        $args['data_fim'] = date('Y-m-d', strtotime('+' . $deadline . ' day', strtotime( $args['data_inicio'] ) ) );
        return $client->gerarReferenciaMBDL( $args );
      }
  
      return $client->gerarReferenciaMB( $args );
    } else {
      //$curl = curl_init();

      if ( isset( $deadline ) && !empty( $deadline ) ) {
        $body = array(
          'chave'         => $this->get_api_key(),
          'valor'         => $this->money_format($valor),
          'id'            => $order_id,
          'per_dup'       => $per_dup,
          'data_inicio'   => date('Y-m-d'),
          'data_fim'      => date('Y-m-d', strtotime('+' . $deadline . ' day', strtotime( date('Y-m-d') ) ) )
        );
      } else {
        $body = array(
          'chave'         => $this->get_api_key(),
          'valor'         => $this->money_format($valor),
          'id'            => $order_id,
          'per_dup'       => $per_dup
        );
      }

      $url = 'https://' . get_option('eupago_endpoint') . '.eupago.pt/clientes/rest_api/multibanco/create';
      $args = array(
          'body' => $body,
          'timeout'     => '60',
      );
      
      $response = wp_remote_post( $url, $args );
      $client     = wp_remote_retrieve_body( $response );

      return $client;
    }
  }

  public function getReferenciaPS($order_id, $valor) {
    if (extension_loaded('soap')) {
      $client = @new SoapClient($this->get_url(), array('cache_wsdl' => WSDL_CACHE_NONE));
      return $client->gerarReferenciaPS(array(
        "chave" => $this->get_api_key(),
        "valor" => $this->money_format($valor),
        "id" => $order_id
      ));
    } else {
      $url = 'https://' . get_option('eupago_endpoint') . '.eupago.pt/clientes/rest_api/payshop/create';
      $args = array(
          'body' => array(
            "chave" => $this->get_api_key(),
            "valor" => $this->money_format($valor),
            "id" => $order_id
          ),
          'timeout'     => '60',
      );
      
      $response = wp_remote_post( $url, $args );
      $client     = wp_remote_retrieve_body( $response );

      return $client;
    }
  }

  public function getReferenciaPQ( $order_id, $valor ) {
    $client = @new SoapClient($this->get_url(), array('cache_wsdl' => WSDL_CACHE_NONE));
    return $client->gerarReferenciaPQ(array(
      "chave" => $this->get_api_key(),
      "valor" => $this->money_format( $valor ),
      "id" => $order_id
    ));
  }

  public function getReferenciaMBW($order_id, $valor, $telefone) {
    if (extension_loaded('soap')) {
      $get_order = wc_get_order($order_id);
      $email = $get_order->get_billing_email();
      $phone = $get_order->get_billing_phone();

      $client = @new SoapClient($this->get_url(), array('cache_wsdl' => WSDL_CACHE_NONE));
      return $client->pedidoMBW(array(
        "chave" => $this->get_api_key(),
        "valor" => $this->money_format($valor),
        "id" => $order_id,
        "alias" => $telefone,
        "failOver" => (int)$this->get_failover(),
        'email'   => $email,
        'contacto' => (int)$phone
      ));
    } else {
      $url = 'https://' . get_option('eupago_endpoint') . '.eupago.pt/clientes/rest_api/mbway/create';
      $args = array(
          'body' => array(
            'chave' => $this->get_api_key(),
            'valor' => $this->money_format($valor),
            'id' => $order_id,
            'alias' => $telefone,
            'descricao' => site_url()
          ),
          'timeout'     => '60',
      );
      
      $response = wp_remote_post( $url, $args );
      $client     = wp_remote_retrieve_body( $response );

      return $client;
    }
  }

  public function pedidoCC($order, $valor, $logo_url, $return_url, $lang, $comment) {
    $client = @new SoapClient($this->get_url(), array('cache_wsdl' => WSDL_CACHE_NONE));
    return $client->pedidoCC(array(
      'chave' => $this->get_api_key(),
      'valor' => $this->money_format( $valor ),
      'id' => $order->get_id(),
      'url_logotipo' => $logo_url,
      'url_retorno' => $return_url,
      'nome' => $order->get_billing_first_name() . ' ' . $order->get_billing_last_name(),
      'email' => $order->get_billing_email(),
      'lang' => $lang,
      'comentario' => $comment,
      'tds' => 1
    ));
  }

  public function pedidoPF($order, $valor, $return_url, $comment) {
    if (extension_loaded('soap')) {
      $client = @new SoapClient($this->get_url(), array('cache_wsdl' => WSDL_CACHE_NONE));
      return $client->pedidoPF(array(
        'chave' => $this->get_api_key(),
        'valor' => $this->money_format($valor),
        'id' => $order->get_id(),
        'admin_callback' => '',
        'url_retorno' => $return_url,
        'comentario' => $comment,
      ));
    } else {

      $url = 'https://' . get_option('eupago_endpoint') . '.eupago.pt/clientes/rest_api/paysafecard/create';
      $args = array(
          'body' => array(
            'chave' => $this->get_api_key(),
            'valor' => $this->money_format($valor),
            'id' => $order->get_id(),
            'admin_callback' => '',
            'url_retorno' => $return_url,
            'comentario' => $comment,
          ),
          'timeout'     => '60',
      );
      
      $response = wp_remote_post( $url, $args );
      $client     = wp_remote_retrieve_body( $response );

      return $client;
    }
  }

  public function pedidoPSC($order, $valor, $return_url, $lang, $comment) {
      $client = @new SoapClient($this->get_url(), array('cache_wsdl' => WSDL_CACHE_NONE));
      return $client->pedidoPSC(array(
        'chave' => $this->get_api_key(),
        'valor' => $this->money_format($valor),
        'id' => $order->get_id(),
        'url_retorno' => $return_url,
        'comentario' => $comment,
        'admin_callback' => '',
        'nome' => $order->get_billing_first_name() . ' ' . $order->get_billing_last_name(),
        'email' => $order->get_billing_email(),
        'lang' => $lang,
      ));
  }

  public function cofidispay_create($order_id)
  {
    $order = wc_get_order( $order_id );

    $data = [
      'payment' => [
        'identifier' => $order->get_order_number(),
        'amount' => [
          'value' => $order->get_total(),
          'currency' => 'EUR'
        ],
        'successUrl' => $order->get_checkout_order_received_url(),
        'failUrl' => $order->get_checkout_payment_url(),
      ],
      'customer' => [
        'notify' => false,
        'email' => $order->get_billing_email(),
        'name' => $order->get_formatted_billing_full_name(),
        'vatNumber' => get_post_meta($order_id, '_eupago_cofidis_vat_number', true),
        'phoneNumber' => $order->get_billing_phone(),
        'billingAddress' => [
          'address' => $order->get_billing_address_1() . ' ' . $order->get_billing_address_2(),
          'zipCode' => $order->get_billing_postcode(),
          'city' => $order->get_billing_city(),
        ],
      ],
      'items' => [],
    ];

    foreach ($order->get_items() as $item) {
      $product_variation_id = $item['variation_id'];

      // Check if product has variation.
      if ($product_variation_id) {
        $_product = wc_get_product($item['variation_id']);
      } else {
        $_product = wc_get_product($item['product_id']);
      }

      // Get SKU
      $item_sku = $_product->get_sku();

      $data['items'][] = [
        'reference' => $item_sku,
        'price' => $item->get_total() + $item->get_total_tax(),
        'quantity' => $item->get_quantity(),
        'tax' => $item->get_tax_class() == 'taxa-reduzida' ? 0 : 23, // TODO: Ver taxas iva
        'discount' => 0,
        'description' => $item->get_name(), // TODO: Ver o que é esta description
      ];
    }

    $portes = 0;

    $portes = $order->get_shipping_total() + $order->get_shipping_tax();

    foreach ($order->fee_lines as $fee_item) {
      $portes += $fee_item->total;
    }

    if ($portes > 0) {
       $data['items'][] = [
        'reference' => 'PORTES',
        'price' => $portes,
        'quantity' => 1,
        'tax' => 23,
        'discount' => 0,
        'description' => 'Custos de expedição', // TODO: Ver o que é esta description
      ];
    }

    $response = wp_remote_request(
      $this->get_cofidis_url(),
      [
        'method' => 'POST',
        'user-agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.8; rv:20.0) Gecko/20100101 Firefox/20.0',
        'headers'     => array(
          'Content-Type' => 'application/json',
          'Cache-Control' => 'no-cache',
          'Authorization' => 'ApiKey ' . $this->get_api_key(),
        ),
        'body' => json_encode($data)
      ]
    );
    $response_body = wp_remote_retrieve_body($response);

    return json_decode($response_body);
  }

}
