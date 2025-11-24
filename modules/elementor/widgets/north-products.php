<?php
namespace Sofir\Elementor\Widgets;

use Sofir\Elementor\BaseWidget;

class North_Products extends BaseWidget {
    public function get_name() {
        return 'sofir-north-products';
    }

    public function get_title() {
        return \esc_html__( 'North Commerce Products', 'sofir' );
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

        $this->end_controls_section();

        $this->add_style_controls();
    }

    protected function render() {
        if ( ! function_exists( 'north_commerce_init' ) ) {
            echo '<p>' . \esc_html__( 'North Commerce is not installed or activated.', 'sofir' ) . '</p>';
            return;
        }

        $settings = $this->get_settings_for_display();

        $args = [
            'post_type' => 'nc_product',
            'posts_per_page' => (int) $settings['products_per_page'],
            'orderby' => $settings['order_by'],
            'order' => $settings['order'],
        ];

        $query = new \WP_Query( $args );

        if ( ! $query->have_posts() ) {
            echo '<p>' . \esc_html__( 'No products found.', 'sofir' ) . '</p>';
            return;
        }

        $columns = (int) $settings['columns'];
        $gap = isset( $settings['gap']['size'] ) ? (int) $settings['gap']['size'] : 20;

        echo '<div class="sofir-north-products" style="display: grid; grid-template-columns: repeat(' . esc_attr( $columns ) . ', 1fr); gap: ' . esc_attr( $gap ) . 'px;">';

        while ( $query->have_posts() ) {
            $query->the_post();

            echo '<div class="sofir-north-product">';

            if ( has_post_thumbnail() ) {
                echo '<a href="' . esc_url( get_permalink() ) . '">';
                the_post_thumbnail( 'medium' );
                echo '</a>';
            }

            echo '<h3><a href="' . esc_url( get_permalink() ) . '">' . get_the_title() . '</a></h3>';

            if ( $settings['show_price'] === 'yes' ) {
                $price = \get_post_meta( get_the_ID(), '_nc_price', true );
                if ( $price ) {
                    echo '<div class="sofir-north-price">' . \esc_html( $price ) . '</div>';
                }
            }

            if ( $settings['show_buy_button'] === 'yes' ) {
                echo '<a href="' . esc_url( get_permalink() ) . '" class="button">' . \esc_html__( 'View Product', 'sofir' ) . '</a>';
            }

            echo '</div>';
        }

        echo '</div>';

        \wp_reset_postdata();
    }
}
