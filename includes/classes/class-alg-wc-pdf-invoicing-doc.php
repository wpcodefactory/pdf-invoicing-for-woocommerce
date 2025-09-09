<?php
/**
 * PDF Invoicing for WooCommerce - Doc Class
 *
 * @version 2.3.0
 * @since   1.0.0
 *
 * @author  WPFactory
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_PDF_Invoicing_Doc' ) ) :

class Alg_WC_PDF_Invoicing_Doc {

	/**
	 * order_id.
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 */
	public $order_id;

	/**
	 * order.
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 */
	public $order;

	/**
	 * doc_id.
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 */
	public $doc_id;

	/**
	 * data.
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 */
	public $data;

	/**
	 * Constructor.
	 *
	 * @version 2.0.0
	 * @since   1.0.0
	 */
	function __construct( $order_id, $doc_id ) {
		$this->order_id = $order_id;
		$this->order    = wc_get_order( $order_id );
		$this->doc_id   = $doc_id;
		$data           = $this->order->get_meta( '_alg_wc_pdf_invoicing_data' );
		$this->data     = ( ! empty( $data[ $this->doc_id ] ) ? $data[ $this->doc_id ] : false );
	}

	/**
	 * is_created.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function is_created() {
		return ( false !== $this->data );
	}

	/**
	 * create.
	 *
	 * @version 2.2.2
	 * @since   1.0.0
	 *
	 * @todo    (dev) also save prefix and other data
	 * @todo    (dev) also save as a separate order meta, e.g., `_alg_wc_pdf_invoicing_number`, `_alg_wc_pdf_invoicing_date`
	 */
	function create( $data ) {

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
	 * remove.
	 *
	 * @version 2.2.2
	 * @since   1.0.0
	 *
	 * @todo    (dev) better function name, e.g., `delete`, `destroy`, `trash`
	 */
	function remove() {

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
	 * get_counter.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function get_counter() {
		return $this->data['id'];
	}

	/**
	 * get_number.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function get_number() {
		alg_wc_pdf_invoicing()->core->shortcodes->set_prop( 'doc_nr', false );
		$return = $this->get_doc_option( 'number_format', true );
		alg_wc_pdf_invoicing()->core->shortcodes->set_prop( 'doc_nr', true );
		return $return;
	}

	/**
	 * get_formatted_date.
	 *
	 * @version 1.5.0
	 * @since   1.0.0
	 *
	 * @todo    (dev) `$add_days`: rethink naming
	 */
	function get_formatted_date( $format = 'Y-m-d', $add_days = 0 ) {
		return date_i18n( $format, ( $this->data['date'] + $add_days * DAY_IN_SECONDS ) );
	}

	/**
	 * get_author_full_name.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function get_author_full_name() {
		$doc_author = $this->data['author'];
		$doc_author = get_userdata( $doc_author );
		return ( $doc_author ? $doc_author->first_name . ' ' . $doc_author->last_name : '' );
	}

	/**
	 * get_doc_option.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function get_doc_option( $option_id, $do_shortcode = false ) {
		return alg_wc_pdf_invoicing()->core->get_doc_option( $this->doc_id, $option_id, $do_shortcode, $this );
	}

	/**
	 * apply_content_filters.
	 *
	 * @version 1.4.0
	 * @since   1.4.0
	 */
	function apply_content_filters( $content ) {
		$content = ( 'yes' === $this->get_doc_option( 'html_content_force_balance_tags' ) ? force_balance_tags( $content ) : $content );
		$content = ( 'yes' === $this->get_doc_option( 'html_content_wpautop' )            ? wpautop( $content )            : $content );
		return $content;
	}

	/**
	 * get_html.
	 *
	 * @version 1.4.0
	 * @since   1.0.0
	 *
	 * @todo    (dev) [!] `curl_init()` for `img`: https://github.com/tecnickcom/TCPDF/blob/6.3.2/include/tcpdf_static.php#L1830
	 */
	function get_html() {
		return '<meta charset="UTF-8">' .
			'<style>' . $this->get_doc_option( 'html_style', true ) . '</style>' .
			apply_filters( 'alg_wc_pdf_invoicing_get_html_content', $this->apply_content_filters( $this->get_doc_option( 'html_content', true ) ), $this );
	}

	/**
	 * get_pdf.
	 *
	 * @version 2.3.0
	 * @since   1.0.0
	 *
	 * @see     https://tcpdf.org/
	 * @see     https://tcpdf.org/examples/example_053/ (IncludeJS)
	 * @see     https://github.com/tecnickcom/TCPDF/blob/6.3.2/tcpdf.php#L1855 (Constructor)
	 * @see     https://github.com/tecnickcom/TCPDF/blob/6.3.2/tcpdf.php#L3280 (SetHeaderData)
	 * @see     https://github.com/tecnickcom/TCPDF/blob/6.3.2/tcpdf.php#L2581 (SetMargins)
	 * @see     https://github.com/tecnickcom/TCPDF/blob/6.3.2/tcpdf.php#L4450 (SetFont)
	 * @see     https://github.com/tecnickcom/TCPDF/blob/6.3.2/tcpdf.php#L7581 (Output)
	 *
	 * @todo    (feature) [!] "Header height" (`PDF_MARGIN_HEADER`?)
	 * @todo    (feature) [!] "Margin: Bottom" (`PDF_MARGIN_BOTTOM`?)
	 * @todo    (fix) `<hr>` + RTL
	 * @todo    (dev) `IncludeJS`: `$_GET['alg-wc-pdf-invoicing-print']`?
	 * @todo    (dev) `IncludeJS`: `... && 'no' === get_option( 'alg_wc_pdf_invoicing_use_print_js', 'yes' )`?
	 * @todo    (feature) more customizable options
	 * @todo    (feature) "custom size" page format
	 * @todo    (feature) custom fonts
	 * @todo    (dev) `$pdf->setFontSubsetting( true );` (before `$pdf->SetFont()`)
	 */
	function get_pdf( $dest = 'I', $path = '' ) {

		// TCPDF class
		Alg_WC_PDF_Invoicing::load_tcpdf_lib();

		// Child TCPDF class
		require_once plugin_dir_path( __FILE__ ) . 'class-alg-wc-pdf-invoicing-tcpdf.php';

		// Suppress errors
		$do_suppress_errors = ( 'yes' === get_option( 'alg_wc_pdf_invoicing_suppress_errors', 'yes' ) );
		if ( $do_suppress_errors ) {
			if ( 0 == ( $error_level = error_reporting() ) ) { // phpcs:ignore WordPress.PHP.DevelopmentFunctions.prevent_path_disclosure_error_reporting
				$do_suppress_errors = false;
			} else {
				error_reporting( 0 ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.prevent_path_disclosure_error_reporting
			}
		}

		// Page format
		$page_format = $this->get_doc_option( 'page_format' );
		if ( 'CUSTOM' === $page_format ) {
			$page_format = array( $this->get_doc_option( 'page_format_custom_width' ), $this->get_doc_option( 'page_format_custom_height' ) );
			if ( in_array( 0, $page_format ) ) {
				$page_format = 'A4';
			}
		}

		// Construct PDF object
		$pdf = new Alg_WC_PDF_Invoicing_TCPDF(
			$this->get_doc_option( 'page_orientation' ),
			'mm',
			$page_format,
			true,
			'UTF-8',
			false
		);
		alg_wc_pdf_invoicing()->core->pdf = $pdf;
		$pdf->alg_wc_pdf_invoicing_doc = $this;

		// PDF props
		$pdf->SetCreator( PDF_CREATOR);
		$pdf->SetAuthor( 'PDF Invoicing for WooCommerce' );
		$pdf->SetTitle( $this->get_number() );
		$pdf->SetHeaderData(
			$this->get_doc_option( 'header_img', true ), $this->get_doc_option( 'header_img_width' ),
			$this->get_doc_option( 'header_title', true ), $this->get_doc_option( 'header_text', true ),
			alg_wc_pdf_invoicing_hex_to_rgb( $this->get_doc_option( 'header_text_color' ) ), alg_wc_pdf_invoicing_hex_to_rgb( $this->get_doc_option( 'header_line_color' ) ) );
		$pdf->setHeaderFont( array( $this->get_doc_option( 'header_font_family' ), '', $this->get_doc_option( 'header_font_size' ) ) );
		$pdf->setFooterFont( array( $this->get_doc_option( 'footer_font_family' ), '', $this->get_doc_option( 'footer_font_size' ) ) );
		$pdf->SetDefaultMonospacedFont( PDF_FONT_MONOSPACED );
		$pdf->SetMargins( $this->get_doc_option( 'margin_left' ), $this->get_doc_option( 'margin_top' ), $this->get_doc_option( 'margin_right' ) );
		$pdf->SetHeaderMargin( PDF_MARGIN_HEADER );
		$pdf->SetFooterMargin( $this->get_doc_option( 'footer_height' ) );
		$pdf->SetAutoPageBreak( true, PDF_MARGIN_BOTTOM );
		$pdf->setImageScale( PDF_IMAGE_SCALE_RATIO );
		$pdf->SetFont( $this->get_doc_option( 'font_family' ), '', $this->get_doc_option( 'font_size' ), '', true );
		$pdf->setRTL( 'yes' === $this->get_doc_option( 'rtl' ) );

		// Content
		$pdf->AddPage();
		$pdf->writeHTML( $this->get_html(), true, false, true, false, '' );

		// JS print
		if ( ! empty( $_GET['alg-wc-pdf-invoicing-print'] ) ) {
			$pdf->IncludeJS( 'print(true);' );
		}

		// Output
		$pdf->Output( $path . $this->get_number() . '.pdf', $dest );

		// Suppress errors rollback
		if ( $do_suppress_errors ) {
			error_reporting( $error_level ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.prevent_path_disclosure_error_reporting
		}

	}
}

endif;
