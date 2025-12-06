<?php
namespace Sofir\WooCommerceAddon\Addons;

class Cart_Reserved_Timer extends Addon_Base {

	public function __construct() {
		$this->id          = 'cart-reserved-timer';
		$this->name        = \__( 'Cart Reserved Timer', 'sofir' );
		$this->description = \__( 'Display a countdown timer and show a FOMO message once someone adds products to the cart.', 'sofir' );
		$this->icon        = '⏱️';
		$this->category    = 'cart';

		parent::__construct();
	}

	public function init(): void {
		\add_action( 'woocommerce_before_cart', [ $this, 'render_timer' ] );
		\add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
	}

	public function enqueue_scripts(): void {
		if ( ! \is_cart() ) {
			return;
		}

		\wp_add_inline_style( 'woocommerce-general', $this->get_timer_css() );
		\wp_add_inline_script( 'jquery', $this->get_timer_js() );
	}

	public function render_timer(): void {
		$minutes = (int) $this->get_option( 'timer_minutes', '15' );
		$message = $this->get_option( 'message', 'Complete your purchase within {time} or your cart will be cleared!' );

		?>
		<div class="sofir-cart-timer">
			<div class="timer-content">
				<span class="timer-icon">⚠️</span>
				<span class="timer-message">
					<?php echo esc_html( str_replace( '{time}', '', $message ) ); ?>
					<strong class="timer-countdown" data-minutes="<?php echo esc_attr( $minutes ); ?>">
						<span class="minutes"><?php echo str_pad( $minutes, 2, '0', STR_PAD_LEFT ); ?></span>:
						<span class="seconds">00</span>
					</strong>
				</span>
			</div>
		</div>
		<?php
	}

	private function get_timer_css(): string {
		return '
		.sofir-cart-timer {
			background: linear-gradient(135deg, #ff6b6b 0%, #ff8e53 100%);
			color: #fff;
			padding: 20px;
			border-radius: 8px;
			margin-bottom: 20px;
			text-align: center;
		}
		.timer-content {
			display: flex;
			align-items: center;
			justify-content: center;
			gap: 15px;
		}
		.timer-icon {
			font-size: 24px;
		}
		.timer-message {
			font-size: 16px;
		}
		.timer-countdown {
			font-size: 20px;
			font-weight: bold;
			background: rgba(255, 255, 255, 0.2);
			padding: 5px 15px;
			border-radius: 4px;
			margin-left: 5px;
		}
		.timer-expired {
			background: linear-gradient(135deg, #333 0%, #555 100%);
		}
		';
	}

	private function get_timer_js(): string {
		return "
		jQuery(function($) {
			var timerMinutes = parseInt($('.timer-countdown').data('minutes'));
			var endTime = new Date().getTime() + (timerMinutes * 60 * 1000);

			var timerInterval = setInterval(function() {
				var now = new Date().getTime();
				var distance = endTime - now;

				if (distance < 0) {
					clearInterval(timerInterval);
					$('.sofir-cart-timer').addClass('timer-expired');
					$('.timer-message').html('⚠️ Time expired! Your cart may be cleared.');
					return;
				}

				var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
				var seconds = Math.floor((distance % (1000 * 60)) / 1000);

				$('.timer-countdown .minutes').text(String(minutes).padStart(2, '0'));
				$('.timer-countdown .seconds').text(String(seconds).padStart(2, '0'));
			}, 1000);
		});
		";
	}

	public function render_settings(): void {
		$timer_minutes = $this->get_option( 'timer_minutes', '15' );
		$message = $this->get_option( 'message', 'Complete your purchase within {time} or your cart will be cleared!' );
		?>
		<tr>
			<th scope="row"><label for="timer_minutes"><?php esc_html_e( 'Timer Duration (minutes)', 'sofir' ); ?></label></th>
			<td>
				<input type="number" id="timer_minutes" name="sofir_wc_addon_cart-reserved-timer_timer_minutes" value="<?php echo esc_attr( $timer_minutes ); ?>" min="1" max="60" />
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="message"><?php esc_html_e( 'Timer Message', 'sofir' ); ?></label></th>
			<td>
				<input type="text" id="message" name="sofir_wc_addon_cart-reserved-timer_message" value="<?php echo esc_attr( $message ); ?>" class="regular-text" />
				<p class="description"><?php esc_html_e( 'Use {time} as placeholder for countdown timer', 'sofir' ); ?></p>
			</td>
		</tr>
		<?php
	}
}
