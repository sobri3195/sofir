<?php
namespace Sofir\WooCommerceAddon\Addons;

class Variation_Swatches extends Addon_Base {

	public function __construct() {
		$this->id          = 'variation-swatches';
		$this->name        = \__( 'Variation Swatches', 'sofir' );
		$this->description = \__( 'Convert product attributes into beautiful swatches to ensure effortless shopping experiences.', 'sofir' );
		$this->icon        = '🎨';
		$this->category    = 'builder';

		parent::__construct();
	}

	public function init(): void {
		\add_filter( 'woocommerce_dropdown_variation_attribute_options_html', [ $this, 'variation_attribute_options' ], 10, 2 );
		\add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
	}

	public function enqueue_scripts(): void {
		\wp_add_inline_style( 'woocommerce-general', $this->get_swatch_css() );
	}

	public function variation_attribute_options( string $html, array $args ): string {
		$attribute = $args['attribute'];
		$options = $args['options'];
		$product = $args['product'];
		$show_option_none = $args['show_option_none'];

		if ( empty( $options ) && ! empty( $product ) && ! empty( $attribute ) ) {
			$attributes = $product->get_variation_attributes();
			$options = $attributes[ $attribute ];
		}

		$swatch_type = $this->get_option( 'swatch_type', 'color' );
		
		$html = '<div class="sofir-variation-swatches" data-attribute="' . esc_attr( $attribute ) . '">';
		
		if ( ! empty( $show_option_none ) ) {
			$html .= '<span class="swatch-item" data-value="">' . esc_html( $show_option_none ) . '</span>';
		}

		foreach ( $options as $option ) {
			$html .= $this->render_swatch( $option, $swatch_type );
		}
		
		$html .= '</div>';

		return $html;
	}

	private function render_swatch( string $value, string $type ): string {
		$style = '';
		$class = 'swatch-item';

		if ( $type === 'color' ) {
			$color = $this->get_color_for_attribute( $value );
			$style = 'background-color: ' . esc_attr( $color ) . ';';
			$class .= ' swatch-color';
		}

		return sprintf(
			'<span class="%s" data-value="%s" style="%s" title="%s">%s</span>',
			esc_attr( $class ),
			esc_attr( $value ),
			$style,
			esc_attr( $value ),
			esc_html( $value )
		);
	}

	private function get_color_for_attribute( string $value ): string {
		$colors = [
			'red' => '#ff0000',
			'blue' => '#0000ff',
			'green' => '#00ff00',
			'black' => '#000000',
			'white' => '#ffffff',
		];

		return $colors[ strtolower( $value ) ] ?? '#cccccc';
	}

	private function get_swatch_css(): string {
		return '
		.sofir-variation-swatches {
			display: flex;
			flex-wrap: wrap;
			gap: 10px;
			margin: 10px 0;
		}
		.sofir-variation-swatches .swatch-item {
			display: inline-block;
			padding: 8px 15px;
			border: 2px solid #ddd;
			border-radius: 4px;
			cursor: pointer;
			transition: all 0.3s ease;
		}
		.sofir-variation-swatches .swatch-item:hover,
		.sofir-variation-swatches .swatch-item.selected {
			border-color: #000;
			transform: scale(1.05);
		}
		.sofir-variation-swatches .swatch-color {
			width: 40px;
			height: 40px;
			padding: 0;
			text-indent: -9999px;
		}
		';
	}

	public function render_settings(): void {
		$swatch_type = $this->get_option( 'swatch_type', 'color' );
		$shape = $this->get_option( 'shape', 'square' );
		?>
		<tr>
			<th scope="row"><label for="swatch_type"><?php esc_html_e( 'Swatch Type', 'sofir' ); ?></label></th>
			<td>
				<select id="swatch_type" name="sofir_wc_addon_variation-swatches_swatch_type">
					<option value="color" <?php selected( $swatch_type, 'color' ); ?>><?php esc_html_e( 'Color', 'sofir' ); ?></option>
					<option value="image" <?php selected( $swatch_type, 'image' ); ?>><?php esc_html_e( 'Image', 'sofir' ); ?></option>
					<option value="label" <?php selected( $swatch_type, 'label' ); ?>><?php esc_html_e( 'Label', 'sofir' ); ?></option>
				</select>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="shape"><?php esc_html_e( 'Shape', 'sofir' ); ?></label></th>
			<td>
				<select id="shape" name="sofir_wc_addon_variation-swatches_shape">
					<option value="square" <?php selected( $shape, 'square' ); ?>><?php esc_html_e( 'Square', 'sofir' ); ?></option>
					<option value="circle" <?php selected( $shape, 'circle' ); ?>><?php esc_html_e( 'Circle', 'sofir' ); ?></option>
				</select>
			</td>
		</tr>
		<?php
	}
}
