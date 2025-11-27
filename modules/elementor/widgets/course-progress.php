<?php
namespace Sofir\Elementor\Widgets;

use Sofir\Elementor\BaseWidget;

class Course_Progress extends BaseWidget {
    public function get_categories() {
        return [ 'sofir-learning' ];
    }

    public function get_name() {
        return 'sofir-course-progress';
    }

    public function get_title() {
        return \esc_html__( 'Course Progress', 'sofir' );
    }

    public function get_icon() {
        return 'eicon-skill-bar';
    }

    protected function register_controls() {
        $this->start_controls_section(
            'section_content',
            [
                'label' => \esc_html__( 'Content', 'sofir' ),
            ]
        );

        $this->add_control(
            'course_id',
            [
                'label' => \esc_html__( 'Course ID', 'sofir' ),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 0,
                'description' => \esc_html__( 'Leave 0 for current post', 'sofir' ),
            ]
        );

        $this->add_control(
            'show_percentage',
            [
                'label' => \esc_html__( 'Show Percentage', 'sofir' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_lesson_list',
            [
                'label' => \esc_html__( 'Show Lesson List', 'sofir' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_completion_status',
            [
                'label' => \esc_html__( 'Show Completion Status', 'sofir' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'progress_bar_color',
            [
                'label' => \esc_html__( 'Progress Bar Color', 'sofir' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#4CAF50',
            ]
        );

        $this->end_controls_section();

        $this->add_style_controls();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();

        $attributes = [
            'courseId' => (int) $settings['course_id'],
            'showPercentage' => $settings['show_percentage'] === 'yes',
            'showLessonList' => $settings['show_lesson_list'] === 'yes',
            'showCompletionStatus' => $settings['show_completion_status'] === 'yes',
            'progressBarColor' => $settings['progress_bar_color'],
        ];

        $this->render_block_content( 'sofir/course-progress', $attributes );
    }
}
