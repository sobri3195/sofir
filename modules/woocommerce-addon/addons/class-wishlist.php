<?php
namespace Sofir\WooCommerceAddon\Addons;

class Wishlist extends Addon_Base {

	public function __construct() {
		$this->id          = 'wishlist';
		$this->name        = \__( 'Wishlist', 'sofir' );
		$this->description = \__( 'Allow customers to save products to their wishlist. Increase engagement and sales.', 'sofir' );
		$this->icon        = '❤️';
		$this->category    = 'customer';

		parent::__construct();
	}

	public function init(): void {
		\add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
		\add_action( 'woocommerce_after_shop_loop_item', [ $this, 'add_wishlist_button' ], 15 );
		\add_action( 'woocommerce_single_product_summary', [ $this, 'add_wishlist_button' ], 35 );
		\add_action( 'wp_ajax_sofir_add_to_wishlist', [ $this, 'ajax_add_to_wishlist' ] );
		\add_action( 'wp_ajax_nopriv_sofir_add_to_wishlist', [ $this, 'ajax_add_to_wishlist' ] );
		\add_action( 'wp_ajax_sofir_remove_from_wishlist', [ $this, 'ajax_remove_from_wishlist' ] );
		\add_action( 'wp_ajax_nopriv_sofir_remove_from_wishlist', [ $this, 'ajax_remove_from_wishlist' ] );
		\add_shortcode( 'sofir_wishlist', [ $this, 'wishlist_shortcode' ] );
	}

	public function enqueue_scripts(): void {
		\wp_enqueue_style( 'sofir-wishlist', SOFIR_PLUGIN_URL . '/modules/woocommerce-addon/assets/wishlist.css', [], '1.0.0' );
		\wp_enqueue_script( 'sofir-wishlist', SOFIR_PLUGIN_URL . '/modules/woocommerce-addon/assets/wishlist.js', [ 'jquery' ], '1.0.0', true );
		
		\wp_localize_script( 'sofir-wishlist', 'sofirWishlist', [
			'ajaxurl' => \admin_url( 'admin-ajax.php' ),
			'nonce'   => \wp_create_nonce( 'sofir_wishlist_nonce' ),
			'i18n'    => [
				'added'   => \__( 'Added to wishlist!', 'sofir' ),
				'removed' => \__( 'Removed from wishlist!', 'sofir' ),
				'error'   => \__( 'Error occurred. Please try again.', 'sofir' ),
			],
		] );
	}

	public function add_wishlist_button(): void {
		global $product;

		if ( ! $product ) {
			return;
		}

		$product_id = $product->get_id();
		$wishlist = $this->get_wishlist();
		$in_wishlist = in_array( $product_id, $wishlist, true );

		$icon = $in_wishlist ? '❤️' : '🤍';
		$class = $in_wishlist ? 'in-wishlist' : '';
		$text = $in_wishlist ? \__( 'In Wishlist', 'sofir' ) : \__( 'Add to Wishlist', 'sofir' );

		echo '<button type="button" class="sofir-wishlist-button ' . esc_attr( $class ) . '" data-product-id="' . esc_attr( $product_id ) . '">';
		echo '<span class="sofir-wishlist-icon">' . $icon . '</span>';
		if ( $this->get_option( 'show_button_text', 'yes' ) === 'yes' ) {
			echo '<span class="sofir-wishlist-text">' . esc_html( $text ) . '</span>';
		}
		echo '</button>';
	}

	public function ajax_add_to_wishlist(): void {
		\check_ajax_referer( 'sofir_wishlist_nonce', 'nonce' );

		$product_id = isset( $_POST['product_id'] ) ? \absint( $_POST['product_id'] ) : 0;

		if ( ! $product_id ) {
			\wp_send_json_error( [ 'message' => \__( 'Invalid product ID', 'sofir' ) ] );
		}

		$wishlist = $this->get_wishlist();
		
		if ( ! in_array( $product_id, $wishlist, true ) ) {
			$wishlist[] = $product_id;
			$this->save_wishlist( $wishlist );
		}

		\wp_send_json_success( [
			'message' => \__( 'Product added to wishlist', 'sofir' ),
			'count'   => count( $wishlist ),
		] );
	}

