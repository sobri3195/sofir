<?php
namespace SofirCodeWattzWidgets;

use SofirElementorBaseWidget;
use ElementorControls_Manager;
use ElementorGroup_Control_Typography;

class Calendar_Widget extends BaseWidget {

    public function get_name(): string {
        return 'sofir-codewattz-calendar';
    }

    public function get_title(): string {
        return esc_html__( 'SOFIR Calendar', 'sofir' );
    }

    public function get_icon(): string {
        return 'eicon-calendar';
    }

    public function get_categories(): array {
        return [ 'sofir', 'sofir-booking' ];
    }

    public function get_keywords(): array {
        return [ 'calendar', 'booking', 'appointment', 'schedule', 'sofir', 'codewattz' ];
    }

    protected function register_controls(): void {
        $this->start_controls_section(
            'section_content',
            [
                'label' => esc_html__( 'Content', 'sofir' ),
            ]
        );

        $this->add_control(
            'post_type',
            [
                'label' => esc_html__( 'Post Type', 'sofir' ),
                'type' => Controls_Manager::SELECT,
                'options' => $this->get_appointment_post_types(),
                'default' => 'appointment',
            ]
        );

        $this->add_control(
            'default_view',
            [
                'label' => esc_html__( 'Default View', 'sofir' ),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'month' => esc_html__( 'Month', 'sofir' ),
                    'week' => esc_html__( 'Week', 'sofir' ),
                    'day' => esc_html__( 'Day', 'sofir' ),
                    'list' => esc_html__( 'List', 'sofir' ),
                ],
                'default' => 'month',
            ]
        );

        $this->add_control(
            'show_filters',
            [
                'label' => esc_html__( 'Show Filters', 'sofir' ),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'allow_booking',
            [
                'label' => esc_html__( 'Allow Booking', 'sofir' ),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'services',
            [
                'label' => esc_html__( 'Available Services', 'sofir' ),
                'type' => Controls_Manager::TEXTAREA,
                'description' => esc_html__( 'Enter services line by line', 'sofir' ),
                'default' => "Consultation\nTreatment\nCheckup",
                'condition' => [
                    'allow_booking' => 'yes',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_styling',
            [
                'label' => esc_html__( 'Styling', 'sofir' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'primary_color',
            [
                'label' => esc_html__( 'Primary Color', 'sofir' ),
                'type' => Controls_Manager::COLOR,
                'default' => '#0073aa',
            ]
        );

        $this->add_control(
            'success_color',
            [
                'label' => esc_html__( 'Success Color', 'sofir' ),
                'type' => Controls_Manager::COLOR,
                'default' => '#28a745',
            ]
        );

        $this->add_control(
            'warning_color',
            [
                'label' => esc_html__( 'Warning Color', 'sofir' ),
                'type' => Controls_Manager::COLOR,
                'default' => '#ffc107',
            ]
        );

        $this->add_control(
            'danger_color',
            [
                'label' => esc_html__( 'Danger Color', 'sofir' ),
                'type' => Controls_Manager::COLOR,
                'default' => '#dc3545',
            ]
        );

        $this->add_control(
            'border_radius',
            [
                'label' => esc_html__( 'Border Radius', 'sofir' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 20,
                    ],
                ],
                'default' => [
                    'size' => 8,
                ],
                'selectors' => [
                    '{{WRAPPER}} .sofir-codewattz-calendar' => 'border-radius: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .calendar-event' => 'border-radius: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .booking-modal-content' => 'border-radius: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_typography',
            [
                'label' => esc_html__( 'Typography', 'sofir' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            ElementorGroup_Control_Typography::get_type(),
            [
                'name' => 'title_typography',
                'label' => esc_html__( 'Title Typography', 'sofir' ),
                'selector' => '{{WRAPPER}} .calendar-title',
            ]
        );

        $this->add_group_control(
            ElementorGroup_Control_Typography::get_type(),
            [
                'name' => 'event_typography',
                'label' => esc_html__( 'Event Typography', 'sofir' ),
                'selector' => '{{WRAPPER}} .calendar-event',
            ]
        );

        $this->end_controls_section();
    }

    private function get_appointment_post_types(): array {
        $post_types = get_post_types( [ 'public' => true ], 'objects' );
        $options = [];

        foreach ( $post_types as $post_type ) {
            if ( in_array( $post_type->name, [ 'appointment', 'booking', 'event' ] ) ) {
                $options[$post_type->name] = $post_type->labels->singular_name;
            }
        }

        // Add SOFIR CPTs with calendar support
        if ( class_exists( 'SofirCptManager' ) ) {
            $cpt_manager = SofirCptManager::instance();
            $sofir_cpts = $cpt_manager->get_post_types();

            foreach ( $sofir_cpts as $slug => $definition ) {
                if ( isset( $definition['has_calendar'] ) && $definition['has_calendar'] ) {
                    $options[$slug] = $definition['labels']['singular_name'];
                }
            }
        }

        return $options;
    }

    protected function render(): void {
        $settings = $this->get_settings_for_display();
        
        $services = ! empty( $settings['services'] ) ? 
            array_map( 'trim', explode( "\n", $settings['services'] ) ) : 
            [];
        
        $this->add_render_attribute( 'calendar', [
            'class' => 'sofir-codewattz-calendar',
            'data-post-type' => $settings['post_type'],
            'data-default-view' => $settings['default_view'],
            'data-show-filters' => $settings['show_filters'],
            'data-allow-booking' => $settings['allow_booking'],
            'data-services' => wp_json_encode( $services ),
        ] );

        // Custom CSS variables for colors
        $this->add_render_attribute( 'calendar', 'style', 
            '--voxel-primary: ' . $settings['primary_color'] . ';' .
            '--voxel-success: ' . $settings['success_color'] . ';' .
            '--voxel-warning: ' . $settings['warning_color'] . ';' .
            '--voxel-danger: ' . $settings['danger_color'] . ';'
        );
        ?>
        <div <?php echo $this->get_render_attribute_string( 'calendar' ); ?>>
            <div class="calendar-loading">
                <div class="spinner"></div>
                <?php esc_html_e( 'Loading calendar...', 'sofir' ); ?>
            </div>
        </div>
        <?php
    }

    protected function content_template(): void {
        ?>
        <div class="sofir-codewattz-calendar" 
             data-post-type="{{ settings.post_type }}" 
             data-default-view="{{ settings.default_view }}"
             data-show-filters="{{ settings.show_filters }}"
             data-allow-booking="{{ settings.allow_booking }}"
             style="--voxel-primary: {{ settings.primary_color }}; --voxel-success: {{ settings.success_color }}; --voxel-warning: {{ settings.warning_color }}; --voxel-danger: {{ settings.danger_color }};">
            <div class="calendar-loading">
                <div class="spinner"></div>
                <?php esc_html_e( 'Loading calendar...', 'sofir' ); ?>
            </div>
        </div>
        <?php
    }
}