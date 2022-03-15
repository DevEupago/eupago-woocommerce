<?php
/**
* Payment instructions.
*
* @author  WebAtual
* @package WooCommerce_EuPago/Templates
* @version 0.1
*/

if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly.
}
?>
<style type="text/css">
table.woocommerce_eupago_table { width: auto !important; margin: auto; }
table.woocommerce_eupago_table td,	table.woocommerce_eupago_table th { background-color: #FFFFFF; color: #000000; padding: 10px; vertical-align: middle; }
table.woocommerce_eupago_table th { text-align: center; font-weight: bold; }
table.woocommerce_eupago_table th img { margin: auto; margin-top: 10px; }
</style>
<?php if ($method == 'eupago_multibanco') : ?>
  <table class="woocommerce_eupago_table" cellpadding="0" cellspacing="0">
    <tr>
      <th colspan="2">
        <?php _e('Payment instructions', 'eupago-gateway-for-woocommerce'); ?>
        <br/>
        <img src="<?php echo plugins_url('assets/images/multibanco_banner.png', dirname(__FILE__)); ?>" alt="<?php echo esc_attr($payment_name); ?>" title="<?php echo esc_attr($payment_name); ?>"/>
      </th>
    </tr>
    <tr>
      <td><?php _e('Entity', 'eupago-gateway-for-woocommerce'); ?>:</td>
      <td><?php echo esc_html($entidade); ?></td>
    </tr>
    <tr>
      <td><?php _e('Reference', 'eupago-gateway-for-woocommerce'); ?>:</td>
      <td><?php echo esc_html(chunk_split($referencia, 3, ' ')); ?></td>
    </tr>
    <tr>
      <td><?php _e('Value', 'eupago-gateway-for-woocommerce'); ?>:</td>
      <td><?php echo esc_html($order_total.'€'); ?></td>
    </tr>
    <?php if ( isset( $data_fim ) && !empty( $data_fim ) ) : ?>
    <tr>
      <td><?php _e('Limit Date', 'eupago-gateway-for-woocommerce'); ?>:</td>
      <td><?php echo date_i18n( wc_date_format(), strtotime( $data_fim ) ); ?></td>
    </tr>
  <?php endif; ?>
    <tr>
      <td colspan="2" style="font-size: small;"><?php _e('The receipt issued by the ATM machine is a proof of payment. Keep it.', 'eupago-gateway-for-woocommerce'); ?></td>
    </tr>
  </table>
  <?php
  elseif ($method == 'eupago_payshop') : ?>
  <table class="woocommerce_eupago_table" cellpadding="0" cellspacing="0">
    <tr>
      <th colspan="2">
        <?php _e('Payment instructions', 'eupago-gateway-for-woocommerce'); ?>
        <br/>
        <img src="<?php echo plugins_url('assets/images/payshop_banner.png', dirname(__FILE__)); ?>" alt="<?php echo esc_attr($payment_name); ?>" title="<?php echo esc_attr($payment_name); ?>"/>
      </th>
    </tr>
    <tr>
      <td><?php _e('Reference', 'eupago-gateway-for-woocommerce'); ?>:</td>
      <td><?php echo chunk_split($referencia, 3, ' '); ?></td>
    </tr>
    <tr>
      <td><?php _e('Value', 'eupago-gateway-for-woocommerce'); ?>:</td>
      <td><?php echo esc_html($order_total.'€'); ?></td>
    </tr>
    <tr>
      <td colspan="2" style="font-size: small;"><?php _e('The receipt issued by the ATM machine is a proof of payment. Keep it.', 'eupago-gateway-for-woocommerce'); ?></td>
    </tr>
  </table>
  <?php
  elseif ($method == 'eupago_pagaqui') : ?>
  <table class="woocommerce_eupago_table" cellpadding="0" cellspacing="0">
    <tr>
      <th colspan="2">
        <?php _e('Payment instructions', 'eupago-gateway-for-woocommerce'); ?>
        <br/>
        <img src="<?php echo plugins_url('assets/images/pagaqui_banner.png', dirname(__FILE__)); ?>" alt="<?php echo esc_attr($payment_name); ?>" title="<?php echo esc_attr($payment_name); ?>"/>
      </th>
    </tr>
    <tr>
      <td><?php _e('Reference', 'eupago-gateway-for-woocommerce'); ?>:</td>
      <td><?php echo esc_html(chunk_split($referencia, 3, ' ')); ?></td>
    </tr>
    <tr>
      <td><?php _e('Value', 'eupago-gateway-for-woocommerce'); ?>:</td>
      <td><?php echo esc_html($order_total.'€'); ?>/td>
    </tr>
    <tr>
      <td colspan="2" style="font-size: small;"><?php _e('The receipt issued by the ATM machine is a proof of payment. Keep it.', 'eupago-gateway-for-woocommerce'); ?></td>
    </tr>
  </table>
<?php elseif ($method == 'eupago_mbway') : ?>
  <table class="woocommerce_eupago_table" cellpadding="0" cellspacing="0">
    <tr>
      <th colspan="2">
        <?php _e('Payment instructions', 'eupago-gateway-for-woocommerce'); ?>
        <br/>
        <img src="<?php echo plugins_url('assets/images/mbway_banner.png', dirname(__FILE__)); ?>" alt="<?php echo esc_attr($payment_name); ?>" title="<?php echo esc_attr($payment_name); ?>"/>
      </th>
    </tr>
    <tr>
      <td><?php _e('Reference', 'eupago-gateway-for-woocommerce'); ?>:</td>
      <td><?php echo esc_html(chunk_split($referencia, 3, ' ')); ?></td>
    </tr>
    <tr>
      <td><?php _e('Value', 'eupago-gateway-for-woocommerce'); ?>:</td>
      <td><?php echo esc_html($order_total.'€'); ?></td>
    </tr>
    <tr>
      <td colspan="2" style="font-size: small;"><?php _e('Accept this payment with your MBWAY mobile app', 'eupago-gateway-for-woocommerce'); ?></td>
    </tr>
  </table>
<?php endif;?>
