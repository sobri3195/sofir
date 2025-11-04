<?php
namespace Sofir\Appointments;

class Manager {
    private static ?Manager $instance = null;

    public static function instance(): Manager {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function boot(): void {
        \add_action( 'wp_ajax_sofir_book_appointment', [ $this, 'handle_booking' ] );
        \add_action( 'wp_ajax_nopriv_sofir_book_appointment', [ $this, 'handle_booking_nopriv' ] );
    }

    public function handle_booking_nopriv(): void {
        \wp_send_json_error( \__( 'You must be logged in to book an appointment.', 'sofir' ) );
    }

    public function handle_booking(): void {
        if ( ! \check_ajax_referer( 'sofir_book_appointment', 'sofir_appointment_nonce', false ) ) {
            \wp_send_json_error( \__( 'Invalid security token.', 'sofir' ) );
            return;
        }

        if ( ! \is_user_logged_in() ) {
            \wp_send_json_error( \__( 'You must be logged in to book an appointment.', 'sofir' ) );
            return;
        }

        $title = isset( $_POST['appointment_title'] ) ? \sanitize_text_field( $_POST['appointment_title'] ) : '';
        $datetime = isset( $_POST['appointment_datetime'] ) ? \sanitize_text_field( $_POST['appointment_datetime'] ) : '';
        $duration = isset( $_POST['appointment_duration'] ) ? \absint( $_POST['appointment_duration'] ) : 30;
        $notes = isset( $_POST['appointment_notes'] ) ? \sanitize_textarea_field( $_POST['appointment_notes'] ) : '';

        if ( empty( $title ) ) {
            \wp_send_json_error( \__( 'Appointment title is required.', 'sofir' ) );
            return;
        }

        if ( empty( $datetime ) || strtotime( $datetime ) === false ) {
            \wp_send_json_error( \__( 'Valid date and time is required.', 'sofir' ) );
            return;
        }

        $post_data = [
            'post_title'   => $title,
            'post_content' => $notes,
            'post_type'    => 'appointment',
            'post_status'  => 'publish',
            'post_author'  => \get_current_user_id(),
        ];

        $post_id = \wp_insert_post( $post_data );

        if ( \is_wp_error( $post_id ) ) {
            \wp_send_json_error( \__( 'Failed to create appointment.', 'sofir' ) );
            return;
        }

        \update_post_meta( $post_id, 'sofir_appointment_datetime', gmdate( 'Y-m-d H:i:s', strtotime( $datetime ) ) );
        \update_post_meta( $post_id, 'sofir_appointment_duration', $duration );
        \update_post_meta( $post_id, 'sofir_appointment_status', 'pending' );
        \update_post_meta( $post_id, 'sofir_appointment_client', \get_current_user_id() );

        \do_action( 'sofir/appointment/booked', $post_id );

        \wp_send_json_success( [
            'appointment_id' => $post_id,
            'redirect'       => \get_permalink( $post_id ),
            'message'        => \__( 'Appointment booked successfully!', 'sofir' ),
        ] );
    }
}
