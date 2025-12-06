<?php
namespace Sofir\WooCommerceAddon\Addons;

class Quick_Social_Share extends Addon_Base {

	public function __construct() {
		$this->id          = 'quick-social-share';
		$this->name        = \__( 'Quick Social Share', 'sofir' );
		$this->description = \__( 'Display social share icons and let your shoppers share products with their social profiles instantly.', 'sofir' );
		$this->icon        = '🔗';
		$this->category    = 'flexibility';

		parent::__construct();
	}

	public function init(): void {
		\add_action( 'woocommerce_share', [ $this, 'render_social_share' ] );
		\add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
	}

	public function enqueue_scripts(): void {
		\wp_add_inline_style( 'woocommerce-general', $this->get_social_css() );
	}

	public function render_social_share(): void {
		global $product;

		if ( ! $product ) {
			return;
		}

		$url = \get_permalink( $product->get_id() );
		$title = $product->get_name();
		$image = \wp_get_attachment_url( $product->get_image_id() );

		$networks = [
			'facebook' => [
				'url' => 'https://www.facebook.com/sharer/sharer.php?u=' . \urlencode( $url ),
				'icon' => '📘',
				'label' => 'Facebook',
			],
			'twitter' => [
				'url' => 'https://twitter.com/intent/tweet?url=' . \urlencode( $url ) . '&text=' . \urlencode( $title ),
				'icon' => '🐦',
				'label' => 'Twitter',
			],
			'pinterest' => [
				'url' => 'https://pinterest.com/pin/create/button/?url=' . \urlencode( $url ) . '&media=' . \urlencode( $image ) . '&description=' . \urlencode( $title ),
				'icon' => '📌',
				'label' => 'Pinterest',
			],
			'whatsapp' => [
				'url' => 'https://wa.me/?text=' . \urlencode( $title . ' ' . $url ),
				'icon' => '💬',
				'label' => 'WhatsApp',
			],
			'telegram' => [
				'url' => 'https://t.me/share/url?url=' . \urlencode( $url ) . '&text=' . \urlencode( $title ),
				'icon' => '✈️',
				'label' => 'Telegram',
			],
		];

		$enabled_networks = $this->get_option( 'networks', 'facebook,twitter,pinterest,whatsapp' );
		$enabled = \explode( ',', $enabled_networks );

		?>
		<div class="sofir-social-share">
			<h3><?php esc_html_e( 'Share this product:', 'sofir' ); ?></h3>
			<div class="social-buttons">
				<?php foreach ( $networks as $key => $network ) :
					if ( ! in_array( $key, $enabled, true ) ) {
						continue;
					}
				?>
				<a href="<?php echo esc_url( $network['url'] ); ?>" target="_blank" rel="noopener noreferrer" class="social-btn social-<?php echo esc_attr( $key ); ?>" title="<?php echo esc_attr( $network['label'] ); ?>">
					<span class="icon"><?php echo $network['icon']; ?></span>
				</a>
				<?php endforeach; ?>
			</div>
		</div>
		<?php
	}

	private function get_social_css(): string {
		return '
		.sofir-social-share {
			margin: 20px 0;
		}
		.sofir-social-share h3 {
			font-size: 14px;
			margin-bottom: 10px;
		}
		.social-buttons {
			display: flex;
			gap: 10px;
			flex-wrap: wrap;
		}
		.social-btn {
			display: inline-flex;
			align-items: center;
			justify-content: center;
			width: 40px;
			height: 40px;
			border-radius: 50%;
			background: #f0f0f0;
			transition: transform 0.3s ease, background 0.3s ease;
			text-decoration: none;
		}
		.social-btn:hover {
			transform: scale(1.1);
		}
		.social-btn .icon {
			font-size: 20px;
		}
		.social-facebook:hover { background: #1877f2; }
		.social-twitter:hover { background: #1da1f2; }
		.social-pinterest:hover { background: #e60023; }
		.social-whatsapp:hover { background: #25d366; }
		.social-telegram:hover { background: #0088cc; }
		';
	}

	public function render_settings(): void {
		$networks = $this->get_option( 'networks', 'facebook,twitter,pinterest,whatsapp' );
		$enabled = \explode( ',', $networks );
		?>
		<tr>
			<th scope="row"><?php esc_html_e( 'Enable Networks', 'sofir' ); ?></th>
			<td>
				<label>
					<input type="checkbox" name="sofir_wc_addon_quick-social-share_networks[]" value="facebook" <?php checked( in_array( 'facebook', $enabled, true ) ); ?>>
					<?php esc_html_e( 'Facebook', 'sofir' ); ?>
				</label><br>
				<label>
					<input type="checkbox" name="sofir_wc_addon_quick-social-share_networks[]" value="twitter" <?php checked( in_array( 'twitter', $enabled, true ) ); ?>>
					<?php esc_html_e( 'Twitter', 'sofir' ); ?>
				</label><br>
				<label>
					<input type="checkbox" name="sofir_wc_addon_quick-social-share_networks[]" value="pinterest" <?php checked( in_array( 'pinterest', $enabled, true ) ); ?>>
					<?php esc_html_e( 'Pinterest', 'sofir' ); ?>
				</label><br>
				<label>
					<input type="checkbox" name="sofir_wc_addon_quick-social-share_networks[]" value="whatsapp" <?php checked( in_array( 'whatsapp', $enabled, true ) ); ?>>
					<?php esc_html_e( 'WhatsApp', 'sofir' ); ?>
				</label><br>
				<label>
					<input type="checkbox" name="sofir_wc_addon_quick-social-share_networks[]" value="telegram" <?php checked( in_array( 'telegram', $enabled, true ) ); ?>>
					<?php esc_html_e( 'Telegram', 'sofir' ); ?>
				</label>
			</td>
		</tr>
		<?php
	}
}
