<?php
namespace Sofir\Elementor\Widgets;

use Sofir\Elementor\BaseWidget;

class Countdown extends BaseWidget {
    public function get_name() {
        return 'sofir-countdown';
    }

    public function get_title() {
        return \esc_html__( 'Countdown', 'sofir' );
    }

    public function get_icon() {
        return 'eicon-countdown';
    }

    protected function register_controls() {
        $this->start_controls_section(
            'section_content',
            [
                'label' => \esc_html__( 'Content', 'sofir' ),
            ]
        );

        $this->add_control(
            'target_date',
            [
                'label' => \esc_html__( 'Target Date', 'sofir' ),
                'type' => \Elementor\Controls_Manager::DATE_TIME,
                'default' => date( 'Y-m-d H:i', strtotime( '+1 month' ) ),
            ]
        );

        $this->add_control(
            'title',
            [
                'label' => \esc_html__( 'Title', 'sofir' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => \esc_html__( 'Coming Soon', 'sofir' ),
            ]
        );

        $this->end_controls_section();

        $this->add_style_controls();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();

        $attributes = [
            'targetDate' => $settings['target_date'],
            'title' => $settings['title'],
        ];

        $this->render_block_content( 'sofir/countdown', $attributes );
    }
}
