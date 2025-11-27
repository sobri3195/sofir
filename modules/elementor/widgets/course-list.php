<?php
namespace Sofir\Elementor\Widgets;

use Sofir\Elementor\BaseWidget;

class Course_List extends BaseWidget {
    public function get_categories() {
        return [ 'sofir-learning' ];
    }

    public function get_name() {
        return 'sofir-course-list';
    }

    public function get_title() {
        return \esc_html__( 'Course List', 'sofir' );
    }

    public function get_icon() {
        return 'eicon-archive-posts';
    }

    protected function register_controls() {
        $this->start_controls_section(
            'section_content',
            [
                'label' => \esc_html__( 'Content', 'sofir' ),
            ]
        );

        $this->add_control(
            'courses_per_page',
            [
                'label' => \esc_html__( 'Courses Per Page', 'sofir' ),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 6,
                'min' => 1,
                'max' => 100,
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
            'show_price',
            [
                'label' => \esc_html__( 'Show Price', 'sofir' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_instructor',
            [
                'label' => \esc_html__( 'Show Instructor', 'sofir' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_duration',
            [
                'label' => \esc_html__( 'Show Duration', 'sofir' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_lessons_count',
            [
                'label' => \esc_html__( 'Show Lessons Count', 'sofir' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_rating',
            [
                'label' => \esc_html__( 'Show Rating', 'sofir' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_enroll_button',
            [
                'label' => \esc_html__( 'Show Enroll Button', 'sofir' ),
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
            'postType' => 'course',
            'postsPerPage' => (int) $settings['courses_per_page'],
            'layout' => $settings['layout'],
            'columns' => (int) $settings['columns'],
            'showPrice' => $settings['show_price'] === 'yes',
            'showInstructor' => $settings['show_instructor'] === 'yes',
            'showDuration' => $settings['show_duration'] === 'yes',
            'showLessonsCount' => $settings['show_lessons_count'] === 'yes',
            'showRating' => $settings['show_rating'] === 'yes',
            'showEnrollButton' => $settings['show_enroll_button'] === 'yes',
        ];

        $this->render_block_content( 'sofir/course-list', $attributes );
    }
}
