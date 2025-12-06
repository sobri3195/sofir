<?php
namespace Sofir\WooCommerceAddon\Addons;

class Product_Video extends Addon_Base {

	public function __construct() {
		$this->id          = 'product-video';
		$this->name        = \__( 'Product Video', 'sofir' );
		$this->description = \__( 'Display product-featured videos instead of featured images and grab users attention to specific products.', 'sofir' );
		$this->icon        = '🎥';
		$this->category    = 'flexibility';

		parent::__construct();
	}

	public function init(): void {
		\add_action( 'woocommerce_before_single_product_summary', [ $this, 'add_video_metabox_display' ], 5 );
		\add_action( 'woocommerce_product_options_general_product_data', [ $this, 'add_video_field' ] );
		\add_action( 'woocommerce_process_product_meta', [ $this, 'save_video_field' ] );
	}

	public function add_video_field(): void {
		global $post;
		?>
		<div class="options_group">
			<?php
			\woocommerce_wp_text_input( [
				'id' => '_product_video_url',
				'label' => \__( 'Product Video URL', 'sofir' ),
				'desc_tip' => true,
				'description' => \__( 'Enter YouTube or Vimeo video URL', 'sofir' ),
				'type' => 'url',
			] );
			?>
		</div>
		<?php
	}

	public function save_video_field( int $post_id ): void {
		$video_url = isset( $_POST['_product_video_url'] ) ? \esc_url_raw( $_POST['_product_video_url'] ) : '';
		\update_post_meta( $post_id, '_product_video_url', $video_url );
	}

	public function add_video_metabox_display(): void {
		global $product;

		$video_url = \get_post_meta( $product->get_id(), '_product_video_url', true );

		if ( empty( $video_url ) ) {
			return;
		}

		$embed_url = $this->get_embed_url( $video_url );

		if ( ! $embed_url ) {
			return;
		}

		?>
		<div class="sofir-product-video">
			<iframe width="100%" height="500" src="<?php echo esc_url( $embed_url ); ?>" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
		</div>
		<?php
	}

	private function get_embed_url( string $url ): ?string {
		if ( strpos( $url, 'youtube.com' ) !== false || strpos( $url, 'youtu.be' ) !== false ) {
			\preg_match( '/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/ ]{11})/', $url, $matches );
			return isset( $matches[1] ) ? 'https://www.youtube.com/embed/' . $matches[1] : null;
		}

		if ( strpos( $url, 'vimeo.com' ) !== false ) {
			\preg_match( '/vimeo\.com\/(\d+)/', $url, $matches );
			return isset( $matches[1] ) ? 'https://player.vimeo.com/video/' . $matches[1] : null;
		}

		return null;
	}

	public function render_settings(): void {
		$replace_featured = $this->get_option( 'replace_featured', 'no' );
		?>
		<tr>
			<th scope="row"><?php esc_html_e( 'Replace Featured Image', 'sofir' ); ?></th>
			<td>
				<label>
					<input type="checkbox" name="sofir_wc_addon_product-video_replace_featured" value="yes" <?php checked( $replace_featured, 'yes' ); ?>>
					<?php esc_html_e( 'Replace product featured image with video', 'sofir' ); ?>
				</label>
			</td>
		</tr>
		<?php
	}
}
