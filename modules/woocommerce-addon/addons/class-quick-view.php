<?php
namespace Sofir\WooCommerceAddon\Addons;

class Quick_View extends Addon_Base {

	public function __construct() {
		$this->id          = 'quick_view';
		$this->name        = \__( 'Quick View', 'sofir' );
		$this->description = \__( 'Quick view popup for products. Let customers preview products without leaving the shop page.', 'sofir' );
		$this->icon        = '👁️';
		$this->category    = 'products';

		parent::__construct();
	}

	public function init(): void {
		\add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
		\add_action( 'woocommerce_after_shop_loop_item', [ $this, 'add_quick_view_button' ], 20 );
		\add_action( 'wp_ajax_sofir_quick_view', [ $this, 'ajax_quick_view' ] );
		\add_action( 'wp_ajax_nopriv_sofir_quick_view', [ $this, 'ajax_quick_view' ] );
		\add_action( 'wp_footer', [ $this, 'quick_view_modal' ] );
	}

	public function enqueue_scripts(): void {
		\wp_enqueue_style( 'sofir-quick-view', SOFIR_PLUGIN_URL . '/modules/woocommerce-addon/assets/quick-view.css', [], '1.0.0' );
		\wp_enqueue_script( 'sofir-quick-view', SOFIR_PLUGIN_URL . '/modules/woocommerce-addon/assets/quick-view.js', [ 'jquery' ], '1.0.0', true );
		
		\wp_localize_script( 'sofir-quick-view', 'sofirQuickView', [
			'ajaxurl' => \admin_url( 'admin-ajax.php' ),
			'nonce'   => \wp_create_nonce( 'sofir_quick_view_nonce' ),
			'i18n'    => [
				'loading' => \__( 'Loading...', 'sofir' ),
				'error'   => \__( 'Error loading product', 'sofir' ),
			],
		] );
	}

	public function add_quick_view_button(): void {
		global $product;

		if ( ! $product ) {
			return;
		}

		$button_text = $this->get_option( 'button_text', \__( 'Quick View', 'sofir' ) );

		echo '<button type="button" class="sofir-quick-view-button" data-product-id="' . esc_attr( $product->get_id() ) . '">';
		echo '<span class="sofir-qv-icon">👁️</span> ';
		echo '<span class="sofir-qv-text">' . esc_html( $button_text ) . '</span>';
		echo '</button>';
	}

	public function ajax_quick_view(): void {
		\check_ajax_referer( 'sofir_quick_view_nonce', 'nonce' );

		$product_id = isset( $_POST['product_id'] ) ? \absint( $_POST['product_id'] ) : 0;

		if ( ! $product_id ) {
			\wp_send_json_error( [ 'message' => \__( 'Invalid product ID', 'sofir' ) ] );
		}

		global $product;
		$product = \wc_get_product( $product_id );

		if ( ! $product ) {
			\wp_send_json_error( [ 'message' => \__( 'Product not found', 'sofir' ) ] );
		}

		\ob_start();
		\wc_get_template_part( 'content', 'quick-view-product' );
		$html = \ob_get_clean();

		if ( empty( $html ) ) {
			\ob_start();
			$this->render_quick_view_content( $product );
			$html = \ob_get_clean();
		}

		\wp_send_json_success( [ 'html' => $html ] );
	}

