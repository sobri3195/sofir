<?php
namespace Sofir\WooCommerceAddon\Addons;

class Pre_Order extends Addon_Base {

	public function __construct() {
		$this->id          = 'pre_order';
		$this->name        = \__( 'Pre-Order', 'sofir' );
		$this->description = \__( 'Allow customers to pre-order out-of-stock products. Never miss a sale.', 'sofir' );
		$this->icon        = '📅';
		$this->category    = 'products';

		parent::__construct();
	}

	public function init(): void {
		\add_action( 'woocommerce_product_options_inventory_product_data', [ $this, 'add_preorder_fields' ] );
		\add_action( 'woocommerce_process_product_meta', [ $this, 'save_preorder_fields' ] );
		\add_filter( 'woocommerce_product_single_add_to_cart_text', [ $this, 'preorder_button_text' ], 10, 2 );
		\add_filter( 'woocommerce_product_add_to_cart_text', [ $this, 'preorder_button_text' ], 10, 2 );
		\add_filter( 'woocommerce_is_purchasable', [ $this, 'make_preorder_purchasable' ], 10, 2 );
		\add_action( 'woocommerce_single_product_summary', [ $this, 'display_preorder_message' ], 25 );
	}

	public function add_preorder_fields(): void {
		global $post;
		
		echo '<div class="options_group sofir-preorder-fields">';
		
		\woocommerce_wp_checkbox( [
			'id'          => '_sofir_preorder_enabled',
			'label'       => \__( 'Enable Pre-Order', 'sofir' ),
			'description' => \__( 'Allow pre-orders when product is out of stock', 'sofir' ),
		] );

		\woocommerce_wp_text_input( [
			'id'          => '_sofir_preorder_date',
			'label'       => \__( 'Expected Availability Date', 'sofir' ),
			'type'        => 'date',
			'description' => \__( 'When will the product be available', 'sofir' ),
		] );

		\woocommerce_wp_text_input( [
			'id'          => '_sofir_preorder_fee',
			'label'       => \__( 'Pre-Order Fee (%)', 'sofir' ),
			'type'        => 'number',
			'custom_attributes' => [
				'step' => '0.01',
				'min'  => '0',
			],
			'description' => \__( 'Extra fee percentage for pre-orders', 'sofir' ),
		] );

		echo '</div>';
	}

	public function save_preorder_fields( int $post_id ): void {
		$enabled = isset( $_POST['_sofir_preorder_enabled'] ) ? 'yes' : 'no';
		\update_post_meta( $post_id, '_sofir_preorder_enabled', $enabled );

		if ( isset( $_POST['_sofir_preorder_date'] ) ) {
			\update_post_meta( $post_id, '_sofir_preorder_date', \sanitize_text_field( $_POST['_sofir_preorder_date'] ) );
		}

		if ( isset( $_POST['_sofir_preorder_fee'] ) ) {
			\update_post_meta( $post_id, '_sofir_preorder_fee', \sanitize_text_field( $_POST['_sofir_preorder_fee'] ) );
		}
	}

	public function preorder_button_text( string $text, $product ): string {
		if ( 'yes' === \get_post_meta( $product->get_id(), '_sofir_preorder_enabled', true ) && ! $product->is_in_stock() ) {
			return $this->get_option( 'button_text', \__( 'Pre-Order Now', 'sofir' ) );
		}
		return $text;
	}

	public function make_preorder_purchasable( bool $purchasable, $product ): bool {
		if ( 'yes' === \get_post_meta( $product->get_id(), '_sofir_preorder_enabled', true ) ) {
			return true;
		}
		return $purchasable;
	}

	public function display_preorder_message(): void {
		global $product;

		if ( 'yes' !== \get_post_meta( $product->get_id(), '_sofir_preorder_enabled', true ) || $product->is_in_stock() ) {
			return;
		}

		$date = \get_post_meta( $product->get_id(), '_sofir_preorder_date', true );

		$message = \__( '📅 This is a pre-order product.', 'sofir' );
		if ( $date ) {
			$message .= ' ' . sprintf( \__( 'Expected availability: %s', 'sofir' ), \date_i18n( \get_option( 'date_format' ), \strtotime( $date ) ) );
		}

		echo '<div class="sofir-preorder-notice" style="background: #FFF3CD; border: 1px solid #FFE69C; color: #856404; padding: 12px; border-radius: 6px; margin: 15px 0;">';
		echo esc_html( $message );
		echo '</div>';
	}

	public function render_settings(): void {
		$button_text = $this->get_option( 'button_text', \__( 'Pre-Order Now', 'sofir' ) );
		$require_payment = $this->get_option( 'require_payment', 'yes' );
		?>
		<tr>
			<th scope="row"><label for="button_text"><?php esc_html_e( 'Button Text', 'sofir' ); ?></label></th>
			<td>
				<input type="text" id="button_text" name="sofir_wc_addon_pre_order_button_text" value="<?php echo esc_attr( $button_text ); ?>" class="regular-text">
			</td>
		</tr>
		<tr>
			<th scope="row"><?php esc_html_e( 'Require Payment', 'sofir' ); ?></th>
			<td>
				<label>
					<input type="checkbox" name="sofir_wc_addon_pre_order_require_payment" value="yes" <?php checked( $require_payment, 'yes' ); ?>>
					<?php esc_html_e( 'Require full payment for pre-orders', 'sofir' ); ?>
				</label>
			</td>
		</tr>
		<?php
	}
}
