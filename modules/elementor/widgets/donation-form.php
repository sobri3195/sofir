<?php
namespace Sofir\Elementor\Widgets;

use Sofir\Elementor\BaseWidget;

class Donation_Form extends BaseWidget {
    public function get_categories() {
        return [ 'sofir-ecommerce' ];
    }

    public function get_name() {
        return 'sofir-donation-form';
    }

    public function get_title() {
        return \esc_html__( 'Donation Form', 'sofir' );
    }

    public function get_icon() {
        return 'eicon-heart';
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
                'default' => \esc_html__( 'Make a Donation', 'sofir' ),
            ]
        );

        $this->add_control(
            'description',
            [
                'label' => \esc_html__( 'Description', 'sofir' ),
                'type' => \Elementor\Controls_Manager::TEXTAREA,
                'default' => \esc_html__( 'Your donation helps us continue our mission.', 'sofir' ),
            ]
        );

        $this->add_control(
            'suggested_amounts',
            [
                'label' => \esc_html__( 'Suggested Amounts', 'sofir' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => '50000,100000,250000,500000',
                'description' => \esc_html__( 'Comma-separated amounts', 'sofir' ),
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
            'allow_custom_amount',
            [
                'label' => \esc_html__( 'Allow Custom Amount', 'sofir' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
                'description' => \esc_html__( 'Allow donors to enter custom amount', 'sofir' ),
            ]
        );

        $this->add_control(
            'show_donor_info',
            [
                'label' => \esc_html__( 'Show Donor Info', 'sofir' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
                'description' => \esc_html__( 'Show name and email fields', 'sofir' ),
            ]
        );

        $this->add_control(
            'button_text',
            [
                'label' => \esc_html__( 'Button Text', 'sofir' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => \esc_html__( 'Donate Now', 'sofir' ),
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
                    '{{WRAPPER}} .sofir-donation-form-widget' => 'background-color: {{VALUE}};',
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
                    '{{WRAPPER}} .sofir-donation-form-widget' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'form_border',
                'selector' => '{{WRAPPER}} .sofir-donation-form-widget',
            ]
        );

        $this->add_control(
            'form_border_radius',
            [
                'label' => \esc_html__( 'Border Radius', 'sofir' ),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .sofir-donation-form-widget' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_amounts',
            [
                'label' => \esc_html__( 'Amount Buttons', 'sofir' ),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'amount_button_color',
            [
                'label' => \esc_html__( 'Text Color', 'sofir' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .donation-amounts button' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'amount_button_background',
            [
                'label' => \esc_html__( 'Background Color', 'sofir' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .donation-amounts button' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'amount_button_active_color',
            [
                'label' => \esc_html__( 'Active Text Color', 'sofir' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .donation-amounts button.active' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'amount_button_active_background',
            [
                'label' => \esc_html__( 'Active Background Color', 'sofir' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .donation-amounts button.active' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_button',
            [
                'label' => \esc_html__( 'Submit Button', 'sofir' ),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'button_text_color',
            [
                'label' => \esc_html__( 'Text Color', 'sofir' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .sofir-donation-form-widget button[type="submit"]' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'button_background',
            [
                'label' => \esc_html__( 'Background Color', 'sofir' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .sofir-donation-form-widget button[type="submit"]' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'button_typography',
                'selector' => '{{WRAPPER}} .sofir-donation-form-widget button[type="submit"]',
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();

        $shortcode_atts = [
            'title="' . esc_attr( $settings['title'] ) . '"',
            'currency="' . esc_attr( $settings['currency'] ) . '"',
            'suggested_amounts="' . esc_attr( $settings['suggested_amounts'] ) . '"',
        ];

        if ( ! empty( $settings['description'] ) ) {
            $shortcode_atts[] = 'description="' . esc_attr( $settings['description'] ) . '"';
        }

        if ( ! empty( $settings['button_text'] ) ) {
            $shortcode_atts[] = 'button_text="' . esc_attr( $settings['button_text'] ) . '"';
        }

        if ( $settings['allow_custom_amount'] === 'yes' ) {
            $shortcode_atts[] = 'allow_custom="yes"';
        }

        if ( $settings['show_donor_info'] === 'yes' ) {
            $shortcode_atts[] = 'show_donor_info="yes"';
        }

        echo '<div class="sofir-donation-form-widget">';
        echo \do_shortcode( '[sofir_donation_form ' . implode( ' ', $shortcode_atts ) . ']' );
        echo '</div>';
    }
}
