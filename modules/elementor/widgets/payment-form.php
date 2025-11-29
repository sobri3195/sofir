<?php
namespace Sofir\Elementor\Widgets;

use Sofir\Elementor\BaseWidget;

class Payment_Form extends BaseWidget {
    public function get_categories() {
        return [ 'sofir-ecommerce' ];
    }

    public function get_name() {
        return 'sofir-payment-form';
    }

    public function get_title() {
        return \esc_html__( 'Payment Form', 'sofir' );
    }

    public function get_icon() {
        return 'eicon-price-table';
    }

    protected function register_controls() {
        $this->start_controls_section(
            'section_content',
            [
                'label' => \esc_html__( 'Content', 'sofir' ),
            ]
        );

        $this->add_control(
            'item_name',
            [
                'label' => \esc_html__( 'Item Name', 'sofir' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => \esc_html__( 'Product or Service', 'sofir' ),
            ]
        );

        $this->add_control(
            'amount',
            [
                'label' => \esc_html__( 'Amount', 'sofir' ),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 100000,
                'min' => 0,
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
            'description',
            [
                'label' => \esc_html__( 'Description', 'sofir' ),
                'type' => \Elementor\Controls_Manager::TEXTAREA,
                'default' => '',
                'placeholder' => \esc_html__( 'Payment description', 'sofir' ),
            ]
        );

        $this->add_control(
            'button_text',
            [
                'label' => \esc_html__( 'Button Text', 'sofir' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => \esc_html__( 'Pay Now', 'sofir' ),
            ]
        );

        $this->add_control(
            'show_customer_info',
            [
                'label' => \esc_html__( 'Show Customer Info', 'sofir' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
                'description' => \esc_html__( 'Show name, email, phone fields', 'sofir' ),
            ]
        );

        $this->add_control(
            'enable_quantity',
            [
                'label' => \esc_html__( 'Enable Quantity', 'sofir' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'no',
                'description' => \esc_html__( 'Allow customers to select quantity', 'sofir' ),
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
                    '{{WRAPPER}} .sofir-payment-form-widget' => 'background-color: {{VALUE}};',
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
                    '{{WRAPPER}} .sofir-payment-form-widget' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'form_border',
                'selector' => '{{WRAPPER}} .sofir-payment-form-widget',
            ]
        );

        $this->add_control(
            'form_border_radius',
            [
                'label' => \esc_html__( 'Border Radius', 'sofir' ),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .sofir-payment-form-widget' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
                    '{{WRAPPER}} .sofir-payment-form-widget button[type="submit"]' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'button_background',
            [
                'label' => \esc_html__( 'Background Color', 'sofir' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .sofir-payment-form-widget button[type="submit"]' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'button_typography',
                'selector' => '{{WRAPPER}} .sofir-payment-form-widget button[type="submit"]',
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();

        $shortcode_atts = [
            'item_name="' . esc_attr( $settings['item_name'] ) . '"',
            'amount="' . esc_attr( $settings['amount'] ) . '"',
            'currency="' . esc_attr( $settings['currency'] ) . '"',
        ];

        if ( ! empty( $settings['description'] ) ) {
            $shortcode_atts[] = 'description="' . esc_attr( $settings['description'] ) . '"';
        }

        if ( ! empty( $settings['button_text'] ) ) {
            $shortcode_atts[] = 'button_text="' . esc_attr( $settings['button_text'] ) . '"';
        }

        if ( $settings['show_customer_info'] === 'yes' ) {
            $shortcode_atts[] = 'show_customer_info="yes"';
        }

        if ( $settings['enable_quantity'] === 'yes' ) {
            $shortcode_atts[] = 'enable_quantity="yes"';
        }

        echo '<div class="sofir-payment-form-widget">';
        echo \do_shortcode( '[sofir_payment_form ' . implode( ' ', $shortcode_atts ) . ']' );
        echo '</div>';
    }
}
