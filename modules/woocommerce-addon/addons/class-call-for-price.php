<?php
namespace Sofir\WooCommerceAddon\Addons;

class Call_For_Price extends Addon_Base {

	public function __construct() {
		$this->id          = 'call-for-price';
		$this->name        = \__( 'Call for Price', 'sofir' );
		$this->description = \__( 'Display a calling button instead of the Add to Cart button for products that dont have prices.', 'sofir' );
		$this->icon        = '📞';
		$this->category    = 'sales';

		parent::__construct();
	}

	public function init(): void {
		\add_filter( 'woocommerce_get_price_html', [ $this, 'replace_price_with_call' ], 10, 2 );
		\add_filter( 'woocommerce_is_purchasable', [ $this, 'disable_purchase_for_call_price' ], 10, 2 );
	}

	public function replace_price_with_call( string $price, $product ): string {
		if ( ! $product->get_price() || $product->get_price() <= 0 ) {
			$phone = $this->get_option( 'phone_number', '' );
			$text = $this->get_option( 'button_text', 'Call for Price' );

			if ( $phone ) {
				return sprintf(
					'<a href="tel:%s" class="button sofir-call-for-price">📞 %s</a>',
					\esc_attr( $phone ),
					\esc_html( $text )
				);
			}

			return '<span class="sofir-call-text">' . \esc_html( $text ) . '</span>';
		}

		return $price;
	}

	public function disable_purchase_for_call_price( bool $purchasable, $product ): bool {
		if ( ! $product->get_price() || $product->get_price() <= 0 ) {
			return false;
		}

		return $purchasable;
	}

	public function render_settings(): void {
		$phone_number = $this->get_option( 'phone_number', '' );
		$button_text = $this->get_option( 'button_text', 'Call for Price' );
		?>
		<tr>
			<th scope="row"><label for="phone_number"><?php esc_html_e( 'Phone Number', 'sofir' ); ?></label></th>
			<td>
				<input type="tel" id="phone_number" name="sofir_wc_addon_call-for-price_phone_number" value="<?php echo esc_attr( $phone_number ); ?>" class="regular-text" />
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="button_text"><?php esc_html_e( 'Button Text', 'sofir' ); ?></label></th>
			<td>
				<input type="text" id="button_text" name="sofir_wc_addon_call-for-price_button_text" value="<?php echo esc_attr( $button_text ); ?>" class="regular-text" />
			</td>
		</tr>
		<?php
	}
}
