<?php
/**
 * PDF Invoicing for WooCommerce - Shortcodes Class
 *
 * @version 2.5.0
 * @since   1.0.0
 *
 * @author WPFactory
 *
 * @package WPFactory\WC_PDF_Invoicing\Shortcodes
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_PDF_Invoicing_Shortcodes' ) ) :

	/**
	 * Alg_WC_PDF_Invoicing_Shortcodes class.
	 *
	 * @version 2.5.0
	 * @since   1.0.0
	 */
	class Alg_WC_PDF_Invoicing_Shortcodes {

		/**
		 * Props.
		 *
		 * @version 2.1.0
		 * @since   2.1.0
		 *
		 * @var array
		 */
		public $props;

		/**
		 * Checkbox counter.
		 *
		 * @version 2.1.0
		 * @since   2.1.0
		 *
		 * @var int
		 */
		public $checkbox_counter;

		/**
		 * Shortcode prefix.
		 *
		 * @version 2.1.0
		 * @since   2.1.0
		 *
		 * @var string
		 */
		public $shortcode_prefix;

		/**
		 * Shortcodes.
		 *
		 * @version 2.4.0
		 * @since   2.4.0
		 *
		 * @var array
		 */
		public $shortcodes;

		/**
		 * Prop aliases.
		 *
		 * @version 2.4.0
		 * @since   2.4.0
		 *
		 * @var array
		 */
		public $prop_aliases;

		/**
		 * Doc object.
		 *
		 * @version 2.1.0
		 * @since   2.1.0
		 *
		 * @var object
		 */
		public $doc_obj;

		/**
		 * Order.
		 *
		 * @version 2.1.0
		 * @since   2.1.0
		 *
		 * @var WC_Order
		 */
		public $order;

		/**
		 * Order ID.
		 *
		 * @version 2.1.0
		 * @since   2.1.0
		 *
		 * @var int
		 */
		public $order_id;

		/**
		 * Order item.
		 *
		 * @version 2.1.0
		 * @since   2.1.0
		 *
		 * @var WC_Order_Item
		 */
		public $order_item;

		/**
		 * Order item number.
		 *
		 * @version 2.1.0
		 * @since   2.1.0
		 *
		 * @var int
		 */
		public $order_item_nr;

		/**
		 * Item product.
		 *
		 * @version 2.1.0
		 * @since   2.1.0
		 *
		 * @var WC_Product
		 */
		public $item_product;

		/**
		 * Order refund.
		 *
		 * @version 2.1.0
		 * @since   2.1.0
		 *
		 * @var WC_Order_Refund
		 */
		public $order_refund;

		/**
		 * Order refund number.
		 *
		 * @version 2.1.0
		 * @since   2.1.0
		 *
		 * @var int
		 */
		public $order_refund_nr;

		/**
		 * Constructor.
		 *
		 * @version 2.4.5
		 * @since   1.0.0
		 *
		 * @todo (feature) Add `[order_barcode_1d]` and `[order_barcode_2d]` shortcodes.
		 * @todo (feature) Add `[set_rtl]` shortcode?
		 * @todo (feature) Add `[reset_items]` shortcode?
		 * @todo (feature) Add `[sort_items]` shortcode?
		 * @todo (feature) Add `[group_items]` shortcode?
		 */
		public function __construct() {
			$this->props = array();

			$this->checkbox_counter = 0;

			$this->shortcode_prefix = get_option( 'alg_wc_pdf_invoicing_shortcode_prefix', '' );

			$this->shortcodes = array(
				'prop',
				'each_item',
				'each_refund',
				'if',
				'clear',
				'break',
				'paragraph',
				'line',
				'current_time',
				'checkbox',
				'page_break',
				'translate',
			);

			$this->prop_aliases = array(

				'doc_author_full_name',
				'doc_counter',
				'doc_formatted_date',
				'doc_nr',

				'order_billing_first_name',
				'order_billing_last_name',
				'order_checkout_payment_url',
				'order_details_email',
				'order_details_table',
				'order_discount',
				'order_discount_incl_tax',
				'order_discount_percent',
				'order_discount_tax',
				'order_formatted_billing_address',
				'order_formatted_date_created',
				'order_formatted_shipping_address',
				'order_func',
				'order_meta',
				'order_number',
				'order_payment_method_title',
				'order_shipping_method',
				'order_shipping_total',
				'order_shipping_total_excl_tax',
				'order_shipping_total_incl_tax',
				'order_status',
				'order_subtotal',
				'order_subtotal_incl_tax',
				'order_tax_totals',
				'order_total',
				'order_total_excl_shipping',
				'order_total_excl_tax',
				'order_total_excl_tax_excl_shipping',
				'order_total_in_words',
				'order_total_items_count',
				'order_total_items_qty',
				'order_total_refunded',
				'order_total_shipping_refunded',
				'order_total_tax',
				'order_total_tax_percent',
				'order_total_tax_refunded',

				'item_discount',
				'item_discount_incl_tax',
				'item_discount_percent',
				'item_discount_tax',
				'item_func',
				'item_meta',
				'item_name',
				'item_nr',
				'item_product_func',
				'item_product_id',
				'item_product_image',
				'item_product_meta',
				'item_product_sku',
				'item_product_taxonomy',
				'item_qty',
				'item_single',
				'item_single_incl_tax',
				'item_subtotal',
				'item_subtotal_tax',
				'item_total',
				'item_total_incl_tax',
				'item_total_tax',
				'item_total_tax_percent',

				'refund_nr',
				'refund_reason',
				'refund_total',

			);

			foreach ( $this->shortcodes as $shortcode ) {
				add_shortcode( $this->shortcode_prefix . $shortcode, array( $this, 'shortcode_' . $shortcode ) );
			}

			foreach ( $this->prop_aliases as $prop_alias ) {
				add_shortcode( $this->shortcode_prefix . $prop_alias, array( $this, 'shortcode_prop_alias' ) );
			}
		}

		/**
		 * Shortcode prop alias.
		 *
		 * @version 1.4.0
		 * @since   1.4.0
		 *
		 * @param array  $atts          Attributes.
		 * @param string $content       Content.
		 * @param string $shortcode_tag Shortcode tag.
		 *
		 * @return string Shortcode output.
		 */
		public function shortcode_prop_alias( $atts, $content, $shortcode_tag ) {
			if ( '' === $atts ) {
				$atts = array();
			}
			$atts['name'] = substr( $shortcode_tag, strlen( $this->shortcode_prefix ) );
			return $this->shortcode_prop( $atts );
		}

		/**
		 * Set doc obj.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @param object $obj Document object.
		 *
		 * @return void
		 */
		public function set_doc_obj( $obj ) {
			$this->doc_obj = $obj;
		}

		/**
		 * Set prop.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @param string $prop  Property name.
		 * @param mixed  $value Property value.
		 *
		 * @return void
		 */
		public function set_prop( $prop, $value ) {
			$this->props[ $prop ] = $value;
		}

		/**
		 * Set order data.
		 *
		 * @version 2.5.0
		 * @since   2.4.3
		 *
		 * @return bool True on success, false on failure.
		 */
		public function set_order_data() {
			if ( ! isset( $this->doc_obj->order_id ) ) {
				return false;
			}
			if (
				! isset( $this->order ) ||
				(int) $this->order_id !== (int) $this->doc_obj->order_id
			) {
				$order = wc_get_order( $this->doc_obj->order_id );
				if ( ! $order ) {
					return false;
				} else {
					// Success (set order data).
					$this->order    = $order;
					$this->order_id = $this->doc_obj->order_id;
					return true;
				}
			}
			// Success (order data already set).
			return true;
		}

		/**
		 * Get current language for translate.
		 *
		 * @version 2.5.0
		 * @since   2.4.3
		 *
		 * @return string|false Current language code or false if not found.
		 *
		 * @todo (v2.4.3) WPML and Polylang: Test?
		 * @todo (v2.4.3) WPML: `wpml_language` or `wpml_languages`?
		 * @todo (v2.4.3) Use `get_locale()`?
		 * @todo (v2.4.3) Use `$_POST['language']`?
		 *
		 * @see https://wpml.org/forums/topic/meta-key-when-creating-an-order-in-woocommerce/
		 * @see https://translatepress.com/docs/developers/
		 */
		public function get_current_language_for_translate() {
			// Order.
			if ( $this->set_order_data() ) {

				// WPML (`wpml_languages` meta).
				$languages = $this->order->get_meta( 'wpml_languages' );
				if (
					$languages &&
					! empty( $languages ) &&
					( $languages = maybe_unserialize( $languages ) ) && // phpcs:ignore Generic.CodeAnalysis.AssignmentInCondition.Found, Squiz.PHP.DisallowMultipleAssignments.FoundInControlStructure
					! empty( $languages[0] )
				) {
					return $languages[0];
				}

				// WPML (`wpml_language` meta).
				$lang = $this->order->get_meta( 'wpml_language' );
				if ( $lang ) {
					return $lang;
				}

				// Polylang.
				$terms = get_the_terms( $this->order_id, 'language' );
				if (
					$terms &&
					! is_wp_error( $terms )
				) {
					foreach ( $terms as $term ) {
						if ( ! empty( $term->slug ) ) {
							return $term->slug;
						}
					}
				}

				// TranslatePress.
				$meta_value = $this->order->get_meta( 'trp_language' );
				if ( '' !== $meta_value ) {
					return strtolower( $meta_value );
				}
			}

			// WPML, Polylang.
			if ( defined( 'ICL_LANGUAGE_CODE' ) ) {
				return strtolower( ICL_LANGUAGE_CODE );
			}

			// TranslatePress.
			// phpcs:disable WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
			global $TRP_LANGUAGE;
			if ( $TRP_LANGUAGE ) {
				return strtolower( $TRP_LANGUAGE );
			}
			// phpcs:enable WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase

			return false;
		}

		/**
		 * Shortcode translate.
		 *
		 * @version 2.5.0
		 * @since   2.4.3
		 *
		 * @param array  $atts    Attributes.
		 * @param string $content Content.
		 *
		 * @return string
		 */
		public function shortcode_translate( $atts, $content = '' ) {
			$current_language = $this->get_current_language_for_translate();

			// E.g.: `[translate lang="EN,DE" lang_text="Text for EN & DE" not_lang_text="Text for other languages"]`.
			if (
				isset( $atts['lang_text'] ) &&
				isset( $atts['not_lang_text'] ) &&
				! empty( $atts['lang'] )
			) {
				return (
					(
						false === $current_language ||
						! in_array(
							$current_language,
							array_map( 'trim', explode( ',', strtolower( $atts['lang'] ) ) ),
							true
						)
					) ?
					wp_kses_post( $atts['not_lang_text'] ) :
					wp_kses_post( $atts['lang_text'] )
				);
			}

			// E.g.: `[translate lang="EN,DE"]Text for EN & DE[/translate][translate not_lang="EN,DE"]Text for other languages[/translate]`.
			return (
				(
					(
						! empty( $atts['lang'] ) &&
						(
							false === $current_language ||
							! in_array(
								$current_language,
								array_map( 'trim', explode( ',', strtolower( $atts['lang'] ) ) ),
								true
							)
						)
					) ||
					(
						! empty( $atts['not_lang'] ) &&
						(
							false !== $current_language &&
							in_array(
								$current_language,
								array_map( 'trim', explode( ',', strtolower( $atts['not_lang'] ) ) ),
								true
							)
						)
					)
				) ?
				'' :
				wp_kses_post( do_shortcode( $content ) )
			);
		}

		/**
		 * Shortcode page break.
		 *
		 * @version 2.5.0
		 * @since   1.6.0
		 *
		 * @param array $atts Shortcode attributes.
		 *
		 * @return string HTML for page break.
		 *
		 * @see https://stackoverflow.com/questions/1605860/manual-page-break-in-tcpdf
		 */
		public function shortcode_page_break( $atts ) {
			return (
				isset( $atts['mode'] ) && 'tcpdf' === $atts['mode'] ?
				'<tcpdf method="AddPage" />' :
				'<br pagebreak="true" />'
			);
		}

		/**
		 * Shortcode current time.
		 *
		 * @version 2.5.0
		 * @since   1.4.0
		 *
		 * @param array $atts Shortcode attributes.
		 *
		 * @return string Current time.
		 */
		public function shortcode_current_time( $atts ) {
			return date_i18n( ( $atts['datetime_format'] ?? 'Y-m-d H:i:s' ) );
		}

		/**
		 * Shortcode checkbox.
		 *
		 * @version 2.5.0
		 * @since   1.4.0
		 *
		 * @todo (feature) `checked` vs `unchecked`.
		 */
		public function shortcode_checkbox() {
			++$this->checkbox_counter;
			return '<input type="checkbox" name="checkbox_' . esc_attr( $this->checkbox_counter ) . '" value="1">';
		}

		/**
		 * Shortcode clear.
		 *
		 * @version 2.5.0
		 * @since   1.0.0
		 */
		public function shortcode_clear() {
			return '<p></p>';
		}

		/**
		 * Shortcode break.
		 *
		 * @version 2.5.0
		 * @since   2.4.0
		 */
		public function shortcode_break() {
			return '<br>';
		}

		/**
		 * Shortcode paragraph.
		 *
		 * @version 2.5.0
		 * @since   2.4.0
		 *
		 * @param array  $atts    Shortcode attributes.
		 * @param string $content Shortcode content.
		 *
		 * @return string HTML paragraph.
		 */
		public function shortcode_paragraph( $atts, $content = '' ) {
			return wp_kses_post( '<p>' . do_shortcode( $content ) . '</p>' );
		}

		/**
		 * Shortcode line.
		 *
		 * @version 2.5.0
		 * @since   2.4.5
		 */
		public function shortcode_line() {
			$style = (
				! empty( $this->doc_obj ) ?
				' style="color:' . esc_attr( $this->doc_obj->get_doc_option( 'line_color' ) ) . ';"' :
				''
			);
			return "<hr{$style}><br><br>";
		}

		/**
		 * Shortcode if.
		 *
		 * @version 2.5.0
		 * @since   1.0.0
		 *
		 * @param array  $atts    Shortcode attributes.
		 * @param string $content Shortcode content.
		 *
		 * @return string Shortcode output.
		 *
		 * @todo (dev) `$this->do_atts_shortcode()`.
		 */
		public function shortcode_if( $atts, $content = '' ) {
			if (
				! isset( $atts['value1'], $atts['operator'], $atts['value2'] ) ||
				'' === $content
			) {
				return '';
			}

			$value1 = do_shortcode( str_replace( array( '{', '}' ), array( '[', ']' ), $atts['value1'] ) );
			$value2 = do_shortcode( str_replace( array( '{', '}' ), array( '[', ']' ), $atts['value2'] ) );

			switch ( $atts['operator'] ) {
				case 'equal':
					return ( $value1 == $value2 ? wp_kses_post( do_shortcode( $content ) ) : '' ); // phpcs:ignore Universal.Operators.StrictComparisons.LooseEqual
				case 'not_equal':
					return ( $value1 != $value2 ? wp_kses_post( do_shortcode( $content ) ) : '' ); // phpcs:ignore Universal.Operators.StrictComparisons.LooseNotEqual
				case 'less':
					return ( $value1 < $value2 ? wp_kses_post( do_shortcode( $content ) ) : '' );
				case 'less_or_equal':
					return ( $value1 <= $value2 ? wp_kses_post( do_shortcode( $content ) ) : '' );
				case 'greater':
					return ( $value1 > $value2 ? wp_kses_post( do_shortcode( $content ) ) : '' );
				case 'greater_or_equal':
					return ( $value1 >= $value2 ? wp_kses_post( do_shortcode( $content ) ) : '' );
			}

			return '';
		}

		/**
		 * Shortcode each item.
		 *
		 * @version 2.5.0
		 * @since   1.0.0
		 *
		 * @see https://github.com/woocommerce/woocommerce/blob/5.5.2/includes/abstracts/abstract-wc-order.php#L752
		 *
		 * @param array  $atts    Shortcode attributes (`$atts['type']` is type(s) of line items to get. Can be a comma separated list. Possible values: `line_item`, `tax`, `shipping`, `fee`, `coupon`. Default: `line_item`.).
		 * @param string $content Shortcode content.
		 *
		 * @todo (dev) Rethink naming, e.g., `[each name="items"]`.
		 */
		public function shortcode_each_item( $atts, $content = '' ) {
			if ( '' === $content ) {
				return '';
			}

			$type                = ( isset( $atts['type'] ) ? explode( ',', $atts['type'] ) : 'line_item' );
			$order               = wc_get_order( $this->doc_obj->order_id );
			$output              = '';
			$this->order_item_nr = 0;
			$this->order_item    = false;
			$this->item_product  = false;

			if ( ! $order ) {
				return '';
			}

			$items = apply_filters( 'alg_wc_pdf_invoicing_shortcode_each_item_items', $order->get_items( $type ), $order, $this );

			foreach ( $items as $item ) {

				// Filter products.
				if (
					isset( $atts['product_id'] ) &&
					isset( $item['product_id'] ) &&
					! in_array(
						(int) ( ! empty( $item['variation_id'] ) ? $item['variation_id'] : $item['product_id'] ),
						array_map( 'intval', array_map( 'trim', explode( ',', $atts['product_id'] ) ) ),
						true
					)
				) {
					continue;
				}

				// Set props.
				++$this->order_item_nr;
				$this->order_item   = $item;
				$this->item_product = ( is_a( $item, 'WC_Order_Item_Product' ) ? $item->get_product() : false );

				// Process item.
				$output  = apply_filters( 'alg_wc_pdf_invoicing_shortcode_each_item_before_item', $output, $item, $order, $this );
				$output .= do_shortcode( $content );
				$output  = apply_filters( 'alg_wc_pdf_invoicing_shortcode_each_item_after_item', $output, $item, $order, $this );

			}

			// Unset props.
			unset( $this->order_item_nr );
			unset( $this->order_item );
			unset( $this->item_product );

			// Result.
			return (
				'' !== $output ?
				wp_kses_post(
					( $atts['before'] ?? '' ) .
					$output .
					( $atts['after'] ?? '' )
				) :
				''
			);
		}

		/**
		 * Shortcode each refund.
		 *
		 * @version 2.5.0
		 * @since   1.5.0
		 *
		 * @param array  $atts    Shortcode attributes.
		 * @param string $content Shortcode content.
		 *
		 * @return string
		 *
		 * @todo (dev) [!] "Full refund" vs "partial refund" (there are no `items` in "full refund").
		 * @todo (feature) "Partial refund" trigger.
		 */
		public function shortcode_each_refund( $atts, $content = '' ) {
			if ( '' === $content ) {
				return '';
			}

			$order                 = wc_get_order( $this->doc_obj->order_id );
			$output                = '';
			$this->order_refund_nr = 0;
			$this->order_refund    = false;
			$order_id              = $this->doc_obj->order_id;

			foreach ( $order->get_refunds() as $refund ) {
				++$this->order_refund_nr;
				$this->order_refund      = $refund;
				$this->doc_obj->order_id = $refund->get_id();
				$output                 .= do_shortcode( $content );
			}

			unset( $this->order_refund_nr );
			unset( $this->order_refund );
			$this->doc_obj->order_id = $order_id;

			return (
				'' !== $output ?
				wp_kses_post(
					( $atts['before'] ?? '' ) .
					$output .
					( $atts['after'] ?? '' )
				) :
				''
			);
		}

		/**
		 * Return prop.
		 *
		 * @version 2.5.0
		 * @since   1.0.0
		 *
		 * @param mixed $value The value.
		 * @param array $atts  The attributes.
		 *
		 * @return mixed The processed value.
		 *
		 * @todo (dev) Math: `multiply` and `divide`: apply `floatval()`?
		 * @todo (dev) `on_empty`; `lang` && `not_lang`; `order_billing_country` && `not_order_billing_country` etc. OR maybe all that can be done via `[if]`?
		 */
		public function return_prop( $value, $atts ) {
			// Math.
			if ( is_numeric( $value ) ) {
				if ( ! empty( $atts['add'] ) ) {
					$value += floatval(
						do_shortcode(
							str_replace(
								array( '{', '}' ),
								array( '[', ']' ),
								$atts['add']
							)
						)
					);
				}
				if ( ! empty( $atts['subtract'] ) ) {
					$value -= floatval(
						do_shortcode(
							str_replace(
								array( '{', '}' ),
								array( '[', ']' ),
								$atts['subtract']
							)
						)
					);
				}
				if ( ! empty( $atts['multiply'] ) ) {
					$value *= do_shortcode(
						str_replace(
							array( '{', '}' ),
							array( '[', ']' ),
							$atts['multiply']
						)
					);
				}
				if ( ! empty( $atts['divide'] ) ) {
					$_value = do_shortcode(
						str_replace(
							array( '{', '}' ),
							array( '[', ']' ),
							$atts['divide']
						)
					);
					if ( 0.0 !== (float) $_value ) {
						$value /= $_value;
					}
				}
			}

			// Format.
			if ( isset( $atts['format'] ) ) {
				switch ( $atts['format'] ) {
					case 'price':
						$value = wc_price(
							$value,
							(
								isset( $this->order ) ?
								array( 'currency' => $this->order->get_currency() ) :
								array()
							)
						);
						break;
					default:
						$value = sprintf( $atts['format'], $value );
				}
			}

			// Find/replace.
			if ( isset( $atts['find'], $atts['replace'] ) ) {
				$value = str_replace( $atts['find'], $atts['replace'], $value );
			}

			// Multiple find/replace.
			if ( isset( $atts['multiple_find'], $atts['multiple_replace'] ) ) {
				$value = str_replace(
					explode( ',', $atts['multiple_find'] ),
					explode( ',', $atts['multiple_replace'] ),
					$value
				);
			}

			// Filter.
			$value = apply_filters( 'alg_wc_pdf_invoicing_return_prop', $value, $atts, $this );

			// Before/after & Final result.
			return (
				'' !== $value ?
				wp_kses_post(
					( $atts['before'] ?? '' ) .
					$value .
					( $atts['after'] ?? '' )
				) :
				''
			);
		}

		/**
		 * Is prop type.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @param string $type      The type.
		 * @param string $prop_name The property name.
		 *
		 * @return bool
		 */
		public function is_prop_type( $type, $prop_name ) {
			$type   = $type . '_';
			$length = strlen( $type );
			return ( substr( $prop_name, 0, $length ) === $type );
		}

		/**
		 * Shortcode prop.
		 *
		 * @version 2.5.0
		 * @since   1.0.0
		 *
		 * @param array $atts The shortcode attributes.
		 *
		 * @return string The shortcode output.
		 *
		 * @todo (dev) `item_` props for coupons: check if it's callable!
		 * @todo (dev) `item_` props for coupons: `get_discount()` and `get_discount_tax()`!
		 * @todo (dev) Rename `item_nr` to `order_item_nr`, etc.
		 * @todo (dev) Data from another doc (e.g., display invoice nr in packing slip).
		 * @todo (dev) `ucfirst()` for `order_total_in_words` (including Unicode).
		 * @todo (dev) Separate shortcodes (e.g., `[doc]`, `[order]`, `[item]` etc.).
		 */
		public function shortcode_prop( $atts ) {
			if (
				! isset( $atts['name'] ) ||
				(
					isset( $this->props[ $atts['name'] ] ) &&
					false === $this->props[ $atts['name'] ]
				) ||
				! isset( $this->doc_obj )
			) {
				return '';
			}

			// Prepare & check data.
			if ( $this->is_prop_type( 'order', $atts['name'] ) ) {
				if ( ! $this->set_order_data() ) {
					return '';
				}
			} elseif ( $this->is_prop_type( 'item_product', $atts['name'] ) ) {
				if ( empty( $this->item_product ) ) {
					return '';
				}
			} elseif ( $this->is_prop_type( 'item', $atts['name'] ) ) {
				if ( empty( $this->order_item ) || empty( $this->order_item_nr ) ) {
					return '';
				}
			}

			// Main switch.
			switch ( $atts['name'] ) {

				/**
				 * Document.
				 */
				case 'doc_counter':
					return $this->return_prop( $this->doc_obj->get_counter(), $atts );

				case 'doc_nr':
					return $this->return_prop( $this->doc_obj->get_number(), $atts );

				case 'doc_formatted_date':
					return $this->return_prop(
						$this->doc_obj->get_formatted_date(
							( $atts['datetime_format'] ?? 'Y-m-d' ),
							( $atts['add_days'] ?? 0 )
						),
						$atts
					);

				case 'doc_author_full_name':
					return $this->return_prop( $this->doc_obj->get_author_full_name(), $atts );

				/**
				 * Order.
				 */
				case 'order_number':
					return $this->return_prop( $this->order->get_order_number(), $atts );

				case 'order_formatted_date_created':
					return $this->return_prop(
						date_i18n(
							( $atts['datetime_format'] ?? 'Y-m-d' ),
							strtotime( $this->order->get_date_created() )
						),
						$atts
					);

				case 'order_status':
					return $this->return_prop( $this->order->get_status(), $atts );

				case 'order_subtotal':
					return $this->return_prop( $this->order->get_subtotal(), $atts );

				case 'order_subtotal_incl_tax':
					return $this->return_prop(
						$this->order->get_subtotal_to_display(
							( isset( $atts['compound'] ) && 'yes' === $atts['compound'] ),
							'incl'
						),
						$atts
					);

				case 'order_total':
					return $this->return_prop( $this->order->get_total(), $atts );

				case 'order_total_excl_shipping':
					return $this->return_prop( $this->order->get_total() - $this->order->get_shipping_total(), $atts );

				case 'order_total_in_words':
					$whole_part   = alg_wc_pdf_invoicing_number_to_words(
						intval( $this->order->get_total() ),
						( isset( $atts['lang'] ) ? strtoupper( $atts['lang'] ) : 'EN' )
					);
					$decimal_part = round(
						( $this->order->get_total() - intval( $this->order->get_total() ) ) * 100,
						2
					);
					return $this->return_prop(
						(
							$whole_part . ' ' . get_woocommerce_currency_symbol( $this->order->get_currency() ) . ' ' .
							$decimal_part . ' &cent;'
						),
						$atts
					);

				case 'order_total_tax':
					return $this->return_prop( $this->order->get_total_tax(), $atts );

				case 'order_total_tax_percent':
					$total      = 0;
					$total_tax  = 0;
					$item_types = array( 'line_item', 'shipping', 'fee' );
					foreach ( $item_types as $item_type ) {
						foreach ( $this->order->get_items( $item_type ) as $item ) {
							if ( 0.0 !== (float) $item->get_total_tax() ) {
								$total     += $item->get_total();
								$total_tax += $item->get_total_tax();
							}
						}
					}
					$result = (
						0.0 !== (float) $total ?
						round(
							( $total_tax / $total * 100 ),
							( $atts['precision'] ?? 1 )
						) :
						0
					);
					return $this->return_prop( $result, $atts );

				case 'order_tax_totals':
					$value      = array();
					$tax_totals = $this->order->get_tax_totals();
					foreach ( $tax_totals as $tax_row ) {
						$value[] = $tax_row->label . ': ' . $tax_row->formatted_amount;
					}
					$value = implode( '<br>', $value );
					return $this->return_prop( $value, $atts );

				case 'order_total_excl_tax':
					return $this->return_prop( $this->order->get_total() - $this->order->get_total_tax(), $atts );

				case 'order_total_excl_tax_excl_shipping':
					return $this->return_prop(
						$this->order->get_total() - $this->order->get_total_tax() - $this->order->get_shipping_total(),
						$atts
					);

				case 'order_discount':
					return $this->return_prop( $this->order->get_total_discount(), $atts );

				case 'order_discount_incl_tax':
					return $this->return_prop( $this->order->get_total_discount( false ), $atts );

				case 'order_discount_tax':
					return $this->return_prop( $this->order->get_discount_tax(), $atts );

				case 'order_discount_percent':
					$subtotal = $this->order->get_subtotal();
					$result   = (
						0.0 !== (float) $subtotal ?
						( $this->order->get_discount_total() / $subtotal * 100 ) :
						0
					);
					return $this->return_prop( $result, $atts );

				case 'order_shipping_total':
				case 'order_shipping_total_excl_tax':
					return $this->return_prop( $this->order->get_shipping_total(), $atts );

				case 'order_shipping_total_incl_tax':
					return $this->return_prop( $this->order->get_shipping_total() + $this->order->get_shipping_tax(), $atts );

				case 'order_shipping_method':
					return $this->return_prop( $this->order->get_shipping_method(), $atts );

				case 'order_payment_method_title':
					return $this->return_prop( $this->order->get_payment_method_title(), $atts );

				case 'order_checkout_payment_url':
					return $this->return_prop( $this->order->get_checkout_payment_url(), $atts );

				case 'order_formatted_billing_address':
					return $this->return_prop( $this->order->get_formatted_billing_address(), $atts );

				case 'order_formatted_shipping_address':
					return $this->return_prop( $this->order->get_formatted_shipping_address(), $atts );

				case 'order_billing_first_name':
					return $this->return_prop( $this->order->get_billing_first_name(), $atts );

				case 'order_billing_last_name':
					return $this->return_prop( $this->order->get_billing_last_name(), $atts );

				case 'order_total_items_count':
					$type = ( isset( $atts['type'] ) ? explode( ',', $atts['type'] ) : 'line_item' );
					return $this->return_prop( count( $this->order->get_items( $type ) ), $atts );

				case 'order_total_items_qty':
					$type   = ( isset( $atts['type'] ) ? explode( ',', $atts['type'] ) : 'line_item' );
					$result = 0;
					foreach ( $this->order->get_items( $type ) as $item ) {
						if ( ! empty( $item['quantity'] ) ) {
							$result += $item['quantity'];
						}
					}
					return $this->return_prop( $result, $atts );

				case 'order_meta':
					return (
						isset( $atts['key'] ) ?
						$this->return_prop( $this->order->get_meta( $atts['key'] ), $atts ) :
						''
					);

				case 'order_details_email':
					ob_start();
					wc_get_template(
						'emails/email-order-details.php',
						array(
							'order'         => $this->order,
							'sent_to_admin' => false,
							'plain_text'    => false,
							'email'         => '',
						)
					);
					return $this->return_prop( ob_get_clean(), $atts );

				case 'order_details_table':
					ob_start();
					woocommerce_order_details_table( $this->order_id );
					return $this->return_prop( ob_get_clean(), $atts );

				case 'order_func':
					if ( ! isset( $atts['func'] ) ) {
						return '';
					}
					$func = $atts['func'];
					return (
						(
							is_callable( array( $this->order, $func ) ) &&
							0 === strpos( $func, 'get_' )
						) ?
						$this->return_prop( $this->order->{$func}(), $atts ) :
						''
					);

				case 'order_total_refunded':
					return $this->return_prop( $this->order->get_total_refunded(), $atts );

				case 'order_total_tax_refunded':
					return $this->return_prop( $this->order->get_total_tax_refunded(), $atts );

				case 'order_total_shipping_refunded':
					return $this->return_prop( $this->order->get_total_shipping_refunded(), $atts );

				/**
				 * Order item.
				 */
				case 'item_nr':
					return $this->return_prop( $this->order_item_nr, $atts );

				case 'item_name':
					return $this->return_prop( $this->order_item->get_name(), $atts );

				case 'item_qty':
					return $this->return_prop( $this->order_item->get_quantity(), $atts );

				case 'item_total':
					return $this->return_prop( $this->order_item->get_total(), $atts );

				case 'item_total_tax':
					return $this->return_prop( $this->order_item->get_total_tax(), $atts );

				case 'item_total_incl_tax':
					return $this->return_prop( $this->order_item->get_total() + $this->order_item->get_total_tax(), $atts );

				case 'item_total_tax_percent':
					$total  = $this->order_item->get_total();
					$result = (
						0.0 !== (float) $total ?
						( $this->order_item->get_total_tax() / $total * 100 ) :
						0
					);
					return $this->return_prop( $result, $atts );

				case 'item_single':
					$qty = $this->order_item->get_quantity();
					return ( 0.0 !== (float) $qty ? $this->return_prop( $this->order_item->get_total() / $qty, $atts ) : 0 );

				case 'item_single_incl_tax':
					$qty = $this->order_item->get_quantity();
					return (
						0.0 !== (float) $qty ?
						$this->return_prop(
							( $this->order_item->get_total() + $this->order_item->get_total_tax() ) / $qty,
							$atts
						) :
						0
					);

				case 'item_subtotal':
					return $this->return_prop( $this->order_item->get_subtotal(), $atts );

				case 'item_subtotal_tax':
					return $this->return_prop( $this->order_item->get_subtotal_tax(), $atts );

				case 'item_discount':
					return $this->return_prop( $this->order_item->get_subtotal() - $this->order_item->get_total(), $atts );

				case 'item_discount_incl_tax':
					return $this->return_prop(
						(
							( $this->order_item->get_subtotal() - $this->order_item->get_total() ) +
							( $this->order_item->get_subtotal_tax() - $this->order_item->get_total_tax() )
						),
						$atts
					);

				case 'item_discount_tax':
					return $this->return_prop(
						$this->order_item->get_subtotal_tax() - $this->order_item->get_total_tax(),
						$atts
					);

				case 'item_discount_percent':
					$subtotal = $this->order_item->get_subtotal();
					$result   = (
						0.0 !== (float) $subtotal ?
						( ( $this->order_item->get_subtotal() - $this->order_item->get_total() ) / $subtotal * 100 ) :
						0
					);
					return $this->return_prop( $result, $atts );

				case 'item_meta':
					return (
						isset( $atts['key'] ) ?
						$this->return_prop( $this->order_item->get_meta( $atts['key'], true ), $atts ) :
						''
					);

				case 'item_func':
					if ( ! isset( $atts['func'] ) ) {
						return '';
					}
					$func = $atts['func'];
					return (
						(
							is_callable( array( $this->order_item, $func ) ) &&
							0 === strpos( $func, 'get_' )
						) ?
						$this->return_prop( $this->order_item->{$func}(), $atts ) :
						''
					);

				/**
				 * Order item product.
				 */
				case 'item_product_id':
					return $this->return_prop( $this->item_product->get_id(), $atts );

				case 'item_product_sku':
					return $this->return_prop( $this->item_product->get_sku(), $atts );

				case 'item_product_image':
					$size   = ( isset( $atts['size'] ) ? $atts['size'] : 'woocommerce_thumbnail' );
					$width  = ( isset( $atts['width'] ) ? $atts['width'] : 30 );
					$height = ( isset( $atts['height'] ) ? $atts['height'] : 30 );
					$img_id = $this->item_product->get_image_id();
					if (
						$img_id &&
						( $img_url = wp_get_attachment_image_src( $img_id, $size ) ) && // phpcs:ignore Generic.CodeAnalysis.AssignmentInCondition.Found, Squiz.PHP.DisallowMultipleAssignments.FoundInControlStructure
						isset( $img_url[0] )
					) {
						$img_url = $img_url[0];
					}
					if ( ! $img_url ) {
						$img_url = wc_placeholder_img_src( $size );
					}
					$image = '<img src="' . $img_url . '" width="' . $width . '" height="' . $height . '">';
					return $this->return_prop( $image, $atts );

				case 'item_product_taxonomy':
					if ( ! isset( $atts['taxonomy'] ) ) {
						return '';
					}
					$terms = get_the_terms( $this->item_product->get_id(), $atts['taxonomy'] );
					if ( ! $terms || is_wp_error( $terms ) ) {
						return '';
					}
					$terms = implode( ', ', wp_list_pluck( $terms, 'name' ) );
					return $this->return_prop( $terms, $atts );

				case 'item_product_meta':
					$product_id = (
						(
							isset( $atts['use_parent'] ) &&
							'yes' === $atts['use_parent'] &&
							( $parent_id = $this->item_product->get_parent_id() ) // phpcs:ignore WordPress.CodeAnalysis.AssignmentInTernaryCondition.FoundInTernaryCondition, Squiz.PHP.DisallowMultipleAssignments.Found
						) ?
						$parent_id :
						$this->item_product->get_id()
					);
					return (
						isset( $atts['key'] ) ?
						$this->return_prop( get_post_meta( $product_id, $atts['key'], true ), $atts ) :
						''
					);

				case 'item_product_func':
					if ( ! isset( $atts['func'] ) ) {
						return '';
					}
					$func = $atts['func'];
					return (
						(
							is_callable( array( $this->item_product, $func ) ) &&
							0 === strpos( $func, 'get_' )
						) ?
						$this->return_prop( $this->item_product->{$func}(), $atts ) :
						''
					);

				/**
				 * Order refund.
				 */
				case 'refund_nr':
					return $this->return_prop( $this->order_refund_nr, $atts );

				case 'refund_total':
					return $this->return_prop( $this->order_refund->get_total(), $atts );

				case 'refund_reason':
					return $this->return_prop( $this->order_refund->get_reason(), $atts );

			}
		}
	}

endif;

return new Alg_WC_PDF_Invoicing_Shortcodes();
