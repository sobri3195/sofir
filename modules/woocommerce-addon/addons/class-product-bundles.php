<?php
namespace Sofir\WooCommerceAddon\Addons;

class Product_Bundles extends Addon_Base {

	public function __construct() {
		$this->id          = 'product_bundles';
		$this->name        = \__( 'Product Bundles', 'sofir' );
		$this->description = \__( 'Create product bundles with discounted pricing. Sell multiple products as a package.', 'sofir' );
		$this->icon        = '📦';
		$this->category    = 'products';

		parent::__construct();
	}

	public function init(): void {
		\add_action( 'woocommerce_product_options_general_product_data', [ $this, 'add_bundle_fields' ] );
		\add_action( 'woocommerce_process_product_meta', [ $this, 'save_bundle_fields' ] );
		\add_filter( 'woocommerce_product_data_tabs', [ $this, 'add_bundle_tab' ] );
		\add_action( 'woocommerce_product_data_panels', [ $this, 'bundle_tab_content' ] );
		\add_filter( 'woocommerce_get_price_html', [ $this, 'bundle_price_html' ], 10, 2 );
		\add_action( 'woocommerce_before_add_to_cart_button', [ $this, 'display_bundle_products' ] );
		\add_filter( 'woocommerce_add_to_cart_validation', [ $this, 'add_bundle_to_cart' ], 10, 3 );
	}

	public function add_bundle_tab( array $tabs ): array {
		$tabs['sofir_bundle'] = [
			'label'    => \__( 'Product Bundle', 'sofir' ),
			'target'   => 'sofir_bundle_product_data',
			'class'    => [ 'show_if_simple' ],
			'priority' => 60,
		];
		return $tabs;
	}

	public function bundle_tab_content(): void {
		global $post;
		?>
		<div id="sofir_bundle_product_data" class="panel woocommerce_options_panel">
			<div class="options_group">
				<?php
				\woocommerce_wp_checkbox( [
					'id'          => '_sofir_is_bundle',
					'label'       => \__( 'Bundle Product', 'sofir' ),
					'description' => \__( 'Enable this product as a bundle', 'sofir' ),
				] );

				\woocommerce_wp_text_input( [
					'id'          => '_sofir_bundle_discount',
					'label'       => \__( 'Bundle Discount (%)', 'sofir' ),
					'type'        => 'number',
					'custom_attributes' => [
						'step' => '1',
						'min'  => '0',
						'max'  => '100',
					],
					'description' => \__( 'Discount percentage for the bundle', 'sofir' ),
				] );
				?>
				
				<p class="form-field">
					<label><?php esc_html_e( 'Bundle Products', 'sofir' ); ?></label>
					<select id="sofir_bundle_products" name="_sofir_bundle_products[]" multiple="multiple" style="width: 50%;" data-placeholder="<?php esc_attr_e( 'Search products...', 'sofir' ); ?>">
						<?php
						$bundle_products = \get_post_meta( $post->ID, '_sofir_bundle_products', true );
						if ( ! empty( $bundle_products ) ) {
							foreach ( $bundle_products as $product_id ) {
								$product = \wc_get_product( $product_id );
								if ( $product ) {
									echo '<option value="' . esc_attr( $product_id ) . '" selected="selected">' . esc_html( $product->get_name() ) . '</option>';
								}
							}
						}
						?>
					</select>
				</p>
			</div>
		</div>
		<script>
		jQuery(document).ready(function($) {
			$('#sofir_bundle_products').select2({
				ajax: {
					url: ajaxurl,
					dataType: 'json',
					delay: 250,
					data: function(params) {
						return {
							action: 'sofir_search_products',
							security: '<?php echo esc_js( \wp_create_nonce( 'search-products' ) ); ?>',
							term: params.term
						};
					},
					processResults: function(data) {
						return {
							results: data
						};
					}
				},
				minimumInputLength: 2
			});
		});
		</script>
		<?php
	}

	public function add_bundle_fields(): void {
	}