	public function ajax_remove_from_wishlist(): void {
		\check_ajax_referer( 'sofir_wishlist_nonce', 'nonce' );

		$product_id = isset( $_POST['product_id'] ) ? \absint( $_POST['product_id'] ) : 0;

		if ( ! $product_id ) {
			\wp_send_json_error( [ 'message' => \__( 'Invalid product ID', 'sofir' ) ] );
		}

		$wishlist = $this->get_wishlist();
		$wishlist = array_diff( $wishlist, [ $product_id ] );
		$this->save_wishlist( $wishlist );

		\wp_send_json_success( [
			'message' => \__( 'Product removed from wishlist', 'sofir' ),
			'count'   => count( $wishlist ),
		] );
	}

	public function wishlist_shortcode(): string {
		$wishlist = $this->get_wishlist();

		if ( empty( $wishlist ) ) {
			return '<div class="sofir-wishlist-empty"><p>' . \esc_html__( 'Your wishlist is empty.', 'sofir' ) . '</p></div>';
		}

		\ob_start();
		?>
		<div class="sofir-wishlist-page">
			<h2><?php esc_html_e( 'My Wishlist', 'sofir' ); ?></h2>
			<div class="sofir-wishlist-items">
				<?php foreach ( $wishlist as $product_id ) :
					$product = \wc_get_product( $product_id );
					if ( ! $product ) {
						continue;
					}
				?>
				<div class="sofir-wishlist-item" data-product-id="<?php echo esc_attr( $product_id ); ?>">
					<div class="sofir-wishlist-item-image">
						<?php echo $product->get_image( 'thumbnail' ); ?>
					</div>
					<div class="sofir-wishlist-item-details">
						<h3><a href="<?php echo esc_url( $product->get_permalink() ); ?>"><?php echo esc_html( $product->get_name() ); ?></a></h3>
						<div class="sofir-wishlist-item-price"><?php echo $product->get_price_html(); ?></div>
					</div>
					<div class="sofir-wishlist-item-actions">
						<?php if ( $product->is_in_stock() ) : ?>
							<a href="<?php echo esc_url( $product->add_to_cart_url() ); ?>" class="button"><?php esc_html_e( 'Add to Cart', 'sofir' ); ?></a>
						<?php else : ?>
							<span class="out-of-stock"><?php esc_html_e( 'Out of Stock', 'sofir' ); ?></span>
						<?php endif; ?>
						<button type="button" class="sofir-remove-wishlist" data-product-id="<?php echo esc_attr( $product_id ); ?>"><?php esc_html_e( 'Remove', 'sofir' ); ?></button>
					</div>
				</div>
				<?php endforeach; ?>
			</div>
		</div>
		<?php
		return \ob_get_clean();
	}

	private function get_wishlist(): array {
		if ( \is_user_logged_in() ) {
			$user_id = \get_current_user_id();
			$wishlist = \get_user_meta( $user_id, 'sofir_wishlist', true );
		} else {
			$wishlist = isset( $_COOKIE['sofir_wishlist'] ) ? \json_decode( \stripslashes( $_COOKIE['sofir_wishlist'] ), true ) : [];
		}

		return is_array( $wishlist ) ? array_map( 'intval', $wishlist ) : [];
	}

	private function save_wishlist( array $wishlist ): void {
		if ( \is_user_logged_in() ) {
			$user_id = \get_current_user_id();
			\update_user_meta( $user_id, 'sofir_wishlist', $wishlist );
		} else {
			\setcookie( 'sofir_wishlist', \wp_json_encode( $wishlist ), time() + ( 30 * DAY_IN_SECONDS ), '/' );
		}
	}

	public function render_settings(): void {
		$show_button_text = $this->get_option( 'show_button_text', 'yes' );
		$button_position = $this->get_option( 'button_position', 'after_cart' );
		?>
		<tr>
			<th scope="row"><?php esc_html_e( 'Show Button Text', 'sofir' ); ?></th>
			<td>
				<label>
					<input type="checkbox" name="sofir_wc_addon_wishlist_show_button_text" value="yes" <?php checked( $show_button_text, 'yes' ); ?>>
					<?php esc_html_e( 'Show "Add to Wishlist" text on button', 'sofir' ); ?>
				</label>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="button_position"><?php esc_html_e( 'Button Position', 'sofir' ); ?></label></th>
			<td>
				<select id="button_position" name="sofir_wc_addon_wishlist_button_position">
					<option value="before_cart" <?php selected( $button_position, 'before_cart' ); ?>><?php esc_html_e( 'Before Add to Cart', 'sofir' ); ?></option>
					<option value="after_cart" <?php selected( $button_position, 'after_cart' ); ?>><?php esc_html_e( 'After Add to Cart', 'sofir' ); ?></option>
				</select>
			</td>
		</tr>
		<?php
	}
}
