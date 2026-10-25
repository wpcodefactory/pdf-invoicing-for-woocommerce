<?php
/**
 * PDF Invoicing for WooCommerce - Settings
 *
 * @version 2.5.0
 * @since   1.0.0
 *
 * @author WPFactory
 *
 * @package WPFactory\WC_PDF_Invoicing\Settings
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_PDF_Invoicing_Settings' ) ) :

	/**
	 * Alg_WC_PDF_Invoicing_Settings class.
	 *
	 * @version 2.5.0
	 * @since   1.0.0
	 */
	class Alg_WC_PDF_Invoicing_Settings extends WC_Settings_Page {

		/**
		 * Constructor.
		 *
		 * @version 2.5.0
		 * @since   1.0.0
		 */
		public function __construct() {
			$this->id    = 'alg_wc_pdf_invoicing';
			$this->label = __( 'PDF Invoicing', 'pdf-invoicing-for-woocommerce' );

			parent::__construct();

			// Sanitization.
			add_filter( 'woocommerce_admin_settings_sanitize_option', array( $this, 'sanitize' ), PHP_INT_MAX, 3 );

			// Sections.
			require_once plugin_dir_path( __FILE__ ) . 'class-alg-wc-pdf-invoicing-settings-section.php';
			require_once plugin_dir_path( __FILE__ ) . 'class-alg-wc-pdf-invoicing-settings-doc-page-formats.php';
			require_once plugin_dir_path( __FILE__ ) . 'class-alg-wc-pdf-invoicing-settings-doc.php';
			require_once plugin_dir_path( __FILE__ ) . 'class-alg-wc-pdf-invoicing-settings-general.php';
			$total = get_option( 'alg_wc_pdf_invoicing_total_docs', 1 );
			for ( $doc_id = 0; $doc_id < $total; $doc_id++ ) {
				new Alg_WC_PDF_Invoicing_Settings_Doc( $doc_id );
			}
			do_action( 'alg_wc_pdf_invoicing_admin_doc_settings_loaded', $this );
			require_once plugin_dir_path( __FILE__ ) . 'class-alg-wc-pdf-invoicing-settings-counters.php';
		}

		/**
		 * Sanitize.
		 *
		 * @version 1.4.0
		 * @since   1.0.0
		 *
		 * @param mixed $value     The sanitized value.
		 * @param array $option    The option array.
		 * @param mixed $raw_value The raw value.
		 *
		 * @return mixed The sanitized value.
		 */
		public function sanitize( $value, $option, $raw_value ) {
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
		 * Get settings.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 */
		public function get_settings() {
			global $current_section;
			return array_merge(
				apply_filters( 'woocommerce_get_settings_' . $this->id . '_' . $current_section, array() ), // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
				array(
					array(
						'title' => __( 'Reset Settings', 'pdf-invoicing-for-woocommerce' ),
						'type'  => 'title',
						'id'    => $this->id . '_' . $current_section . '_reset_options',
					),
					array(
						'title'    => __( 'Reset section settings', 'pdf-invoicing-for-woocommerce' ),
						'desc'     => '<strong>' . __( 'Reset', 'pdf-invoicing-for-woocommerce' ) . '</strong>',
						'desc_tip' => __( 'Check the box and save changes to reset.', 'pdf-invoicing-for-woocommerce' ),
						'id'       => $this->id . '_' . $current_section . '_reset',
						'default'  => 'no',
						'type'     => 'checkbox',
					),
					array(
						'type' => 'sectionend',
						'id'   => $this->id . '_' . $current_section . '_reset_options',
					),
				)
			);
		}

		/**
		 * Maybe reset settings.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 */
		public function maybe_reset_settings() {
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
		 * Admin notices settings reset success.
		 *
		 * @version 2.3.0
		 * @since   1.0.0
		 */
		public function admin_notices_settings_reset_success() {
			echo '<div class="notice notice-warning is-dismissible"><p><strong>' .
				esc_html__( 'Your settings have been reset.', 'pdf-invoicing-for-woocommerce' ) .
			'</strong></p></div>';
		}

		/**
		 * Save.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @todo (fix) Notices (not showing now because of redirect).
		 */
		public function save() {
			parent::save();
			$this->maybe_reset_settings();
			wp_safe_redirect( add_query_arg( array() ) );
			exit;
		}
	}

endif;

return new Alg_WC_PDF_Invoicing_Settings();
