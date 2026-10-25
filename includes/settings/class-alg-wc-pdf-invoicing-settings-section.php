<?php
/**
 * PDF Invoicing for WooCommerce - Section Settings
 *
 * @version 2.5.0
 * @since   1.0.0
 *
 * @author WPFactory
 *
 * @package WPFactory\WC_PDF_Invoicing\Settings
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_PDF_Invoicing_Settings_Section' ) ) :

	/**
	 * Alg_WC_PDF_Invoicing_Settings_Section class.
	 *
	 * @version 2.5.0
	 * @since   1.0.0
	 */
	class Alg_WC_PDF_Invoicing_Settings_Section {

		/**
		 * ID.
		 *
		 * @version 2.1.0
		 * @since   2.1.0
		 *
		 * @var string
		 */
		public $id;

		/**
		 * Description.
		 *
		 * @version 2.1.0
		 * @since   2.1.0
		 *
		 * @var string
		 */
		public $desc;

		/**
		 * Constructor.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 */
		public function __construct() {
			add_filter(
				'woocommerce_get_sections_alg_wc_pdf_invoicing',
				array( $this, 'settings_section' )
			);
			add_filter(
				'woocommerce_get_settings_alg_wc_pdf_invoicing_' . $this->id,
				array( $this, 'get_settings' ),
				PHP_INT_MAX
			);
		}

		/**
		 * Settings section.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @param array $sections Array of existing sections.
		 *
		 * @return array Modified array of sections.
		 */
		public function settings_section( $sections ) {
			$sections[ $this->id ] = $this->desc;
			return $sections;
		}

		/**
		 * Get uploads directory.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 */
		public function get_uploads_dir() {
			$uploads_dir = wp_upload_dir();
			return $uploads_dir['basedir'] . '/';
		}

		/**
		 * Get emails.
		 *
		 * @version 2.5.0
		 * @since   1.0.0
		 *
		 * @todo (dev) Sort array?
		 */
		public function get_emails() {
			$emails    = array();
			$wc_emails = WC_Emails::instance();
			foreach ( $wc_emails->get_emails() as $email ) {
				$emails[ $email->id ] = $email->get_title();
			}
			$emails['customer_partially_refunded_order'] = __( 'Partially refunded order', 'pdf-invoicing-for-woocommerce' );
			return $emails;
		}

		/**
		 * Get hooks.
		 *
		 * @version 1.5.0
		 * @since   1.0.0
		 *
		 * @todo (dev) Sort array?
		 * @todo (feature) Custom hooks.
		 */
		public function get_hooks() {
			$hooks = array(
				'manual'                               => __( 'Manually', 'pdf-invoicing-for-woocommerce' ),
				'woocommerce_new_order'                => __( 'On new order', 'pdf-invoicing-for-woocommerce' ),
				'woocommerce_payment_complete'         => __( 'On payment complete', 'pdf-invoicing-for-woocommerce' ),
				'woocommerce_checkout_order_processed' => __( 'On checkout order processed', 'pdf-invoicing-for-woocommerce' ),
				'woocommerce_order_partially_refunded' => __( 'On order partially refunded', 'pdf-invoicing-for-woocommerce' ),
			);
			foreach ( wc_get_order_statuses() as $status_slug => $status_name ) {
				if ( 'wc-' === substr( $status_slug, 0, 3 ) ) {
					$status_slug = substr( $status_slug, 3 );
				}
				$hooks[ 'woocommerce_order_status_' . $status_slug ] = sprintf(
					/* Translators: %s: Order status name. */
					__( 'On order status %s', 'pdf-invoicing-for-woocommerce' ),
					$status_name
				);
			}
			return $hooks;
		}

		/**
		 * Get fonts.
		 *
		 * @version 1.4.0
		 * @since   1.4.0
		 */
		public function get_fonts() {
			return array(
				'dejavusans' => 'DejaVu Sans (Unicode)',
				'times'      => 'Times New Roman',
				'helvetica'  => 'Helvetica',
				'courier'    => 'Courier',
			);
		}

		/**
		 * Get fonts description.
		 *
		 * @version 1.4.0
		 * @since   1.4.0
		 *
		 * @todo (desc) Improve.
		 */
		public function get_fonts_desc() {
			return sprintf(
				/* Translators: %s: Font name. */
				__( 'If you are having issues displaying your language specific letters, select "%s" font.', 'pdf-invoicing-for-woocommerce' ),
				'DejaVu Sans (Unicode)'
			);
		}
	}

endif;
