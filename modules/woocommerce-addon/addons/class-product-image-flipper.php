<?php
namespace Sofir\WooCommerceAddon\Addons;

class Product_Image_Flipper extends Addon_Base {

	public function __construct() {
		$this->id          = 'product-image-flipper';
		$this->name        = \__( 'Product Image Flipper', 'sofir' );
		$this->description = \__( 'Display a different image of products when the shoppers hover over a product.', 'sofir' );
		$this->icon        = '🔄';
		$this->category    = 'flexibility';

		parent::__construct();
	}

	public function init(): void {
		\add_action( 'woocommerce_before_shop_loop_item_title', [ $this, 'add_flip_image' ], 10 );
		\add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
	}

	public function enqueue_scripts(): void {
		\wp_add_inline_style( 'woocommerce-general', $this->get_flipper_css() );
	}

	public function add_flip_image(): void {
		global $product;

		$gallery_images = $product->get_gallery_image_ids();

		if ( empty( $gallery_images ) ) {
			return;
		}

		$flip_image_id = $gallery_images[0];
		$flip_image = \wp_get_attachment_image( $flip_image_id, 'woocommerce_thumbnail', false, [ 'class' => 'sofir-flip-image' ] );

		echo '<div class="sofir-image-flipper">';
		echo $flip_image;
		echo '</div>';
	}

	private function get_flipper_css(): string {
		$effect = $this->get_option( 'effect', 'fade' );

		return '
		.products .product {
			position: relative;
		}
		.products .product img {
			transition: opacity 0.3s ease;
		}
		.sofir-image-flipper {
			position: absolute;
			top: 0;
			left: 0;
			width: 100%;
			height: 100%;
			opacity: 0;
			transition: opacity 0.3s ease;
			z-index: 1;
		}
		.products .product:hover .sofir-image-flipper {
			opacity: 1;
		}
		.products .product:hover > a > img {
			opacity: 0;
		}
		';
	}

	public function render_settings(): void {
		$effect = $this->get_option( 'effect', 'fade' );
		?>
		<tr>
			<th scope="row"><label for="effect"><?php esc_html_e( 'Flip Effect', 'sofir' ); ?></label></th>
			<td>
				<select id="effect" name="sofir_wc_addon_product-image-flipper_effect">
					<option value="fade" <?php selected( $effect, 'fade' ); ?>><?php esc_html_e( 'Fade', 'sofir' ); ?></option>
					<option value="slide" <?php selected( $effect, 'slide' ); ?>><?php esc_html_e( 'Slide', 'sofir' ); ?></option>
				</select>
			</td>
		</tr>
		<?php
	}
}
