<?php
/**
 * PDF Invoicing for WooCommerce - Shortcodes Class
 *
 * @version 1.7.1
 * @since   1.0.0
 *
 * @author  Algoritmika Ltd
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_PDF_Invoicing_Shortcodes' ) ) :

class Alg_WC_PDF_Invoicing_Shortcodes {

	/**
	 * Constructor.
	 *
	 * @version 1.6.0
	 * @since   1.0.0
	 *
	 * @todo    [now] (feature) `[order_barcode_1d]` and `[order_barcode_2d]` shortcodes
	 * @todo    [now] (feature) `[set_rtl]` shortcode?
	 * @todo    [now] (feature) `[reset_items]` shortcode?
	 * @todo    [now] (feature) `[sort_items]` shortcode?
	 * @todo    [now] (feature) `[group_items]` shortcode?
	 */
	function __construct() {
		$this->props            = array();
		$this->checkbox_counter = 0;
		$this->shortcode_prefix = get_option( 'alg_wc_pdf_invoicing_shortcode_prefix', '' );
		$shortcodes             = array(
			'prop',
			'each_item',
			'each_refund',
			'if',
			'clear',
			'current_time',
			'checkbox',
			'page_break',
		);
		$prop_aliases           = array(
			'doc_counter',
			'doc_nr',
			'doc_formatted_date',
			'doc_author_full_name',
			'order_number',
			'order_formatted_date_created',
			'order_status',
			'order_subtotal',
			'order_subtotal_incl_tax',
			'order_total',
			'order_total_excl_shipping',
			'order_total_in_words',
			'order_total_tax',
			'order_tax_totals',
			'order_total_excl_tax',
			'order_total_excl_tax_excl_shipping',
			'order_discount',
			'order_discount_incl_tax',
			'order_discount_tax',
			'order_discount_percent',
			'order_shipping_total',
			'order_shipping_method',
			'order_payment_method_title',
			'order_formatted_billing_address',
			'order_formatted_shipping_address',
			'order_billing_first_name',
			'order_total_items_count',
			'order_total_items_qty',
			'order_meta',
			'order_details_email',
			'order_details_table',
			'order_func',
			'order_total_refunded',
			'order_total_tax_refunded',
			'order_total_shipping_refunded',
			'item_nr',
			'item_name',
			'item_qty',
			'item_total',
			'item_total_tax',
			'item_total_incl_tax',
			'item_total_tax_percent',
			'item_single',
			'item_single_incl_tax',
			'item_subtotal',
			'item_subtotal_tax',
			'item_discount',
			'item_discount_incl_tax',
			'item_discount_tax',
			'item_discount_percent',
			'item_meta',
			'item_func',
			'item_product_id',
			'item_product_sku',
			'item_product_image',
			'item_product_taxonomy',
			'item_product_meta',
			'item_product_func',
			'refund_nr',
			'refund_total',
			'refund_reason',
		);
		foreach ( $shortcodes as $shortcode ) {
			add_shortcode( $this->shortcode_prefix . $shortcode, array( $this, 'shortcode_' . $shortcode ) );
		}
		foreach ( $prop_aliases as $prop_alias ) {
			add_shortcode( $this->shortcode_prefix . $prop_alias, array( $this, 'shortcode_prop_alias' ) );
		}
	}

	/**
	 * shortcode_prop_alias.
	 *
	 * @version 1.4.0
	 * @since   1.4.0
	 */
	function shortcode_prop_alias( $atts, $content, $shortcode_tag ) {
		if ( '' === $atts ) {
			$atts = array();
		}
		$atts['name'] = substr( $shortcode_tag, strlen( $this->shortcode_prefix ) );
		return $this->shortcode_prop( $atts );
	}

	/**
	 * set_doc_obj.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function set_doc_obj( $obj ) {
		$this->doc_obj = $obj;
	}

	/**
	 * set_prop.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function set_prop( $prop, $value ) {
		$this->props[ $prop ] = $value;
	}

	/**
	 * shortcode_page_break.
	 *
	 * @version 1.6.0
	 * @since   1.6.0
	 *
	 * @see     https://stackoverflow.com/questions/1605860/manual-page-break-in-tcpdf
	 */
	function shortcode_page_break( $atts, $content = '' ) {
		return ( isset( $atts['mode'] ) && 'tcpdf' === $atts['mode'] ? '<tcpdf method="AddPage" />' : '<br pagebreak="true" />' );
	}

	/**
	 * shortcode_current_time.
	 *
	 * @version 1.4.0
	 * @since   1.4.0
	 */
	function shortcode_current_time( $atts, $content = '' ) {
		return date_i18n( ( isset( $atts['datetime_format'] ) ? $atts['datetime_format'] : 'Y-m-d H:i:s' ) );
	}

	/**
	 * shortcode_checkbox.
	 *
	 * @version 1.4.0
	 * @since   1.4.0
	 *
	 * @todo    [now] (feature) `checked` vs `unchecked`
	 */
	function shortcode_checkbox( $atts, $content = '' ) {
		$this->checkbox_counter++;
		return '<input type="checkbox" name="checkbox_' . $this->checkbox_counter . '" value="1">';
	}

	/**
	 * shortcode_clear.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function shortcode_clear( $atts, $content = '' ) {
		return '<p></p>';
	}

	/**
	 * shortcode_if.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 *
	 * @todo    [now] (dev) `$this->do_atts_shortcode()`
	 */
	function shortcode_if( $atts, $content = '' ) {
		if ( ! isset( $atts['value1'], $atts['operator'], $atts['value2'] ) || '' === $content ) {
			return '';
		}
		$value1 = do_shortcode( str_replace( array( '{', '}' ), array( '[', ']' ), $atts['value1'] ) );
		$value2 = do_shortcode( str_replace( array( '{', '}' ), array( '[', ']' ), $atts['value2'] ) );
		switch ( $atts['operator'] ) {
			case 'equal':
				return ( $value1 == $value2 ? do_shortcode( $content ) : '' );
			case 'not_equal':
				return ( $value1 != $value2 ? do_shortcode( $content ) : '' );
			case 'less':
				return ( $value1 <  $value2 ? do_shortcode( $content ) : '' );
			case 'less_or_equal':
				return ( $value1 <= $value2 ? do_shortcode( $content ) : '' );
			case 'greater':
				return ( $value1 >  $value2 ? do_shortcode( $content ) : '' );
			case 'greater_or_equal':
				return ( $value1 >= $value2 ? do_shortcode( $content ) : '' );
		}
		return '';
	}

	/**
	 * shortcode_each_item.
	 *
	 * @version 1.7.0
	 * @since   1.0.0
	 *
	 * @see     https://github.com/woocommerce/woocommerce/blob/5.5.2/includes/abstracts/abstract-wc-order.php#L752
	 *
	 * @param   string $atts['type'] Type(s) of line items to get. Can be a comma separated list. Possible values: `line_item`, `tax`, `shipping`, `fee`, `coupon`. Default: `line_item`.
	 *
	 * @todo    [maybe] (dev) rethink naming, e.g., `[each name="items"]`
	 */
	function shortcode_each_item( $atts, $content = '' ) {
		if ( '' === $content ) {
			return '';
		}

		$type   = ( isset( $atts['type'] ) ? explode( ',', $atts['type'] ) : 'line_item' );
		$order  = wc_get_order( $this->doc_obj->order_id );
		$output = '';
		$this->order_item_nr = 0;
		$this->order_item    = false;
		$this->item_product  = false;

		if ( ! $order ) {
			return '';
		}

		foreach ( $order->get_items( $type ) as $item_id => $item ) {

			// Filter products
			if (
				isset( $atts['product_id'] ) &&
				isset( $item['product_id'] ) &&
				! in_array(
					( ! empty( $item['variation_id'] ) ? $item['variation_id'] : $item['product_id'] ),
					array_map( 'trim', explode( ',', $atts['product_id'] ) )
				)
			) {
				continue;
			}

			// Process item
			$this->order_item_nr++;
			$this->order_item   = $item;
			$this->item_product = ( is_a( $item, 'WC_Order_Item_Product' ) ? $item->get_product() : false );
			$output .= do_shortcode( $content );

		}

		unset( $this->order_item_nr );
		unset( $this->order_item );
		unset( $this->item_product );

		return ( '' !== $output ? ( ( isset( $atts['before'] ) ? $atts['before'] : '' ) . $output . ( isset( $atts['after'] ) ? $atts['after'] : '' ) ) : '' );
	}

	/**
	 * shortcode_each_refund.
	 *
	 * @version 1.6.0
	 * @since   1.5.0
	 *
	 * @todo    [now] [!!!] (dev) "full refund" vs "partial refund" (there are no `items` in "full refund")
	 * @todo    [now] (feature) "partial refund" trigger
	 */
	function shortcode_each_refund( $atts, $content = '' ) {
		if ( '' === $content ) {
			return '';
		}

		$order    = wc_get_order( $this->doc_obj->order_id );
		$output   = '';
		$this->order_refund_nr = 0;
		$this->order_refund    = false;
		$order_id              = $this->doc_obj->order_id;

		foreach ( $order->get_refunds() as $refund ) {
			$this->order_refund_nr++;
			$this->order_refund      = $refund;
			$this->doc_obj->order_id = $refund->get_id();
			$output .= do_shortcode( $content );
		}

		unset( $this->order_refund_nr );
		unset( $this->order_refund );
		$this->doc_obj->order_id = $order_id;

		return ( '' !== $output ? ( ( isset( $atts['before'] ) ? $atts['before'] : '' ) . $output . ( isset( $atts['after'] ) ? $atts['after'] : '' ) ) : '' );
	}

	/**
	 * return_prop.
	 *
	 * @version 1.7.1
	 * @since   1.0.0
	 *
	 * @todo    [now] (dev) Math: `multiply` and `divide`: apply `floatval()`?
	 * @todo    [now] (feature) `multiple_find`, `multiple_replace`
	 * @todo    [dev] (maybe) `on_empty`; `lang` && `not_lang`; `order_billing_country` && `not_order_billing_country` etc. OR maybe all that can be done via `[if]`?
	 */
	function return_prop( $value, $atts ) {

		// Math
		if ( is_numeric( $value ) ) {
			if ( ! empty( $atts['add'] ) ) {
				$value += floatval( do_shortcode( str_replace( array( '{', '}' ), array( '[', ']' ), $atts['add'] ) ) );
			}
			if ( ! empty( $atts['subtract'] ) ) {
				$value -= floatval( do_shortcode( str_replace( array( '{', '}' ), array( '[', ']' ), $atts['subtract'] ) ) );
			}
			if ( ! empty( $atts['multiply'] ) ) {
				$value *= do_shortcode( str_replace( array( '{', '}' ), array( '[', ']' ), $atts['multiply'] ) );
			}
			if ( ! empty( $atts['divide'] ) ) {
				if ( 0 != ( $_value = do_shortcode( str_replace( array( '{', '}' ), array( '[', ']' ), $atts['divide'] ) ) ) ) {
					$value /= $_value;
				}
			}
		}

		// Format
		if ( isset( $atts['format'] ) ) {
			switch ( $atts['format'] ) {
				case 'price':
					$value = wc_price( $value, ( isset( $this->order ) ? array( 'currency' => $this->order->get_currency() ) : array() ) );
					break;
				default:
					$value = sprintf( $atts['format'], $value );
			}
		}

		// Find/replace
		if ( isset( $atts['find'], $atts['replace'] ) ) {
			$value = str_replace( $atts['find'], $atts['replace'], $value );
		}

		// Filter
		$value = apply_filters( 'alg_wc_pdf_invoicing_return_prop', $value, $atts );

		// Before/after & Final result
		return ( '' !== $value ? ( ( isset( $atts['before'] ) ? $atts['before'] : '' ) . $value . ( isset( $atts['after'] ) ? $atts['after'] : '' ) ) : '' );

	}

	/**
	 * is_prop_type.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function is_prop_type( $type, $prop_name ) {
		$type   = $type . '_';
		$length = strlen( $type );
		return ( $type === substr( $prop_name, 0, $length ) );
	}

	/**
	 * shortcode_prop.
	 *
	 * @version 1.5.0
	 * @since   1.0.0
	 *
	 * @todo    [now] [!!!] (dev) `item_` props for coupons: check if it's callable
	 * @todo    [now] [!!!] (dev) `item_` props for coupons: `get_discount()` and `get_discount_tax()`
	 * @todo    [now] (dev) rename `item_nr` to `order_item_nr`, etc.
	 * @todo    [dev] data from another doc (e.g. display invoice nr in packing slip)
	 * @todo    [dev] `ucfirst()` for `order_total_in_words` (including Unicode)
	 * @todo    [dev] (maybe) separate shortcodes (e.g. `[doc]`, `[order]`, `[item]` etc.)
	 */
	function shortcode_prop( $atts, $content = '' ) {
		if ( ! isset( $atts['name'] ) || ( isset( $this->props[ $atts['name'] ] ) && false === $this->props[ $atts['name'] ] ) || ! isset( $this->doc_obj ) ) {
			return '';
		}

		// Prepare & check data
		if ( $this->is_prop_type( 'order', $atts['name'] ) ) {
			if ( ! isset( $this->doc_obj->order_id ) ) {
				return '';
			} elseif ( ! isset( $this->order ) || $this->order_id != $this->doc_obj->order_id ) {
				if ( ! ( $order = wc_get_order( $this->doc_obj->order_id ) ) ) {
					return '';
				} else {
					$this->order    = $order;
					$this->order_id = $this->doc_obj->order_id;
				}
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

		// Main switch
		switch ( $atts['name'] ) {

			// Document
			case 'doc_counter':
				return $this->return_prop( $this->doc_obj->get_counter(), $atts );
			case 'doc_nr':
				return $this->return_prop( $this->doc_obj->get_number(), $atts );
			case 'doc_formatted_date':
				return $this->return_prop( $this->doc_obj->get_formatted_date( ( isset( $atts['datetime_format'] ) ? $atts['datetime_format'] : 'Y-m-d' ),
					( isset( $atts['add_days'] ) ? $atts['add_days'] : 0 ) ), $atts );
			case 'doc_author_full_name':
				return $this->return_prop( $this->doc_obj->get_author_full_name(), $atts );

			// Order
			case 'order_number':
				return $this->return_prop( $this->order->get_order_number(), $atts );
			case 'order_formatted_date_created':
				return $this->return_prop( date_i18n( ( isset( $atts['datetime_format'] ) ? $atts['datetime_format'] : 'Y-m-d' ), strtotime( $this->order->get_date_created() ) ), $atts );
			case 'order_status':
				return $this->return_prop( $this->order->get_status(), $atts );
			case 'order_subtotal':
				return $this->return_prop( $this->order->get_subtotal(), $atts );
			case 'order_subtotal_incl_tax':
				return $this->return_prop( $this->order->get_subtotal_to_display( ( isset( $atts['compound'] ) && 'yes' === $atts['compound'] ), 'incl' ), $atts );
			case 'order_total':
				return $this->return_prop( $this->order->get_total(), $atts );
			case 'order_total_excl_shipping':
				return $this->return_prop( $this->order->get_total() - $this->order->get_shipping_total(), $atts );
			case 'order_total_in_words':
				$whole_part   = alg_wc_pdf_invoicing_number_to_words( intval( $this->order->get_total() ), ( isset( $atts['lang'] ) ? strtoupper( $atts['lang'] ) : 'EN' ) );
				$decimal_part = round( ( $this->order->get_total() - intval( $this->order->get_total() ) ) * 100, 2 );
				return $this->return_prop( $whole_part . ' ' . get_woocommerce_currency_symbol( $this->order->get_currency() ) . ' ' . $decimal_part . ' ' . '&cent;', $atts );
			case 'order_total_tax':
				return $this->return_prop( $this->order->get_total_tax(), $atts );
			case 'order_tax_totals':
				$value = array();
				$tax_totals = $this->order->get_tax_totals();
				foreach ( $tax_totals as $tax_row ) {
					$value[] = $tax_row->label . ': ' . $tax_row->formatted_amount;
				}
				$value = implode( '<br>', $value );
				return $this->return_prop( $value, $atts );
			case 'order_total_excl_tax':
				return $this->return_prop( $this->order->get_total() - $this->order->get_total_tax(), $atts );
			case 'order_total_excl_tax_excl_shipping':
				return $this->return_prop( $this->order->get_total() - $this->order->get_total_tax() - $this->order->get_shipping_total(), $atts );
			case 'order_discount':
				return $this->return_prop( $this->order->get_total_discount(), $atts );
			case 'order_discount_incl_tax':
				return $this->return_prop( $this->order->get_total_discount( false ), $atts );
			case 'order_discount_tax':
				return $this->return_prop( $this->order->get_discount_tax(), $atts );
			case 'order_discount_percent':
				$subtotal = $this->order->get_subtotal();
				return ( 0 != $subtotal ? $this->return_prop( $this->order->get_discount_total() / $subtotal * 100, $atts ) : 0 );
			case 'order_shipping_total':
				return $this->return_prop( $this->order->get_shipping_total(), $atts );
			case 'order_shipping_method':
				return $this->return_prop( $this->order->get_shipping_method(), $atts );
			case 'order_payment_method_title':
				return $this->return_prop( $this->order->get_payment_method_title(), $atts );
			case 'order_formatted_billing_address':
				return $this->return_prop( $this->order->get_formatted_billing_address(), $atts );
			case 'order_formatted_shipping_address':
				return $this->return_prop( $this->order->get_formatted_shipping_address(), $atts );
			case 'order_billing_first_name':
				return $this->return_prop( $this->order->get_billing_first_name(), $atts );
			case 'order_total_items_count':
				$type = ( isset( $atts['type'] ) ? explode( ',', $atts['type'] ) : 'line_item' );
				return $this->return_prop( count( $this->order->get_items( $type ) ), $atts );
			case 'order_total_items_qty':
				$type = ( isset( $atts['type'] ) ? explode( ',', $atts['type'] ) : 'line_item' );
				$result = 0;
				foreach ( $this->order->get_items( $type ) as $item ) {
					if ( ! empty( $item['quantity'] ) ) {
						$result += $item['quantity'];
					}
				}
				return $this->return_prop( $result, $atts );
			case 'order_meta':
				return ( isset( $atts['key'] ) ? $this->return_prop( get_post_meta( $this->order->get_id(), $atts['key'], true ), $atts ) : '' );
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
				return ( is_callable( array( $this->order, $func ) ) ? $this->return_prop( $this->order->{$func}(), $atts ) : '' );
			case 'order_total_refunded':
				return $this->return_prop( $this->order->get_total_refunded(), $atts );
			case 'order_total_tax_refunded':
				return $this->return_prop( $this->order->get_total_tax_refunded(), $atts );
			case 'order_total_shipping_refunded':
				return $this->return_prop( $this->order->get_total_shipping_refunded(), $atts );

			// Order item
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
				$result = ( 0 != $total ? ( $this->order_item->get_total_tax() / $this->order_item->get_total() * 100 ) : 0 );
				return $this->return_prop( $result, $atts );
			case 'item_single':
				$qty = $this->order_item->get_quantity();
				return ( 0 != $qty ? $this->return_prop( $this->order_item->get_total() / $qty, $atts ) : 0 );
			case 'item_single_incl_tax':
				$qty = $this->order_item->get_quantity();
				return ( 0 != $qty ? $this->return_prop( ( $this->order_item->get_total() + $this->order_item->get_total_tax() ) / $qty, $atts ) : 0 );
			case 'item_subtotal':
				return $this->return_prop( $this->order_item->get_subtotal(), $atts );
			case 'item_subtotal_tax':
				return $this->return_prop( $this->order_item->get_subtotal_tax(), $atts );
			case 'item_discount':
				return $this->return_prop( $this->order_item->get_subtotal() - $this->order_item->get_total(), $atts );
			case 'item_discount_incl_tax':
				return $this->return_prop( ( $this->order_item->get_subtotal() - $this->order_item->get_total() ) +
					( $this->order_item->get_subtotal_tax() - $this->order_item->get_total_tax() ), $atts );
			case 'item_discount_tax':
				return $this->return_prop( $this->order_item->get_subtotal_tax() - $this->order_item->get_total_tax(), $atts );
			case 'item_discount_percent':
				$subtotal = $this->order_item->get_subtotal();
				$result   = ( 0 != $subtotal ? ( ( $this->order_item->get_subtotal() - $this->order_item->get_total() ) / $subtotal * 100 ) : 0 );
				return $this->return_prop( $result, $atts );
			case 'item_meta':
				return ( isset( $atts['key'] ) ? $this->return_prop( $this->order_item->get_meta( $atts['key'], true ), $atts ) : '' );
			case 'item_func':
				if ( ! isset( $atts['func'] ) ) {
					return '';
				}
				$func = $atts['func'];
				return ( is_callable( array( $this->order_item, $func ) ) ? $this->return_prop( $this->order_item->{$func}(), $atts ) : '' );

			// Order item product
			case 'item_product_id':
				return $this->return_prop( $this->item_product->get_id(), $atts );
			case 'item_product_sku':
				return $this->return_prop( $this->item_product->get_sku(), $atts );
			case 'item_product_image':
				$size   = ( isset( $atts['size'] )   ? $atts['size']   : 'woocommerce_thumbnail' );
				$width  = ( isset( $atts['width'] )  ? $atts['width']  : 30 );
				$height = ( isset( $atts['height'] ) ? $atts['height'] : 30 );
				if ( ( $img_id = $this->item_product->get_image_id() ) && ( $img_url = wp_get_attachment_image_src( $img_id, $size ) ) && isset( $img_url[0] ) ) {
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
				$product_id = ( isset( $atts['use_parent'] ) && 'yes' === $atts['use_parent'] && ( $parent_id = $this->item_product->get_parent_id() ) ? $parent_id : $this->item_product->get_id() );
				return ( isset( $atts['key'] ) ? $this->return_prop( get_post_meta( $product_id, $atts['key'], true ), $atts ) : '' );
			case 'item_product_func':
				if ( ! isset( $atts['func'] ) ) {
					return '';
				}
				$func = $atts['func'];
				return ( is_callable( array( $this->item_product, $func ) ) ? $this->return_prop( $this->item_product->{$func}(), $atts ) : '' );

			// Order refund
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
