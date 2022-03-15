<?php
/**
* HTML email instructions.
*
* @author  WebAtual
* @package eupago-gateway-for-woocommerce/Templates
* @version 0.1
*/

if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly.
}
?>

<?php if ($method == 'eupago_multibanco') : ?>
  <?php echo wpautop( wptexturize( $instructions ) ) . PHP_EOL; ?>
  <table cellpadding="10" cellspacing="0" align="center" border="0" style="margin: auto; margin-top: 10px; margin-bottom: 10px; border-collapse: collapse; border: 1px solid #1465AA; border-radius: 4px !important; background-color: #FFFFFF;">
    <tr>
      <td style="border: 1px solid #1465AA; border-top-right-radius: 4px !important; border-top-left-radius: 4px !important; text-align: center; color: #000000; font-weight: bold;" colspan="2">
        <?php _e('Payment instructions', 'eupago-gateway-for-woocommerce'); ?>
        <br/>
        <img src="<?php echo plugins_url('assets/images/multibanco_banner.png', dirname(dirname(__FILE__))); ?>" alt="<?php echo esc_attr($payment_name); ?>" title="<?php echo esc_attr($payment_name); ?>" style="margin-top: 10px;"/>
      </td>
    </tr>
    <tr>
      <td style="border: 1px solid #1465AA; color: #000000;"><?php _e('Entity', 'eupago-gateway-for-woocommerce'); ?>:</td>
      <td style="border: 1px solid #1465AA; color: #000000; white-space: nowrap;"><?php echo esc_html($entidade); ?></td>
    </tr>
    <tr>
      <td style="border: 1px solid #1465AA; color: #000000;"><?php _e('Reference', 'eupago-gateway-for-woocommerce'); ?>:</td>
      <td style="border: 1px solid #1465AA; color: #000000; white-space: nowrap;"><?php echo esc_html(chunk_split($referencia, 3, ' ')); ?></td>
    </tr>
    <tr>
      <td style="border: 1px solid #1465AA; color: #000000;"><?php _e('Value', 'eupago-gateway-for-woocommerce'); ?>:</td>
      <td style="border: 1px solid #1465AA; color: #000000; white-space: nowrap;"><?php echo esc_html($order_total); ?>€;</td>
    </tr>
    <?php if ( isset( $data_fim ) && !empty( $data_fim ) ) : ?>
<tr>
      <td style="border: 1px solid #1465AA; color: #000000;"><?php _e('Limit Date', 'eupago-gateway-for-woocommerce'); ?>:</td>
      <td style="border: 1px solid #1465AA; color: #000000; white-space: nowrap;"><?php echo esc_html($data_fim); ?></td>
    <tr>
    <?php endif; ?>

      <td style="font-size: x-small; border: 1px solid #1465AA; border-bottom-right-radius: 4px !important; border-bottom-left-radius: 4px !important; color: #000000; text-align: center;" colspan="2"><?php _e('The receipt issued by the ATM machine is a proof of payment. Keep it.', 'eupago-gateway-for-woocommerce'); ?></td>
    </tr>
  </table>
