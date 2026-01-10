<?php
namespace SofirCodeWattzIntegration;

class Calendar_Integration {
    private static ?Calendar_Integration $instance = null;

    public static function instance(): Calendar_Integration {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function boot(): void {
        // AJAX handlers
        add_action( 'wp_ajax_sofir_get_calendar_events', [ $this, 'ajax_get_events' ] );
        add_action( 'wp_ajax_nopriv_sofir_get_calendar_events', [ $this, 'ajax_get_events' ] );
        add_action( 'wp_ajax_sofir_create_booking', [ $this, 'ajax_create_booking' ] );
        add_action( 'wp_ajax_sofir_get_time_slots', [ $this, 'ajax_get_time_slots' ] );
        
        // CPT integration
        add_action( 'sofir/appointment/created', [ $this, 'sync_to_calendar' ] );
        add_filter( 'sofir/appointment/form_fields', [ $this, 'add_calendar_fields' ] );
        
        // Voxel integration
        if ( defined( 'VOXEL_VERSION' ) ) {
            add_action( 'voxel/post-type/created', [ $this, 'handle_voxel_event' ] );
            add_filter( 'voxel/calendar/events', [ $this, 'add_sofir_events_to_voxel' ] );
        }
    }

    public function get_events( string $post_type, string $start_date = '', string $end_date = '' ): array {
        if ( empty( $start_date ) ) {
            $start_date = date( 'Y-m-01' );
        }
        
        if ( empty( $end_date ) ) {
            $end_date = date( 'Y-m-t' );
        }

        $args = [
            'post_type' => $post_type,
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'meta_query' => [
                'relation' => 'AND',
                [
                    'key' => 'sofir_appointment_date',
                    'value' => $start_date,
                    'compare' => '>=',
                    'type' => 'DATE',
                ],
                [
                    'key' => 'sofir_appointment_date',
                    'value' => $end_date,
                    'compare' => '<=',
                    'type' => 'DATE',
                ],
            ],
        ];

        $query = new WP_Query( $args );
        $events = [];

        if ( $query->have_posts() ) {
            while ( $query->have_posts() ) {
                $query->the_post();
                $post_id = get_the_ID();

                $event_date = get_post_meta( $post_id, 'sofir_appointment_date', true );
                $start_time = get_post_meta( $post_id, 'sofir_appointment_time', true );
                $duration = get_post_meta( $post_id, 'sofir_appointment_duration', true ) ?: 60;
                $status = get_post_meta( $post_id, 'sofir_appointment_status', true ) ?: 'pending';

                $events[] = [
                    'id' => $post_id,
                    'title' => get_the_title(),
                    'start' => $this->format_datetime( $event_date, $start_time ),
                    'end' => $this->calculate_end_time( $event_date, $start_time, $duration ),
                    'url' => get_permalink(),
                    'status' => $status,
                    'color' => $this->get_status_color( $status ),
                    'extendedProps' => [
                        'post_type' => $post_type,
                        'duration' => $duration,
                        'customer' => get_post_meta( $post_id, 'sofir_customer_name', true ),
                        'email' => get_post_meta( $post_id, 'sofir_customer_email', true ),
                        'phone' => get_post_meta( $post_id, 'sofir_customer_phone', true ),
                    ],
                ];
            }
        }

        wp_reset_postdata();
        return $events;
    }

    private function format_datetime( string $date, string $time ): string {
        return $date . 'T' . $time . ':00';
    }

    private function calculate_end_time( string $date, string $time, int $duration ): string {
        $start = new DateTime( $date . ' ' . $time );
        $end = clone $start;
        $end->add( new DateInterval( 'PT' . $duration . 'M' ) );
        return $end->format( 'Y-m-dTH:i:00' );
    }

    private function get_status_color( string $status ): string {
        $colors = [
            'pending' => '#ffc107',
            'confirmed' => '#28a745',
            'cancelled' => '#dc3545',
            'completed' => '#6c757d',
        ];

        return $colors[$status] ?? '#007bff';
    }

    public function ajax_get_events(): void {
        check_ajax_referer( 'sofir_codewattz_nonce', 'nonce' );

        $post_type = sanitize_text_field( $_POST['post_type'] ?? 'appointment' );
        $start_date = sanitize_text_field( $_POST['start_date'] ?? '' );
        $end_date = sanitize_text_field( $_POST['end_date'] ?? '' );

        $events = $this->get_events( $post_type, $start_date, $end_date );

        wp_send_json_success( $events );
    }

    public function ajax_get_time_slots(): void {
        check_ajax_referer( 'sofir_codewattz_nonce', 'nonce' );

        $date = sanitize_text_field( $_POST['date'] );
        $duration = intval( $_POST['duration'] ?? 30 );
        $service = sanitize_text_field( $_POST['service'] ?? '' );

        $time_slots = $this->generate_time_slots( $date, $duration, $service );

        wp_send_json_success( $time_slots );
    }

    private function generate_time_slots( string $date, int $duration, string $service ): array {
        $slots = [];
        $start_hour = 9;
        $end_hour = 17;
        $slot_duration = $duration;

        // Get existing appointments for this date
        $existing = $this->get_appointments_for_date( $date );

        for ( $hour = $start_hour; $hour < $end_hour; $hour++ ) {
            for ( $minute = 0; $minute < 60; $minute += $slot_duration ) {
                $time = sprintf( '%02d:%02d', $hour, $minute );
                $datetime = "$date $time";

                if ( ! $this->is_time_slot_available( $datetime, $duration, $existing ) ) {
                    continue;
                }

                $slots[] = [
                    'time' => $time,
                    'datetime' => $datetime,
                    'available' => true,
                    'price' => $this->calculate_service_price( $service, $duration ),
                ];
            }
        }

        return $slots;
    }

    private function get_appointments_for_date( string $date ): array {
        $args = [
            'post_type' => 'appointment',
            'post_status' => ['publish', 'pending'],
            'posts_per_page' => -1,
            'meta_query' => [
                [
                    'key' => 'sofir_appointment_date',
                    'value' => $date,
                    'compare' => '=',
                ],
            ],
        ];

        $query = new WP_Query( $args );
        $appointments = [];

        if ( $query->have_posts() ) {
            while ( $query->have_posts() ) {
                $query->the_post();
                $time = get_post_meta( get_the_ID(), 'sofir_appointment_time', true );
                $duration = get_post_meta( get_the_ID(), 'sofir_appointment_duration', true ) ?: 30;
                $status = get_post_meta( get_the_ID(), 'sofir_appointment_status', true );

                if ( $status !== 'cancelled' ) {
                    $appointments[] = [
                        'time' => $time,
                        'duration' => $duration,
                    ];
                }
            }
        }

        wp_reset_postdata();
        return $appointments;
    }

    private function is_time_slot_available( string $datetime, int $duration, array $existing ): bool {
        $slot_start = new DateTime( $datetime );
        $slot_end = clone $slot_start;
        $slot_end->add( new DateInterval( 'PT' . $duration . 'M' ) );

        foreach ( $existing as $appointment ) {
            $apt_start = new DateTime( date( 'Y-m-d', strtotime( $datetime ) ) . ' ' . $appointment['time'] );
            $apt_end = clone $apt_start;
            $apt_end->add( new DateInterval( 'PT' . $appointment['duration'] . 'M' ) );

            // Check for overlap
            if ( ( $slot_start < $apt_end ) && ( $slot_end > $apt_start ) ) {
                return false;
            }
        }

        return true;
    }

    private function calculate_service_price( string $service, int $duration ): float {
        // Logic to calculate price based on service and duration
        $base_prices = [
            'consultation' => 50,
            'treatment' => 100,
            'checkup' => 75,
        ];

        $base_price = $base_prices[$service] ?? 50;
        return $base_price * ( $duration / 30 );
    }

    public function ajax_create_booking(): void {
        check_ajax_referer( 'sofir_codewattz_nonce', 'nonce' );

        $data = json_decode( stripslashes( $_POST['booking_data'] ), true );

        if ( ! $this->validate_booking_data( $data ) ) {
            wp_send_json_error( [ 'message' => __( 'Invalid booking data', 'sofir' ) ] );
        }

        $post_id = wp_insert_post( [
            'post_type' => 'appointment',
            'post_title' => $data['service'] . ' - ' . $data['customer_name'],
            'post_status' => 'pending',
            'meta_input' => [
                'sofir_appointment_date' => $data['date'],
                'sofir_appointment_time' => $data['time'],
                'sofir_appointment_duration' => $data['duration'],
                'sofir_appointment_status' => 'pending',
                'sofir_customer_name' => $data['customer_name'],
                'sofir_customer_email' => $data['customer_email'],
                'sofir_customer_phone' => $data['customer_phone'] ?? '',
                'sofir_service_type' => $data['service'],
                'sofir_total_price' => $data['price'],
            ],
        ] );

        if ( $post_id && ! is_wp_error( $post_id ) ) {
            // Send confirmation email
            $this->send_booking_confirmation( $post_id, $data );

            // Trigger webhook
            do_action( 'sofir/booking/created', $post_id, $data );

            wp_send_json_success( [
                'booking_id' => $post_id,
                'message' => __( 'Booking created successfully', 'sofir' ),
            ] );
        } else {
            wp_send_json_error( [ 'message' => __( 'Failed to create booking', 'sofir' ) ] );
        }
    }

    private function validate_booking_data( array $data ): bool {
        $required = ['date', 'time', 'duration', 'customer_name', 'customer_email', 'service'];

        foreach ( $required as $field ) {
            if ( empty( $data[$field] ) ) {
                return false;
            }
        }

        // Validate email
        if ( ! filter_var( $data['customer_email'], FILTER_VALIDATE_EMAIL ) ) {
            return false;
        }

        // Validate date format
        if ( ! DateTime::createFromFormat( 'Y-m-d', $data['date'] ) ) {
            return false;
        }

        // Validate time format
        if ( ! DateTime::createFromFormat( 'H:i', $data['time'] ) ) {
            return false;
        }

        return true;
    }

    private function send_booking_confirmation( int $post_id, array $data ): void {
        $to = $data['customer_email'];
        $subject = sprintf( __( 'Booking Confirmation: %s on %s at %s', 'sofir' ), 
            $data['service'], $data['date'], $data['time'] );

        $message = $this->get_booking_email_template( $post_id, $data );

        $headers = ['Content-Type: text/html; charset=UTF-8'];

        wp_mail( $to, $subject, $message, $headers );
    }

    private function get_booking_email_template( int $post_id, array $data ): string {
        $template = "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
            <h2 style='color: #007bff;'>" . __( 'Booking Confirmation', 'sofir' ) . "</h2>
            <div style='background: #f8f9fa; padding: 20px; border-radius: 5px; margin: 20px 0;'>
                <h3>" . __( 'Booking Details', 'sofir' ) . "</h3>
                <p><strong>" . __( 'Service:', 'sofir' ) . "</strong> {$data['service']}</p>
                <p><strong>" . __( 'Date:', 'sofir' ) . "</strong> {$data['date']}</p>
                <p><strong>" . __( 'Time:', 'sofir' ) . "</strong> {$data['time']}</p>
                <p><strong>" . __( 'Duration:', 'sofir' ) . "</strong> {$data['duration']} " . __( 'minutes', 'sofir' ) . "</p>
                <p><strong>" . __( 'Price:', 'sofir' ) . "</strong> $" . number_format( $data['price'], 2 ) . "</p>
            </div>
            <div style='background: #e9ecef; padding: 15px; border-radius: 5px;'>
                <h3>" . __( 'Customer Information', 'sofir' ) . "</h3>
                <p><strong>" . __( 'Name:', 'sofir' ) . "</strong> {$data['customer_name']}</p>
                <p><strong>" . __( 'Email:', 'sofir' ) . "</strong> {$data['customer_email']}</p>";
        
        if ( ! empty( $data['customer_phone'] ) ) {
            $template .= "<p><strong>" . __( 'Phone:', 'sofir' ) . "</strong> {$data['customer_phone']}</p>";
        }
        
        $template .= "
            </div>
            <div style='margin-top: 30px; text-align: center;'>
                <a href='" . get_permalink( $post_id ) . "' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>" . __( 'View Booking', 'sofir' ) . "</a>
            </div>
        </div>";

        return $template;
    }

    public function sync_to_calendar( int $post_id ): void {
        // Sync appointment to external calendar systems
        $this->sync_to_google_calendar( $post_id );
        $this->sync_to_outlook_calendar( $post_id );
    }

    private function sync_to_google_calendar( int $post_id ): void {
        // Google Calendar integration logic
        // This would use Google Calendar API
    }

    private function sync_to_outlook_calendar( int $post_id ): void {
        // Outlook Calendar integration logic
        // This would use Microsoft Graph API
    }

    public function create_booking( array $data ): array {
        $result = [
            'success' => false,
            'message' => '',
            'booking_id' => null,
        ];

        try {
            $post_id = wp_insert_post( [
                'post_type' => 'appointment',
                'post_title' => $data['service'] . ' - ' . $data['customer_name'],
                'post_status' => 'pending',
                'meta_input' => [
                    'sofir_appointment_date' => $data['date'],
                    'sofir_appointment_time' => $data['time'],
                    'sofir_appointment_duration' => $data['duration'],
                    'sofir_appointment_status' => 'pending',
                    'sofir_customer_name' => $data['customer_name'],
                    'sofir_customer_email' => $data['customer_email'],
                    'sofir_customer_phone' => $data['customer_phone'] ?? '',
                    'sofir_service_type' => $data['service'],
                    'sofir_total_price' => $data['price'],
                ],
            ] );

            if ( $post_id && ! is_wp_error( $post_id ) ) {
                $this->send_booking_confirmation( $post_id, $data );
                do_action( 'sofir/booking/created', $post_id, $data );

                $result['success'] = true;
                $result['message'] = __( 'Booking created successfully', 'sofir' );
                $result['booking_id'] = $post_id;
            } else {
                $result['message'] = __( 'Failed to create booking', 'sofir' );
            }
        } catch ( Exception $e ) {
            $result['message'] = $e->getMessage();
        }

        return $result;
    }
}