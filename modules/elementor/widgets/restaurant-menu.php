<?php
namespace Sofir\Elementor\Widgets;

use Sofir\Elementor\BaseWidget;

class Restaurant_Menu extends BaseWidget {
    public function get_categories() {
        return [ 'sofir-booking' ];
    }

    public function get_name() {
        return 'sofir-restaurant-menu';
    }

    public function get_title() {
        return \esc_html__( 'Restaurant Menu', 'sofir' );
    }

    public function get_icon() {
        return 'eicon-product-categories';
    }

    protected function register_controls() {
        $this->start_controls_section(
            'section_content',
            [
                'label' => \esc_html__( 'Content', 'sofir' ),
            ]
        );

        $this->add_control(
            'items_per_page',
            [
                'label' => \esc_html__( 'Items Per Page', 'sofir' ),
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
            'show_category_filter',
            [
                'label' => \esc_html__( 'Show Category Filter', 'sofir' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_price',
            [
                'label' => \esc_html__( 'Show Price', 'sofir' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_description',
            [
                'label' => \esc_html__( 'Show Description', 'sofir' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_image',
            [
                'label' => \esc_html__( 'Show Image', 'sofir' ),
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
            'postType' => 'menu_item',
            'postsPerPage' => (int) $settings['items_per_page'],
            'layout' => $settings['layout'],
            'columns' => (int) $settings['columns'],
            'showCategoryFilter' => $settings['show_category_filter'] === 'yes',
            'showPrice' => $settings['show_price'] === 'yes',
            'showDescription' => $settings['show_description'] === 'yes',
            'showImage' => $settings['show_image'] === 'yes',
            'showAddToCart' => $settings['show_add_to_cart'] === 'yes',
        ];

        $this->render_block_content( 'sofir/restaurant-menu', $attributes );
    }
}
