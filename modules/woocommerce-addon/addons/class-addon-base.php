<?php
namespace Sofir\WooCommerceAddon\Addons;

abstract class Addon_Base {
	protected string $id;
	protected string $name;
	protected string $description;
	protected string $icon;
	protected string $category;
	protected bool $is_pro = false;

	abstract public function init(): void;

	abstract public function render_settings(): void;

	public function __construct() {
		if ( $this->is_enabled() ) {
			$this->init();
		}
	}

	public function is_enabled(): bool {
		return (bool) \get_option( "sofir_wc_addon_{$this->id}_enabled", false );
	}

	public function enable(): void {
		\update_option( "sofir_wc_addon_{$this->id}_enabled", true );
		\do_action( "sofir/woocommerce/addon/{$this->id}/enabled" );
	}

	public function disable(): void {
		\update_option( "sofir_wc_addon_{$this->id}_enabled", false );
		\do_action( "sofir/woocommerce/addon/{$this->id}/disabled" );
	}

	public function get_id(): string {
		return $this->id;
	}

	public function get_name(): string {
		return $this->name;
	}

	public function get_description(): string {
		return $this->description;
	}

	public function get_icon(): string {
		return $this->icon;
	}

	public function get_category(): string {
		return $this->category;
	}

	public function is_pro(): bool {
		return $this->is_pro;
	}

	protected function get_option( string $key, $default = '' ) {
		return \get_option( "sofir_wc_addon_{$this->id}_{$key}", $default );
	}

	protected function update_option( string $key, $value ): bool {
		return \update_option( "sofir_wc_addon_{$this->id}_{$key}", $value );
	}

	protected function delete_option( string $key ): bool {
		return \delete_option( "sofir_wc_addon_{$this->id}_{$key}" );
	}

	public function save_settings( array $data ): void {
		foreach ( $data as $key => $value ) {
			$this->update_option( $key, $value );
		}
		\do_action( "sofir/woocommerce/addon/{$this->id}/settings_saved", $data );
	}
}
