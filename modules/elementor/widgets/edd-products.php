<?php
namespace Sofir\Elementor\Widgets;

use Sofir\Elementor\BaseWidget;

class Edd_Products extends BaseWidget {
    public function get_name() {
        return 'sofir-edd-products';
    }

    public function get_title() {
        return \esc_html__( 'EDD Products', 'sofir' );
    }

    public function get_icon() {
        return 'eicon-products';
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
            'products_per_page',
            [
                'label' => \esc_html__( 'Products Per Page', 'sofir' ),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 8,
                'min' => 1,
                'max' => 100,
            ]
        );

        $this->add_control(
            'order_by',
            [
                'label' => \esc_html__( 'Order By', 'sofir' ),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'date',
                'options' => [
                    'date' => \esc_html__( 'Date', 'sofir' ),
                    'title' => \esc_html__( 'Title', 'sofir' ),
                    'price' => \esc_html__( 'Price', 'sofir' ),
                    'sales' => \esc_html__( 'Sales', 'sofir' ),
                ],
            ]
        );

        $this->add_control(
            'order',
            [
                'label' => \esc_html__( 'Order', 'sofir' ),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'DESC',
                'options' => [
                    'ASC' => \esc_html__( 'Ascending', 'sofir' ),
                    'DESC' => \esc_html__( 'Descending', 'sofir' ),
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
            'show_buy_button',
            [
                'label' => \esc_html__( 'Show Buy Button', 'sofir' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $categories = \get_terms( [ 'taxonomy' => 'download_category', 'hide_empty' => false ] );
        $category_options = [ '' => \esc_html__( 'All Categories', 'sofir' ) ];
        if ( ! is_wp_error( $categories ) ) {
            foreach ( $categories as $category ) {
                $category_options[ $category->slug ] = $category->name;
            }
        }

        $this->add_control(
            'category',
            [
                'label' => \esc_html__( 'Category', 'sofir' ),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => '',
                'options' => $category_options,
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
            'post_type' => 'download',
            'posts_per_page' => (int) $settings['products_per_page'],
            'orderby' => $settings['order_by'],
            'order' => $settings['order'],
        ];

        if ( ! empty( $settings['category'] ) ) {
            $args['tax_query'] = [
                [
                    'taxonomy' => 'download_category',
                    'field' => 'slug',
                    'terms' => $settings['category'],
                ],
            ];
        }

        $query = new \WP_Query( $args );

        if ( ! $query->have_posts() ) {
            echo '<p>' . \esc_html__( 'No downloads found.', 'sofir' ) . '</p>';
            return;
        }

        $columns = (int) $settings['columns'];
        $gap = isset( $settings['gap']['size'] ) ? (int) $settings['gap']['size'] : 20;

        echo '<div class="sofir-edd-products" style="display: grid; grid-template-columns: repeat(' . esc_attr( $columns ) . ', 1fr); gap: ' . esc_attr( $gap ) . 'px;">';

        while ( $query->have_posts() ) {
            $query->the_post();

            echo '<div class="sofir-edd-product">';

            if ( has_post_thumbnail() ) {
                echo '<a href="' . esc_url( get_permalink() ) . '">';
                the_post_thumbnail( 'medium' );
                echo '</a>';
            }

            echo '<h3><a href="' . esc_url( get_permalink() ) . '">' . get_the_title() . '</a></h3>';

            if ( $settings['show_price'] === 'yes' ) {
                echo '<div class="sofir-edd-price">' . edd_price( get_the_ID(), false ) . '</div>';
            }

            if ( $settings['show_buy_button'] === 'yes' ) {
                echo edd_get_purchase_link( [ 'download_id' => get_the_ID() ] );
            }

            echo '</div>';
        }

        echo '</div>';

        \wp_reset_postdata();
    }
}
