<?php
namespace Sofir\Elementor\Widgets;

use Sofir\Elementor\BaseWidget;

class Contact_Info extends BaseWidget {
    public function get_name() {
        return 'sofir-contact-info';
    }

    public function get_title() {
        return \esc_html__( 'Contact Info', 'sofir' );
    }

    public function get_icon() {
        return 'eicon-info-box';
    }

    protected function register_controls() {
        $this->start_controls_section(
            'section_content',
            [
                'label' => \esc_html__( 'Content', 'sofir' ),
            ]
        );

        $this->add_control(
            'post_id',
            [
                'label' => \esc_html__( 'Post ID', 'sofir' ),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 0,
                'description' => \esc_html__( 'Leave 0 for current post', 'sofir' ),
            ]
        );

        $this->add_control(
            'show_phone',
            [
                'label' => \esc_html__( 'Show Phone', 'sofir' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_email',
            [
                'label' => \esc_html__( 'Show Email', 'sofir' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_website',
            [
                'label' => \esc_html__( 'Show Website', 'sofir' ),
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
            'postId' => (int) $settings['post_id'],
            'showPhone' => $settings['show_phone'] === 'yes',
            'showEmail' => $settings['show_email'] === 'yes',
            'showWebsite' => $settings['show_website'] === 'yes',
        ];

        $this->render_block_content( 'sofir/contact-info', $attributes );
    }
}
