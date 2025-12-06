<?php
namespace Sofir\WooCommerceAddon\Addons;

class Size_Chart extends Addon_Base {

	public function __construct() {
		$this->id          = 'size-chart';
		$this->name        = \__( 'Size Chart', 'sofir' );
		$this->description = \__( 'Create & display size charts to help the potential buyers make better buying decisions.', 'sofir' );
		$this->icon        = '📏';
		$this->category    = 'flexibility';

		parent::__construct();
	}

	public function init(): void {
		\add_action( 'woocommerce_single_product_summary', [ $this, 'add_size_chart_button' ], 35 );
		\add_action( 'wp_footer', [ $this, 'render_size_chart_modal' ] );
		\add_action( 'woocommerce_product_options_general_product_data', [ $this, 'add_size_chart_field' ] );
		\add_action( 'woocommerce_process_product_meta', [ $this, 'save_size_chart_field' ] );
		\add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
	}

	public function enqueue_scripts(): void {
		if ( ! \is_product() ) {
			return;
		}

		\wp_add_inline_style( 'woocommerce-general', $this->get_size_chart_css() );
		\wp_add_inline_script( 'jquery', $this->get_size_chart_js() );
	}

	public function add_size_chart_button(): void {
		global $product;

		$chart_data = \get_post_meta( $product->get_id(), '_size_chart_data', true );

		if ( empty( $chart_data ) ) {
			return;
		}

		?>
		<button type="button" class="button sofir-size-chart-btn" data-product-id="<?php echo esc_attr( $product->get_id() ); ?>">
			📏 <?php esc_html_e( 'Size Guide', 'sofir' ); ?>
		</button>
		<?php
	}

	public function add_size_chart_field(): void {
		?>
		<div class="options_group">
			<?php
			\woocommerce_wp_textarea_input( [
				'id' => '_size_chart_data',
				'label' => \__( 'Size Chart (HTML)', 'sofir' ),
				'desc_tip' => true,
				'description' => \__( 'Enter HTML table for size chart', 'sofir' ),
			] );
			?>
		</div>
		<?php
	}

	public function save_size_chart_field( int $post_id ): void {
		$chart_data = isset( $_POST['_size_chart_data'] ) ? \wp_kses_post( $_POST['_size_chart_data'] ) : '';
		\update_post_meta( $post_id, '_size_chart_data', $chart_data );
	}

	public function render_size_chart_modal(): void {
		if ( ! \is_product() ) {
			return;
		}

		global $product;
		$chart_data = \get_post_meta( $product->get_id(), '_size_chart_data', true );

		if ( empty( $chart_data ) ) {
			return;
		}

		?>
		<div id="sofir-size-chart-modal" class="sofir-modal">
			<div class="modal-content">
				<span class="close-modal">&times;</span>
				<h2><?php esc_html_e( 'Size Guide', 'sofir' ); ?></h2>
				<div class="size-chart-content">
					<?php echo \wp_kses_post( $chart_data ); ?>
				</div>
			</div>
		</div>
		<?php
	}

	private function get_size_chart_css(): string {
		return '
		.sofir-size-chart-btn {
			margin: 10px 0;
		}
		.sofir-modal {
			display: none;
			position: fixed;
			z-index: 10000;
			left: 0;
			top: 0;
			width: 100%;
			height: 100%;
			background-color: rgba(0,0,0,0.5);
		}
		.sofir-modal .modal-content {
			background-color: #fff;
			margin: 5% auto;
			padding: 30px;
			width: 80%;
			max-width: 800px;
			border-radius: 8px;
			position: relative;
		}
		.sofir-modal .close-modal {
			position: absolute;
			right: 15px;
			top: 15px;
			font-size: 28px;
			font-weight: bold;
			cursor: pointer;
		}
		.size-chart-content table {
			width: 100%;
			border-collapse: collapse;
		}
		.size-chart-content table th,
		.size-chart-content table td {
			border: 1px solid #ddd;
			padding: 10px;
			text-align: center;
		}
		.size-chart-content table th {
			background: #f5f5f5;
		}
		';
	}

	private function get_size_chart_js(): string {
		return "
		jQuery(function($) {
			$('.sofir-size-chart-btn').on('click', function() {
				$('#sofir-size-chart-modal').fadeIn();
			});
			$('.close-modal').on('click', function() {
				$('.sofir-modal').fadeOut();
			});
			$(window).on('click', function(e) {
				if ($(e.target).hasClass('sofir-modal')) {
					$('.sofir-modal').fadeOut();
				}
			});
		});
		";
	}

	public function render_settings(): void {
		$button_text = $this->get_option( 'button_text', 'Size Guide' );
		?>
		<tr>
			<th scope="row"><label for="button_text"><?php esc_html_e( 'Button Text', 'sofir' ); ?></label></th>
			<td>
				<input type="text" id="button_text" name="sofir_wc_addon_size-chart_button_text" value="<?php echo esc_attr( $button_text ); ?>" />
			</td>
		</tr>
		<?php
	}
}
