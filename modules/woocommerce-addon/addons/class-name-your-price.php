<?php
namespace Sofir\WooCommerceAddon\Addons;

class Name_Your_Price extends Addon_Base {

	public function __construct() {
		$this->id          = 'name-your-price';
		$this->name        = \__( 'Name Your Price', 'sofir' );
		$this->description = \__( 'Let customers purchase products at their desired prices. Add Min & Max restrictions if required.', 'sofir' );
		$this->icon        = '💰';
		$this->category    = 'sales';

		parent::__construct();
	}

	public function init(): void {
		\add_action( 'woocommerce_before_add_to_cart_button', [ $this, 'add_price_field' ] );
		\add_filter( 'woocommerce_add_cart_item_data', [ $this, 'add_custom_price_to_cart' ], 10, 2 );
		\add_action( 'woocommerce_before_calculate_totals', [ $this, 'update_cart_price' ] );
		\add_action( 'woocommerce_product_options_pricing', [ $this, 'add_product_fields' ] );
		\add_action( 'woocommerce_process_product_meta', [ $this, 'save_product_fields' ] );
	}

	public function add_product_fields(): void {
		?>
		<div class="options_group">
			<?php
			\woocommerce_wp_checkbox( [
				'id' => '_enable_name_your_price',
				'label' => \__( 'Enable Name Your Price', 'sofir' ),
				'description' => \__( 'Let customers choose their own price', 'sofir' ),
			] );

			\woocommerce_wp_text_input( [
				'id' => '_min_price',
				'label' => \__( 'Minimum Price', 'sofir' ),
				'type' => 'number',
				'custom_attributes' => [ 'step' => '0.01', 'min' => '0' ],
			] );

			\woocommerce_wp_text_input( [
				'id' => '_max_price',
				'label' => \__( 'Maximum Price', 'sofir' ),
				'type' => 'number',
				'custom_attributes' => [ 'step' => '0.01', 'min' => '0' ],
			] );
			?>
		</div>
		<?php
	}

	public function save_product_fields( int $post_id ): void {
		$enable = isset( $_POST['_enable_name_your_price'] ) ? 'yes' : 'no';
		\update_post_meta( $post_id, '_enable_name_your_price', $enable );

		$min_price = isset( $_POST['_min_price'] ) ? \sanitize_text_field( $_POST['_min_price'] ) : '';
		\update_post_meta( $post_id, '_min_price', $min_price );

		$max_price = isset( $_POST['_max_price'] ) ? \sanitize_text_field( $_POST['_max_price'] ) : '';
		\update_post_meta( $post_id, '_max_price', $max_price );
	}

	public function add_price_field(): void {
		global $product;

		$enabled = \get_post_meta( $product->get_id(), '_enable_name_your_price', true );

		if ( $enabled !== 'yes' ) {
			return;
		}

		$min_price = \get_post_meta( $product->get_id(), '_min_price', true );
		$max_price = \get_post_meta( $product->get_id(), '_max_price', true );

		?>
		<div class="sofir-name-your-price">
			<label for="custom-price"><?php esc_html_e( 'Name Your Price', 'sofir' ); ?></label>
			<input type="number" id="custom-price" name="custom_price" step="0.01" min="<?php echo esc_attr( $min_price ?: '0' ); ?>" <?php echo $max_price ? 'max="' . esc_attr( $max_price ) . '"' : ''; ?> required />
			<?php if ( $min_price || $max_price ) : ?>
			<small class="price-range">
				<?php
				if ( $min_price && $max_price ) {
					echo sprintf( esc_html__( 'Between %s and %s', 'sofir' ), \wc_price( $min_price ), \wc_price( $max_price ) );
				} elseif ( $min_price ) {
					echo sprintf( esc_html__( 'Minimum %s', 'sofir' ), \wc_price( $min_price ) );
				} elseif ( $max_price ) {
					echo sprintf( esc_html__( 'Maximum %s', 'sofir' ), \wc_price( $max_price ) );
				}
				?>
			</small>
			<?php endif; ?>
		</div>
		<?php
	}

	public function add_custom_price_to_cart( array $cart_item_data, int $product_id ): array {
		if ( isset( $_POST['custom_price'] ) ) {
			$custom_price = \floatval( $_POST['custom_price'] );
			$cart_item_data['custom_price'] = $custom_price;
		}
		return $cart_item_data;
	}

	public function update_cart_price( $cart ): void {
		foreach ( $cart->get_cart() as $cart_item ) {
			if ( isset( $cart_item['custom_price'] ) ) {
				$cart_item['data']->set_price( $cart_item['custom_price'] );
			}
		}
	}

	public function render_settings(): void {
		$default_min = $this->get_option( 'default_min', '1' );
		?>
		<tr>
			<th scope="row"><label for="default_min"><?php esc_html_e( 'Default Minimum Price', 'sofir' ); ?></label></th>
			<td>
				<input type="number" id="default_min" name="sofir_wc_addon_name-your-price_default_min" value="<?php echo esc_attr( $default_min ); ?>" step="0.01" min="0" />
			</td>
		</tr>
		<?php
	}
}
