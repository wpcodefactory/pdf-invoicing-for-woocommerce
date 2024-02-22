<?php
/*
Plugin Name: PDF Invoicing for WooCommerce
Plugin URI: https://wpfactory.com/item/pdf-invoicing-for-woocommerce/
Description: Add PDF invoices to WooCommerce.
Version: 2.1.0
Author: WPFactory
Author URI: https://wpfactory.com
Text Domain: pdf-invoicing-for-woocommerce
Domain Path: /langs
WC tested up to: 8.6
*/

defined( 'ABSPATH' ) || exit;

if ( 'pdf-invoicing-for-woocommerce.php' === basename( __FILE__ ) ) {
	/**
	 * Check if Pro plugin version is activated.
	 *
	 * @version 2.0.0
	 * @since   1.3.0
	 */
	$plugin = 'pdf-invoicing-for-woocommerce-pro/pdf-invoicing-for-woocommerce-pro.php';
	if (
		in_array( $plugin, (array) get_option( 'active_plugins', array() ), true ) ||
		( is_multisite() && array_key_exists( $plugin, (array) get_site_option( 'active_sitewide_plugins', array() ) ) )
	) {
		defined( 'ALG_WC_PDF_INVOICING_FILE_FREE' ) || define( 'ALG_WC_PDF_INVOICING_FILE_FREE', __FILE__ );
		return;
	}
}

defined( 'ALG_WC_PDF_INVOICING_VERSION' ) || define( 'ALG_WC_PDF_INVOICING_VERSION', '2.1.0' );

defined( 'ALG_WC_PDF_INVOICING_FILE' ) || define( 'ALG_WC_PDF_INVOICING_FILE', __FILE__ );

require_once( 'includes/class-alg-wc-pdf-invoicing.php' );

if ( ! function_exists( 'alg_wc_pdf_invoicing' ) ) {
	/**
	 * Returns the main instance of Alg_WC_PDF_Invoicing to prevent the need to use globals.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function alg_wc_pdf_invoicing() {
		return Alg_WC_PDF_Invoicing::instance();
	}
}

add_action( 'plugins_loaded', 'alg_wc_pdf_invoicing' );
