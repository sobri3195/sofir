<?php
namespace Sofir\Blocks;

use Sofir\Directory\Manager as DirectoryManager;
use Sofir\Enhancement\Auth as AuthEnhancer;
use Sofir\Membership\Manager as MembershipManager;
use Sofir\Payments\Manager as PaymentsManager;
use Sofir\Cpt\Manager as CptManager;

class Elements {
    private static ?Elements $instance = null;

    public static function instance(): Elements {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function boot(): void {
        \add_action( 'init', [ $this, 'register_all_blocks' ] );
    }

    public function register_all_blocks(): void {
        if ( ! \function_exists( 'register_block_type' ) ) {
            return;
        }

        $this->register_action_block();
        $this->register_cart_summary_block();
        $this->register_countdown_block();
        $this->register_create_post_block();
        $this->register_dashboard_block();
        $this->register_gallery_block();
        $this->register_login_register_block();
        $this->register_map_block();
        $this->register_messages_block();
        $this->register_navbar_block();
        $this->register_order_block();
        $this->register_popup_kit_block();
        $this->register_post_feed_block();
        $this->register_print_template_block();
        $this->register_product_form_block();
        $this->register_product_price_block();
        $this->register_quick_search_block();
        $this->register_review_stats_block();
        $this->register_ring_chart_block();
        $this->register_sales_chart_block();
        $this->register_search_form_block();
        $this->register_slider_block();
        $this->register_term_feed_block();
        $this->register_timeline_block();
        $this->register_timeline_style_kit_block();
        $this->register_user_bar_block();
        $this->register_visit_chart_block();
        $this->register_work_hours_block();
        $this->register_testimonial_slider_block();
        $this->register_pricing_table_block();
        $this->register_team_grid_block();
        $this->register_faq_accordion_block();
        $this->register_cta_banner_block();
        $this->register_feature_box_block();
        $this->register_contact_form_block();
        $this->register_social_share_block();
        $this->register_breadcrumb_block();
        $this->register_progress_bar_block();
        $this->register_appointment_booking_block();
    }

    private function register_action_block(): void {
        \register_block_type(
            'sofir/action',
            [
                'api_version'     => 2,
                'category'        => 'sofir',
                'attributes'      => [
                    'actionType' => [ 'type' => 'string', 'default' => 'button' ],
                    'actionLabel' => [ 'type' => 'string', 'default' => 'Click Me' ],
                    'actionUrl' => [ 'type' => 'string', 'default' => '' ],
                    'actionClass' => [ 'type' => 'string', 'default' => '' ],
                ],
                'render_callback' => function ( array $attributes ): string {
                    $label = $attributes['actionLabel'] ?? 'Click Me';
                    $url = $attributes['actionUrl'] ?? '#';
                    $class = 'sofir-action-button ' . ( $attributes['actionClass'] ?? '' );
                    
                    return sprintf(
                        '<div class="sofir-action-block"><a href="%s" class="%s">%s</a></div>',
                        \esc_url( $url ),
                        \esc_attr( $class ),
                        \esc_html( $label )
                    );
                },
            ]
        );
    }

    private function register_cart_summary_block(): void {
        \register_block_type(
            'sofir/cart-summary',
            [
                'render_callback' => function (): string {
                    if ( ! class_exists( 'PaymentsManager' ) ) {
                        return '';
                    }
                    
                    ob_start();
                    echo '<div class="sofir-cart-summary">';
                    echo '<h3>' . \esc_html__( 'Cart Summary', 'sofir' ) . '</h3>';
                    echo '<div class="sofir-cart-items" id="sofir-cart-items"></div>';
                    echo '<div class="sofir-cart-total"><strong>' . \esc_html__( 'Total:', 'sofir' ) . '</strong> <span id="sofir-cart-total-amount">0</span></div>';
                    echo '</div>';
                    
                    return (string) ob_get_clean();
                },
            ]
        );
    }

    private function register_countdown_block(): void {
        \register_block_type(
            'sofir/countdown',
            [
                'attributes'      => [
                    'targetDate' => [ 'type' => 'string', 'default' => '' ],
                    'format' => [ 'type' => 'string', 'default' => 'dhms' ],
                ],
                'render_callback' => function ( array $attributes ): string {
                    $target = $attributes['targetDate'] ?? '';
                    $format = $attributes['format'] ?? 'dhms';
                    
                    \wp_enqueue_script( 'sofir-countdown' );
                    
                    return sprintf(
                        '<div class="sofir-countdown" data-target="%s" data-format="%s">
                            <span class="days">00</span>:<span class="hours">00</span>:<span class="minutes">00</span>:<span class="seconds">00</span>
                        </div>',
                        \esc_attr( $target ),
                        \esc_attr( $format )
                    );
                },
            ]
        );
    }

    private function register_create_post_block(): void {
        \register_block_type(
            'sofir/create-post',
            [
                'attributes'      => [
                    'postType' => [ 'type' => 'string', 'default' => 'post' ],
                    'buttonLabel' => [ 'type' => 'string', 'default' => 'Create Post' ],
                ],
                'render_callback' => function ( array $attributes ): string {
                    if ( ! \is_user_logged_in() ) {
                        return '<p>' . \esc_html__( 'You must be logged in to create a post.', 'sofir' ) . '</p>';
                    }
                    
                    $post_type = $attributes['postType'] ?? 'post';
                    $label = $attributes['buttonLabel'] ?? 'Create Post';
                    
                    \wp_enqueue_script( 'sofir-create-post' );
                    
                    ob_start();
                    echo '<div class="sofir-create-post">';
                    echo '<form class="sofir-post-form" data-post-type="' . \esc_attr( $post_type ) . '">';
                    echo '<input type="text" name="post_title" placeholder="' . \esc_attr__( 'Title', 'sofir' ) . '" required />';
                    echo '<textarea name="post_content" placeholder="' . \esc_attr__( 'Content', 'sofir' ) . '" rows="5"></textarea>';
                    echo '<button type="submit" class="button button-primary">' . \esc_html( $label ) . '</button>';
                    echo '</form>';
                    echo '</div>';
                    
                    return (string) ob_get_clean();
                },
            ]
        );
    }

    private function register_dashboard_block(): void {
        \register_block_type(
            'sofir/dashboard',
            [
                'attributes'      => [
                    'title' => [ 'type' => 'string', 'default' => 'Dashboard' ],
                    'showStats' => [ 'type' => 'boolean', 'default' => true ],
                    'showRecent' => [ 'type' => 'boolean', 'default' => true ],
                ],
                'render_callback' => function ( array $attributes ): string {
                    if ( ! \is_user_logged_in() ) {
                        return '<p>' . \esc_html__( 'You must be logged in to view the dashboard.', 'sofir' ) . '</p>';
                    }
                    
                    $user = \wp_get_current_user();
                    $title = $attributes['title'] ?? 'Dashboard';
                    $show_stats = $attributes['showStats'] ?? true;
                    $show_recent = $attributes['showRecent'] ?? true;
                    
                    ob_start();
                    echo '<div class="sofir-dashboard">';
                    echo '<h2>' . \esc_html( $title ) . '</h2>';
                    
                    echo '<div class="sofir-dashboard-welcome">';
                    echo '<p>' . \sprintf( \esc_html__( 'Welcome back, %s!', 'sofir' ), '<strong>' . \esc_html( $user->display_name ) . '</strong>' ) . '</p>';
                    echo '</div>';
                    
                    if ( $show_stats ) {
                        $post_count = \count_user_posts( $user->ID );
                        $comment_count = \get_comments( [ 'user_id' => $user->ID, 'count' => true ] );
                        
                        echo '<div class="sofir-dashboard-stats">';
                        echo '<div class="sofir-stat-card">';
                        echo '<span class="sofir-stat-value">' . \esc_html( $post_count ) . '</span>';
                        echo '<span class="sofir-stat-label">' . \esc_html__( 'Posts', 'sofir' ) . '</span>';
                        echo '</div>';
                        echo '<div class="sofir-stat-card">';
                        echo '<span class="sofir-stat-value">' . \esc_html( $comment_count ) . '</span>';
                        echo '<span class="sofir-stat-label">' . \esc_html__( 'Comments', 'sofir' ) . '</span>';
                        echo '</div>';
                        echo '<div class="sofir-stat-card">';
                        echo '<span class="sofir-stat-value">' . \esc_html( \gmdate( 'M Y', \strtotime( $user->user_registered ) ) ) . '</span>';
                        echo '<span class="sofir-stat-label">' . \esc_html__( 'Member Since', 'sofir' ) . '</span>';
                        echo '</div>';
                        echo '</div>';
                    }
                    
                    if ( $show_recent ) {
                        $recent_posts = \get_posts( [
                            'author' => $user->ID,
                            'numberposts' => 5,
                            'post_status' => 'publish',
                        ] );
                        
                        if ( ! empty( $recent_posts ) ) {
                            echo '<div class="sofir-dashboard-recent">';
                            echo '<h3>' . \esc_html__( 'Recent Posts', 'sofir' ) . '</h3>';
                            echo '<ul class="sofir-post-list">';
                            foreach ( $recent_posts as $post ) {
                                echo '<li>';
                                echo '<a href="' . \esc_url( \get_permalink( $post ) ) . '">' . \esc_html( $post->post_title ) . '</a>';
                                echo '<span class="sofir-post-date">' . \esc_html( \get_the_date( '', $post ) ) . '</span>';
                                echo '</li>';
                            }
                            echo '</ul>';
                            echo '</div>';
                        }
                    }
                    
                    echo '<div class="sofir-dashboard-actions">';
                    echo '<a href="' . \esc_url( \admin_url( 'profile.php' ) ) . '" class="button">' . \esc_html__( 'Edit Profile', 'sofir' ) . '</a>';
                    echo '<a href="' . \esc_url( \admin_url( 'post-new.php' ) ) . '" class="button button-primary">' . \esc_html__( 'Create Post', 'sofir' ) . '</a>';
                    echo '</div>';
                    
                    echo '</div>';
                    
                    return (string) ob_get_clean();
                },
            ]
        );
    }

    private function register_gallery_block(): void {
        \register_block_type(
            'sofir/gallery',
            [
                'attributes'      => [
                    'imageIds' => [ 'type' => 'array', 'default' => [] ],
                    'columns' => [ 'type' => 'number', 'default' => 3 ],
                ],
                'render_callback' => function ( array $attributes ): string {
                    $ids = $attributes['imageIds'] ?? [];
                    $columns = $attributes['columns'] ?? 3;
                    
                    if ( empty( $ids ) ) {
                        return '';
                    }
                    
                    ob_start();
                    echo '<div class="sofir-gallery sofir-gallery-columns-' . \esc_attr( $columns ) . '">';
                    foreach ( $ids as $id ) {
                        $img = \wp_get_attachment_image( $id, 'medium', false, [ 'class' => 'sofir-gallery-image' ] );
                        if ( $img ) {
                            echo '<div class="sofir-gallery-item">' . $img . '</div>';
                        }
                    }
                    echo '</div>';
                    
                    return (string) ob_get_clean();
                },
            ]
        );
    }

    private function register_login_register_block(): void {
        \register_block_type(
            'sofir/login-register',
            [
                'attributes'      => [
                    'showRegister' => [ 'type' => 'boolean', 'default' => true ],
                    'redirectUrl' => [ 'type' => 'string', 'default' => '' ],
                ],
                'render_callback' => function ( array $attributes ): string {
                    if ( \is_user_logged_in() ) {
                        return '<p>' . \esc_html__( 'You are already logged in.', 'sofir' ) . '</p>';
                    }
                    
                    $show_register = $attributes['showRegister'] ?? true;
                    $redirect = $attributes['redirectUrl'] ?? '';
                    
                    ob_start();
                    echo '<div class="sofir-login-register">';
                    echo '<div class="sofir-auth-tabs">';
                    echo '<button class="sofir-tab-btn active" data-tab="login">' . \esc_html__( 'Login', 'sofir' ) . '</button>';
                    if ( $show_register ) {
                        echo '<button class="sofir-tab-btn" data-tab="register">' . \esc_html__( 'Register', 'sofir' ) . '</button>';
                    }
                    echo '</div>';
                    
                    echo '<div class="sofir-tab-content active" data-content="login">';
                    echo \wp_login_form( [ 'echo' => false, 'redirect' => $redirect ] );
                    echo '</div>';
                    
                    if ( $show_register ) {
                        echo '<div class="sofir-tab-content" data-content="register">';
                        echo '<form class="sofir-register-form">';
                        echo '<input type="text" name="username" placeholder="' . \esc_attr__( 'Username', 'sofir' ) . '" required />';
                        echo '<input type="email" name="email" placeholder="' . \esc_attr__( 'Email', 'sofir' ) . '" required />';
                        echo '<input type="tel" name="phone" placeholder="' . \esc_attr__( 'Phone Number', 'sofir' ) . '" />';
                        echo '<input type="password" name="password" placeholder="' . \esc_attr__( 'Password', 'sofir' ) . '" required />';
                        echo '<button type="submit" class="button button-primary">' . \esc_html__( 'Register', 'sofir' ) . '</button>';
                        echo '</form>';
                        echo '</div>';
                    }
                    
                    echo '</div>';
                    
                    return (string) ob_get_clean();
                },
            ]
        );
    }

    private function register_map_block(): void {
        \register_block_type(
            'sofir/map',
            [
                'attributes'      => [
                    'postType' => [ 'type' => 'string', 'default' => 'listing' ],
                    'zoom' => [ 'type' => 'number', 'default' => 12 ],
                    'height' => [ 'type' => 'string', 'default' => '400px' ],
                ],
                'render_callback' => function ( array $attributes ): string {
                    return DirectoryManager::instance()->render_map_shortcode( [
                        'post_type' => $attributes['postType'] ?? 'listing',
                        'zoom' => $attributes['zoom'] ?? 12,
                    ] );
                },
            ]
        );
    }

    private function register_messages_block(): void {
        \register_block_type(
            'sofir/messages',
            [
                'render_callback' => function (): string {
                    if ( ! \is_user_logged_in() ) {
                        return '<p>' . \esc_html__( 'Please login to view messages.', 'sofir' ) . '</p>';
                    }
                    
                    \wp_enqueue_script( 'sofir-messages' );
                    
                    ob_start();
                    echo '<div class="sofir-messages">';
                    echo '<div class="sofir-messages-list"></div>';
                    echo '<div class="sofir-message-compose">';
                    echo '<textarea placeholder="' . \esc_attr__( 'Type your message...', 'sofir' ) . '"></textarea>';
                    echo '<button class="button button-primary">' . \esc_html__( 'Send', 'sofir' ) . '</button>';
                    echo '</div>';
                    echo '</div>';
                    
                    return (string) ob_get_clean();
                },
            ]
        );
    }

    private function register_navbar_block(): void {
        \register_block_type(
            'sofir/navbar',
            [
                'attributes'      => [
                    'menuId' => [ 'type' => 'number', 'default' => 0 ],
                    'mobileBreakpoint' => [ 'type' => 'number', 'default' => 768 ],
                ],
                'render_callback' => function ( array $attributes ): string {
                    $menu_id = $attributes['menuId'] ?? 0;
                    
                    \wp_enqueue_script( 'sofir-navbar' );
                    
                    ob_start();
                    echo '<nav class="sofir-navbar">';
                    echo '<div class="sofir-navbar-container">';
                    echo '<button class="sofir-mobile-toggle" aria-label="' . \esc_attr__( 'Toggle menu', 'sofir' ) . '">';
                    echo '<span></span><span></span><span></span>';
                    echo '</button>';
                    
                    if ( $menu_id ) {
                        \wp_nav_menu( [
                            'menu' => $menu_id,
                            'container_class' => 'sofir-navbar-menu',
                            'fallback_cb' => false,
                        ] );
                    } else {
                        echo '<div class="sofir-navbar-menu"></div>';
                    }
                    
                    echo '</div>';
                    echo '</nav>';
                    
                    return (string) ob_get_clean();
                },
            ]
        );
    }

    private function register_order_block(): void {
        \register_block_type(
            'sofir/order',
            [
                'attributes'      => [
                    'orderId' => [ 'type' => 'number', 'default' => 0 ],
                ],
                'render_callback' => function ( array $attributes ): string {
                    if ( ! \is_user_logged_in() ) {
                        return '<p>' . \esc_html__( 'Please login to view orders.', 'sofir' ) . '</p>';
                    }
                    
                    $order_id = $attributes['orderId'] ?? 0;
                    
                    ob_start();
                    echo '<div class="sofir-order">';
                    echo '<h3>' . \esc_html__( 'Order Details', 'sofir' ) . '</h3>';
                    echo '<div class="sofir-order-items"></div>';
                    echo '</div>';
                    
                    return (string) ob_get_clean();
                },
            ]
        );
    }

    private function register_popup_kit_block(): void {
        \register_block_type(
            'sofir/popup-kit',
            [
                'attributes'      => [
                    'triggerText' => [ 'type' => 'string', 'default' => 'Open Popup' ],
                    'popupTitle' => [ 'type' => 'string', 'default' => '' ],
                    'popupContent' => [ 'type' => 'string', 'default' => '' ],
                ],
                'render_callback' => function ( array $attributes ): string {
                    $trigger = $attributes['triggerText'] ?? 'Open Popup';
                    $title = $attributes['popupTitle'] ?? '';
                    $content = $attributes['popupContent'] ?? '';
                    
                    \wp_enqueue_script( 'sofir-popup' );
                    
                    $popup_id = 'sofir-popup-' . \wp_rand();
                    
                    ob_start();
                    echo '<div class="sofir-popup-kit">';
                    echo '<button class="sofir-popup-trigger" data-popup="' . \esc_attr( $popup_id ) . '">' . \esc_html( $trigger ) . '</button>';
                    echo '<div id="' . \esc_attr( $popup_id ) . '" class="sofir-popup-modal" style="display:none;">';
                    echo '<div class="sofir-popup-content">';
                    echo '<button class="sofir-popup-close">&times;</button>';
                    if ( $title ) {
                        echo '<h3>' . \esc_html( $title ) . '</h3>';
                    }
                    echo '<div class="sofir-popup-body">' . \wp_kses_post( $content ) . '</div>';
                    echo '</div>';
                    echo '</div>';
                    echo '</div>';
                    
                    return (string) ob_get_clean();
                },
            ]
        );
    }

    private function register_post_feed_block(): void {
        \register_block_type(
            'sofir/post-feed',
            [
                'attributes'      => [
                    'postType' => [ 'type' => 'string', 'default' => 'post' ],
                    'postsPerPage' => [ 'type' => 'number', 'default' => 10 ],
                    'layout' => [ 'type' => 'string', 'default' => 'grid' ],
                ],
                'render_callback' => function ( array $attributes ): string {
                    $post_type = $attributes['postType'] ?? 'post';
                    $per_page = $attributes['postsPerPage'] ?? 10;
                    $layout = $attributes['layout'] ?? 'grid';
                    
                    $query = new \WP_Query( [
                        'post_type' => $post_type,
                        'posts_per_page' => $per_page,
                        'post_status' => 'publish',
                    ] );
                    
                    ob_start();
                    echo '<div class="sofir-post-feed sofir-post-feed-' . \esc_attr( $layout ) . '">';
                    
                    if ( $query->have_posts() ) {
                        while ( $query->have_posts() ) {
                            $query->the_post();
                            echo '<article class="sofir-post-item">';
                            if ( \has_post_thumbnail() ) {
                                echo '<div class="sofir-post-thumbnail">' . \get_the_post_thumbnail( null, 'medium' ) . '</div>';
                            }
                            echo '<h3><a href="' . \esc_url( \get_permalink() ) . '">' . \get_the_title() . '</a></h3>';
                            echo '<div class="sofir-post-excerpt">' . \get_the_excerpt() . '</div>';
                            echo '</article>';
                        }
                        \wp_reset_postdata();
                    }
                    
                    echo '</div>';
                    
                    return (string) ob_get_clean();
                },
            ]
        );
    }

    private function register_print_template_block(): void {
        \register_block_type(
            'sofir/print-template',
            [
                'attributes'      => [
                    'templateId' => [ 'type' => 'number', 'default' => 0 ],
                ],
                'render_callback' => function ( array $attributes ): string {
                    $template_id = $attributes['templateId'] ?? 0;
                    
                    ob_start();
                    echo '<div class="sofir-print-template">';
                    echo '<button class="button" onclick="window.print()">' . \esc_html__( 'Print', 'sofir' ) . '</button>';
                    echo '</div>';
                    
                    return (string) ob_get_clean();
                },
            ]
        );
    }

    private function register_product_form_block(): void {
        \register_block_type(
            'sofir/product-form',
            [
                'render_callback' => function (): string {
                    ob_start();
                    echo '<form class="sofir-product-form">';
                    echo '<input type="text" name="product_name" placeholder="' . \esc_attr__( 'Product Name', 'sofir' ) . '" required />';
                    echo '<textarea name="product_description" placeholder="' . \esc_attr__( 'Description', 'sofir' ) . '"></textarea>';
                    echo '<input type="number" name="product_price" placeholder="' . \esc_attr__( 'Price', 'sofir' ) . '" step="0.01" required />';
                    echo '<button type="submit" class="button button-primary">' . \esc_html__( 'Add Product', 'sofir' ) . '</button>';
                    echo '</form>';
                    
                    return (string) ob_get_clean();
                },
            ]
        );
    }

    private function register_product_price_block(): void {
        \register_block_type(
            'sofir/product-price',
            [
                'attributes'      => [
                    'productId' => [ 'type' => 'number', 'default' => 0 ],
                    'showCurrency' => [ 'type' => 'boolean', 'default' => true ],
                ],
                'render_callback' => function ( array $attributes ): string {
                    $product_id = $attributes['productId'] ?? \get_the_ID();
                    $show_currency = $attributes['showCurrency'] ?? true;
                    
                    $price = \get_post_meta( $product_id, 'sofir_product_price', true );
                    
                    if ( ! $price ) {
                        return '';
                    }
                    
                    $currency = $show_currency ? \get_option( 'sofir_currency', 'USD' ) . ' ' : '';
                    
                    return '<div class="sofir-product-price">' . \esc_html( $currency . $price ) . '</div>';
                },
            ]
        );
    }

    private function register_quick_search_block(): void {
        \register_block_type(
            'sofir/quick-search',
            [
                'attributes'      => [
                    'postType' => [ 'type' => 'string', 'default' => 'post' ],
                    'placeholder' => [ 'type' => 'string', 'default' => 'Search...' ],
                ],
                'render_callback' => function ( array $attributes ): string {
                    $post_type = $attributes['postType'] ?? 'post';
                    $placeholder = $attributes['placeholder'] ?? 'Search...';
                    
                    \wp_enqueue_script( 'sofir-quick-search' );
                    
                    ob_start();
                    echo '<div class="sofir-quick-search" data-post-type="' . \esc_attr( $post_type ) . '">';
                    echo '<input type="search" placeholder="' . \esc_attr( $placeholder ) . '" />';
                    echo '<div class="sofir-quick-search-results"></div>';
                    echo '</div>';
                    
                    return (string) ob_get_clean();
                },
            ]
        );
    }

    private function register_review_stats_block(): void {
        \register_block_type(
            'sofir/review-stats',
            [
                'attributes'      => [
                    'postId' => [ 'type' => 'number', 'default' => 0 ],
                ],
                'render_callback' => function ( array $attributes ): string {
                    $post_id = $attributes['postId'] ?? \get_the_ID();
                    
                    $average = \get_post_meta( $post_id, 'sofir_review_average', true );
                    $comments = \get_comments_number( $post_id );
                    
                    if ( ! $average ) {
                        return '';
                    }
                    
                    ob_start();
                    echo '<div class="sofir-review-stats">';
                    echo '<div class="sofir-rating-average">' . \esc_html( \number_format_i18n( (float) $average, 1 ) ) . '</div>';
                    echo '<div class="sofir-rating-stars">' . str_repeat( '⭐', (int) round( (float) $average ) ) . '</div>';
                    echo '<div class="sofir-rating-count">' . \sprintf( \_n( '%s review', '%s reviews', $comments, 'sofir' ), \number_format_i18n( $comments ) ) . '</div>';
                    echo '</div>';
                    
                    return (string) ob_get_clean();
                },
            ]
        );
    }

    private function register_ring_chart_block(): void {
        \register_block_type(
            'sofir/ring-chart',
            [
                'attributes'      => [
                    'data' => [ 'type' => 'array', 'default' => [] ],
                    'title' => [ 'type' => 'string', 'default' => '' ],
                ],
                'render_callback' => function ( array $attributes ): string {
                    $data = $attributes['data'] ?? [];
                    $title = $attributes['title'] ?? '';
                    
                    \wp_enqueue_script( 'sofir-charts' );
                    
                    ob_start();
                    echo '<div class="sofir-ring-chart">';
                    if ( $title ) {
                        echo '<h3>' . \esc_html( $title ) . '</h3>';
                    }
                    echo '<canvas class="sofir-chart-canvas" data-type="doughnut" data-values="' . \esc_attr( \wp_json_encode( $data ) ) . '"></canvas>';
                    echo '</div>';
                    
                    return (string) ob_get_clean();
                },
            ]
        );
    }

    private function register_sales_chart_block(): void {
        \register_block_type(
            'sofir/sales-chart',
            [
                'attributes'      => [
                    'period' => [ 'type' => 'string', 'default' => 'month' ],
                    'title' => [ 'type' => 'string', 'default' => 'Sales Chart' ],
                ],
                'render_callback' => function ( array $attributes ): string {
                    $period = $attributes['period'] ?? 'month';
                    $title = $attributes['title'] ?? 'Sales Chart';
                    
                    \wp_enqueue_script( 'sofir-charts' );
                    
                    ob_start();
                    echo '<div class="sofir-sales-chart">';
                    echo '<h3>' . \esc_html( $title ) . '</h3>';
                    echo '<canvas class="sofir-chart-canvas" data-type="line" data-period="' . \esc_attr( $period ) . '"></canvas>';
                    echo '</div>';
                    
                    return (string) ob_get_clean();
                },
            ]
        );
    }

    private function register_search_form_block(): void {
        \register_block_type(
            'sofir/search-form',
            [
                'attributes'      => [
                    'postType' => [ 'type' => 'string', 'default' => 'post' ],
                    'advancedFilters' => [ 'type' => 'boolean', 'default' => false ],
                ],
                'render_callback' => function ( array $attributes ): string {
                    $post_type = $attributes['postType'] ?? 'post';
                    $advanced = $attributes['advancedFilters'] ?? false;
                    
                    ob_start();
                    echo '<form class="sofir-search-form" method="get" action="' . \esc_url( \home_url( '/' ) ) . '">';
                    echo '<input type="hidden" name="post_type" value="' . \esc_attr( $post_type ) . '" />';
                    echo '<input type="search" name="s" placeholder="' . \esc_attr__( 'Search...', 'sofir' ) . '" />';
                    
                    if ( $advanced ) {
                        $taxonomies = \get_object_taxonomies( $post_type, 'objects' );
                        foreach ( $taxonomies as $tax ) {
                            $terms = \get_terms( [ 'taxonomy' => $tax->name, 'hide_empty' => true ] );
                            if ( ! empty( $terms ) && ! \is_wp_error( $terms ) ) {
                                echo '<select name="' . \esc_attr( $tax->name ) . '">';
                                echo '<option value="">' . \esc_html( $tax->label ) . '</option>';
                                foreach ( $terms as $term ) {
                                    echo '<option value="' . \esc_attr( $term->slug ) . '">' . \esc_html( $term->name ) . '</option>';
                                }
                                echo '</select>';
                            }
                        }
                    }
                    
                    echo '<button type="submit" class="button button-primary">' . \esc_html__( 'Search', 'sofir' ) . '</button>';
                    echo '</form>';
                    
                    return (string) ob_get_clean();
                },
            ]
        );
    }

    private function register_slider_block(): void {
        \register_block_type(
            'sofir/slider',
            [
                'attributes'      => [
                    'slides' => [ 'type' => 'array', 'default' => [] ],
                    'autoplay' => [ 'type' => 'boolean', 'default' => true ],
                    'interval' => [ 'type' => 'number', 'default' => 5000 ],
                ],
                'render_callback' => function ( array $attributes ): string {
                    $slides = $attributes['slides'] ?? [];
                    $autoplay = $attributes['autoplay'] ?? true;
                    $interval = $attributes['interval'] ?? 5000;
                    
                    if ( empty( $slides ) ) {
                        return '';
                    }
                    
                    \wp_enqueue_script( 'sofir-slider' );
                    
                    ob_start();
                    echo '<div class="sofir-slider" data-autoplay="' . \esc_attr( $autoplay ? 'true' : 'false' ) . '" data-interval="' . \esc_attr( $interval ) . '">';
                    echo '<div class="sofir-slider-track">';
                    
                    foreach ( $slides as $slide ) {
                        echo '<div class="sofir-slide">';
                        if ( isset( $slide['image'] ) ) {
                            echo \wp_get_attachment_image( $slide['image'], 'large' );
                        }
                        if ( isset( $slide['caption'] ) ) {
                            echo '<div class="sofir-slide-caption">' . \esc_html( $slide['caption'] ) . '</div>';
                        }
                        echo '</div>';
                    }
                    
                    echo '</div>';
                    echo '<button class="sofir-slider-prev">&lsaquo;</button>';
                    echo '<button class="sofir-slider-next">&rsaquo;</button>';
                    echo '</div>';
                    
                    return (string) ob_get_clean();
                },
            ]
        );
    }

    private function register_term_feed_block(): void {
        \register_block_type(
            'sofir/term-feed',
            [
                'attributes'      => [
                    'taxonomy' => [ 'type' => 'string', 'default' => 'category' ],
                    'limit' => [ 'type' => 'number', 'default' => 10 ],
                ],
                'render_callback' => function ( array $attributes ): string {
                    $taxonomy = $attributes['taxonomy'] ?? 'category';
                    $limit = $attributes['limit'] ?? 10;
                    
                    $terms = \get_terms( [
                        'taxonomy' => $taxonomy,
                        'number' => $limit,
                        'hide_empty' => true,
                    ] );
                    
                    if ( empty( $terms ) || \is_wp_error( $terms ) ) {
                        return '';
                    }
                    
                    ob_start();
                    echo '<div class="sofir-term-feed">';
                    foreach ( $terms as $term ) {
                        echo '<div class="sofir-term-item">';
                        echo '<a href="' . \esc_url( \get_term_link( $term ) ) . '">' . \esc_html( $term->name ) . '</a>';
                        echo '<span class="sofir-term-count">(' . \esc_html( $term->count ) . ')</span>';
                        echo '</div>';
                    }
                    echo '</div>';
                    
                    return (string) ob_get_clean();
                },
            ]
        );
    }

    private function register_timeline_block(): void {
        \register_block_type(
            'sofir/timeline',
            [
                'attributes'      => [
                    'items' => [ 'type' => 'array', 'default' => [] ],
                    'orientation' => [ 'type' => 'string', 'default' => 'vertical' ],
                ],
                'render_callback' => function ( array $attributes ): string {
                    $items = $attributes['items'] ?? [];
                    $orientation = $attributes['orientation'] ?? 'vertical';
                    
                    if ( empty( $items ) ) {
                        return '';
                    }
                    
                    ob_start();
                    echo '<div class="sofir-timeline sofir-timeline-' . \esc_attr( $orientation ) . '">';
                    
                    foreach ( $items as $item ) {
                        echo '<div class="sofir-timeline-item">';
                        echo '<div class="sofir-timeline-marker"></div>';
                        echo '<div class="sofir-timeline-content">';
                        if ( isset( $item['date'] ) ) {
                            echo '<time class="sofir-timeline-date">' . \esc_html( $item['date'] ) . '</time>';
                        }
                        if ( isset( $item['title'] ) ) {
                            echo '<h4>' . \esc_html( $item['title'] ) . '</h4>';
                        }
                        if ( isset( $item['content'] ) ) {
                            echo '<div class="sofir-timeline-text">' . \wp_kses_post( $item['content'] ) . '</div>';
                        }
                        echo '</div>';
                        echo '</div>';
                    }
                    
                    echo '</div>';
                    
                    return (string) ob_get_clean();
                },
            ]
        );
    }

    private function register_timeline_style_kit_block(): void {
        \register_block_type(
            'sofir/timeline-style-kit',
            [
                'attributes'      => [
                    'stylePreset' => [ 'type' => 'string', 'default' => 'modern' ],
                    'colorScheme' => [ 'type' => 'string', 'default' => 'blue' ],
                ],
                'render_callback' => function ( array $attributes ): string {
                    $preset = $attributes['stylePreset'] ?? 'modern';
                    $scheme = $attributes['colorScheme'] ?? 'blue';
                    
                    return '<div class="sofir-timeline-style-kit" data-preset="' . \esc_attr( $preset ) . '" data-scheme="' . \esc_attr( $scheme ) . '"></div>';
                },
            ]
        );
    }

    private function register_user_bar_block(): void {
        \register_block_type(
            'sofir/user-bar',
            [
                'render_callback' => function (): string {
                    ob_start();
                    echo '<div class="sofir-user-bar">';
                    
                    if ( \is_user_logged_in() ) {
                        $user = \wp_get_current_user();
                        echo '<div class="sofir-user-info">';
                        echo \get_avatar( $user->ID, 32 );
                        echo '<span class="sofir-user-name">' . \esc_html( $user->display_name ) . '</span>';
                        echo '</div>';
                        echo '<div class="sofir-user-actions">';
                        echo '<a href="' . \esc_url( \admin_url( 'profile.php' ) ) . '">' . \esc_html__( 'Profile', 'sofir' ) . '</a>';
                        echo '<a href="' . \esc_url( \wp_logout_url() ) . '">' . \esc_html__( 'Logout', 'sofir' ) . '</a>';
                        echo '</div>';
                    } else {
                        echo '<a href="' . \esc_url( \wp_login_url() ) . '" class="button">' . \esc_html__( 'Login', 'sofir' ) . '</a>';
                    }
                    
                    echo '</div>';
                    
                    return (string) ob_get_clean();
                },
            ]
        );
    }

    private function register_visit_chart_block(): void {
        \register_block_type(
            'sofir/visit-chart',
            [
                'attributes'      => [
                    'period' => [ 'type' => 'string', 'default' => 'week' ],
                    'title' => [ 'type' => 'string', 'default' => 'Visitor Statistics' ],
                ],
                'render_callback' => function ( array $attributes ): string {
                    $period = $attributes['period'] ?? 'week';
                    $title = $attributes['title'] ?? 'Visitor Statistics';
                    
                    \wp_enqueue_script( 'sofir-charts' );
                    
                    ob_start();
                    echo '<div class="sofir-visit-chart">';
                    echo '<h3>' . \esc_html( $title ) . '</h3>';
                    echo '<canvas class="sofir-chart-canvas" data-type="bar" data-period="' . \esc_attr( $period ) . '"></canvas>';
                    echo '</div>';
                    
                    return (string) ob_get_clean();
                },
            ]
        );
    }

    private function register_work_hours_block(): void {
        \register_block_type(
            'sofir/work-hours',
            [
                'attributes'      => [
                    'postId' => [ 'type' => 'number', 'default' => 0 ],
                    'showStatus' => [ 'type' => 'boolean', 'default' => true ],
                ],
                'render_callback' => function ( array $attributes ): string {
                    $post_id = $attributes['postId'] ?? \get_the_ID();
                    $show_status = $attributes['showStatus'] ?? true;
                    
                    $hours = \get_post_meta( $post_id, 'sofir_work_hours', true );
                    
                    if ( empty( $hours ) || ! \is_array( $hours ) ) {
                        return '';
                    }
                    
                    ob_start();
                    echo '<div class="sofir-work-hours">';
                    
                    if ( $show_status ) {
                        $is_open = $this->check_if_open_now( $hours );
                        $status_class = $is_open ? 'is-open' : 'is-closed';
                        $status_text = $is_open ? \__( 'Open Now', 'sofir' ) : \__( 'Closed', 'sofir' );
                        echo '<div class="sofir-work-status ' . \esc_attr( $status_class ) . '">' . \esc_html( $status_text ) . '</div>';
                    }
                    
                    echo '<table class="sofir-hours-table">';
                    $days = [ 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday' ];
                    
                    foreach ( $days as $day ) {
                        if ( isset( $hours[ $day ] ) ) {
                            $day_label = \ucfirst( $day );
                            $hours_text = $hours[ $day ]['closed'] ?? false ? \__( 'Closed', 'sofir' ) : ( $hours[ $day ]['open'] ?? '' ) . ' - ' . ( $hours[ $day ]['close'] ?? '' );
                            echo '<tr>';
                            echo '<td class="sofir-day">' . \esc_html( $day_label ) . '</td>';
                            echo '<td class="sofir-hours">' . \esc_html( $hours_text ) . '</td>';
                            echo '</tr>';
                        }
                    }
                    
                    echo '</table>';
                    echo '</div>';
                    
                    return (string) ob_get_clean();
                },
            ]
        );
    }

    private function check_if_open_now( array $hours ): bool {
        $current_day = \strtolower( \gmdate( 'l' ) );
        $current_time = \gmdate( 'H:i' );
        
        if ( ! isset( $hours[ $current_day ] ) || ! empty( $hours[ $current_day ]['closed'] ) ) {
            return false;
        }
        
        $open = $hours[ $current_day ]['open'] ?? '';
        $close = $hours[ $current_day ]['close'] ?? '';
        
        if ( ! $open || ! $close ) {
            return false;
        }
        
        return $current_time >= $open && $current_time <= $close;
    }

    private function register_testimonial_slider_block(): void {
        \register_block_type(
            'sofir/testimonial-slider',
            [
                'attributes'      => [
                    'autoplay' => [ 'type' => 'boolean', 'default' => true ],
                    'interval' => [ 'type' => 'number', 'default' => 5000 ],
                    'showRating' => [ 'type' => 'boolean', 'default' => true ],
                    'postType' => [ 'type' => 'string', 'default' => 'testimonial' ],
                    'numberOfItems' => [ 'type' => 'number', 'default' => 6 ],
                ],
                'render_callback' => function ( array $attributes ): string {
                    $post_type = $attributes['postType'] ?? 'testimonial';
                    $number = $attributes['numberOfItems'] ?? 6;
                    $show_rating = $attributes['showRating'] ?? true;
                    $autoplay = $attributes['autoplay'] ?? true;
                    $interval = $attributes['interval'] ?? 5000;
                    
                    $query = new \WP_Query( [
                        'post_type' => $post_type,
                        'posts_per_page' => $number,
                        'orderby' => 'date',
                        'order' => 'DESC',
                    ] );
                    
                    if ( ! $query->have_posts() ) {
                        return '';
                    }
                    
                    ob_start();
                    echo '<div class="sofir-testimonial-slider" data-autoplay="' . \esc_attr( $autoplay ? 'true' : 'false' ) . '" data-interval="' . \esc_attr( $interval ) . '">';
                    echo '<div class="sofir-testimonial-slides">';
                    
                    while ( $query->have_posts() ) {
                        $query->the_post();
                        $rating = \get_post_meta( \get_the_ID(), 'sofir_rating', true );
                        $author = \get_post_meta( \get_the_ID(), 'sofir_author', true );
                        $position = \get_post_meta( \get_the_ID(), 'sofir_position', true );
                        
                        echo '<div class="sofir-testimonial-slide">';
                        echo '<div class="sofir-testimonial-content">';
                        
                        if ( $show_rating && $rating ) {
                            echo '<div class="sofir-testimonial-rating">';
                            for ( $i = 0; $i < 5; $i++ ) {
                                echo $i < $rating ? '★' : '☆';
                            }
                            echo '</div>';
                        }
                        
                        echo '<blockquote>' . \get_the_content() . '</blockquote>';
                        echo '<div class="sofir-testimonial-author">';
                        echo '<strong>' . \esc_html( $author ?: \get_the_title() ) . '</strong>';
                        
                        if ( $position ) {
                            echo '<span class="sofir-testimonial-position">' . \esc_html( $position ) . '</span>';
                        }
                        
                        echo '</div>';
                        echo '</div>';
                        echo '</div>';
                    }
                    
                    \wp_reset_postdata();
                    
                    echo '</div>';
                    echo '<button class="sofir-slider-prev">‹</button>';
                    echo '<button class="sofir-slider-next">›</button>';
                    echo '<div class="sofir-slider-dots"></div>';
                    echo '</div>';
                    
                    return (string) ob_get_clean();
                },
            ]
        );
    }

    private function register_pricing_table_block(): void {
        \register_block_type(
            'sofir/pricing-table',
            [
                'attributes'      => [
                    'columns' => [ 'type' => 'number', 'default' => 3 ],
                    'postType' => [ 'type' => 'string', 'default' => 'pricing' ],
                    'showFeatures' => [ 'type' => 'boolean', 'default' => true ],
                    'highlightBest' => [ 'type' => 'boolean', 'default' => true ],
                ],
                'render_callback' => function ( array $attributes ): string {
                    $columns = $attributes['columns'] ?? 3;
                    $post_type = $attributes['postType'] ?? 'pricing';
                    $show_features = $attributes['showFeatures'] ?? true;
                    $highlight_best = $attributes['highlightBest'] ?? true;
                    
                    $query = new \WP_Query( [
                        'post_type' => $post_type,
                        'posts_per_page' => $columns,
                        'orderby' => 'menu_order',
                        'order' => 'ASC',
                    ] );
                    
                    if ( ! $query->have_posts() ) {
                        return '';
                    }
                    
                    ob_start();
                    echo '<div class="sofir-pricing-table sofir-pricing-columns-' . \esc_attr( $columns ) . '">';
                    
                    while ( $query->have_posts() ) {
                        $query->the_post();
                        $price = \get_post_meta( \get_the_ID(), 'sofir_price', true );
                        $period = \get_post_meta( \get_the_ID(), 'sofir_period', true );
                        $features = \get_post_meta( \get_the_ID(), 'sofir_features', true );
                        $button_text = \get_post_meta( \get_the_ID(), 'sofir_button_text', true );
                        $button_url = \get_post_meta( \get_the_ID(), 'sofir_button_url', true );
                        $is_featured = \get_post_meta( \get_the_ID(), 'sofir_featured', true );
                        
                        $class = 'sofir-pricing-plan';
                        if ( $highlight_best && $is_featured ) {
                            $class .= ' sofir-pricing-featured';
                        }
                        
                        echo '<div class="' . \esc_attr( $class ) . '">';
                        
                        if ( $is_featured && $highlight_best ) {
                            echo '<div class="sofir-pricing-badge">' . \esc_html__( 'Most Popular', 'sofir' ) . '</div>';
                        }
                        
                        echo '<h3 class="sofir-pricing-title">' . \get_the_title() . '</h3>';
                        echo '<div class="sofir-pricing-price">';
                        echo '<span class="sofir-pricing-amount">' . \esc_html( $price ) . '</span>';
                        
                        if ( $period ) {
                            echo '<span class="sofir-pricing-period">/' . \esc_html( $period ) . '</span>';
                        }
                        
                        echo '</div>';
                        
                        if ( $show_features && $features ) {
                            echo '<ul class="sofir-pricing-features">';
                            
                            if ( \is_array( $features ) ) {
                                foreach ( $features as $feature ) {
                                    echo '<li>' . \esc_html( $feature ) . '</li>';
                                }
                            }
                            
                            echo '</ul>';
                        }
                        
                        if ( $button_text && $button_url ) {
                            echo '<a href="' . \esc_url( $button_url ) . '" class="sofir-pricing-button">' . \esc_html( $button_text ) . '</a>';
                        }
                        
                        echo '</div>';
                    }
                    
                    \wp_reset_postdata();
                    
                    echo '</div>';
                    
                    return (string) ob_get_clean();
                },
            ]
        );
    }

    private function register_team_grid_block(): void {
        \register_block_type(
            'sofir/team-grid',
            [
                'attributes'      => [
                    'columns' => [ 'type' => 'number', 'default' => 3 ],
                    'postType' => [ 'type' => 'string', 'default' => 'team_member' ],
                    'numberOfItems' => [ 'type' => 'number', 'default' => 6 ],
                    'showSocial' => [ 'type' => 'boolean', 'default' => true ],
                ],
                'render_callback' => function ( array $attributes ): string {
                    $columns = $attributes['columns'] ?? 3;
                    $post_type = $attributes['postType'] ?? 'team_member';
                    $number = $attributes['numberOfItems'] ?? 6;
                    $show_social = $attributes['showSocial'] ?? true;
                    
                    $query = new \WP_Query( [
                        'post_type' => $post_type,
                        'posts_per_page' => $number,
                        'orderby' => 'menu_order',
                        'order' => 'ASC',
                    ] );
                    
                    if ( ! $query->have_posts() ) {
                        return '';
                    }
                    
                    ob_start();
                    echo '<div class="sofir-team-grid sofir-team-columns-' . \esc_attr( $columns ) . '">';
                    
                    while ( $query->have_posts() ) {
                        $query->the_post();
                        $position = \get_post_meta( \get_the_ID(), 'sofir_position', true );
                        $twitter = \get_post_meta( \get_the_ID(), 'sofir_twitter', true );
                        $linkedin = \get_post_meta( \get_the_ID(), 'sofir_linkedin', true );
                        $email = \get_post_meta( \get_the_ID(), 'sofir_email', true );
                        
                        echo '<div class="sofir-team-member">';
                        
                        if ( \has_post_thumbnail() ) {
                            echo '<div class="sofir-team-photo">';
                            \the_post_thumbnail( 'medium' );
                            echo '</div>';
                        }
                        
                        echo '<div class="sofir-team-info">';
                        echo '<h3 class="sofir-team-name">' . \get_the_title() . '</h3>';
                        
                        if ( $position ) {
                            echo '<p class="sofir-team-position">' . \esc_html( $position ) . '</p>';
                        }
                        
                        if ( \get_the_content() ) {
                            echo '<div class="sofir-team-bio">' . \wp_kses_post( \get_the_content() ) . '</div>';
                        }
                        
                        if ( $show_social && ( $twitter || $linkedin || $email ) ) {
                            echo '<div class="sofir-team-social">';
                            
                            if ( $twitter ) {
                                echo '<a href="' . \esc_url( $twitter ) . '" target="_blank" rel="noopener">Twitter</a>';
                            }
                            
                            if ( $linkedin ) {
                                echo '<a href="' . \esc_url( $linkedin ) . '" target="_blank" rel="noopener">LinkedIn</a>';
                            }
                            
                            if ( $email ) {
                                echo '<a href="mailto:' . \esc_attr( $email ) . '">Email</a>';
                            }
                            
                            echo '</div>';
                        }
                        
                        echo '</div>';
                        echo '</div>';
                    }
                    
                    \wp_reset_postdata();
                    
                    echo '</div>';
                    
                    return (string) ob_get_clean();
                },
            ]
        );
    }

    private function register_faq_accordion_block(): void {
        \register_block_type(
            'sofir/faq-accordion',
            [
                'attributes'      => [
                    'postType' => [ 'type' => 'string', 'default' => 'faq' ],
                    'numberOfItems' => [ 'type' => 'number', 'default' => 10 ],
                    'expandFirst' => [ 'type' => 'boolean', 'default' => true ],
                ],
                'render_callback' => function ( array $attributes ): string {
                    $post_type = $attributes['postType'] ?? 'faq';
                    $number = $attributes['numberOfItems'] ?? 10;
                    $expand_first = $attributes['expandFirst'] ?? true;
                    
                    $query = new \WP_Query( [
                        'post_type' => $post_type,
                        'posts_per_page' => $number,
                        'orderby' => 'menu_order',
                        'order' => 'ASC',
                    ] );
                    
                    if ( ! $query->have_posts() ) {
                        return '';
                    }
                    
                    ob_start();
                    echo '<div class="sofir-faq-accordion">';
                    
                    $index = 0;
                    while ( $query->have_posts() ) {
                        $query->the_post();
                        $is_expanded = $expand_first && $index === 0;
                        
                        echo '<div class="sofir-faq-item' . ( $is_expanded ? ' sofir-faq-expanded' : '' ) . '">';
                        echo '<button class="sofir-faq-question">';
                        echo '<span>' . \get_the_title() . '</span>';
                        echo '<span class="sofir-faq-icon">' . ( $is_expanded ? '−' : '+' ) . '</span>';
                        echo '</button>';
                        echo '<div class="sofir-faq-answer" style="' . ( $is_expanded ? '' : 'display:none;' ) . '">';
                        echo \wp_kses_post( \get_the_content() );
                        echo '</div>';
                        echo '</div>';
                        
                        $index++;
                    }
                    
                    \wp_reset_postdata();
                    
                    echo '</div>';
                    
                    return (string) ob_get_clean();
                },
            ]
        );
    }

    private function register_cta_banner_block(): void {
        \register_block_type(
            'sofir/cta-banner',
            [
                'attributes'      => [
                    'title' => [ 'type' => 'string', 'default' => 'Ready to Get Started?' ],
                    'description' => [ 'type' => 'string', 'default' => '' ],
                    'buttonText' => [ 'type' => 'string', 'default' => 'Get Started' ],
                    'buttonUrl' => [ 'type' => 'string', 'default' => '#' ],
                    'backgroundColor' => [ 'type' => 'string', 'default' => '#0073aa' ],
                    'textColor' => [ 'type' => 'string', 'default' => '#ffffff' ],
                    'alignment' => [ 'type' => 'string', 'default' => 'center' ],
                ],
                'render_callback' => function ( array $attributes ): string {
                    $title = $attributes['title'] ?? 'Ready to Get Started?';
                    $description = $attributes['description'] ?? '';
                    $button_text = $attributes['buttonText'] ?? 'Get Started';
                    $button_url = $attributes['buttonUrl'] ?? '#';
                    $bg_color = $attributes['backgroundColor'] ?? '#0073aa';
                    $text_color = $attributes['textColor'] ?? '#ffffff';
                    $alignment = $attributes['alignment'] ?? 'center';
                    
                    ob_start();
                    echo '<div class="sofir-cta-banner sofir-cta-align-' . \esc_attr( $alignment ) . '" style="background-color:' . \esc_attr( $bg_color ) . ';color:' . \esc_attr( $text_color ) . ';">';
                    echo '<div class="sofir-cta-content">';
                    echo '<h2 class="sofir-cta-title">' . \esc_html( $title ) . '</h2>';
                    
                    if ( $description ) {
                        echo '<p class="sofir-cta-description">' . \esc_html( $description ) . '</p>';
                    }
                    
                    echo '</div>';
                    echo '<div class="sofir-cta-action">';
                    echo '<a href="' . \esc_url( $button_url ) . '" class="sofir-cta-button">' . \esc_html( $button_text ) . '</a>';
                    echo '</div>';
                    echo '</div>';
                    
                    return (string) ob_get_clean();
                },
            ]
        );
    }

    private function register_feature_box_block(): void {
        \register_block_type(
            'sofir/feature-box',
            [
                'attributes'      => [
                    'icon' => [ 'type' => 'string', 'default' => '⭐' ],
                    'title' => [ 'type' => 'string', 'default' => 'Feature Title' ],
                    'description' => [ 'type' => 'string', 'default' => 'Feature description goes here.' ],
                    'iconPosition' => [ 'type' => 'string', 'default' => 'top' ],
                    'alignment' => [ 'type' => 'string', 'default' => 'center' ],
                ],
                'render_callback' => function ( array $attributes ): string {
                    $icon = $attributes['icon'] ?? '⭐';
                    $title = $attributes['title'] ?? 'Feature Title';
                    $description = $attributes['description'] ?? 'Feature description goes here.';
                    $icon_position = $attributes['iconPosition'] ?? 'top';
                    $alignment = $attributes['alignment'] ?? 'center';
                    
                    ob_start();
                    echo '<div class="sofir-feature-box sofir-feature-icon-' . \esc_attr( $icon_position ) . ' sofir-feature-align-' . \esc_attr( $alignment ) . '">';
                    echo '<div class="sofir-feature-icon">' . \wp_kses_post( $icon ) . '</div>';
                    echo '<div class="sofir-feature-content">';
                    echo '<h3 class="sofir-feature-title">' . \esc_html( $title ) . '</h3>';
                    echo '<p class="sofir-feature-description">' . \esc_html( $description ) . '</p>';
                    echo '</div>';
                    echo '</div>';
                    
                    return (string) ob_get_clean();
                },
            ]
        );
    }

    private function register_contact_form_block(): void {
        \register_block_type(
            'sofir/contact-form',
            [
                'attributes'      => [
                    'title' => [ 'type' => 'string', 'default' => 'Contact Us' ],
                    'showSubject' => [ 'type' => 'boolean', 'default' => true ],
                    'showPhone' => [ 'type' => 'boolean', 'default' => false ],
                    'submitText' => [ 'type' => 'string', 'default' => 'Send Message' ],
                ],
                'render_callback' => function ( array $attributes ): string {
                    $title = $attributes['title'] ?? 'Contact Us';
                    $show_subject = $attributes['showSubject'] ?? true;
                    $show_phone = $attributes['showPhone'] ?? false;
                    $submit_text = $attributes['submitText'] ?? 'Send Message';
                    
                    ob_start();
                    echo '<div class="sofir-contact-form">';
                    
                    if ( $title ) {
                        echo '<h3 class="sofir-contact-title">' . \esc_html( $title ) . '</h3>';
                    }
                    
                    echo '<form class="sofir-contact-form-fields" method="post" action="' . \esc_url( \admin_url( 'admin-post.php' ) ) . '">';
                    echo '<input type="hidden" name="action" value="sofir_contact_form">';
                    \wp_nonce_field( 'sofir_contact_form', 'sofir_contact_nonce' );
                    
                    echo '<div class="sofir-form-field">';
                    echo '<label for="sofir-contact-name">' . \esc_html__( 'Name', 'sofir' ) . ' <span class="required">*</span></label>';
                    echo '<input type="text" id="sofir-contact-name" name="contact_name" required>';
                    echo '</div>';
                    
                    echo '<div class="sofir-form-field">';
                    echo '<label for="sofir-contact-email">' . \esc_html__( 'Email', 'sofir' ) . ' <span class="required">*</span></label>';
                    echo '<input type="email" id="sofir-contact-email" name="contact_email" required>';
                    echo '</div>';
                    
                    if ( $show_phone ) {
                        echo '<div class="sofir-form-field">';
                        echo '<label for="sofir-contact-phone">' . \esc_html__( 'Phone', 'sofir' ) . '</label>';
                        echo '<input type="tel" id="sofir-contact-phone" name="contact_phone">';
                        echo '</div>';
                    }
                    
                    if ( $show_subject ) {
                        echo '<div class="sofir-form-field">';
                        echo '<label for="sofir-contact-subject">' . \esc_html__( 'Subject', 'sofir' ) . '</label>';
                        echo '<input type="text" id="sofir-contact-subject" name="contact_subject">';
                        echo '</div>';
                    }
                    
                    echo '<div class="sofir-form-field">';
                    echo '<label for="sofir-contact-message">' . \esc_html__( 'Message', 'sofir' ) . ' <span class="required">*</span></label>';
                    echo '<textarea id="sofir-contact-message" name="contact_message" rows="5" required></textarea>';
                    echo '</div>';
                    
                    echo '<div class="sofir-form-field">';
                    echo '<button type="submit" class="sofir-contact-submit">' . \esc_html( $submit_text ) . '</button>';
                    echo '</div>';
                    
                    echo '</form>';
                    echo '</div>';
                    
                    return (string) ob_get_clean();
                },
            ]
        );
    }

    private function register_social_share_block(): void {
        \register_block_type(
            'sofir/social-share',
            [
                'attributes'      => [
                    'title' => [ 'type' => 'string', 'default' => 'Share this:' ],
                    'platforms' => [ 'type' => 'array', 'default' => [ 'facebook', 'twitter', 'linkedin', 'whatsapp' ] ],
                    'layout' => [ 'type' => 'string', 'default' => 'horizontal' ],
                ],
                'render_callback' => function ( array $attributes ): string {
                    $title = $attributes['title'] ?? 'Share this:';
                    $platforms = $attributes['platforms'] ?? [ 'facebook', 'twitter', 'linkedin', 'whatsapp' ];
                    $layout = $attributes['layout'] ?? 'horizontal';
                    
                    $url = \get_permalink();
                    $post_title = \get_the_title();
                    
                    $share_urls = [
                        'facebook' => 'https://www.facebook.com/sharer/sharer.php?u=' . \urlencode( $url ),
                        'twitter' => 'https://twitter.com/intent/tweet?url=' . \urlencode( $url ) . '&text=' . \urlencode( $post_title ),
                        'linkedin' => 'https://www.linkedin.com/sharing/share-offsite/?url=' . \urlencode( $url ),
                        'whatsapp' => 'https://wa.me/?text=' . \urlencode( $post_title . ' ' . $url ),
                    ];
                    
                    $labels = [
                        'facebook' => 'Facebook',
                        'twitter' => 'Twitter',
                        'linkedin' => 'LinkedIn',
                        'whatsapp' => 'WhatsApp',
                    ];
                    
                    ob_start();
                    echo '<div class="sofir-social-share sofir-social-' . \esc_attr( $layout ) . '">';
                    
                    if ( $title ) {
                        echo '<span class="sofir-social-title">' . \esc_html( $title ) . '</span>';
                    }
                    
                    echo '<div class="sofir-social-buttons">';
                    
                    foreach ( $platforms as $platform ) {
                        if ( isset( $share_urls[ $platform ] ) ) {
                            echo '<a href="' . \esc_url( $share_urls[ $platform ] ) . '" class="sofir-social-button sofir-social-' . \esc_attr( $platform ) . '" target="_blank" rel="noopener">';
                            echo \esc_html( $labels[ $platform ] );
                            echo '</a>';
                        }
                    }
                    
                    echo '</div>';
                    echo '</div>';
                    
                    return (string) ob_get_clean();
                },
            ]
        );
    }

    private function register_breadcrumb_block(): void {
        \register_block_type(
            'sofir/breadcrumb',
            [
                'attributes'      => [
                    'showHome' => [ 'type' => 'boolean', 'default' => true ],
                    'homeLabel' => [ 'type' => 'string', 'default' => 'Home' ],
                    'separator' => [ 'type' => 'string', 'default' => '/' ],
                ],
                'render_callback' => function ( array $attributes ): string {
                    $show_home = $attributes['showHome'] ?? true;
                    $home_label = $attributes['homeLabel'] ?? 'Home';
                    $separator = $attributes['separator'] ?? '/';
                    
                    if ( \is_front_page() ) {
                        return '';
                    }
                    
                    $breadcrumbs = [];
                    
                    if ( $show_home ) {
                        $breadcrumbs[] = [
                            'title' => $home_label,
                            'url' => \home_url( '/' ),
                        ];
                    }
                    
                    if ( \is_category() || \is_tag() || \is_tax() ) {
                        $term = \get_queried_object();
                        $breadcrumbs[] = [
                            'title' => $term->name,
                            'url' => '',
                        ];
                    } elseif ( \is_single() ) {
                        $post_type = \get_post_type();
                        $post_type_object = \get_post_type_object( $post_type );
                        
                        if ( $post_type_object && $post_type !== 'post' ) {
                            $breadcrumbs[] = [
                                'title' => $post_type_object->labels->name,
                                'url' => \get_post_type_archive_link( $post_type ),
                            ];
                        }
                        
                        $categories = \get_the_category();
                        if ( ! empty( $categories ) ) {
                            $category = $categories[0];
                            $breadcrumbs[] = [
                                'title' => $category->name,
                                'url' => \get_category_link( $category->term_id ),
                            ];
                        }
                        
                        $breadcrumbs[] = [
                            'title' => \get_the_title(),
                            'url' => '',
                        ];
                    } elseif ( \is_page() ) {
                        $breadcrumbs[] = [
                            'title' => \get_the_title(),
                            'url' => '',
                        ];
                    }
                    
                    if ( empty( $breadcrumbs ) ) {
                        return '';
                    }
                    
                    ob_start();
                    echo '<nav class="sofir-breadcrumb" aria-label="Breadcrumb">';
                    echo '<ol class="sofir-breadcrumb-list">';
                    
                    foreach ( $breadcrumbs as $index => $crumb ) {
                        $is_last = $index === count( $breadcrumbs ) - 1;
                        
                        echo '<li class="sofir-breadcrumb-item' . ( $is_last ? ' sofir-breadcrumb-current' : '' ) . '">';
                        
                        if ( $crumb['url'] && ! $is_last ) {
                            echo '<a href="' . \esc_url( $crumb['url'] ) . '">' . \esc_html( $crumb['title'] ) . '</a>';
                        } else {
                            echo \esc_html( $crumb['title'] );
                        }
                        
                        if ( ! $is_last ) {
                            echo '<span class="sofir-breadcrumb-separator">' . \esc_html( $separator ) . '</span>';
                        }
                        
                        echo '</li>';
                    }
                    
                    echo '</ol>';
                    echo '</nav>';
                    
                    return (string) ob_get_clean();
                },
            ]
        );
    }

    private function register_progress_bar_block(): void {
        \register_block_type(
            'sofir/progress-bar',
            [
                'attributes'      => [
                    'label' => [ 'type' => 'string', 'default' => 'Progress' ],
                    'percentage' => [ 'type' => 'number', 'default' => 75 ],
                    'color' => [ 'type' => 'string', 'default' => '#0073aa' ],
                    'height' => [ 'type' => 'number', 'default' => 20 ],
                    'showPercentage' => [ 'type' => 'boolean', 'default' => true ],
                    'animated' => [ 'type' => 'boolean', 'default' => true ],
                ],
                'render_callback' => function ( array $attributes ): string {
                    $label = $attributes['label'] ?? 'Progress';
                    $percentage = $attributes['percentage'] ?? 75;
                    $color = $attributes['color'] ?? '#0073aa';
                    $height = $attributes['height'] ?? 20;
                    $show_percentage = $attributes['showPercentage'] ?? true;
                    $animated = $attributes['animated'] ?? true;
                    
                    $percentage = \max( 0, \min( 100, $percentage ) );
                    
                    ob_start();
                    echo '<div class="sofir-progress-bar-wrapper">';
                    
                    if ( $label || $show_percentage ) {
                        echo '<div class="sofir-progress-header">';
                        
                        if ( $label ) {
                            echo '<span class="sofir-progress-label">' . \esc_html( $label ) . '</span>';
                        }
                        
                        if ( $show_percentage ) {
                            echo '<span class="sofir-progress-value">' . \esc_html( $percentage ) . '%</span>';
                        }
                        
                        echo '</div>';
                    }
                    
                    echo '<div class="sofir-progress-bar" style="height:' . \esc_attr( $height ) . 'px;">';
                    echo '<div class="sofir-progress-fill' . ( $animated ? ' sofir-progress-animated' : '' ) . '" style="width:' . \esc_attr( $percentage ) . '%;background-color:' . \esc_attr( $color ) . ';" data-percentage="' . \esc_attr( $percentage ) . '"></div>';
                    echo '</div>';
                    echo '</div>';
                    
                    return (string) ob_get_clean();
                },
            ]
        );
    }

    private function register_appointment_booking_block(): void {
        \register_block_type(
            'sofir/appointment-booking',
            [
                'attributes'      => [
                    'serviceId' => [ 'type' => 'number', 'default' => 0 ],
                    'providerId' => [ 'type' => 'number', 'default' => 0 ],
                    'buttonText' => [ 'type' => 'string', 'default' => 'Book Appointment' ],
                    'showCalendar' => [ 'type' => 'boolean', 'default' => true ],
                ],
                'render_callback' => function ( array $attributes ): string {
                    if ( ! \is_user_logged_in() ) {
                        return '<div class="sofir-appointment-booking"><p>' . \esc_html__( 'Please login to book an appointment.', 'sofir' ) . '</p></div>';
                    }

                    $button_text = $attributes['buttonText'] ?? 'Book Appointment';
                    $show_calendar = $attributes['showCalendar'] ?? true;
                    
                    \wp_enqueue_script( 'sofir-appointment' );
                    
                    ob_start();
                    echo '<div class="sofir-appointment-booking">';
                    echo '<form class="sofir-appointment-form" method="post">';
                    \wp_nonce_field( 'sofir_book_appointment', 'sofir_appointment_nonce' );
                    
                    echo '<div class="sofir-form-group">';
                    echo '<label for="sofir-appointment-title">' . \esc_html__( 'Appointment Title', 'sofir' ) . '</label>';
                    echo '<input type="text" id="sofir-appointment-title" name="appointment_title" required class="sofir-form-control" />';
                    echo '</div>';
                    
                    if ( $show_calendar ) {
                        echo '<div class="sofir-form-group">';
                        echo '<label for="sofir-appointment-datetime">' . \esc_html__( 'Date & Time', 'sofir' ) . '</label>';
                        echo '<input type="datetime-local" id="sofir-appointment-datetime" name="appointment_datetime" required class="sofir-form-control" />';
                        echo '</div>';
                    }
                    
                    echo '<div class="sofir-form-group">';
                    echo '<label for="sofir-appointment-duration">' . \esc_html__( 'Duration (minutes)', 'sofir' ) . '</label>';
                    echo '<select id="sofir-appointment-duration" name="appointment_duration" class="sofir-form-control">';
                    echo '<option value="15">15</option>';
                    echo '<option value="30" selected>30</option>';
                    echo '<option value="45">45</option>';
                    echo '<option value="60">60</option>';
                    echo '<option value="90">90</option>';
                    echo '<option value="120">120</option>';
                    echo '</select>';
                    echo '</div>';
                    
                    echo '<div class="sofir-form-group">';
                    echo '<label for="sofir-appointment-notes">' . \esc_html__( 'Notes', 'sofir' ) . '</label>';
                    echo '<textarea id="sofir-appointment-notes" name="appointment_notes" rows="4" class="sofir-form-control"></textarea>';
                    echo '</div>';
                    
                    echo '<button type="submit" class="button button-primary sofir-appointment-submit">' . \esc_html( $button_text ) . '</button>';
                    echo '</form>';
                    echo '</div>';
                    
                    return (string) ob_get_clean();
                },
            ]
        );
    }
}
