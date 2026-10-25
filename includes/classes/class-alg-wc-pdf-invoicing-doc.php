<?php
/**
 * PDF Invoicing for WooCommerce - Doc Class
 *
 * @version 2.5.0
 * @since   1.0.0
 *
 * @author WPFactory
 *
 * @package WPFactory\WC_PDF_Invoicing
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_PDF_Invoicing_Doc' ) ) :

	/**
	 * Alg_WC_PDF_Invoicing_Doc class.
	 *
	 * @version 2.5.0
	 * @since   1.0.0
	 */
	class Alg_WC_PDF_Invoicing_Doc {

		/**
		 * Order ID.
		 *
		 * @version 2.0.0
		 * @since   2.0.0
		 *
		 * @var int
		 */
		public $order_id;

		/**
		 * Order.
		 *
		 * @version 2.0.0
		 * @since   2.0.0
		 *
		 * @var WC_Order
		 */
		public $order;

		/**
		 * Document ID.
		 *
		 * @version 2.0.0
		 * @since   2.0.0
		 *
		 * @var int
		 */
		public $doc_id;

		/**
		 * Data.
		 *
		 * @version 2.0.0
		 * @since   2.0.0
		 *
		 * @var array|false
		 */
		public $data;

		/**
		 * Constructor.
		 *
		 * @version 2.0.0
		 * @since   1.0.0
		 *
		 * @param int $order_id Order ID.
		 * @param int $doc_id   Document ID.
		 */
		public function __construct( $order_id, $doc_id ) {
			$this->order_id = $order_id;
			$this->order    = wc_get_order( $order_id );
			$this->doc_id   = $doc_id;
			$data           = $this->order->get_meta( '_alg_wc_pdf_invoicing_data' );
			$this->data     = ( ! empty( $data[ $this->doc_id ] ) ? $data[ $this->doc_id ] : false );
		}

		/**
		 * Is created.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @return bool
		 */
		public function is_created() {
			return ( false !== $this->data );
		}

		/**
		 * Create.
		 *
		 * @version 2.2.2
		 * @since   1.0.0
		 *
		 * @param array $data Data.
		 *
		 * @return void
		 *
		 * @todo (dev) Also save prefix and other data.
		 * @todo (dev) Also save as a separate order meta, e.g., `_alg_wc_pdf_invoicing_number`, `_alg_wc_pdf_invoicing_date`.
		 */
		public function create( $data ) {
			$data_all_docs = $this->order->get_meta( '_alg_wc_pdf_invoicing_data' );
			if ( empty( $data_all_docs ) ) {
				$data_all_docs = array();
			}
			$data_all_docs[ $this->doc_id ] = $data;
			$this->order->update_meta_data( '_alg_wc_pdf_invoicing_data', $data_all_docs );
			$this->order->save();
			$this->data = $data;

			if ( apply_filters( 'alg_wc_pdf_invoicing_add_order_notes', true ) ) {
				$this->order->add_order_note(
					sprintf(
						/* Translators: %1$s: Document title, %2$s: Document number. */
						__( '%1$s #%2$s created.', 'pdf-invoicing-for-woocommerce' ),
						$this->get_doc_option( 'admin_title' ),
						$this->get_number()
					)
				);
			}

			do_action( 'alg_wc_pdf_invoicing_doc_created', $this->order_id, $this );
		}

		/**
		 * Remove.
		 *
		 * @version 2.2.2
		 * @since   1.0.0
		 *
		 * @todo (dev) Better function name, e.g., `delete`, `destroy`, `trash`.
		 */
		public function remove() {
			if ( apply_filters( 'alg_wc_pdf_invoicing_add_order_notes', true ) ) {
				$this->order->add_order_note(
					sprintf(
						/* Translators: %1$s: Document title, %2$s: Document number. */
						__( '%1$s #%2$s deleted.', 'pdf-invoicing-for-woocommerce' ),
						$this->get_doc_option( 'admin_title' ),
						$this->get_number()
					)
				);
			}

			$data_all_docs = $this->order->get_meta( '_alg_wc_pdf_invoicing_data' );
			if ( empty( $data_all_docs ) ) {
				$data_all_docs = array();
			}
			$data_all_docs[ $this->doc_id ] = false;
			$this->order->update_meta_data( '_alg_wc_pdf_invoicing_data', $data_all_docs );
			$this->order->save();
			$this->data = false;

			do_action( 'alg_wc_pdf_invoicing_doc_removed', $this->order_id, $this );
		}

		/**
		 * Get counter.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 */
		public function get_counter() {
			return $this->data['id'];
		}

		/**
		 * Get number.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 */
		public function get_number() {
			alg_wc_pdf_invoicing()->core->shortcodes->set_prop( 'doc_nr', false );
			$return = $this->get_doc_option( 'number_format', true );
			alg_wc_pdf_invoicing()->core->shortcodes->set_prop( 'doc_nr', true );
			return $return;
		}

		/**
		 * Get formatted date.
		 *
		 * @version 1.5.0
		 * @since   1.0.0
		 *
		 * @param string $format   Date format.
		 * @param int    $add_days Number of days to add to the date. Default 0.
		 *
		 * @return string Formatted date.
		 *
		 * @todo (dev) `$add_days`: Rethink naming.
		 */
		public function get_formatted_date( $format = 'Y-m-d', $add_days = 0 ) {
			return date_i18n( $format, ( $this->data['date'] + $add_days * DAY_IN_SECONDS ) );
		}

		/**
		 * Get author full name.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 */
		public function get_author_full_name() {
			$doc_author = $this->data['author'];
			$doc_author = get_userdata( $doc_author );
			return ( $doc_author ? $doc_author->first_name . ' ' . $doc_author->last_name : '' );
		}

		/**
		 * Get document option.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @param string $option_id    Option ID.
		 * @param bool   $do_shortcode Whether to apply shortcodes.
		 *
		 * @return mixed Document option.
		 */
		public function get_doc_option( $option_id, $do_shortcode = false ) {
			return alg_wc_pdf_invoicing()->core->get_doc_option( $this->doc_id, $option_id, $do_shortcode, $this );
		}

		/**
		 * Apply content filters.
		 *
		 * @version 1.4.0
		 * @since   1.4.0
		 *
		 * @param string $content Content to apply filters to.
		 *
		 * @return string Filtered content.
		 */
		public function apply_content_filters( $content ) {
			$content = (
				'yes' === $this->get_doc_option( 'html_content_force_balance_tags' ) ?
				force_balance_tags( $content ) :
				$content
			);
			$content = (
				'yes' === $this->get_doc_option( 'html_content_wpautop' ) ?
				wpautop( $content ) :
				$content
			);
			return $content;
		}

		/**
		 * Get HTML.
		 *
		 * @version 1.4.0
		 * @since   1.0.0
		 *
		 * @todo (dev) [!] `curl_init()` for `img`: https://github.com/tecnickcom/TCPDF/blob/6.3.2/include/tcpdf_static.php#L1830
		 */
		public function get_html() {
			return '<meta charset="UTF-8">' .
				'<style>' . $this->get_doc_option( 'html_style', true ) . '</style>' .
				apply_filters(
					'alg_wc_pdf_invoicing_get_html_content',
					$this->apply_content_filters( $this->get_doc_option( 'html_content', true ) ),
					$this
				);
		}

		/**
		 * Get PDF.
		 *
		 * @version 2.5.0
		 * @since   1.0.0
		 *
		 * @param string $dest Destination.
		 * @param string $path Path.
		 *
		 * @return void
		 *
		 * @see https://tcpdf.org/
		 * @see https://tcpdf.org/examples/example_053/ (IncludeJS)
		 * @see https://github.com/tecnickcom/TCPDF/blob/6.3.2/tcpdf.php#L1855 (Constructor)
		 * @see https://github.com/tecnickcom/TCPDF/blob/6.3.2/tcpdf.php#L3280 (SetHeaderData)
		 * @see https://github.com/tecnickcom/TCPDF/blob/6.3.2/tcpdf.php#L2581 (SetMargins)
		 * @see https://github.com/tecnickcom/TCPDF/blob/6.3.2/tcpdf.php#L4450 (SetFont)
		 * @see https://github.com/tecnickcom/TCPDF/blob/6.3.2/tcpdf.php#L7581 (Output)
		 *
		 * @todo (feature) [!] "Header height" (`PDF_MARGIN_HEADER`?).
		 * @todo (feature) [!] "Margin: Bottom" (`PDF_MARGIN_BOTTOM`?).
		 * @todo (fix) `<hr>` + RTL.
		 * @todo (dev) `IncludeJS`: `$_GET['alg-wc-pdf-invoicing-print']`?
		 * @todo (dev) `IncludeJS`: `... && 'no' === get_option( 'alg_wc_pdf_invoicing_use_print_js', 'yes' )`?
		 * @todo (feature) More customizable options.
		 * @todo (feature) "Custom size" page format.
		 * @todo (feature) Custom fonts.
		 * @todo (dev) `$pdf->setFontSubsetting( true );` (before `$pdf->SetFont()`).
		 */
		public function get_pdf( $dest = 'I', $path = '' ) {
			// TCPDF class.
			Alg_WC_PDF_Invoicing::load_tcpdf_lib();

			// Child TCPDF class.
			require_once plugin_dir_path( __FILE__ ) . 'class-alg-wc-pdf-invoicing-tcpdf.php';

			// Suppress errors.
			$do_suppress_errors = ( 'yes' === get_option( 'alg_wc_pdf_invoicing_suppress_errors', 'yes' ) );
			if ( $do_suppress_errors ) {
				$error_level = error_reporting(); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.prevent_path_disclosure_error_reporting, PluginCheck.CodeAnalysis.PHPErrorReporting.DirectErrorReportingCall, WordPress.PHP.DiscouragedPHPFunctions.runtime_configuration_error_reporting
				if ( 0 === $error_level ) {
					$do_suppress_errors = false;
				} else {
					error_reporting( 0 ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.prevent_path_disclosure_error_reporting, PluginCheck.CodeAnalysis.PHPErrorReporting.DirectErrorReportingCall, WordPress.PHP.DiscouragedPHPFunctions.runtime_configuration_error_reporting
				}
			}

			// Page format.
			$page_format = $this->get_doc_option( 'page_format' );
			if ( 'CUSTOM' === $page_format ) {
				$page_format = array(
					$this->get_doc_option( 'page_format_custom_width' ),
					$this->get_doc_option( 'page_format_custom_height' ),
				);
				if ( in_array( 0, array_map( 'intval', $page_format ), true ) ) {
					$page_format = 'A4';
				}
			}

			// Construct PDF object.
			$pdf = new Alg_WC_PDF_Invoicing_TCPDF(
				$this->get_doc_option( 'page_orientation' ),
				'mm',
				$page_format,
				true,
				'UTF-8',
				false
			);

			alg_wc_pdf_invoicing()->core->pdf = $pdf;
			$pdf->alg_wc_pdf_invoicing_doc    = $this;

			// PDF props.
			$pdf->SetCreator( PDF_CREATOR );
			$pdf->SetAuthor( 'PDF Invoicing for WooCommerce' );
			$pdf->SetTitle( $this->get_number() );
			$pdf->SetHeaderData(
				$this->get_doc_option( 'header_img', true ),
				$this->get_doc_option( 'header_img_width' ),
				$this->get_doc_option( 'header_title', true ),
				$this->get_doc_option( 'header_text', true ),
				alg_wc_pdf_invoicing_hex_to_rgb( $this->get_doc_option( 'header_text_color' ) ),
				alg_wc_pdf_invoicing_hex_to_rgb( $this->get_doc_option( 'header_line_color' ) )
			);
			$pdf->setHeaderFont(
				array(
					$this->get_doc_option( 'header_font_family' ),
					'',
					$this->get_doc_option( 'header_font_size' ),
				)
			);
			$pdf->setFooterFont(
				array(
					$this->get_doc_option( 'footer_font_family' ),
					'',
					$this->get_doc_option( 'footer_font_size' ),
				)
			);
			$pdf->SetDefaultMonospacedFont( PDF_FONT_MONOSPACED );
			$pdf->SetMargins(
				$this->get_doc_option( 'margin_left' ),
				$this->get_doc_option( 'margin_top' ),
				$this->get_doc_option( 'margin_right' )
			);
			$pdf->SetHeaderMargin( PDF_MARGIN_HEADER );
			$pdf->SetFooterMargin( $this->get_doc_option( 'footer_height' ) );
			$pdf->SetAutoPageBreak(
				true,
				PDF_MARGIN_BOTTOM
			);
			$pdf->setImageScale( PDF_IMAGE_SCALE_RATIO );
			$pdf->SetFont(
				$this->get_doc_option( 'font_family' ),
				'',
				$this->get_doc_option( 'font_size' ),
				'',
				true
			);
			$pdf->setRTL( 'yes' === $this->get_doc_option( 'rtl' ) );

			// Content.
			$pdf->AddPage();
			$pdf->writeHTML(
				$this->get_html(),
				true,
				false,
				true,
				false,
				''
			);

			// JS print.
			if ( ! empty( $_GET['alg-wc-pdf-invoicing-print'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				$pdf->IncludeJS( 'print(true);' );
			}

			// Output.
			$pdf->Output(
				$path . $this->get_number() . '.pdf',
				$dest
			);

			// Suppress errors rollback.
			if ( $do_suppress_errors ) {
				error_reporting( $error_level ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.prevent_path_disclosure_error_reporting, PluginCheck.CodeAnalysis.PHPErrorReporting.DirectErrorReportingCall, WordPress.PHP.DiscouragedPHPFunctions.runtime_configuration_error_reporting
			}
		}
	}

endif;
