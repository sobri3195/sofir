<?php
namespace Sofir\Elementor\Widgets;

use Sofir\Elementor\BaseWidget;

class Event_List extends BaseWidget {
    public function get_categories() {
        return [ 'sofir-booking' ];
    }

    public function get_name() {
        return 'sofir-event-list';
    }

    public function get_title() {
        return \esc_html__( 'Event List', 'sofir' );
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
            'posts_per_page',
            [
                'label' => \esc_html__( 'Events Per Page', 'sofir' ),
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
            'show_date',
            [
                'label' => \esc_html__( 'Show Date', 'sofir' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_location',
            [
                'label' => \esc_html__( 'Show Location', 'sofir' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_capacity',
            [
                'label' => \esc_html__( 'Show Capacity', 'sofir' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_thumbnail',
            [
                'label' => \esc_html__( 'Show Thumbnail', 'sofir' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'filter_upcoming',
            [
                'label' => \esc_html__( 'Show Upcoming Only', 'sofir' ),
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
            'postType' => 'event',
            'postsPerPage' => (int) $settings['posts_per_page'],
            'layout' => $settings['layout'],
            'columns' => (int) $settings['columns'],
            'showDate' => $settings['show_date'] === 'yes',
            'showLocation' => $settings['show_location'] === 'yes',
            'showCapacity' => $settings['show_capacity'] === 'yes',
            'showThumbnail' => $settings['show_thumbnail'] === 'yes',
            'filterUpcoming' => $settings['filter_upcoming'] === 'yes',
        ];

        $this->render_block_content( 'sofir/post-feed', $attributes );
    }
}
