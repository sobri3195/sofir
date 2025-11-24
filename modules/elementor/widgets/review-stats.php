<?php
namespace Sofir\Elementor\Widgets;

use Sofir\Elementor\BaseWidget;

class Review_Stats extends BaseWidget {
    public function get_name() {
        return 'sofir-review-stats';
    }

    public function get_title() {
        return \esc_html__( 'Review Stats', 'sofir' );
    }

    public function get_icon() {
        return 'eicon-star';
    }

    protected function register_controls() {
        $this->start_controls_section(
            'section_content',
            [
                'label' => \esc_html__( 'Content', 'sofir' ),
            ]
        );

        $this->add_control(
            'average_rating',
            [
                'label' => \esc_html__( 'Average Rating', 'sofir' ),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 4.5,
                'min' => 0,
                'max' => 5,
                'step' => 0.1,
            ]
        );

        $this->add_control(
            'total_reviews',
            [
                'label' => \esc_html__( 'Total Reviews', 'sofir' ),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 150,
                'min' => 0,
            ]
        );

        $this->end_controls_section();

        $this->add_style_controls();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();

        $attributes = [
            'averageRating' => (float) $settings['average_rating'],
            'totalReviews' => (int) $settings['total_reviews'],
        ];

        $this->render_block_content( 'sofir/review-stats', $attributes );
    }
}
