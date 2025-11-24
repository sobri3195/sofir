<?php
namespace Sofir\Elementor\Widgets;

use Sofir\Elementor\BaseWidget;

class Edd_Cart extends BaseWidget {
    public function get_name() {
        return 'sofir-edd-cart';
    }

    public function get_title() {
        return \esc_html__( 'EDD Cart', 'sofir' );
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
                'raw' => \esc_html__( 'This widget displays the EDD shopping cart.', 'sofir' ),
                'content_classes' => 'elementor-descriptor',
            ]
        );

        $this->end_controls_section();

        $this->add_style_controls();
    }

    protected function render() {
        if ( ! class_exists( 'Easy_Digital_Downloads' ) ) {
            echo '<p>' . \esc_html__( 'Easy Digital Downloads is not installed or activated.', 'sofir' ) . '</p>';
            return;
        }

        echo do_shortcode( '[download_cart]' );
    }
}
