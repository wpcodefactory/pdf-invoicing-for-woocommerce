<?php
/**
 * PDF Invoicing for WooCommerce - General Section Settings
 *
 * @version 1.8.0
 * @since   1.0.0
 *
 * @author  Algoritmika Ltd
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_PDF_Invoicing_Settings_General' ) ) :

class Alg_WC_PDF_Invoicing_Settings_General extends Alg_WC_PDF_Invoicing_Settings_Section {

	/**
	 * Constructor.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function __construct() {
		$this->id   = '';
		$this->desc = __( 'General', 'pdf-invoicing-for-woocommerce' );
		parent::__construct();
	}

	/**
	 * get_settings.
	 *
	 * @version 1.8.0
	 * @since   1.0.0
	 *
	 * @todo    [now] (dev) `alg_wc_pdf_invoicing_shortcode_prefix`: better default value, e.g. `alg_wc_pdf_` or `alg_wc_pi_`?
	 * @todo    [now] (desc) `alg_wc_pdf_invoicing_use_print_js`
	 */
	function get_settings() {

		$plugin_settings = array(
			array(
				'title'    => __( 'PDF Invoicing Options', 'pdf-invoicing-for-woocommerce' ),
				'type'     => 'title',
				'id'       => 'alg_wc_pdf_invoicing_plugin_options',
			),
			array(
				'title'    => __( 'PDF Invoicing', 'pdf-invoicing-for-woocommerce' ),
				'desc'     => '<strong>' . __( 'Enable plugin', 'pdf-invoicing-for-woocommerce' ) . '</strong>',
				'desc_tip' => __( 'Add PDF invoices to WooCommerce.', 'pdf-invoicing-for-woocommerce' ),
				'id'       => 'alg_wc_pdf_invoicing_plugin_enabled',
				'default'  => 'yes',
				'type'     => 'checkbox',
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_pdf_invoicing_plugin_options',
			),
		);

		$general_settings = array(
			array(
				'title'    => __( 'General Options', 'pdf-invoicing-for-woocommerce' ),
				'type'     => 'title',
				'id'       => 'alg_wc_pdf_invoicing_general_options',
			),
			array(
				'title'    => __( 'Total documents', 'pdf-invoicing-for-woocommerce' ),
				'desc_tip' => __( 'Sets total number of documents you wish to add.', 'pdf-invoicing-for-woocommerce' ) . ' ' .
					__( 'After you save changes, new settings sections will be displayed.', 'pdf-invoicing-for-woocommerce' ),
				'desc'     => apply_filters( 'alg_wc_pdf_invoicing_settings',
					'You will need <a href="https://wpfactory.com/item/pdf-invoicing-for-woocommerce/" target="_blank">PDF Invoicing for WooCommerce Pro</a> to add more than one document type.',
					'button' ),
				'id'       => 'alg_wc_pdf_invoicing_total_docs',
				'default'  => 1,
				'type'     => 'number',
				'custom_attributes' => apply_filters( 'alg_wc_pdf_invoicing_settings', array( 'readonly' => 'readonly' ), 'array' ),
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_pdf_invoicing_general_options',
			),
			array(
				'title'    => __( 'Advanced Options', 'pdf-invoicing-for-woocommerce' ),
				'type'     => 'title',
				'id'       => 'alg_wc_pdf_invoicing_advanced_options',
			),
			array(
				'title'    => __( 'Shortcode prefix', 'pdf-invoicing-for-woocommerce' ),
				'desc_tip' => __( 'Prefix for all plugin shortcodes. Setting this is useful if you have compatibility issues with other shortcodes (from other plugins) with matching names.', 'pdf-invoicing-for-woocommerce' ) . ' ' .
					__( 'Prefix will be automatically sanitized into a slug.', 'pdf-invoicing-for-woocommerce' ),
				'id'       => 'alg_wc_pdf_invoicing_shortcode_prefix',
				'default'  => '',
				'type'     => 'text',
				'alg_wc_pi_sanitize' => 'sanitize_title',
			),
			array(
				'title'    => __( 'Suppress errors', 'pdf-invoicing-for-woocommerce' ),
				'desc'     => __( 'Enable', 'pdf-invoicing-for-woocommerce' ) . ' (' . __( 'recommended', 'pdf-invoicing-for-woocommerce' ) . ')',
				'desc_tip' => __( 'Suppress PHP errors when generating PDF.', 'pdf-invoicing-for-woocommerce' ),
				'id'       => 'alg_wc_pdf_invoicing_suppress_errors',
				'default'  => 'yes',
				'type'     => 'checkbox',
			),
			array(
				'title'    => __( 'Use custom config', 'pdf-invoicing-for-woocommerce' ),
				'desc'     => __( 'Enable', 'pdf-invoicing-for-woocommerce' ),
				'desc_tip' => __( 'Use custom TCPDF config file.', 'pdf-invoicing-for-woocommerce' ),
				'id'       => 'alg_wc_pdf_invoicing_use_custom_tcpdf_config',
				'default'  => 'yes',
				'type'     => 'checkbox',
				'show_if_checked' => 'option',
				'checkboxgroup'   => 'start',
			),
			array(
				'desc'     => __( 'TCPDF methods in HTML', 'pdf-invoicing-for-woocommerce' ),
				'desc_tip' => __( 'Allow calling TCPDF methods using HTML syntax.', 'pdf-invoicing-for-woocommerce' ),
				'id'       => 'alg_wc_pdf_invoicing_use_custom_tcpdf_config_calls_in_html',
				'default'  => 'yes',
				'type'     => 'checkbox',
				'show_if_checked' => 'yes',
				'checkboxgroup'   => 'end',
			),
			array(
				'title'    => __( 'Set default images directory', 'pdf-invoicing-for-woocommerce' ),
				'desc'     => __( 'Enable', 'pdf-invoicing-for-woocommerce' ),
				'desc_tip' => __( 'Set TCPDF default images directory to WP upload directory.', 'pdf-invoicing-for-woocommerce' ),
				'id'       => 'alg_wc_pdf_invoicing_tcpdf_path_images',
				'default'  => 'yes',
				'type'     => 'checkbox',
			),
			array(
				'title'    => __( 'Use Print.js', 'pdf-invoicing-for-woocommerce' ),
				'desc'     => __( 'Enable', 'pdf-invoicing-for-woocommerce' ),
				'desc_tip' => sprintf( __( 'Use %s library for printing PDF documents.', 'pdf-invoicing-for-woocommerce' ),
					'<a href="https://printjs.crabbly.com/" target="_blank">Print.js</a>' ),
				'id'       => 'alg_wc_pdf_invoicing_use_print_js',
				'default'  => 'yes',
				'type'     => 'checkbox',
			),
			array(
				'title'    => __( 'Use monospace font', 'pdf-invoicing-for-woocommerce' ),
				'desc'     => __( 'Enable', 'pdf-invoicing-for-woocommerce' ),
				'desc_tip' => sprintf( __( 'Use monospace font in "%s" and "%s" admin options fields.', 'pdf-invoicing-for-woocommerce' ),
						__( 'HTML style', 'pdf-invoicing-for-woocommerce' ),
						__( 'HTML content', 'pdf-invoicing-for-woocommerce' ),
						__( 'Header text', 'pdf-invoicing-for-woocommerce' ),
						__( 'Footer HTML content', 'pdf-invoicing-for-woocommerce' )
					),
				'id'       => 'alg_wc_pdf_invoicing_use_monospace_font',
				'default'  => 'no',
				'type'     => 'checkbox',
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_pdf_invoicing_advanced_options',
			),
		);

		return array_merge( $plugin_settings, $general_settings );
	}

}

endif;

return new Alg_WC_PDF_Invoicing_Settings_General();
