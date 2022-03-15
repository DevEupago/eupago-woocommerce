<?php
$order = new WC_Order( $post->ID );
echo '<p>';

$payment_method = version_compare( WC_VERSION, '3.0', '>=' ) ? $order->get_payment_method() : $order->payment_method;
$payment_method_title = version_compare( WC_VERSION, '3.0', '>=' ) ? $order->get_payment_method_title() : $order->payment_method_title;
$order_total = version_compare( WC_VERSION, '3.0', '>=' ) ? $order->get_total() : $order->order_total;

switch ($payment_method) {
  case 'eupago_multibanco':
  echo '<img src="' . plugins_url('assets/images/multibanco_banner.png', dirname(dirname(__FILE__))) . '" alt="' . esc_attr($payment_method_title) . '" title="' . esc_attr($payment_method_title) . '" /><br />';
  echo __('Entity', 'eupago-gateway-for-woocommerce').': '.trim(get_post_meta($post->ID, '_eupago_multibanco_entidade', true)).'<br/>';
  echo __('Reference', 'eupago-gateway-for-woocommerce').': '.chunk_split(trim(get_post_meta($post->ID, '_eupago_multibanco_referencia', true)), 3, ' ').'<br/>';
  echo __('Value', 'eupago-gateway-for-woocommerce').': '.wc_price( $order_total );
  break;

  case 'eupago_payshop':
  echo '<img src="' . plugins_url('assets/images/payshop_banner.png', dirname(dirname(__FILE__))) . '" alt="' . esc_attr($payment_method_title) . '" title="' . esc_attr($payment_method_title) . '" /><br />';
  echo __('Reference', 'eupago-gateway-for-woocommerce').': '.chunk_split(trim(get_post_meta($post->ID, '_eupago_payshop_referencia', true)), 3, ' ').'<br/>';
  echo __('Value', 'eupago-gateway-for-woocommerce').': '.wc_price( $order_total );
  break;

  case 'eupago_pagaqui':
  echo '<img src="' . plugins_url('assets/images/pagaqui_banner.png', dirname(dirname(__FILE__))) . '" alt="' . esc_attr($payment_method_title) . '" title="' . esc_attr($payment_method_title) . '" /><br />';
  echo __('Reference', 'eupago-gateway-for-woocommerce').': '.chunk_split(trim(get_post_meta($post->ID, '_eupago_pagaqui_referencia', true)), 3, ' ').'<br/>';
  echo __('Value', 'eupago-gateway-for-woocommerce').': '.wc_price( $order_total );
  break;

  case 'eupago_mbway':
  echo '<img src="' . plugins_url('assets/images/mbway_banner.png', dirname(dirname(__FILE__))) . '" alt="' . esc_attr($payment_method_title) . '" title="' . esc_attr($payment_method_title) . '" /><br />';
  echo __('Reference', 'eupago-gateway-for-woocommerce').': '.trim(get_post_meta($post->ID, '_eupago_mbway_referencia', true)).'<br/>';
  echo __('Value', 'eupago-gateway-for-woocommerce').': '.wc_price( $order_total );
  break;

  default:
  echo __('No details available', 'eupago-gateway-for-woocommerce');
  break;
}
echo '</p>';