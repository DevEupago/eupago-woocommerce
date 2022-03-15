<?php
/**
* Plugin Name: EuPago Gateway For Woocommerce
* Plugin URI: 
* Description: This plugin allows customers to pay their orders with Multibanco, MB WAY, Payshop and Credit Card with euPago’s gateway. Beta Version.
* Version: 3.0
* Author: euPago
* Author URI: https://www.eupago.pt/
* Text Domain: eupago-gateway-for-woocommerce
* WC tested up to: 5.8.1
**/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( !class_exists( 'WC_EuPago' ) ) :
	class WC_EuPago {
		/**
		* Plugin version.
		*
		* @var string
		*/
		const VERSION = '3.0';

		/**
		* Instance of this class.
		*
		* @var object
		*/
		protected static $instance = null;

		/**
		* Initialize the plugin public actions.
		*/
		public function __construct() {
			// Load plugin text domain
			add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

			// Load CSS and JS
			add_action( 'admin_enqueue_scripts', array( $this, 'load_scripts' ) );		

			// Checks with WooCommerce is installed.
			if ( class_exists( 'WC_Payment_Gateway' ) ) {
				$this->includes();

				add_filter( 'woocommerce_payment_gateways', array( $this, 'add_gateway' ) );

				add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'plugin_action_links' ) );

				// Register the integration.
				//add_filter( 'woocommerce_integrations', array( $this, 'add_integration' ) );

				add_action( 'add_meta_boxes', array( $this, 'eupago_order_add_meta_box' ) );

				// Set Callback.
				new WC_EuPago_Callback();

			} else {
				add_action( 'admin_notices', array( $this, 'woocommerce_missing_notice' ) );
			}
		}

		/**
		* Return an instance of this class.
		*
		* @return object A single instance of this class.
		*/
		public static function get_instance() {
			// If the single instance hasn't been set, set it now.
			if ( null == self::$instance ) {
				self::$instance = new self;
			}

			return self::$instance;
		}

		/**
		* Get templates path.
		*
		* @return string
		*/
		public static function get_templates_path() {
			return plugin_dir_path( __FILE__ ) . 'templates/';
		}

		/**
		* Load the plugin text domain for translation.
		*/
		public function load_plugin_textdomain() {
			load_plugin_textdomain( 'eupago-gateway-for-woocommerce', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
		}


		/**
		* Load css.
		*/
		public function load_scripts() {
			wp_enqueue_style( 'admin_style', plugin_dir_url( __FILE__ ) . 'assets/css/admin_style.css' );
			wp_enqueue_script('admin_script', plugin_dir_url( __FILE__ ) . 'assets/js/admin_js.js', array('jquery'), true);
			wp_localize_script( 'admin_script', 'MYajax', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
		}



		/**
		* Action links.
		*
		* @param  array $links
		*
		* @return array
		*/
		public function plugin_action_links( $links ) {
			$plugin_links = [
				'<a href="' . esc_url( admin_url( 'admin.php?page=eupago' ) ) . '">' . __( 'API Settings', 'eupago-gateway-for-woocommerce' ) . '</a>',
				'<a href="' . esc_url( admin_url( 'admin.php?page=wc-settings&tab=checkout&section=eupago_multibanco' ) ) . '">' . __( 'Multibanco', 'eupago-gateway-for-woocommerce' ) . '</a>',
				'<a href="' . esc_url( admin_url( 'admin.php?page=wc-settings&tab=checkout&section=eupago_mbway' ) ) . '">' . __( 'MB WAY', 'eupago-gateway-for-woocommerce' ) . '</a>',
				'<a href="' . esc_url( admin_url( 'admin.php?page=wc-settings&tab=checkout&section=eupago_cc' ) ) . '">' . __( 'Credit Card', 'eupago-gateway-for-woocommerce' ) . '</a>',
				'<a href="' . esc_url( admin_url( 'admin.php?page=wc-settings&tab=checkout&section=eupago_payshop' ) ) . '">' . __( 'Payshop', 'eupago-gateway-for-woocommerce' ) . '</a>'
			];

			return array_merge( $plugin_links, $links );
		}

		/**
		* Includes.
		*/
		private function includes() {
			include_once 'includes/class-wc-eupago-integration.php';
			include_once 'includes/class-wc-eupago-api.php';
			include_once 'includes/class-wc-eupago-multibanco.php';
			include_once 'includes/class-wc-eupago-payshop.php';
			//include_once 'includes/class-wc-eupago-pagaqui.php';
			include_once 'includes/class-wc-eupago-mbway.php';
			include_once 'includes/class-wc-eupago-cc.php';
			include_once 'includes/class-wc-eupago-paysafecard.php';
			//include_once 'includes/class-wc-eupago-paysafecash.php';
			include_once 'includes/class-wc-eupago-callback.php';
			include_once 'includes/hooks/hooks-refund.php';
			include_once 'includes/hooks/hooks-sms.php';
			include_once 'includes/views/eupago-admin-page.php';
		}

		/**
		* Add the gateway to WooCommerce.
		*
		* @param   array $methods WooCommerce payment methods.
		*
		* @return  array          Payment methods with EuPago.
		*/
		public function add_gateway( $methods ) {
			$methods[] = 'WC_EuPago_Multibanco_WebAtual';
			$methods[] = 'WC_EuPago_PayShop_WebAtual';
			$methods[] = 'WC_EuPago_MBWAY_WebAtual';
			$methods[] = 'WC_EuPago_Pagaqui_WebAtual';
			$methods[] = 'WC_EuPago_CC_WebAtual';
			$methods[] = 'WC_EuPago_PF_WebAtual';
			$methods[] = 'WC_EuPago_PSC_WebAtual';

			return $methods;
		}

		/* Add a new integration to WooCommerce. */
		public function add_integration( $integrations ) {
			$integrations[] = 'WC_EuPago_Integration';
			return $integrations;
		}

		/* Order metabox to show Multibanco payment details */
		public function eupago_order_add_meta_box() {
			add_meta_box( 'woocommerce_eupago', __( 'EuPago Payment Details', 'eupago-gateway-for-woocommerce' ), array( $this, 'mbeupago_order_meta_box_html' ), 'shop_order', 'side', 'core');
		}

		public function mbeupago_order_meta_box_html($post) {
			include 'includes/views/order-meta-box.php';
		}

		/* WooCommerce fallback notice. */
		public function woocommerce_missing_notice() {
			echo '<div class="error"><p>' . sprintf( __( 'EuPago for WooCommerce Gateway depends on the last version of %s to work!', 'eupago-gateway-for-woocommerce' ), '<a href="https://wordpress.org/plugins/woocommerce/">' . __( 'WooCommerce', 'eupago-gateway-for-woocommerce' ) . '</a>' ) . '</p></div>';
			}

			public function woocommerce_payment_complete_reduce_order_stock( $reduce, $order, $payment_method, $stock_when ) {
				if ( $reduce ) {
					// $order = new WC_Order( $order_id );
					if ( $order->get_payment_method() == $payment_method ) {
						if ( version_compare( WC_VERSION, '3.4.0', '>=' ) ) {
							//After 3.4.0
							if ( $order->has_status( array( 'pending', 'on-hold' ) ) ) {
								//Pending payment
								return $stock_when == 'order' ? true : false;
							} else {
								//Payment done
								return $stock_when == '' ? true : false;
							}
						} else {
							//Before 3.4.0 - This only runs for paid orders
							return $stock_when == 'order' ? true : false;
						}
					} else {
						return $reduce;
					}
				} else {
					//Already reduced
					return false;
				}
			}
		}

		add_action( 'plugins_loaded', array( 'WC_EuPago', 'get_instance' ) );

	endif;
