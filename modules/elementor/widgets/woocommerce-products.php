<?php
namespace Sofir\Elementor\Widgets;

use Sofir\Elementor\BaseWidget;

class Woocommerce_Products extends BaseWidget {
    public function get_name() {
        return 'sofir-woocommerce-products';
    }

    public function get_title() {
        return \esc_html__( 'WooCommerce Products', 'sofir' );
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
                    'popularity' => \esc_html__( 'Popularity', 'sofir' ),
                    'rating' => \esc_html__( 'Rating', 'sofir' ),
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
            'show_sale_badge',
            [
                'label' => \esc_html__( 'Show Sale Badge', 'sofir' ),
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
            'show_add_to_cart',
            [
                'label' => \esc_html__( 'Show Add to Cart', 'sofir' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $categories = \get_terms( [ 'taxonomy' => 'product_cat', 'hide_empty' => false ] );
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
        if ( ! class_exists( 'WooCommerce' ) ) {
            echo '<p>' . \esc_html__( 'WooCommerce is not installed or activated.', 'sofir' ) . '</p>';
            return;
        }

        $settings = $this->get_settings_for_display();

        $args = [
            'post_type' => 'product',
            'posts_per_page' => (int) $settings['products_per_page'],
            'orderby' => $settings['order_by'],
            'order' => $settings['order'],
        ];

        if ( ! empty( $settings['category'] ) ) {
            $args['tax_query'] = [
                [
                    'taxonomy' => 'product_cat',
                    'field' => 'slug',
                    'terms' => $settings['category'],
                ],
            ];
        }

        $query = new \WP_Query( $args );

        if ( ! $query->have_posts() ) {
            echo '<p>' . \esc_html__( 'No products found.', 'sofir' ) . '</p>';
            return;
        }

        $columns = (int) $settings['columns'];
        $gap = isset( $settings['gap']['size'] ) ? (int) $settings['gap']['size'] : 20;

        echo '<div class="sofir-wc-products" style="display: grid; grid-template-columns: repeat(' . esc_attr( $columns ) . ', 1fr); gap: ' . esc_attr( $gap ) . 'px;">';

        while ( $query->have_posts() ) {
            $query->the_post();
            global $product;

            echo '<div class="sofir-wc-product">';
            
            if ( $product->is_on_sale() && $settings['show_sale_badge'] === 'yes' ) {
                echo '<span class="sofir-sale-badge">' . \esc_html__( 'Sale!', 'sofir' ) . '</span>';
            }

            if ( has_post_thumbnail() ) {
                echo '<a href="' . esc_url( get_permalink() ) . '">';
                the_post_thumbnail( 'medium' );
                echo '</a>';
            }

            echo '<h3><a href="' . esc_url( get_permalink() ) . '">' . get_the_title() . '</a></h3>';

            if ( $settings['show_rating'] === 'yes' && $product->get_average_rating() > 0 ) {
                echo wc_get_rating_html( $product->get_average_rating() );
            }

            echo '<div class="sofir-wc-price">' . $product->get_price_html() . '</div>';

            if ( $settings['show_add_to_cart'] === 'yes' ) {
                woocommerce_template_loop_add_to_cart();
            }

            echo '</div>';
        }

        echo '</div>';

        \wp_reset_postdata();
    }
}
