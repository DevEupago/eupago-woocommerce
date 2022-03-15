<?php
/**
* EuPago Integration.
*
* @package  WC_EuPago_Integration
* @category Integration
* @author   WebAtual
*/

if ( ! class_exists( 'WC_EuPago_Integration' ) ) :

	class WC_EuPago_Integration extends WC_Integration {

		/**
		* Init and hook in the integration.
		*/
		public function __construct() {
			global $woocommerce;

			$this->id                 = 'eupago-gateway-for-woocommerce';
			//$this->method_title       = __( 'EuPago', 'eupago-gateway-for-woocommerce' );
			//$this->method_description = __( 'EuPago services integration.', 'eupago-gateway-for-woocommerce' );

			// Load the settings.
			//$this->init_form_fields();
			//$this->init_settings();

			// Define user set variables.
			$this->channel 		= get_option('eupago_channel');
			$this->api_key		= get_option('eupago_api_key');
			$this->debug 		= get_option('eupago_debug');
			$this->debug_email 	= trim($this->get_option( 'debug_email' ));

			$this->notify_url 	= ( get_option('permalink_structure') == '' ? home_url( '/' ) . '?wc-api=WC_euPago_WebAtual' : home_url( '/' ) . 'wc-api/WC_euPago_WebAtual/' );

			// Actions.
			//add_action( 'woocommerce_update_options_integration_' .  $this->id, array( $this, 'process_admin_options' ) );

		}

		/**
		* Initialize integration settings form fields.
		*/
		/*public function init_form_fields() {
			$this->form_fields = array(
				'channel' => array(
					'title' => __('Channel Name', 'eupago-gateway-for-woocommerce'),
					'type' => 'text',
					'description' => __('Account Channel', 'eupago-gateway-for-woocommerce'),
					'default' => ''
				),
				'api_key' => array(
					'title' => __('API Key', 'eupago-gateway-for-woocommerce'),
					'type' => 'text',
					'description' => sprintf( __( 'Please activate callback to this url on euPago dashboard: <code>%s</code>', 'eupago-gateway-for-woocommerce' ), (get_option('permalink_structure') == '' ? home_url( '/' ) . '?wc-api=WC_euPago' : home_url( '/' ) . 'wc-api/WC_euPago/' ) ),
					'default' => ''
				),
				'debug' => array(
					'title' => __( 'Debug Log', 'eupago-gateway-for-woocommerce' ),
					'type' => 'checkbox',
					'label' => __( 'Enable logging', 'eupago-gateway-for-woocommerce' ),
					'default' => 'no',
					'description' => sprintf( __( 'Log plugin events, such as callback requests, inside <code>%s</code>', 'eupago-gateway-for-woocommerce' ), wc_get_log_file_path($this->id) ),
				),
				'debug_email' => array(
					'title' => __( 'Debug to email', 'eupago-gateway-for-woocommerce' ),
					'type' => 'email',
					'label' => __( 'Enable email logging', 'eupago-gateway-for-woocommerce' ),
					'default' => '',
					'description' => __( 'Send plugin events to this email address, such as callback requests.', 'eupago-gateway-for-woocommerce' ),
				)
			);
		}*/

		public function get_api() {
			return $this->api_key;
		}

	}

endif;
