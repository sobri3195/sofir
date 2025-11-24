<?php
namespace Sofir\Elementor\Widgets;

use Sofir\Elementor\BaseWidget;

class Dynamic_Data extends BaseWidget {
    public function get_name() {
        return 'sofir-dynamic-data';
    }

    public function get_title() {
        return \esc_html__( 'Dynamic Data', 'sofir' );
    }

    public function get_icon() {
        return 'eicon-database';
    }

    protected function register_controls() {
        $this->start_controls_section(
            'section_content',
            [
                'label' => \esc_html__( 'Content', 'sofir' ),
            ]
        );

        $this->add_control(
            'data_source',
            [
                'label' => \esc_html__( 'Data Source', 'sofir' ),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'post_title',
                'options' => [
                    'post_title' => \esc_html__( 'Post Title', 'sofir' ),
                    'post_excerpt' => \esc_html__( 'Post Excerpt', 'sofir' ),
                    'post_date' => \esc_html__( 'Post Date', 'sofir' ),
                    'post_author' => \esc_html__( 'Post Author', 'sofir' ),
                    'custom_field' => \esc_html__( 'Custom Field', 'sofir' ),
                    'taxonomy_terms' => \esc_html__( 'Taxonomy Terms', 'sofir' ),
                ],
            ]
        );

        $this->add_control(
            'custom_field_key',
            [
                'label' => \esc_html__( 'Custom Field Key', 'sofir' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'condition' => [
                    'data_source' => 'custom_field',
                ],
            ]
        );

        $this->add_control(
            'format',
            [
                'label' => \esc_html__( 'Format', 'sofir' ),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'plain',
                'options' => [
                    'plain' => \esc_html__( 'Plain Text', 'sofir' ),
                    'html' => \esc_html__( 'HTML', 'sofir' ),
                    'link' => \esc_html__( 'Link', 'sofir' ),
                    'image' => \esc_html__( 'Image', 'sofir' ),
                ],
            ]
        );

        $this->end_controls_section();

        $this->add_style_controls();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();

        $attributes = [
            'dataSource' => $settings['data_source'],
            'customFieldKey' => $settings['custom_field_key'] ?? '',
            'format' => $settings['format'],
        ];

        $this->render_block_content( 'sofir/dynamic-data', $attributes );
    }
}
