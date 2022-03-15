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
}