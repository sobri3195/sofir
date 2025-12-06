<?php
namespace Sofir\WooCommerceAddon\Addons;

class Stock_Progress_Bar extends Addon_Base {

	public function __construct() {
		$this->id          = 'stock-progress-bar';
		$this->name        = \__( 'Stock Progress Bar', 'sofir' );
		$this->description = \__( 'Visually highlight the total and remaining stocks of products to encourage shoppers to create FOMO.', 'sofir' );
		$this->icon        = '📊';
		$this->category    = 'sales';

		parent::__construct();
	}

	public function init(): void {
		\add_action( 'woocommerce_single_product_summary', [ $this, 'render_stock_bar' ], 25 );
		\add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
	}

	public function enqueue_scripts(): void {
		\wp_add_inline_style( 'woocommerce-general', $this->get_stock_css() );
	}

	public function render_stock_bar(): void {
		global $product;

		if ( ! $product || ! $product->managing_stock() ) {
			return;
		}

		$stock_quantity = $product->get_stock_quantity();
		$initial_stock = (int) \get_post_meta( $product->get_id(), '_sofir_initial_stock', true );

		if ( ! $initial_stock ) {
			$initial_stock = $stock_quantity;
			\update_post_meta( $product->get_id(), '_sofir_initial_stock', $initial_stock );
		}

		$percentage = $initial_stock > 0 ? ( $stock_quantity / $initial_stock ) * 100 : 0;
		$sold = $initial_stock - $stock_quantity;

		$color = $this->get_bar_color( $percentage );
		$message = $this->get_stock_message( $stock_quantity, $sold, $initial_stock );

		?>
		<div class="sofir-stock-progress">
			<div class="stock-info">
				<span class="stock-label"><?php echo esc_html( $message ); ?></span>
				<span class="stock-count"><?php echo sprintf( esc_html__( '%d left', 'sofir' ), $stock_quantity ); ?></span>
			</div>
			<div class="stock-bar">
				<div class="stock-bar-fill" style="width: <?php echo esc_attr( $percentage ); ?>%; background-color: <?php echo esc_attr( $color ); ?>;"></div>
			</div>
			<?php if ( $this->get_option( 'show_sold_count', 'yes' ) === 'yes' ) : ?>
			<div class="stock-sold">
				<span>🔥 <?php echo sprintf( esc_html__( '%d sold', 'sofir' ), $sold ); ?></span>
			</div>
			<?php endif; ?>
		</div>
		<?php
	}

	private function get_bar_color( float $percentage ): string {
		if ( $percentage > 50 ) {
			return '#4caf50';
		} elseif ( $percentage > 20 ) {
			return '#ff9800';
		}
		return '#f44336';
	}

	private function get_stock_message( int $stock, int $sold, int $initial ): string {
		if ( $stock < 5 ) {
			return \__( 'Hurry! Only a few left!', 'sofir' );
		} elseif ( $stock < 20 ) {
			return \__( 'Low stock! Order soon', 'sofir' );
		}
		return \__( 'In Stock', 'sofir' );
	}

	private function get_stock_css(): string {
		return '
		.sofir-stock-progress {
			margin: 20px 0;
			padding: 15px;
			background: #f9f9f9;
			border-radius: 8px;
		}
		.stock-info {
			display: flex;
			justify-content: space-between;
			margin-bottom: 10px;
			font-size: 14px;
		}
		.stock-label {
			font-weight: 600;
		}
		.stock-count {
			color: #666;
		}
		.stock-bar {
			height: 10px;
			background: #e0e0e0;
			border-radius: 5px;
			overflow: hidden;
		}
		.stock-bar-fill {
			height: 100%;
			transition: width 0.3s ease;
		}
		.stock-sold {
			margin-top: 10px;
			text-align: center;
			font-weight: 600;
			color: #ff5722;
		}
		';
	}

	public function render_settings(): void {
		$show_sold_count = $this->get_option( 'show_sold_count', 'yes' );
		$enable_fomo = $this->get_option( 'enable_fomo', 'yes' );
		?>
		<tr>
			<th scope="row"><?php esc_html_e( 'Show Sold Count', 'sofir' ); ?></th>
			<td>
				<label>
					<input type="checkbox" name="sofir_wc_addon_stock-progress-bar_show_sold_count" value="yes" <?php checked( $show_sold_count, 'yes' ); ?>>
					<?php esc_html_e( 'Display how many items have been sold', 'sofir' ); ?>
				</label>
			</td>
		</tr>
		<tr>
			<th scope="row"><?php esc_html_e( 'Enable FOMO Messages', 'sofir' ); ?></th>
			<td>
				<label>
					<input type="checkbox" name="sofir_wc_addon_stock-progress-bar_enable_fomo" value="yes" <?php checked( $enable_fomo, 'yes' ); ?>>
					<?php esc_html_e( 'Show urgency messages when stock is low', 'sofir' ); ?>
				</label>
			</td>
		</tr>
		<?php
	}
}
