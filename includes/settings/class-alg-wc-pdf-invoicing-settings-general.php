<?php
/**
 * PDF Invoicing for WooCommerce - General Section Settings
 *
 * @version 2.3.0
 * @since   1.0.0
 *
 * @author  WPFactory
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
	 * @version 2.3.0
	 * @since   1.0.0
	 *
	 * @todo    (dev) `alg_wc_pdf_invoicing_tcpdf_early_load`: default to `yes`?
	 * @todo    (dev) `alg_wc_pdf_invoicing_shortcode_prefix`: better default value, e.g., `alg_wc_pdf_` or `alg_wc_pi_`?
	 * @todo    (desc) `alg_wc_pdf_invoicing_use_print_js`
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
			),
			array(
				'desc'     => __( 'TCPDF methods in HTML', 'pdf-invoicing-for-woocommerce' ),
				'desc_tip' => __( 'Allow calling TCPDF methods using HTML syntax.', 'pdf-invoicing-for-woocommerce' ) . '<br>' .
					__( '"Use custom TCPDF config file" option must be enabled.', 'pdf-invoicing-for-woocommerce' ),
				'id'       => 'alg_wc_pdf_invoicing_use_custom_tcpdf_config_calls_in_html',
				'default'  => 'yes',
				'type'     => 'checkbox',
			),
			array(
				'desc'     => (
					sprintf(
						/* Translators: %s: Example. */
						__( 'Allowed TCPDF methods, e.g.: %s', 'pdf-invoicing-for-woocommerce' ),
						'<code>|write1DBarcode|write2DBarcode|</code>'
					) .
					'<br>' .
					__( '"Use custom TCPDF config file" and "TCPDF methods in HTML" options must be enabled.', 'pdf-invoicing-for-woocommerce' )
				),
				'desc_tip' => __( 'The list of TCPDF methods that are allowed to be called using HTML syntax.', 'pdf-invoicing-for-woocommerce' ) . ' ' .
					__( 'Each method name must be surrounded with | (pipe) characters.', 'pdf-invoicing-for-woocommerce' ),
				'id'       => 'alg_wc_pdf_invoicing_use_custom_tcpdf_config_allowed_tags',
				'default'  => '',
				'type'     => 'text',
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
				'title'    => __( 'Early TCPDF load', 'pdf-invoicing-for-woocommerce' ),
				'desc'     => __( 'Enable', 'pdf-invoicing-for-woocommerce' ),
				'desc_tip' => __( 'Load TCPDF library as early as possible.', 'pdf-invoicing-for-woocommerce' ),
				'id'       => 'alg_wc_pdf_invoicing_tcpdf_early_load',
				'default'  => 'no',
				'type'     => 'checkbox',
			),
			array(
				'title'    => __( 'Use Print.js', 'pdf-invoicing-for-woocommerce' ),
				'desc'     => __( 'Enable', 'pdf-invoicing-for-woocommerce' ),
				'desc_tip' => sprintf(
					/* Translators: %s: Site link. */
					__( 'Use %s library for printing PDF documents.', 'pdf-invoicing-for-woocommerce' ),
					'<a href="https://printjs.crabbly.com/" target="_blank">Print.js</a>'
				),
				'id'       => 'alg_wc_pdf_invoicing_use_print_js',
				'default'  => 'yes',
				'type'     => 'checkbox',
			),
			array(
				'title'    => __( 'Use monospace font', 'pdf-invoicing-for-woocommerce' ),
				'desc'     => __( 'Enable', 'pdf-invoicing-for-woocommerce' ),
				'desc_tip' => sprintf(
					/* Translators: %1$s: Option name, %2$s: Option name, %3$s: Option name, %4$s: Option name. */
					__( 'Use monospace font in "%1$s", "%2$s", "%3$s" and "%4$s" admin options fields.', 'pdf-invoicing-for-woocommerce' ),
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
				'title'    => __( 'View PDFs in a new tab', 'pdf-invoicing-for-woocommerce' ),
				'desc'     => __( 'Enable', 'pdf-invoicing-for-woocommerce' ),
				'desc_tip' => __( 'For the "Bulk actions > View PDFs" option.', 'pdf-invoicing-for-woocommerce' ),
				'id'       => 'alg_wc_pdf_invoicing_view_pdfs_new_tab',
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
