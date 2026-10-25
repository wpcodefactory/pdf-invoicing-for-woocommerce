<?php
/**
 * PDF Invoicing for WooCommerce - Admin Class
 *
 * @version 2.5.0
 * @since   1.0.0
 *
 * @author WPFactory
 *
 * @package WPFactory\WC_PDF_Invoicing
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_PDF_Invoicing_Admin' ) ) :

	/**
	 * Alg_WC_PDF_Invoicing_Admin class.
	 *
	 * @version 2.5.0
	 * @since   1.0.0
	 */
	class Alg_WC_PDF_Invoicing_Admin {

		/**
		 * Constructor.
		 *
		 * @version 2.5.0
		 * @since   1.0.0
		 */
		public function __construct() {
			// Action links.
			add_filter(
				'plugin_action_links_' . plugin_basename( alg_wc_pdf_invoicing()->plugin_file() ),
				array( $this, 'action_links' )
			);

			// "Recommendations" page.
			add_action(
				'init',
				array( $this, 'add_cross_selling_library' )
			);

			// WC Settings tab as WPFactory submenu item.
			add_action(
				'init',
				array( $this, 'move_wc_settings_tab_to_wpfactory_menu' )
			);

			// Settings.
			add_filter(
				'woocommerce_get_settings_pages',
				array( $this, 'add_woocommerce_settings_tab' )
			);

			// Version update.
			if ( get_option( 'alg_wc_pdf_invoicing_version', '' ) !== alg_wc_pdf_invoicing()->version ) {
				add_action(
					'admin_init',
					array( $this, 'version_updated' )
				);
			}

			// Enqueue admin scripts and styles.
			add_action(
				'admin_enqueue_scripts',
				array( $this, 'enqueue_admin_scripts_and_styles' )
			);

			// Hooks the Rich Text Editor field into WooCommerce settings API.
			add_action(
				'woocommerce_admin_field_alg_wc_text_editor',
				array( $this, 'generate_text_editor_html' )
			);

			// Core.
			if ( 'yes' === get_option( 'alg_wc_pdf_invoicing_plugin_enabled', 'yes' ) ) {
				// Order list columns.
				add_filter(
					'manage_edit-shop_order_columns',
					array( $this, 'add_order_columns' ),
					PHP_INT_MAX
				);
				add_filter(
					'manage_woocommerce_page_wc-orders_columns',
					array( $this, 'add_order_columns' ),
					PHP_INT_MAX
				);
				add_action(
					'manage_shop_order_posts_custom_column',
					array( $this, 'render_order_columns' ),
					PHP_INT_MAX,
					2
				);
				add_action(
					'manage_woocommerce_page_wc-orders_custom_column',
					array( $this, 'render_order_columns' ),
					PHP_INT_MAX,
					2
				);

				// Order edit page meta box.
				add_action(
					'add_meta_boxes',
					array( $this, 'add_order_meta_box' )
				);

				// Orders "Bulk action".
				add_filter(
					'bulk_actions-edit-shop_order',
					array( $this, 'add_order_bulk_actions' ),
					PHP_INT_MAX
				);
				add_filter(
					'bulk_actions-woocommerce_page_wc-orders',
					array( $this, 'add_order_bulk_actions' ),
					PHP_INT_MAX
				);
				add_filter(
					'handle_bulk_actions-edit-shop_order',
					array( $this, 'handle_order_bulk_actions' ),
					PHP_INT_MAX,
					3
				);
				add_filter(
					'handle_bulk_actions-woocommerce_page_wc-orders',
					array( $this, 'handle_order_bulk_actions' ),
					PHP_INT_MAX,
					3
				);
				add_action(
					'admin_enqueue_scripts',
					array( $this, 'bulk_actions_js' )
				);

				// Scripts.
				add_action(
					'admin_enqueue_scripts',
					array( $this, 'add_scripts' )
				);
			}
		}

		/**
		 * Returns allowed HTML for wp_kses.
		 *
		 * @version 2.5.0
		 * @since   2.5.0
		 */
		public function get_allowed_html() {
			$allowed_html = wp_kses_allowed_html( 'post' );

			$allowed_html['a']['onclick'] = true;

			return $allowed_html;
		}

		/**
		 * Bulk actions JS.
		 *
		 * @version 2.5.0
		 * @since   2.2.4
		 *
		 * @todo (v2.5.0) Move to a separate JS file (instead of inline script)?
		 */
		public function bulk_actions_js() {
			// Check option.
			if ( 'yes' !== get_option( 'alg_wc_pdf_invoicing_view_pdfs_new_tab', 'no' ) ) {
				return;
			}

			// Check current screen.
			$current_screen = get_current_screen();
			if (
				! $current_screen ||
				! in_array(
					$current_screen->id,
					array(
						'edit-shop_order',
						'woocommerce_page_wc-orders',
					),
					true
				)
			) {
				return;
			}

			// Check if "View PDFs" exists.
			$do_view_pdf_exists = false;
			if ( version_compare( PHP_VERSION, '5.3.0', '>=' ) ) {
				foreach ( alg_wc_pdf_invoicing()->core->get_enabled_docs() as $doc_id ) {
					if (
						in_array(
							'view',
							alg_wc_pdf_invoicing()->core->get_doc_option( $doc_id, 'order_bulk_actions' ),
							true
						)
					) {
						$do_view_pdf_exists = true;
						break;
					}
				}
			}
			if ( ! $do_view_pdf_exists ) {
				return;
			}

			// Script.
			wp_register_script(
				'alg-wc-pdf-invoicing-bulk-actions',
				false,
				array( 'jquery' ),
				ALG_WC_PDF_INVOICING_VERSION,
				true
			);

			wp_enqueue_script( 'alg-wc-pdf-invoicing-bulk-actions' );

			return wp_add_inline_script(
				'alg-wc-pdf-invoicing-bulk-actions',
				'jQuery(document).ready(function () {
					jQuery("#posts-filter, #wc-orders-filter").submit(function (e) {
						e.preventDefault();
						var value = jQuery("#bulk-action-selector-top").val().substring(0, 26);
						var is_view_pdf = "alg_wc_pdf_invoicing_view_" === value;
						if (is_view_pdf) {
							jQuery(this).prop("target", "_blank");
						}
						this.submit();
						if (is_view_pdf) {
							jQuery(this).prop("target", "_self");
						}
					});
				});'
			);
		}

		/**
		 * Add cross-selling library.
		 *
		 * @version 2.2.0
		 * @since   2.2.0
		 */
		public function add_cross_selling_library() {
			if ( ! class_exists( '\WPFactory\WPFactory_Cross_Selling\WPFactory_Cross_Selling' ) ) {
				return;
			}

			$cross_selling = new \WPFactory\WPFactory_Cross_Selling\WPFactory_Cross_Selling();
			$cross_selling->setup( array( 'plugin_file_path' => ALG_WC_PDF_INVOICING_FILE ) );
			$cross_selling->init();
		}

		/**
		 * Move WC settings tab to WPFactory menu.
		 *
		 * @version 2.3.0
		 * @since   2.2.0
		 */
		public function move_wc_settings_tab_to_wpfactory_menu() {
			if ( ! class_exists( '\WPFactory\WPFactory_Admin_Menu\WPFactory_Admin_Menu' ) ) {
				return;
			}

			$wpfactory_admin_menu = \WPFactory\WPFactory_Admin_Menu\WPFactory_Admin_Menu::get_instance();

			if ( ! method_exists( $wpfactory_admin_menu, 'move_wc_settings_tab_to_wpfactory_menu' ) ) {
				return;
			}

			$wpfactory_admin_menu->move_wc_settings_tab_to_wpfactory_menu(
				array(
					'wc_settings_tab_id' => 'alg_wc_pdf_invoicing',
					'menu_title'         => __( 'PDF Invoicing', 'pdf-invoicing-for-woocommerce' ),
					'page_title'         => __( 'PDF Invoices & Packing Slips Generator for WooCommerce', 'pdf-invoicing-for-woocommerce' ),
					'plugin_icon'        => array(
						'get_url_method'    => 'wporg_plugins_api',
						'wporg_plugin_slug' => 'pdf-invoicing-for-woocommerce',
					),
				)
			);
		}

		/**
		 * Get order bulk actions.
		 *
		 * @version 1.5.0
		 * @since   1.5.0
		 *
		 * @todo (desc) Better naming, e.g., "View" and "Download" (i.e., remove "PDFs")?
		 * @todo (feature) New action: Print (i.e., merge and print)?
		 * @todo (feature) New action: Archive (i.e., zip).
		 */
		public function get_order_bulk_actions() {
			return array(
				'view'     => __( 'View PDFs', 'pdf-invoicing-for-woocommerce' ),
				'download' => __( 'Download PDFs', 'pdf-invoicing-for-woocommerce' ),
			);
		}

		/**
		 * Add order bulk actions.
		 *
		 * @version 2.5.0
		 * @since   1.5.0
		 *
		 * @param array $actions Existing bulk actions.
		 *
		 * @return array Modified bulk actions.
		 */
		public function add_order_bulk_actions( $actions ) {
			foreach ( $this->get_order_bulk_actions() as $id => $title ) {
				foreach ( alg_wc_pdf_invoicing()->core->get_enabled_docs() as $doc_id ) {
					if (
						in_array( $id, array( 'view', 'download' ), true ) &&
						version_compare( PHP_VERSION, '5.3.0', '<' )
					) {
						continue;
					}
					if (
						! in_array(
							$id,
							alg_wc_pdf_invoicing()->core->get_doc_option( $doc_id, 'order_bulk_actions' ),
							true
						)
					) {
						continue;
					}
					$actions[ "alg_wc_pdf_invoicing_{$id}_{$doc_id}" ] = (
						$title . ': ' .
						alg_wc_pdf_invoicing()->core->get_doc_option( $doc_id, 'admin_title' )
					);
				}
			}
			return $actions;
		}

		/**
		 * Handle order bulk actions.
		 *
		 * @version 1.5.0
		 * @since   1.5.0
		 *
		 * @param string $redirect_to The redirect URL.
		 * @param string $action      The bulk action being performed.
		 * @param array  $post_ids    The IDs of the posts being acted upon.
		 *
		 * @return string The redirect URL.
		 */
		public function handle_order_bulk_actions( $redirect_to, $action, $post_ids ) {
			if (
				! empty( $post_ids ) &&
				false !== strpos( $action, 'alg_wc_pdf_invoicing' )
			) {
				// Extract action and doc ID.
				$action = str_replace( 'alg_wc_pdf_invoicing_', '', $action );
				$action = explode( '_', $action );
				$doc_id = $action[1];
				$action = $action[0];

				// Process action.
				switch ( $action ) {
					case 'view':
						alg_wc_pdf_invoicing()->core->merge_docs( $post_ids, $doc_id, 'I' );
						break;
					case 'download':
						alg_wc_pdf_invoicing()->core->merge_docs( $post_ids, $doc_id, 'D' );
						break;
				}
			}
			return $redirect_to;
		}

		/**
		 * Add scripts.
		 *
		 * @version 2.3.0
		 * @since   1.4.0
		 *
		 * @see https://printjs.crabbly.com/
		 * @see https://printjs-4de6.kxcdn.com/print.min.js
		 */
		public function add_scripts() {
			if ( 'yes' === get_option( 'alg_wc_pdf_invoicing_use_print_js', 'yes' ) ) {
				wp_enqueue_script(
					'alg-wc-pdf-invoicing-print',
					alg_wc_pdf_invoicing()->plugin_url() . '/assets/js/print.min.js',
					array(),
					alg_wc_pdf_invoicing()->version,
					true
				);
			}
		}

		/**
		 * Get actions.
		 *
		 * @version 2.5.0
		 * @since   1.4.0
		 *
		 * @param int $doc_id   The document ID.
		 * @param int $order_id The order ID.
		 *
		 * @return string HTML for the actions.
		 *
		 * @todo (feature) Customizable icons?
		 */
		public function get_actions( $doc_id, $order_id ) {
			$actions = '';

			$doc = new Alg_WC_PDF_Invoicing_Doc( $order_id, $doc_id );
			if ( $doc->is_created() ) {
				// Get URLs (Actions: View, Download, Print, Delete).
				$order_id_url = add_query_arg( array( 'alg-wc-pdf-invoicing-order-id' => $order_id ) );
				$view_url     = add_query_arg( array( 'alg-wc-pdf-invoicing-view-doc' => $doc_id ), $order_id_url );
				$download_url = add_query_arg( array( 'alg-wc-pdf-invoicing-pdf-dest' => 'D' ), $view_url );
				$print_url    = add_query_arg( array( 'alg-wc-pdf-invoicing-print' => true ), $view_url );
				$delete_url   = add_query_arg( array( 'alg-wc-pdf-invoicing-delete-doc' => $doc_id ), $order_id_url );

				// Add nonce to delete URL.
				$delete_url = wp_nonce_url(
					$delete_url,
					'alg-wc-pdf-invoicing-delete-doc',
					'alg-wc-pdf-invoicing-delete-doc-nonce'
				);

				// Icons.
				$download_icon = '<span
					title="' . esc_attr__( 'Download', 'pdf-invoicing-for-woocommerce' ) . '"
					class="dashicons dashicons-pdf"
				></span>';
				$print_icon    = '<span
					title="' . esc_attr__( 'Print', 'pdf-invoicing-for-woocommerce' ) . '"
					class="dashicons dashicons-printer"
				></span>';
				$delete_icon   = '<span
					title="' . esc_attr__( 'Delete', 'pdf-invoicing-for-woocommerce' ) . '"
					class="dashicons dashicons-trash"
				></span>';

				// Misc.
				$view_title = __( 'View', 'pdf-invoicing-for-woocommerce' );
				$delete_msg = __( 'Are you sure?', 'pdf-invoicing-for-woocommerce' );

				// Results.
				$view     = '<a href="' . esc_url( $view_url ) . '" target="_blank" title="' . esc_attr( $view_title ) . '">' . $doc->get_number() . '</a>';
				$download = '<a href="' . esc_url( $download_url ) . '">' . $download_icon . '</a>';
				$print    = (
					'yes' === get_option( 'alg_wc_pdf_invoicing_use_print_js', 'yes' ) ?
					'<a href="#" onclick="printJS(\'' . esc_js( $print_url ) . '\'); return false;">' . $print_icon . '</a>' :
					'<a href="' . esc_url( $print_url ) . '" target="_blank">' . $print_icon . '</a>'
				);
				$delete   = '<a href="' . esc_url( $delete_url ) . '" onclick="return confirm(\'' . $delete_msg . '\')">' . $delete_icon . '</a>';

				// Final results.
				$actions = implode( ' ', array( $view, $download, $print, $delete ) );
			} else {
				// Action: Create.
				$doc_hooks = alg_wc_pdf_invoicing()->core->get_doc_option( $doc_id, 'hooks' );
				if ( in_array( 'manual', $doc_hooks, true ) || empty( $doc_hooks ) ) {
					$create_url  = add_query_arg(
						array(
							'alg-wc-pdf-invoicing-create-doc' => $doc_id,
							'alg-wc-pdf-invoicing-order-id'   => $order_id,
						)
					);
					$create_url  = wp_nonce_url(
						$create_url,
						'alg-wc-pdf-invoicing-create-doc',
						'alg-wc-pdf-invoicing-create-doc-nonce'
					);
					$create_icon = '<span
						title="' . esc_attr__( 'Create', 'pdf-invoicing-for-woocommerce' ) . '"
						class="dashicons dashicons-insert"
					></span>';
					$create_msg  = __( 'Are you sure?', 'pdf-invoicing-for-woocommerce' );
					$actions     = '<a
						href="' . esc_url( $create_url ) . '"
						onclick="return confirm(\'' . $create_msg . '\')"
					>' . $create_icon . '</a>';
				}
			}

			return $actions;
		}

		/**
		 * Add order meta box.
		 *
		 * @version 2.0.1
		 * @since   1.4.0
		 *
		 * @todo (feature) Customizable title.
		 * @todo (feature) Add option to disable this.
		 */
		public function add_order_meta_box() {
			$screen = (
				(
					class_exists( '\Automattic\WooCommerce\Internal\DataStores\Orders\CustomOrdersTableController' ) &&
					wc_get_container()->get( \Automattic\WooCommerce\Internal\DataStores\Orders\CustomOrdersTableController::class )->custom_orders_table_usage_is_enabled()
				) ?
				wc_get_page_screen_id( 'shop-order' ) :
				'shop_order'
			);

			add_meta_box(
				'alg-wc-pdf-invoicing',
				__( 'PDF Invoicing', 'pdf-invoicing-for-woocommerce' ),
				array( $this, 'display_order_meta_box' ),
				$screen,
				'side'
			);
		}

		/**
		 * Display order meta box.
		 *
		 * @version 2.5.0
		 * @since   1.4.0
		 *
		 * @param WP_Post|WC_Order $post_or_order Post or order.
		 *
		 * @todo (feature) Customizable template
		 */
		public function display_order_meta_box( $post_or_order ) {
			$order = wc_get_order( $post_or_order );
			if ( ! $order ) {
				return;
			}

			$data = array();
			foreach ( alg_wc_pdf_invoicing()->core->get_enabled_docs() as $doc_id ) {
				$row_data = array(
					alg_wc_pdf_invoicing()->core->get_doc_option( $doc_id, 'admin_title' ),
					$this->get_actions( $doc_id, $order->get_id() ),
				);
				$data[]   = '<tr><td>' . implode( '</td><td>', $row_data ) . '</td></tr>';
			}

			echo '<table class="widefat striped"><tbody>' .
				wp_kses( implode( '', $data ), $this->get_allowed_html() ) .
			'</tbody></table>';
		}

		/**
		 * Add order columns.
		 *
		 * @version 2.5.0
		 * @since   1.0.0
		 *
		 * @param array $columns Existing order columns.
		 *
		 * @return array Modified order columns.
		 */
		public function add_order_columns( $columns ) {
			foreach ( alg_wc_pdf_invoicing()->core->get_enabled_docs() as $doc_id ) {
				$title = alg_wc_pdf_invoicing()->core->get_doc_option( $doc_id, 'admin_title' );
				$columns[ 'alg_wc_pdf_invoicing_doc_' . $doc_id ] = $title;
			}
			return $columns;
		}

		/**
		 * Render order columns.
		 *
		 * @version 2.5.0
		 * @since   1.0.0
		 *
		 * @param string       $column            Column name.
		 * @param int|WC_Order $order_id_or_order Order ID or order object.
		 *
		 * @return void
		 */
		public function render_order_columns( $column, $order_id_or_order ) {
			$order = wc_get_order( $order_id_or_order );
			if ( ! $order ) {
				return;
			}

			foreach ( alg_wc_pdf_invoicing()->core->get_enabled_docs() as $doc_id ) {
				if ( 'alg_wc_pdf_invoicing_doc_' . $doc_id === $column ) {
					echo wp_kses( $this->get_actions( $doc_id, $order->get_id() ), $this->get_allowed_html() );
				}
			}
		}

		/**
		 * Action links.
		 *
		 * @version 2.2.2
		 * @since   1.0.0
		 *
		 * @param mixed $links Existing action links.
		 *
		 * @return array Modified action links.
		 */
		public function action_links( $links ) {
			$custom_links = array();

			$custom_links[] = '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=alg_wc_pdf_invoicing' ) . '">' .
				__( 'Settings', 'pdf-invoicing-for-woocommerce' ) .
			'</a>';

			if ( 'pdf-invoicing-for-woocommerce.php' === basename( alg_wc_pdf_invoicing()->plugin_file() ) ) {
				$custom_links[] = '<a target="_blank" style="font-weight: bold; color: green;" href="https://wpfactory.com/item/pdf-invoicing-for-woocommerce/">' .
					__( 'Go Pro', 'pdf-invoicing-for-woocommerce' ) .
				'</a>';
			}

			return array_merge( $custom_links, $links );
		}

		/**
		 * Add WooCommerce settings tab.
		 *
		 * @version 2.3.0
		 * @since   1.0.0
		 *
		 * @param array $settings Existing WooCommerce setting tabs.
		 *
		 * @return array Modified WooCommerce setting tabs.
		 */
		public function add_woocommerce_settings_tab( $settings ) {
			$settings[] = require_once plugin_dir_path( __FILE__ ) . 'settings/class-alg-wc-pdf-invoicing-settings.php';
			return $settings;
		}

		/**
		 * Version updated.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 */
		public function version_updated() {
			update_option( 'alg_wc_pdf_invoicing_version', alg_wc_pdf_invoicing()->version );
		}

		/**
		 * Enqueue admin scripts and styles.
		 *
		 * @version 2.5.0
		 * @since   2.4.0
		 *
		 * @todo (v2.4.0) Check `$_GET['section']` as well?
		 * @todo (v2.4.0) Test with the "Custom Emails" plugin?
		 */
		public function enqueue_admin_scripts_and_styles() {
			// phpcs:disable WordPress.Security.NonceVerification.Recommended
			if (
				! isset( $_GET['page'], $_GET['tab'] ) ||
				'wc-settings' !== sanitize_text_field( wp_unslash( $_GET['page'] ) ) ||
				'alg_wc_pdf_invoicing' !== sanitize_text_field( wp_unslash( $_GET['tab'] ) )
			) {
				return;
			}
			// phpcs:enable WordPress.Security.NonceVerification.Recommended

			$min = ( defined( 'SCRIPT_DEBUG' ) && true === SCRIPT_DEBUG ? '' : '.min' );

			// Enqueue CSS for the admin pages.
			wp_enqueue_style(
				'alg-wc-pdf-invoicing-admin',
				alg_wc_pdf_invoicing()->plugin_url() . '/assets/css/alg-wc-pdf-invoicing-admin' . $min . '.css',
				array(),
				alg_wc_pdf_invoicing()->version
			);

			// Enqueue JS for the admin pages.
			wp_enqueue_script(
				'alg-wc-pdf-invoicing-admin',
				alg_wc_pdf_invoicing()->plugin_url() . '/assets/js/alg-wc-pdf-invoicing-admin' . $min . '.js',
				array( 'jquery' ),
				alg_wc_pdf_invoicing()->version,
				true
			);

			// Localize the script to pass PHP data to JavaScript.
			wp_localize_script(
				'alg-wc-pdf-invoicing-admin',
				'alg_wc_pdf_invoicing_admin_js',
				array(
					'shortcodes'      => $this->generate_shortcode_list_html(),
					'shortcodes_text' => __( 'Shortcodes', 'pdf-invoicing-for-woocommerce' ),
				)
			);
		}

		/**
		 * Generate rich text editor HTML.
		 *
		 * @version 2.5.0
		 * @since   2.4.0
		 *
		 * @param array $value Field value and settings.
		 *
		 * @return void
		 */
		public function generate_text_editor_html( $value ) {
			$option_value = $value['value'];

			// Get the description and tooltip HTML for the field.
			$field_description = WC_Admin_Settings::get_field_description( $value );
			$description       = $field_description['description'];
			$tooltip_html      = $field_description['tooltip_html'];

			$custom_attributes = array();

			if ( ! empty( $value['custom_attributes'] ) && is_array( $value['custom_attributes'] ) ) {
				foreach ( $value['custom_attributes'] as $attribute => $attribute_value ) {
					$custom_attributes[] = esc_attr( $attribute ) . '="' . esc_attr( $attribute_value ) . '"';
				}
			}

			?>
			<tr class="<?php echo esc_attr( $value['row_class'] ); ?>">
				<th scope="row" class="titledesc">
					<label for="<?php echo esc_attr( $value['id'] ); ?>">
						<?php echo esc_html( $value['title'] ); ?><?php echo wp_kses_post( $tooltip_html ); ?>
					</label>
				</th>
				<td
					class="forminp forminp-<?php echo esc_attr( sanitize_title( $value['type'] ) ); ?> alg-wc-text-editor"
				>
					<?php echo wp_kses_post( $description ); ?>
					<div <?php echo implode( ' ', $custom_attributes ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
						<?php
						$editor_id  = sanitize_html_class( $value['id'] );
						$editor_css = $value['css'];

						$settings = array(
							'textarea_name' => $value['field_name'],
							'editor_class'  => $value['class'] . ' ' . $editor_id,
							'editor_css'    => "<style>.{$editor_id}.wp-editor-area { {$editor_css} }</style>",
						);

						add_filter(
							'tiny_mce_before_init',
							function ( $init ) use ( $editor_id, $editor_css ) {
								$init['body_class']    = $editor_id;
								$init['content_style'] = 'body.' . $editor_id . ' {' . $editor_css . '}';

								return $init;
							}
						);

						wp_editor( htmlspecialchars_decode( $option_value, ENT_QUOTES ), $editor_id, $settings );
						?>
					</div>
				</td>
			</tr>
			<?php
		}

		/**
		 * Generate shortcode list.
		 *
		 * @version 2.4.0
		 * @since   2.4.0
		 *
		 * @param array $shortcodes Shortcodes.
		 *
		 * @return string HTML for the shortcode list.
		 */
		public function generate_shortcode_list_html( $shortcodes = array() ) {
			$default_shortcodes = alg_wc_pdf_invoicing()->core->shortcodes->shortcodes ?? array();
			$prop_alias         = alg_wc_pdf_invoicing()->core->shortcodes->prop_aliases ?? array();

			$shortcodes = ! empty( $shortcodes ) ? $shortcodes : array_merge( $default_shortcodes, $prop_alias );

			$html  = '<div class="alg-wc-shortcode-list">';
			$html .= '<input' .
				' type="text"' .
				' class="alg-wc-shortcode-search"' .
				' placeholder="' . __( 'Search for a shortcode&hellip;', 'pdf-invoicing-for-woocommerce' ) . '"' .
			'>';
			$html .= '<ul>';

			foreach ( $shortcodes as $shortcode ) {
				$html .= sprintf(
					'<li data-shortcode="[%1$s]">[%2$s]</li>',
					esc_attr( alg_wc_pdf_invoicing()->core->shortcodes->shortcode_prefix . $shortcode ),
					esc_html( alg_wc_pdf_invoicing()->core->shortcodes->shortcode_prefix . $shortcode )
				);
			}

			$html .= '</ul>';
			$html .= '</div>';

			return $html;
		}
	}

endif;

return new Alg_WC_PDF_Invoicing_Admin();
