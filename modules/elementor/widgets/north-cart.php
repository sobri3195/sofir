<?php
namespace Sofir\Elementor\Widgets;

use Sofir\Elementor\BaseWidget;

class North_Cart extends BaseWidget {
    public function get_name() {
        return 'sofir-north-cart';
    }

    public function get_title() {
        return \esc_html__( 'North Commerce Cart', 'sofir' );
    }

    public function get_icon() {
        return 'eicon-cart';
    }

    public function get_categories() {
        return [ 'sofir-ecommerce' ];
    }

    protected function register_controls() {
        $this->start_controls_section(
            'section_content',
            [
                'label' => \esc_html__( 'Content', 'sofir' ),
            ]
        );

        $this->add_control(
            'notice',
            [
                'type' => \Elementor\Controls_Manager::RAW_HTML,
                'raw' => \esc_html__( 'This widget displays the North Commerce shopping cart.', 'sofir' ),
                'content_classes' => 'elementor-descriptor',
            ]
        );

        $this->end_controls_section();

        $this->add_style_controls();
    }

    protected function render() {
        if ( ! function_exists( 'north_commerce_init' ) ) {
            echo '<p>' . \esc_html__( 'North Commerce is not installed or activated.', 'sofir' ) . '</p>';
            return;
        }

        if ( function_exists( 'north_commerce_cart' ) ) {
            echo north_commerce_cart();
        } else {
            echo do_shortcode( '[north_cart]' );
        }
    }
}
