<?php
namespace Sofir\WooCommerceAddon\Addons;

class Product_Title_Limit extends Addon_Base {

	public function __construct() {
		$this->id          = 'product-title-limit';
		$this->name        = \__( 'Product Title Limit', 'sofir' );
		$this->description = \__( 'Shorten the product title on the shop, archive, and product pages to keep your store organized.', 'sofir' );
		$this->icon        = '📝';
		$this->category    = 'flexibility';

		parent::__construct();
	}

	public function init(): void {
		\add_filter( 'the_title', [ $this, 'limit_product_title' ], 10, 2 );
	}

	public function limit_product_title( string $title, int $post_id ): string {
		if ( \get_post_type( $post_id ) !== 'product' ) {
			return $title;
		}

		if ( \is_singular( 'product' ) && $this->get_option( 'limit_single', 'no' ) !== 'yes' ) {
			return $title;
		}

		$max_length = (int) $this->get_option( 'max_length', '50' );
		$suffix = $this->get_option( 'suffix', '...' );

		if ( \mb_strlen( $title ) > $max_length ) {
			$title = \mb_substr( $title, 0, $max_length ) . $suffix;
		}

		return $title;
	}

	public function render_settings(): void {
		$max_length = $this->get_option( 'max_length', '50' );
		$suffix = $this->get_option( 'suffix', '...' );
		$limit_single = $this->get_option( 'limit_single', 'no' );
		?>
		<tr>
			<th scope="row"><label for="max_length"><?php esc_html_e( 'Maximum Characters', 'sofir' ); ?></label></th>
			<td>
				<input type="number" id="max_length" name="sofir_wc_addon_product-title-limit_max_length" value="<?php echo esc_attr( $max_length ); ?>" min="10" max="200" />
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="suffix"><?php esc_html_e( 'Suffix', 'sofir' ); ?></label></th>
			<td>
				<input type="text" id="suffix" name="sofir_wc_addon_product-title-limit_suffix" value="<?php echo esc_attr( $suffix ); ?>" />
			</td>
		</tr>
		<tr>
			<th scope="row"><?php esc_html_e( 'Limit Single Product', 'sofir' ); ?></th>
			<td>
				<label>
					<input type="checkbox" name="sofir_wc_addon_product-title-limit_limit_single" value="yes" <?php checked( $limit_single, 'yes' ); ?>>
					<?php esc_html_e( 'Also limit title on single product pages', 'sofir' ); ?>
				</label>
			</td>
		</tr>
		<?php
	}
}
