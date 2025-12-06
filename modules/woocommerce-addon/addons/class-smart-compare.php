<?php
namespace Sofir\WooCommerceAddon\Addons;

class Smart_Compare extends Addon_Base {

	public function __construct() {
		$this->id          = 'smart_compare';
		$this->name        = \__( 'Smart Compare', 'sofir' );
		$this->description = \__( 'Compare products side-by-side. Help customers make better purchase decisions.', 'sofir' );
		$this->icon        = '⚖️';
		$this->category    = 'products';

		parent::__construct();
	}

	public function init(): void {
		\add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
		\add_action( 'woocommerce_after_shop_loop_item', [ $this, 'add_compare_button' ], 25 );
		\add_action( 'woocommerce_single_product_summary', [ $this, 'add_compare_button' ], 40 );
		\add_action( 'wp_ajax_sofir_add_to_compare', [ $this, 'ajax_add_to_compare' ] );
		\add_action( 'wp_ajax_nopriv_sofir_add_to_compare', [ $this, 'ajax_add_to_compare' ] );
		\add_shortcode( 'sofir_compare', [ $this, 'compare_shortcode' ] );
	}

	public function enqueue_scripts(): void {
		\wp_enqueue_style( 'sofir-compare', SOFIR_PLUGIN_URL . '/modules/woocommerce-addon/assets/compare.css', [], '1.0.0' );
		\wp_enqueue_script( 'sofir-compare', SOFIR_PLUGIN_URL . '/modules/woocommerce-addon/assets/compare.js', [ 'jquery' ], '1.0.0', true );
		
		\wp_localize_script( 'sofir-compare', 'sofirCompare', [
			'ajaxurl' => \admin_url( 'admin-ajax.php' ),
			'nonce'   => \wp_create_nonce( 'sofir_compare_nonce' ),
			'i18n'    => [
				'added'   => \__( 'Added to compare!', 'sofir' ),
				'limit'   => \__( 'You can compare maximum %d products', 'sofir' ),
			],
			'max_products' => (int) $this->get_option( 'max_products', 4 ),
		] );
	}

	public function add_compare_button(): void {
		global $product;

		if ( ! $product ) {
			return;
		}

		$button_text = $this->get_option( 'button_text', \__( 'Compare', 'sofir' ) );

		echo '<button type="button" class="sofir-compare-button" data-product-id="' . esc_attr( $product->get_id() ) . '">';
		echo '<span class="sofir-compare-icon">⚖️</span> ';
		echo '<span class="sofir-compare-text">' . esc_html( $button_text ) . '</span>';
		echo '</button>';
	}

	public function ajax_add_to_compare(): void {
		\check_ajax_referer( 'sofir_compare_nonce', 'nonce' );

		$product_id = isset( $_POST['product_id'] ) ? \absint( $_POST['product_id'] ) : 0;

		if ( ! $product_id ) {
			\wp_send_json_error( [ 'message' => \__( 'Invalid product ID', 'sofir' ) ] );
		}

		$compare_list = $this->get_compare_list();
		$max_products = (int) $this->get_option( 'max_products', 4 );

		if ( count( $compare_list ) >= $max_products ) {
			\wp_send_json_error( [ 'message' => sprintf( \__( 'You can compare maximum %d products', 'sofir' ), $max_products ) ] );
		}

		if ( ! in_array( $product_id, $compare_list, true ) ) {
			$compare_list[] = $product_id;
			$this->save_compare_list( $compare_list );
		}

		\wp_send_json_success( [
			'message' => \__( 'Product added to compare', 'sofir' ),
			'count'   => count( $compare_list ),
		] );
	}

