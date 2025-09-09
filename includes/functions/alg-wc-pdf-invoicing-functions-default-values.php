<?php
/**
 * PDF Invoicing for WooCommerce - Functions - Default Values
 *
 * @version 2.4.0
 * @since   1.0.0
 *
 * @author  WPFactory
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'alg_wc_pdf_invoicing_get_default' ) ) {
	/**
	 * alg_wc_pdf_invoicing_get_default.
	 *
	 * @version 2.4.0
	 * @since   1.0.0
	 *
	 * @todo    (dev) `str_replace( "\t", '', ... )`?
	 * @todo    (dev) `[each_item type="fee"]`
	 * @todo    (dev) `[each_item type="shipping"]`
	 * @todo    (dev) better defaults, e.g., HTML style & content
	 */
	function alg_wc_pdf_invoicing_get_default( $option_id, $doc_id = 0 ) {
		switch ( $option_id ) {

			// General Options
			case 'enable':
				return 'yes';
			case 'admin_title':
				return apply_filters( 'alg_wc_pdf_invoicing_default_doc_title', __( 'Invoice', 'pdf-invoicing-for-woocommerce' ), $doc_id );
			case 'hooks':
				return array( 'manual' );
			case 'emails':
				return array();
			case 'number_format':
				return 'INV-[doc_counter format="%06d"]';
			case 'my_account_orders':
				return 'no';
			case 'order_bulk_actions':
				return array( 'download' );

			// Page Options
			case 'page_orientation':
				return 'P';
			case 'page_format':
				return 'A4';
			case 'page_format_custom_width':
				return 0;
			case 'page_format_custom_height':
				return 0;
			case 'margin_top':
				return 27; // PDF_MARGIN_TOP
			case 'margin_left':
				return 15; // PDF_MARGIN_LEFT
			case 'margin_right':
				return 15; // PDF_MARGIN_RIGHT
			case 'rtl':
				return 'no';
			case 'page_background_img':
				return '';
			case 'page_foreground_img':
				return '';

			// Header Options
			case 'enable_header':
				return 'yes';
			case 'header_img':
				return '';
			case 'header_img_width':
				return 50;
			case 'header_img_palign':
				return 'L';
			case 'header_text_palign':
				return 'L';
			case 'header_title':
				return __( 'Invoice', 'pdf-invoicing-for-woocommerce' ) . ' [doc_nr]';
			case 'header_text':
				return str_replace( "\t", '',
					'Company name
					Company address' );
			case 'header_text_color':
				return '#000000';
			case 'header_line_color':
				return '#000000';
			case 'header_font_family':
				return 'dejavusans';
			case 'header_font_size':
				return 7;

			// Footer Options
			case 'enable_footer':
				return 'yes';
			case 'footer_text':
				return '%page_num% / %total_pages%';
			case 'footer_text_align':
				return 'R';
			case 'footer_font_size':
				return 7;
			case 'footer_height':
				return 10; // PDF_MARGIN_FOOTER
			case 'footer_font_family':
				return 'dejavusans';
			case 'footer_text_color':
				return '#000000';

			// Content Options
			case 'font_family':
				return 'dejavusans';
			case 'line_color':
				return '#000000';
			case 'font_size':
				return 8;
			case 'html_content':
				return str_replace( "\t", '',
					'<h1>Invoice</h1>

					<table>
					    <tbody>
					        <tr><td>Invoice nr.</td><td><strong>[doc_nr]</strong></td></tr>
					        <tr><td>Invoice date</td><td><strong>[doc_formatted_date]</strong></td></tr>
					        <tr><td>Order nr.</td><td>[order_number]</td></tr>
					        <tr><td>Order date</td><td>[order_formatted_date_created]</td></tr>
					    </tbody>
					</table>

					[clear]

					<!-- Order Addresses -->
					<table>
					    <tbody>
					        <tr><th>Seller</th><th>Buyer</th></tr>
					        <tr><td>Company name<br>Company address</td><td>[order_formatted_billing_address]</td></tr>
					    </tbody>
					</table>

					[clear]

					<!-- Header table -->
					<table class="order-items">
					    <tbody>
					        <tr>
					            <th style="width:5%">Nr.</th>
					            <th style="width:56%">Description</th>
					            <th style="width:12%">SKU</th>
					            <th class="qty" style="width:7%">Qty</th>
					            <th class="price" style="width:10%">Price (excl. tax)</th>
					            <th class="price" style="width:10%">Total (excl. tax)</th>
					        </tr>
					    </tbody>
					</table>

					[each_item]
					<table class="order-items">
					    <tbody>
					        <tr>
					            <td style="width:5%">[item_nr]</td>
					            <td style="width:56%">[item_name]</td>
					            <td style="width:12%">[item_product_sku]</td>
					            <td class="qty" style="width:7%">[item_qty]</td>
					            <td class="price" style="width:10%">[item_single format="price"]</td>
					            <td class="price" style="width:10%">[item_total format="price"]</td>
					        </tr>
					    </tbody>
					</table>
					[/each_item]

					[if value1="{order_shipping_total}" operator="greater" value2="0"]
					<table class="order-items">
					    <tbody>
					        <tr>
					            <td style="width:5%">[order_total_items_count add="1"]</td>
					            <td style="width:56%">[order_shipping_method]</td>
					            <td style="width:12%"></td>
					            <td class="qty" style="width:7%">1</td>
					            <td class="price" style="width:10%">[order_shipping_total format="price"]</td>
					            <td class="price" style="width:10%">[order_shipping_total format="price"]</td>
					        </tr>
					    </tbody>
					</table>
					[/if]

					<table class="totals">
					    <tbody>
					        <tr><th>Total (excl. tax)</th><td>[order_total_excl_tax format="price"]</td></tr>
					        <tr><th>Tax</th><td>[order_total_tax format="price"]</td></tr>
					        <tr><th>Total (incl. tax)</th><td>[order_total format="price"]</td></tr>
					    </tbody>
					</table>

					<p><strong>Total in words:</strong> [order_total_in_words]</p>
					<p><strong>Payment method:</strong> [order_payment_method_title]</p>

					[clear]

					<!-- Footer -->
					<table style="margin-top: 30px">
					    <tbody>
					        <tr><th>Invoice created by:</th><td>[doc_author_full_name]</td></tr>
					        <tr><th>Invoice accepted by:</th><td></td></tr>
					    </tbody>
					</table>
					' );

			// Styling and Filtering Options
			case 'html_content_wpautop':
				return 'yes';
			case 'html_content_force_balance_tags':
				return 'yes';
			case 'html_style':
				return str_replace( "\t", '',
					'table {
					    width: 100%;
					}

					table th {
					    font-weight: bold;
					}

					table, tr, th, td {
					    border: 1px solid gray;
					    border-collapse: collapse;
					    padding: 2px;
					}

					table.order-items th.price, table.order-items td.price {
					    text-align: right;
					}

					table.order-items th.qty, table.order-items td.qty {
					    text-align: center;
					}

					table.totals {
					    border: none;
					}

					table.totals th {
					    width: 90%;
					    text-align: right;
					    border: none;
					    font-weight: normal;
					}

					table.totals td {
					    width: 10%;
					    text-align: right;
					    border: none;
					    font-weight: bold;
					}
					' );

		}
	}
}
