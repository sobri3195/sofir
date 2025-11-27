<?php
namespace Sofir\Elementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

class Voxel_Listings extends Widget_Base {

    public function get_name(): string {
        return 'sofir-voxel-listings';
    }

    public function get_title(): string {
        return \esc_html__( 'Voxel Listings', 'sofir' );
    }

    public function get_icon(): string {
        return 'eicon-post-list';
    }

    public function get_categories(): array {
        return [ 'sofir' ];
    }

    public function get_keywords(): array {
        return [ 'voxel', 'listings', 'directory', 'cpt', 'sofir' ];
    }

    protected function register_controls(): void {
        $this->start_controls_section(
            'section_content',
            [
                'label' => \esc_html__( 'Content', 'sofir' ),
            ]
        );

        $post_types = $this->get_voxel_post_types();
        $this->add_control(
            'post_type',
            [
                'label' => \esc_html__( 'Post Type', 'sofir' ),
                'type' => Controls_Manager::SELECT,
                'options' => $post_types,
                'default' => 'listing',
            ]
        );

        $this->add_control(
            'posts_per_page',
            [
                'label' => \esc_html__( 'Posts Per Page', 'sofir' ),
                'type' => Controls_Manager::NUMBER,
                'default' => 12,
                'min' => 1,
                'max' => 100,
            ]
        );

        $this->add_control(
            'orderby',
            [
                'label' => \esc_html__( 'Order By', 'sofir' ),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'date' => \esc_html__( 'Date', 'sofir' ),
                    'title' => \esc_html__( 'Title', 'sofir' ),
                    'modified' => \esc_html__( 'Modified', 'sofir' ),
                    'rand' => \esc_html__( 'Random', 'sofir' ),
                    'menu_order' => \esc_html__( 'Menu Order', 'sofir' ),
                ],
                'default' => 'date',
            ]
        );

        $this->add_control(
            'order',
            [
                'label' => \esc_html__( 'Order', 'sofir' ),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'DESC' => \esc_html__( 'Descending', 'sofir' ),
                    'ASC' => \esc_html__( 'Ascending', 'sofir' ),
                ],
                'default' => 'DESC',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_layout',
            [
                'label' => \esc_html__( 'Layout', 'sofir' ),
            ]
        );