<?php elseif ($method == 'eupago_payshop') : ?>
  <?php echo esc_html(wpautop( wptexturize( $instructions ) ) . PHP_EOL); ?>
  <table cellpadding="10" cellspacing="0" align="center" border="0" style="margin: auto; margin-top: 10px; margin-bottom: 10px; border-collapse: collapse; border: 1px solid #1465AA; border-radius: 4px !important; background-color: #FFFFFF;">
    <tr>
      <td style="border: 1px solid #1465AA; border-top-right-radius: 4px !important; border-top-left-radius: 4px !important; text-align: center; color: #000000; font-weight: bold;" colspan="2">
        <?php _e('Payment instructions', 'eupago-gateway-for-woocommerce'); ?>
        <br/>
        <img src="<?php echo plugins_url('assets/images/payshop_banner.png', dirname(dirname(__FILE__))); ?>" alt="<?php echo esc_attr($payment_name); ?>" title="<?php echo esc_attr($payment_name); ?>" style="margin-top: 10px;"/>
      </td>
    </tr>
    <tr>
      <td style="border: 1px solid #1465AA; color: #000000;"><?php _e('Reference', 'eupago-gateway-for-woocommerce'); ?>:</td>
      <td style="border: 1px solid #1465AA; color: #000000; white-space: nowrap;"><?php echo esc_html(chunk_split($referencia, 3, ' ')); ?></td>
    </tr>
    <tr>
      <td style="border: 1px solid #1465AA; color: #000000;"><?php _e('Value', 'eupago-gateway-for-woocommerce'); ?>:</td>
      <td style="border: 1px solid #1465AA; color: #000000; white-space: nowrap;"><?php echo esc_html($order_total); ?>€;</td>
    </tr>
    <tr>
      <td style="font-size: x-small; border: 1px solid #1465AA; border-bottom-right-radius: 4px !important; border-bottom-left-radius: 4px !important; color: #000000; text-align: center;" colspan="2"><?php _e('The receipt issued by the ATM machine is a proof of payment. Keep it.', 'eupago-gateway-for-woocommerce'); ?></td>
    </tr>
  </table>
  <?php elseif ($method == 'eupago_pagaqui') : ?>
  <table cellpadding="10" cellspacing="0" align="center" border="0" style="margin: auto; margin-top: 10px; margin-bottom: 10px; border-collapse: collapse; border: 1px solid #1465AA; border-radius: 4px !important; background-color: #FFFFFF;">
    <tr>
      <td style="border: 1px solid #1465AA; border-top-right-radius: 4px !important; border-top-left-radius: 4px !important; text-align: center; color: #000000; font-weight: bold;" colspan="2">
        <?php _e('Payment instructions', 'eupago-gateway-for-woocommerce'); ?>
        <br/>
        <img src="<?php echo plugins_url('assets/images/pagaqui_banner.png', dirname(dirname(__FILE__))); ?>" alt="<?php echo esc_attr($payment_name); ?>" title="<?php echo esc_attr($payment_name); ?>" style="margin-top: 10px;"/>
      </td>
    </tr>
    <tr>
      <td style="border: 1px solid #1465AA; color: #000000;"><?php _e('Reference', 'eupago-gateway-for-woocommerce'); ?>:</td>
      <td style="border: 1px solid #1465AA; color: #000000; white-space: nowrap;"><?php echo esc_html(chunk_split($referencia, 3, ' ')); ?></td>
    </tr>
    <tr>
      <td style="border: 1px solid #1465AA; color: #000000;"><?php _e('Value', 'eupago-gateway-for-woocommerce'); ?>:</td>
      <td style="border: 1px solid #1465AA; color: #000000; white-space: nowrap;"><?php echo esc_html($order_total); ?>€;</td>
    </tr>
    <tr>
      <td style="font-size: x-small; border: 1px solid #1465AA; border-bottom-right-radius: 4px !important; border-bottom-left-radius: 4px !important; color: #000000; text-align: center;" colspan="2"><?php _e('The receipt issued by the ATM machine is a proof of payment. Keep it.', 'eupago-gateway-for-woocommerce'); ?></td>
    </tr>
  </table>
<?php elseif ($method == 'eupago_mbway') : ?>
  <table cellpadding="10" cellspacing="0" align="center" border="0" style="margin: auto; margin-top: 10px; margin-bottom: 10px; border-collapse: collapse; border: 1px solid #1465AA; border-radius: 4px !important; background-color: #FFFFFF;">
    <tr>
      <td style="border: 1px solid #1465AA; border-top-right-radius: 4px !important; border-top-left-radius: 4px !important; text-align: center; color: #000000; font-weight: bold;" colspan="2">
        <?php _e('Payment instructions', 'eupago-gateway-for-woocommerce'); ?>
        <br/>
        <img src="<?php echo plugins_url('assets/images/mbway_banner.png', dirname(dirname(__FILE__))); ?>" alt="<?php echo esc_attr($payment_name); ?>" title="<?php echo esc_attr($payment_name); ?>" style="margin-top: 10px;"/>
      </td>
    </tr>
    <tr>
      <td style="border: 1px solid #1465AA; color: #000000;"><?php _e('Entity', 'eupago-gateway-for-woocommerce'); ?>:</td>
      <td style="border: 1px solid #1465AA; color: #000000; white-space: nowrap;">EUPAGO.PT</td>
    </tr>
    <tr>
      <td style="border: 1px solid #1465AA; color: #000000;"><?php _e('Reference', 'eupago-gateway-for-woocommerce'); ?>:</td>
      <td style="border: 1px solid #1465AA; color: #000000; white-space: nowrap;"><?php echo esc_html(chunk_split($referencia, 3, ' ')); ?></td>
    </tr>
    <tr>
      <td style="border: 1px solid #1465AA; color: #000000;"><?php _e('Value', 'eupago-gateway-for-woocommerce'); ?>:</td>
      <td style="border: 1px solid #1465AA; color: #000000; white-space: nowrap;"><?php echo esc_html($order_total); ?>€;</td>
    </tr>
    <tr>
      <td style="font-size: x-small; border: 1px solid #1465AA; border-bottom-right-radius: 4px !important; border-bottom-left-radius: 4px !important; color: #000000; text-align: center;" colspan="2"><?php _e('Accept this payment at your MBWAY mobile app.', 'eupago-gateway-for-woocommerce'); ?></td>
    </tr>
  </table>
<?php else :
  echo '<p><strong>' . __('Error getting payment details', 'eupago-gateway-for-woocommerce') . '</strong>';
endif; ?>
