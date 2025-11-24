<?php
namespace Sofir\Elementor\Widgets;

use Sofir\Elementor\BaseWidget;

class Term_Feed extends BaseWidget {
    public function get_name() {
        return 'sofir-term-feed';
    }

    public function get_title() {
        return \esc_html__( 'Term Feed', 'sofir' );
    }

    public function get_icon() {
        return 'eicon-taxonomy-filter';
    }

    protected function register_controls() {
        $this->start_controls_section(
            'section_content',
            [
                'label' => \esc_html__( 'Content', 'sofir' ),
            ]
        );

        $taxonomies = \get_taxonomies( [ 'public' => true ], 'objects' );
        $taxonomy_options = [];
        foreach ( $taxonomies as $taxonomy ) {
            $taxonomy_options[ $taxonomy->name ] = $taxonomy->label;
        }

        $this->add_control(
            'taxonomy',
            [
                'label' => \esc_html__( 'Taxonomy', 'sofir' ),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'category',
                'options' => $taxonomy_options,
            ]
        );

        $this->add_control(
            'number_of_terms',
            [
                'label' => \esc_html__( 'Number of Terms', 'sofir' ),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 10,
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

        $this->add_control(
            'show_count',
            [
                'label' => \esc_html__( 'Show Count', 'sofir' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_layout_controls();

        $this->end_controls_section();

        $this->add_style_controls();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();

        $attributes = [
            'taxonomy' => $settings['taxonomy'],
            'numberOfTerms' => (int) $settings['number_of_terms'],
            'layout' => $settings['layout'],
            'showCount' => $settings['show_count'] === 'yes',
        ];

        $this->render_block_content( 'sofir/term-feed', $attributes );
    }
}
