<?php
namespace Sofir\Elementor\Widgets;

use Sofir\Elementor\BaseWidget;

class Edd_Categories extends BaseWidget {
    public function get_name() {
        return 'sofir-edd-categories';
    }

    public function get_title() {
        return \esc_html__( 'EDD Categories', 'sofir' );
    }

    public function get_icon() {
        return 'eicon-product-categories';
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
            'number',
            [
                'label' => \esc_html__( 'Number of Categories', 'sofir' ),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 8,
                'min' => 1,
                'max' => 100,
            ]
        );

        $this->add_control(
            'hide_empty',
            [
                'label' => \esc_html__( 'Hide Empty Categories', 'sofir' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_layout_controls();

        $this->add_control(
            'show_count',
            [
                'label' => \esc_html__( 'Show Download Count', 'sofir' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
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

        $args = [
            'taxonomy' => 'download_category',
            'number' => (int) $settings['number'],
            'hide_empty' => $settings['hide_empty'] === 'yes',
        ];

        $categories = \get_terms( $args );

        if ( is_wp_error( $categories ) || empty( $categories ) ) {
            echo '<p>' . \esc_html__( 'No categories found.', 'sofir' ) . '</p>';
            return;
        }

        $columns = (int) $settings['columns'];
        $gap = isset( $settings['gap']['size'] ) ? (int) $settings['gap']['size'] : 20;

        echo '<div class="sofir-edd-categories" style="display: grid; grid-template-columns: repeat(' . esc_attr( $columns ) . ', 1fr); gap: ' . esc_attr( $gap ) . 'px;">';

        foreach ( $categories as $category ) {
            echo '<div class="sofir-edd-category">';
            echo '<a href="' . esc_url( get_term_link( $category ) ) . '">';
            echo '<h3>' . esc_html( $category->name ) . '</h3>';
            
            if ( $settings['show_count'] === 'yes' ) {
                echo '<span class="sofir-category-count">' . sprintf( 
                    \_n( '%s download', '%s downloads', $category->count, 'sofir' ), 
                    number_format_i18n( $category->count ) 
                ) . '</span>';
            }
            
            echo '</a>';
            echo '</div>';
        }

        echo '</div>';
    }
}
