<?php
namespace Sofir\Elementor\Widgets;

use Sofir\Elementor\BaseWidget;

class My_Courses extends BaseWidget {
    public function get_categories() {
        return [ 'sofir-learning' ];
    }

    public function get_name() {
        return 'sofir-my-courses';
    }

    public function get_title() {
        return \esc_html__( 'My Courses', 'sofir' );
    }

    public function get_icon() {
        return 'eicon-my-account';
    }

    protected function register_controls() {
        $this->start_controls_section(
            'section_content',
            [
                'label' => \esc_html__( 'Content', 'sofir' ),
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
            'show_progress',
            [
                'label' => \esc_html__( 'Show Progress', 'sofir' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_continue_button',
            [
                'label' => \esc_html__( 'Show Continue Button', 'sofir' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_certificate',
            [
                'label' => \esc_html__( 'Show Certificate Link', 'sofir' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'filter_status',
            [
                'label' => \esc_html__( 'Filter by Status', 'sofir' ),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'all',
                'options' => [
                    'all' => \esc_html__( 'All Courses', 'sofir' ),
                    'in-progress' => \esc_html__( 'In Progress', 'sofir' ),
                    'completed' => \esc_html__( 'Completed', 'sofir' ),
                ],
            ]
        );

        $this->end_controls_section();

        $this->add_style_controls();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();

        $attributes = [
            'layout' => $settings['layout'],
            'columns' => (int) $settings['columns'],
            'showProgress' => $settings['show_progress'] === 'yes',
            'showContinueButton' => $settings['show_continue_button'] === 'yes',
            'showCertificate' => $settings['show_certificate'] === 'yes',
            'filterStatus' => $settings['filter_status'],
        ];

        $this->render_block_content( 'sofir/my-courses', $attributes );
    }
}
