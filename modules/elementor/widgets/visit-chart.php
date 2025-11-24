<?php
namespace Sofir\Elementor\Widgets;

use Sofir\Elementor\BaseWidget;

class Visit_Chart extends BaseWidget {
    public function get_name() {
        return 'sofir-visit-chart';
    }

    public function get_title() {
        return \esc_html__( 'Visit Chart', 'sofir' );
    }

    public function get_icon() {
        return 'eicon-bar-chart';
    }

    protected function register_controls() {
        $this->start_controls_section(
            'section_content',
            [
                'label' => \esc_html__( 'Content', 'sofir' ),
            ]
        );

        $this->add_control(
            'period',
            [
                'label' => \esc_html__( 'Period', 'sofir' ),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => '7days',
                'options' => [
                    '7days' => \esc_html__( '7 Days', 'sofir' ),
                    '30days' => \esc_html__( '30 Days', 'sofir' ),
                    '90days' => \esc_html__( '90 Days', 'sofir' ),
                    '365days' => \esc_html__( '365 Days', 'sofir' ),
                ],
            ]
        );

        $this->add_control(
            'title',
            [
                'label' => \esc_html__( 'Title', 'sofir' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => \esc_html__( 'Visitor Statistics', 'sofir' ),
            ]
        );

        $this->end_controls_section();

        $this->add_style_controls();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();

        $attributes = [
            'period' => $settings['period'],
            'title' => $settings['title'],
        ];

        $this->render_block_content( 'sofir/visit-chart', $attributes );
    }
}
