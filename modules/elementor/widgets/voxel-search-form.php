<?php
namespace Sofir\Elementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

class Voxel_Search_Form extends Widget_Base {

    public function get_name(): string {
        return 'sofir-voxel-search-form';
    }

    public function get_title(): string {
        return \esc_html__( 'Voxel Search Form', 'sofir' );
    }

    public function get_icon(): string {
        return 'eicon-search';
    }

    public function get_categories(): array {
        return [ 'sofir' ];
    }

    public function get_keywords(): array {
        return [ 'voxel', 'search', 'filter', 'form', 'sofir' ];
    }

    protected function register_controls(): void {
        $this->start_controls_section(
            'section_content',
            [
                'label' => \esc_html__( 'Content', 'sofir' ),
            ]
        );

        $post_types = $this->get_post_types();
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
            'redirect_to',
            [
                'label' => \esc_html__( 'Redirect To', 'sofir' ),
                'type' => Controls_Manager::TEXT,
                'placeholder' => '/search-results/',
                'description' => \esc_html__( 'Leave empty to search on the same page', 'sofir' ),
            ]
        );

        $this->add_control(
            'show_keyword',
            [
                'label' => \esc_html__( 'Show Keyword Search', 'sofir' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => \esc_html__( 'Yes', 'sofir' ),
                'label_off' => \esc_html__( 'No', 'sofir' ),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_location',
            [
                'label' => \esc_html__( 'Show Location Search', 'sofir' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => \esc_html__( 'Yes', 'sofir' ),
                'label_off' => \esc_html__( 'No', 'sofir' ),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_categories',
            [
                'label' => \esc_html__( 'Show Categories', 'sofir' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => \esc_html__( 'Yes', 'sofir' ),
                'label_off' => \esc_html__( 'No', 'sofir' ),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_fields',
            [
                'label' => \esc_html__( 'Filter Fields', 'sofir' ),
            ]
        );

        $this->add_control(
            'show_price_range',
            [
                'label' => \esc_html__( 'Show Price Range', 'sofir' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => \esc_html__( 'Yes', 'sofir' ),
                'label_off' => \esc_html__( 'No', 'sofir' ),
                'return_value' => 'yes',
                'default' => 'no',
            ]
        );

        $this->add_control(
            'show_rating',
            [
                'label' => \esc_html__( 'Show Rating Filter', 'sofir' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => \esc_html__( 'Yes', 'sofir' ),
                'label_off' => \esc_html__( 'No', 'sofir' ),
                'return_value' => 'yes',
                'default' => 'no',
            ]
        );

        $this->add_control(
            'show_date_range',
            [
                'label' => \esc_html__( 'Show Date Range', 'sofir' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => \esc_html__( 'Yes', 'sofir' ),
                'label_off' => \esc_html__( 'No', 'sofir' ),
                'return_value' => 'yes',
                'default' => 'no',
            ]
        );

        $this->add_control(
            'show_open_now',
            [
                'label' => \esc_html__( 'Show Open Now Filter', 'sofir' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => \esc_html__( 'Yes', 'sofir' ),
                'label_off' => \esc_html__( 'No', 'sofir' ),
                'return_value' => 'yes',
                'default' => 'no',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style',
            [
                'label' => \esc_html__( 'Style', 'sofir' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'form_layout',
            [
                'label' => \esc_html__( 'Layout', 'sofir' ),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'horizontal' => \esc_html__( 'Horizontal', 'sofir' ),
                    'vertical' => \esc_html__( 'Vertical', 'sofir' ),
                    'inline' => \esc_html__( 'Inline', 'sofir' ),
                ],
                'default' => 'horizontal',
            ]
        );

        $this->add_control(
            'button_text',
            [
                'label' => \esc_html__( 'Button Text', 'sofir' ),
                'type' => Controls_Manager::TEXT,
                'default' => \esc_html__( 'Search', 'sofir' ),
            ]
        );

        $this->add_control(
            'button_icon',
            [
                'label' => \esc_html__( 'Button Icon', 'sofir' ),
                'type' => Controls_Manager::ICONS,
                'default' => [
                    'value' => 'fas fa-search',
                    'library' => 'solid',
                ],
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

        $form_class = 'sofir-voxel-search-form';
        $form_class .= ' sofir-layout-' . ( $settings['form_layout'] ?? 'horizontal' );

        $redirect = $settings['redirect_to'] ?? '';
        $action = ! empty( $redirect ) ? $redirect : get_post_type_archive_link( $post_type );

        echo '<form class="' . \esc_attr( $form_class ) . '" method="get" action="' . \esc_url( $action ) . '">';
        
        echo '<input type="hidden" name="post_type" value="' . \esc_attr( $post_type ) . '" />';

        echo '<div class="sofir-search-fields">';

        if ( 'yes' === $settings['show_keyword'] ) {
            $this->render_keyword_field();
        }

        if ( 'yes' === $settings['show_location'] ) {
            $this->render_location_field();
        }

        if ( 'yes' === $settings['show_categories'] ) {
            $this->render_categories_field( $post_type );
        }

        if ( 'yes' === $settings['show_price_range'] ) {
            $this->render_price_range_field();
        }

        if ( 'yes' === $settings['show_rating'] ) {
            $this->render_rating_field();
        }

        if ( 'yes' === $settings['show_date_range'] ) {
            $this->render_date_range_field();
        }

        if ( 'yes' === $settings['show_open_now'] ) {
            $this->render_open_now_field();
        }

        echo '</div>';

        echo '<div class="sofir-search-button">';
        echo '<button type="submit" class="sofir-btn sofir-btn-primary">';
        
        if ( ! empty( $settings['button_icon']['value'] ) ) {
            \Elementor\Icons_Manager::render_icon( $settings['button_icon'], [ 'aria-hidden' => 'true' ] );
        }
        
        echo ' ' . \esc_html( $settings['button_text'] ?? __( 'Search', 'sofir' ) );
        echo '</button>';
        echo '</div>';

        echo '</form>';
    }

    private function render_keyword_field(): void {
        $value = isset( $_GET['s'] ) ? \sanitize_text_field( $_GET['s'] ) : '';
        
        echo '<div class="sofir-field sofir-field-keyword">';
        echo '<label for="sofir-search-keyword">' . \esc_html__( 'Keyword', 'sofir' ) . '</label>';
        echo '<input type="text" id="sofir-search-keyword" name="s" value="' . \esc_attr( $value ) . '" placeholder="' . \esc_attr__( 'What are you looking for?', 'sofir' ) . '" />';
        echo '</div>';
    }

    private function render_location_field(): void {
        $value = isset( $_GET['sofir_location'] ) ? \sanitize_text_field( $_GET['sofir_location'] ) : '';
        
        echo '<div class="sofir-field sofir-field-location">';
        echo '<label for="sofir-search-location">' . \esc_html__( 'Location', 'sofir' ) . '</label>';
        echo '<input type="text" id="sofir-search-location" name="sofir_location" value="' . \esc_attr( $value ) . '" placeholder="' . \esc_attr__( 'Enter location...', 'sofir' ) . '" class="sofir-location-autocomplete" />';
        echo '</div>';
    }

    private function render_categories_field( string $post_type ): void {
        $taxonomies = get_object_taxonomies( $post_type, 'objects' );
        
        if ( empty( $taxonomies ) ) {
            return;
        }

        $taxonomy = reset( $taxonomies );
        $selected = isset( $_GET[ $taxonomy->query_var ] ) ? \sanitize_text_field( $_GET[ $taxonomy->query_var ] ) : '';

        echo '<div class="sofir-field sofir-field-category">';
        echo '<label for="sofir-search-category">' . \esc_html( $taxonomy->label ) . '</label>';
        echo '<select id="sofir-search-category" name="' . \esc_attr( $taxonomy->query_var ) . '">';
        echo '<option value="">' . \esc_html__( 'All Categories', 'sofir' ) . '</option>';

        $terms = get_terms( [
            'taxonomy' => $taxonomy->name,
            'hide_empty' => true,
        ] );

        if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
            foreach ( $terms as $term ) {
                $selected_attr = selected( $selected, $term->slug, false );
                echo '<option value="' . \esc_attr( $term->slug ) . '"' . $selected_attr . '>' . \esc_html( $term->name ) . '</option>';
            }
        }

        echo '</select>';
        echo '</div>';
    }

    private function render_price_range_field(): void {
        $min = isset( $_GET['sofir_price_min'] ) ? \sanitize_text_field( $_GET['sofir_price_min'] ) : '';
        $max = isset( $_GET['sofir_price_max'] ) ? \sanitize_text_field( $_GET['sofir_price_max'] ) : '';
        
        echo '<div class="sofir-field sofir-field-price-range">';
        echo '<label>' . \esc_html__( 'Price Range', 'sofir' ) . '</label>';
        echo '<div class="sofir-range-inputs">';
        echo '<input type="number" name="sofir_price_min" value="' . \esc_attr( $min ) . '" placeholder="' . \esc_attr__( 'Min', 'sofir' ) . '" min="0" />';
        echo '<span class="sofir-range-separator">-</span>';
        echo '<input type="number" name="sofir_price_max" value="' . \esc_attr( $max ) . '" placeholder="' . \esc_attr__( 'Max', 'sofir' ) . '" min="0" />';
        echo '</div>';
        echo '</div>';
    }

    private function render_rating_field(): void {
        $selected = isset( $_GET['sofir_rating'] ) ? \sanitize_text_field( $_GET['sofir_rating'] ) : '';
        
        echo '<div class="sofir-field sofir-field-rating">';
        echo '<label for="sofir-search-rating">' . \esc_html__( 'Minimum Rating', 'sofir' ) . '</label>';
        echo '<select id="sofir-search-rating" name="sofir_rating">';
        echo '<option value="">' . \esc_html__( 'Any Rating', 'sofir' ) . '</option>';
        
        for ( $i = 5; $i >= 1; $i-- ) {
            $selected_attr = selected( $selected, $i, false );
            echo '<option value="' . \esc_attr( $i ) . '"' . $selected_attr . '>' . str_repeat( '⭐', $i ) . ' ' . \esc_html__( '& Up', 'sofir' ) . '</option>';
        }
        
        echo '</select>';
        echo '</div>';
    }

    private function render_date_range_field(): void {
        $start = isset( $_GET['sofir_date_start'] ) ? \sanitize_text_field( $_GET['sofir_date_start'] ) : '';
        $end = isset( $_GET['sofir_date_end'] ) ? \sanitize_text_field( $_GET['sofir_date_end'] ) : '';
        
        echo '<div class="sofir-field sofir-field-date-range">';
        echo '<label>' . \esc_html__( 'Date Range', 'sofir' ) . '</label>';
        echo '<div class="sofir-range-inputs">';
        echo '<input type="date" name="sofir_date_start" value="' . \esc_attr( $start ) . '" />';
        echo '<span class="sofir-range-separator">-</span>';
        echo '<input type="date" name="sofir_date_end" value="' . \esc_attr( $end ) . '" />';
        echo '</div>';
        echo '</div>';
    }

    private function render_open_now_field(): void {
        $checked = isset( $_GET['sofir_open_now'] ) ? 'checked' : '';
        
        echo '<div class="sofir-field sofir-field-checkbox">';
        echo '<label>';
        echo '<input type="checkbox" name="sofir_open_now" value="1" ' . $checked . ' /> ';
        echo \esc_html__( 'Open Now', 'sofir' );
        echo '</label>';
        echo '</div>';
    }

    private function get_post_types(): array {
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
}
