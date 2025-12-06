<?php
namespace Sofir\WooCommerceAddon\Addons;

class Product_Addons extends Addon_Base {

	public function __construct() {
		$this->id          = 'product_addons';
		$this->name        = \__( 'Product Addons', 'sofir' );
		$this->description = \__( 'Add extra product options and custom fields. Increase average order value.', 'sofir' );
		$this->icon        = '➕';
		$this->category    = 'products';

		parent::__construct();
	}

	public function init(): void {
		\add_action( 'woocommerce_product_options_advanced', [ $this, 'add_addon_fields' ] );
		\add_action( 'woocommerce_process_product_meta', [ $this, 'save_addon_fields' ] );
		\add_action( 'woocommerce_before_add_to_cart_button', [ $this, 'display_addons' ] );
		\add_filter( 'woocommerce_add_to_cart_validation', [ $this, 'validate_addons' ], 10, 3 );
		\add_filter( 'woocommerce_add_cart_item_data', [ $this, 'add_cart_item_data' ], 10, 2 );
		\add_filter( 'woocommerce_get_item_data', [ $this, 'display_cart_item_data' ], 10, 2 );
		\add_action( 'woocommerce_before_calculate_totals', [ $this, 'adjust_cart_item_price' ], 10, 1 );
	}

	public function add_addon_fields(): void {
		global $post;
		?>
		<div class="options_group sofir-addons-fields">
			<p class="form-field">
				<label><?php esc_html_e( 'Product Addons', 'sofir' ); ?></label>
				<button type="button" class="button sofir-add-addon"><?php esc_html_e( 'Add Addon', 'sofir' ); ?></button>
			</p>
			
			<div class="sofir-addons-list">
				<?php
				$addons = \get_post_meta( $post->ID, '_sofir_product_addons', true );
				if ( ! empty( $addons ) && is_array( $addons ) ) {
					foreach ( $addons as $index => $addon ) {
						$this->render_addon_field( $index, $addon );
					}
				}
				?>
			</div>
		</div>

		<script type="text/template" id="sofir-addon-template">
			<?php $this->render_addon_field( '{{INDEX}}', [] ); ?>
		</script>
		<?php
	}

	private function render_addon_field( $index, array $addon ): void {
		$name = $addon['name'] ?? '';
		$type = $addon['type'] ?? 'text';
		$price = $addon['price'] ?? '';
		$required = $addon['required'] ?? 'no';
		?>
		<div class="sofir-addon-item">
			<p>
				<label><?php esc_html_e( 'Name', 'sofir' ); ?></label>
				<input type="text" name="_sofir_product_addons[<?php echo esc_attr( $index ); ?>][name]" value="<?php echo esc_attr( $name ); ?>" class="regular-text">
			</p>
			<p>
				<label><?php esc_html_e( 'Type', 'sofir' ); ?></label>
				<select name="_sofir_product_addons[<?php echo esc_attr( $index ); ?>][type]">
					<option value="text" <?php selected( $type, 'text' ); ?>><?php esc_html_e( 'Text', 'sofir' ); ?></option>
					<option value="checkbox" <?php selected( $type, 'checkbox' ); ?>><?php esc_html_e( 'Checkbox', 'sofir' ); ?></option>
					<option value="select" <?php selected( $type, 'select' ); ?>><?php esc_html_e( 'Dropdown', 'sofir' ); ?></option>
				</select>
			</p>
			<p>
				<label><?php esc_html_e( 'Extra Price', 'sofir' ); ?></label>
				<input type="number" step="0.01" name="_sofir_product_addons[<?php echo esc_attr( $index ); ?>][price]" value="<?php echo esc_attr( $price ); ?>" class="small-text">
			</p>
			<p>
				<label>
					<input type="checkbox" name="_sofir_product_addons[<?php echo esc_attr( $index ); ?>][required]" value="yes" <?php checked( $required, 'yes' ); ?>>
					<?php esc_html_e( 'Required', 'sofir' ); ?>
				</label>
			</p>
			<p>
				<button type="button" class="button sofir-remove-addon"><?php esc_html_e( 'Remove', 'sofir' ); ?></button>
			</p>
		</div>
		<?php
	}

	public function save_addon_fields( int $post_id ): void {
		if ( isset( $_POST['_sofir_product_addons'] ) && is_array( $_POST['_sofir_product_addons'] ) ) {
			$addons = [];
			foreach ( $_POST['_sofir_product_addons'] as $addon ) {
				if ( ! empty( $addon['name'] ) ) {
					$addons[] = [
						'name'     => \sanitize_text_field( $addon['name'] ),
						'type'     => \sanitize_text_field( $addon['type'] ?? 'text' ),
						'price'    => \sanitize_text_field( $addon['price'] ?? '0' ),
						'required' => isset( $addon['required'] ) ? 'yes' : 'no',
					];
				}
			}
			\update_post_meta( $post_id, '_sofir_product_addons', $addons );
		} else {
			\delete_post_meta( $post_id, '_sofir_product_addons' );
		}
	}

