<?php
/**
 * PDF Invoicing for WooCommerce - Counters Section Settings
 *
 * @version 2.5.0
 * @since   1.0.0
 *
 * @author WPFactory
 *
 * @package WPFactory\WC_PDF_Invoicing\Settings
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_PDF_Invoicing_Settings_Counters' ) ) :

	/**
	 * Alg_WC_PDF_Invoicing_Settings_Counters class.
	 *
	 * @version 2.5.0
	 * @since   1.0.0
	 */
	class Alg_WC_PDF_Invoicing_Settings_Counters extends Alg_WC_PDF_Invoicing_Settings_Section {

		/**
		 * Constructor.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 */
		public function __construct() {
			$this->id   = 'counters';
			$this->desc = __( 'Counters', 'pdf-invoicing-for-woocommerce' );
			parent::__construct();
		}

		/**
		 * Get settings.
		 *
		 * @version 2.5.0
		 * @since   1.0.0
		 */
		public function get_settings() {
			$counters_settings = array(
				array(
					'title' => __( 'Counters', 'pdf-invoicing-for-woocommerce' ),
					'desc'  => (
						__( 'This section allows you to set current counters for each document.', 'pdf-invoicing-for-woocommerce' ) . ' ' .
						__( 'Counter number will be assigned to the new document and then automatically increased.', 'pdf-invoicing-for-woocommerce' ) . ' ' .
						sprintf(
							/* Translators: %s: Shortcode name. */
							__( 'You can use counter in document settings with %s shortcode.', 'pdf-invoicing-for-woocommerce' ),
							'<code>[prop name="doc_counter"]</code>'
						)
					),
					'type'  => 'title',
					'id'    => 'alg_wc_pdf_invoicing_counters_options',
				),
			);

			$total = get_option( 'alg_wc_pdf_invoicing_total_docs', 1 );
			for ( $doc_id = 0; $doc_id < $total; $doc_id++ ) {
				$counters_settings[] = array(
					'title'   => alg_wc_pdf_invoicing()->core->get_doc_option( $doc_id, 'admin_title' ),
					'type'    => 'number',
					'id'      => "alg_wc_pdf_invoicing_counters[doc_{$doc_id}]",
					'default' => 1,
				);
			}

			$counters_settings[] = array(
				'type' => 'sectionend',
				'id'   => 'alg_wc_pdf_invoicing_counters_options',
			);

			return apply_filters( 'alg_wc_pdf_invoicing_counters_settings', $counters_settings );
		}
	}

endif;

return new Alg_WC_PDF_Invoicing_Settings_Counters();
