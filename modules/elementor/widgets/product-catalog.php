<?php
namespace Sofir\Elementor\Widgets;

use Sofir\Elementor\BaseWidget;

class Product_Catalog extends BaseWidget {
    public function get_categories() {
        return [ 'sofir-ecommerce' ];
    }

    public function get_name() {
        return 'sofir-product-catalog';
    }

    public function get_title() {
        return \esc_html__( 'Product Catalog', 'sofir' );
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
            'title',
            [
                'label' => \esc_html__( 'Title', 'sofir' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => \esc_html__( 'Our Products', 'sofir' ),
            ]
        );

        $this->add_control(
            'columns',
            [
                'label' => \esc_html__( 'Columns', 'sofir' ),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => '3',
                'options' => [
                    '1' => '1',
                    '2' => '2',
                    '3' => '3',
                    '4' => '4',
                    '5' => '5',
                    '6' => '6',
                ],
            ]
        );

        $this->add_control(
            'limit',
            [
                'label' => \esc_html__( 'Products Per Page', 'sofir' ),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 12,
                'min' => -1,
                'description' => \esc_html__( 'Use -1 to show all products', 'sofir' ),
            ]
        );

        $this->add_control(
            'orderby',
            [
                'label' => \esc_html__( 'Order By', 'sofir' ),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'date',
                'options' => [
                    'date' => \esc_html__( 'Date', 'sofir' ),
                    'title' => \esc_html__( 'Title', 'sofir' ),
                    'price' => \esc_html__( 'Price', 'sofir' ),
                    'rand' => \esc_html__( 'Random', 'sofir' ),
                    'menu_order' => \esc_html__( 'Menu Order', 'sofir' ),
                ],
            ]
        );

        $this->add_control(
            'order',
            [
                'label' => \esc_html__( 'Order', 'sofir' ),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'DESC',
                'options' => [
                    'ASC' => \esc_html__( 'Ascending', 'sofir' ),
                    'DESC' => \esc_html__( 'Descending', 'sofir' ),
                ],
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
            'show_price',
            [
                'label' => \esc_html__( 'Show Price', 'sofir' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_sale_badge',
            [
                'label' => \esc_html__( 'Show Sale Badge', 'sofir' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_description',
            [
                'label' => \esc_html__( 'Show Description', 'sofir' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'no',
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

        $this->add_control(
            'button_text',
            [
                'label' => \esc_html__( 'Button Text', 'sofir' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => \esc_html__( 'Buy Now', 'sofir' ),
                'condition' => [
                    'show_add_to_cart' => 'yes',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_grid',
            [
                'label' => \esc_html__( 'Grid Style', 'sofir' ),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'grid_gap',
            [
                'label' => \esc_html__( 'Gap', 'sofir' ),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'size' => 20,
                ],
                'selectors' => [
                    '{{WRAPPER}} .sofir-product-catalog-widget .products-grid' => 'gap: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_card',
            [
                'label' => \esc_html__( 'Product Card', 'sofir' ),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'card_background',
            [
                'label' => \esc_html__( 'Background Color', 'sofir' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .product-card' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'card_padding',
            [
                'label' => \esc_html__( 'Padding', 'sofir' ),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .product-card' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'card_border',
                'selector' => '{{WRAPPER}} .product-card',
            ]
        );

        $this->add_control(
            'card_border_radius',
            [
                'label' => \esc_html__( 'Border Radius', 'sofir' ),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .product-card' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'card_box_shadow',
                'selector' => '{{WRAPPER}} .product-card',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_title',
            [
                'label' => \esc_html__( 'Product Title', 'sofir' ),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'title_color',
            [
                'label' => \esc_html__( 'Color', 'sofir' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .product-card .product-title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'title_typography',
                'selector' => '{{WRAPPER}} .product-card .product-title',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_price',
            [
                'label' => \esc_html__( 'Price', 'sofir' ),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'price_color',
            [
                'label' => \esc_html__( 'Color', 'sofir' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .product-card .product-price' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'price_typography',
                'selector' => '{{WRAPPER}} .product-card .product-price',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_button',
            [
                'label' => \esc_html__( 'Button', 'sofir' ),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'button_text_color',
            [
                'label' => \esc_html__( 'Text Color', 'sofir' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .product-card button' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'button_background',
            [
                'label' => \esc_html__( 'Background Color', 'sofir' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .product-card button' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'button_typography',
                'selector' => '{{WRAPPER}} .product-card button',
            ]
        );

        $this->add_control(
            'button_border_radius',
            [
                'label' => \esc_html__( 'Border Radius', 'sofir' ),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .product-card button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();

        $shortcode_atts = [
            'columns="' . esc_attr( $settings['columns'] ) . '"',
            'limit="' . esc_attr( $settings['limit'] ) . '"',
            'orderby="' . esc_attr( $settings['orderby'] ) . '"',
            'order="' . esc_attr( $settings['order'] ) . '"',
        ];

        if ( ! empty( $settings['title'] ) ) {
            $shortcode_atts[] = 'title="' . esc_attr( $settings['title'] ) . '"';
        }

        if ( $settings['show_image'] === 'yes' ) {
            $shortcode_atts[] = 'show_image="yes"';
        }

        if ( $settings['show_price'] === 'yes' ) {
            $shortcode_atts[] = 'show_price="yes"';
        }

        if ( $settings['show_sale_badge'] === 'yes' ) {
            $shortcode_atts[] = 'show_sale_badge="yes"';
        }

        if ( $settings['show_description'] === 'yes' ) {
            $shortcode_atts[] = 'show_description="yes"';
        }

        if ( $settings['show_add_to_cart'] === 'yes' ) {
            $shortcode_atts[] = 'show_add_to_cart="yes"';
        }

        if ( ! empty( $settings['button_text'] ) ) {
            $shortcode_atts[] = 'button_text="' . esc_attr( $settings['button_text'] ) . '"';
        }

        echo '<div class="sofir-product-catalog-widget">';
        echo \do_shortcode( '[sofir_product_catalog ' . implode( ' ', $shortcode_atts ) . ']' );
        echo '</div>';
    }
}