	public function display_addons(): void {
		global $product;

		$addons = \get_post_meta( $product->get_id(), '_sofir_product_addons', true );
		if ( empty( $addons ) || ! is_array( $addons ) ) {
			return;
		}

		echo '<div class="sofir-product-addons">';
		echo '<h4>' . esc_html__( 'Extra Options', 'sofir' ) . '</h4>';

		foreach ( $addons as $index => $addon ) {
			$field_name = 'sofir_addon_' . $index;
			$required = $addon['required'] === 'yes' ? ' <span class="required">*</span>' : '';
			$price_html = ! empty( $addon['price'] ) && $addon['price'] > 0 ? ' (+' . \wc_price( $addon['price'] ) . ')' : '';

			echo '<p class="sofir-addon-field">';
			echo '<label>' . esc_html( $addon['name'] ) . $required . $price_html . '</label>';

			switch ( $addon['type'] ) {
				case 'checkbox':
					echo '<input type="checkbox" name="' . esc_attr( $field_name ) . '" value="yes">';
					break;

				case 'select':
					echo '<select name="' . esc_attr( $field_name ) . '">';
					echo '<option value="">' . esc_html__( 'Select...', 'sofir' ) . '</option>';
					echo '<option value="yes">' . esc_html__( 'Yes', 'sofir' ) . '</option>';
					echo '</select>';
					break;

				default:
					echo '<input type="text" name="' . esc_attr( $field_name ) . '" class="input-text">';
					break;
			}

			echo '</p>';
		}

		echo '</div>';
	}

	public function validate_addons( bool $passed, int $product_id, int $quantity ): bool {
		$addons = \get_post_meta( $product_id, '_sofir_product_addons', true );
		if ( empty( $addons ) || ! is_array( $addons ) ) {
			return $passed;
		}

		foreach ( $addons as $index => $addon ) {
			if ( $addon['required'] === 'yes' ) {
				$field_name = 'sofir_addon_' . $index;
				if ( empty( $_POST[ $field_name ] ) ) {
					\wc_add_notice( sprintf( \__( '%s is required.', 'sofir' ), $addon['name'] ), 'error' );
					$passed = false;
				}
			}
		}

		return $passed;
	}

	public function add_cart_item_data( array $cart_item_data, int $product_id ): array {
		$addons = \get_post_meta( $product_id, '_sofir_product_addons', true );
		if ( empty( $addons ) || ! is_array( $addons ) ) {
			return $cart_item_data;
		}

		$addon_data = [];
		foreach ( $addons as $index => $addon ) {
			$field_name = 'sofir_addon_' . $index;
			if ( ! empty( $_POST[ $field_name ] ) ) {
				$addon_data[ $index ] = [
					'name'  => $addon['name'],
					'value' => \sanitize_text_field( $_POST[ $field_name ] ),
					'price' => (float) $addon['price'],
				];
			}
		}

		if ( ! empty( $addon_data ) ) {
			$cart_item_data['sofir_addons'] = $addon_data;
		}

		return $cart_item_data;
	}

	public function display_cart_item_data( array $item_data, array $cart_item ): array {
		if ( isset( $cart_item['sofir_addons'] ) && is_array( $cart_item['sofir_addons'] ) ) {
			foreach ( $cart_item['sofir_addons'] as $addon ) {
				$item_data[] = [
					'name'  => $addon['name'],
					'value' => $addon['value'],
				];
			}
		}

		return $item_data;
	}

	public function adjust_cart_item_price( $cart ): void {
		if ( \is_admin() && ! \defined( 'DOING_AJAX' ) ) {
			return;
		}

		foreach ( $cart->get_cart() as $cart_item ) {
			if ( isset( $cart_item['sofir_addons'] ) && is_array( $cart_item['sofir_addons'] ) ) {
				$extra_price = 0;
				foreach ( $cart_item['sofir_addons'] as $addon ) {
					$extra_price += (float) $addon['price'];
				}

				if ( $extra_price > 0 ) {
					$original_price = (float) $cart_item['data']->get_price();
					$cart_item['data']->set_price( $original_price + $extra_price );
				}
			}
		}
	}

	public function render_settings(): void {
		$show_in_summary = $this->get_option( 'show_in_summary', 'yes' );
		?>
		<tr>
			<th scope="row"><?php esc_html_e( 'Show in Summary', 'sofir' ); ?></th>
			<td>
				<label>
					<input type="checkbox" name="sofir_wc_addon_product_addons_show_in_summary" value="yes" <?php checked( $show_in_summary, 'yes' ); ?>>
					<?php esc_html_e( 'Display selected addons in cart and checkout', 'sofir' ); ?>
				</label>
			</td>
		</tr>
		<?php
	}
}
