<?php
namespace Sofir\Elementor\Widgets;

use Sofir\Elementor\BaseWidget;

class Post_Feed extends BaseWidget {
    public function get_name() {
        return 'sofir-post-feed';
    }

    public function get_title() {
        return \esc_html__( 'Post Feed', 'sofir' );
    }

    public function get_icon() {
        return 'eicon-posts-grid';
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
            'posts_per_page',
            [
                'label' => \esc_html__( 'Posts Per Page', 'sofir' ),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 6,
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
                    'masonry' => \esc_html__( 'Masonry', 'sofir' ),
                ],
            ]
        );

        $this->add_layout_controls();

        $this->add_control(
            'show_excerpt',
            [
                'label' => \esc_html__( 'Show Excerpt', 'sofir' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_meta',
            [
                'label' => \esc_html__( 'Show Meta', 'sofir' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_thumbnail',
            [
                'label' => \esc_html__( 'Show Thumbnail', 'sofir' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->end_controls_section();

        $this->add_style_controls();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();

        $attributes = [
            'postType' => $settings['post_type'],
            'postsPerPage' => (int) $settings['posts_per_page'],
            'layout' => $settings['layout'],
            'columns' => (int) $settings['columns'],
            'showExcerpt' => $settings['show_excerpt'] === 'yes',
            'showMeta' => $settings['show_meta'] === 'yes',
            'showThumbnail' => $settings['show_thumbnail'] === 'yes',
        ];

        $this->render_block_content( 'sofir/post-feed', $attributes );
    }
}
