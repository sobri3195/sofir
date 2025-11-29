<?php
namespace Sofir\Elementor\Widgets;

use Sofir\Elementor\BaseWidget;

class Form extends BaseWidget {
    public function get_categories() {
        return [ 'sofir' ];
    }

    public function get_name() {
        return 'sofir-form';
    }

    public function get_title() {
        return \esc_html__( 'Form', 'sofir' );
    }

    public function get_icon() {
        return 'eicon-form-horizontal';
    }

    protected function register_controls() {
        $this->start_controls_section(
            'section_content',
            [
                'label' => \esc_html__( 'Content', 'sofir' ),
            ]
        );

        $forms = \get_posts( [
            'post_type' => 'sofir_form',
            'posts_per_page' => -1,
            'post_status' => 'publish',
        ] );

        $form_options = [ '' => \esc_html__( 'Select a form', 'sofir' ) ];
        foreach ( $forms as $form ) {
            $form_options[ $form->ID ] = $form->post_title;
        }

        $this->add_control(
            'form_id',
            [
                'label' => \esc_html__( 'Select Form', 'sofir' ),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => $form_options,
                'default' => '',
            ]
        );

        $this->add_control(
            'show_title',
            [
                'label' => \esc_html__( 'Show Form Title', 'sofir' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_description',
            [
                'label' => \esc_html__( 'Show Form Description', 'sofir' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'ajax_submit',
            [
                'label' => \esc_html__( 'AJAX Submit', 'sofir' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
                'description' => \esc_html__( 'Submit form without page reload', 'sofir' ),
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_form',
            [
                'label' => \esc_html__( 'Form Style', 'sofir' ),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'form_background',
            [
                'label' => \esc_html__( 'Background Color', 'sofir' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .sofir-form-widget' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'form_padding',
            [
                'label' => \esc_html__( 'Padding', 'sofir' ),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .sofir-form-widget' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'form_border',
                'selector' => '{{WRAPPER}} .sofir-form-widget',
            ]
        );

        $this->add_control(
            'form_border_radius',
            [
                'label' => \esc_html__( 'Border Radius', 'sofir' ),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .sofir-form-widget' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_field',
            [
                'label' => \esc_html__( 'Field Style', 'sofir' ),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'field_text_color',
            [
                'label' => \esc_html__( 'Text Color', 'sofir' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .sofir-form-widget input[type="text"]' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .sofir-form-widget input[type="email"]' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .sofir-form-widget input[type="tel"]' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .sofir-form-widget input[type="number"]' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .sofir-form-widget textarea' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .sofir-form-widget select' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'field_background',
            [
                'label' => \esc_html__( 'Background Color', 'sofir' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .sofir-form-widget input[type="text"]' => 'background-color: {{VALUE}};',
                    '{{WRAPPER}} .sofir-form-widget input[type="email"]' => 'background-color: {{VALUE}};',
                    '{{WRAPPER}} .sofir-form-widget input[type="tel"]' => 'background-color: {{VALUE}};',
                    '{{WRAPPER}} .sofir-form-widget input[type="number"]' => 'background-color: {{VALUE}};',
                    '{{WRAPPER}} .sofir-form-widget textarea' => 'background-color: {{VALUE}};',
                    '{{WRAPPER}} .sofir-form-widget select' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'field_border',
                'selector' => '{{WRAPPER}} .sofir-form-widget input[type="text"], {{WRAPPER}} .sofir-form-widget input[type="email"], {{WRAPPER}} .sofir-form-widget input[type="tel"], {{WRAPPER}} .sofir-form-widget input[type="number"], {{WRAPPER}} .sofir-form-widget textarea, {{WRAPPER}} .sofir-form-widget select',
            ]
        );

        $this->add_control(
            'field_border_radius',
            [
                'label' => \esc_html__( 'Border Radius', 'sofir' ),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .sofir-form-widget input[type="text"]' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} .sofir-form-widget input[type="email"]' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} .sofir-form-widget input[type="tel"]' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} .sofir-form-widget input[type="number"]' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} .sofir-form-widget textarea' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} .sofir-form-widget select' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_button',
            [
                'label' => \esc_html__( 'Button Style', 'sofir' ),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'button_text_color',
            [
                'label' => \esc_html__( 'Text Color', 'sofir' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .sofir-form-widget button[type="submit"]' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'button_background',
            [
                'label' => \esc_html__( 'Background Color', 'sofir' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .sofir-form-widget button[type="submit"]' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'button_typography',
                'selector' => '{{WRAPPER}} .sofir-form-widget button[type="submit"]',
            ]
        );

        $this->add_control(
            'button_border_radius',
            [
                'label' => \esc_html__( 'Border Radius', 'sofir' ),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .sofir-form-widget button[type="submit"]' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'button_padding',
            [
                'label' => \esc_html__( 'Padding', 'sofir' ),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .sofir-form-widget button[type="submit"]' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $form_id = (int) $settings['form_id'];

        if ( ! $form_id ) {
            if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
                echo '<div class="elementor-alert elementor-alert-info">';
                echo \esc_html__( 'Please select a form from the widget settings.', 'sofir' );
                echo '</div>';
            }
            return;
        }

        $show_title = $settings['show_title'] === 'yes';
        $show_description = $settings['show_description'] === 'yes';

        echo '<div class="sofir-form-widget">';
        echo \do_shortcode( sprintf(
            '[sofir_form id="%d" show_title="%s" show_description="%s"]',
            $form_id,
            $show_title ? 'yes' : 'no',
            $show_description ? 'yes' : 'no'
        ) );
        echo '</div>';
    }
}
