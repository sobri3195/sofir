<?php
namespace Sofir\Elementor\Widgets;

use Sofir\Elementor\BaseWidget;

class Woocommerce_Categories extends BaseWidget {
    public function get_name() {
        return 'sofir-woocommerce-categories';
    }

    public function get_title() {
        return \esc_html__( 'WooCommerce Categories', 'sofir' );
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
                'label' => \esc_html__( 'Show Product Count', 'sofir' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->end_controls_section();

        $this->add_style_controls();
    }

    protected function render() {
        if ( ! class_exists( 'WooCommerce' ) ) {
            echo '<p>' . \esc_html__( 'WooCommerce is not installed or activated.', 'sofir' ) . '</p>';
            return;
        }

        $settings = $this->get_settings_for_display();

        $args = [
            'taxonomy' => 'product_cat',
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

        echo '<div class="sofir-wc-categories" style="display: grid; grid-template-columns: repeat(' . esc_attr( $columns ) . ', 1fr); gap: ' . esc_attr( $gap ) . 'px;">';

        foreach ( $categories as $category ) {
            $thumbnail_id = \get_term_meta( $category->term_id, 'thumbnail_id', true );
            $image_url = $thumbnail_id ? \wp_get_attachment_url( $thumbnail_id ) : \wc_placeholder_img_src();

            echo '<div class="sofir-wc-category">';
            echo '<a href="' . esc_url( get_term_link( $category ) ) . '">';
            
            if ( $image_url ) {
                echo '<img src="' . esc_url( $image_url ) . '" alt="' . esc_attr( $category->name ) . '" />';
            }
            
            echo '<h3>' . esc_html( $category->name ) . '</h3>';
            
            if ( $settings['show_count'] === 'yes' ) {
                echo '<span class="sofir-category-count">' . sprintf( 
                    \_n( '%s product', '%s products', $category->count, 'sofir' ), 
                    number_format_i18n( $category->count ) 
                ) . '</span>';
            }
            
            echo '</a>';
            echo '</div>';
        }

        echo '</div>';
    }
}
