<?php
namespace Sofir\WooCommerceAddon\Addons;

class Smart_Notifications extends Addon_Base {

	public function __construct() {
		$this->id          = 'smart_notifications';
		$this->name        = \__( 'Smart Notifications', 'sofir' );
		$this->description = \__( 'Sales popup notifications. Show recent purchases to increase trust and conversions.', 'sofir' );
		$this->icon        = '🔔';
		$this->category    = 'marketing';

		parent::__construct();
	}

	public function init(): void {
		\add_action( 'wp_footer', [ $this, 'render_notification_popup' ] );
		\add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
		\add_action( 'wp_ajax_sofir_get_recent_orders', [ $this, 'ajax_get_recent_orders' ] );
		\add_action( 'wp_ajax_nopriv_sofir_get_recent_orders', [ $this, 'ajax_get_recent_orders' ] );
	}

	public function enqueue_scripts(): void {
		\wp_enqueue_style( 'sofir-smart-notifications', SOFIR_PLUGIN_URL . '/modules/woocommerce-addon/assets/smart-notifications.css', [], '1.0.0' );
		\wp_enqueue_script( 'sofir-smart-notifications', SOFIR_PLUGIN_URL . '/modules/woocommerce-addon/assets/smart-notifications.js', [ 'jquery' ], '1.0.0', true );
		
		\wp_localize_script( 'sofir-smart-notifications', 'sofirNotifications', [
			'ajaxurl' => \admin_url( 'admin-ajax.php' ),
			'nonce'   => \wp_create_nonce( 'sofir_notifications_nonce' ),
			'settings' => [
				'delay'    => (int) $this->get_option( 'initial_delay', 3 ),
				'interval' => (int) $this->get_option( 'display_interval', 15 ),
				'position' => $this->get_option( 'position', 'bottom-left' ),
			],
		] );
	}

	public function render_notification_popup(): void {
		if ( ! $this->should_display() ) {
			return;
		}

		?>
		<div id="sofir-notification-popup" class="sofir-notification-hidden">
			<div class="sofir-notification-content">
				<div class="sofir-notification-icon">🛒</div>
				<div class="sofir-notification-body">
					<div class="sofir-notification-title"></div>
					<div class="sofir-notification-message"></div>
					<div class="sofir-notification-time"></div>
				</div>
				<button type="button" class="sofir-notification-close">&times;</button>
			</div>
		</div>
		<?php
	}

	public function ajax_get_recent_orders(): void {
		\check_ajax_referer( 'sofir_notifications_nonce', 'nonce' );

		$limit = (int) $this->get_option( 'orders_limit', 10 );

		$orders = \wc_get_orders( [
			'limit'  => $limit,
			'status' => [ 'wc-completed', 'wc-processing' ],
			'orderby' => 'date',
			'order'  => 'DESC',
		] );

		$notifications = [];

		foreach ( $orders as $order ) {
			foreach ( $order->get_items() as $item ) {
				$product = $item->get_product();
				if ( ! $product ) {
					continue;
				}

				$customer_name = $this->get_anonymized_name( $order );
				$location = $this->get_order_location( $order );
				$time_ago = $this->get_time_ago( $order->get_date_created() );

				$notifications[] = [
					'title'   => $customer_name,
					'message' => sprintf( \__( 'purchased %s', 'sofir' ), $product->get_name() ),
					'time'    => $time_ago,
					'location' => $location,
				];
			}
		}

		shuffle( $notifications );
		$notifications = array_slice( $notifications, 0, $limit );

		\wp_send_json_success( $notifications );
	}

	private function should_display(): bool {
		if ( \is_admin() || \is_cart() || \is_checkout() ) {
			return false;
		}

		$pages = $this->get_option( 'display_pages', [ 'shop', 'product' ] );

		if ( ! is_array( $pages ) ) {
			$pages = [];
		}

		if ( in_array( 'shop', $pages, true ) && ( \is_shop() || \is_product_category() ) ) {
			return true;
		}

		if ( in_array( 'product', $pages, true ) && \is_product() ) {
			return true;
		}

		if ( in_array( 'home', $pages, true ) && \is_front_page() ) {
			return true;
		}

		return false;
	}

	private function get_anonymized_name( $order ): string {
		$first_name = $order->get_billing_first_name();
		$last_name = $order->get_billing_last_name();

		if ( ! $first_name && ! $last_name ) {
			return \__( 'Someone', 'sofir' );
		}

		return $first_name . ' ' . substr( $last_name, 0, 1 ) . '.';
	}

