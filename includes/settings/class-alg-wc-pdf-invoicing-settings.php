<?php
/**
 * PDF Invoicing for WooCommerce - Settings
 *
 * @version 2.1.0
 * @since   1.0.0
 *
 * @author  Algoritmika Ltd
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_PDF_Invoicing_Settings' ) ) :

class Alg_WC_PDF_Invoicing_Settings extends WC_Settings_Page {

	/**
	 * Constructor.
	 *
	 * @version 2.1.0
	 * @since   1.0.0
	 */
	function __construct() {

		$this->id    = 'alg_wc_pdf_invoicing';
		$this->label = __( 'PDF Invoicing', 'pdf-invoicing-for-woocommerce' );
		parent::__construct();

		// Sanitization
		add_filter( 'woocommerce_admin_settings_sanitize_option', array( $this, 'sanitize' ), PHP_INT_MAX, 3 );

		// Sections
		require_once( 'class-alg-wc-pdf-invoicing-settings-section.php' );
		require_once( 'class-alg-wc-pdf-invoicing-settings-doc-page-formats.php' );
		require_once( 'class-alg-wc-pdf-invoicing-settings-doc.php' );
		require_once( 'class-alg-wc-pdf-invoicing-settings-general.php' );
		new Alg_WC_PDF_Invoicing_Settings_Doc();
		do_action( 'alg_wc_pdf_invoicing_admin_doc_settings_loaded', $this );
		require_once( 'class-alg-wc-pdf-invoicing-settings-counters.php' );

	}

	/**
	 * sanitize.
	 *
	 * @version 1.4.0
	 * @since   1.0.0
	 */
	function sanitize( $value, $option, $raw_value ) {
		if ( isset( $option['alg_wc_pi_sanitize'] ) ) {
			switch ( $option['alg_wc_pi_sanitize'] ) {
				case 'textarea':
					return wp_kses_post( trim( $raw_value ) );
				default:
					if ( function_exists( $option['alg_wc_pi_sanitize'] ) ) {
						$func = $option['alg_wc_pi_sanitize'];
						return $func( $raw_value );
					}
			}
		}
		return $value;
	}

	/**
	 * get_settings.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function get_settings() {
		global $current_section;
		return array_merge( apply_filters( 'woocommerce_get_settings_' . $this->id . '_' . $current_section, array() ), array(
			array(
				'title'     => __( 'Reset Settings', 'pdf-invoicing-for-woocommerce' ),
				'type'      => 'title',
				'id'        => $this->id . '_' . $current_section . '_reset_options',
			),
			array(
				'title'     => __( 'Reset section settings', 'pdf-invoicing-for-woocommerce' ),
				'desc'      => '<strong>' . __( 'Reset', 'pdf-invoicing-for-woocommerce' ) . '</strong>',
				'desc_tip'  => __( 'Check the box and save changes to reset.', 'pdf-invoicing-for-woocommerce' ),
				'id'        => $this->id . '_' . $current_section . '_reset',
				'default'   => 'no',
				'type'      => 'checkbox',
			),
			array(
				'type'      => 'sectionend',
				'id'        => $this->id . '_' . $current_section . '_reset_options',
			),
		) );
	}

	/**
	 * maybe_reset_settings.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function maybe_reset_settings() {
		global $current_section;
		if ( 'yes' === get_option( $this->id . '_' . $current_section . '_reset', 'no' ) ) {
			foreach ( $this->get_settings() as $value ) {
				if ( isset( $value['id'] ) ) {
					$id = explode( '[', $value['id'] );
					delete_option( $id[0] );
				}
			}
			add_action( 'admin_notices', array( $this, 'admin_notices_settings_reset_success' ), PHP_INT_MAX );
		}
	}

	/**
	 * admin_notices_settings_reset_success.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function admin_notices_settings_reset_success() {
		echo '<div class="notice notice-success is-dismissible"><p><strong>' .
			__( 'Your settings have been reset.', 'pdf-invoicing-for-woocommerce' ) . '</strong></p></div>';
	}

	/**
	 * save.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 *
	 * @todo    (fix) notices (not showing now because of redirect)
	 */
	function save() {
		parent::save();
		$this->maybe_reset_settings();
		wp_safe_redirect( add_query_arg( array() ) );
		exit;
	}

}

endif;

return new Alg_WC_PDF_Invoicing_Settings();
