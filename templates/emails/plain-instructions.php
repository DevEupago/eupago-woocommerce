html-<?php
/**
* Plain email instructions.
*
* @author  WebAtual
* @package eupago-gateway-for-woocommerce/Templates
* @version 0.1
*/

if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly.
}
?>

<?php
if ($method == 'eupago_multibanco') :
  _e('Payment instructions', 'eupago-gateway-for-woocommerce');
  echo "\n";
  _e('Entity', 'eupago-gateway-for-woocommerce'); echo esc_html(': '); echo esc_html($entidade);
  echo "\n";
  _e('Reference', 'eupago-gateway-for-woocommerce'); echo esc_html(': '); echo esc_html(chunk_split($referencia, 3, ' '));
  echo "\n";
  _e('Value', 'eupago-gateway-for-woocommerce'); echo esc_html(': '); echo esc_html($order_total); echo esc_html('€');
  echo "\n";
  _e('The receipt issued by the ATM machine is a proof of payment. Keep it.', 'eupago-gateway-for-woocommerce');
  elseif ($method == 'eupago_payshop') :
    _e('Payment instructions', 'eupago-gateway-for-woocommerce');
    echo "\n";
    _e('Reference', 'eupago-gateway-for-woocommerce'); echo esc_html(': '); echo esc_html(chunk_split($referencia, 3, ' '));
    echo "\n";
    _e('Value', 'eupago-gateway-for-woocommerce'); echo esc_html(': '); echo esc_html($order_total); echo esc_html('€');
    echo "\n";
    _e('The receipt issued by the ATM machine is a proof of payment. Keep it.', 'eupago-gateway-for-woocommerce');
    elseif ($method == 'eupago_pagaqui') :
      _e('Payment instructions', 'eupago-gateway-for-woocommerce');
      echo "\n";
      _e('Reference', 'eupago-gateway-for-woocommerce'); echo esc_html(': '); echo esc_html(chunk_split($referencia, 3, ' '));
      echo "\n";
      _e('Value', 'eupago-gateway-for-woocommerce'); echo esc_html(': '); echo esc_html($order_total); echo esc_html('€');
      echo "\n";
      _e('The receipt issued by the ATM machine is a proof of payment. Keep it.', 'eupago-gateway-for-woocommerce');
    elseif ($method == 'eupago_mbway') :
      _e('Payment instructions', 'eupago-gateway-for-woocommerce');
      echo "\n";
      _e('Entity', 'eupago-gateway-for-woocommerce'); echo esc_html(': '); echo esc_html($entidade);
      echo "\n";
      _e('Reference', 'eupago-gateway-for-woocommerce'); echo esc_html(': '); echo esc_html(chunk_split($referencia, 3, ' '));
      echo "\n";
      _e('Value', 'eupago-gateway-for-woocommerce'); echo esc_html(': '); echo esc_html($order_total); echo esc_html('€');
      echo "\n";
      _e('Accept this payment at your MBWAY mobile app', 'eupago-gateway-for-woocommerce');
      else :
        _e('Error getting payment details', 'eupago-gateway-for-woocommerce');
      endif;
      ?>
