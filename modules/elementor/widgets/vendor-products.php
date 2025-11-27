<?php
namespace Sofir\Elementor\Widgets;

use Sofir\Elementor\BaseWidget;

class Vendor_Products extends BaseWidget {
    public function get_categories() {
        return [ 'sofir-ecommerce' ];
    }

    public function get_name() {
        return 'sofir-vendor-products';
    }

    public function get_title() {
        return \esc_html__( 'Vendor Products', 'sofir' );
    }

    public function get_icon() {
        return 'eicon-products';
    }

    protected function register_controls() {
        $this->start_controls_section(
            'section_content',
            [
                'label' => \esc_html__( 'Content', 'sofir' ),
            ]
        );

        $this->add_control(
            'vendor_id',
            [
                'label' => \esc_html__( 'Vendor Store ID', 'sofir' ),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 0,
                'description' => \esc_html__( 'Leave 0 for current vendor or all products', 'sofir' ),
            ]
        );

        $this->add_control(
            'products_per_page',
            [
                'label' => \esc_html__( 'Products Per Page', 'sofir' ),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 12,
                'min' => 1,
                'max' => 100,
            ]
        );

        $this->add_control(
            'layout',
            [
                'label' => \esc_html__( 'Layout', 'sofir' ),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'grid',
                'options' => [
                    'grid' => \esc_html__( 'Grid', 'sofir' ),
                    'list' => \esc_html__( 'List', 'sofir' ),
                ],
            ]
        );

        $this->add_layout_controls();

        $this->add_control(
            'show_price',
            [
                'label' => \esc_html__( 'Show Price', 'sofir' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_vendor_info',
            [
                'label' => \esc_html__( 'Show Vendor Info', 'sofir' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_rating',
            [
                'label' => \esc_html__( 'Show Rating', 'sofir' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_add_to_cart',
            [
                'label' => \esc_html__( 'Show Add to Cart', 'sofir' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->end_controls_section();

        $this->add_style_controls();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();

        $attributes = [
            'postType' => 'vendor_product',
            'vendorId' => (int) $settings['vendor_id'],
            'postsPerPage' => (int) $settings['products_per_page'],
            'layout' => $settings['layout'],
            'columns' => (int) $settings['columns'],
            'showPrice' => $settings['show_price'] === 'yes',
            'showVendorInfo' => $settings['show_vendor_info'] === 'yes',
            'showRating' => $settings['show_rating'] === 'yes',
            'showAddToCart' => $settings['show_add_to_cart'] === 'yes',
        ];

        $this->render_block_content( 'sofir/post-feed', $attributes );
    }
}
