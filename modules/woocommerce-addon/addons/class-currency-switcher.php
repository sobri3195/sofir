<?php
namespace Sofir\WooCommerceAddon\Addons;

class Currency_Switcher extends Addon_Base {

	public function __construct() {
		$this->id          = 'currency-switcher';
		$this->name        = \__( 'Currency Switcher', 'sofir' );
		$this->description = \__( 'Allow customers to switch product prices and make payments in their local currencies.', 'sofir' );
		$this->icon        = '💱';
		$this->category    = 'sales';

		parent::__construct();
	}

	public function init(): void {
		\add_action( 'wp_footer', [ $this, 'render_currency_switcher' ] );
		\add_filter( 'woocommerce_currency', [ $this, 'change_currency' ] );
		\add_filter( 'woocommerce_currency_symbol', [ $this, 'change_currency_symbol' ], 10, 2 );
		\add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
	}

	public function enqueue_scripts(): void {
		\wp_add_inline_style( 'woocommerce-general', $this->get_switcher_css() );
		\wp_add_inline_script( 'jquery', $this->get_switcher_js() );
	}

	public function render_currency_switcher(): void {
		$currencies = $this->get_currencies();
		$current = isset( $_COOKIE['sofir_currency'] ) ? \sanitize_text_field( $_COOKIE['sofir_currency'] ) : 'USD';

		?>
		<div class="sofir-currency-switcher">
			<select id="sofir-currency-select">
				<?php foreach ( $currencies as $code => $data ) : ?>
				<option value="<?php echo esc_attr( $code ); ?>" <?php selected( $current, $code ); ?>>
					<?php echo esc_html( $data['symbol'] . ' ' . $code ); ?>
				</option>
				<?php endforeach; ?>
			</select>
		</div>
		<?php
	}

	public function change_currency( string $currency ): string {
		if ( isset( $_COOKIE['sofir_currency'] ) ) {
			return \sanitize_text_field( $_COOKIE['sofir_currency'] );
		}

		return $currency;
	}

	public function change_currency_symbol( string $symbol, string $currency ): string {
		$currencies = $this->get_currencies();

		if ( isset( $currencies[ $currency ] ) ) {
			return $currencies[ $currency ]['symbol'];
		}

		return $symbol;
	}

	private function get_currencies(): array {
		return [
			'USD' => [ 'symbol' => '$', 'rate' => 1.0 ],
			'EUR' => [ 'symbol' => '€', 'rate' => 0.85 ],
			'GBP' => [ 'symbol' => '£', 'rate' => 0.73 ],
			'JPY' => [ 'symbol' => '¥', 'rate' => 110.0 ],
			'IDR' => [ 'symbol' => 'Rp', 'rate' => 14000.0 ],
		];
	}

	private function get_switcher_css(): string {
		return '
		.sofir-currency-switcher {
			position: fixed;
			bottom: 20px;
			right: 20px;
			z-index: 999;
			background: #fff;
			padding: 10px;
			border-radius: 8px;
			box-shadow: 0 2px 10px rgba(0,0,0,0.1);
		}
		#sofir-currency-select {
			padding: 5px 10px;
			border: 1px solid #ddd;
			border-radius: 4px;
			font-size: 14px;
		}
		';
	}

	private function get_switcher_js(): string {
		return "
		jQuery(function($) {
			$('#sofir-currency-select').on('change', function() {
				var currency = $(this).val();
				document.cookie = 'sofir_currency=' + currency + '; path=/; max-age=2592000';
				location.reload();
			});
		});
		";
	}

	public function render_settings(): void {
		$position = $this->get_option( 'position', 'bottom-right' );
		?>
		<tr>
			<th scope="row"><label for="position"><?php esc_html_e( 'Switcher Position', 'sofir' ); ?></label></th>
			<td>
				<select id="position" name="sofir_wc_addon_currency-switcher_position">
					<option value="bottom-right" <?php selected( $position, 'bottom-right' ); ?>><?php esc_html_e( 'Bottom Right', 'sofir' ); ?></option>
					<option value="bottom-left" <?php selected( $position, 'bottom-left' ); ?>><?php esc_html_e( 'Bottom Left', 'sofir' ); ?></option>
					<option value="top-right" <?php selected( $position, 'top-right' ); ?>><?php esc_html_e( 'Top Right', 'sofir' ); ?></option>
					<option value="top-left" <?php selected( $position, 'top-left' ); ?>><?php esc_html_e( 'Top Left', 'sofir' ); ?></option>
				</select>
			</td>
		</tr>
		<?php
	}
}
