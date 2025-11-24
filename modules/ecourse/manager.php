<?php
namespace Sofir\Ecourse;

class Manager {
    private static ?Manager $instance = null;

    public static function instance(): Manager {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function boot(): void {
        \add_action( 'rest_api_init', [ $this, 'register_rest_routes' ] );
        \add_action( 'wp_ajax_sofir_enroll_course', [ $this, 'handle_enrollment' ] );
        \add_action( 'wp_ajax_nopriv_sofir_enroll_course', [ $this, 'handle_enrollment_nopriv' ] );
        \add_action( 'wp_ajax_sofir_complete_lesson', [ $this, 'handle_complete_lesson' ] );
        \add_shortcode( 'sofir_course_list', [ $this, 'render_course_list' ] );
        \add_shortcode( 'sofir_course_progress', [ $this, 'render_progress' ] );
        \add_shortcode( 'sofir_my_courses', [ $this, 'render_my_courses' ] );
        \add_action( 'wp_enqueue_scripts', [ $this, 'register_assets' ] );
    }

    public function register_rest_routes(): void {
        \register_rest_route(
            'sofir/v1',
            '/ecourse/courses',
            [
                'methods' => 'GET',
                'callback' => [ $this, 'rest_get_courses' ],
                'permission_callback' => '__return_true',
            ]
        );

        \register_rest_route(
            'sofir/v1',
            '/ecourse/courses/(?P<id>\d+)',
            [
                'methods' => 'GET',
                'callback' => [ $this, 'rest_get_course' ],
                'permission_callback' => '__return_true',
            ]
        );

        \register_rest_route(
            'sofir/v1',
            '/ecourse/enrollment',
            [
                'methods' => 'POST',
                'callback' => [ $this, 'rest_enroll' ],
                'permission_callback' => function () {
                    return \is_user_logged_in();
                },
            ]
        );

        \register_rest_route(
            'sofir/v1',
            '/ecourse/progress/(?P<user_id>\d+)/(?P<course_id>\d+)',
            [
                'methods' => 'GET',
                'callback' => [ $this, 'rest_get_progress' ],
                'permission_callback' => function ( $request ) {
                    $user_id = $request->get_param( 'user_id' );
                    return \current_user_can( 'edit_user', $user_id ) || \get_current_user_id() === (int) $user_id;
                },
            ]
        );

        \register_rest_route(
            'sofir/v1',
            '/ecourse/lesson/(?P<lesson_id>\d+)/complete',
            [
                'methods' => 'POST',
                'callback' => [ $this, 'rest_complete_lesson' ],
                'permission_callback' => function () {
                    return \is_user_logged_in();
                },
            ]
        );
    }

    public function register_assets(): void {
        \wp_register_script(
            'sofir-ecourse',
            SOFIR_ASSETS_URL . 'js/ecourse.js',
            [ 'wp-api-fetch' ],
            SOFIR_VERSION,
            true
        );

        \wp_localize_script(
            'sofir-ecourse',
            'SOFIR_ECOURSE_DATA',
            [
                'restRoot' => \esc_url_raw( \rest_url() ),
                'nonce' => \wp_create_nonce( 'wp_rest' ),
                'userId' => \get_current_user_id(),
            ]
        );

        \wp_register_style(
            'sofir-ecourse',
            SOFIR_ASSETS_URL . 'css/ecourse.css',
            [],
            SOFIR_VERSION
        );
    }

    public function handle_enrollment_nopriv(): void {
        \wp_send_json_error( \__( 'You must be logged in to enroll in a course.', 'sofir' ) );
    }

    public function handle_enrollment(): void {
        $course_id = isset( $_POST['course_id'] ) ? \absint( $_POST['course_id'] ) : 0;

        if ( ! $course_id ) {
            \wp_send_json_error( \__( 'Invalid course.', 'sofir' ) );
            return;
        }

        $user_id = \get_current_user_id();

        if ( $this->is_enrolled( $user_id, $course_id ) ) {
            \wp_send_json_error( \__( 'You are already enrolled in this course.', 'sofir' ) );
            return;
        }

        $enrollments = \get_user_meta( $user_id, 'sofir_course_enrollments', true );
        if ( ! is_array( $enrollments ) ) {
            $enrollments = [];
        }

        $enrollments[] = [
            'course_id' => $course_id,
            'enrolled_at' => \current_time( 'mysql' ),
            'status' => 'active',
        ];

        \update_user_meta( $user_id, 'sofir_course_enrollments', $enrollments );

        \do_action( 'sofir/ecourse/enrolled', $user_id, $course_id );

        \wp_send_json_success( [
            'message' => \__( 'Successfully enrolled in course!', 'sofir' ),
            'course_id' => $course_id,
        ] );
    }

    public function handle_complete_lesson(): void {
        $lesson_id = isset( $_POST['lesson_id'] ) ? \absint( $_POST['lesson_id'] ) : 0;
        $course_id = isset( $_POST['course_id'] ) ? \absint( $_POST['course_id'] ) : 0;

        if ( ! $lesson_id || ! $course_id ) {
            \wp_send_json_error( \__( 'Invalid lesson or course.', 'sofir' ) );
            return;
        }

        $user_id = \get_current_user_id();

        if ( ! $this->is_enrolled( $user_id, $course_id ) ) {
            \wp_send_json_error( \__( 'You are not enrolled in this course.', 'sofir' ) );
            return;
        }

        $completed = \get_user_meta( $user_id, "sofir_course_{$course_id}_completed_lessons", true );
        if ( ! is_array( $completed ) ) {
            $completed = [];
        }

        if ( ! in_array( $lesson_id, $completed, true ) ) {
            $completed[] = $lesson_id;
            \update_user_meta( $user_id, "sofir_course_{$course_id}_completed_lessons", $completed );

            \do_action( 'sofir/ecourse/lesson_completed', $user_id, $course_id, $lesson_id );
        }

        $progress = $this->calculate_progress( $user_id, $course_id );

        \wp_send_json_success( [
            'message' => \__( 'Lesson marked as complete!', 'sofir' ),
            'progress' => $progress,
        ] );
    }

    public function rest_get_courses(): \WP_REST_Response {
        $args = [
            'post_type' => 'course',
            'posts_per_page' => -1,
            'orderby' => 'date',
            'order' => 'DESC',
        ];

        $query = new \WP_Query( $args );
        $courses = [];

        foreach ( $query->posts as $post ) {
            $courses[] = [
                'id' => $post->ID,
                'title' => $post->post_title,
                'excerpt' => $post->post_excerpt,
                'description' => $post->post_content,
                'thumbnail' => \get_the_post_thumbnail_url( $post->ID, 'medium' ),
                'instructor' => \get_post_meta( $post->ID, 'sofir_course_instructor', true ),
                'duration' => \get_post_meta( $post->ID, 'sofir_course_duration', true ),
                'level' => \get_post_meta( $post->ID, 'sofir_course_level', true ),
                'price' => (float) \get_post_meta( $post->ID, 'sofir_course_price', true ),
                'enrolled_count' => (int) \get_post_meta( $post->ID, 'sofir_course_enrolled_count', true ),
                'rating' => (float) \get_post_meta( $post->ID, 'sofir_course_rating', true ),
            ];
        }

        return \rest_ensure_response( $courses );
    }

    public function rest_get_course( \WP_REST_Request $request ): \WP_REST_Response {
        $course_id = (int) $request->get_param( 'id' );
        $post = \get_post( $course_id );

        if ( ! $post || $post->post_type !== 'course' ) {
            return new \WP_REST_Response( [ 'message' => \__( 'Course not found', 'sofir' ) ], 404 );
        }

        $lessons = $this->get_course_lessons( $course_id );
        $user_id = \get_current_user_id();
        $is_enrolled = $user_id ? $this->is_enrolled( $user_id, $course_id ) : false;
        $progress = $is_enrolled ? $this->calculate_progress( $user_id, $course_id ) : 0;

        return \rest_ensure_response( [
            'id' => $post->ID,
            'title' => $post->post_title,
            'description' => $post->post_content,
            'thumbnail' => \get_the_post_thumbnail_url( $post->ID, 'large' ),
            'instructor' => \get_post_meta( $post->ID, 'sofir_course_instructor', true ),
            'duration' => \get_post_meta( $post->ID, 'sofir_course_duration', true ),
            'level' => \get_post_meta( $post->ID, 'sofir_course_level', true ),
            'price' => (float) \get_post_meta( $post->ID, 'sofir_course_price', true ),
            'lessons' => $lessons,
            'is_enrolled' => $is_enrolled,
            'progress' => $progress,
        ] );
    }

    public function rest_enroll( \WP_REST_Request $request ): \WP_REST_Response {
        $course_id = (int) $request->get_param( 'course_id' );
        $user_id = \get_current_user_id();

        if ( $this->is_enrolled( $user_id, $course_id ) ) {
            return new \WP_REST_Response( [ 'message' => \__( 'Already enrolled', 'sofir' ) ], 400 );
        }

        $enrollments = \get_user_meta( $user_id, 'sofir_course_enrollments', true );
        if ( ! is_array( $enrollments ) ) {
            $enrollments = [];
        }

        $enrollments[] = [
            'course_id' => $course_id,
            'enrolled_at' => \current_time( 'mysql' ),
            'status' => 'active',
        ];

        \update_user_meta( $user_id, 'sofir_course_enrollments', $enrollments );

        $enrolled_count = (int) \get_post_meta( $course_id, 'sofir_course_enrolled_count', true );
        \update_post_meta( $course_id, 'sofir_course_enrolled_count', $enrolled_count + 1 );

        \do_action( 'sofir/ecourse/enrolled', $user_id, $course_id );

        return \rest_ensure_response( [
            'status' => 'success',
            'message' => \__( 'Successfully enrolled', 'sofir' ),
        ] );
    }

    public function rest_get_progress( \WP_REST_Request $request ): \WP_REST_Response {
        $user_id = (int) $request->get_param( 'user_id' );
        $course_id = (int) $request->get_param( 'course_id' );

        $progress = $this->calculate_progress( $user_id, $course_id );
        $completed_lessons = \get_user_meta( $user_id, "sofir_course_{$course_id}_completed_lessons", true );

        return \rest_ensure_response( [
            'progress' => $progress,
            'completed_lessons' => is_array( $completed_lessons ) ? $completed_lessons : [],
        ] );
    }

    public function rest_complete_lesson( \WP_REST_Request $request ): \WP_REST_Response {
        $lesson_id = (int) $request->get_param( 'lesson_id' );
        $course_id = (int) \get_post_meta( $lesson_id, 'sofir_lesson_course_id', true );
        $user_id = \get_current_user_id();

        if ( ! $course_id ) {
            return new \WP_REST_Response( [ 'message' => \__( 'Invalid lesson', 'sofir' ) ], 400 );
        }

        if ( ! $this->is_enrolled( $user_id, $course_id ) ) {
            return new \WP_REST_Response( [ 'message' => \__( 'Not enrolled', 'sofir' ) ], 403 );
        }

        $completed = \get_user_meta( $user_id, "sofir_course_{$course_id}_completed_lessons", true );
        if ( ! is_array( $completed ) ) {
            $completed = [];
        }

        if ( ! in_array( $lesson_id, $completed, true ) ) {
            $completed[] = $lesson_id;
            \update_user_meta( $user_id, "sofir_course_{$course_id}_completed_lessons", $completed );

            \do_action( 'sofir/ecourse/lesson_completed', $user_id, $course_id, $lesson_id );
        }

        return \rest_ensure_response( [
            'status' => 'success',
            'progress' => $this->calculate_progress( $user_id, $course_id ),
        ] );
    }

    public function render_course_list( array $atts ): string {
        $atts = \shortcode_atts(
            [
                'columns' => 3,
                'count' => 12,
            ],
            $atts,
            'sofir_course_list'
        );

        \wp_enqueue_style( 'sofir-ecourse' );

        $args = [
            'post_type' => 'course',
            'posts_per_page' => \absint( $atts['count'] ),
            'orderby' => 'date',
            'order' => 'DESC',
        ];

        $query = new \WP_Query( $args );

        if ( ! $query->have_posts() ) {
            return '<p>' . \esc_html__( 'No courses available.', 'sofir' ) . '</p>';
        }

        ob_start();
        echo '<div class="sofir-course-list" style="display: grid; grid-template-columns: repeat(' . \absint( $atts['columns'] ) . ', 1fr); gap: 30px;">';

        while ( $query->have_posts() ) {
            $query->the_post();
            $price = (float) \get_post_meta( \get_the_ID(), 'sofir_course_price', true );
            $level = \get_post_meta( \get_the_ID(), 'sofir_course_level', true );
            $duration = \get_post_meta( \get_the_ID(), 'sofir_course_duration', true );

            echo '<div class="sofir-course-card" style="border: 1px solid #ddd; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">';
            
            if ( \has_post_thumbnail() ) {
                echo '<div class="course-thumbnail">';
                \the_post_thumbnail( 'medium_large', [ 'style' => 'width: 100%; height: 200px; object-fit: cover;' ] );
                echo '</div>';
            }

            echo '<div class="course-content" style="padding: 20px;">';
            
            if ( $level ) {
                echo '<span class="course-level" style="display: inline-block; padding: 4px 12px; background: #0073aa; color: #fff; border-radius: 12px; font-size: 12px; margin-bottom: 10px;">' . \esc_html( $level ) . '</span>';
            }

            echo '<h3 style="margin: 10px 0;"><a href="' . \esc_url( \get_permalink() ) . '">' . \esc_html( \get_the_title() ) . '</a></h3>';
            
            if ( \get_the_excerpt() ) {
                echo '<p style="color: #666; margin: 10px 0;">' . \esc_html( \get_the_excerpt() ) . '</p>';
            }

            if ( $duration ) {
                echo '<div style="color: #666; font-size: 14px; margin: 10px 0;">⏱ ' . \esc_html( $duration ) . '</div>';
            }

            echo '<div style="display: flex; justify-content: space-between; align-items: center; margin-top: 20px; padding-top: 20px; border-top: 1px solid #ddd;">';
            
            if ( $price > 0 ) {
                echo '<div class="course-price" style="font-size: 20px; font-weight: bold; color: #0073aa;">';
                echo 'Rp ' . \number_format_i18n( $price, 0 );
                echo '</div>';
            } else {
                echo '<div class="course-price" style="font-size: 18px; font-weight: bold; color: #00a32a;">' . \esc_html__( 'Free', 'sofir' ) . '</div>';
            }

            echo '<a href="' . \esc_url( \get_permalink() ) . '" class="button button-primary">' . \esc_html__( 'View Course', 'sofir' ) . '</a>';
            echo '</div>';

            echo '</div>';
            echo '</div>';
        }

        echo '</div>';
        \wp_reset_postdata();

        return (string) ob_get_clean();
    }

    public function render_progress( array $atts ): string {
        if ( ! \is_user_logged_in() ) {
            return '<p>' . \esc_html__( 'Please log in to view your progress.', 'sofir' ) . '</p>';
        }

        $atts = \shortcode_atts(
            [
                'course_id' => 0,
            ],
            $atts,
            'sofir_course_progress'
        );

        $course_id = \absint( $atts['course_id'] );
        if ( ! $course_id ) {
            $course_id = \get_the_ID();
        }

        $user_id = \get_current_user_id();
        $progress = $this->calculate_progress( $user_id, $course_id );

        ob_start();
        echo '<div class="sofir-course-progress" style="padding: 20px; background: #f0f0f1; border-radius: 8px;">';
        echo '<h4 style="margin: 0 0 15px 0;">' . \esc_html__( 'Your Progress', 'sofir' ) . '</h4>';
        echo '<div style="background: #fff; height: 30px; border-radius: 15px; overflow: hidden; margin-bottom: 10px;">';
        echo '<div style="height: 100%; background: linear-gradient(90deg, #0073aa, #00a32a); width: ' . \esc_attr( $progress ) . '%; transition: width 0.3s;"></div>';
        echo '</div>';
        echo '<div style="text-align: center; font-size: 18px; font-weight: bold; color: #0073aa;">';
        echo \esc_html( number_format( $progress, 1 ) ) . '%';
        echo '</div>';
        echo '</div>';

        return (string) ob_get_clean();
    }

    public function render_my_courses(): string {
        if ( ! \is_user_logged_in() ) {
            return '<p>' . \esc_html__( 'Please log in to view your courses.', 'sofir' ) . '</p>';
        }

        \wp_enqueue_style( 'sofir-ecourse' );

        $user_id = \get_current_user_id();
        $enrollments = \get_user_meta( $user_id, 'sofir_course_enrollments', true );

        if ( ! is_array( $enrollments ) || empty( $enrollments ) ) {
            return '<p>' . \esc_html__( 'You are not enrolled in any courses yet.', 'sofir' ) . '</p>';
        }

        ob_start();
        echo '<div class="sofir-my-courses" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px;">';

        foreach ( $enrollments as $enrollment ) {
            $course_id = $enrollment['course_id'];
            $course = \get_post( $course_id );

            if ( ! $course ) {
                continue;
            }

            $progress = $this->calculate_progress( $user_id, $course_id );

            echo '<div class="my-course-card" style="border: 1px solid #ddd; border-radius: 8px; padding: 20px;">';
            echo '<h3 style="margin: 0 0 10px 0;"><a href="' . \esc_url( \get_permalink( $course_id ) ) . '">' . \esc_html( $course->post_title ) . '</a></h3>';
            
            echo '<div style="background: #f0f0f1; height: 20px; border-radius: 10px; overflow: hidden; margin: 15px 0;">';
            echo '<div style="height: 100%; background: #0073aa; width: ' . \esc_attr( $progress ) . '%;"></div>';
            echo '</div>';
            
            echo '<div style="display: flex; justify-content: space-between; align-items: center;">';
            echo '<div style="color: #666;">' . \esc_html( number_format( $progress, 1 ) ) . '% ' . \esc_html__( 'Complete', 'sofir' ) . '</div>';
            echo '<a href="' . \esc_url( \get_permalink( $course_id ) ) . '" class="button">' . \esc_html__( 'Continue', 'sofir' ) . '</a>';
            echo '</div>';
            
            echo '</div>';
        }

        echo '</div>';

        return (string) ob_get_clean();
    }

    private function is_enrolled( int $user_id, int $course_id ): bool {
        $enrollments = \get_user_meta( $user_id, 'sofir_course_enrollments', true );
        
        if ( ! is_array( $enrollments ) ) {
            return false;
        }

        foreach ( $enrollments as $enrollment ) {
            if ( isset( $enrollment['course_id'] ) && $enrollment['course_id'] === $course_id ) {
                return true;
            }
        }

        return false;
    }

    private function get_course_lessons( int $course_id ): array {
        $args = [
            'post_type' => 'lesson',
            'posts_per_page' => -1,
            'orderby' => 'menu_order',
            'order' => 'ASC',
            'meta_query' => [
                [
                    'key' => 'sofir_lesson_course_id',
                    'value' => $course_id,
                ],
            ],
        ];

        $query = new \WP_Query( $args );
        $lessons = [];

        foreach ( $query->posts as $post ) {
            $lessons[] = [
                'id' => $post->ID,
                'title' => $post->post_title,
                'content' => $post->post_content,
                'duration' => \get_post_meta( $post->ID, 'sofir_lesson_duration', true ),
                'order' => (int) $post->menu_order,
            ];
        }

        return $lessons;
    }

    private function calculate_progress( int $user_id, int $course_id ): float {
        $lessons = $this->get_course_lessons( $course_id );
        $total_lessons = count( $lessons );

        if ( $total_lessons === 0 ) {
            return 0.0;
        }

        $completed = \get_user_meta( $user_id, "sofir_course_{$course_id}_completed_lessons", true );
        if ( ! is_array( $completed ) ) {
            $completed = [];
        }

        $completed_count = count( $completed );

        return ( $completed_count / $total_lessons ) * 100;
    }
}
