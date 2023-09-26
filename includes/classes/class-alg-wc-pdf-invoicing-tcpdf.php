<?php
/**
 * PDF Invoicing for WooCommerce - TCPDF Class
 *
 * @version 1.8.0
 * @since   1.1.0
 *
 * @author  Algoritmika Ltd
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_PDF_Invoicing_TCPDF' ) ) :

class Alg_WC_PDF_Invoicing_TCPDF extends TCPDF {

	/**
	 * Header.
	 *
	 * @version 1.6.0
	 * @since   1.1.0
	 *
	 * @see     https://github.com/tecnickcom/TCPDF/blob/6.3.2/tcpdf.php#L3415 (Header)
	 * @see     https://github.com/tecnickcom/TCPDF/blob/6.3.2/tcpdf.php#L6855 (Image)
	 * @see     https://github.com/tecnickcom/TCPDF/blob/6.3.2/tcpdf.php#L11377 (SetLineStyle)
	 * @see     https://tcpdf.org/examples/example_051/ (Background image)
	 * @see     https://github.com/tecnickcom/TCPDF/blob/6.3.2/tcpdf.php#L6855 (Image)
	 *
	 * @todo    (feature) [!] HTML in header
	 * @todo    (feature) 'C' image alignment
	 * @todo    (feature) 'C' text alignment
	 * @todo    (feature) "Line color (in content)": more params, e.g., width?
	 * @todo    (feature) add "Header text width" (in percent)?
	 * @todo    (dev) rethink `0.75` and `0.25`
	 * @todo    (dev) modify "Header image width": make it in percent?
	 * @todo    (dev) "Line color (in content)": move elsewhere?
	 */
	function Header() {

		if ( 'yes' === $this->alg_wc_pdf_invoicing_doc->get_doc_option( 'enable_header' ) ) {
			$header_img_palign  = $this->alg_wc_pdf_invoicing_doc->get_doc_option( 'header_img_palign' );
			$header_text_palign = $this->alg_wc_pdf_invoicing_doc->get_doc_option( 'header_text_palign' );
			$paligns            = $header_img_palign . $header_text_palign;
			if ( 'LL' === $paligns ) {
				parent::Header();
			} else {
				if ($this->header_xobjid === false) {
					// start a new XObject Template
					$this->header_xobjid = $this->startTemplate($this->w, $this->tMargin);
					$headerfont = $this->getHeaderFont();
					$headerdata = $this->getHeaderData();
					$this->y = $this->header_margin;
					// Algoritmika start
					switch ( $paligns ) {
						case 'RL':
						case 'RR':
							$this->x = ( $this->rtl ? $this->original_lMargin + $headerdata['logo_width'] : $this->w - $this->original_rMargin - $headerdata['logo_width'] );
							break;
						case 'LR':
						case 'LL': // never called here
							$this->x = ( $this->rtl ? $this->w - $this->original_rMargin : $this->original_lMargin );
							break;
					}
					// Algoritmika end
					if (($headerdata['logo']) AND ($headerdata['logo'] != K_BLANK_IMAGE)) {
						$imgtype = TCPDF_IMAGES::getImageFileType(K_PATH_IMAGES.$headerdata['logo']);
						if (($imgtype == 'eps') OR ($imgtype == 'ai')) {
							$this->ImageEps(K_PATH_IMAGES.$headerdata['logo'], '', '', $headerdata['logo_width']);
						} elseif ($imgtype == 'svg') {
							$this->ImageSVG(K_PATH_IMAGES.$headerdata['logo'], '', '', $headerdata['logo_width']);
						} else {
							$this->Image(K_PATH_IMAGES.$headerdata['logo'], '', '', $headerdata['logo_width']);
						}
						$imgy = $this->getImageRBY();
					} else {
						$imgy = $this->y;
					}
					$cell_height = $this->getCellHeight($headerfont[2] / $this->k);
					// set starting margin for text data cell
					// Algoritmika start
					switch ( $paligns ) {
						case 'RL':
							$header_x = ( $this->getRTL() ? $this->original_rMargin : $this->original_lMargin );
							$cw       = $this->w - $this->original_lMargin - $this->original_rMargin;
							break;
						case 'LR':
							$header_x = $this->w * 0.75 - ( $this->getRTL() ? $this->original_lMargin : $this->original_rMargin );
							$cw       = $this->w * 0.25;
							break;
						case 'RR':
							$header_x = $this->w * 0.75 - ( $this->getRTL() ? $this->original_lMargin : $this->original_rMargin ) - ($headerdata['logo_width'] * 1.1);
							$cw       = $this->w * 0.25;
							break;
						case 'LL': // never called here
							$header_x = ( $this->getRTL() ? $this->original_rMargin : $this->original_lMargin ) + ($headerdata['logo_width'] * 1.1);
							$cw       = $this->w - $this->original_lMargin - $this->original_rMargin - ($headerdata['logo_width'] * 1.1);
							break;
					}
					// Algoritmika end
					$this->SetTextColorArray($this->header_text_color);
					// header title
					$this->SetFont($headerfont[0], 'B', $headerfont[2] + 1);
					$this->SetX($header_x);
					$this->Cell($cw, $cell_height, $headerdata['title'], 0, 1, '', 0, '', 0);
					// header string
					$this->SetFont($headerfont[0], $headerfont[1], $headerfont[2]);
					$this->SetX($header_x);
					$this->MultiCell($cw, $cell_height, $headerdata['string'], 0, '', 0, 1, '', '', true, 0, false, true, 0, 'T', false);
					// print an ending header line
					$this->SetLineStyle(array('width' => 0.85 / $this->k, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => $headerdata['line_color']));
					$this->SetY((2.835 / $this->k) + max($imgy, $this->y));
					if ($this->rtl) {
						$this->SetX($this->original_rMargin);
					} else {
						$this->SetX($this->original_lMargin);
					}
					$this->Cell(($this->w - $this->original_lMargin - $this->original_rMargin), 0, '', 'T', 0, 'C');
					$this->endTemplate();
				}
				// print header template
				$x = 0;
				$dx = 0;
				if (!$this->header_xobj_autoreset AND $this->booklet AND (($this->page % 2) == 0)) {
					// adjust margins for booklet mode
					$dx = ($this->original_lMargin - $this->original_rMargin);
				}
				if ($this->rtl) {
					$x = $this->w + $dx;
				} else {
					$x = 0 + $dx;
				}
				$this->printTemplate($this->header_xobjid, $x, 0, 0, 0, '', '', false);
				if ($this->header_xobj_autoreset) {
					// reset header xobject template at each page
					$this->header_xobjid = false;
				}
			}
		}

		// Line color (in content)
		$this->SetLineStyle( array( 'width' => 0.85 / $this->k, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0,
			'color' => alg_wc_pdf_invoicing_hex_to_rgb( $this->alg_wc_pdf_invoicing_doc->get_doc_option( 'line_color' ) ) ) );

		// Background image
		if ( '' !== ( $page_background_img = $this->alg_wc_pdf_invoicing_doc->get_doc_option( 'page_background_img', true ) ) ) {
			$break_margin    = $this->getBreakMargin();
			$auto_page_break = $this->AutoPageBreak;
			$this->SetAutoPageBreak( false, 0 );
			$this->Image( K_PATH_IMAGES . $page_background_img, 0, 0, $this->w, $this->h, '', '', '', false, 300, '', false, false, 0 );
			$this->SetAutoPageBreak( $auto_page_break, $break_margin );
			$this->setPageMark();
		}

	}

	/**
	 * Footer.
	 *
	 * @see     https://github.com/tecnickcom/TCPDF/blob/6.3.2/tcpdf.php#L3492 (Footer)
	 * @see     https://github.com/tecnickcom/TCPDF/blob/6.3.2/tcpdf.php#L17157 (writeHTMLCell)
	 *
	 * @version 1.8.0
	 * @since   1.1.0
	 *
	 * @todo    (fix) [!] "Justify" doesn't work for the `writeHTMLCell()`
	 */
	function Footer() {

		// Footer
		if ( 'yes' === $this->alg_wc_pdf_invoicing_doc->get_doc_option( 'enable_footer' ) ) {
			if ( '' != ( $footer_text = $this->alg_wc_pdf_invoicing_doc->get_doc_option( 'footer_text', true ) ) ) {
				$this->SetTextColorArray( alg_wc_pdf_invoicing_hex_to_rgb( $this->alg_wc_pdf_invoicing_doc->get_doc_option( 'footer_text_color' ) ) );
				$footer_text = str_replace( array( '%page_num%', '%total_pages%' ), array( $this->getAliasNumPage(), $this->getAliasNbPages() ), $footer_text );
				$footer_text = $this->alg_wc_pdf_invoicing_doc->apply_content_filters( $footer_text );
				$style       = '<style>' . $this->alg_wc_pdf_invoicing_doc->get_doc_option( 'html_style', true ) . '</style>';
				$this->writeHTMLCell( 0, 0, $this->x, $this->y, $style . $footer_text, 'T', 0, false, true, $this->alg_wc_pdf_invoicing_doc->get_doc_option( 'footer_text_align' ) );
			}
		}

		// Foreground image
		if ( '' !== ( $page_foreground_img = $this->alg_wc_pdf_invoicing_doc->get_doc_option( 'page_foreground_img', true ) ) ) {
			$break_margin    = $this->getBreakMargin();
			$auto_page_break = $this->AutoPageBreak;
			$this->SetAutoPageBreak( false, 0 );
			$this->Image( K_PATH_IMAGES . $page_foreground_img, 0, 0, $this->w, $this->h, '', '', '', false, 300, '', false, false, 0 );
			$this->SetAutoPageBreak( $auto_page_break, $break_margin );
			$this->setPageMark();
		}

	}

}

endif;