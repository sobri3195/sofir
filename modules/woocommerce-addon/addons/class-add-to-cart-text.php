<?php
namespace Sofir\WooCommerceAddon\Addons;

class Add_To_Cart_Text extends Addon_Base {

	public function __construct() {
		$this->id          = 'add-to-cart-text';
		$this->name        = \__( 'Add to Cart Text', 'sofir' );
		$this->description = \__( 'Change any product type default Add to Cart button text in the Shop, Archive, and Product pages.', 'sofir' );
		$this->icon        = '✏️';
		$this->category    = 'cart';

		parent::__construct();
	}

	public function init(): void {
		\add_filter( 'woocommerce_product_single_add_to_cart_text', [ $this, 'change_single_add_to_cart_text' ] );
		\add_filter( 'woocommerce_product_add_to_cart_text', [ $this, 'change_archive_add_to_cart_text' ], 10, 2 );
	}

	public function change_single_add_to_cart_text( string $text ): string {
		$custom_text = $this->get_option( 'single_text', '' );
		return $custom_text ?: $text;
	}

	public function change_archive_add_to_cart_text( string $text, $product ): string {
		if ( $product->is_type( 'simple' ) ) {
			$custom_text = $this->get_option( 'simple_text', '' );
			return $custom_text ?: $text;
		}

		if ( $product->is_type( 'variable' ) ) {
			$custom_text = $this->get_option( 'variable_text', '' );
			return $custom_text ?: $text;
		}

		if ( $product->is_type( 'grouped' ) ) {
			$custom_text = $this->get_option( 'grouped_text', '' );
			return $custom_text ?: $text;
		}

		if ( $product->is_type( 'external' ) ) {
			$custom_text = $this->get_option( 'external_text', '' );
			return $custom_text ?: $text;
		}

		return $text;
	}

	public function render_settings(): void {
		$single_text = $this->get_option( 'single_text', '' );
		$simple_text = $this->get_option( 'simple_text', '' );
		$variable_text = $this->get_option( 'variable_text', '' );
		$grouped_text = $this->get_option( 'grouped_text', '' );
		$external_text = $this->get_option( 'external_text', '' );
		?>
		<tr>
			<th scope="row"><label for="single_text"><?php esc_html_e( 'Single Product Page', 'sofir' ); ?></label></th>
			<td>
				<input type="text" id="single_text" name="sofir_wc_addon_add-to-cart-text_single_text" value="<?php echo esc_attr( $single_text ); ?>" class="regular-text" placeholder="<?php esc_attr_e( 'Add to Cart', 'sofir' ); ?>" />
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="simple_text"><?php esc_html_e( 'Simple Products', 'sofir' ); ?></label></th>
			<td>
				<input type="text" id="simple_text" name="sofir_wc_addon_add-to-cart-text_simple_text" value="<?php echo esc_attr( $simple_text ); ?>" class="regular-text" placeholder="<?php esc_attr_e( 'Add to Cart', 'sofir' ); ?>" />
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="variable_text"><?php esc_html_e( 'Variable Products', 'sofir' ); ?></label></th>
			<td>
				<input type="text" id="variable_text" name="sofir_wc_addon_add-to-cart-text_variable_text" value="<?php echo esc_attr( $variable_text ); ?>" class="regular-text" placeholder="<?php esc_attr_e( 'Select Options', 'sofir' ); ?>" />
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="grouped_text"><?php esc_html_e( 'Grouped Products', 'sofir' ); ?></label></th>
			<td>
				<input type="text" id="grouped_text" name="sofir_wc_addon_add-to-cart-text_grouped_text" value="<?php echo esc_attr( $grouped_text ); ?>" class="regular-text" placeholder="<?php esc_attr_e( 'View Products', 'sofir' ); ?>" />
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="external_text"><?php esc_html_e( 'External Products', 'sofir' ); ?></label></th>
			<td>
				<input type="text" id="external_text" name="sofir_wc_addon_add-to-cart-text_external_text" value="<?php echo esc_attr( $external_text ); ?>" class="regular-text" placeholder="<?php esc_attr_e( 'Buy Product', 'sofir' ); ?>" />
			</td>
		</tr>
		<?php
	}
}
