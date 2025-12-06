<?php
namespace Sofir\WooCommerceAddon\Addons;

class Request_Quote extends Addon_Base {
    public function get_id(): string {
        return 'request-quote';
    }

    public function get_name(): string {
        return __( 'Request a Quote', 'sofir' );
    }

    public function get_description(): string {
        return __( 'Let buyers request quotes for products with custom pricing and negotiation options.', 'sofir' );
    }

    public function get_category(): string {
        return 'sales';
    }

    public function get_icon(): string {
        return 'dashicons-format-quote';
    }

    public function get_settings(): array {
        return [
            'enable_quote_requests' => [
                'type' => 'checkbox',
                'label' => __( 'Enable Quote Requests', 'sofir' ),
                'default' => true,
            ],
            'show_on_product_page' => [
                'type' => 'checkbox',
                'label' => __( 'Show Quote Button on Product Pages', 'sofir' ),
                'default' => true,
            ],
            'show_on_shop_page' => [
                'type' => 'checkbox',
                'label' => __( 'Show Quote Button on Shop Pages', 'sofir' ),
                'default' => false,
            ],
            'auto_approve_quotes' => [
                'type' => 'checkbox',
                'label' => __( 'Auto Approve Quote Requests', 'sofir' ),
                'default' => false,
            ],
            'require_login' => [
                'type' => 'checkbox',
                'label' => __( 'Require User Login', 'sofir' ),
                'default' => true,
            ],
            'quote_validity_days' => [
                'type' => 'number',
                'label' => __( 'Quote Validity (Days)', 'sofir' ),
                'default' => 7,
                'min' => 1,
                'max' => 90,
            ],
            'admin_email' => [
                'type' => 'email',
                'label' => __( 'Admin Notification Email', 'sofir' ),
                'default' => \get_option( 'admin_email' ),
            ],
            'custom_message_required' => [
                'type' => 'checkbox',
                'label' => __( 'Require Custom Message', 'sofir' ),
                'default' => false,
            ],
        ];
    }

    public function enable(): void {
        parent::enable();
        
        \add_action( 'init', [ $this, 'register_quote_cpt' ] );
        \add_action( 'woocommerce_single_product_summary', [ $this, 'add_quote_button_product_page' ], 35 );
        \add_action( 'woocommerce_after_shop_loop_item', [ $this, 'add_quote_button_shop_page' ] );
        \add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
        \add_action( 'wp_ajax_sofir_request_quote', [ $this, 'ajax_request_quote' ] );
        \add_action( 'wp_ajax_nopriv_sofir_request_quote', [ $this, 'ajax_request_quote' ] );
        \add_action( 'wp_ajax_sofir_update_quote_status', [ $this, 'ajax_update_quote_status' ] );
        \add_action( 'wp_ajax_sofir_send_quote_response', [ $this, 'ajax_send_quote_response' ] );
        \add_shortcode( 'sofir_quote_form', [ $this, 'render_quote_form' ] );
        \add_filter( 'woocommerce_my_account_my_orders_query', [ $this, 'add_quotes_to_my_account' ] );
        \add_action( 'woocommerce_account_quotes_endpoint', [ $this, 'render_quotes_page' ] );
        \add_action( 'init', [ $this, 'add_quotes_endpoint' ] );
        \add_filter( 'woocommerce_account_menu_items', [ $this, 'add_quotes_menu_item' ] );
        \add_action( 'add_meta_boxes', [ $this, 'add_quote_meta_boxes' ] );
        \add_action( 'save_post', [ $this, 'save_quote_meta_data' ] );
    }

