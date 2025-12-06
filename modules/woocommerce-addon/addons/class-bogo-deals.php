<?php
namespace Sofir\WooCommerceAddon\Addons;

class BOGO_Deals extends Addon_Base {

	public function __construct() {
		$this->id          = 'bogo_deals';
		$this->name        = \__( 'BOGO Deals', 'sofir' );
		$this->description = \__( 'Buy One Get One deals. Create BOGO promotions with flexible rules.', 'sofir' );
		$this->icon        = '🎁';
		$this->category    = 'marketing';

		parent::__construct();
	}

	public function init(): void {
		\add_action( 'woocommerce_product_options_pricing', [ $this, 'add_bogo_fields' ] );
		\add_action( 'woocommerce_process_product_meta', [ $this, 'save_bogo_fields' ] );
		\add_action( 'woocommerce_before_calculate_totals', [ $this, 'apply_bogo_discount' ], 10, 1 );
		\add_action( 'woocommerce_single_product_summary', [ $this, 'display_bogo_message' ], 25 );
		\add_filter( 'woocommerce_get_price_html', [ $this, 'add_bogo_badge' ], 10, 2 );
	}

	public function add_bogo_fields(): void {
		global $post;
		
		echo '<div class="options_group sofir-bogo-fields">';
		
		\woocommerce_wp_checkbox( [
			'id'          => '_sofir_bogo_enabled',
			'label'       => \__( 'Enable BOGO', 'sofir' ),
			'description' => \__( 'Enable Buy One Get One deal for this product', 'sofir' ),
		] );

		\woocommerce_wp_select( [
			'id'          => '_sofir_bogo_type',
			'label'       => \__( 'BOGO Type', 'sofir' ),
			'options'     => [
				'bogo'        => \__( 'Buy 1 Get 1 Free', 'sofir' ),
				'buy2get1'    => \__( 'Buy 2 Get 1 Free', 'sofir' ),
				'buy3get1'    => \__( 'Buy 3 Get 1 Free', 'sofir' ),
				'percentage'  => \__( 'Buy 1 Get 1 at X%', 'sofir' ),
			],
		] );

		\woocommerce_wp_text_input( [
			'id'          => '_sofir_bogo_discount',
			'label'       => \__( 'Discount (%)', 'sofir' ),
			'type'        => 'number',
			'custom_attributes' => [
				'step' => '1',
				'min'  => '0',
				'max'  => '100',
			],
			'description' => \__( 'For percentage type only', 'sofir' ),
		] );

		echo '</div>';
	}

	public function save_bogo_fields( int $post_id ): void {
		$enabled = isset( $_POST['_sofir_bogo_enabled'] ) ? 'yes' : 'no';
		\update_post_meta( $post_id, '_sofir_bogo_enabled', $enabled );

		if ( isset( $_POST['_sofir_bogo_type'] ) ) {
			\update_post_meta( $post_id, '_sofir_bogo_type', \sanitize_text_field( $_POST['_sofir_bogo_type'] ) );
		}

		if ( isset( $_POST['_sofir_bogo_discount'] ) ) {
			\update_post_meta( $post_id, '_sofir_bogo_discount', \sanitize_text_field( $_POST['_sofir_bogo_discount'] ) );
		}
	}

