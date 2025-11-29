<?php
namespace Sofir\Elementor\Widgets;

use Sofir\Elementor\BaseWidget;

class Subscription_Form extends BaseWidget {
    public function get_categories() {
        return [ 'sofir-ecommerce' ];
    }

    public function get_name() {
        return 'sofir-subscription-form';
    }

    public function get_title() {
        return \esc_html__( 'Subscription Form', 'sofir' );
    }

    public function get_icon() {
        return 'eicon-sync';
    }

    protected function register_controls() {
        $this->start_controls_section(
            'section_content',
            [
                'label' => \esc_html__( 'Content', 'sofir' ),
            ]
        );

        $this->add_control(
            'title',
            [
                'label' => \esc_html__( 'Title', 'sofir' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => \esc_html__( 'Subscribe Now', 'sofir' ),
            ]
        );

        $this->add_control(
            'description',
            [
                'label' => \esc_html__( 'Description', 'sofir' ),
                'type' => \Elementor\Controls_Manager::TEXTAREA,
                'default' => \esc_html__( 'Get access to premium features with our subscription plans.', 'sofir' ),
            ]
        );

        $subscriptions = \get_posts( [
            'post_type' => 'sofir_subscription',
            'posts_per_page' => -1,
            'post_status' => 'publish',
        ] );

        $subscription_options = [ '' => \esc_html__( 'All subscriptions', 'sofir' ) ];
        foreach ( $subscriptions as $subscription ) {
            $subscription_options[ $subscription->ID ] = $subscription->post_title;
        }

        $this->add_control(
            'subscription_id',
            [
                'label' => \esc_html__( 'Specific Subscription', 'sofir' ),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => $subscription_options,
                'default' => '',
                'description' => \esc_html__( 'Leave empty to show all available subscriptions', 'sofir' ),
            ]
        );

        $this->add_control(
            'currency',
            [
                'label' => \esc_html__( 'Currency', 'sofir' ),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'IDR',
                'options' => [
                    'IDR' => 'IDR - Indonesian Rupiah',
                    'USD' => 'USD - US Dollar',
                    'EUR' => 'EUR - Euro',
                    'GBP' => 'GBP - British Pound',
                    'SGD' => 'SGD - Singapore Dollar',
                    'MYR' => 'MYR - Malaysian Ringgit',
                ],
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
                    'table' => \esc_html__( 'Table', 'sofir' ),
                ],
            ]
        );

        $this->add_control(
            'columns',
            [
                'label' => \esc_html__( 'Columns', 'sofir' ),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => '3',
                'options' => [
                    '1' => '1',
                    '2' => '2',
                    '3' => '3',
                    '4' => '4',
                ],
                'condition' => [
                    'layout' => 'grid',
                ],
            ]
        );

        $this->add_control(
            'show_features',
            [
                'label' => \esc_html__( 'Show Features', 'sofir' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
                'description' => \esc_html__( 'Display subscription features list', 'sofir' ),
            ]
        );

        $this->add_control(
            'button_text',
            [
                'label' => \esc_html__( 'Button Text', 'sofir' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => \esc_html__( 'Subscribe', 'sofir' ),
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
                    '{{WRAPPER}} .sofir-subscription-form-widget' => 'background-color: {{VALUE}};',
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
                    '{{WRAPPER}} .sofir-subscription-form-widget' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'form_border',
                'selector' => '{{WRAPPER}} .sofir-subscription-form-widget',
            ]
        );

        $this->add_control(
            'form_border_radius',
            [
                'label' => \esc_html__( 'Border Radius', 'sofir' ),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .sofir-subscription-form-widget' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_card',
            [
                'label' => \esc_html__( 'Plan Card', 'sofir' ),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'card_background',
            [
                'label' => \esc_html__( 'Background Color', 'sofir' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .subscription-plan' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'card_border',
                'selector' => '{{WRAPPER}} .subscription-plan',
            ]
        );

        $this->add_control(
            'card_border_radius',
            [
                'label' => \esc_html__( 'Border Radius', 'sofir' ),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .subscription-plan' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'card_box_shadow',
                'selector' => '{{WRAPPER}} .subscription-plan',
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
                    '{{WRAPPER}} .sofir-subscription-form-widget button.subscribe-button' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'button_background',
            [
                'label' => \esc_html__( 'Background Color', 'sofir' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .sofir-subscription-form-widget button.subscribe-button' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'button_typography',
                'selector' => '{{WRAPPER}} .sofir-subscription-form-widget button.subscribe-button',
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();

        $shortcode_atts = [
            'currency="' . esc_attr( $settings['currency'] ) . '"',
            'layout="' . esc_attr( $settings['layout'] ) . '"',
        ];

        if ( ! empty( $settings['title'] ) ) {
            $shortcode_atts[] = 'title="' . esc_attr( $settings['title'] ) . '"';
        }

        if ( ! empty( $settings['description'] ) ) {
            $shortcode_atts[] = 'description="' . esc_attr( $settings['description'] ) . '"';
        }

        if ( ! empty( $settings['subscription_id'] ) ) {
            $shortcode_atts[] = 'subscription_id="' . esc_attr( $settings['subscription_id'] ) . '"';
        }

        if ( $settings['layout'] === 'grid' && ! empty( $settings['columns'] ) ) {
            $shortcode_atts[] = 'columns="' . esc_attr( $settings['columns'] ) . '"';
        }

        if ( $settings['show_features'] === 'yes' ) {
            $shortcode_atts[] = 'show_features="yes"';
        }

        if ( ! empty( $settings['button_text'] ) ) {
            $shortcode_atts[] = 'button_text="' . esc_attr( $settings['button_text'] ) . '"';
        }

        echo '<div class="sofir-subscription-form-widget">';
        echo \do_shortcode( '[sofir_subscription_form ' . implode( ' ', $shortcode_atts ) . ']' );
        echo '</div>';
    }
}
