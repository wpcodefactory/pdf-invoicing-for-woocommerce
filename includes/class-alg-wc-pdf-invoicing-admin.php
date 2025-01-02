<?php
/**
 * PDF Invoicing for WooCommerce - Admin Class
 *
 * @version 2.2.4
 * @since   1.0.0
 *
 * @author  Algoritmika Ltd
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_PDF_Invoicing_Admin' ) ) :

class Alg_WC_PDF_Invoicing_Admin {

	/**
	 * Constructor.
	 *
	 * @version 2.2.4
	 * @since   1.0.0
	 */
	function __construct() {

		// Action links
		add_filter( 'plugin_action_links_' . plugin_basename( alg_wc_pdf_invoicing()->plugin_file() ), array( $this, 'action_links' ) );

		// "Recommendations" page
		$this->add_cross_selling_library();

		// WC Settings tab as WPFactory submenu item
		$this->move_wc_settings_tab_to_wpfactory_menu();

		// Settings
		add_filter( 'woocommerce_get_settings_pages', array( $this, 'add_woocommerce_settings_tab' ) );

		// Version update
		if ( get_option( 'alg_wc_pdf_invoicing_version', '' ) !== alg_wc_pdf_invoicing()->version ) {
			add_action( 'admin_init', array( $this, 'version_updated' ) );
		}

		// Core
		if ( 'yes' === get_option( 'alg_wc_pdf_invoicing_plugin_enabled', 'yes' ) ) {

			// Order list columns
			add_filter( 'manage_edit-shop_order_columns',            array( $this, 'add_order_columns' ), PHP_INT_MAX );
			add_filter( 'manage_woocommerce_page_wc-orders_columns', array( $this, 'add_order_columns' ), PHP_INT_MAX );
			add_action( 'manage_shop_order_posts_custom_column',           array( $this, 'render_order_columns' ), PHP_INT_MAX, 2 );
			add_action( 'manage_woocommerce_page_wc-orders_custom_column', array( $this, 'render_order_columns' ), PHP_INT_MAX, 2 );

			// Order edit page meta box
			add_action( 'add_meta_boxes', array( $this, 'add_order_meta_box' ), 10, 2 );

			// Orders "Bulk action"
			add_filter( 'bulk_actions-edit-shop_order',            array( $this, 'add_order_bulk_actions' ), PHP_INT_MAX );
			add_filter( 'bulk_actions-woocommerce_page_wc-orders', array( $this, 'add_order_bulk_actions' ), PHP_INT_MAX );
			add_filter( 'handle_bulk_actions-edit-shop_order',            array( $this, 'handle_order_bulk_actions' ), PHP_INT_MAX, 3 );
			add_filter( 'handle_bulk_actions-woocommerce_page_wc-orders', array( $this, 'handle_order_bulk_actions' ), PHP_INT_MAX, 3 );
			add_action( 'admin_footer', array( $this, 'bulk_actions_js' ) );

			// Scripts
			add_action( 'admin_head', array( $this, 'add_scripts' ) );

		}

	}

	/**
	 * bulk_actions_js.
	 *
	 * @version 2.2.4
	 * @since   2.2.4
	 */
	function bulk_actions_js() {

		// Check option
		if ( 'yes' !== get_option( 'alg_wc_pdf_invoicing_view_pdfs_new_tab', 'no' ) ) {
			return;
		}

		// Check current screen
		if (
			! ( $current_screen = get_current_screen() ) ||
			! in_array(
				$current_screen->id,
				array(
					'edit-shop_order',
					'woocommerce_page_wc-orders',
				)
			)
		) {
			return;
		}

		// Check if "View PDFs" exists
		$do_view_pdf_exists = false;
		if ( version_compare( PHP_VERSION, '5.3.0', '>=' ) ) {
			foreach ( apply_filters( 'alg_wc_pdf_invoicing_enabled_docs', array( '0' ) ) as $doc_id ) {
				if ( in_array( 'view', alg_wc_pdf_invoicing()->core->get_doc_option( $doc_id, 'order_bulk_actions' ) ) ) {
					$do_view_pdf_exists = true;
					break;
				}
			}
		}
		if ( ! $do_view_pdf_exists ) {
			return;
		}

		// Script
		?>
		<script>
			jQuery( document ).ready( function () {
				jQuery( '#posts-filter, #wc-orders-filter' ).submit( function ( e ) {
					e.preventDefault();
					var value = jQuery( '#bulk-action-selector-top' ).val().substring( 0, 26 );
					var is_view_pdf = ( 'alg_wc_pdf_invoicing_view_' === value );
					if ( is_view_pdf ) {
						jQuery( this ).prop( 'target', '_blank' );
					}
					this.submit();
					if ( is_view_pdf ) {
						jQuery( this ).prop( 'target', '_self' );
					}
				} );
			} );
		</script>
		<?php

	}

	/**
	 * add_cross_selling_library.
	 *
	 * @version 2.2.0
	 * @since   2.2.0
	 */
	function add_cross_selling_library() {

		if ( ! class_exists( '\WPFactory\WPFactory_Cross_Selling\WPFactory_Cross_Selling' ) ) {
			return;
		}

		$cross_selling = new \WPFactory\WPFactory_Cross_Selling\WPFactory_Cross_Selling();
		$cross_selling->setup( array( 'plugin_file_path' => ALG_WC_PDF_INVOICING_FILE ) );
		$cross_selling->init();

	}

	/**
	 * move_wc_settings_tab_to_wpfactory_menu.
	 *
	 * @version 2.2.0
	 * @since   2.2.0
	 */
	function move_wc_settings_tab_to_wpfactory_menu() {

		if ( ! class_exists( '\WPFactory\WPFactory_Admin_Menu\WPFactory_Admin_Menu' ) ) {
			return;
		}

		$wpfactory_admin_menu = \WPFactory\WPFactory_Admin_Menu\WPFactory_Admin_Menu::get_instance();

		if ( ! method_exists( $wpfactory_admin_menu, 'move_wc_settings_tab_to_wpfactory_menu' ) ) {
			return;
		}

		$wpfactory_admin_menu->move_wc_settings_tab_to_wpfactory_menu( array(
			'wc_settings_tab_id' => 'alg_wc_pdf_invoicing',
			'menu_title'         => __( 'PDF Invoicing', 'pdf-invoicing-for-woocommerce' ),
			'page_title'         => __( 'PDF Invoicing', 'pdf-invoicing-for-woocommerce' ),
		) );

	}

	/**
	 * get_order_bulk_actions.
	 *
	 * @version 1.5.0
	 * @since   1.5.0
	 *
	 *
	 * @todo    (desc) [!] better naming, e.g., "View" and "Download" (i.e., remove "PDFs")?
	 * @todo    (feature) new action: print (i.e., merge and print)?
	 * @todo    (feature) new action: archive (i.e., zip)
	 */
	function get_order_bulk_actions() {
		return array(
			'view'     => __( 'View PDFs', 'pdf-invoicing-for-woocommerce' ),
			'download' => __( 'Download PDFs', 'pdf-invoicing-for-woocommerce' ),
		);
	}

	/**
	 * add_order_bulk_actions.
	 *
	 * @version 1.5.0
	 * @since   1.5.0
	 */
	function add_order_bulk_actions( $actions ) {
		foreach ( $this->get_order_bulk_actions() as $id => $title ) {
			foreach ( apply_filters( 'alg_wc_pdf_invoicing_enabled_docs', array( '0' ) ) as $doc_id ) {
				if ( in_array( $id, array( 'view', 'download' ) ) && version_compare( PHP_VERSION, '5.3.0', '<' ) ) {
					continue;
				}
				if ( ! in_array( $id, alg_wc_pdf_invoicing()->core->get_doc_option( $doc_id, 'order_bulk_actions' ) ) ) {
					continue;
				}
				$actions[ "alg_wc_pdf_invoicing_{$id}_{$doc_id}" ] = $title . ': ' .
					alg_wc_pdf_invoicing()->core->get_doc_option( $doc_id, 'admin_title' );
			}
		}
		return $actions;
	}

	/**
	 * handle_order_bulk_actions.
	 *
	 * @version 1.5.0
	 * @since   1.5.0
	 */
	function handle_order_bulk_actions( $redirect_to, $action, $post_ids ) {
		if ( ! empty( $post_ids ) && false !== strpos( $action, 'alg_wc_pdf_invoicing' ) ) {

			// Extract action and doc ID
			$action = str_replace( 'alg_wc_pdf_invoicing_', '', $action );
			$action = explode( '_', $action );
			$doc_id = $action[1];
			$action = $action[0];

			// Process action
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
	 * add_scripts.
	 *
	 * @version 1.4.0
	 * @since   1.4.0
	 *
	 * @see     https://printjs.crabbly.com/
	 *
	 * @todo    (dev) redo this
	 * @todo    (dev) local copy
	 * @todo    (dev) css?
	 */
	function add_scripts() {
		if ( 'yes' === get_option( 'alg_wc_pdf_invoicing_use_print_js', 'yes' ) ) {
			echo '<script src="https://printjs-4de6.kxcdn.com/print.min.js"></script>';
		}
	}

	/**
	 * get_actions.
	 *
	 * @version 2.2.2
	 * @since   1.4.0
	 *
	 * @todo    (feature) customizable icons?
	 */
	function get_actions( $doc_id, $order_id, $order ) {
		$actions = '';

		$doc = new Alg_WC_PDF_Invoicing_Doc( $order_id, $doc_id );
		if ( $doc->is_created() ) {

			// URLs (Actions: View, Download, Print, Delete)
			$order_id_url  = add_query_arg( array( 'alg-wc-pdf-invoicing-order-id'   => $order_id ) );
			$view_url      = add_query_arg( array( 'alg-wc-pdf-invoicing-view-doc'   => $doc_id ), $order_id_url );
			$download_url  = add_query_arg( array( 'alg-wc-pdf-invoicing-pdf-dest'   => 'D' ),     $view_url );
			$print_url     = add_query_arg( array( 'alg-wc-pdf-invoicing-print'      => true ),    $view_url );
			$delete_url    = add_query_arg( array( 'alg-wc-pdf-invoicing-delete-doc' => $doc_id ), $order_id_url );

			// Icons
			$download_icon = '<span title="' . esc_attr__( 'Download', 'pdf-invoicing-for-woocommerce' ) . '" class="dashicons dashicons-pdf"></span>';
			$print_icon    = '<span title="' . esc_attr__( 'Print', 'pdf-invoicing-for-woocommerce' )    . '" class="dashicons dashicons-printer"></span>';
			$delete_icon   = '<span title="' . esc_attr__( 'Delete', 'pdf-invoicing-for-woocommerce' )   . '" class="dashicons dashicons-trash"></span>';

			// Misc.
			$view_title    = __( 'View', 'pdf-invoicing-for-woocommerce' );
			$delete_msg    = __( 'Are you sure?', 'pdf-invoicing-for-woocommerce' );

			// Results
			$view          = '<a href="' . esc_url( $view_url ) . '" target="_blank" title="' . esc_attr( $view_title ) . '">' . $doc->get_number() . '</a>';
			$download      = '<a href="' . esc_url( $download_url ) . '">' . $download_icon . '</a>';
			$print         = (
				'yes' === get_option( 'alg_wc_pdf_invoicing_use_print_js', 'yes' ) ?
				'<a href="#" onclick="printJS(\'' . esc_js( $print_url ) . '\'); return false;">' . $print_icon . '</a>' :
				'<a href="' . esc_url( $print_url ) . '" target="_blank">' . $print_icon . '</a>'
			);
			$delete        = '<a href="' . esc_url( $delete_url ) . '" onclick="return confirm(\'' . $delete_msg . '\')">' . $delete_icon . '</a>';

			// Final results
			$actions       = implode( ' ', array( $view, $download, $print, $delete ) );

		} else {

			// Action: Create
			$doc_hooks = alg_wc_pdf_invoicing()->core->get_doc_option( $doc_id, 'hooks' );
			if ( in_array( 'manual', $doc_hooks ) || empty( $doc_hooks ) ) {
				$create_url   = add_query_arg( array( 'alg-wc-pdf-invoicing-create-doc' => $doc_id, 'alg-wc-pdf-invoicing-order-id' => $order_id ) );
				$create_icon  = '<span title="' . esc_attr__( 'Create', 'pdf-invoicing-for-woocommerce' ) . '" class="dashicons dashicons-insert"></span>';
				$create_msg   = __( 'Are you sure?', 'pdf-invoicing-for-woocommerce' );
				$actions      = '<a href="' . esc_url( $create_url ) . '" onclick="return confirm(\'' . $create_msg . '\')">' . $create_icon . '</a>';
			}

		}

		return $actions;
	}

	/**
	 * add_order_meta_box.
	 *
	 * @version 2.0.1
	 * @since   1.4.0
	 *
	 * @todo    (feature) customizable title
	 * @todo    (feature) add option to disable this
	 */
	function add_order_meta_box( $post_type, $post ) {

		$screen = (
			class_exists( '\Automattic\WooCommerce\Internal\DataStores\Orders\CustomOrdersTableController' ) &&
			wc_get_container()->get( \Automattic\WooCommerce\Internal\DataStores\Orders\CustomOrdersTableController::class )->custom_orders_table_usage_is_enabled()
		) ? wc_get_page_screen_id( 'shop-order' ) : 'shop_order';

		add_meta_box( 'alg-wc-pdf-invoicing',
			__( 'PDF Invoicing', 'pdf-invoicing-for-woocommerce' ),
			array( $this, 'display_order_meta_box' ),
			$screen,
			'side'
		);

	}

	/**
	 * display_order_meta_box.
	 *
	 * @version 2.0.1
	 * @since   1.4.0
	 *
	 * @todo    (feature) customizable template
	 * @todo    (dev) nonces everywhere
	 */
	function display_order_meta_box( $post_or_order ) {

		if ( ! ( $order = wc_get_order( $post_or_order ) ) ) {
			return;
		}

		$data = array();
		foreach ( apply_filters( 'alg_wc_pdf_invoicing_enabled_docs', array( '0' ) ) as $doc_id ) {
			$row_data = array(
				alg_wc_pdf_invoicing()->core->get_doc_option( $doc_id, 'admin_title' ),
				$this->get_actions( $doc_id, $order->get_id(), $order ),
			);
			$data[] = '<tr><td>' . implode( '</td><td>', $row_data ) . '</td></tr>';
		}
		echo '<table class="widefat striped"><tbody>' . implode( '', $data ) . '</tbody></table>';

	}

	/**
	 * add_order_columns.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function add_order_columns( $columns ) {
		foreach ( apply_filters( 'alg_wc_pdf_invoicing_enabled_docs', array( '0' ) ) as $doc_id ) {
			$title = alg_wc_pdf_invoicing()->core->get_doc_option( $doc_id, 'admin_title' );
			$columns[ 'alg_wc_pdf_invoicing_doc_' . $doc_id ] = $title;
		}
		return $columns;
	}

	/**
	 * render_order_columns.
	 *
	 * @version 2.0.1
	 * @since   1.0.0
	 */
	function render_order_columns( $column, $order_id_or_order ) {

		if ( ! ( $order = wc_get_order( $order_id_or_order ) ) ) {
			return;
		}

		foreach ( apply_filters( 'alg_wc_pdf_invoicing_enabled_docs', array( '0' ) ) as $doc_id ) {
			if ( 'alg_wc_pdf_invoicing_doc_' . $doc_id === $column ) {
				echo $this->get_actions( $doc_id, $order->get_id(), $order );
			}
		}

	}

	/**
	 * action_links.
	 *
	 * @version 2.2.2
	 * @since   1.0.0
	 *
	 * @param   mixed $links
	 * @return  array
	 */
	function action_links( $links ) {
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
	 * add_woocommerce_settings_tab.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function add_woocommerce_settings_tab( $settings ) {
		$settings[] = require_once( 'settings/class-alg-wc-pdf-invoicing-settings.php' );
		return $settings;
	}

	/**
	 * version_updated.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function version_updated() {
		update_option( 'alg_wc_pdf_invoicing_version', alg_wc_pdf_invoicing()->version );
	}

}

endif;

return new Alg_WC_PDF_Invoicing_Admin();
