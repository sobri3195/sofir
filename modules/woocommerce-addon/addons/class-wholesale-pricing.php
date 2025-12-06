<?php
namespace Sofir\WooCommerceAddon\Addons;

class Wholesale_Pricing extends Addon_Base {

	public function __construct() {
		$this->id          = 'wholesale_pricing';
		$this->name        = \__( 'Wholesale Pricing', 'sofir' );
		$this->description = \__( 'B2B wholesale pricing tiers. Offer special prices for bulk buyers.', 'sofir' );
		$this->icon        = '💼';
		$this->category    = 'products';

		parent::__construct();
	}

	public function init(): void {
		\add_action( 'woocommerce_product_options_pricing', [ $this, 'add_wholesale_fields' ] );
		\add_action( 'woocommerce_process_product_meta', [ $this, 'save_wholesale_fields' ] );
		\add_filter( 'woocommerce_get_price_html', [ $this, 'wholesale_price_html' ], 10, 2 );
		\add_filter( 'woocommerce_product_get_price', [ $this, 'apply_wholesale_price' ], 10, 2 );
		\add_action( 'woocommerce_single_product_summary', [ $this, 'display_wholesale_table' ], 30 );
	}

	public function add_wholesale_fields(): void {
		echo '<div class="options_group sofir-wholesale-fields">';
		
		\woocommerce_wp_checkbox( [
			'id'          => '_sofir_wholesale_enabled',
			'label'       => \__( 'Enable Wholesale', 'sofir' ),
			'description' => \__( 'Enable wholesale pricing for this product', 'sofir' ),
		] );

		echo '<p class="form-field"><strong>' . \esc_html__( 'Wholesale Price Tiers', 'sofir' ) . '</strong></p>';

		for ( $i = 1; $i <= 3; $i++ ) {
			echo '<div class="sofir-wholesale-tier">';
			\woocommerce_wp_text_input( [
				'id'          => "_sofir_wholesale_qty_{$i}",
				'label'       => sprintf( \__( 'Tier %d - Min Qty', 'sofir' ), $i ),
				'type'        => 'number',
				'placeholder' => $i * 10,
			] );

			\woocommerce_wp_text_input( [
				'id'          => "_sofir_wholesale_price_{$i}",
				'label'       => sprintf( \__( 'Tier %d - Price', 'sofir' ), $i ),
				'type'        => 'number',
				'data_type'   => 'price',
				'placeholder' => \__( 'Wholesale price', 'sofir' ),
			] );
			echo '</div>';
		}

		echo '</div>';
	}

	public function save_wholesale_fields( int $post_id ): void {
		$enabled = isset( $_POST['_sofir_wholesale_enabled'] ) ? 'yes' : 'no';
		\update_post_meta( $post_id, '_sofir_wholesale_enabled', $enabled );

		for ( $i = 1; $i <= 3; $i++ ) {
			if ( isset( $_POST["_sofir_wholesale_qty_{$i}"] ) ) {
				\update_post_meta( $post_id, "_sofir_wholesale_qty_{$i}", \sanitize_text_field( $_POST["_sofir_wholesale_qty_{$i}"] ) );
			}
			if ( isset( $_POST["_sofir_wholesale_price_{$i}"] ) ) {
				\update_post_meta( $post_id, "_sofir_wholesale_price_{$i}", \sanitize_text_field( $_POST["_sofir_wholesale_price_{$i}"] ) );
			}
		}
	}

	public function wholesale_price_html( string $price, $product ): string {
		if ( 'yes' !== \get_post_meta( $product->get_id(), '_sofir_wholesale_enabled', true ) ) {
			return $price;
		}

		$tiers = $this->get_wholesale_tiers( $product->get_id() );
		if ( empty( $tiers ) ) {
			return $price;
		}

		$lowest_price = min( array_column( $tiers, 'price' ) );
		$regular_price = (float) $product->get_regular_price();

		if ( $lowest_price < $regular_price ) {
			return \wc_format_price_range( $lowest_price, $regular_price );
		}

		return $price;
	}

	public function apply_wholesale_price( $price, $product ): float {
		if ( 'yes' !== \get_post_meta( $product->get_id(), '_sofir_wholesale_enabled', true ) ) {
			return $price;
		}

		if ( ! \is_user_logged_in() ) {
			return $price;
		}

		return $price;
	}

	public function display_wholesale_table(): void {
		global $product;

		if ( 'yes' !== \get_post_meta( $product->get_id(), '_sofir_wholesale_enabled', true ) ) {
			return;
		}

		$tiers = $this->get_wholesale_tiers( $product->get_id() );
		if ( empty( $tiers ) ) {
			return;
		}

		?>
		<div class="sofir-wholesale-table">
			<h3><?php esc_html_e( 'Wholesale Pricing', 'sofir' ); ?></h3>
			<table>
				<thead>
					<tr>
						<th><?php esc_html_e( 'Quantity', 'sofir' ); ?></th>
						<th><?php esc_html_e( 'Price per Unit', 'sofir' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( $tiers as $tier ) : ?>
					<tr>
						<td><?php echo esc_html( $tier['qty'] ) . '+'; ?></td>
						<td><?php echo \wc_price( $tier['price'] ); ?></td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
		<?php
	}

	private function get_wholesale_tiers( int $product_id ): array {
		$tiers = [];

		for ( $i = 1; $i <= 3; $i++ ) {
			$qty = \get_post_meta( $product_id, "_sofir_wholesale_qty_{$i}", true );
			$price = \get_post_meta( $product_id, "_sofir_wholesale_price_{$i}", true );

			if ( $qty && $price ) {
				$tiers[] = [
					'qty'   => (int) $qty,
					'price' => (float) $price,
				];
			}
		}

		usort( $tiers, function( $a, $b ) {
			return $a['qty'] - $b['qty'];
		} );

		return $tiers;
	}

	public function render_settings(): void {
		$show_table = $this->get_option( 'show_table', 'yes' );
		$require_login = $this->get_option( 'require_login', 'no' );
		?>
		<tr>
			<th scope="row"><?php esc_html_e( 'Show Pricing Table', 'sofir' ); ?></th>
			<td>
				<label>
					<input type="checkbox" name="sofir_wc_addon_wholesale_pricing_show_table" value="yes" <?php checked( $show_table, 'yes' ); ?>>
					<?php esc_html_e( 'Display wholesale pricing table on product page', 'sofir' ); ?>
				</label>
			</td>
		</tr>
		<tr>
			<th scope="row"><?php esc_html_e( 'Require Login', 'sofir' ); ?></th>
			<td>
				<label>
					<input type="checkbox" name="sofir_wc_addon_wholesale_pricing_require_login" value="yes" <?php checked( $require_login, 'yes' ); ?>>
					<?php esc_html_e( 'Customers must be logged in to see wholesale prices', 'sofir' ); ?>
				</label>
			</td>
		</tr>
		<?php
	}
}
