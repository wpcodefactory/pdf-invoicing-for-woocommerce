<?php
/**
 * PDF Invoicing for WooCommerce - Core Class
 *
 * @version 2.5.0
 * @since   1.0.0
 *
 * @author WPFactory
 *
 * @package WPFactory\WC_PDF_Invoicing
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_PDF_Invoicing_Core' ) ) :

	/**
	 * Alg_WC_PDF_Invoicing_Core class.
	 *
	 * @version 2.5.0
	 * @since   1.0.0
	 */
	class Alg_WC_PDF_Invoicing_Core {

		/**
		 * PDF.
		 *
		 * @version 2.1.0
		 * @since   2.1.0
		 *
		 * @var false|Alg_WC_PDF_Invoicing_TCPDF
		 */
		public $pdf;

		/**
		 * Shortcodes.
		 *
		 * @version 2.1.0
		 * @since   2.1.0
		 *
		 * @var Alg_WC_PDF_Invoicing_Shortcodes
		 */
		public $shortcodes;

		/**
		 * Doc options.
		 *
		 * @version 2.1.0
		 * @since   2.1.0
		 *
		 * @var array
		 */
		public $doc_options;

		/**
		 * Constructor.
		 *
		 * @version 2.5.0
		 * @since   1.0.0
		 *
		 * @todo (dev) Order meta box.
		 * @todo (dev) Replace TCPDF lib with `tc-lib-pdf`.
		 */
		public function __construct() {
			// PDF.
			$this->pdf = false;

			// Action.
			do_action( 'alg_wc_pdf_invoicing_before_core_loaded', $this );

			// Functions.
			require_once plugin_dir_path( __FILE__ ) . 'functions/alg-wc-pdf-invoicing-functions.php';
			require_once plugin_dir_path( __FILE__ ) . 'functions/alg-wc-pdf-invoicing-functions-default-values.php';

			// Classes.
			require_once plugin_dir_path( __FILE__ ) . 'classes/class-alg-wc-pdf-invoicing-doc.php';

			// Core.
			if ( 'yes' === get_option( 'alg_wc_pdf_invoicing_plugin_enabled', 'yes' ) ) {
				// Hooks.
				add_action( 'admin_init', array( $this, 'delete_doc' ) );
				add_action( 'init', array( $this, 'view_doc' ) );
				add_action( 'admin_init', array( $this, 'create_doc' ) );

				// Create docs automatically.
				$all_hooks = array();
				foreach ( $this->get_enabled_docs() as $doc_id ) {
					$doc_hooks = $this->get_doc_option( $doc_id, 'hooks' );
					$all_hooks = array_merge( $all_hooks, $doc_hooks );
				}
				$all_hooks = array_unique( $all_hooks );
				foreach ( $all_hooks as $hook ) {
					if ( 'manual' !== $hook ) {
						add_action( $hook, array( $this, 'create_docs' ) );
					}
				}

				// Emails.
				add_filter(
					'woocommerce_email_attachments',
					array( $this, 'email_attachments' ),
					PHP_INT_MAX,
					3
				);

				// "My account > Orders".
				add_filter(
					'woocommerce_my_account_my_orders_actions',
					array( $this, 'my_account_my_orders_actions' ),
					PHP_INT_MAX,
					2
				);

				// Shortcodes.
				$this->shortcodes = require_once plugin_dir_path( __FILE__ ) . 'class-alg-wc-pdf-invoicing-shortcodes.php';
			}
		}

		/**
		 * Get enabled docs.
		 *
		 * @version 2.5.0
		 * @since   1.0.0
		 *
		 * @todo (feature) Custom sorting.
		 */
		public function get_enabled_docs() {
			$docs  = array();
			$total = get_option( 'alg_wc_pdf_invoicing_total_docs', 1 );
			for ( $doc_id = 0; $doc_id < $total; $doc_id++ ) {
				$enable = $this->get_doc_option( $doc_id, 'enable' );
				if ( 'yes' === $enable ) {
					$docs[] = $doc_id;
				}
			}
			return $docs;
		}

		/**
		 * My account my orders actions.
		 *
		 * @version 2.5.0
		 * @since   1.3.0
		 *
		 * @param array    $actions Existing actions.
		 * @param WC_Order $order   Order object.
		 *
		 * @return array Modified actions.
		 */
		public function my_account_my_orders_actions( $actions, $order ) {
			if ( is_object( $order ) && is_a( $order, 'WC_Order' ) ) {
				foreach ( $this->get_enabled_docs() as $doc_id ) {
					if ( 'yes' === $this->get_doc_option( $doc_id, 'my_account_orders' ) ) {
						if ( ! isset( $order_id ) ) {
							$order_id = $order->get_id();
						}
						$doc = new Alg_WC_PDF_Invoicing_Doc( $order_id, $doc_id );
						if ( $doc->is_created() ) {
							$actions[ 'alg_wc_pdf_invoicing_view_doc_' . $doc_id ] = array(
								'url'  => add_query_arg(
									array(
										'alg-wc-pdf-invoicing-view-doc' => $doc_id,
										'alg-wc-pdf-invoicing-order-id' => $order_id,
									)
								),
								'name' => $this->get_doc_option( $doc_id, 'admin_title' ),
							);
						}
					}
				}
			}
			return $actions;
		}

		/**
		 * Do shortcode.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @param string $value   Value.
		 * @param object $doc_obj Document object.
		 *
		 * @return string Processed value.
		 */
		public function do_shortcode( $value, $doc_obj ) {
			$this->shortcodes->set_doc_obj( $doc_obj );
			return do_shortcode( $value );
		}

		/**
		 * Get doc option.
		 *
		 * @version 2.4.7
		 * @since   1.0.0
		 *
		 * @param int    $doc_id       Document ID.
		 * @param string $option_id    Option ID.
		 * @param bool   $do_shortcode Whether to process shortcodes.
		 * @param object $doc_obj      Document object.
		 *
		 * @return mixed Document option value.
		 */
		public function get_doc_option( $doc_id, $option_id, $do_shortcode = false, $doc_obj = null ) {
			if ( ! isset( $this->doc_options[ $doc_id ] ) ) {
				$this->doc_options[ $doc_id ] = get_option( "alg_wc_pdf_invoicing_doc_{$doc_id}", array() );
			}

			$value = ( $this->doc_options[ $doc_id ][ $option_id ] ?? alg_wc_pdf_invoicing_get_default( $option_id, $doc_id ) );

			if ( $do_shortcode ) {
				$value = $this->do_shortcode( $value, $doc_obj );
			}

			return apply_filters(
				'alg_wc_pdf_invoicing_get_doc_option',
				$value,
				$doc_id,
				$option_id
			);
		}

		/**
		 * Get and update counter.
		 *
		 * @version 2.1.1
		 * @since   1.0.0
		 *
		 * @param int $doc_id Document ID.
		 *
		 * @return int Counter value.
		 *
		 * @todo (dev) Need another solution (instead of `get_option()`): a) new table (with auto-incremental counter); b) save counter as custom post (i.e., template) option.
		 */
		public function get_and_update_counter( $doc_id ) {
			// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery
			global $wpdb;

			// Vars.
			$doc_id = 'doc_' . $doc_id;
			$option = 'alg_wc_pdf_invoicing_counters';

			// Start MySQL transaction.
			$wpdb->query( 'START TRANSACTION' ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching

			// Get current counters.
			$row = $wpdb->get_row( $wpdb->prepare( "SELECT option_value FROM {$wpdb->options} WHERE option_name = %s LIMIT 1", $option ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching

			// Increase (and serialize) counter.
			$counters            = (
				isset( $row->option_value ) && '' !== $row->option_value ?
				maybe_unserialize( $row->option_value ) :
				array()
			);
			$counter             = ( $counters[ $doc_id ] ?? 1 );
			$counters[ $doc_id ] = $counter + 1;
			$counters            = maybe_serialize( $counters );

			// Update counters.
			$result = $wpdb->update( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching
				$wpdb->options,
				array(
					'option_value' => $counters,
					'autoload'     => 'yes',
				),
				array( 'option_name' => $option )
			);

			// Try "insert" instead of "update" (i.e., no counters set yet).
			if ( 0 === $result ) {
				$result = $wpdb->insert(
					$wpdb->options,
					array(
						'option_value' => $counters,
						'autoload'     => 'yes',
						'option_name'  => $option,
					)
				);
			}

			// Commit/Rollback MySQL transaction.
			$wpdb->query( ( $result ? 'COMMIT' : 'ROLLBACK' ) ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery.NoCaching

			return $counter;
			// phpcs:enable WordPress.DB.DirectDatabaseQuery.DirectQuery
		}

		/**
		 * Email attachments.
		 *
		 * @version 2.5.0
		 * @since   1.0.0
		 *
		 * @param array  $attachments Email attachments.
		 * @param string $email_id    Email ID.
		 * @param object $email_order Email order object.
		 *
		 * @return array Modified email attachments.
		 *
		 * @todo (dev) Per payment gateways.
		 */
		public function email_attachments( $attachments, $email_id, $email_order ) {
			if ( is_object( $email_order ) && is_a( $email_order, 'WC_Order' ) ) {
				foreach ( $this->get_enabled_docs() as $doc_id ) {
					$doc_emails = $this->get_doc_option( $doc_id, 'emails' );
					if ( in_array( $email_id, $doc_emails, true ) ) {
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
		 * Create docs.
		 *
		 * @version 2.5.0
		 * @since   1.0.0
		 *
		 * @param int $order_id Order ID.
		 *
		 * @return void
		 *
		 * @todo (dev) Per payment gateways.
		 * @todo (dev) Optionally do not create when order total is zero.
		 */
		public function create_docs( $order_id ) {
			foreach ( $this->get_enabled_docs() as $doc_id ) {
				$doc_hooks = $this->get_doc_option( $doc_id, 'hooks' );
				if ( in_array( current_filter(), $doc_hooks, true ) ) {
					$doc = new Alg_WC_PDF_Invoicing_Doc( $order_id, $doc_id );
					if (
						! $doc->is_created() &&
						apply_filters( 'alg_wc_pdf_invoicing_create_docs', true, $doc_id, $order_id )
					) {
						$doc->create(
							array(
								'author' => 0,
								'id'     => $this->get_and_update_counter( $doc_id ),
								'date'   => time(),
							)
						);
					}
				}
			}
		}

		/**
		 * Create doc.
		 *
		 * @version 2.5.0
		 * @since   1.0.0
		 *
		 * @return void
		 *
		 * @todo (dev) Rename function?
		 * @todo (dev) Add notice.
		 * @todo (dev) Check if `manual` is enabled (or `hooks` option is empty).
		 */
		public function create_doc() {
			if ( isset( $_GET['alg-wc-pdf-invoicing-create-doc'] ) ) {
				if (
					! isset( $_GET['alg-wc-pdf-invoicing-create-doc-nonce'] ) ||
					! wp_verify_nonce(
						sanitize_text_field( wp_unslash( $_GET['alg-wc-pdf-invoicing-create-doc-nonce'] ) ),
						'alg-wc-pdf-invoicing-create-doc'
					)
				) {
					wp_die(
						sprintf(
							/* Translators: %s: Error message. */
							esc_html__( 'Error: %s', 'pdf-invoicing-for-woocommerce' ),
							esc_html__( 'Nonce not set or invalid', 'pdf-invoicing-for-woocommerce' )
						)
					);
				}

				if ( ! current_user_can( 'manage_woocommerce' ) ) { // phpcs:ignore WordPress.WP.Capabilities.Unknown
					wp_die(
						sprintf(
							/* Translators: %s: Error message. */
							esc_html__( 'Error: %s', 'pdf-invoicing-for-woocommerce' ),
							esc_html__( 'Insufficient user capability.', 'pdf-invoicing-for-woocommerce' )
						)
					);
				}

				if ( ! isset( $_GET['alg-wc-pdf-invoicing-order-id'] ) ) {
					wp_die(
						sprintf(
							/* Translators: %s: Error message. */
							esc_html__( 'Error: %s', 'pdf-invoicing-for-woocommerce' ),
							esc_html__( 'Order ID not set.', 'pdf-invoicing-for-woocommerce' )
						)
					);
				}

				$doc_id   = intval( $_GET['alg-wc-pdf-invoicing-create-doc'] );
				$order_id = intval( $_GET['alg-wc-pdf-invoicing-order-id'] );
				$doc      = new Alg_WC_PDF_Invoicing_Doc( $order_id, $doc_id );

				if ( $doc->is_created() ) {
					wp_die(
						sprintf(
							/* Translators: %s: Error message. */
							esc_html__( 'Error: %s', 'pdf-invoicing-for-woocommerce' ),
							esc_html__( 'Document is already created.', 'pdf-invoicing-for-woocommerce' )
						)
					);
				}

				$doc->create(
					array(
						'author' => get_current_user_id(),
						'id'     => $this->get_and_update_counter( $doc_id ),
						'date'   => time(),
					)
				);

				wp_safe_redirect(
					remove_query_arg(
						array(
							'alg-wc-pdf-invoicing-create-doc',
							'alg-wc-pdf-invoicing-order-id',
						)
					)
				);
				exit;
			}
		}

		/**
		 * Is current user order.
		 *
		 * @version 2.5.0
		 * @since   1.3.0
		 *
		 * @param int $order_id Order ID.
		 *
		 * @return bool True if current user is the order's customer, false otherwise.
		 *
		 * @todo (dev) Also check if `my_account_orders` option is enabled?
		 */
		public function is_current_user_order( $order_id ) {
			$current_user_id = get_current_user_id();
			if ( ! $current_user_id ) {
				return false;
			}

			$order = wc_get_order( $order_id );
			if ( ! $order ) {
				return false;
			}

			return ( (int) $order->get_customer_id() === $current_user_id );
		}

		/**
		 * View doc.
		 *
		 * @version 2.5.0
		 * @since   1.0.0
		 */
		public function view_doc() {
			// phpcs:disable WordPress.Security.NonceVerification.Recommended
			if ( isset( $_GET['alg-wc-pdf-invoicing-view-doc'] ) ) {
				// Require order ID.
				if ( ! isset( $_GET['alg-wc-pdf-invoicing-order-id'] ) ) {
					wp_die(
						sprintf(
							/* Translators: %s: Error message. */
							esc_html__( 'Error: %s', 'pdf-invoicing-for-woocommerce' ),
							esc_html__( 'Order ID not set', 'pdf-invoicing-for-woocommerce' )
						)
					);
				}

				// Doc ID and order ID.
				$doc_id   = intval( $_GET['alg-wc-pdf-invoicing-view-doc'] );
				$order_id = intval( $_GET['alg-wc-pdf-invoicing-order-id'] );

				// Check user capability.
				if (
					! current_user_can( 'manage_woocommerce' ) && // phpcs:ignore WordPress.WP.Capabilities.Unknown
					! $this->is_current_user_order( $order_id )
				) {
					wp_die(
						sprintf(
							/* Translators: %s: Error message. */
							esc_html__( 'Error: %s', 'pdf-invoicing-for-woocommerce' ),
							esc_html__( 'Insufficient user capability.', 'pdf-invoicing-for-woocommerce' )
						)
					);
				}

				// Doc.
				$doc = new Alg_WC_PDF_Invoicing_Doc( $order_id, $doc_id );

				// Require doc to be created.
				if ( ! $doc->is_created() ) {
					wp_die(
						sprintf(
							/* Translators: %s: Error message. */
							esc_html__( 'Error: %s', 'pdf-invoicing-for-woocommerce' ),
							esc_html__( 'Document is not created yet.', 'pdf-invoicing-for-woocommerce' )
						)
					);
				}

				// "Min Max Step Quantity Limits Manager for WooCommerce" plugin compatibility.
				if (
					function_exists( 'alg_wc_pq' ) &&
					! empty( alg_wc_pq()->core ) &&
					PHP_INT_MAX === has_filter(
						'init',
						array( alg_wc_pq()->core, 'float_stock_amount' )
					)
				) {
					alg_wc_pq()->core->float_stock_amount();
				}

				// Output.
				$dest = (
					isset( $_GET['alg-wc-pdf-invoicing-pdf-dest'] ) ?
					wc_clean( wp_unslash( $_GET['alg-wc-pdf-invoicing-pdf-dest'] ) ) : // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
					'I'
				);
				$doc->get_pdf( $dest );

				die();
			}
			// phpcs:enable WordPress.Security.NonceVerification.Recommended
		}

		/**
		 * Delete doc.
		 *
		 * @version 2.5.0
		 * @since   1.0.0
		 *
		 * @todo (dev) Add notice.
		 * @todo (dev) Make optional (e.g., visible to admin but not for shop manager etc.).
		 */
		public function delete_doc() {
			if ( isset( $_GET['alg-wc-pdf-invoicing-delete-doc'] ) ) {
				if (
					! isset( $_GET['alg-wc-pdf-invoicing-delete-doc-nonce'] ) ||
					! wp_verify_nonce(
						sanitize_text_field( wp_unslash( $_GET['alg-wc-pdf-invoicing-delete-doc-nonce'] ) ),
						'alg-wc-pdf-invoicing-delete-doc'
					)
				) {
					wp_die(
						sprintf(
							/* Translators: %s: Error message. */
							esc_html__( 'Error: %s', 'pdf-invoicing-for-woocommerce' ),
							esc_html__( 'Nonce not set or invalid', 'pdf-invoicing-for-woocommerce' )
						)
					);
				}

				if ( ! current_user_can( 'manage_woocommerce' ) ) { // phpcs:ignore WordPress.WP.Capabilities.Unknown
					wp_die(
						sprintf(
							/* Translators: %s: Error message. */
							esc_html__( 'Error: %s', 'pdf-invoicing-for-woocommerce' ),
							esc_html__( 'Insufficient user capability.', 'pdf-invoicing-for-woocommerce' )
						)
					);
				}

				if ( ! isset( $_GET['alg-wc-pdf-invoicing-order-id'] ) ) {
					wp_die(
						sprintf(
							/* Translators: %s: Error message. */
							esc_html__( 'Error: %s', 'pdf-invoicing-for-woocommerce' ),
							esc_html__( 'Order ID not set', 'pdf-invoicing-for-woocommerce' )
						)
					);
				}

				$doc_id   = intval( $_GET['alg-wc-pdf-invoicing-delete-doc'] );
				$order_id = intval( $_GET['alg-wc-pdf-invoicing-order-id'] );
				$doc      = new Alg_WC_PDF_Invoicing_Doc( $order_id, $doc_id );

				if ( ! $doc->is_created() ) {
					wp_die(
						sprintf(
							/* Translators: %s: Error message. */
							esc_html__( 'Error: %s', 'pdf-invoicing-for-woocommerce' ),
							esc_html__( 'Document is not created yet.', 'pdf-invoicing-for-woocommerce' )
						)
					);
				}

				$doc->remove();

				wp_safe_redirect(
					remove_query_arg(
						array(
							'alg-wc-pdf-invoicing-delete-doc',
							'alg-wc-pdf-invoicing-order-id',
						)
					)
				);
				exit;
			}
		}

		/**
		 * Merge docs.
		 *
		 * @version 1.7.0
		 * @since   1.5.0
		 *
		 * @param array  $order_ids Array of order IDs.
		 * @param int    $doc_id    Document ID.
		 * @param string $dest      Destination for the merged PDF (default 'I').
		 *
		 * @see https://www.setasign.com/products/fpdi/demos/concatenate-fake/
		 *
		 * @todo (dev) Better title and file name (i.e., not `docs.pdf`)?
		 */
		public function merge_docs( $order_ids, $doc_id, $dest = 'I' ) {
			// Get PDFs.
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

			// FPDI.
			if ( ! class_exists( '\setasign\Fpdi\TcpdfFpdi' ) ) {
				require_once alg_wc_pdf_invoicing()->plugin_path() . '/assets/lib/fpdi/src/autoload.php';
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