    public function disable(): void {
        parent::disable();
        
        \remove_action( 'init', [ $this, 'register_quote_cpt' ] );
        \remove_action( 'woocommerce_single_product_summary', [ $this, 'add_quote_button_product_page' ], 35 );
        \remove_action( 'woocommerce_after_shop_loop_item', [ $this, 'add_quote_button_shop_page' ] );
        \remove_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
        \remove_action( 'wp_ajax_sofir_request_quote', [ $this, 'ajax_request_quote' ] );
        \remove_action( 'wp_ajax_nopriv_sofir_request_quote', [ $this, 'ajax_request_quote' ] );
        \remove_action( 'wp_ajax_sofir_update_quote_status', [ $this, 'ajax_update_quote_status' ] );
        \remove_action( 'wp_ajax_sofir_send_quote_response', [ $this, 'ajax_send_quote_response' ] );
        \remove_shortcode( 'sofir_quote_form' );
        \remove_filter( 'woocommerce_my_account_my_orders_query', [ $this, 'add_quotes_to_my_account' ] );
        \remove_action( 'woocommerce_account_quotes_endpoint', [ $this, 'render_quotes_page' ] );
        \remove_action( 'init', [ $this, 'add_quotes_endpoint' ] );
        \remove_filter( 'woocommerce_account_menu_items', [ $this, 'add_quotes_menu_item' ] );
        \remove_action( 'add_meta_boxes', [ $this, 'add_quote_meta_boxes' ] );
        \remove_action( 'save_post', [ $this, 'save_quote_meta_data' ] );
    }

    public function register_quote_cpt(): void {
        \register_post_type( 'sofir_quote', [
            'label' => __( 'Quote Requests', 'sofir' ),
            'public' => false,
            'show_ui' => true,
            'capability_type' => 'post',
            'supports' => [ 'title', 'custom-fields', 'comments' ],
            'show_in_menu' => 'sofir-woocommerce-addon',
            'has_archive' => false,
            'rewrite' => false,
            'menu_icon' => 'dashicons-format-quote',
        ] );

        \register_post_status( 'quote-pending', [
            'label' => __( 'Pending', 'sofir' ),
            'public' => true,
            'exclude_from_search' => false,
            'show_in_admin_all_list' => true,
            'show_in_admin_status_list' => true,
            'label_count' => _n_noop( 'Pending <span class="count">(%s)</span>', 'Pending <span class="count">(%s)</span>', 'sofir' ),
        ] );

        \register_post_status( 'quote-approved', [
            'label' => __( 'Approved', 'sofir' ),
            'public' => true,
            'exclude_from_search' => false,
            'show_in_admin_all_list' => true,
            'show_in_admin_status_list' => true,
            'label_count' => _n_noop( 'Approved <span class="count">(%s)</span>', 'Approved <span class="count">(%s)</span>', 'sofir' ),
        ] );

        \register_post_status( 'quote-rejected', [
            'label' => __( 'Rejected', 'sofir' ),
            'public' => true,
            'exclude_from_search' => false,
            'show_in_admin_all_list' => true,
            'show_in_admin_status_list' => true,
            'label_count' => _n_noop( 'Rejected <span class="count">(%s)</span>', 'Rejected <span class="count">(%s)</span>', 'sofir' ),
        ] );
    }

    public function enqueue_scripts(): void {
        if ( \get_option( 'sofir_wc_addon_request_quote_enable_quote_requests', true ) ) {
            \wp_enqueue_style(
                'sofir-quote-request',
                SOFIR_PLUGIN_URL . '/modules/woocommerce-addon/assets/quote-request.css',
                [],
                '1.0.0'
            );
            
            \wp_enqueue_script(
                'sofir-quote-request',
                SOFIR_PLUGIN_URL . '/modules/woocommerce-addon/assets/quote-request.js',
                [ 'jquery' ],
                '1.0.0',
                true
            );
            
            \wp_localize_script( 'sofir-quote-request', 'sofirQuote', [
                'ajaxurl' => \admin_url( 'admin-ajax.php' ),
                'nonce' => \wp_create_nonce( 'sofir_quote_nonce' ),
                'i18n' => [
                    'request_quote' => __( 'Request Quote', 'sofir' ),
                    'close' => __( 'Close', 'sofir' ),
                    'sending' => __( 'Sending...', 'sofir' ),
                    'success' => __( 'Quote request sent successfully!', 'sofir' ),
                    'error' => __( 'Error sending quote request', 'sofir' ),
                    'login_required' => __( 'Please login to request a quote', 'sofir' ),
                ],
            ] );
        }
    }

    public function add_quote_button_product_page(): void {
        global $product;
        
        if ( ! \get_option( 'sofir_wc_addon_request_quote_show_on_product_page', true ) ) {
            return;
        }

        if ( ! $this->can_request_quote( $product ) ) {
            return;
        }

        echo '<div class="sofir-quote-button-wrapper">';
        echo '<button type="button" class="button sofir-request-quote" data-product-id="' . $product->get_id() . '">' . __( 'Request Quote', 'sofir' ) . '</button>';
        echo '</div>';
    }

