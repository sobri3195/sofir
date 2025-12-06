<?php
namespace Sofir\WooCommerceAddon\Addons;

class Product_Timer extends Addon_Base {

	public function __construct() {
		$this->id          = 'product_timer';
		$this->name        = \__( 'Product Timer', 'sofir' );
		$this->description = \__( 'Add countdown timers to products. Create urgency and boost conversions.', 'sofir' );
		$this->icon        = '⏰';
		$this->category    = 'marketing';

		parent::__construct();
	}

	public function init(): void {
		\add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
		\add_action( 'woocommerce_product_options_general_product_data', [ $this, 'add_timer_fields' ] );
		\add_action( 'woocommerce_process_product_meta', [ $this, 'save_timer_fields' ] );
		\add_action( 'woocommerce_single_product_summary', [ $this, 'display_timer' ], 20 );
		\add_action( 'woocommerce_after_shop_loop_item_title', [ $this, 'display_timer_shop' ], 15 );
	}

	public function enqueue_scripts(): void {
		\wp_enqueue_style( 'sofir-product-timer', SOFIR_PLUGIN_URL . '/modules/woocommerce-addon/assets/product-timer.css', [], '1.0.0' );
		\wp_enqueue_script( 'sofir-product-timer', SOFIR_PLUGIN_URL . '/modules/woocommerce-addon/assets/product-timer.js', [ 'jquery' ], '1.0.0', true );
	}

	public function add_timer_fields(): void {
		echo '<div class="options_group sofir-timer-fields">';
		
		\woocommerce_wp_checkbox( [
			'id'          => '_sofir_timer_enabled',
			'label'       => \__( 'Enable Countdown', 'sofir' ),
			'description' => \__( 'Show countdown timer for this product', 'sofir' ),
		] );

		\woocommerce_wp_text_input( [
			'id'          => '_sofir_timer_end_date',
			'label'       => \__( 'End Date & Time', 'sofir' ),
			'type'        => 'datetime-local',
			'description' => \__( 'When should the countdown end', 'sofir' ),
		] );

		\woocommerce_wp_text_input( [
			'id'          => '_sofir_timer_text',
			'label'       => \__( 'Timer Text', 'sofir' ),
			'placeholder' => \__( 'Limited Time Offer', 'sofir' ),
			'description' => \__( 'Text to display above timer', 'sofir' ),
		] );

		echo '</div>';
	}

	public function save_timer_fields( int $post_id ): void {
		$enabled = isset( $_POST['_sofir_timer_enabled'] ) ? 'yes' : 'no';
		\update_post_meta( $post_id, '_sofir_timer_enabled', $enabled );

		if ( isset( $_POST['_sofir_timer_end_date'] ) ) {
			\update_post_meta( $post_id, '_sofir_timer_end_date', \sanitize_text_field( $_POST['_sofir_timer_end_date'] ) );
		}

		if ( isset( $_POST['_sofir_timer_text'] ) ) {
			\update_post_meta( $post_id, '_sofir_timer_text', \sanitize_text_field( $_POST['_sofir_timer_text'] ) );
		}
	}

	public function display_timer(): void {
		$this->render_timer();
	}

	public function display_timer_shop(): void {
		$this->render_timer( true );
	}

	private function render_timer( bool $is_shop = false ): void {
		global $product;

		if ( ! $product || 'yes' !== \get_post_meta( $product->get_id(), '_sofir_timer_enabled', true ) ) {
			return;
		}

		$end_date = \get_post_meta( $product->get_id(), '_sofir_timer_end_date', true );
		if ( ! $end_date ) {
			return;
		}

		$end_timestamp = \strtotime( $end_date );
		if ( $end_timestamp < time() ) {
			return;
		}

		$timer_text = \get_post_meta( $product->get_id(), '_sofir_timer_text', true );
		if ( ! $timer_text ) {
			$timer_text = \__( 'Limited Time Offer', 'sofir' );
		}

		$class = $is_shop ? 'sofir-product-timer-shop' : 'sofir-product-timer-single';
		?>
		<div class="sofir-product-timer <?php echo esc_attr( $class ); ?>" data-end-time="<?php echo esc_attr( $end_timestamp ); ?>">
			<div class="sofir-timer-text"><?php echo esc_html( $timer_text ); ?></div>
			<div class="sofir-timer-countdown">
				<div class="sofir-timer-box">
					<span class="sofir-timer-number" data-unit="days">0</span>
					<span class="sofir-timer-label"><?php esc_html_e( 'Days', 'sofir' ); ?></span>
				</div>
				<div class="sofir-timer-box">
					<span class="sofir-timer-number" data-unit="hours">0</span>
					<span class="sofir-timer-label"><?php esc_html_e( 'Hours', 'sofir' ); ?></span>
				</div>
				<div class="sofir-timer-box">
					<span class="sofir-timer-number" data-unit="minutes">0</span>
					<span class="sofir-timer-label"><?php esc_html_e( 'Min', 'sofir' ); ?></span>
				</div>
				<div class="sofir-timer-box">
					<span class="sofir-timer-number" data-unit="seconds">0</span>
					<span class="sofir-timer-label"><?php esc_html_e( 'Sec', 'sofir' ); ?></span>
				</div>
			</div>
		</div>
		<?php
	}

	public function render_settings(): void {
		$default_text = $this->get_option( 'default_text', \__( 'Limited Time Offer', 'sofir' ) );
		$style = $this->get_option( 'style', 'gradient' );
		?>
		<tr>
			<th scope="row"><label for="default_text"><?php esc_html_e( 'Default Timer Text', 'sofir' ); ?></label></th>
			<td>
				<input type="text" id="default_text" name="sofir_wc_addon_product_timer_default_text" value="<?php echo esc_attr( $default_text ); ?>" class="regular-text">
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="style"><?php esc_html_e( 'Timer Style', 'sofir' ); ?></label></th>
			<td>
				<select id="style" name="sofir_wc_addon_product_timer_style">
					<option value="gradient" <?php selected( $style, 'gradient' ); ?>><?php esc_html_e( 'Gradient', 'sofir' ); ?></option>
					<option value="simple" <?php selected( $style, 'simple' ); ?>><?php esc_html_e( 'Simple', 'sofir' ); ?></option>
					<option value="modern" <?php selected( $style, 'modern' ); ?>><?php esc_html_e( 'Modern', 'sofir' ); ?></option>
				</select>
			</td>
		</tr>
		<?php
	}
}
