<?php
namespace Sofir\Elementor\Widgets;

use Sofir\Elementor\BaseWidget;

class Map extends BaseWidget {
    public function get_name() {
        return 'sofir-map';
    }

    public function get_title() {
        return \esc_html__( 'Map', 'sofir' );
    }

    public function get_icon() {
        return 'eicon-google-maps';
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
                'default' => 'listing',
                'options' => $post_type_options,
            ]
        );

        $this->add_control(
            'zoom',
            [
                'label' => \esc_html__( 'Zoom Level', 'sofir' ),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'default' => [
                    'size' => 12,
                ],
                'range' => [
                    'px' => [
                        'min' => 1,
                        'max' => 20,
                    ],
                ],
            ]
        );

        $this->add_control(
            'height',
            [
                'label' => \esc_html__( 'Height', 'sofir' ),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'range' => [
                    'px' => [
                        'min' => 200,
                        'max' => 1000,
                    ],
                ],
                'default' => [
                    'size' => 400,
                ],
            ]
        );

        $this->add_control(
            'center_lat',
            [
                'label' => \esc_html__( 'Center Latitude', 'sofir' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => '-6.2088',
            ]
        );

        $this->add_control(
            'center_lng',
            [
                'label' => \esc_html__( 'Center Longitude', 'sofir' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => '106.8456',
            ]
        );

        $this->end_controls_section();

        $this->add_style_controls();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();

        $attributes = [
            'postType' => $settings['post_type'],
            'zoom' => (int) $settings['zoom']['size'],
            'height' => (int) $settings['height']['size'],
            'centerLat' => $settings['center_lat'],
            'centerLng' => $settings['center_lng'],
        ];

        $this->render_block_content( 'sofir/map', $attributes );
    }
}