	private function get_order_location( $order ): string {
		$city = $order->get_billing_city();
		$state = $order->get_billing_state();
		$country = $order->get_billing_country();

		if ( $city && $country ) {
			return $city . ', ' . \WC()->countries->countries[ $country ] ?? $country;
		}

		if ( $country ) {
			return \WC()->countries->countries[ $country ] ?? $country;
		}

		return '';
	}

	private function get_time_ago( $datetime ): string {
		$now = new \DateTime();
		$ago = new \DateTime( $datetime->date( 'Y-m-d H:i:s' ) );
		$diff = $now->diff( $ago );

		if ( $diff->d > 0 ) {
			return sprintf( _n( '%d day ago', '%d days ago', $diff->d, 'sofir' ), $diff->d );
		}

		if ( $diff->h > 0 ) {
			return sprintf( _n( '%d hour ago', '%d hours ago', $diff->h, 'sofir' ), $diff->h );
		}

		if ( $diff->i > 0 ) {
			return sprintf( _n( '%d minute ago', '%d minutes ago', $diff->i, 'sofir' ), $diff->i );
		}

		return \__( 'Just now', 'sofir' );
	}

	public function render_settings(): void {
		$initial_delay = $this->get_option( 'initial_delay', 3 );
		$display_interval = $this->get_option( 'display_interval', 15 );
		$orders_limit = $this->get_option( 'orders_limit', 10 );
		$position = $this->get_option( 'position', 'bottom-left' );
		$display_pages = $this->get_option( 'display_pages', [ 'shop', 'product' ] );

		if ( ! is_array( $display_pages ) ) {
			$display_pages = [];
		}
		?>
		<tr>
			<th scope="row"><label for="initial_delay"><?php esc_html_e( 'Initial Delay (seconds)', 'sofir' ); ?></label></th>
			<td>
				<input type="number" id="initial_delay" name="sofir_wc_addon_smart_notifications_initial_delay" value="<?php echo esc_attr( $initial_delay ); ?>" min="0" class="small-text">
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="display_interval"><?php esc_html_e( 'Display Interval (seconds)', 'sofir' ); ?></label></th>
			<td>
				<input type="number" id="display_interval" name="sofir_wc_addon_smart_notifications_display_interval" value="<?php echo esc_attr( $display_interval ); ?>" min="5" class="small-text">
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="orders_limit"><?php esc_html_e( 'Orders Limit', 'sofir' ); ?></label></th>
			<td>
				<input type="number" id="orders_limit" name="sofir_wc_addon_smart_notifications_orders_limit" value="<?php echo esc_attr( $orders_limit ); ?>" min="1" max="50" class="small-text">
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="position"><?php esc_html_e( 'Position', 'sofir' ); ?></label></th>
			<td>
				<select id="position" name="sofir_wc_addon_smart_notifications_position">
					<option value="bottom-left" <?php selected( $position, 'bottom-left' ); ?>><?php esc_html_e( 'Bottom Left', 'sofir' ); ?></option>
					<option value="bottom-right" <?php selected( $position, 'bottom-right' ); ?>><?php esc_html_e( 'Bottom Right', 'sofir' ); ?></option>
					<option value="top-left" <?php selected( $position, 'top-left' ); ?>><?php esc_html_e( 'Top Left', 'sofir' ); ?></option>
					<option value="top-right" <?php selected( $position, 'top-right' ); ?>><?php esc_html_e( 'Top Right', 'sofir' ); ?></option>
				</select>
			</td>
		</tr>
		<tr>
			<th scope="row"><?php esc_html_e( 'Display On', 'sofir' ); ?></th>
			<td>
				<label><input type="checkbox" name="sofir_wc_addon_smart_notifications_display_pages[]" value="home" <?php checked( in_array( 'home', $display_pages, true ) ); ?>> <?php esc_html_e( 'Home Page', 'sofir' ); ?></label><br>
				<label><input type="checkbox" name="sofir_wc_addon_smart_notifications_display_pages[]" value="shop" <?php checked( in_array( 'shop', $display_pages, true ) ); ?>> <?php esc_html_e( 'Shop Page', 'sofir' ); ?></label><br>
				<label><input type="checkbox" name="sofir_wc_addon_smart_notifications_display_pages[]" value="product" <?php checked( in_array( 'product', $display_pages, true ) ); ?>> <?php esc_html_e( 'Product Page', 'sofir' ); ?></label>
			</td>
		</tr>
		<?php
	}
}
