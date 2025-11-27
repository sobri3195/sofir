<?php
namespace Sofir\Elementor\Widgets;

use Sofir\Elementor\BaseWidget;

class Booking_Form extends BaseWidget {
    public function get_categories() {
        return [ 'sofir-booking' ];
    }

    public function get_name() {
        return 'sofir-booking-form';
    }

    public function get_title() {
        return \esc_html__( 'Booking Form', 'sofir' );
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

        $post_types = \get_post_types( [ 'public' => true ], 'objects' );
        $post_type_options = [];
        foreach ( $post_types as $post_type ) {
            $post_type_options[ $post_type->name ] = $post_type->label;
        }

        $this->add_control(
            'post_type',
            [
                'label' => \esc_html__( 'Booking Type', 'sofir' ),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'appointment',
                'options' => $post_type_options,
            ]
        );

        $this->add_control(
            'item_id',
            [
                'label' => \esc_html__( 'Item ID', 'sofir' ),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 0,
                'description' => \esc_html__( 'Leave 0 for current post', 'sofir' ),
            ]
        );

        $this->add_control(
            'show_calendar',
            [
                'label' => \esc_html__( 'Show Calendar', 'sofir' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_time_slots',
            [
                'label' => \esc_html__( 'Show Time Slots', 'sofir' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_notes',
            [
                'label' => \esc_html__( 'Show Notes Field', 'sofir' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'require_payment',
            [
                'label' => \esc_html__( 'Require Payment', 'sofir' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'no',
            ]
        );

        $this->add_control(
            'button_text',
            [
                'label' => \esc_html__( 'Button Text', 'sofir' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => \esc_html__( 'Book Now', 'sofir' ),
            ]
        );

        $this->end_controls_section();

        $this->add_style_controls();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $item_id = (int) $settings['item_id'];
        
        if ( ! $item_id ) {
            $item_id = \get_the_ID();
        }

        echo '<div class="sofir-booking-form-widget">';
        echo '<form class="booking-form" data-item-id="' . esc_attr( $item_id ) . '" data-post-type="' . esc_attr( $settings['post_type'] ) . '">';
        
        \wp_nonce_field( 'sofir_booking', 'booking_nonce' );
        
        echo '<div class="form-group">';
        echo '<label for="booking_name">' . \esc_html__( 'Full Name', 'sofir' ) . ' <span class="required">*</span></label>';
        echo '<input type="text" id="booking_name" name="booking_name" required>';
        echo '</div>';

        echo '<div class="form-group">';
        echo '<label for="booking_email">' . \esc_html__( 'Email', 'sofir' ) . ' <span class="required">*</span></label>';
        echo '<input type="email" id="booking_email" name="booking_email" required>';
        echo '</div>';

        echo '<div class="form-group">';
        echo '<label for="booking_phone">' . \esc_html__( 'Phone', 'sofir' ) . ' <span class="required">*</span></label>';
        echo '<input type="tel" id="booking_phone" name="booking_phone" required>';
        echo '</div>';

        if ( $settings['show_calendar'] === 'yes' ) {
            echo '<div class="form-group">';
            echo '<label for="booking_date">' . \esc_html__( 'Select Date', 'sofir' ) . ' <span class="required">*</span></label>';
            echo '<input type="date" id="booking_date" name="booking_date" required>';
            echo '</div>';
        }

        if ( $settings['show_time_slots'] === 'yes' ) {
            echo '<div class="form-group">';
            echo '<label for="booking_time">' . \esc_html__( 'Select Time', 'sofir' ) . ' <span class="required">*</span></label>';
            echo '<select id="booking_time" name="booking_time" required>';
            echo '<option value="">' . \esc_html__( 'Choose a time', 'sofir' ) . '</option>';
            for ( $hour = 9; $hour <= 17; $hour++ ) {
                echo '<option value="' . sprintf( '%02d:00', $hour ) . '">' . sprintf( '%02d:00', $hour ) . '</option>';
                echo '<option value="' . sprintf( '%02d:30', $hour ) . '">' . sprintf( '%02d:30', $hour ) . '</option>';
            }
            echo '</select>';
            echo '</div>';
        }

        echo '<div class="form-group">';
        echo '<label for="booking_guests">' . \esc_html__( 'Number of Guests', 'sofir' ) . '</label>';
        echo '<input type="number" id="booking_guests" name="booking_guests" min="1" value="1">';
        echo '</div>';

        if ( $settings['show_notes'] === 'yes' ) {
            echo '<div class="form-group">';
            echo '<label for="booking_notes">' . \esc_html__( 'Special Requests', 'sofir' ) . '</label>';
            echo '<textarea id="booking_notes" name="booking_notes" rows="4"></textarea>';
            echo '</div>';
        }

        if ( $settings['require_payment'] === 'yes' ) {
            echo '<div class="form-group">';
            echo '<p class="payment-notice">' . \esc_html__( 'Payment will be required to confirm your booking.', 'sofir' ) . '</p>';
            echo '</div>';
        }

        echo '<div class="form-actions">';
        echo '<button type="submit" class="submit-button">' . esc_html( $settings['button_text'] ) . '</button>';
        echo '</div>';

        echo '<div class="form-message" style="display: none;"></div>';

        echo '</form>';
        echo '</div>';
    }
}