    public function add_quote_button_shop_page(): void {
        global $product;
        
        if ( ! \get_option( 'sofir_wc_addon_request_quote_show_on_shop_page', false ) ) {
            return;
        }

        if ( ! $this->can_request_quote( $product ) ) {
            return;
        }

        echo '<div class="sofir-quote-button-wrapper">';
        echo '<button type="button" class="button sofir-request-quote" data-product-id="' . $product->get_id() . '">' . __( 'Request Quote', 'sofir' ) . '</button>';
        echo '</div>';
    }

    private function can_request_quote( $product ): bool {
        if ( ! \get_option( 'sofir_wc_addon_request_quote_enable_quote_requests', true ) ) {
            return false;
        }

        if ( \get_option( 'sofir_wc_addon_request_quote_require_login', true ) && ! \is_user_logged_in() ) {
            return false;
        }

        // Check if product is quoteable
        $quoteable = \get_post_meta( $product->get_id(), '_quoteable', true );
        if ( $quoteable === 'no' ) {
            return false;
        }

        return true;
    }

    public function render_quote_form( $atts ): string {
        $atts = \shortcode_atts( [
            'product_id' => 0,
            'show_products' => 'yes',
        ], $atts );

        ob_start();
        ?>
        <div class="sofir-quote-form-container">
            <h2><?php _e( 'Request a Quote', 'sofir' ); ?></h2>
            
            <?php if ( \get_option( 'sofir_wc_addon_request_quote_require_login', true ) && ! \is_user_logged_in() ): ?>
                <p><?php _e( 'Please login to request a quote.', 'sofir' ); ?></p>
                <a href="<?php echo \wc_get_page_permalink( 'myaccount' ); ?>" class="button"><?php _e( 'Login', 'sofir' ); ?></a>
            <?php else: ?>
                <form id="sofir-quote-form" class="sofir-quote-form">
                    <?php if ( $atts['show_products'] === 'yes' && ! $atts['product_id'] ): ?>
                    <div class="form-row">
                        <label for="quote-products"><?php _e( 'Select Products', 'sofir' ); ?></label>
                        <select id="quote-products" name="products[]" multiple>
                            <?php
                            $products = \wc_get_products( [ 'status' => 'publish', 'limit' => -1 ] );
                            foreach ( $products as $product ) {
                                echo '<option value="' . $product->get_id() . '">' . $product->get_name() . ' - ' . $product->get_price_html() . '</option>';
                            }
                            ?>
                        </select>
                        <small><?php _e( 'Hold Ctrl/Cmd to select multiple products', 'sofir' ); ?></small>
                    </div>
                    <?php endif; ?>

                    <div class="form-row">
                        <label for="quote-quantity"><?php _e( 'Quantity', 'sofir' ); ?></label>
                        <input type="number" id="quote-quantity" name="quantity" value="1" min="1" required>
                    </div>

                    <?php if ( \get_option( 'sofir_wc_addon_request_quote_custom_message_required', false ) ): ?>
                    <div class="form-row">
                        <label for="quote-message"><?php _e( 'Message', 'sofir' ); ?></label>
                        <textarea id="quote-message" name="message" rows="4" required></textarea>
                    </div>
                    <?php endif; ?>

                    <div class="form-row">
                        <label for="quote-budget"><?php _e( 'Target Budget (Optional)', 'sofir' ); ?></label>
                        <input type="text" id="quote-budget" name="budget" placeholder="<?php _e( 'Enter your target budget', 'sofir' ); ?>">
                    </div>

                    <div class="form-row">
                        <label for="quote-deadline"><?php _e( 'Required By (Optional)', 'sofir' ); ?></label>
                        <input type="date" id="quote-deadline" name="deadline">
                    </div>

                    <input type="hidden" name="product_id" value="<?php echo intval( $atts['product_id'] ); ?>">
                    <input type="hidden" name="action" value="sofir_request_quote">
                    <input type="hidden" name="nonce" value="<?php echo \wp_create_nonce( 'sofir_quote_nonce' ); ?>">

                    <button type="submit" class="button alt"><?php _e( 'Send Quote Request', 'sofir' ); ?></button>
                </form>
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }

