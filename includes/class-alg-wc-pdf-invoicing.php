<?php
/**
 * PDF Invoicing for WooCommerce - Main Class
 *
 * @version 2.0.0
 * @since   1.0.0
 *
 * @author  Algoritmika Ltd
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_PDF_Invoicing' ) ) :

/**
 * Main Alg_WC_PDF_Invoicing Class
 *
 * @version 1.3.0
 * @since   1.0.0
 *
 * @class   Alg_WC_PDF_Invoicing
 */
final class Alg_WC_PDF_Invoicing {

	/**
	 * Plugin version.
	 *
	 * @var   string
	 * @since 1.0.0
	 */
	public $version = ALG_WC_PDF_INVOICING_VERSION;

	/**
	 * @var   Alg_WC_PDF_Invoicing The single instance of the class
	 * @since 1.0.0
	 */
	protected static $_instance = null;

	/**
	 * Main Alg_WC_PDF_Invoicing Instance
	 *
	 * Ensures only one instance of Alg_WC_PDF_Invoicing is loaded or can be loaded.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 *
	 * @static
	 * @return  Alg_WC_PDF_Invoicing - Main instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Alg_WC_PDF_Invoicing Constructor.
	 *
	 * @version 2.0.0
	 * @since   1.0.0
	 *
	 * @access  public
	 */
	function __construct() {

		// Check for active WooCommerce plugin
		if ( ! function_exists( 'WC' ) ) {
			return;
		}

		// Set up localisation
		add_action( 'init', array( $this, 'localize' ) );

		// Declare compatibility with custom order tables for WooCommerce
		add_action( 'before_woocommerce_init', array( $this, 'wc_declare_compatibility' ) );

		// Pro
		if ( 'pdf-invoicing-for-woocommerce-pro.php' === basename( ALG_WC_PDF_INVOICING_FILE ) ) {
			require_once( 'pro/class-alg-wc-pdf-invoicing-pro.php' );
		}

		// Core
		$this->core = require_once( 'class-alg-wc-pdf-invoicing-core.php' );

		// Admin
		if ( is_admin() ) {
			$this->admin = require_once( 'class-alg-wc-pdf-invoicing-admin.php' );
		}

	}

	/**
	 * localize.
	 *
	 * @version 1.3.0
	 * @since   1.3.0
	 */
	function localize() {
		load_plugin_textdomain( 'pdf-invoicing-for-woocommerce', false, dirname( plugin_basename( ALG_WC_PDF_INVOICING_FILE ) ) . '/langs/' );
	}

	/**
	 * wc_declare_compatibility.
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 *
	 * @see     https://github.com/woocommerce/woocommerce/wiki/High-Performance-Order-Storage-Upgrade-Recipe-Book#declaring-extension-incompatibility
	 */
	function wc_declare_compatibility() {
		if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
			$files = ( defined( 'ALG_WC_PDF_INVOICING_FILE_FREE' ) ? array( ALG_WC_PDF_INVOICING_FILE, ALG_WC_PDF_INVOICING_FILE_FREE ) : array( ALG_WC_PDF_INVOICING_FILE ) );
			foreach ( $files as $file ) {
				\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', $file, true );
			}
		}
	}

	/**
	 * plugin_url.
	 *
	 * @version 1.3.0
	 * @since   1.0.0
	 *
	 * @return  string
	 */
	function plugin_url() {
		return untrailingslashit( plugin_dir_url( ALG_WC_PDF_INVOICING_FILE ) );
	}

	/**
	 * plugin_path.
	 *
	 * @version 1.3.0
	 * @since   1.0.0
	 *
	 * @return  string
	 */
	function plugin_path() {
		return untrailingslashit( plugin_dir_path( ALG_WC_PDF_INVOICING_FILE ) );
	}

	/**
	 * plugin_file.
	 *
	 * @version 1.3.0
	 * @since   1.0.0
	 */
	function plugin_file() {
		return ALG_WC_PDF_INVOICING_FILE;
	}

}

endif;
