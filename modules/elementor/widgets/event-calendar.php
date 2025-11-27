<?php
namespace Sofir\Elementor\Widgets;

use Sofir\Elementor\BaseWidget;

class Event_Calendar extends BaseWidget {
    public function get_categories() {
        return [ 'sofir-booking' ];
    }

    public function get_name() {
        return 'sofir-event-calendar';
    }

    public function get_title() {
        return \esc_html__( 'Event Calendar', 'sofir' );
    }

    public function get_icon() {
        return 'eicon-calendar';
    }

    protected function register_controls() {
        $this->start_controls_section(
            'section_content',
            [
                'label' => \esc_html__( 'Content', 'sofir' ),
            ]
        );

        $this->add_control(
            'view_type',
            [
                'label' => \esc_html__( 'Default View', 'sofir' ),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'month',
                'options' => [
                    'month' => \esc_html__( 'Month', 'sofir' ),
                    'week' => \esc_html__( 'Week', 'sofir' ),
                    'day' => \esc_html__( 'Day', 'sofir' ),
                ],
            ]
        );

        $this->add_control(
            'show_navigation',
            [
                'label' => \esc_html__( 'Show Navigation', 'sofir' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_event_details',
            [
                'label' => \esc_html__( 'Show Event Details', 'sofir' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'enable_popup',
            [
                'label' => \esc_html__( 'Enable Popup', 'sofir' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->end_controls_section();

        $this->add_style_controls();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();

        echo '<div class="sofir-event-calendar-widget">';
        echo '<div class="sofir-calendar-container" 
                   data-view="' . esc_attr( $settings['view_type'] ) . '"
                   data-navigation="' . esc_attr( $settings['show_navigation'] ) . '"
                   data-details="' . esc_attr( $settings['show_event_details'] ) . '"
                   data-popup="' . esc_attr( $settings['enable_popup'] ) . '">';
        
        $events = \get_posts( [
            'post_type' => 'event',
            'posts_per_page' => -1,
            'meta_key' => 'sofir_event_date',
            'orderby' => 'meta_value',
            'order' => 'ASC',
        ] );

        if ( $events ) {
            echo '<div class="calendar-header">';
            echo '<button class="calendar-prev">&laquo;</button>';
            echo '<h3 class="calendar-title"></h3>';
            echo '<button class="calendar-next">&raquo;</button>';
            echo '</div>';
            
            echo '<div class="calendar-body">';
            echo '<table class="calendar-table">';
            echo '<thead><tr>';
            $days = [ 'Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat' ];
            foreach ( $days as $day ) {
                echo '<th>' . esc_html( $day ) . '</th>';
            }
            echo '</tr></thead>';
            echo '<tbody class="calendar-dates"></tbody>';
            echo '</table>';
            echo '</div>';

            echo '<div class="calendar-events-data" style="display: none;">';
            foreach ( $events as $event ) {
                $event_date = \get_post_meta( $event->ID, 'sofir_event_date', true );
                $event_location = \get_post_meta( $event->ID, 'sofir_event_location', true );
                
                echo '<div class="event-data" 
                          data-id="' . esc_attr( $event->ID ) . '"
                          data-date="' . esc_attr( $event_date ) . '"
                          data-title="' . esc_attr( $event->post_title ) . '"
                          data-location="' . esc_attr( $event_location ) . '"
                          data-url="' . esc_url( \get_permalink( $event->ID ) ) . '"></div>';
            }
            echo '</div>';
        } else {
            echo '<p>' . \esc_html__( 'No events found.', 'sofir' ) . '</p>';
        }

        echo '</div>';
        echo '</div>';
    }
}