    public function ajax_request_quote(): void {
        \check_ajax_referer( 'sofir_quote_nonce', 'nonce' );

        if ( \get_option( 'sofir_wc_addon_request_quote_require_login', true ) && ! \is_user_logged_in() ) {
            \wp_send_json_error( [ 'message' => __( 'Please login to request a quote', 'sofir' ) ] );
        }

        $product_id = isset( $_POST['product_id'] ) ? intval( $_POST['product_id'] ) : 0;
        $products = isset( $_POST['products'] ) ? array_map( 'intval', $_POST['products'] ) : [];
        $quantity = isset( $_POST['quantity'] ) ? intval( $_POST['quantity'] ) : 1;
        $message = isset( $_POST['message'] ) ? \sanitize_textarea_field( $_POST['message'] ) : '';
        $budget = isset( $_POST['budget'] ) ? \sanitize_text_field( $_POST['budget'] ) : '';
        $deadline = isset( $_POST['deadline'] ) ? \sanitize_text_field( $_POST['deadline'] ) : '';

        if ( ! $product_id && empty( $products ) ) {
            \wp_send_json_error( [ 'message' => __( 'Please select at least one product', 'sofir' ) ] );
        }

        if ( $product_id ) {
            $products = [ $product_id ];
        }

        $user_id = \get_current_user_id();
        $title = sprintf( __( 'Quote Request for %d products', 'sofir' ), count( $products ) );

        $quote_id = \wp_insert_post( [
            'post_type' => 'sofir_quote',
            'post_title' => $title,
            'post_status' => \get_option( 'sofir_wc_addon_request_quote_auto_approve', false ) ? 'quote-approved' : 'quote-pending',
            'post_author' => $user_id,
            'meta_input' => [
                '_quote_products' => $products,
                '_quote_quantity' => $quantity,
                '_quote_message' => $message,
                '_quote_budget' => $budget,
                '_quote_deadline' => $deadline,
                '_quote_status' => \get_option( 'sofir_wc_addon_request_quote_auto_approve', false ) ? 'approved' : 'pending',
                '_quote_date' => \current_time( 'mysql' ),
            ],
        ] );

        if ( $quote_id ) {
            // Send admin notification
            $admin_email = \get_option( 'sofir_wc_addon_request_quote_admin_email', \get_option( 'admin_email' ) );
            $subject = sprintf( __( 'New Quote Request #%d', 'sofir' ), $quote_id );
            $message = sprintf(
                __( 'A new quote request has been submitted. View details: %s', 'sofir' ),
                \admin_url( 'post.php?post=' . $quote_id . '&action=edit' )
            );
            \wp_mail( $admin_email, $subject, $message );

            \wp_send_json_success( [ 'message' => __( 'Quote request sent successfully!', 'sofir' ) ] );
        } else {
            \wp_send_json_error( [ 'message' => __( 'Error creating quote request', 'sofir' ) ] );
        }
    }

    public function add_quotes_endpoint(): void {
        \add_rewrite_endpoint( 'quotes', EP_ROOT | EP_PAGES );
    }

    public function add_quotes_menu_item( $items ): array {
        $items['quotes'] = __( 'Quotes', 'sofir' );
        return $items;
    }

