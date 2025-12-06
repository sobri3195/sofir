<?php
namespace Sofir\WooCommerceAddon\Addons;

class Backorder extends Addon_Base {

	public function __construct() {
		$this->id          = 'backorder';
		$this->name        = \__( 'Backorder', 'sofir' );
		$this->description = \__( 'Keep getting orders for the products that are currently out of stock and will be restocked soon.', 'sofir' );
		$this->icon        = '📦';
		$this->category    = 'sales';

		parent::__construct();
	}

	public function init(): void {
		\add_filter( 'woocommerce_product_backorders_allowed', [ $this, 'enable_backorders' ], 10, 3 );
		\add_filter( 'woocommerce_get_availability_text', [ $this, 'backorder_availability_text' ], 10, 2 );
		\add_action( 'woocommerce_product_options_stock_status', [ $this, 'add_backorder_field' ] );
		\add_action( 'woocommerce_process_product_meta', [ $this, 'save_backorder_field' ] );
	}

	public function enable_backorders( bool $backorders_allowed, int $product_id, $product ): bool {
		$enable_backorder = \get_post_meta( $product_id, '_sofir_enable_backorder', true );
		
		if ( $enable_backorder === 'yes' ) {
			return true;
		}

		return $backorders_allowed;
	}

	public function backorder_availability_text( string $availability, $product ): string {
		if ( ! $product->is_in_stock() && $product->backorders_allowed() ) {
			$custom_text = $this->get_option( 'availability_text', 'Available on backorder' );
			return $custom_text;
		}

		return $availability;
	}

	public function add_backorder_field(): void {
		?>
		<div class="options_group">
			<?php
			\woocommerce_wp_checkbox( [
				'id' => '_sofir_enable_backorder',
				'label' => \__( 'Enable Backorder', 'sofir' ),
				'description' => \__( 'Allow customers to order this product when out of stock', 'sofir' ),
			] );

			\woocommerce_wp_text_input( [
				'id' => '_sofir_backorder_date',
				'label' => \__( 'Expected Restock Date', 'sofir' ),
				'type' => 'date',
				'desc_tip' => true,
				'description' => \__( 'Estimated date when product will be back in stock', 'sofir' ),
			] );
			?>
		</div>
		<?php
	}

	public function save_backorder_field( int $post_id ): void {
		$enable = isset( $_POST['_sofir_enable_backorder'] ) ? 'yes' : 'no';
		\update_post_meta( $post_id, '_sofir_enable_backorder', $enable );

		$date = isset( $_POST['_sofir_backorder_date'] ) ? \sanitize_text_field( $_POST['_sofir_backorder_date'] ) : '';
		\update_post_meta( $post_id, '_sofir_backorder_date', $date );
	}

	public function render_settings(): void {
		$availability_text = $this->get_option( 'availability_text', 'Available on backorder' );
		$enable_notification = $this->get_option( 'enable_notification', 'yes' );
		?>
		<tr>
			<th scope="row"><label for="availability_text"><?php esc_html_e( 'Availability Text', 'sofir' ); ?></label></th>
			<td>
				<input type="text" id="availability_text" name="sofir_wc_addon_backorder_availability_text" value="<?php echo esc_attr( $availability_text ); ?>" class="regular-text" />
			</td>
		</tr>
		<tr>
			<th scope="row"><?php esc_html_e( 'Email Notification', 'sofir' ); ?></th>
			<td>
				<label>
					<input type="checkbox" name="sofir_wc_addon_backorder_enable_notification" value="yes" <?php checked( $enable_notification, 'yes' ); ?>>
					<?php esc_html_e( 'Send email when backordered items are restocked', 'sofir' ); ?>
				</label>
			</td>
		</tr>
		<?php
	}
}
