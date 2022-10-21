<?php
/**
 * PDF Invoicing for WooCommerce - Counters Section Settings
 *
 * @version 1.0.0
 * @since   1.0.0
 *
 * @author  Algoritmika Ltd
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_PDF_Invoicing_Settings_Counters' ) ) :

class Alg_WC_PDF_Invoicing_Settings_Counters extends Alg_WC_PDF_Invoicing_Settings_Section {

	/**
	 * Constructor.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function __construct() {
		$this->id   = 'counters';
		$this->desc = __( 'Counters', 'pdf-invoicing-for-woocommerce' );
		parent::__construct();
	}

	/**
	 * get_settings.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function get_settings() {

		$counters_settings = array(
			array(
				'title'    => __( 'Counters', 'pdf-invoicing-for-woocommerce' ),
				'desc'     => __( 'This section allows you to set current counters for each document.', 'pdf-invoicing-for-woocommerce' ) . ' ' .
					__( 'Counter number will be assigned to the new document and then automatically increased.', 'pdf-invoicing-for-woocommerce' ) . ' ' .
					sprintf( __( 'You can use counter in document settings with %s shortcode.', 'pdf-invoicing-for-woocommerce' ),
						'<code>[prop name="doc_counter"]</code>' ),
				'type'     => 'title',
				'id'       => 'alg_wc_pdf_invoicing_counters_options',
			),
			array(
				'title'    => alg_wc_pdf_invoicing()->core->get_doc_option( 0, 'admin_title' ),
				'type'     => 'number',
				'id'       => 'alg_wc_pdf_invoicing_counters[doc_0]',
				'default'  => 1,
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_pdf_invoicing_counters_options',
			),
		);

		return apply_filters( 'alg_wc_pdf_invoicing_counters_settings', $counters_settings );
	}

}

endif;

return new Alg_WC_PDF_Invoicing_Settings_Counters();