    public function render_quotes_page(): void {
        $user_id = \get_current_user_id();
        $quotes = \get_posts( [
            'post_type' => 'sofir_quote',
            'author' => $user_id,
            'posts_per_page' => -1,
            'post_status' => [ 'quote-pending', 'quote-approved', 'quote-rejected' ],
        ] );

        echo '<div class="sofir-quotes">';
        echo '<h2>' . __( 'My Quote Requests', 'sofir' ) . '</h2>';

        if ( ! empty( $quotes ) ) {
            echo '<div class="woocommerce-orders woocommerce-MyAccount-orders">';
            echo '<table class="woocommerce-orders-table woocommerce-MyAccount-orders-table">';
            echo '<thead><tr>';
            echo '<th class="woocommerce-orders-table__header-order-number"><span class="nobr">' . __( 'Quote #', 'sofir' ) . '</span></th>';
            echo '<th class="woocommerce-orders-table__header-order-date"><span class="nobr">' . __( 'Date', 'sofir' ) . '</span></th>';
            echo '<th class="woocommerce-orders-table__header-order-status"><span class="nobr">' . __( 'Status', 'sofir' ) . '</span></th>';
            echo '<th class="woocommerce-orders-table__header-order-total"><span class="nobr">' . __( 'Products', 'sofir' ) . '</span></th>';
            echo '<th class="woocommerce-orders-table__header-order-actions"></th>';
            echo '</tr></thead>';
            echo '<tbody>';

            foreach ( $quotes as $quote ) {
                $status = \get_post_meta( $quote->ID, '_quote_status', true );
                $products = \get_post_meta( $quote->ID, '_quote_products', true );
                $product_count = is_array( $products ) ? count( $products ) : 0;

                echo '<tr class="woocommerce-orders-table__row woocommerce-orders-table__row--status-' . esc_attr( $status ) . '">';
                echo '<td class="woocommerce-orders-table__cell-order-number" data-title="' . esc_attr__( 'Quote #', 'sofir' ) . '">';
                echo '<a href="' . esc_url( \add_query_arg( 'quote', $quote->ID, \wc_get_account_endpoint_url( 'quotes' ) ) ) . '">';
                echo '#' . $quote->ID;
                echo '</a>';
                echo '</td>';
                echo '<td class="woocommerce-orders-table__cell-order-date" data-title="' . esc_attr__( 'Date', 'sofir' ) . '">';
                echo '<time datetime="' . esc_attr( $quote->post_date ) . '">' . \wc_format_datetime( $quote->post_date ) . '</time>';
                echo '</td>';
                echo '<td class="woocommerce-orders-table__cell-order-status" data-title="' . esc_attr__( 'Status', 'sofir' ) . '">';
                echo '<span class="sofir-quote-status quote-status-' . esc_attr( $status ) . '">' . \ucfirst( $status ) . '</span>';
                echo '</td>';
                echo '<td class="woocommerce-orders-table__cell-order-total" data-title="' . esc_attr__( 'Products', 'sofir' ) . '">';
                echo $product_count . ' ' . _n( 'product', 'products', $product_count, 'sofir' );
                echo '</td>';
                echo '<td class="woocommerce-orders-table__cell-order-actions" data-title="' . esc_attr__( 'Actions', 'sofir' ) . '">';
                echo '<a href="' . esc_url( \add_query_arg( 'quote', $quote->ID, \wc_get_account_endpoint_url( 'quotes' ) ) ) . '" class="woocommerce-button button view">' . __( 'View', 'sofir' ) . '</a>';
                echo '</td>';
                echo '</tr>';
            }

            echo '</tbody></table></div>';
        } else {
            echo '<p>' . __( 'No quote requests found.', 'sofir' ) . '</p>';
        }

        echo '</div>';
    }

    public function add_quote_meta_boxes(): void {
        \add_meta_box(
            'sofir-quote-details',
            __( 'Quote Details', 'sofir' ),
            [ $this, 'render_quote_meta_box' ],
            'sofir_quote',
            'normal',
            'high'
        );

        \add_meta_box(
            'sofir-quote-response',
            __( 'Quote Response', 'sofir' ),
            [ $this, 'render_quote_response_meta_box' ],
            'sofir_quote',
            'normal',
            'high'
        );
    }

    public function render_quote_meta_box( $post ): void {
        $products = \get_post_meta( $post->ID, '_quote_products', true );
        $quantity = \get_post_meta( $post->ID, '_quote_quantity', true );
        $message = \get_post_meta( $post->ID, '_quote_message', true );
        $budget = \get_post_meta( $post->ID, '_quote_budget', true );
        $deadline = \get_post_meta( $post->ID, '_quote_deadline', true );
        $status = \get_post_meta( $post->ID, '_quote_status', true );

        echo '<div class="sofir-quote-meta">';
        echo '<p><strong>' . __( 'Products:', 'sofir' ) . '</strong> ';
        if ( is_array( $products ) ) {
            foreach ( $products as $product_id ) {
                $product = \wc_get_product( $product_id );
                if ( $product ) {
                    echo $product->get_name() . ' (' . $product->get_price_html() . ')<br>';
                }
            }
        }
        echo '</p>';
        echo '<p><strong>' . __( 'Quantity:', 'sofir' ) . '</strong> ' . esc_html( $quantity ) . '</p>';
        echo '<p><strong>' . __( 'Message:', 'sofir' ) . '</strong><br>' . esc_textarea( $message ) . '</p>';
        echo '<p><strong>' . __( 'Target Budget:', 'sofir' ) . '</strong> ' . esc_html( $budget ) . '</p>';
        echo '<p><strong>' . __( 'Deadline:', 'sofir' ) . '</strong> ' . esc_html( $deadline ) . '</p>';
        echo '</div>';
    }

