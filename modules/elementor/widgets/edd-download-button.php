<?php
namespace Sofir\Elementor\Widgets;

use Sofir\Elementor\BaseWidget;

class Edd_Download_Button extends BaseWidget {
    public function get_name() {
        return 'sofir-edd-download-button';
    }

    public function get_title() {
        return \esc_html__( 'EDD Download Button', 'sofir' );
    }

    public function get_icon() {
        return 'eicon-button';
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
            'download_id',
            [
                'label' => \esc_html__( 'Download ID', 'sofir' ),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 0,
                'description' => \esc_html__( 'Leave 0 for current post', 'sofir' ),
            ]
        );

        $this->add_control(
            'button_text',
            [
                'label' => \esc_html__( 'Button Text', 'sofir' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => \esc_html__( 'Purchase', 'sofir' ),
            ]
        );

        $this->add_control(
            'button_style',
            [
                'label' => \esc_html__( 'Button Style', 'sofir' ),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'button',
                'options' => [
                    'button' => \esc_html__( 'Button', 'sofir' ),
                    'plain' => \esc_html__( 'Plain', 'sofir' ),
                ],
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

        $settings = $this->get_settings_for_display();
        $download_id = (int) $settings['download_id'];

        if ( $download_id === 0 ) {
            $download_id = get_the_ID();
        }

        echo edd_get_purchase_link( [
            'download_id' => $download_id,
            'text' => $settings['button_text'],
            'style' => $settings['button_style'],
        ] );
    }
}
