<?php
/**
 * PDF Invoicing for WooCommerce - Document Settings
 *
 * @version 2.4.4
 * @since   1.0.0
 *
 * @author  WPFactory
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_PDF_Invoicing_Settings_Doc' ) ) :

class Alg_WC_PDF_Invoicing_Settings_Doc extends Alg_WC_PDF_Invoicing_Settings_Section {

	/**
	 * doc_id.
	 *
	 * @version 2.1.0
	 * @since   2.1.0
	 */
	public $doc_id;

	/**
	 * Constructor.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 *
	 * @todo    (feature) option to copy all settings from another doc || export/import (doc) settings options
	 * @todo    (feature) `margin_bottom` (i.e., "page break" in TCPDF)
	 */
	function __construct( $doc_id = 0 ) {
		$this->doc_id = $doc_id;
		$this->id     = 'doc_' . $this->doc_id;
		$this->desc   = sprintf(
			/* Translators: %s: Document title. */
			__( 'Doc: %s', 'pdf-invoicing-for-woocommerce' ),
			alg_wc_pdf_invoicing()->core->get_doc_option( $this->doc_id, 'admin_title' )
		);
		parent::__construct();
	}

	/**
	 * get_settings.
	 *
	 * @version 2.4.4
	 * @since   1.0.0
	 *
	 * @todo    (desc) [!] `order_bulk_actions`: better desc?
	 * @todo    (feature) [!] customizable `unit`
	 * @todo    (dev) [!] `show_if_checked`
	 * @todo    (desc) `line_color`
	 * @todo    (dev) predefined content variants
	 * @todo    (dev) better descriptions
	 * @todo    (dev) HTML content: wp_editor?
	 * @todo    (dev) "raw" (at least optional): HTML style, HTML content etc.
	 */
	function get_settings() {
		return array(
			array(
				'title'    => sprintf(
					/* Translators: %s: Document title. */
					__( '%s Options', 'pdf-invoicing-for-woocommerce' ),
					$this->desc
				),
				'type'     => 'title',
				'id'       => "alg_wc_pdf_invoicing_doc_{$this->doc_id}_general_options",
			),
			array(
				'title'    => __( 'Enable/Disable', 'pdf-invoicing-for-woocommerce' ),
				'desc'     => __( 'Enable', 'pdf-invoicing-for-woocommerce' ),
				'id'       => "alg_wc_pdf_invoicing_doc_{$this->doc_id}[enable]",
				'default'  => alg_wc_pdf_invoicing_get_default( 'enable' ),
				'type'     => 'checkbox',
			),
			array(
				'title'    => __( 'Title', 'pdf-invoicing-for-woocommerce' ),
				'desc_tip' => __( 'Used in admin, and in "My account > Orders".', 'pdf-invoicing-for-woocommerce' ),
				'id'       => "alg_wc_pdf_invoicing_doc_{$this->doc_id}[admin_title]", // mislabeled, should be `title`
				'default'  => alg_wc_pdf_invoicing_get_default( 'admin_title', $this->doc_id ),
				'type'     => 'text',
				'css'      => 'width:100%;',
			),
			array(
				'title'    => __( 'Create', 'pdf-invoicing-for-woocommerce' ),
				'id'       => "alg_wc_pdf_invoicing_doc_{$this->doc_id}[hooks]",
				'default'  => alg_wc_pdf_invoicing_get_default( 'hooks' ),
				'type'     => 'multiselect',
				'class'    => 'chosen_select',
				'options'  => $this->get_hooks(),
			),
			array(
				'title'    => __( 'Emails', 'pdf-invoicing-for-woocommerce' ),
				'desc_tip' => __( 'Email attachments.', 'pdf-invoicing-for-woocommerce' ),
				'id'       => "alg_wc_pdf_invoicing_doc_{$this->doc_id}[emails]",
				'default'  => alg_wc_pdf_invoicing_get_default( 'emails' ),
				'type'     => 'multiselect',
				'class'    => 'chosen_select',
				'options'  => $this->get_emails(),
			),
			array(
				'title'    => __( 'Number format', 'pdf-invoicing-for-woocommerce' ),
				'desc_tip' => __( 'You can use shortcodes here.', 'pdf-invoicing-for-woocommerce' ) . ' ' .
					__( 'Please note that you can\'t use `doc_nr` prop here.', 'pdf-invoicing-for-woocommerce' ),
				'id'       => "alg_wc_pdf_invoicing_doc_{$this->doc_id}[number_format]",
				'default'  => alg_wc_pdf_invoicing_get_default( 'number_format' ),
				'type'     => 'text',
				'css'      => 'width:100%;',
				'alg_wc_pi_sanitize' => 'textarea',
			),
			array(
				'title'    => __( 'My account', 'pdf-invoicing-for-woocommerce' ),
				'desc_tip' => __( 'Allow customers to view documents in "My account > Orders".', 'pdf-invoicing-for-woocommerce' ),
				'desc'     => __( 'Enable', 'pdf-invoicing-for-woocommerce' ),
				'id'       => "alg_wc_pdf_invoicing_doc_{$this->doc_id}[my_account_orders]",
				'default'  => alg_wc_pdf_invoicing_get_default( 'my_account_orders' ),
				'type'     => 'checkbox',
			),
			array(
				'title'    => __( 'Bulk actions', 'pdf-invoicing-for-woocommerce' ),
				'desc_tip' => __( 'Actions in "Orders > Bulk actions".', 'pdf-invoicing-for-woocommerce' ),
				'id'       => "alg_wc_pdf_invoicing_doc_{$this->doc_id}[order_bulk_actions]",
				'default'  => alg_wc_pdf_invoicing_get_default( 'order_bulk_actions' ),
				'type'     => 'multiselect',
				'class'    => 'chosen_select',
				'options'  => alg_wc_pdf_invoicing()->admin->get_order_bulk_actions(),
			),
			array(
				'type'     => 'sectionend',
				'id'       => "alg_wc_pdf_invoicing_doc_{$this->doc_id}_general_options",
			),
			array(
				'title'    => __( 'Page Options', 'pdf-invoicing-for-woocommerce' ),
				'type'     => 'title',
				'id'       => "alg_wc_pdf_invoicing_doc_{$this->doc_id}_page_options",
			),
			array(
				'title'    => __( 'Page orientation', 'pdf-invoicing-for-woocommerce' ),
				'id'       => "alg_wc_pdf_invoicing_doc_{$this->doc_id}[page_orientation]",
				'default'  => alg_wc_pdf_invoicing_get_default( 'page_orientation' ),
				'type'     => 'select',
				'class'    => 'chosen_select',
				'options'  => array(
					'P' => __( 'Portrait', 'pdf-invoicing-for-woocommerce' ),
					'L' => __( 'Landscape', 'pdf-invoicing-for-woocommerce' ),
				),
			),
			array(
				'title'    => __( 'Page format', 'pdf-invoicing-for-woocommerce' ),
				'id'       => "alg_wc_pdf_invoicing_doc_{$this->doc_id}[page_format]",
				'default'  => alg_wc_pdf_invoicing_get_default( 'page_format' ),
				'type'     => 'select',
				'class'    => 'chosen_select',
				'options'  => Alg_WC_PDF_Invoicing_Settings_Doc_Page_Formats::get_page_formats(),
			),
			array(
				'desc'     => __( 'Custom width', 'pdf-invoicing-for-woocommerce' ),
				'desc_tip' => __( 'Ignored unless "Custom" is selected for the "Page format" option.', 'pdf-invoicing-for-woocommerce' ),
				'id'       => "alg_wc_pdf_invoicing_doc_{$this->doc_id}[page_format_custom_width]",
				'default'  => alg_wc_pdf_invoicing_get_default( 'page_format_custom_width' ),
				'type'     => 'number',
				'custom_attributes' => array( 'min' => '0', 'step' => '0.000001' ),
			),
			array(
				'desc'     => __( 'Custom height', 'pdf-invoicing-for-woocommerce' ),
				'desc_tip' => __( 'Ignored unless "Custom" is selected for the "Page format" option.', 'pdf-invoicing-for-woocommerce' ),
				'id'       => "alg_wc_pdf_invoicing_doc_{$this->doc_id}[page_format_custom_height]",
				'default'  => alg_wc_pdf_invoicing_get_default( 'page_format_custom_height' ),
				'type'     => 'number',
				'custom_attributes' => array( 'min' => '0', 'step' => '0.000001' ),
			),
			array(
				'title'    => __( 'Margin: Top', 'pdf-invoicing-for-woocommerce' ),
				'id'       => "alg_wc_pdf_invoicing_doc_{$this->doc_id}[margin_top]",
				'default'  => alg_wc_pdf_invoicing_get_default( 'margin_top' ),
				'type'     => 'number',
			),
			array(
				'title'    => __( 'Margin: Left', 'pdf-invoicing-for-woocommerce' ),
				'id'       => "alg_wc_pdf_invoicing_doc_{$this->doc_id}[margin_left]",
				'default'  => alg_wc_pdf_invoicing_get_default( 'margin_left' ),
				'type'     => 'number',
			),
			array(
				'title'    => __( 'Margin: Right', 'pdf-invoicing-for-woocommerce' ),
				'id'       => "alg_wc_pdf_invoicing_doc_{$this->doc_id}[margin_right]",
				'default'  => alg_wc_pdf_invoicing_get_default( 'margin_right' ),
				'type'     => 'number',
			),
			array(
				'title'    => __( 'RTL', 'pdf-invoicing-for-woocommerce' ),
				'desc_tip' => __( 'Enable Right-To-Left language mode.', 'pdf-invoicing-for-woocommerce' ),
				'desc'     => __( 'Enable', 'pdf-invoicing-for-woocommerce' ),
				'id'       => "alg_wc_pdf_invoicing_doc_{$this->doc_id}[rtl]",
				'default'  => alg_wc_pdf_invoicing_get_default( 'rtl' ),
				'type'     => 'checkbox',
			),
			array(
				'title'    => __( 'Page background image', 'pdf-invoicing-for-woocommerce' ),
				'desc_tip' => __( 'You can use shortcodes here.', 'pdf-invoicing-for-woocommerce' ),
				'desc'     => sprintf(
					/* Translators: %s: Directory path. */
					__( 'Path in %s', 'pdf-invoicing-for-woocommerce' ),
					'<code>' . $this->get_uploads_dir() . '</code>'
				),
				'id'       => "alg_wc_pdf_invoicing_doc_{$this->doc_id}[page_background_img]",
				'default'  => alg_wc_pdf_invoicing_get_default( 'page_background_img' ),
				'type'     => 'text',
				'css'      => 'width:100%;',
				'alg_wc_pi_sanitize' => 'textarea',
			),
			array(
				'title'    => __( 'Page foreground image', 'pdf-invoicing-for-woocommerce' ),
				'desc_tip' => __( 'You can use shortcodes here.', 'pdf-invoicing-for-woocommerce' ),
				'desc'     => sprintf(
					/* Translators: %s: Directory path. */
					__( 'Path in %s', 'pdf-invoicing-for-woocommerce' ),
					'<code>' . $this->get_uploads_dir() . '</code>'
				),
				'id'       => "alg_wc_pdf_invoicing_doc_{$this->doc_id}[page_foreground_img]",
				'default'  => alg_wc_pdf_invoicing_get_default( 'page_foreground_img' ),
				'type'     => 'text',
				'css'      => 'width:100%;',
				'alg_wc_pi_sanitize' => 'textarea',
			),
			array(
				'type'     => 'sectionend',
				'id'       => "alg_wc_pdf_invoicing_doc_{$this->doc_id}_page_options",
			),
			array(
				'title'    => __( 'Header Options', 'pdf-invoicing-for-woocommerce' ),
				'type'     => 'title',
				'id'       => "alg_wc_pdf_invoicing_doc_{$this->doc_id}_header_options",
			),
			array(
				'title'    => __( 'Enable header', 'pdf-invoicing-for-woocommerce' ),
				'desc'     => __( 'Enable', 'pdf-invoicing-for-woocommerce' ),
				'id'       => "alg_wc_pdf_invoicing_doc_{$this->doc_id}[enable_header]",
				'default'  => alg_wc_pdf_invoicing_get_default( 'enable_header' ),
				'type'     => 'checkbox',
			),
			array(
				'title'    => __( 'Header image', 'pdf-invoicing-for-woocommerce' ),
				'desc_tip' => __( 'You can use shortcodes here.', 'pdf-invoicing-for-woocommerce' ),
				'desc'     => sprintf(
					/* Translators: %s: Directory path. */
					__( 'Path in %s', 'pdf-invoicing-for-woocommerce' ),
					'<code>' . $this->get_uploads_dir() . '</code>'
				),
				'id'       => "alg_wc_pdf_invoicing_doc_{$this->doc_id}[header_img]",
				'default'  => alg_wc_pdf_invoicing_get_default( 'header_img' ),
				'type'     => 'text',
				'css'      => 'width:100%;',
				'alg_wc_pi_sanitize' => 'textarea',
			),
			array(
				'title'    => __( 'Header image width', 'pdf-invoicing-for-woocommerce' ),
				'desc_tip' => __( 'In mm.', 'pdf-invoicing-for-woocommerce' ) . ' ' .
					__( 'If equal to zero, it is automatically calculated.', 'pdf-invoicing-for-woocommerce' ),
				'id'       => "alg_wc_pdf_invoicing_doc_{$this->doc_id}[header_img_width]",
				'default'  => alg_wc_pdf_invoicing_get_default( 'header_img_width' ),
				'type'     => 'number',
				'custom_attributes' => array( 'min' => 0 ),
			),
			array(
				'title'    => __( 'Header image alignment', 'pdf-invoicing-for-woocommerce' ),
				'id'       => "alg_wc_pdf_invoicing_doc_{$this->doc_id}[header_img_palign]",
				'default'  => alg_wc_pdf_invoicing_get_default( 'header_img_palign' ),
				'type'     => 'select',
				'class'    => 'chosen_select',
				'options'  => array(
					'L' => __( 'Left', 'pdf-invoicing-for-woocommerce' ),
					'R' => __( 'Right', 'pdf-invoicing-for-woocommerce' ),
				),
			),
			array(
				'title'    => __( 'Header title', 'pdf-invoicing-for-woocommerce' ),
				'desc_tip' => __( 'You can use shortcodes here.', 'pdf-invoicing-for-woocommerce' ),
				'id'       => "alg_wc_pdf_invoicing_doc_{$this->doc_id}[header_title]",
				'default'  => alg_wc_pdf_invoicing_get_default( 'header_title' ),
				'type'     => 'text',
				'css'      => 'width:100%;',
				'alg_wc_pi_sanitize' => 'textarea',
			),
			array(
				'title'    => __( 'Header text', 'pdf-invoicing-for-woocommerce' ),
				'desc_tip' => __( 'You can use shortcodes here.', 'pdf-invoicing-for-woocommerce' ),
				'id'       => "alg_wc_pdf_invoicing_doc_{$this->doc_id}[header_text]",
				'default'  => alg_wc_pdf_invoicing_get_default( 'header_text' ),
				'type'     => 'textarea',
				'css'      => 'width:100%;height:100px;' . ( 'yes' === get_option( 'alg_wc_pdf_invoicing_use_monospace_font', 'no' ) ? 'font-family:monospace;' : '' ),
			),
			array(
				'title'    => __( 'Header text alignment', 'pdf-invoicing-for-woocommerce' ),
				'id'       => "alg_wc_pdf_invoicing_doc_{$this->doc_id}[header_text_palign]",
				'default'  => alg_wc_pdf_invoicing_get_default( 'header_text_palign' ),
				'type'     => 'select',
				'class'    => 'chosen_select',
				'options'  => array(
					'L' => __( 'Left', 'pdf-invoicing-for-woocommerce' ),
					'R' => __( 'Right', 'pdf-invoicing-for-woocommerce' ),
				),
			),
			array(
				'title'    => __( 'Header text color', 'pdf-invoicing-for-woocommerce' ),
				'id'       => "alg_wc_pdf_invoicing_doc_{$this->doc_id}[header_text_color]",
				'default'  => alg_wc_pdf_invoicing_get_default( 'header_text_color' ),
				'type'     => 'color',
			),
			array(
				'title'    => __( 'Header line color', 'pdf-invoicing-for-woocommerce' ),
				'id'       => "alg_wc_pdf_invoicing_doc_{$this->doc_id}[header_line_color]",
				'default'  => alg_wc_pdf_invoicing_get_default( 'header_line_color' ),
				'type'     => 'color',
			),
			array(
				'title'    => __( 'Header font', 'pdf-invoicing-for-woocommerce' ),
				'desc_tip' => $this->get_fonts_desc(),
				'id'       => "alg_wc_pdf_invoicing_doc_{$this->doc_id}[header_font_family]",
				'default'  => alg_wc_pdf_invoicing_get_default( 'header_font_family' ),
				'type'     => 'select',
				'class'    => 'chosen_select',
				'options'  => $this->get_fonts(),
			),
			array(
				'title'    => __( 'Header font size', 'pdf-invoicing-for-woocommerce' ),
				'id'       => "alg_wc_pdf_invoicing_doc_{$this->doc_id}[header_font_size]",
				'default'  => alg_wc_pdf_invoicing_get_default( 'header_font_size' ),
				'type'     => 'number',
				'custom_attributes' => array( 'min' => 1 ),
			),
			array(
				'type'     => 'sectionend',
				'id'       => "alg_wc_pdf_invoicing_doc_{$this->doc_id}_header_options",
			),
			array(
				'title'    => __( 'Footer Options', 'pdf-invoicing-for-woocommerce' ),
				'type'     => 'title',
				'id'       => "alg_wc_pdf_invoicing_doc_{$this->doc_id}_footer_options",
			),
			array(
				'title'    => __( 'Enable footer', 'pdf-invoicing-for-woocommerce' ),
				'desc'     => __( 'Enable', 'pdf-invoicing-for-woocommerce' ),
				'id'       => "alg_wc_pdf_invoicing_doc_{$this->doc_id}[enable_footer]",
				'default'  => alg_wc_pdf_invoicing_get_default( 'enable_footer' ),
				'type'     => 'checkbox',
			),
			array(
				'title'    => __( 'Footer HTML content', 'pdf-invoicing-for-woocommerce' ),
				'desc_tip' => __( 'You can use shortcodes here.', 'pdf-invoicing-for-woocommerce' ),
				'desc'     => sprintf(
					/* Translators: %s: Placeholder list. */
					__( 'Available placeholders: %s.', 'pdf-invoicing-for-woocommerce' ),
					'<code>%page_num%</code>, <code>%total_pages%</code>'
				),
				'id'       => "alg_wc_pdf_invoicing_doc_{$this->doc_id}[footer_text]",
				'default'  => alg_wc_pdf_invoicing_get_default( 'footer_text' ),
				'type'     => 'textarea',
				'css'      => 'width:100%;height:100px;' . ( 'yes' === get_option( 'alg_wc_pdf_invoicing_use_monospace_font', 'no' ) ? 'font-family:monospace;' : '' ),
			),
			array(
				'title'    => __( 'Footer height', 'pdf-invoicing-for-woocommerce' ),
				'id'       => "alg_wc_pdf_invoicing_doc_{$this->doc_id}[footer_height]",
				'default'  => alg_wc_pdf_invoicing_get_default( 'footer_height' ),
				'type'     => 'number',
			),
			array(
				'title'    => __( 'Footer text alignment', 'pdf-invoicing-for-woocommerce' ),
				'id'       => "alg_wc_pdf_invoicing_doc_{$this->doc_id}[footer_text_align]",
				'default'  => alg_wc_pdf_invoicing_get_default( 'footer_text_align' ),
				'type'     => 'select',
				'class'    => 'chosen_select',
				'options'  => array(
					'L' => __( 'Left', 'pdf-invoicing-for-woocommerce' ),
					'C' => __( 'Center', 'pdf-invoicing-for-woocommerce' ),
					'R' => __( 'Right', 'pdf-invoicing-for-woocommerce' ),
					'J' => __( 'Justify', 'pdf-invoicing-for-woocommerce' ),
				),
			),
			array(
				'title'    => __( 'Footer text color', 'pdf-invoicing-for-woocommerce' ),
				'id'       => "alg_wc_pdf_invoicing_doc_{$this->doc_id}[footer_text_color]",
				'default'  => alg_wc_pdf_invoicing_get_default( 'footer_text_color' ),
				'type'     => 'color',
			),
			array(
				'title'    => __( 'Footer line color', 'pdf-invoicing-for-woocommerce' ),
				'id'       => "alg_wc_pdf_invoicing_doc_{$this->doc_id}[footer_line_color]",
				'default'  => alg_wc_pdf_invoicing_get_default( 'footer_line_color' ),
				'type'     => 'color',
			),
			array(
				'title'    => __( 'Footer font', 'pdf-invoicing-for-woocommerce' ),
				'desc_tip' => $this->get_fonts_desc(),
				'id'       => "alg_wc_pdf_invoicing_doc_{$this->doc_id}[footer_font_family]",
				'default'  => alg_wc_pdf_invoicing_get_default( 'footer_font_family' ),
				'type'     => 'select',
				'class'    => 'chosen_select',
				'options'  => $this->get_fonts(),
			),
			array(
				'title'    => __( 'Footer font size', 'pdf-invoicing-for-woocommerce' ),
				'id'       => "alg_wc_pdf_invoicing_doc_{$this->doc_id}[footer_font_size]",
				'default'  => alg_wc_pdf_invoicing_get_default( 'footer_font_size' ),
				'type'     => 'number',
				'custom_attributes' => array( 'min' => 1 ),
			),
			array(
				'type'     => 'sectionend',
				'id'       => "alg_wc_pdf_invoicing_doc_{$this->doc_id}_footer_options",
			),
			array(
				'title'    => __( 'Content Options', 'pdf-invoicing-for-woocommerce' ),
				'type'     => 'title',
				'id'       => "alg_wc_pdf_invoicing_doc_{$this->doc_id}_content_options",
			),
			array(
				'title'    => __( 'HTML content', 'pdf-invoicing-for-woocommerce' ),
				'desc_tip' => __( 'You can use shortcodes here.', 'pdf-invoicing-for-woocommerce' ),
				'id'       => "alg_wc_pdf_invoicing_doc_{$this->doc_id}[html_content]",
				'default'  => alg_wc_pdf_invoicing_get_default( 'html_content' ),
				'type'     => 'alg_wc_text_editor',
				'class'    => 'alg-wc-shortcode-field',
				'css'      => 'height:800px;' . ( 'yes' === get_option( 'alg_wc_pdf_invoicing_use_monospace_font', 'no' ) ? 'font-family:monospace;' : '' ),
				'alg_wc_pi_sanitize' => 'textarea',
			),
			array(
				'title'    => __( 'Line color', 'pdf-invoicing-for-woocommerce' ),
				'id'       => "alg_wc_pdf_invoicing_doc_{$this->doc_id}[line_color]",
				'default'  => alg_wc_pdf_invoicing_get_default( 'line_color' ),
				'type'     => 'color',
			),
			array(
				'title'    => __( 'Font', 'pdf-invoicing-for-woocommerce' ),
				'desc_tip' => $this->get_fonts_desc(),
				'id'       => "alg_wc_pdf_invoicing_doc_{$this->doc_id}[font_family]",
				'default'  => alg_wc_pdf_invoicing_get_default( 'font_family' ),
				'type'     => 'select',
				'class'    => 'chosen_select',
				'options'  => $this->get_fonts(),
			),
			array(
				'title'             => __( 'Font size', 'pdf-invoicing-for-woocommerce' ),
				'id'                => "alg_wc_pdf_invoicing_doc_{$this->doc_id}[font_size]",
				'default'           => alg_wc_pdf_invoicing_get_default( 'font_size' ),
				'type'              => 'number',
				'custom_attributes' => array( 'min' => 1 ),
			),
			array(
				'type'     => 'sectionend',
				'id'       => "alg_wc_pdf_invoicing_doc_{$this->doc_id}_content_options",
			),
			array(
				'title'    => __( 'Styling and Filtering Options', 'pdf-invoicing-for-woocommerce' ),
				'desc'     => __( 'Applied in "HTML content" and "Footer HTML content" options.', 'pdf-invoicing-for-woocommerce' ),
				'type'     => 'title',
				'id'       => "alg_wc_pdf_invoicing_doc_{$this->doc_id}_style_options",
			),
			array(
				'title'    => __( 'HTML style', 'pdf-invoicing-for-woocommerce' ),
				'desc_tip' => __( 'You can use shortcodes here.', 'pdf-invoicing-for-woocommerce' ),
				'id'       => "alg_wc_pdf_invoicing_doc_{$this->doc_id}[html_style]",
				'default'  => alg_wc_pdf_invoicing_get_default( 'html_style' ),
				'type'     => 'textarea',
				'css'      => 'width:100%;height:300px;' . ( 'yes' === get_option( 'alg_wc_pdf_invoicing_use_monospace_font', 'no' ) ? 'font-family:monospace;' : '' ),
			),
			array(
				'title'    => __( 'HTML content filters', 'pdf-invoicing-for-woocommerce' ),
				'desc'     => __( 'Balance tags', 'pdf-invoicing-for-woocommerce' ) . ' (' . __( 'recommended', 'pdf-invoicing-for-woocommerce' ) . ')',
				'desc_tip' => (
					__( 'Balances tags, i.e., prevents unmatched elements.', 'pdf-invoicing-for-woocommerce' ) .
					'<br>' .
					sprintf(
						/* Translators: %s: Function page link. */
						__( 'Uses WordPress %s function.', 'pdf-invoicing-for-woocommerce' ),
						'<a href="https://developer.wordpress.org/reference/functions/force_balance_tags/" target="_blank">' .
							'<code>force_balance_tags()</code>' .
						'</a>'
					)
				),
				'id'       => "alg_wc_pdf_invoicing_doc_{$this->doc_id}[html_content_force_balance_tags]",
				'default'  => alg_wc_pdf_invoicing_get_default( 'html_content_force_balance_tags' ),
				'type'     => 'checkbox',
				'checkboxgroup' => 'start',
			),
			array(
				'desc'     => __( 'Replace line breaks', 'pdf-invoicing-for-woocommerce' ),
				'desc_tip' => (
					sprintf(
						/* Translators: %s: HTML line break tag. */
						__( 'Replaces double line breaks with paragraph elements, and all remaining line breaks with %s tag.', 'pdf-invoicing-for-woocommerce' ),
						'<code>' . esc_html( '<br>' ) . '</code>'
					) .
					'<br>' .
					sprintf(
						/* Translators: %s: Function page link. */
						__( 'Uses WordPress %s function.', 'pdf-invoicing-for-woocommerce' ),
						'<a href="https://developer.wordpress.org/reference/functions/wpautop/" target="_blank">' .
							'<code>wpautop()</code>' .
						'</a>'
					)
				),
				'id'       => "alg_wc_pdf_invoicing_doc_{$this->doc_id}[html_content_wpautop]",
				'default'  => alg_wc_pdf_invoicing_get_default( 'html_content_wpautop' ),
				'type'     => 'checkbox',
				'checkboxgroup' => 'end',
			),
			array(
				'type'     => 'sectionend',
				'id'       => "alg_wc_pdf_invoicing_doc_{$this->doc_id}_style_options",
			),
		);
	}

}

endif;