    public function render_quote_response_meta_box( $post ): void {
        $quoted_price = \get_post_meta( $post->ID, '_quoted_price', true );
        $response_message = \get_post_meta( $post->ID, '_response_message', true );
        $valid_until = \get_post_meta( $post->ID, '_valid_until', true );

        echo '<div class="sofir-quote-response">';
        echo '<p><label>' . __( 'Quoted Price:', 'sofir' ) . '</label>';
        echo '<input type="text" name="quoted_price" value="' . esc_attr( $quoted_price ) . '" class="regular-text"></p>';
        
        echo '<p><label>' . __( 'Response Message:', 'sofir' ) . '</label>';
        echo '<textarea name="response_message" rows="6" class="large-text">' . esc_textarea( $response_message ) . '</textarea></p>';
        
        echo '<p><label>' . __( 'Valid Until:', 'sofir' ) . '</label>';
        echo '<input type="date" name="valid_until" value="' . esc_attr( $valid_until ) . '"></p>';
        
        echo '<p><label><input type="checkbox" name="send_email" value="1"> ' . __( 'Send email notification to customer', 'sofir' ) . '</label></p>';
        echo '</div>';
    }

    public function save_quote_meta_data( $post_id ): void {
        if ( \get_post_type( $post_id ) !== 'sofir_quote' ) {
            return;
        }

        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        if ( ! \current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

        if ( isset( $_POST['quoted_price'] ) ) {
            \update_post_meta( $post_id, '_quoted_price', \sanitize_text_field( $_POST['quoted_price'] ) );
        }

        if ( isset( $_POST['response_message'] ) ) {
            \update_post_meta( $post_id, '_response_message', \sanitize_textarea_field( $_POST['response_message'] ) );
        }

        if ( isset( $_POST['valid_until'] ) ) {
            \update_post_meta( $post_id, '_valid_until', \sanitize_text_field( $_POST['valid_until'] ) );
        }

        // Send email notification if requested
        if ( isset( $_POST['send_email'] ) && $_POST['send_email'] ) {
            $this->send_quote_response_email( $post_id );
        }
    }

    private function send_quote_response_email( $quote_id ): void {
        $quote = \get_post( $quote_id );
        $user = \get_userdata( $quote->post_author );
        
        if ( ! $user ) {
            return;
        }

        $quoted_price = \get_post_meta( $quote_id, '_quoted_price', true );
        $response_message = \get_post_meta( $quote_id, '_response_message', true );
        $valid_until = \get_post_meta( $quote_id, '_valid_until', true );

        $subject = sprintf( __( 'Quote Response for Request #%d', 'sofir' ), $quote_id );
        $message = sprintf(
            __( 'Hello %s, your quote request #%d has been responded to. Quoted Price: %s. Valid Until: %s. Message: %s', 'sofir' ),
            $user->display_name,
            $quote_id,
            $quoted_price,
            $valid_until,
            $response_message
        );

        \wp_mail( $user->user_email, $subject, $message );
    }

    public function ajax_update_quote_status(): void {
        \check_ajax_referer( 'sofir_wc_addon_nonce', 'nonce' );

        if ( ! \current_user_can( 'manage_options' ) ) {
            \wp_send_json_error( [ 'message' => __( 'Permission denied', 'sofir' ) ] );
        }

        $quote_id = isset( $_POST['quote_id'] ) ? intval( $_POST['quote_id'] ) : 0;
        $status = isset( $_POST['status'] ) ? \sanitize_text_field( $_POST['status'] ) : '';

        if ( ! $quote_id || ! $status ) {
            \wp_send_json_error( [ 'message' => __( 'Invalid parameters', 'sofir' ) ] );
        }

        \update_post_meta( $quote_id, '_quote_status', $status );
        
        $post_status = 'quote-' . $status;
        \wp_update_post( [
            'ID' => $quote_id,
            'post_status' => $post_status,
        ] );

        \wp_send_json_success( [ 'message' => __( 'Quote status updated', 'sofir' ) ] );
    }

    public function ajax_send_quote_response(): void {
        $this->ajax_update_quote_status();
    }
}