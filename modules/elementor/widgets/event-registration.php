<?php
namespace Sofir\Elementor\Widgets;

use Sofir\Elementor\BaseWidget;

class Event_Registration extends BaseWidget {
    public function get_categories() {
        return [ 'sofir-booking' ];
    }

    public function get_name() {
        return 'sofir-event-registration';
    }

    public function get_title() {
        return \esc_html__( 'Event Registration Form', 'sofir' );
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

        $this->add_control(
            'event_id',
            [
                'label' => \esc_html__( 'Event ID', 'sofir' ),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 0,
                'description' => \esc_html__( 'Leave 0 for current post', 'sofir' ),
            ]
        );

        $this->add_control(
            'show_event_info',
            [
                'label' => \esc_html__( 'Show Event Info', 'sofir' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_capacity',
            [
                'label' => \esc_html__( 'Show Capacity', 'sofir' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_terms',
            [
                'label' => \esc_html__( 'Show Terms & Conditions', 'sofir' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'button_text',
            [
                'label' => \esc_html__( 'Button Text', 'sofir' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => \esc_html__( 'Register Now', 'sofir' ),
            ]
        );

        $this->add_control(
            'success_message',
            [
                'label' => \esc_html__( 'Success Message', 'sofir' ),
                'type' => \Elementor\Controls_Manager::TEXTAREA,
                'default' => \esc_html__( 'Registration successful! You will receive a confirmation email shortly.', 'sofir' ),
            ]
        );

        $this->end_controls_section();

        $this->add_style_controls();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $event_id = (int) $settings['event_id'];
        
        if ( ! $event_id ) {
            $event_id = \get_the_ID();
        }

        if ( ! $event_id ) {
            echo '<p>' . \esc_html__( 'Please select an event.', 'sofir' ) . '</p>';
            return;
        }

        echo '<div class="sofir-event-registration-widget">';
        echo '<form class="event-registration-form" data-event-id="' . esc_attr( $event_id ) . '">';
        
        if ( $settings['show_event_info'] === 'yes' ) {
            $event_date = \get_post_meta( $event_id, 'sofir_event_date', true );
            $event_location = \get_post_meta( $event_id, 'sofir_event_location', true );
            
            echo '<div class="event-info">';
            echo '<h3>' . \esc_html( \get_the_title( $event_id ) ) . '</h3>';
            if ( $event_date ) {
                echo '<p class="event-date"><strong>' . \esc_html__( 'Date:', 'sofir' ) . '</strong> ' . esc_html( $event_date ) . '</p>';
            }
            if ( $event_location ) {
                echo '<p class="event-location"><strong>' . \esc_html__( 'Location:', 'sofir' ) . '</strong> ' . esc_html( $event_location ) . '</p>';
            }
            echo '</div>';
        }

        if ( $settings['show_capacity'] === 'yes' ) {
            $capacity = \get_post_meta( $event_id, 'sofir_event_capacity', true );
            if ( $capacity ) {
                echo '<p class="event-capacity"><strong>' . \esc_html__( 'Available Spots:', 'sofir' ) . '</strong> ' . esc_html( $capacity ) . '</p>';
            }
        }

        \wp_nonce_field( 'sofir_event_registration', 'event_registration_nonce' );
        
        echo '<div class="form-group">';
        echo '<label for="attendee_name">' . \esc_html__( 'Full Name', 'sofir' ) . ' <span class="required">*</span></label>';
        echo '<input type="text" id="attendee_name" name="attendee_name" required>';
        echo '</div>';

        echo '<div class="form-group">';
        echo '<label for="attendee_email">' . \esc_html__( 'Email', 'sofir' ) . ' <span class="required">*</span></label>';
        echo '<input type="email" id="attendee_email" name="attendee_email" required>';
        echo '</div>';

        echo '<div class="form-group">';
        echo '<label for="attendee_phone">' . \esc_html__( 'Phone', 'sofir' ) . '</label>';
        echo '<input type="tel" id="attendee_phone" name="attendee_phone">';
        echo '</div>';

        echo '<div class="form-group">';
        echo '<label for="attendee_notes">' . \esc_html__( 'Notes', 'sofir' ) . '</label>';
        echo '<textarea id="attendee_notes" name="attendee_notes" rows="4"></textarea>';
        echo '</div>';

        if ( $settings['show_terms'] === 'yes' ) {
            echo '<div class="form-group">';
            echo '<label>';
            echo '<input type="checkbox" name="accept_terms" required> ';
            echo \esc_html__( 'I accept the terms and conditions', 'sofir' );
            echo '</label>';
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
