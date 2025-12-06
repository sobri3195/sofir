<?php
namespace Sofir\WooCommerceAddon;

class Addons_Manager {
	private static ?Addons_Manager $instance = null;
	private array $addons = [];

	public static function instance(): Addons_Manager {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public function __construct() {
		$this->load_addons();
		$this->init_addons();
		$this->register_ajax_handlers();
	}

	private function load_addons(): void {
		require_once SOFIR_PLUGIN_DIR . '/modules/woocommerce-addon/addons/class-addon-base.php';

		$addon_files = [
			'class-product-bundles.php',
			'class-bogo-deals.php',
			'class-wishlist.php',
			'class-quick-view.php',
			'class-smart-compare.php',
			'class-pre-order.php',
			'class-product-timer.php',
			'class-wholesale-pricing.php',
			'class-smart-notifications.php',
			'class-product-addons.php',
		];

		foreach ( $addon_files as $file ) {
			$filepath = SOFIR_PLUGIN_DIR . '/modules/woocommerce-addon/addons/' . $file;
			if ( file_exists( $filepath ) ) {
				require_once $filepath;
			}
		}
	}

	private function init_addons(): void {
		$addon_classes = [
			'Sofir\WooCommerceAddon\Addons\Product_Bundles',
			'Sofir\WooCommerceAddon\Addons\BOGO_Deals',
			'Sofir\WooCommerceAddon\Addons\Wishlist',
			'Sofir\WooCommerceAddon\Addons\Quick_View',
			'Sofir\WooCommerceAddon\Addons\Smart_Compare',
			'Sofir\WooCommerceAddon\Addons\Pre_Order',
			'Sofir\WooCommerceAddon\Addons\Product_Timer',
			'Sofir\WooCommerceAddon\Addons\Wholesale_Pricing',
			'Sofir\WooCommerceAddon\Addons\Smart_Notifications',
			'Sofir\WooCommerceAddon\Addons\Product_Addons',
		];

		foreach ( $addon_classes as $class ) {
			if ( class_exists( $class ) ) {
				$addon = new $class();
				$this->addons[ $addon->get_id() ] = $addon;
			}
		}

		\do_action( 'sofir/woocommerce/addons_loaded', $this->addons );
	}

	public function get_addons(): array {
		return $this->addons;
	}

	public function get_addon( string $id ) {
		return $this->addons[ $id ] ?? null;
	}

	public function get_addons_by_category( string $category ): array {
		return array_filter( $this->addons, function( $addon ) use ( $category ) {
			return $addon->get_category() === $category;
		} );
	}

	public function get_categories(): array {
		return [
			'products'  => \__( 'Products', 'sofir' ),
			'marketing' => \__( 'Marketing', 'sofir' ),
			'customer'  => \__( 'Customer', 'sofir' ),
			'checkout'  => \__( 'Checkout', 'sofir' ),
			'analytics' => \__( 'Analytics', 'sofir' ),
		];
	}

	private function register_ajax_handlers(): void {
		\add_action( 'wp_ajax_sofir_toggle_addon', [ $this, 'ajax_toggle_addon' ] );
		\add_action( 'wp_ajax_sofir_save_addon_settings', [ $this, 'ajax_save_addon_settings' ] );
	}

	public function ajax_toggle_addon(): void {
		\check_ajax_referer( 'sofir_wc_addon_nonce', 'nonce' );

		if ( ! \current_user_can( 'manage_options' ) ) {
			\wp_send_json_error( [ 'message' => \__( 'Permission denied', 'sofir' ) ] );
		}

		$addon_id = isset( $_POST['addon_id'] ) ? \sanitize_text_field( $_POST['addon_id'] ) : '';
		$enabled = isset( $_POST['enabled'] ) && $_POST['enabled'] === 'true';

		$addon = $this->get_addon( $addon_id );

		if ( ! $addon ) {
			\wp_send_json_error( [ 'message' => \__( 'Addon not found', 'sofir' ) ] );
		}

		if ( $enabled ) {
			$addon->enable();
			$message = sprintf( \__( '%s enabled successfully', 'sofir' ), $addon->get_name() );
		} else {
			$addon->disable();
			$message = sprintf( \__( '%s disabled successfully', 'sofir' ), $addon->get_name() );
		}

		\wp_send_json_success( [
			'message' => $message,
			'enabled' => $enabled,
		] );
	}

	public function ajax_save_addon_settings(): void {
		\check_ajax_referer( 'sofir_wc_addon_nonce', 'nonce' );

		if ( ! \current_user_can( 'manage_options' ) ) {
			\wp_send_json_error( [ 'message' => \__( 'Permission denied', 'sofir' ) ] );
		}

		$addon_id = isset( $_POST['addon_id'] ) ? \sanitize_text_field( $_POST['addon_id'] ) : '';
		$settings = isset( $_POST['settings'] ) ? (array) $_POST['settings'] : [];

		$addon = $this->get_addon( $addon_id );

		if ( ! $addon ) {
			\wp_send_json_error( [ 'message' => \__( 'Addon not found', 'sofir' ) ] );
		}

		$addon->save_settings( $settings );

		\wp_send_json_success( [
			'message' => sprintf( \__( '%s settings saved successfully', 'sofir' ), $addon->get_name() ),
		] );
	}
}
