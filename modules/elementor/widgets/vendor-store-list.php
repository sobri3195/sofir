<?php
namespace Sofir\Elementor\Widgets;

use Sofir\Elementor\BaseWidget;

class Vendor_Store_List extends BaseWidget {
    public function get_categories() {
        return [ 'sofir-ecommerce' ];
    }

    public function get_name() {
        return 'sofir-vendor-store-list';
    }

    public function get_title() {
        return \esc_html__( 'Vendor Store List', 'sofir' );
    }

    public function get_icon() {
        return 'eicon-sitemap';
    }

    protected function register_controls() {
        $this->start_controls_section(
            'section_content',
            [
                'label' => \esc_html__( 'Content', 'sofir' ),
            ]
        );

        $this->add_control(
            'stores_per_page',
            [
                'label' => \esc_html__( 'Stores Per Page', 'sofir' ),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 9,
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
            'show_logo',
            [
                'label' => \esc_html__( 'Show Store Logo', 'sofir' ),
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
            'show_product_count',
            [
                'label' => \esc_html__( 'Show Product Count', 'sofir' ),
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
            'show_location',
            [
                'label' => \esc_html__( 'Show Location', 'sofir' ),
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
            'postType' => 'vendor_store',
            'postsPerPage' => (int) $settings['stores_per_page'],
            'layout' => $settings['layout'],
            'columns' => (int) $settings['columns'],
            'showLogo' => $settings['show_logo'] === 'yes',
            'showDescription' => $settings['show_description'] === 'yes',
            'showProductCount' => $settings['show_product_count'] === 'yes',
            'showRating' => $settings['show_rating'] === 'yes',
            'showLocation' => $settings['show_location'] === 'yes',
        ];

        $this->render_block_content( 'sofir/post-feed', $attributes );
    }
}
