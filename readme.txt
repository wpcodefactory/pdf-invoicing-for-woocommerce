=== PDF Invoicing for WooCommerce ===
Contributors: wpcodefactory, algoritmika, anbinder, karzin, omardabbas
Tags: woocommerce, pdf, invoice, credit note, packing list
Requires at least: 4.4
Tested up to: 6.6
Stable tag: 2.2.1
License: GNU General Public License v3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Add PDF invoices to WooCommerce.

== Description ==

**PDF Invoicing for WooCommerce** plugin lets you add various PDF documents, e.g., invoices, packing slips, credit notes etc. to WooCommerce.

### &#9989; Main Features ###

* **Create** PDF documents **manually** or **automatically** (e.g., on new order; on order status change).
* Add PDF documents to **email attachments**.
* Add PDF documents to **My account > Orders**.
* Customize PDF documents **number format** (e.g., sequential; date based etc.).
* Customize PDF documents **page** orientation, format, margins.
* Set PDF documents **header** image, title, text etc.
* Set PDF documents **footer** text etc.
* Set PDF documents **content** style and text with our [shortcodes](https://wpfactory.com/item/pdf-invoicing-for-woocommerce/#section-shortcodes).
* And more...

### &#128472; Feedback ###

* We are open to your suggestions and feedback. Thank you for using or trying out one of our plugins!
* [Visit plugin site](https://wpfactory.com/item/pdf-invoicing-for-woocommerce/).

### &#8505; More ###

* The plugin is **"High-Performance Order Storage (HPOS)"** compatible.

== Installation ==

1. Upload the entire plugin folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the "Plugins" menu in WordPress.
3. Start by visiting plugin settings at "WooCommerce > Settings > PDF Invoicing".

== Changelog ==

= 2.2.1 - 28/10/2024 =
* Dev - Admin settings descriptions updated.
* Dev - Code refactoring.

= 2.2.0 - 16/10/2024 =
* Dev - General - Advanced Options - "Early TCPDF load" option added (defaults to `no`).
* Dev - General - Advanced Options - "Allowed TCPDF methods" option added (defaults to an empty string).
* Dev - Plugin settings moved to the "WPFactory" menu.
* Dev - "Key Manager" added.
* Dev - "Recommendations" added.
* Dev - TCPDF - Library updated (v6.7.6).
* Dev - Code refactoring.
* WC tested up to: 9.3.

= 2.1.4 - 31/07/2024 =
* WC tested up to: 9.1.
* Tested up to: 6.6.

= 2.1.3 - 28/05/2024 =
* Dev - Shortcodes - `[order_shipping_total_incl_tax]` shortcode added.
* Dev - Shortcodes - `[order_shipping_total_excl_tax]` shortcode alias (for the `[order_shipping_total]`) added.
* WC tested up to: 8.9.
* Tested up to: 6.5.
* WooCommerce added to the "Requires Plugins" (plugin header).

= 2.1.2 - 11/03/2024 =
* Dev - Shortcodes - `[order_billing_last_name]` shortcode added.
* Readme.txt - Tags - `woo commerce` tag removed.

= 2.1.1 - 07/03/2024 =
* Dev - Shortcodes - `[order_checkout_payment_url]` shortcode added.
* Dev - Code refactoring.

= 2.1.0 - 22/02/2024 =
* Dev - PHP 8.2 compatibility - "Creation of dynamic property is deprecated" notice fixed.
* Dev - Code refactoring.
* WC tested up to: 8.6.

= 2.0.1 - 02/02/2024 =
* Fix - HPOS compatibility - "Order list columns", "Order edit page meta box", "Order bulk actions".
* WC tested up to: 8.5.
* Tested up to: 6.4.

= 2.0.0 - 26/09/2023 =
* Dev – "High-Performance Order Storage (HPOS)" compatibility.
* Dev - Admin settings descriptions updated.
* Dev - Minor code refactoring.

= 1.9.3 - 26/09/2023 =
* WC tested up to: 8.1.
* Tested up to: 6.3.
* Plugin icon, banner updated.

= 1.9.2 - 21/06/2023 =
* Fix - Sequential counters on fresh installs issue fixed.

= 1.9.1 - 19/06/2023 =
* WC tested up to: 7.8.
* Tested up to: 6.2.

= 1.9.0 - 23/03/2023 =
* Dev - "Update counter" MySQL transaction code improved.
* Dev - "Create doc" button redesigned (using dashicon instead of text now).
* Dev - Developers - Shortcodes - `[each_item]` - `alg_wc_pdf_invoicing_shortcode_each_item_items`, `alg_wc_pdf_invoicing_shortcode_each_item_before_item`, `alg_wc_pdf_invoicing_shortcode_each_item_after_item` filters added.
* Dev - Code refactoring.
* WC tested up to: 7.5.

= 1.8.0 - 16/12/2022 =
* Fix - "Notice: Constant K_TCPDF_CALLS_IN_HTML already defined in..." fixed.
* Dev - General - Advanced Options - "Use custom config" option added (defaults to `yes`). It uses the `K_TCPDF_EXTERNAL_CONFIG` constant to load a custom `tcpdf_config.php` file.
* Dev - General - Advanced Options - Use custom config - "TCPDF methods in HTML" option added (defaults to `yes`). Sets the `K_TCPDF_CALLS_IN_HTML` constant.
* Dev - General - Advanced Options - "Set default images directory" option added (defaults to `yes`). Sets the `K_PATH_IMAGES` constant.
* Dev - Doc - Page Options - "Page foreground image" option added.
* Dev - Developers - `alg_wc_pdf_invoicing_create_docs` filter added.
* WC tested up to: 7.2.
* Tested up to: 6.1.

= 1.7.1 - 30/10/2022 =
* Dev - Shortcodes - `[prop]` - `subtract` and `divide` attributes added.

= 1.7.0 - 24/10/2022 =
* Dev - Now checking if classes (`TCPDF` and `\setasign\Fpdi\TcpdfFpdi`) exist before including the libraries.
* Dev - Shortcodes - `[each_item]` - Now checking if it's a valid `$order`.
* Dev - Developers - `alg_wc_pdf_invoicing_doc_created` and `alg_wc_pdf_invoicing_doc_removed` actions added.
* Dev - Minor code refactoring.
* Deploy script added.
* WC tested up to: 7.0.

= 1.6.0 - 01/08/2022 =
* Fix - Doc - Content Options - HTML content - Typo in the default value fixed.
* Dev - Doc - Page Options - "Page background image" option added.
* Dev - Shortcodes - `[page_break]` shortcode added.
* Dev - Shortcodes - `[each_item]` - `product_id` attribute added.
* Dev - Shortcodes - `[each_item]`, `[each_refund]` - `before` and `after` attributes added.
* Dev - It's now possible to call TCPDF methods in HTML with the `<tcpdf>` tag, e.g., `<tcpdf method="AddPage" />` (`K_TCPDF_CALLS_IN_HTML` constant set to `true`).
* Tested up to: 6.0.
* WC tested up to: 6.7.

= 1.5.0 - 14/04/2022 =
* Fix - Admin actions - Displaying actions for the `refunded` orders as well now.
* Dev - General - Advanced Options - Use monospace font - Now applied in the "Header text" and "Footer HTML content" settings as well.
* Dev - Doc - "Bulk actions" option added.
* Dev - Doc - "Styling and Filtering Options" settings section added: "HTML style" and "HTML content filters" options moved from the "Content Options" subsection.
* Dev - Doc - Page format - "Custom" (and "Custom width", "Custom height") values added.
* Dev - Doc - Create:
    * "On payment complete" trigger added.
    * "On checkout order processed" trigger added.
    * "On order partially refunded" trigger added.
* Dev - Doc - Emails - "Partially refunded order" email added.
* Dev - Doc - Footer:
    * "Footer height" option added.
    * "Footer text color" option added.
    * Footer HTML content - HTML is processed now. Option renamed (was "Footer text").
* Dev - Shortcodes - `[each_refund]` shortcode added.
* Dev - Shortcodes - `[prop]`:
    * `refund_nr` option added.
    * `refund_total` option added.
    * `refund_reason` option added.
    * `order_total_refunded` option added.
    * `order_total_tax_refunded` option added.
    * `order_total_shipping_refunded` option added.
    * `order_status` option added.
    * `order_total_items_qty` option added.
    * `order_total_excl_tax_excl_shipping` option added.
    * `item_product_image` option added.
    * `format` - `price` - Taking into account current order currency now.
    * `doc_formatted_date` - `add_days` attribute added.
    * `item_product_meta` - `use_parent` attribute added.
* Dev - `get_pdf()`: Checking if `K_PATH_IMAGES` is defined now.
* Dev - Merge PDFs: FPDI library (v2.3.6) added.
* Dev - "TCPDF" library updated to v6.4.4 (was v6.3.2).
* Dev - Code refactoring.
* WC tested up to: 6.4.
* Tested up to: 5.9.

= 1.4.0 - 09/08/2021 =
* Dev - General - Admin order edit page meta box added.
* Dev - General - Admin actions - "Print" button added.
* Dev - General - Admin actions - Images replaced with icons.
* Dev - General - Advanced Options - "Shortcode prefix" option added (defaults to empty string).
* Dev - General - Advanced Options - "Suppress errors" option added (defaults to `yes`).
* Dev - General - Advanced Options - "Use monospace font" option added (defaults to `no`).
* Dev - Page Options - "RTL" option added.
* Dev - Header Options - "Header text alignment" option added.
* Dev - Header Options - "Header text color" option added.
* Dev - Header Options - "Header line color" option added.
* Dev - Header Options - "Header image width" option added.
* Dev - Header Options - "Header image alignment" option added.
* Dev - Header Options - "Font" option added.
* Dev - Footer Options - "Font" option added.
* Dev - Content Options - "Line color" option added.
* Dev - Content Options - HTML content filters - "Balance tags" option added (defaults to `yes`).
* Dev - Content Options - HTML content filters - "Replace line breaks" option added (defaults to `no`).
* Dev - Content Options - "Font" option added.
* Dev - Shortcodes - `[prop]` - Aliases added for all properties. Now it's possible to use e.g., `[order_number]` instead of `[prop name="order_number"]`, etc. `[prop]` shortcodes replaced with aliases in doc settings default values.
* Dev - Shortcodes - `[prop]` - `order_tax_totals` option added.
* Dev - Shortcodes - `[prop]` - `order_subtotal_incl_tax` option added.
* Dev - Shortcodes - `[prop]` - `order_total_excl_shipping` option added.
* Dev - Shortcodes - `[prop]` - `item_single_incl_tax` option added.
* Dev - Shortcodes - `[prop]` - `doc_formatted_date` - `datetime_format` attribute added (defaults to `Y-m-d`).
* Dev - Shortcodes - `[prop]` - `order_formatted_date_created` - `datetime_format` attribute added (defaults to `Y-m-d`).
* Dev - Shortcodes - `[prop]` - `find` and `replace` attributes added.
* Dev - Shortcodes - `[current_time]` shortcode added.
* Dev - Shortcodes - `[checkbox]` shortcode added.
* Dev - Code refactoring.
* WC tested up to: 5.5.
* Tested up to: 5.8.

= 1.3.0 - 26/06/2021 =
* Fix - Shortcodes - `[prop]` - `order_func`, `item_func`, `item_product_func` - Now properly checking if function is callable.
* Fix - Spelling error fixed in "number to words" function for the Lithuanian language.
* Dev - Doc - "My account" option added.
* Dev - Admin - Orders list - Showing created documents for cancelled orders now.
* Dev - Admin - Settings restyled.
* Dev - Localization - `load_plugin_textdomain()` moved to the `init` action.
* Dev - Code refactoring.
* WC tested up to: 5.4.
* Tested up to: 5.7.

= 1.2.2 - 04/08/2020 =
* Dev - Adding order notes on doc creation and removal now. `alg_wc_pdf_invoicing_add_order_notes` filter added (defaults to `true`).
* WC tested up to: 4.3.

= 1.2.1 - 19/06/2020 =
* Dev - Doc - "Margin" options added.
* Dev - Shortcodes - `[each_item]` - Optional `type` attribute added (defaults to `line_item`).
* Dev - Shortcodes - `[prop]` - `order_total_items_count` - Optional `type` attribute added (defaults to `line_item`).
* Dev - Shortcodes - `[prop]` - Shortcodes are now processed in optional `add` and `multiply` attributes.
* WC tested up to: 4.2.
* Description updated in readme.txt

= 1.2.0 - 03/04/2020 =
* Fix - Shortcodes - Orders - Bulk actions (e.g., email attachments) fixed.
* Dev - Shortcodes - `[prop]` - `item_total_tax_percent` - Not applying `round()` anymore.
* Dev - Shortcodes - `[prop]` - `order_discount` option added.
* Dev - Shortcodes - `[prop]` - `order_discount_incl_tax` option added.
* Dev - Shortcodes - `[prop]` - `order_discount_tax` option added.
* Dev - Shortcodes - `[prop]` - `order_discount_percent` option added.
* Dev - Shortcodes - `[prop]` - `item_subtotal` option added.
* Dev - Shortcodes - `[prop]` - `item_subtotal_tax` option added.
* Dev - Shortcodes - `[prop]` - `item_discount` option added.
* Dev - Shortcodes - `[prop]` - `item_discount_incl_tax` option added.
* Dev - Shortcodes - `[prop]` - `item_discount_tax` option added.
* Dev - Shortcodes - `[prop]` - `item_discount_percent` option added.
* Dev - Shortcodes - `alg_wc_pdf_invoicing_return_prop` filter added.
* Dev - Admin settings descriptions updated.
* Tested up to: 5.4.
* WC tested up to: 4.0.

= 1.1.1 - 12/02/2020 =
* Fix - Emails - Possible "Too few arguments ..." error fixed.

= 1.1.0 - 04/02/2020 =
* Dev - Doc - "Enable header" option added.
* Dev - Doc - "Header font size" option added.
* Dev - Doc - "Enable footer" option added.
* Dev - Doc - "Footer text" option added.
* Dev - Doc - "Footer text alignment" option added.
* Dev - Doc - "Footer font size" option added.
* Dev - Doc - "Font size" option added.
* Dev - Admin settings restyled (divided into subsections).
* Dev - `Alg_WC_PDF_Invoicing_TCPDF` class added.
* Dev - Shortcodes - `[prop]` - `item_total_tax_percent` option added.
* Dev - Shortcodes - `[prop]` - `item_total_incl_tax` option added.
* Dev - Shortcodes - `[prop]` - `item_total_tax` option added.
* Dev - Shortcodes - `[prop]` - `order_subtotal` option added.
* Dev - Shortcodes - `[prop]` - `order_billing_first_name` option added.
* WC tested up to: 3.9.

= 1.0.2 - 21/01/2020 =
* Dev - Shortcodes - `before` and `after` attributes are now displayed for non-empty values only.

= 1.0.1 - 16/01/2020 =
* Fix - Shortcodes - `item_product` shortcodes fixed.
* Dev - Shortcodes - `[prop]` - `item_product_id` option added.
* Dev - Shortcodes - `[prop]` - `item_product_taxonomy` option added.

= 1.0.0 - 14/01/2020 =
* Initial Release.

== Upgrade Notice ==

= 1.0.0 =
This is the first release of the plugin.
