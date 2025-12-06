<?php
namespace Sofir\WooCommerceAddon\Addons;

class Sticky_Add_To_Cart extends Addon_Base {

	public function __construct() {
		$this->id          = 'sticky-add-to-cart';
		$this->name        = \__( 'Sticky Add to Cart', 'sofir' );
		$this->description = \__( 'Make the Add to Cart Button sticky on the top or bottom while shoppers scroll the product pages.', 'sofir' );
		$this->icon        = '📌';
		$this->category    = 'cart';

		parent::__construct();
	}

	public function init(): void {
		\add_action( 'wp_footer', [ $this, 'render_sticky_bar' ] );
		\add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
	}

	public function enqueue_scripts(): void {
		if ( ! \is_product() ) {
			return;
		}

		\wp_add_inline_style( 'woocommerce-general', $this->get_sticky_css() );
		\wp_add_inline_script( 'jquery', $this->get_sticky_js() );
	}

	public function render_sticky_bar(): void {
		if ( ! \is_product() ) {
			return;
		}

		global $product;
		if ( ! $product ) {
			return;
		}

		$position = $this->get_option( 'position', 'bottom' );
		?>
		<div id="sofir-sticky-atc" class="sofir-sticky-atc <?php echo esc_attr( $position ); ?>" style="display: none;">
			<div class="sticky-atc-content">
				<div class="sticky-product-info">
					<?php echo $product->get_image( 'thumbnail' ); ?>
					<div class="sticky-product-details">
						<h4><?php echo esc_html( $product->get_name() ); ?></h4>
						<span class="sticky-price"><?php echo $product->get_price_html(); ?></span>
					</div>
				</div>
				<div class="sticky-atc-button">
					<button type="button" class="button alt" id="sticky-add-to-cart-btn" data-product-id="<?php echo esc_attr( $product->get_id() ); ?>">
						<?php esc_html_e( 'Add to Cart', 'sofir' ); ?>
					</button>
				</div>
			</div>
		</div>
		<?php
	}

	private function get_sticky_css(): string {
		$position = $this->get_option( 'position', 'bottom' );
		$bg_color = $this->get_option( 'bg_color', '#ffffff' );

		return "
		.sofir-sticky-atc {
			position: fixed;
			left: 0;
			right: 0;
			z-index: 9999;
			background: {$bg_color};
			box-shadow: 0 0 10px rgba(0,0,0,0.1);
			padding: 15px 0;
			" . ( $position === 'top' ? 'top: 0;' : 'bottom: 0;' ) . "
		}
		.sofir-sticky-atc.bottom {
			border-top: 1px solid #e0e0e0;
		}
		.sofir-sticky-atc.top {
			border-bottom: 1px solid #e0e0e0;
		}
		.sticky-atc-content {
			max-width: 1200px;
			margin: 0 auto;
			padding: 0 20px;
			display: flex;
			align-items: center;
			justify-content: space-between;
		}
		.sticky-product-info {
			display: flex;
			align-items: center;
			gap: 15px;
		}
		.sticky-product-info img {
			width: 50px;
			height: 50px;
			object-fit: cover;
		}
		.sticky-product-details h4 {
			margin: 0;
			font-size: 14px;
		}
		.sticky-price {
			font-weight: bold;
			color: #000;
		}
		.sticky-atc-button button {
			min-width: 150px;
		}
		";
	}

	private function get_sticky_js(): string {
		$scroll_threshold = $this->get_option( 'scroll_threshold', '300' );

		return "
		jQuery(function($) {
			var stickyBar = $('#sofir-sticky-atc');
			var scrollThreshold = {$scroll_threshold};

			$(window).on('scroll', function() {
				if ($(window).scrollTop() > scrollThreshold) {
					stickyBar.fadeIn();
				} else {
					stickyBar.fadeOut();
				}
			});

			$('#sticky-add-to-cart-btn').on('click', function() {
				$('form.cart button[type=\"submit\"]').first().click();
			});
		});
		";
	}

	public function render_settings(): void {
		$position = $this->get_option( 'position', 'bottom' );
		$scroll_threshold = $this->get_option( 'scroll_threshold', '300' );
		$bg_color = $this->get_option( 'bg_color', '#ffffff' );
		?>
		<tr>
			<th scope="row"><label for="position"><?php esc_html_e( 'Position', 'sofir' ); ?></label></th>
			<td>
				<select id="position" name="sofir_wc_addon_sticky-add-to-cart_position">
					<option value="top" <?php selected( $position, 'top' ); ?>><?php esc_html_e( 'Top', 'sofir' ); ?></option>
					<option value="bottom" <?php selected( $position, 'bottom' ); ?>><?php esc_html_e( 'Bottom', 'sofir' ); ?></option>
				</select>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="scroll_threshold"><?php esc_html_e( 'Scroll Threshold (px)', 'sofir' ); ?></label></th>
			<td>
				<input type="number" id="scroll_threshold" name="sofir_wc_addon_sticky-add-to-cart_scroll_threshold" value="<?php echo esc_attr( $scroll_threshold ); ?>" min="0" step="50" />
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="bg_color"><?php esc_html_e( 'Background Color', 'sofir' ); ?></label></th>
			<td>
				<input type="color" id="bg_color" name="sofir_wc_addon_sticky-add-to-cart_bg_color" value="<?php echo esc_attr( $bg_color ); ?>" />
			</td>
		</tr>
		<?php
	}
}
