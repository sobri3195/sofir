<?php
namespace Sofir\Elementor\Widgets;

use Sofir\Elementor\BaseWidget;

class Appointment_Form extends BaseWidget {
    public function get_name() {
        return 'sofir-appointment-form';
    }

    public function get_title() {
        return \esc_html__( 'Appointment Form', 'sofir' );
    }

    public function get_icon() {
        return 'eicon-calendar';
    }

    protected function register_controls() {
        $this->start_controls_section(
            'section_content',
            [
                'label' => \esc_html__( 'Content', 'sofir' ),
            ]
        );

        $this->add_control(
            'provider_id',
            [
                'label' => \esc_html__( 'Provider ID', 'sofir' ),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 0,
                'description' => \esc_html__( 'Leave 0 for current post', 'sofir' ),
            ]
        );

        $this->add_control(
            'show_calendar',
            [
                'label' => \esc_html__( 'Show Calendar', 'sofir' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_time_slots',
            [
                'label' => \esc_html__( 'Show Time Slots', 'sofir' ),
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
            'providerId' => (int) $settings['provider_id'],
            'showCalendar' => $settings['show_calendar'] === 'yes',
            'showTimeSlots' => $settings['show_time_slots'] === 'yes',
        ];

        $this->render_block_content( 'sofir/appointment-form', $attributes );
    }
}
