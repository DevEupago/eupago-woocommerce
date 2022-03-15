<?php global $woocommerce; ?>
<div id="wc_eupago">
  <div id="wc_eupago_settings">
    <h3><?php echo $this->method_title; ?> <span style="font-size: 75%;">v.<?php echo WC_EuPago::VERSION; ?></span></h3>
    
    <?php if ($this->id == 'eupago_cc') { ?>
      <?php if (!extension_loaded('soap')) { ?>
        <div class="eupago-soap-error">
          <p><?php esc_html_e('Requires SOAP protocol active on your server.', 'eupago-gateway-for-woocommerce'); ?></p>
        </div>
      <?php } ?>

      <h4><?php esc_html_e('How can I provide this payment method?', 'eupago-gateway-for-woocommerce'); ?></h4>
      <p>
        <?php esc_html_e('You must first request its activation on your account by email:', 'eupago-gateway-for-woocommerce'); ?> 
        <a href="mailto:comercial@eupago.pt" target="_BLANK">comercial@eupago.pt</a>
      </p>
      <p><?php esc_html_e('This request is subject to the approval of euPago\'s compliance department.', 'eupago-gateway-for-woocommerce'); ?></p>
    <?php } ?>
      
    <?php if ($this->id == 'eupago_psc') { ?>        
      <?php if (!extension_loaded('soap')) { ?>
        <div class="eupago-soap-error">
          <p><?php esc_html_e('Requires SOAP protocol active on your server.', 'eupago-gateway-for-woocommerce'); ?></p>
        </div>
      <?php } ?>
    <?php } ?>

    <table class="form-table">
      <?php
      if ( trim( get_woocommerce_currency() ) == 'EUR' ) {
        $this->generate_settings_html();
      } else {
        ?>
        <p><strong><?php _e('ERROR!', 'eupago-gateway-for-woocommerce'); ?> <?php printf( __('Set WooCommerce currency to <strong>Euros (&euro;)</strong> %1$s', 'eupago-gateway-for-woocommerce'), '<a href="admin.php?page=wc-settings&tab=general">'.__('here', 'eupago-gateway-for-woocommerce').'</a>.'); ?></strong></p>
        <?php
      }
      ?>
    </table>
  </div>
</div>
<div class="clear"></div>
<style type="text/css">
@media (min-width: 961px) {
  #wc_eupago { height: auto; overflow: hidden; }
  #wc_eupago_settings { width: auto; overflow: hidden; }
}
.wc_eupago_list { list-style-type: disc; list-style-position: inside; }
.wc_eupago_list li { margin-left: 1.5em; }
</style>
