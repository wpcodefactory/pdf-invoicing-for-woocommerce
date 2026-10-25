<?php
/**
 * PDF Invoicing for WooCommerce - Main Class
 *
 * @version 2.5.0
 * @since   1.0.0
 *
 * @author WPFactory
 *
 * @package WPFactory\WC_PDF_Invoicing
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_PDF_Invoicing' ) ) :

	/**
	 * Alg_WC_PDF_Invoicing class.
	 *
	 * @version 2.5.0
	 * @since   1.0.0
	 */
	final class Alg_WC_PDF_Invoicing {

		/**
		 * Plugin version.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @var string
		 */
		public $version = ALG_WC_PDF_INVOICING_VERSION;

		/**
		 * Core.
		 *
		 * @version 2.1.0
		 * @since   2.1.0
		 *
		 * @var Alg_WC_PDF_Invoicing_Core
		 */
		public $core;

		/**
		 * Admin.
		 *
		 * @version 2.1.0
		 * @since   2.1.0
		 *
		 * @var Alg_WC_PDF_Invoicing_Admin
		 */
		public $admin;

		/**
		 * The single instance of the class.
		 *
		 * @version 2.5.0
		 * @since   1.0.0
		 *
		 * @var Alg_WC_PDF_Invoicing
		 */
		protected static $instance = null;

		/**
		 * Main Alg_WC_PDF_Invoicing Instance.
		 *
		 * Ensures only one instance of Alg_WC_PDF_Invoicing is loaded or can be loaded.
		 *
		 * @version 2.5.0
		 * @since   1.0.0
		 *
		 * @static
		 *
		 * @return Alg_WC_PDF_Invoicing
		 */
		public static function instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 * Alg_WC_PDF_Invoicing Constructor.
		 *
		 * @version 2.5.0
		 * @since   1.0.0
		 *
		 * @access public
		 */
		public function __construct() {
			// Check for active WooCommerce plugin.
			if ( ! function_exists( 'WC' ) ) {
				return;
			}

			// Load libs.
			if ( is_admin() ) {
				require_once plugin_dir_path( ALG_WC_PDF_INVOICING_FILE ) . 'vendor/autoload.php';
			}

			// Declare compatibility with custom order tables for WooCommerce.
			add_action( 'before_woocommerce_init', array( $this, 'wc_declare_compatibility' ) );

			// Pro.
			if ( 'pdf-invoicing-for-woocommerce-pro.php' === basename( ALG_WC_PDF_INVOICING_FILE ) ) {
				require_once plugin_dir_path( __FILE__ ) . 'pro/class-alg-wc-pdf-invoicing-pro.php';
			}

			// Core.
			$this->core = require_once plugin_dir_path( __FILE__ ) . 'class-alg-wc-pdf-invoicing-core.php';

			// Admin.
			if ( is_admin() ) {
				$this->admin = require_once plugin_dir_path( __FILE__ ) . 'class-alg-wc-pdf-invoicing-admin.php';
			}
		}

		/**
		 * Load TCPDF library.
		 *
		 * @version 2.2.0
		 * @since   2.2.0
		 */
		public static function load_tcpdf_lib() {
			// `K_PATH_IMAGES` constant.
			if ( 'yes' === get_option( 'alg_wc_pdf_invoicing_tcpdf_path_images', 'yes' ) ) {
				$uploads_dir = wp_upload_dir();
				defined( 'K_PATH_IMAGES' ) || define( 'K_PATH_IMAGES', $uploads_dir['basedir'] . '/' ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedConstantFound
			}

			// TCPDF.
			if ( ! class_exists( 'TCPDF' ) ) {
				// Config.
				if ( 'yes' === get_option( 'alg_wc_pdf_invoicing_use_custom_tcpdf_config', 'yes' ) ) {
					defined( 'K_TCPDF_EXTERNAL_CONFIG' ) || define( 'K_TCPDF_EXTERNAL_CONFIG', true ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedConstantFound
					require_once plugin_dir_path( ALG_WC_PDF_INVOICING_FILE ) . 'includes/config/tcpdf_config.php';
				}

				// Lib.
				require_once plugin_dir_path( ALG_WC_PDF_INVOICING_FILE ) . 'assets/lib/tcpdf/tcpdf.php';
			}
		}

		/**
		 * WC declare compatibility.
		 *
		 * @version 2.0.0
		 * @since   2.0.0
		 *
		 * @see https://developer.woocommerce.com/docs/features/high-performance-order-storage/recipe-book/
		 */
		public function wc_declare_compatibility() {
			if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
				$files = (
					defined( 'ALG_WC_PDF_INVOICING_FILE_FREE' ) ?
					array( ALG_WC_PDF_INVOICING_FILE, ALG_WC_PDF_INVOICING_FILE_FREE ) :
					array( ALG_WC_PDF_INVOICING_FILE )
				);
				foreach ( $files as $file ) {
					\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility(
						'custom_order_tables',
						$file,
						true
					);
				}
			}
		}

		/**
		 * Plugin URL.
		 *
		 * @version 1.3.0
		 * @since   1.0.0
		 *
		 * @return string
		 */
		public function plugin_url() {
			return untrailingslashit( plugin_dir_url( ALG_WC_PDF_INVOICING_FILE ) );
		}

		/**
		 * Plugin path.
		 *
		 * @version 1.3.0
		 * @since   1.0.0
		 *
		 * @return string
		 */
		public function plugin_path() {
			return untrailingslashit( plugin_dir_path( ALG_WC_PDF_INVOICING_FILE ) );
		}

		/**
		 * Plugin file.
		 *
		 * @version 1.3.0
		 * @since   1.0.0
		 */
		public function plugin_file() {
			return ALG_WC_PDF_INVOICING_FILE;
		}
	}

endif;