	private function render_quick_view_content( $product ): void {
		?>
		<div class="sofir-quick-view-content">
			<div class="sofir-qv-images">
				<?php echo $product->get_image( 'woocommerce_single' ); ?>
			</div>
			<div class="sofir-qv-summary">
				<h2 class="product_title entry-title"><?php echo esc_html( $product->get_name() ); ?></h2>
				
				<div class="sofir-qv-price">
					<?php echo $product->get_price_html(); ?>
				</div>

				<?php if ( $product->get_rating_count() > 0 ) : ?>
				<div class="woocommerce-product-rating">
					<?php \wc_get_template( 'single-product/rating.php' ); ?>
				</div>
				<?php endif; ?>

				<div class="sofir-qv-excerpt">
					<?php echo \wp_kses_post( \wp_trim_words( $product->get_short_description(), 20 ) ); ?>
				</div>

				<?php if ( $product->is_in_stock() ) : ?>
				<form class="cart" action="<?php echo esc_url( $product->get_permalink() ); ?>" method="post" enctype='multipart/form-data'>
					<?php
					\do_action( 'woocommerce_before_add_to_cart_button' );

					if ( $product->is_type( 'simple' ) ) {
						\woocommerce_quantity_input( [
							'min_value'   => 1,
							'max_value'   => $product->get_max_purchase_quantity(),
							'input_value' => 1,
						] );
					}

					echo '<button type="submit" name="add-to-cart" value="' . esc_attr( $product->get_id() ) . '" class="single_add_to_cart_button button alt">' . esc_html( $product->single_add_to_cart_text() ) . '</button>';

					\do_action( 'woocommerce_after_add_to_cart_button' );
					?>
				</form>
				<?php else : ?>
				<p class="stock out-of-stock"><?php esc_html_e( 'Out of stock', 'sofir' ); ?></p>
				<?php endif; ?>

				<div class="sofir-qv-meta">
					<?php if ( $product->get_sku() ) : ?>
					<span class="sku_wrapper"><?php esc_html_e( 'SKU:', 'sofir' ); ?> <span class="sku"><?php echo esc_html( $product->get_sku() ); ?></span></span>
					<?php endif; ?>

					<?php echo \wc_get_product_category_list( $product->get_id(), ', ', '<span class="posted_in">' . _n( 'Category:', 'Categories:', count( $product->get_category_ids() ), 'sofir' ) . ' ', '</span>' ); ?>
				</div>

				<a href="<?php echo esc_url( $product->get_permalink() ); ?>" class="sofir-qv-details-link">
					<?php esc_html_e( 'View Full Details', 'sofir' ); ?> →
				</a>
			</div>
		</div>
		<?php
	}

	public function quick_view_modal(): void {
		?>
		<div id="sofir-quick-view-modal" class="sofir-qv-modal" style="display: none;">
			<div class="sofir-qv-overlay"></div>
			<div class="sofir-qv-container">
				<button type="button" class="sofir-qv-close">&times;</button>
				<div class="sofir-qv-inner">
					<div class="sofir-qv-loading">
						<span class="spinner"></span>
						<p><?php esc_html_e( 'Loading product...', 'sofir' ); ?></p>
					</div>
					<div class="sofir-qv-product-content"></div>
				</div>
			</div>
		</div>
		<?php
	}

	public function render_settings(): void {
		$button_text = $this->get_option( 'button_text', \__( 'Quick View', 'sofir' ) );
		$show_icon = $this->get_option( 'show_icon', 'yes' );
		$animation = $this->get_option( 'animation', 'fade' );
		?>
		<tr>
			<th scope="row"><label for="button_text"><?php esc_html_e( 'Button Text', 'sofir' ); ?></label></th>
			<td>
				<input type="text" id="button_text" name="sofir_wc_addon_quick_view_button_text" value="<?php echo esc_attr( $button_text ); ?>" class="regular-text">
			</td>
		</tr>
		<tr>
			<th scope="row"><?php esc_html_e( 'Show Icon', 'sofir' ); ?></th>
			<td>
				<label>
					<input type="checkbox" name="sofir_wc_addon_quick_view_show_icon" value="yes" <?php checked( $show_icon, 'yes' ); ?>>
					<?php esc_html_e( 'Show eye icon on button', 'sofir' ); ?>
				</label>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="animation"><?php esc_html_e( 'Modal Animation', 'sofir' ); ?></label></th>
			<td>
				<select id="animation" name="sofir_wc_addon_quick_view_animation">
					<option value="fade" <?php selected( $animation, 'fade' ); ?>><?php esc_html_e( 'Fade In', 'sofir' ); ?></option>
					<option value="slide" <?php selected( $animation, 'slide' ); ?>><?php esc_html_e( 'Slide Down', 'sofir' ); ?></option>
					<option value="zoom" <?php selected( $animation, 'zoom' ); ?>><?php esc_html_e( 'Zoom In', 'sofir' ); ?></option>
				</select>
			</td>
		</tr>
		<?php
	}
}
