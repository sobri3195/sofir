<?php
namespace Sofir\Elementor\Widgets;

use Sofir\Elementor\BaseWidget;

class Search_Form extends BaseWidget {
    public function get_name() {
        return 'sofir-search-form';
    }

    public function get_title() {
        return \esc_html__( 'Search Form', 'sofir' );
    }

    public function get_icon() {
        return 'eicon-search';
    }

    protected function register_controls() {
        $this->start_controls_section(
            'section_content',
            [
                'label' => \esc_html__( 'Content', 'sofir' ),
            ]
        );

        $post_types = \get_post_types( [ 'public' => true ], 'objects' );
        $post_type_options = [];
        foreach ( $post_types as $post_type ) {
            $post_type_options[ $post_type->name ] = $post_type->label;
        }

        $this->add_control(
            'post_type',
            [
                'label' => \esc_html__( 'Post Type', 'sofir' ),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'post',
                'options' => $post_type_options,
            ]
        );

        $this->add_control(
            'placeholder',
            [
                'label' => \esc_html__( 'Placeholder', 'sofir' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => \esc_html__( 'Search...', 'sofir' ),
            ]
        );

        $this->add_control(
            'show_filters',
            [
                'label' => \esc_html__( 'Show Filters', 'sofir' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_map',
            [
                'label' => \esc_html__( 'Show Map', 'sofir' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'no',
            ]
        );

        $this->end_controls_section();

        $this->add_style_controls();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();

        $attributes = [
            'postType' => $settings['post_type'],
            'placeholder' => $settings['placeholder'],
            'showFilters' => $settings['show_filters'] === 'yes',
            'showMap' => $settings['show_map'] === 'yes',
        ];

        $this->render_block_content( 'sofir/search-form', $attributes );
    }
}
