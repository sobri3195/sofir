<?php
namespace Sofir\Elementor\Widgets;

use Sofir\Elementor\BaseWidget;

class Ring_Chart extends BaseWidget {
    public function get_name() {
        return 'sofir-ring-chart';
    }

    public function get_title() {
        return \esc_html__( 'Ring Chart', 'sofir' );
    }

    public function get_icon() {
        return 'eicon-circle';
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
                'default' => \esc_html__( 'Statistics', 'sofir' ),
            ]
        );

        $this->add_control(
            'data',
            [
                'label' => \esc_html__( 'Data (JSON)', 'sofir' ),
                'type' => \Elementor\Controls_Manager::TEXTAREA,
                'default' => '{"Category A": 30, "Category B": 40, "Category C": 30}',
                'description' => \esc_html__( 'Enter data in JSON format', 'sofir' ),
            ]
        );

        $this->end_controls_section();

        $this->add_style_controls();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();

        $attributes = [
            'title' => $settings['title'],
            'data' => $settings['data'],
        ];

        $this->render_block_content( 'sofir/ring-chart', $attributes );
    }
}