        $this->add_control(
            'layout',
            [
                'label' => \esc_html__( 'Layout', 'sofir' ),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'grid' => \esc_html__( 'Grid', 'sofir' ),
                    'list' => \esc_html__( 'List', 'sofir' ),
                    'masonry' => \esc_html__( 'Masonry', 'sofir' ),
                    'map' => \esc_html__( 'Map View', 'sofir' ),
                ],
                'default' => 'grid',
            ]
        );

        $this->add_responsive_control(
            'columns',
            [
                'label' => \esc_html__( 'Columns', 'sofir' ),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    '1' => '1',
                    '2' => '2',
                    '3' => '3',
                    '4' => '4',
                    '6' => '6',
                ],
                'default' => '3',
                'tablet_default' => '2',
                'mobile_default' => '1',
                'condition' => [
                    'layout' => [ 'grid', 'masonry' ],
                ],
            ]
        );

        $this->add_control(
            'show_filters',
            [
                'label' => \esc_html__( 'Show Filters', 'sofir' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => \esc_html__( 'Yes', 'sofir' ),
                'label_off' => \esc_html__( 'No', 'sofir' ),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_search',
            [
                'label' => \esc_html__( 'Show Search', 'sofir' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => \esc_html__( 'Yes', 'sofir' ),
                'label_off' => \esc_html__( 'No', 'sofir' ),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_sorting',
            [
                'label' => \esc_html__( 'Show Sorting', 'sofir' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => \esc_html__( 'Yes', 'sofir' ),
                'label_off' => \esc_html__( 'No', 'sofir' ),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_voxel',
            [
                'label' => \esc_html__( 'Voxel Settings', 'sofir' ),
            ]
        );

        $this->add_control(
            'use_voxel_templates',
            [
                'label' => \esc_html__( 'Use Voxel Templates', 'sofir' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => \esc_html__( 'Yes', 'sofir' ),
                'label_off' => \esc_html__( 'No', 'sofir' ),
                'return_value' => 'yes',
                'default' => 'yes',
                'description' => \esc_html__( 'Use Voxel theme templates for displaying cards', 'sofir' ),
            ]
        );

        $this->add_control(
            'voxel_card_style',
            [
                'label' => \esc_html__( 'Card Style', 'sofir' ),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'default' => \esc_html__( 'Default', 'sofir' ),
                    'minimal' => \esc_html__( 'Minimal', 'sofir' ),
                    'detailed' => \esc_html__( 'Detailed', 'sofir' ),
                    'overlay' => \esc_html__( 'Overlay', 'sofir' ),
                ],
                'default' => 'default',
                'condition' => [
                    'use_voxel_templates' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'enable_ajax',
            [
                'label' => \esc_html__( 'Enable AJAX Filtering', 'sofir' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => \esc_html__( 'Yes', 'sofir' ),
                'label_off' => \esc_html__( 'No', 'sofir' ),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->end_controls_section();

        $this->add_style_controls();
    }

    private function add_style_controls(): void {
        $this->start_controls_section(
            'section_style_cards',
            [
                'label' => \esc_html__( 'Cards', 'sofir' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'card_gap',
            [
                'label' => \esc_html__( 'Gap', 'sofir' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'size' => 20,
                ],
                'selectors' => [
                    '{{WRAPPER}} .sofir-voxel-listings' => 'gap: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'card_background',
            [
                'label' => \esc_html__( 'Background', 'sofir' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .sofir-listing-card' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'card_border',
                'selector' => '{{WRAPPER}} .sofir-listing-card',
            ]
        );

        $this->add_control(
            'card_border_radius',
            [
                'label' => \esc_html__( 'Border Radius', 'sofir' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .sofir-listing-card' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'card_box_shadow',
                'selector' => '{{WRAPPER}} .sofir-listing-card',
            ]
        );

        $this->end_controls_section();
    }

    protected function render(): void {
        $settings = $this->get_settings_for_display();
        $post_type = $settings['post_type'] ?? 'listing';
        
        if ( ! post_type_exists( $post_type ) ) {
            if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
                echo '<div class="elementor-alert elementor-alert-warning">';
                echo \esc_html__( 'Selected post type does not exist.', 'sofir' );
                echo '</div>';
            }
            return;
        }

        $query_args = [
            'post_type' => $post_type,
            'posts_per_page' => $settings['posts_per_page'] ?? 12,
            'orderby' => $settings['orderby'] ?? 'date',
            'order' => $settings['order'] ?? 'DESC',
            'post_status' => 'publish',
        ];

        $query = new \WP_Query( $query_args );

        $wrapper_class = 'sofir-voxel-listings';
        $wrapper_class .= ' sofir-layout-' . ( $settings['layout'] ?? 'grid' );
        if ( 'grid' === $settings['layout'] || 'masonry' === $settings['layout'] ) {
            $wrapper_class .= ' sofir-columns-' . ( $settings['columns'] ?? 3 );
        }

        $ajax_data = [
            'post_type' => $post_type,
            'settings' => $settings,
        ];

        echo '<div class="' . \esc_attr( $wrapper_class ) . '" data-ajax="' . \esc_attr( wp_json_encode( $ajax_data ) ) . '">';

        if ( 'yes' === $settings['show_filters'] || 'yes' === $settings['show_search'] || 'yes' === $settings['show_sorting'] ) {
            $this->render_toolbar( $settings, $post_type );
        }

        echo '<div class="sofir-listings-grid">';

        if ( $query->have_posts() ) {
            while ( $query->have_posts() ) {
                $query->the_post();
                
                if ( 'yes' === $settings['use_voxel_templates'] && $this->is_voxel_active() ) {
                    $this->render_voxel_card( $settings );
                } else {
                    $this->render_default_card( $settings );
                }
            }
            wp_reset_postdata();
        } else {
            echo '<div class="sofir-no-results">';
            echo '<p>' . \esc_html__( 'No listings found.', 'sofir' ) . '</p>';
            echo '</div>';
        }

        echo '</div>';

        if ( $query->max_num_pages > 1 ) {
            $this->render_pagination( $query );
        }

        echo '</div>';
    }

    private function render_toolbar( array $settings, string $post_type ): void {
        echo '<div class="sofir-listings-toolbar">';

        if ( 'yes' === $settings['show_search'] ) {
            echo '<div class="sofir-search-box">';
            echo '<input type="text" class="sofir-search-input" placeholder="' . \esc_attr__( 'Search...', 'sofir' ) . '" />';
            echo '</div>';
        }

        if ( 'yes' === $settings['show_filters'] ) {
            $this->render_filters( $post_type );
        }

        if ( 'yes' === $settings['show_sorting'] ) {
            echo '<div class="sofir-sorting">';
            echo '<select class="sofir-sort-select">';
            echo '<option value="date-desc">' . \esc_html__( 'Newest First', 'sofir' ) . '</option>';
            echo '<option value="date-asc">' . \esc_html__( 'Oldest First', 'sofir' ) . '</option>';
            echo '<option value="title-asc">' . \esc_html__( 'Title A-Z', 'sofir' ) . '</option>';
            echo '<option value="title-desc">' . \esc_html__( 'Title Z-A', 'sofir' ) . '</option>';
            echo '</select>';
            echo '</div>';
        }

        echo '</div>';
    }

    private function render_filters( string $post_type ): void {
        $taxonomies = get_object_taxonomies( $post_type, 'objects' );
        
        if ( empty( $taxonomies ) ) {
            return;
        }

        echo '<div class="sofir-filters">';
        
        foreach ( $taxonomies as $taxonomy ) {
            $terms = get_terms( [
                'taxonomy' => $taxonomy->name,
                'hide_empty' => true,
            ] );

            if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
                echo '<div class="sofir-filter-group">';
                echo '<label>' . \esc_html( $taxonomy->label ) . '</label>';
                echo '<select class="sofir-filter-select" data-taxonomy="' . \esc_attr( $taxonomy->name ) . '">';
                echo '<option value="">' . \esc_html__( 'All', 'sofir' ) . '</option>';
                foreach ( $terms as $term ) {
                    echo '<option value="' . \esc_attr( $term->slug ) . '">' . \esc_html( $term->name ) . '</option>';
                }
                echo '</select>';
                echo '</div>';
            }
        }
        
        echo '</div>';
    }

    private function render_voxel_card( array $settings ): void {
        if ( function_exists( '\Voxel\render_template' ) ) {
            \Voxel\render_template( 'card', [
                'post' => get_post(),
                'style' => $settings['voxel_card_style'] ?? 'default',
            ] );
        } else {
            $this->render_default_card( $settings );
        }
    }

    private function render_default_card( array $settings ): void {
        echo '<article class="sofir-listing-card">';
        
        if ( has_post_thumbnail() ) {
            echo '<div class="sofir-card-image">';
            echo '<a href="' . \esc_url( get_permalink() ) . '">';
            the_post_thumbnail( 'medium' );
            echo '</a>';
            echo '</div>';
        }
        
        echo '<div class="sofir-card-content">';
        echo '<h3 class="sofir-card-title">';
        echo '<a href="' . \esc_url( get_permalink() ) . '">' . \esc_html( get_the_title() ) . '</a>';
        echo '</h3>';
        
        if ( has_excerpt() ) {
            echo '<div class="sofir-card-excerpt">';
            the_excerpt();
            echo '</div>';
        }
        
        echo '<div class="sofir-card-meta">';
        $this->render_card_meta();
        echo '</div>';
        
        echo '</div>';
        echo '</article>';
    }

    private function render_card_meta(): void {
        $rating = get_post_meta( get_the_ID(), 'sofir_rating', true );
        if ( $rating ) {
            echo '<span class="sofir-rating">⭐ ' . \esc_html( $rating ) . '</span>';
        }

        $location = get_post_meta( get_the_ID(), 'sofir_location', true );
        if ( $location ) {
            echo '<span class="sofir-location">📍 ' . \esc_html( $location ) . '</span>';
        }

        $price = get_post_meta( get_the_ID(), 'sofir_price', true );
        if ( $price ) {
            echo '<span class="sofir-price">💰 ' . \esc_html( $price ) . '</span>';
        }
    }

    private function render_pagination( \WP_Query $query ): void {
        echo '<div class="sofir-pagination">';
        echo paginate_links( [
            'total' => $query->max_num_pages,
            'current' => max( 1, get_query_var( 'paged' ) ),
            'prev_text' => \esc_html__( '← Previous', 'sofir' ),
            'next_text' => \esc_html__( 'Next →', 'sofir' ),
        ] );
        echo '</div>';
    }

    private function get_voxel_post_types(): array {
        $post_types = [];
        
        $cpt_manager = \Sofir\Cpt\Manager::instance();
        $registered_cpts = $cpt_manager->get_post_types();
        
        foreach ( $registered_cpts as $slug => $definition ) {
            $label = $definition['args']['labels']['name'] ?? ucfirst( $slug );
            $post_types[ $slug ] = $label;
        }
        
        if ( empty( $post_types ) ) {
            $post_types['listing'] = \esc_html__( 'Listing', 'sofir' );
        }
        
        return $post_types;
    }

    private function is_voxel_active(): bool {
        return defined( 'VOXEL_VERSION' ) || class_exists( '\Voxel\Post_Type' );
    }
}
