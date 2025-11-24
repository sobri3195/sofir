<?php
namespace Sofir\Elementor\Widgets;

use Sofir\Elementor\BaseWidget;

class Woocommerce_Cart extends BaseWidget {
    public function get_name() {
        return 'sofir-woocommerce-cart';
    }

    public function get_title() {
        return \esc_html__( 'WooCommerce Cart', 'sofir' );
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
            'show_coupon',
            [
                'label' => \esc_html__( 'Show Coupon Form', 'sofir' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_update_cart',
            [
                'label' => \esc_html__( 'Show Update Cart Button', 'sofir' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->end_controls_section();

        $this->add_style_controls();
    }

    protected function render() {
        if ( ! class_exists( 'WooCommerce' ) ) {
            echo '<p>' . \esc_html__( 'WooCommerce is not installed or activated.', 'sofir' ) . '</p>';
            return;
        }

        echo do_shortcode( '[woocommerce_cart]' );
    }
}