	public function compare_shortcode(): string {
		$compare_list = $this->get_compare_list();

		if ( empty( $compare_list ) ) {
			return '<div class="sofir-compare-empty"><p>' . \esc_html__( 'No products to compare.', 'sofir' ) . '</p></div>';
		}

		$products = array_map( 'wc_get_product', $compare_list );
		$products = array_filter( $products );

		if ( empty( $products ) ) {
			return '<div class="sofir-compare-empty"><p>' . \esc_html__( 'No products to compare.', 'sofir' ) . '</p></div>';
		}

		\ob_start();
		?>
		<div class="sofir-compare-table-wrapper">
			<table class="sofir-compare-table">
				<thead>
					<tr>
						<th><?php esc_html_e( 'Feature', 'sofir' ); ?></th>
						<?php foreach ( $products as $product ) : ?>
						<th>
							<?php echo $product->get_image( 'thumbnail' ); ?>
							<h3><?php echo esc_html( $product->get_name() ); ?></h3>
						</th>
						<?php endforeach; ?>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td><strong><?php esc_html_e( 'Price', 'sofir' ); ?></strong></td>
						<?php foreach ( $products as $product ) : ?>
						<td><?php echo $product->get_price_html(); ?></td>
						<?php endforeach; ?>
					</tr>
					<tr>
						<td><strong><?php esc_html_e( 'Rating', 'sofir' ); ?></strong></td>
						<?php foreach ( $products as $product ) : ?>
						<td><?php echo \wc_get_rating_html( $product->get_average_rating() ); ?></td>
						<?php endforeach; ?>
					</tr>
					<tr>
						<td><strong><?php esc_html_e( 'Availability', 'sofir' ); ?></strong></td>
						<?php foreach ( $products as $product ) : ?>
						<td><?php echo $product->is_in_stock() ? \__( 'In Stock', 'sofir' ) : \__( 'Out of Stock', 'sofir' ); ?></td>
						<?php endforeach; ?>
					</tr>
					<tr>
						<td><strong><?php esc_html_e( 'Description', 'sofir' ); ?></strong></td>
						<?php foreach ( $products as $product ) : ?>
						<td><?php echo \wp_kses_post( \wp_trim_words( $product->get_short_description(), 15 ) ); ?></td>
						<?php endforeach; ?>
					</tr>
					<tr>
						<td><strong><?php esc_html_e( 'Action', 'sofir' ); ?></strong></td>
						<?php foreach ( $products as $product ) : ?>
						<td>
							<a href="<?php echo esc_url( $product->add_to_cart_url() ); ?>" class="button"><?php esc_html_e( 'Add to Cart', 'sofir' ); ?></a>
							<button type="button" class="sofir-remove-compare" data-product-id="<?php echo esc_attr( $product->get_id() ); ?>"><?php esc_html_e( 'Remove', 'sofir' ); ?></button>
						</td>
						<?php endforeach; ?>
					</tr>
				</tbody>
			</table>
		</div>
		<?php
		return \ob_get_clean();
	}

	private function get_compare_list(): array {
		$compare_list = isset( $_COOKIE['sofir_compare'] ) ? \json_decode( \stripslashes( $_COOKIE['sofir_compare'] ), true ) : [];
		return is_array( $compare_list ) ? array_map( 'intval', $compare_list ) : [];
	}

	private function save_compare_list( array $compare_list ): void {
		\setcookie( 'sofir_compare', \wp_json_encode( $compare_list ), time() + ( 30 * DAY_IN_SECONDS ), '/' );
	}

	public function render_settings(): void {
		$button_text = $this->get_option( 'button_text', \__( 'Compare', 'sofir' ) );
		$max_products = $this->get_option( 'max_products', 4 );
		?>
		<tr>
			<th scope="row"><label for="button_text"><?php esc_html_e( 'Button Text', 'sofir' ); ?></label></th>
			<td>
				<input type="text" id="button_text" name="sofir_wc_addon_smart_compare_button_text" value="<?php echo esc_attr( $button_text ); ?>" class="regular-text">
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="max_products"><?php esc_html_e( 'Maximum Products', 'sofir' ); ?></label></th>
			<td>
				<input type="number" id="max_products" name="sofir_wc_addon_smart_compare_max_products" value="<?php echo esc_attr( $max_products ); ?>" min="2" max="10" class="small-text">
				<p class="description"><?php esc_html_e( 'Maximum number of products that can be compared at once', 'sofir' ); ?></p>
			</td>
		</tr>
		<?php
	}
}
