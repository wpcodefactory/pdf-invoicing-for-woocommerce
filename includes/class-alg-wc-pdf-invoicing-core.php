<?php
/**
 * PDF Invoicing for WooCommerce - Core Class
 *
 * @version 1.9.2
 * @since   1.0.0
 *
 * @author  Algoritmika Ltd
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_PDF_Invoicing_Core' ) ) :

class Alg_WC_PDF_Invoicing_Core {

	/**
	 * Constructor.
	 *
	 * @version 1.7.0
	 * @since   1.0.0
	 *
	 * @todo    (dev) order meta box
	 * @todo    (dev) replace TCPDF lib with `tc-lib-pdf`
	 */
	function __construct() {

		$this->pdf = false;

		// Action
		do_action( 'alg_wc_pdf_invoicing_before_core_loaded', $this );

		// Functions
		require_once( 'functions/alg-wc-pdf-invoicing-functions.php' );
		require_once( 'functions/alg-wc-pdf-invoicing-functions-default-values.php' );

		// Classes
		require_once( 'classes/class-alg-wc-pdf-invoicing-doc.php' );

		// Core
		if ( 'yes' === get_option( 'alg_wc_pdf_invoicing_plugin_enabled', 'yes' ) ) {

			// Hooks
			add_action( 'admin_init', array( $this, 'delete_doc' ) );
			add_action( 'init',       array( $this, 'view_doc' ) );
			add_action( 'admin_init', array( $this, 'create_doc' ) );

			// Create docs automatically
			$all_hooks = array();
			foreach ( apply_filters( 'alg_wc_pdf_invoicing_enabled_docs', array( '0' ) ) as $doc_id ) {
				$doc_hooks = $this->get_doc_option( $doc_id, 'hooks' );
				$all_hooks = array_merge( $all_hooks, $doc_hooks );
			}
			$all_hooks = array_unique( $all_hooks );
			foreach ( $all_hooks as $hook ) {
				if ( 'manual' != $hook ) {
					add_action( $hook, array( $this, 'create_docs' ) );
				}
			}

			// Emails
			add_filter( 'woocommerce_email_attachments', array( $this, 'email_attachments' ), PHP_INT_MAX, 4 );

			// "My account > Orders"
			add_filter( 'woocommerce_my_account_my_orders_actions', array( $this, 'my_account_my_orders_actions' ), PHP_INT_MAX, 2 );

			// Shortcodes
			$this->shortcodes = require_once( 'class-alg-wc-pdf-invoicing-shortcodes.php' );

		}

	}

	/**
	 * my_account_my_orders_actions.
	 *
	 * @version 1.3.0
	 * @since   1.3.0
	 */
	function my_account_my_orders_actions( $actions, $order ) {
		if ( is_object( $order ) && is_a( $order, 'WC_Order' ) ) {
			foreach ( apply_filters( 'alg_wc_pdf_invoicing_enabled_docs', array( '0' ) ) as $doc_id ) {
				if ( 'yes' === $this->get_doc_option( $doc_id, 'my_account_orders' ) ) {
					if ( ! isset( $order_id ) ) {
						$order_id = $order->get_id();
					}
					$doc = new Alg_WC_PDF_Invoicing_Doc( $order_id, $doc_id );
					if ( $doc->is_created() ) {
						$actions[ 'alg_wc_pdf_invoicing_view_doc_' . $doc_id ] = array(
							'url'  => add_query_arg( array( 'alg-wc-pdf-invoicing-view-doc' => $doc_id, 'alg-wc-pdf-invoicing-order-id' => $order_id ) ),
							'name' => $this->get_doc_option( $doc_id, 'admin_title' ),
						);
					}
				}
			}
		}
		return $actions;
	}

	/**
	 * do_shortcode.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function do_shortcode( $value, $doc_obj ) {
		$this->shortcodes->set_doc_obj( $doc_obj );
		return do_shortcode( $value );
	}

	/**
	 * get_doc_option.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function get_doc_option( $doc_id, $option_id, $do_shortcode = false, $doc_obj = null ) {
		if ( ! isset( $this->doc_options[ $doc_id ] ) ) {
			$this->doc_options[ $doc_id ] = get_option( "alg_wc_pdf_invoicing_doc_{$doc_id}", array() );
		}
		$value = ( isset( $this->doc_options[ $doc_id ][ $option_id ] ) ? $this->doc_options[ $doc_id ][ $option_id ] : alg_wc_pdf_invoicing_get_default( $option_id, $doc_id ) );
		return ( $do_shortcode ? $this->do_shortcode( $value, $doc_obj ) : $value );
	}

	/**
	 * get_and_update_counter.
	 *
	 * @version 1.9.2
	 * @since   1.0.0
	 *
	 * @todo    (dev) need another solution (instead of `get_option()`): a) new table (with auto-incremental counter); b) save counter as custom post (i.e., template) option
	 */
	function get_and_update_counter( $doc_id ) {
		global $wpdb;

		// Vars
		$doc_id = 'doc_' . $doc_id;
		$option = 'alg_wc_pdf_invoicing_counters';

		// Start MySQL transaction
		$wpdb->query( 'START TRANSACTION' );

		// Get current counters
		$row = $wpdb->get_row( $wpdb->prepare( "SELECT option_value FROM {$wpdb->options} WHERE option_name = %s LIMIT 1", $option ) );

		// Increase (and serialize) counter
		$counters            = ( isset( $row->option_value ) ? maybe_unserialize( $row->option_value ) : array() );
		$counter             = ( isset( $counters[ $doc_id ] ) ? $counters[ $doc_id ] : 1 );
		$counters[ $doc_id ] = $counter + 1;
		$counters            = maybe_serialize( $counters );

		// Update counters
		$result = $wpdb->update( $wpdb->options, array( 'option_value' => $counters, 'autoload' => 'yes' ), array( 'option_name' => $option ) );

		// Try "insert" instead of "update" (i.e., no counters set yet)
		if ( 0 === $result ) {
			$result = $wpdb->insert( $wpdb->options, array( 'option_value' => $counters, 'autoload' => 'yes', 'option_name' => $option ) );
		}

		// Commit/Rollback MySQL transaction
		$wpdb->query( ( $result ? 'COMMIT' : 'ROLLBACK' ) );

		return $counter;
	}

	/**
	 * email_attachments.
	 *
	 * @version 1.1.1
	 * @since   1.0.0
	 *
	 * @todo    (dev) per payment gateways
	 */
	function email_attachments( $attachments, $email_id, $email_order, $email = false ) {
		if ( is_object( $email_order ) && is_a( $email_order, 'WC_Order' ) ) {
			foreach ( apply_filters( 'alg_wc_pdf_invoicing_enabled_docs', array( '0' ) ) as $doc_id ) {
				$doc_emails = $this->get_doc_option( $doc_id, 'emails' );
				if ( in_array( $email_id, $doc_emails ) ) {
					if ( ! isset( $order_id ) ) {
						$order_id = $email_order->get_id();
					}
					$doc = new Alg_WC_PDF_Invoicing_Doc( $order_id, $doc_id );
					if ( $doc->is_created() ) {
						$tmp_dir = trailingslashit( get_temp_dir() );
						$doc->get_pdf( 'F', $tmp_dir );
						$attachments[] = $tmp_dir . $doc->get_number() . '.pdf';
					}
				}
			}
		}
		return $attachments;
	}

	/**
	 * create_docs.
	 *
	 * @version 1.8.0
	 * @since   1.0.0
	 *
	 * @todo    (dev) per payment gateways
	 * @todo    (dev) optionally do not create when order total is zero
	 */
	function create_docs( $order_id ) {
		foreach ( apply_filters( 'alg_wc_pdf_invoicing_enabled_docs', array( '0' ) ) as $doc_id ) {
			$doc_hooks = $this->get_doc_option( $doc_id, 'hooks' );
			if ( in_array( current_filter(), $doc_hooks ) ) {
				$doc = new Alg_WC_PDF_Invoicing_Doc( $order_id, $doc_id );
				if ( ! $doc->is_created() && apply_filters( 'alg_wc_pdf_invoicing_create_docs', true, $doc_id, $order_id ) ) {
					$doc->create( array(
						'author' => 0,
						'id'     => $this->get_and_update_counter( $doc_id ),
						'date'   => time(),
					) );
				}
			}
		}
	}

	/**
	 * create_doc.
	 *
	 * @version 1.9.0
	 * @since   1.0.0
	 *
	 * @todo    (dev) rename function?
	 * @todo    (dev) add notice
	 * @todo    (dev) check if `manual` is enabled (or `hooks` option is empty)
	 */
	function create_doc() {
		if ( isset( $_GET['alg-wc-pdf-invoicing-create-doc'] ) ) {
			if ( ! isset( $_GET['alg-wc-pdf-invoicing-order-id'] ) ) {
				wp_die( sprintf( __( 'Error: %s', 'pdf-invoicing-for-woocommerce' ), __( 'Order ID not set.', 'pdf-invoicing-for-woocommerce' ) ) );
			}
			if ( ! current_user_can( 'manage_woocommerce' ) ) {
				wp_die( sprintf( __( 'Error: %s', 'pdf-invoicing-for-woocommerce' ), __( 'Insufficient user capability.', 'pdf-invoicing-for-woocommerce' ) ) );
			}
			$doc_id   = intval( $_GET['alg-wc-pdf-invoicing-create-doc'] );
			$order_id = intval( $_GET['alg-wc-pdf-invoicing-order-id'] );
			$doc      = new Alg_WC_PDF_Invoicing_Doc( $order_id, $doc_id );
			if ( $doc->is_created() ) {
				wp_die( sprintf( __( 'Error: %s', 'pdf-invoicing-for-woocommerce' ), __( 'Document is already created.', 'pdf-invoicing-for-woocommerce' ) ) );
			}
			$doc->create( array(
				'author' => get_current_user_id(),
				'id'     => $this->get_and_update_counter( $doc_id ),
				'date'   => time(),
			) );
			wp_safe_redirect( remove_query_arg( array( 'alg-wc-pdf-invoicing-create-doc', 'alg-wc-pdf-invoicing-order-id' ) ) );
			exit;
		}
	}

	/**
	 * is_current_user_order.
	 *
	 * @version 1.3.0
	 * @since   1.3.0
	 *
	 * @todo    (dev) also check if `my_account_orders` option is enabled?
	 */
	function is_current_user_order( $order_id ) {
		return ( 0 != ( $current_user_id = get_current_user_id() ) && ( $order = wc_get_order( $order_id ) ) && $order->get_customer_id() == $current_user_id );
	}

	/**
	 * view_doc.
	 *
	 * @version 1.3.0
	 * @since   1.0.0
	 */
	function view_doc() {
		if ( isset( $_GET['alg-wc-pdf-invoicing-view-doc'] ) ) {
			if ( ! isset( $_GET['alg-wc-pdf-invoicing-order-id'] ) ) {
				wp_die( sprintf( __( 'Error: %s.', 'pdf-invoicing-for-woocommerce' ), __( 'Order ID not set', 'pdf-invoicing-for-woocommerce' ) ) );
			}
			$doc_id   = intval( $_GET['alg-wc-pdf-invoicing-view-doc'] );
			$order_id = intval( $_GET['alg-wc-pdf-invoicing-order-id'] );
			if ( ! current_user_can( 'manage_woocommerce' ) && ! $this->is_current_user_order( $order_id ) ) {
				wp_die( sprintf( __( 'Error: %s', 'pdf-invoicing-for-woocommerce' ), __( 'Insufficient user capability.', 'pdf-invoicing-for-woocommerce' ) ) );
			}
			$doc = new Alg_WC_PDF_Invoicing_Doc( $order_id, $doc_id );
			if ( ! $doc->is_created() ) {
				wp_die( sprintf( __( 'Error: %s', 'pdf-invoicing-for-woocommerce' ), __( 'Document is not created yet.', 'pdf-invoicing-for-woocommerce' ) ) );
			}
			echo $doc->get_pdf( ( isset( $_GET['alg-wc-pdf-invoicing-pdf-dest'] ) ? wc_clean( $_GET['alg-wc-pdf-invoicing-pdf-dest'] ) : 'I' ) );
			die();
		}
	}

	/**
	 * delete_doc.
	 *
	 * @version 1.3.0
	 * @since   1.0.0
	 *
	 * @todo    (dev) add notice
	 * @todo    (dev) make optional (e.g. visible to admin but not for shop manager etc.)
	 */
	function delete_doc() {
		if ( isset( $_GET['alg-wc-pdf-invoicing-delete-doc'] ) ) {
			if ( ! isset( $_GET['alg-wc-pdf-invoicing-order-id'] ) ) {
				wp_die( sprintf( __( 'Error: %s.', 'pdf-invoicing-for-woocommerce' ), __( 'Order ID not set', 'pdf-invoicing-for-woocommerce' ) ) );
			}
			$doc_id   = intval( $_GET['alg-wc-pdf-invoicing-delete-doc'] );
			$order_id = intval( $_GET['alg-wc-pdf-invoicing-order-id'] );
			if ( ! current_user_can( 'manage_woocommerce' ) ) {
				wp_die( sprintf( __( 'Error: %s', 'pdf-invoicing-for-woocommerce' ), __( 'Insufficient user capability.', 'pdf-invoicing-for-woocommerce' ) ) );
			}
			$doc = new Alg_WC_PDF_Invoicing_Doc( $order_id, $doc_id );
			if ( ! $doc->is_created() ) {
				wp_die( sprintf( __( 'Error: %s', 'pdf-invoicing-for-woocommerce' ), __( 'Document is not created yet.', 'pdf-invoicing-for-woocommerce' ) ) );
			}
			$doc->remove();
			wp_safe_redirect( remove_query_arg( array( 'alg-wc-pdf-invoicing-delete-doc', 'alg-wc-pdf-invoicing-order-id' ) ) );
			exit;
		}
	}

	/**
	 * merge_docs.
	 *
	 * @version 1.7.0
	 * @since   1.5.0
	 *
	 * @see     https://www.setasign.com/products/fpdi/demos/concatenate-fake/
	 *
	 * @todo    (dev) better title and file name (i.e. not `docs.pdf`)?
	 */
	function merge_docs( $order_ids, $doc_id, $dest = 'I' ) {

		// Get PDFs
		$files = array();
		foreach ( $order_ids as $order_id ) {
			$doc = new Alg_WC_PDF_Invoicing_Doc( $order_id, $doc_id );
			if ( $doc->is_created() ) {
				$tmp_dir = trailingslashit( get_temp_dir() );
				$doc->get_pdf( 'F', $tmp_dir );
				$files[] = $tmp_dir . $doc->get_number() . '.pdf';
			}
		}
		if ( empty( $files ) ) {
			return false;
		}

		// FPDI
		if ( ! class_exists( '\setasign\Fpdi\TcpdfFpdi' ) ) {
			require_once( alg_wc_pdf_invoicing()->plugin_path() . '/assets/lib/fpdi/src/autoload.php' );
		}
		$fpdi_pdf = new \setasign\Fpdi\TcpdfFpdi();
		$fpdi_pdf->SetTitle( 'docs.pdf' );
		$fpdi_pdf->setPrintHeader( false );
		$fpdi_pdf->setPrintFooter( false );
		foreach ( $files as $file ) {
			$page_count = $fpdi_pdf->setSourceFile( $file );
			for ( $page_nr = 1; $page_nr <= $page_count; $page_nr++ ) {
				$page_id = $fpdi_pdf->ImportPage( $page_nr );
				$s       = $fpdi_pdf->getTemplatesize( $page_id );
				$fpdi_pdf->AddPage( $s['orientation'], $s );
				$fpdi_pdf->useImportedPage( $page_id );
			}
		}
		$fpdi_pdf->Output( 'docs.pdf', $dest );

		die();
	}

}

endif;

return new Alg_WC_PDF_Invoicing_Core();
