<?php
/**
 * PDF Invoicing for WooCommerce - Section Settings
 *
 * @version 1.5.0
 * @since   1.0.0
 *
 * @author  Algoritmika Ltd
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_PDF_Invoicing_Settings_Section' ) ) :

class Alg_WC_PDF_Invoicing_Settings_Section {

	/**
	 * Constructor.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function __construct() {
		add_filter( 'woocommerce_get_sections_alg_wc_pdf_invoicing',              array( $this, 'settings_section' ) );
		add_filter( 'woocommerce_get_settings_alg_wc_pdf_invoicing_' . $this->id, array( $this, 'get_settings' ), PHP_INT_MAX );
	}

	/**
	 * settings_section.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function settings_section( $sections ) {
		$sections[ $this->id ] = $this->desc;
		return $sections;
	}

	/**
	 * get_uploads_dir.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function get_uploads_dir() {
		$uploads_dir = wp_upload_dir();
		return $uploads_dir['basedir'] . '/';
	}

	/**
	 * get_emails.
	 *
	 * @version 1.5.0
	 * @since   1.0.0
	 *
	 * @todo    [now] (dev) sort array?
	 */
	function get_emails() {
		$emails    = array();
		$wc_emails = WC_Emails::instance();
		foreach ( $wc_emails->get_emails() as $email_id => $email ) {
			$emails[ $email->id ] = $email->get_title();
		}
		$emails['customer_partially_refunded_order'] = __( 'Partially refunded order', 'pdf-invoicing-for-woocommerce' );
		return $emails;
	}

	/**
	 * get_hooks.
	 *
	 * @version 1.5.0
	 * @since   1.0.0
	 *
	 * @todo    [now] (dev) sort array?
	 * @todo    [now] (feature) custom hooks
	 */
	function get_hooks() {
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
			$hooks[ 'woocommerce_order_status_' . $status_slug ] = sprintf( __( 'On order status %s', 'pdf-invoicing-for-woocommerce' ), $status_name );
		}
		return $hooks;
	}

	/**
	 * get_fonts.
	 *
	 * @version 1.4.0
	 * @since   1.4.0
	 */
	function get_fonts() {
		return array(
			'dejavusans' => 'DejaVu Sans (Unicode)',
			'times'      => 'Times New Roman',
			'helvetica'  => 'Helvetica',
			'courier'    => 'Courier',
		);
	}

	/**
	 * get_fonts_desc.
	 *
	 * @version 1.4.0
	 * @since   1.4.0
	 *
	 * @todo    [now] (desc) improve
	 */
	function get_fonts_desc() {
		return sprintf( __( 'If you are having issues displaying your language specific letters, select "%s" font.', 'pdf-invoicing-for-woocommerce' ),
			'DejaVu Sans (Unicode)' );
	}

}

endif;