	public function apply_bogo_discount( $cart ): void {
		if ( \is_admin() && ! \defined( 'DOING_AJAX' ) ) {
			return;
		}

		foreach ( $cart->get_cart() as $cart_item_key => $cart_item ) {
			$product_id = $cart_item['product_id'];
			$bogo_enabled = \get_post_meta( $product_id, '_sofir_bogo_enabled', true );

			if ( 'yes' !== $bogo_enabled ) {
				continue;
			}

			$quantity = $cart_item['quantity'];
			$bogo_type = \get_post_meta( $product_id, '_sofir_bogo_type', true );
			$discount = (float) \get_post_meta( $product_id, '_sofir_bogo_discount', true );

			$original_price = (float) $cart_item['data']->get_price();
			$new_price = $original_price;

			switch ( $bogo_type ) {
				case 'bogo':
					if ( $quantity >= 2 ) {
						$free_items = floor( $quantity / 2 );
						$paid_items = $quantity - $free_items;
						$new_price = ( $original_price * $paid_items ) / $quantity;
					}
					break;

				case 'buy2get1':
					if ( $quantity >= 3 ) {
						$free_items = floor( $quantity / 3 );
						$paid_items = $quantity - $free_items;
						$new_price = ( $original_price * $paid_items ) / $quantity;
					}
					break;

				case 'buy3get1':
					if ( $quantity >= 4 ) {
						$free_items = floor( $quantity / 4 );
						$paid_items = $quantity - $free_items;
						$new_price = ( $original_price * $paid_items ) / $quantity;
					}
					break;

				case 'percentage':
					if ( $quantity >= 2 && $discount > 0 ) {
						$discounted_items = floor( $quantity / 2 );
						$regular_items = $quantity - $discounted_items;
						$discounted_price = $original_price * ( 1 - ( $discount / 100 ) );
						$new_price = ( ( $regular_items * $original_price ) + ( $discounted_items * $discounted_price ) ) / $quantity;
					}
					break;
			}

			if ( $new_price !== $original_price ) {
				$cart_item['data']->set_price( $new_price );
			}
		}
	}

	public function display_bogo_message(): void {
		global $product;

		$bogo_enabled = \get_post_meta( $product->get_id(), '_sofir_bogo_enabled', true );
		if ( 'yes' !== $bogo_enabled ) {
			return;
		}

		$bogo_type = \get_post_meta( $product->get_id(), '_sofir_bogo_type', true );
		$discount = (float) \get_post_meta( $product->get_id(), '_sofir_bogo_discount', true );

		$message = '';
		switch ( $bogo_type ) {
			case 'bogo':
				$message = \__( '🎁 Buy 1 Get 1 FREE!', 'sofir' );
				break;
			case 'buy2get1':
				$message = \__( '🎁 Buy 2 Get 1 FREE!', 'sofir' );
				break;
			case 'buy3get1':
				$message = \__( '🎁 Buy 3 Get 1 FREE!', 'sofir' );
				break;
			case 'percentage':
				$message = sprintf( \__( '🎁 Buy 1 Get 1 at %d%% OFF!', 'sofir' ), $discount );
				break;
		}

		if ( $message ) {
			echo '<div class="sofir-bogo-message" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 15px; border-radius: 8px; margin: 15px 0; font-weight: 600; text-align: center;">';
			echo esc_html( $message );
			echo '</div>';
		}
	}

	public function add_bogo_badge( string $price, $product ): string {
		$bogo_enabled = \get_post_meta( $product->get_id(), '_sofir_bogo_enabled', true );
		if ( 'yes' !== $bogo_enabled ) {
			return $price;
		}

		if ( $this->get_option( 'show_bogo_badge', 'yes' ) === 'yes' ) {
			$price .= ' <span class="sofir-bogo-badge" style="background: #ff4757; color: white; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: 600; margin-left: 8px;">BOGO</span>';
		}

		return $price;
	}

	public function render_settings(): void {
		$show_bogo_badge = $this->get_option( 'show_bogo_badge', 'yes' );
		$badge_position = $this->get_option( 'badge_position', 'price' );
		?>
		<tr>
			<th scope="row"><?php esc_html_e( 'Show BOGO Badge', 'sofir' ); ?></th>
			<td>
				<label>
					<input type="checkbox" name="sofir_wc_addon_bogo_deals_show_bogo_badge" value="yes" <?php checked( $show_bogo_badge, 'yes' ); ?>>
					<?php esc_html_e( 'Display BOGO badge on products', 'sofir' ); ?>
				</label>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="badge_position"><?php esc_html_e( 'Badge Position', 'sofir' ); ?></label></th>
			<td>
				<select id="badge_position" name="sofir_wc_addon_bogo_deals_badge_position">
					<option value="price" <?php selected( $badge_position, 'price' ); ?>><?php esc_html_e( 'After Price', 'sofir' ); ?></option>
					<option value="thumbnail" <?php selected( $badge_position, 'thumbnail' ); ?>><?php esc_html_e( 'On Product Image', 'sofir' ); ?></option>
				</select>
			</td>
		</tr>
		<?php
	}
}