	public function save_bundle_fields( int $post_id ): void {
		$is_bundle = isset( $_POST['_sofir_is_bundle'] ) ? 'yes' : 'no';
		\update_post_meta( $post_id, '_sofir_is_bundle', $is_bundle );

		if ( isset( $_POST['_sofir_bundle_discount'] ) ) {
			\update_post_meta( $post_id, '_sofir_bundle_discount', \sanitize_text_field( $_POST['_sofir_bundle_discount'] ) );
		}

		if ( isset( $_POST['_sofir_bundle_products'] ) ) {
			$bundle_products = array_map( 'intval', (array) $_POST['_sofir_bundle_products'] );
			\update_post_meta( $post_id, '_sofir_bundle_products', $bundle_products );
		}
	}

	public function bundle_price_html( string $price, $product ): string {
		if ( 'yes' !== \get_post_meta( $product->get_id(), '_sofir_is_bundle', true ) ) {
			return $price;
		}

		$bundle_products = \get_post_meta( $product->get_id(), '_sofir_bundle_products', true );
		$discount = (float) \get_post_meta( $product->get_id(), '_sofir_bundle_discount', true );

		if ( empty( $bundle_products ) ) {
			return $price;
		}

		$total_price = 0;
		foreach ( $bundle_products as $product_id ) {
			$bundle_product = \wc_get_product( $product_id );
			if ( $bundle_product ) {
				$total_price += (float) $bundle_product->get_price();
			}
		}

		if ( $discount > 0 ) {
			$discounted_price = $total_price * ( 1 - ( $discount / 100 ) );
			return \wc_format_sale_price( $total_price, $discounted_price ) . ' <span class="sofir-bundle-savings">' . sprintf( \__( 'Save %s%%', 'sofir' ), $discount ) . '</span>';
		}

		return \wc_price( $total_price );
	}

	public function display_bundle_products(): void {
		global $product;

		if ( 'yes' !== \get_post_meta( $product->get_id(), '_sofir_is_bundle', true ) ) {
			return;
		}

		$bundle_products = \get_post_meta( $product->get_id(), '_sofir_bundle_products', true );
		if ( empty( $bundle_products ) ) {
			return;
		}

		echo '<div class="sofir-bundle-products">';
		echo '<h4>' . esc_html__( 'This bundle includes:', 'sofir' ) . '</h4>';
		echo '<ul class="sofir-bundle-list">';

		foreach ( $bundle_products as $product_id ) {
			$bundle_product = \wc_get_product( $product_id );
			if ( $bundle_product ) {
				echo '<li>';
				echo '<strong>' . esc_html( $bundle_product->get_name() ) . '</strong>';
				echo ' - ' . $bundle_product->get_price_html();
				echo '</li>';
			}
		}

		echo '</ul>';
		echo '</div>';
	}

	public function add_bundle_to_cart( bool $passed, int $product_id, int $quantity ): bool {
		if ( 'yes' !== \get_post_meta( $product_id, '_sofir_is_bundle', true ) ) {
			return $passed;
		}

		return $passed;
	}

	public function render_settings(): void {
		$show_bundle_badge = $this->get_option( 'show_bundle_badge', 'yes' );
		$bundle_badge_text = $this->get_option( 'bundle_badge_text', \__( 'Bundle', 'sofir' ) );
		?>
		<tr>
			<th scope="row"><?php esc_html_e( 'Show Bundle Badge', 'sofir' ); ?></th>
			<td>
				<label>
					<input type="checkbox" name="sofir_wc_addon_product_bundles_show_bundle_badge" value="yes" <?php checked( $show_bundle_badge, 'yes' ); ?>>
					<?php esc_html_e( 'Display a badge on bundle products', 'sofir' ); ?>
				</label>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="bundle_badge_text"><?php esc_html_e( 'Bundle Badge Text', 'sofir' ); ?></label></th>
			<td>
				<input type="text" id="bundle_badge_text" name="sofir_wc_addon_product_bundles_bundle_badge_text" value="<?php echo esc_attr( $bundle_badge_text ); ?>" class="regular-text">
				<p class="description"><?php esc_html_e( 'Text to display on the bundle badge', 'sofir' ); ?></p>
			</td>
		</tr>
		<?php
	}
}
