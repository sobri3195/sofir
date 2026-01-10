<?php
namespace SofirCodeWattzIntegration;

class Advanced_Fields {
    private static ?Advanced_Fields $instance = null;

    public static function instance(): Advanced_Fields {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function boot(): void {
        add_filter( 'sofir/field/render', [ $this, 'render_advanced_field' ], 10, 3 );
        add_action( 'sofir/field/save', [ $this, 'save_advanced_field' ], 10, 3 );
        add_filter( 'sofir/field/validation', [ $this, 'validate_advanced_field' ], 10, 3 );
        
        // Business hours field
        add_action( 'wp_ajax_sofir_get_business_hours_status', [ $this, 'ajax_get_business_hours_status' ] );
        
        // Advanced rating field
        add_action( 'wp_ajax_sofir_submit_rating', [ $this, 'ajax_submit_rating' ] );
    }

    public function render_advanced_field( string $html, array $field, $value ): string {
        switch ( $field['type'] ) {
            case 'business_hours':
                return $this->render_business_hours_field( $field, $value );
            case 'advanced_rating':
                return $this->render_advanced_rating_field( $field, $value );
            case 'price_range':
                return $this->render_price_range_field( $field, $value );
            case 'location_plus':
                return $this->render_location_plus_field( $field, $value );
            case 'gallery_plus':
                return $this->render_gallery_plus_field( $field, $value );
            default:
                return $html;
        }
    }

    private function render_business_hours_field( array $field, $value ): string {
        $week_days = [
            'monday' => __( 'Monday', 'sofir' ),
            'tuesday' => __( 'Tuesday', 'sofir' ),
            'wednesday' => __( 'Wednesday', 'sofir' ),
            'thursday' => __( 'Thursday', 'sofir' ),
            'friday' => __( 'Friday', 'sofir' ),
            'saturday' => __( 'Saturday', 'sofir' ),
            'sunday' => __( 'Sunday', 'sofir' ),
        ];

        $default_hours = [
            'monday' => [ 'open' => '09:00', 'close' => '17:00', 'closed' => false ],
            'tuesday' => [ 'open' => '09:00', 'close' => '17:00', 'closed' => false ],
            'wednesday' => [ 'open' => '09:00', 'close' => '17:00', 'closed' => false ],
            'thursday' => [ 'open' => '09:00', 'close' => '17:00', 'closed' => false ],
            'friday' => [ 'open' => '09:00', 'close' => '17:00', 'closed' => false ],
            'saturday' => [ 'open' => '09:00', 'close' => '13:00', 'closed' => false ],
            'sunday' => [ 'open' => '09:00', 'close' => '17:00', 'closed' => true ],
        ];

        $hours = ! empty( $value ) ? array_merge( $default_hours, $value ) : $default_hours;

        ob_start();
        ?>
        <div class="sofir-business-hours-field" data-field-name="<?php echo esc_attr( $field['name'] ); ?>">
            <div class="hours-grid">
                <?php foreach ( $week_days as $day => $label ): ?>
                    <div class="hours-row" data-day="<?php echo esc_attr( $day ); ?>">
                        <div class="day-label">
                            <label><?php echo esc_html( $label ); ?></label>
                            <div class="day-status" id="status-<?php echo esc_attr( $day ); ?>">
                                <span class="status-badge closed"><?php _e( 'Closed', 'sofir' ); ?></span>
                            </div>
                        </div>
                        <div class="day-hours">
                            <div class="time-inputs">
                                <input type="time" 
                                       name="<?php echo esc_attr( $field['name'] ); ?>[<?php echo esc_attr( $day ); ?>][open]" 
                                       value="<?php echo esc_attr( $hours[$day]['open'] ); ?>"
                                       class="time-input open-time" <?php echo $hours[$day]['closed'] ? 'disabled' : ''; ?> />
                                <span class="time-separator">-</span>
                                <input type="time" 
                                       name="<?php echo esc_attr( $field['name'] ); ?>[<?php echo esc_attr( $day ); ?>][close]" 
                                       value="<?php echo esc_attr( $hours[$day]['close'] ); ?>"
                                       class="time-input close-time" <?php echo $hours[$day]['closed'] ? 'disabled' : ''; ?> />
                            </div>
                            <div class="closed-checkbox">
                                <label>
                                    <input type="checkbox" 
                                           name="<?php echo esc_attr( $field['name'] ); ?>[<?php echo esc_attr( $day ); ?>][closed]" 
                                           value="1"
                                           <?php checked( $hours[$day]['closed'] ); ?> />
                                    <?php _e( 'Closed', 'sofir' ); ?>
                                </label>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="hours-preview">
                <div class="current-status">
                    <strong><?php _e( 'Current Status:', 'sofir' ); ?></strong>
                    <span id="current-business-status" class="status-badge"><?php _e( 'Checking...', 'sofir' ); ?></span>
                </div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    private function render_advanced_rating_field( array $field, $value ): string {
        $criteria = $field['criteria'] ?? [
            'quality' => __( 'Quality', 'sofir' ),
            'service' => __( 'Service', 'sofir' ),
            'value' => __( 'Value', 'sofir' ),
        ];

        $ratings = ! empty( $value ) ? $value : [];

        ob_start();
        ?>
        <div class="sofir-advanced-rating-field" data-field-name="<?php echo esc_attr( $field['name'] ); ?>">
            <div class="rating-criteria">
                <?php foreach ( $criteria as $key => $label ): ?>
                    <div class="rating-criterion" data-criterion="<?php echo esc_attr( $key ); ?>">
                        <label class="criterion-label"><?php echo esc_html( $label ); ?></label>
                        <div class="star-rating" data-rating="<?php echo esc_attr( $ratings[$key] ?? 0 ); ?>">
                            <?php for ( $i = 1; $i <= 5; $i++ ): ?>
                                <input type="radio" 
                                       name="<?php echo esc_attr( $field['name'] ); ?>[<?php echo esc_attr( $key ); ?>]" 
                                       value="<?php echo $i; ?>"
                                       <?php checked( $ratings[$key] ?? 0, $i ); ?>
                                       class="star-input" />
                                <span class="star" data-value="<?php echo $i; ?>">⭐</span>
                            <?php endfor; ?>
                        </div>
                        <span class="rating-value"><?php echo esc_html( $ratings[$key] ?? 0 ); ?>/5</span>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="rating-summary">
                <div class="overall-rating">
                    <span class="rating-label"><?php _e( 'Overall Rating:', 'sofir' ); ?></span>
                    <span class="rating-value" id="average-stars">0/5</span>
                </div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    private function render_price_range_field( array $field, $value ): string {
        $min = $value['min'] ?? $field['min'] ?? 0;
        $max = $value['max'] ?? $field['max'] ?? 1000;
        $currency = $field['currency'] ?? '$';

        ob_start();
        ?>
        <div class="sofir-price-range-field" data-field-name="<?php echo esc_attr( $field['name'] ); ?>">
            <div class="range-container">
                <div class="range-slider">
                    <div class="slider-track">
                        <div class="slider-range"></div>
                    </div>
                    <input type="range" 
                           name="<?php echo esc_attr( $field['name'] ); ?>[min]" 
                           value="<?php echo esc_attr( $min ); ?>"
                           min="<?php echo esc_attr( $field['min'] ?? 0 ); ?>"
                           max="<?php echo esc_attr( $field['max'] ?? 1000 ); ?>"
                           class="range-input min-input" />
                    <input type="range" 
                           name="<?php echo esc_attr( $field['name'] ); ?>[max]" 
                           value="<?php echo esc_attr( $max ); ?>"
                           min="<?php echo esc_attr( $field['min'] ?? 0 ); ?>"
                           max="<?php echo esc_attr( $field['max'] ?? 1000 ); ?>"
                           class="range-input max-input" />
                </div>
                <div class="range-inputs">
                    <div class="input-group">
                        <label><?php _e( 'Min Price:', 'sofir' ); ?></label>
                        <div class="input-with-currency">
                            <span class="currency"><?php echo esc_html( $currency ); ?></span>
                            <input type="number" 
                                   name="<?php echo esc_attr( $field['name'] ); ?>[min]" 
                                   value="<?php echo esc_attr( $min ); ?>"
                                   min="<?php echo esc_attr( $field['min'] ?? 0 ); ?>"
                                   max="<?php echo esc_attr( $field['max'] ?? 1000 ); ?>"
                                   class="number-input min-number" />
                        </div>
                    </div>
                    <div class="input-group">
                        <label><?php _e( 'Max Price:', 'sofir' ); ?></label>
                        <div class="input-with-currency">
                            <span class="currency"><?php echo esc_html( $currency ); ?></span>
                            <input type="number" 
                                   name="<?php echo esc_attr( $field['name'] ); ?>[max]" 
                                   value="<?php echo esc_attr( $max ); ?>"
                                   min="<?php echo esc_attr( $field['min'] ?? 0 ); ?>"
                                   max="<?php echo esc_attr( $field['max'] ?? 1000 ); ?>"
                                   class="number-input max-number" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="range-display">
                <span class="range-label"><?php _e( 'Price Range:', 'sofir' ); ?></span>
                <span class="range-value">
                    <?php echo esc_html( $currency . $min ); ?> - <?php echo esc_html( $currency . $max ); ?>
                </span>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    private function render_location_plus_field( array $field, $value ): string {
        $address = $value['address'] ?? '';
        $lat = $value['lat'] ?? '';
        $lng = $value['lng'] ?? '';

        ob_start();
        ?>
        <div class="sofir-location-plus-field" data-field-name="<?php echo esc_attr( $field['name'] ); ?>">
            <div class="location-inputs">
                <div class="address-input-group">
                    <label><?php _e( 'Address:', 'sofir' ); ?></label>
                    <input type="text" 
                           name="<?php echo esc_attr( $field['name'] ); ?>[address]" 
                           value="<?php echo esc_attr( $address ); ?>"
                           class="address-input"
                           placeholder="<?php esc_attr_e( 'Enter address...', 'sofir' ); ?>" />
                </div>
                <div class="coordinates-group">
                    <div class="coord-input-group">
                        <label><?php _e( 'Latitude:', 'sofir' ); ?></label>
                        <input type="number" 
                               name="<?php echo esc_attr( $field['name'] ); ?>[lat]" 
                               value="<?php echo esc_attr( $lat ); ?>"
                               step="any"
                               class="coord-input lat-input" />
                    </div>
                    <div class="coord-input-group">
                        <label><?php _e( 'Longitude:', 'sofir' ); ?></label>
                        <input type="number" 
                               name="<?php echo esc_attr( $field['name'] ); ?>[lng]" 
                               value="<?php echo esc_attr( $lng ); ?>"
                               step="any"
                               class="coord-input lng-input" />
                    </div>
                </div>
            </div>
            <div class="location-map">
                <div id="location-map-<?php echo esc_attr( $field['name'] ); ?>" class="map-container"></div>
                <div class="map-controls">
                    <button type="button" class="button locate-btn">
                        <?php _e( '📍 Get Current Location', 'sofir' ); ?>
                    </button>
                    <button type="button" class="button search-btn">
                        <?php _e( '🔍 Find on Map', 'sofir' ); ?>
                    </button>
                </div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    private function render_gallery_plus_field( array $field, $value ): string {
        $images = $value ?? [];
        $max_images = $field['max_images'] ?? 10;

        ob_start();
        ?>
        <div class="sofir-gallery-plus-field" data-field-name="<?php echo esc_attr( $field['name'] ); ?>" data-max-images="<?php echo esc_attr( $max_images ); ?>">
            <div class="gallery-upload">
                <button type="button" class="button upload-images-btn">
                    <?php _e( '📷 Upload Images', 'sofir' ); ?>
                </button>
                <span class="upload-hint">
                    <?php printf( __( 'Max %d images', 'sofir' ), $max_images ); ?>
                </span>
            </div>
            <div class="gallery-grid" id="gallery-<?php echo esc_attr( $field['name'] ); ?>">
                <?php if ( ! empty( $images ) ): ?>
                    <?php foreach ( $images as $index => $image ): ?>
                        <div class="gallery-item" data-index="<?php echo $index; ?>">
                            <div class="image-preview">
                                <img src="<?php echo esc_url( $image['url'] ); ?>" alt="Gallery image" />
                            </div>
                            <div class="image-overlay">
                                <button type="button" class="button edit-btn" data-index="<?php echo $index; ?>">
                                    <?php _e( '✏️', 'sofir' ); ?>
                                </button>
                                <button type="button" class="button remove-btn" data-index="<?php echo $index; ?>">
                                    <?php _e( '🗑️', 'sofir' ); ?>
                                </button>
                            </div>
                            <input type="hidden" 
                                   name="<?php echo esc_attr( $field['name'] ); ?>[<?php echo $index; ?>][url]" 
                                   value="<?php echo esc_url( $image['url'] ); ?>" />
                            <input type="hidden" 
                                   name="<?php echo esc_attr( $field['name'] ); ?>[<?php echo $index; ?>][id]" 
                                   value="<?php echo esc_attr( $image['id'] ?? '' ); ?>" />
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <div class="gallery-settings">
                <div class="setting-group">
                    <label>
                        <input type="checkbox" 
                               name="<?php echo esc_attr( $field['name'] ); ?>[lightbox]" 
                               value="1"
                               <?php checked( $field['lightbox'] ?? true ); ?> />
                        <?php _e( 'Enable lightbox', 'sofir' ); ?>
                    </label>
                </div>
                <div class="setting-group">
                    <label>
                        <input type="checkbox" 
                               name="<?php echo esc_attr( $field['name'] ); ?>[captions]" 
                               value="1"
                               <?php checked( $field['captions'] ?? false ); ?> />
                        <?php _e( 'Show captions', 'sofir' ); ?>
                    </label>
                </div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    public function save_advanced_field( array $field, $post_id, $value ): void {
        switch ( $field['type'] ) {
            case 'business_hours':
                $this->save_business_hours_field( $field, $post_id, $value );
                break;
            case 'advanced_rating':
                $this->save_advanced_rating_field( $field, $post_id, $value );
                break;
            case 'location_plus':
                $this->save_location_plus_field( $field, $post_id, $value );
                break;
        }
    }

    private function save_business_hours_field( array $field, int $post_id, array $value ): void {
        $sanitized = [];
        foreach ( $value as $day => $hours ) {
            $sanitized[$day] = [
                'open' => sanitize_text_field( $hours['open'] ),
                'close' => sanitize_text_field( $hours['close'] ),
                'closed' => (bool) ($hours['closed'] ?? false),
            ];
        }
        update_post_meta( $post_id, $field['name'], $sanitized );
    }

    private function save_advanced_rating_field( array $field, int $post_id, array $value ): void {
        $sanitized = [];
        $total = 0;
        $count = 0;

        foreach ( $value as $criterion => $rating ) {
            $rating = intval( $rating );
            $sanitized[$criterion] = $rating;
            $total += $rating;
            $count++;
        }

        update_post_meta( $post_id, $field['name'], $sanitized );

        // Calculate and save average rating
        $average = $count > 0 ? round( $total / $count, 1 ) : 0;
        update_post_meta( $post_id, '_sofir_average_rating', $average );
        update_post_meta( $post_id, '_sofir_rating_count', $count );
    }

    private function save_location_plus_field( array $field, int $post_id, array $value ): void {
        $sanitized = [
            'address' => sanitize_text_field( $value['address'] ?? '' ),
            'lat' => floatval( $value['lat'] ?? 0 ),
            'lng' => floatval( $value['lng'] ?? 0 ),
        ];
        update_post_meta( $post_id, $field['name'], $sanitized );
    }

    public function validate_advanced_field( array $field, $value, array $context ): bool {
        $is_valid = true;

        switch ( $field['type'] ) {
            case 'business_hours':
                return $this->validate_business_hours( $field, $value, $context );
            case 'advanced_rating':
                return $this->validate_advanced_rating( $field, $value, $context );
            case 'price_range':
                return $this->validate_price_range( $field, $value, $context );
            default:
                return $is_valid;
        }
    }

    private function validate_business_hours( array $field, array $value, array $context ): bool {
        foreach ( $value as $day => $hours ) {
            if ( ! $hours['closed'] ) {
                $time_pattern = '/^([0-1]?[0-9]|2[0-3]):[0-5][0-9]$/';
                if ( ! preg_match( $time_pattern, $hours['open'] ) || 
                     ! preg_match( $time_pattern, $hours['close'] ) ) {
                    return false;
                }
            }
        }
        return true;
    }

    private function validate_advanced_rating( array $field, array $value, array $context ): bool {
        foreach ( $value as $criterion => $rating ) {
            if ( ! is_numeric( $rating ) || $rating < 1 || $rating > 5 ) {
                return false;
            }
        }
        return true;
    }

    private function validate_price_range( array $field, array $value, array $context ): bool {
        $min = floatval( $value['min'] ?? 0 );
        $max = floatval( $value['max'] ?? 0 );

        return $max > $min;
    }

    public function ajax_get_business_hours_status(): void {
        check_ajax_referer( 'sofir_codewattz_nonce', 'nonce' );
        
        $post_id = intval( $_POST['post_id'] );
        $hours = get_post_meta( $post_id, 'sofir_business_hours', true );
        
        if ( ! $hours ) {
            wp_send_json_error( [ 'message' => __( 'No business hours found', 'sofir' ) ] );
        }
        
        $current_day = strtolower( date( 'l' ) );
        $current_time = date( 'H:i' );
        
        if ( ! isset( $hours[ $current_day ] ) || $hours[ $current_day ]['closed'] ) {
            wp_send_json_success( [
                'status' => 'closed',
                'message' => __( 'Currently closed', 'sofir' ),
                'next_open' => $this->get_next_open_time( $hours ),
            ] );
        }
        
        $open = $hours[ $current_day ]['open'];
        $close = $hours[ $current_day ]['close'];
        
        if ( $current_time >= $open && $current_time <= $close ) {
            wp_send_json_success( [
                'status' => 'open',
                'message' => __( 'Currently open', 'sofir' ),
                'closes_at' => $close,
            ] );
        } else {
            wp_send_json_success( [
                'status' => 'closed',
                'message' => __( 'Currently closed', 'sofir' ),
                'opens_at' => $open,
            ] );
        }
    }

    private function get_next_open_time( array $hours ): string {
        // Logic to find next open day and time
        return __( 'Tomorrow at 9:00 AM', 'sofir' );
    }

    public function ajax_submit_rating(): void {
        check_ajax_referer( 'sofir_codewattz_nonce', 'nonce' );
        
        $post_id = intval( $_POST['post_id'] );
        $ratings = $_POST['ratings'];
        
        if ( ! $this->validate_advanced_rating( [ 'type' => 'advanced_rating' ], $ratings, [] ) ) {
            wp_send_json_error( [ 'message' => __( 'Invalid ratings', 'sofir' ) ] );
        }
        
        // Save ratings
        $this->save_advanced_rating_field( [ 'name' => 'sofir_advanced_rating' ], $post_id, $ratings );
        
        // Calculate average
        $total = array_sum( $ratings );
        $count = count( $ratings );
        $average = round( $total / $count, 1 );
        
        wp_send_json_success( [
            'average' => $average,
            'count' => $count,
            'message' => __( 'Rating submitted successfully', 'sofir' ),
        ] );
    }
}