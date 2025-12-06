<?php
namespace Sofir\WooCommerceAddon\Addons;

class Conversations extends Addon_Base {
    public function get_id(): string {
        return 'conversations';
    }

    public function get_name(): string {
        return __( 'Conversations', 'sofir' );
    }

    public function get_description(): string {
        return __( 'Enable customers to send messages from their My Account Dashboard for better communication.', 'sofir' );
    }

    public function get_category(): string {
        return 'customer';
    }

    public function get_icon(): string {
        return 'dashicons-format-chat';
    }

    public function get_settings(): array {
        return [
            'enable_conversations' => [
                'type' => 'checkbox',
                'label' => __( 'Enable Conversations', 'sofir' ),
                'default' => true,
            ],
            'require_login' => [
                'type' => 'checkbox',
                'label' => __( 'Require User Login', 'sofir' ),
                'default' => true,
            ],
            'admin_email_notifications' => [
                'type' => 'checkbox',
                'label' => __( 'Admin Email Notifications', 'sofir' ),
                'default' => true,
            ],
            'customer_email_notifications' => [
                'type' => 'checkbox',
                'label' => __( 'Customer Email Notifications', 'sofir' ),
                'default' => true,
            ],
            'auto_reply_enabled' => [
                'type' => 'checkbox',
                'label' => __( 'Enable Auto Reply', 'sofir' ),
                'default' => false,
            ],
            'auto_reply_message' => [
                'type' => 'textarea',
                'label' => __( 'Auto Reply Message', 'sofir' ),
                'default' => __( 'Thank you for your message. We will get back to you soon.', 'sofir' ),
                'rows' => 4,
            ],
            'max_file_size' => [
                'type' => 'number',
                'label' => __( 'Max File Size (MB)', 'sofir' ),
                'default' => 5,
                'min' => 1,
                'max' => 20,
            ],
            'allowed_file_types' => [
                'type' => 'text',
                'label' => __( 'Allowed File Types', 'sofir' ),
                'default' => 'jpg,jpeg,png,gif,pdf,doc,docx,txt',
                'description' => __( 'Comma-separated file extensions', 'sofir' ),
            ],
        ];
    }

    public function enable(): void {
        parent::enable();
        
        \add_action( 'init', [ $this, 'register_conversation_cpt' ] );
        \add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
        \add_action( 'wp_ajax_sofir_send_message', [ $this, 'ajax_send_message' ] );
        \add_action( 'wp_ajax_sofir_get_conversations', [ $this, 'ajax_get_conversations' ] );
        \add_action( 'wp_ajax_sofir_get_messages', [ $this, 'ajax_get_messages' ] );
        \add_action( 'wp_ajax_sofir_mark_read', [ $this, 'ajax_mark_read' ] );
        \add_action( 'wp_ajax_sofir_upload_file', [ $this, 'ajax_upload_file' ] );
        \add_action( 'woocommerce_account_menu_items', [ $this, 'add_conversations_menu_item' ] );
        \add_action( 'init', [ $this, 'add_conversations_endpoint' ] );
        \add_action( 'woocommerce_account_conversations_endpoint', [ $this, 'render_conversations_page' ] );
        \add_action( 'add_meta_boxes', [ $this, 'add_conversation_meta_boxes' ] );
        \add_action( 'save_post', [ $this, 'save_conversation_meta_data' ] );
        \add_filter( 'manage_sofir_conversation_posts_columns', [ $this, 'add_conversation_columns' ] );
        \add_action( 'manage_sofir_conversation_posts_custom_column', [ $this, 'display_conversation_columns' ], 10, 2 );
        \add_shortcode( 'sofir_contact_form', [ $this, 'render_contact_form' ] );
    }

    public function disable(): void {
        parent::disable();
        
        \remove_action( 'init', [ $this, 'register_conversation_cpt' ] );
        \remove_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
        \remove_action( 'wp_ajax_sofir_send_message', [ $this, 'ajax_send_message' ] );
        \remove_action( 'wp_ajax_sofir_get_conversations', [ $this, 'ajax_get_conversations' ] );
        \remove_action( 'wp_ajax_sofir_get_messages', [ $this, 'ajax_get_messages' ] );
        \remove_action( 'wp_ajax_sofir_mark_read', [ $this, 'ajax_mark_read' ] );
        \remove_action( 'wp_ajax_sofir_upload_file', [ $this, 'ajax_upload_file' ] );
        \remove_filter( 'woocommerce_account_menu_items', [ $this, 'add_conversations_menu_item' ] );
        \remove_action( 'init', [ $this, 'add_conversations_endpoint' ] );
        \remove_action( 'woocommerce_account_conversations_endpoint', [ $this, 'render_conversations_page' ] );
        \remove_action( 'add_meta_boxes', [ $this, 'add_conversation_meta_boxes' ] );
        \remove_action( 'save_post', [ $this, 'save_conversation_meta_data' ] );
        \remove_filter( 'manage_sofir_conversation_posts_columns', [ $this, 'add_conversation_columns' ] );
        \remove_action( 'manage_sofir_conversation_posts_custom_column', [ $this, 'display_conversation_columns' ], 10 );
        \remove_shortcode( 'sofir_contact_form' );
    }

    public function register_conversation_cpt(): void {
        \register_post_type( 'sofir_conversation', [
            'label' => __( 'Conversations', 'sofir' ),
            'public' => false,
            'show_ui' => true,
            'capability_type' => 'post',
            'supports' => [ 'title', 'custom-fields', 'comments' ],
            'show_in_menu' => 'sofir-woocommerce-addon',
            'has_archive' => false,
            'rewrite' => false,
            'menu_icon' => 'dashicons-format-chat',
        ] );

        \register_post_type( 'sofir_message', [
            'label' => __( 'Messages', 'sofir' ),
            'public' => false,
            'show_ui' => false,
            'capability_type' => 'post',
            'supports' => [ 'custom-fields' ],
            'has_archive' => false,
            'rewrite' => false,
        ] );
    }

    public function enqueue_scripts(): void {
        if ( \get_option( 'sofir_wc_addon_conversations_enable_conversations', true ) ) {
            \wp_enqueue_style(
                'sofir-conversations',
                SOFIR_PLUGIN_URL . '/modules/woocommerce-addon/assets/conversations.css',
                [],
                '1.0.0'
            );
            
            \wp_enqueue_script(
                'sofir-conversations',
                SOFIR_PLUGIN_URL . '/modules/woocommerce-addon/assets/conversations.js',
                [ 'jquery' ],
                '1.0.0',
                true
            );
            
            \wp_localize_script( 'sofir-conversations', 'sofirConversations', [
                'ajaxurl' => \admin_url( 'admin-ajax.php' ),
                'nonce' => \wp_create_nonce( 'sofir_conversations_nonce' ),
                'i18n' => [
                    'sending' => __( 'Sending...', 'sofir' ),
                    'send' => __( 'Send', 'sofir' ),
                    'type_message' => __( 'Type your message...', 'sofir' ),
                    'new_conversation' => __( 'New Conversation', 'sofir' ),
                    'subject' => __( 'Subject', 'sofir' ),
                    'message' => __( 'Message', 'sofir' ),
                    'attach_file' => __( 'Attach File', 'sofir' ),
                    'login_required' => __( 'Please login to send messages', 'sofir' ),
                ],
            ] );
        }
    }

    public function add_conversations_menu_item( $items ): array {
        $items['conversations'] = __( 'Messages', 'sofir' );
        return $items;
    }

    public function add_conversations_endpoint(): void {
        \add_rewrite_endpoint( 'conversations', EP_ROOT | EP_PAGES );
    }

    public function render_conversations_page(): void {
        if ( \get_option( 'sofir_wc_addon_conversations_require_login', true ) && ! \is_user_logged_in() ) {
            echo '<p>' . __( 'Please login to view your conversations.', 'sofir' ) . '</p>';
            echo '<a href="' . \wc_get_page_permalink( 'myaccount' ) . '" class="button">' . __( 'Login', 'sofir' ) . '</a>';
            return;
        }

        $user_id = \get_current_user_id();
        $conversation_id = isset( $_GET['conversation'] ) ? intval( $_GET['conversation'] ) : 0;

        echo '<div class="sofir-conversations-dashboard">';
        echo '<h2>' . __( 'Messages', 'sofir' ) . '</h2>';

        if ( $conversation_id ) {
            $this->render_single_conversation( $conversation_id );
        } else {
            $this->render_conversations_list();
        }

        echo '</div>';
    }

    private function render_conversations_list(): void {
        $user_id = \get_current_user_id();
        $conversations = $this->get_user_conversations( $user_id );

        echo '<div class="sofir-conversations-list">';
        
        if ( ! empty( $conversations ) ) {
            echo '<div class="conversations-header">';
            echo '<button type="button" id="new-conversation-btn" class="button">' . __( 'New Message', 'sofir' ) . '</button>';
            echo '</div>';
            
            echo '<div class="conversations-table">';
            foreach ( $conversations as $conversation ) {
                $unread_count = $this->get_unread_count( $conversation->ID, $user_id );
                $last_message = $this->get_last_message( $conversation->ID );
                $status = \get_post_meta( $conversation->ID, '_conversation_status', true );
                
                echo '<div class="conversation-item" data-id="' . $conversation->ID . '">';
                echo '<div class="conversation-avatar">';
                echo \get_avatar( \get_post_meta( $conversation->ID, '_admin_id', true ) ?: 1, 50 );
                echo '</div>';
                echo '<div class="conversation-content">';
                echo '<div class="conversation-header">';
                echo '<h4 class="conversation-title">' . \get_the_title( $conversation->ID ) . '</h4>';
                echo '<span class="conversation-date">' . \wc_format_datetime( $last_message->post_date ) . '</span>';
                echo '</div>';
                echo '<div class="conversation-preview">' . \wp_trim_words( $last_message->post_content, 15 ) . '</div>';
                if ( $unread_count > 0 ) {
                    echo '<span class="unread-count">' . $unread_count . '</span>';
                }
                echo '</div>';
                echo '</div>';
            }
            echo '</div>';
        } else {
            echo '<div class="no-conversations">';
            echo '<p>' . __( 'No conversations yet.', 'sofir' ) . '</p>';
            echo '<button type="button" id="start-conversation-btn" class="button">' . __( 'Start New Conversation', 'sofir' ) . '</button>';
            echo '</div>';
        }

        echo '</div>';
    }

    private function render_single_conversation( $conversation_id ): void {
        $user_id = \get_current_user_id();
        $conversation = \get_post( $conversation_id );
        
        if ( ! $conversation || \get_post_meta( $conversation_id, '_customer_id', true ) != $user_id ) {
            echo '<p>' . __( 'Conversation not found.', 'sofir' ) . '</p>';
            return;
        }

        $messages = $this->get_conversation_messages( $conversation_id );
        
        echo '<div class="sofir-conversation-detail">';
        echo '<div class="conversation-header">';
        echo '<a href="' . \wc_get_account_endpoint_url( 'conversations' ) . '" class="back-link">&larr; ' . __( 'Back to Messages', 'sofir' ) . '</a>';
        echo '<h3>' . \get_the_title( $conversation_id ) . '</h3>';
        echo '</div>';
        
        echo '<div class="messages-container">';
        foreach ( $messages as $message ) {
            $is_admin = \get_post_meta( $message->ID, '_is_admin', true );
            $sender_name = $is_admin ? __( 'Support', 'sofir' ) : \wp_get_current_user()->display_name;
            $avatar = \get_avatar( $is_admin ? \get_post_meta( $conversation_id, '_admin_id', true ) ?: 1 : $user_id, 40 );
            
            echo '<div class="message-item ' . ( $is_admin ? 'admin-message' : 'customer-message' ) . '">';
            echo '<div class="message-avatar">' . $avatar . '</div>';
            echo '<div class="message-content">';
            echo '<div class="message-header">';
            echo '<span class="sender-name">' . $sender_name . '</span>';
            echo '<span class="message-time">' . \wc_format_datetime( $message->post_date ) . '</span>';
            echo '</div>';
            echo '<div class="message-text">' . \wpautop( $message->post_content ) . '</div>';
            
            $attachment_id = \get_post_meta( $message->ID, '_attachment_id', true );
            if ( $attachment_id ) {
                $attachment_url = \wp_get_attachment_url( $attachment_id );
                $attachment_name = \basename( get_attached_file( $attachment_id ) );
                echo '<div class="message-attachment">';
                echo '<a href="' . esc_url( $attachment_url ) . '" target="_blank">';
                echo '<span class="dashicons dashicons-paperclip"></span> ' . esc_html( $attachment_name );
                echo '</a>';
                echo '</div>';
            }
            
            echo '</div>';
            echo '</div>';
        }
        echo '</div>';
        
        echo '<div class="message-form">';
        echo '<form id="reply-form" data-conversation-id="' . $conversation_id . '">';
        echo '<div class="form-row">';
        echo '<textarea name="message" placeholder="' . __( 'Type your reply...', 'sofir' ) . '" required></textarea>';
        echo '</div>';
        echo '<div class="form-row">';
        echo '<button type="button" id="attach-file-btn" class="button">' . __( 'Attach File', 'sofir' ) . '</button>';
        echo '<input type="file" id="file-input" style="display: none;" accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx,.txt">';
        echo '<button type="submit" class="button alt">' . __( 'Send Reply', 'sofir' ) . '</button>';
        echo '</div>';
        echo '</form>';
        echo '</div>';
        
        echo '</div>';
    }

    private function get_user_conversations( $user_id ): array {
        return \get_posts( [
            'post_type' => 'sofir_conversation',
            'meta_key' => '_customer_id',
            'meta_value' => $user_id,
            'posts_per_page' => -1,
            'orderby' => 'modified',
            'order' => 'DESC',
        ] );
    }

    private function get_conversation_messages( $conversation_id ): array {
        return \get_posts( [
            'post_type' => 'sofir_message',
            'meta_key' => '_conversation_id',
            'meta_value' => $conversation_id,
            'posts_per_page' => -1,
            'orderby' => 'date',
            'order' => 'ASC',
        ] );
    }

    private function get_last_message( $conversation_id ): \WP_Post {
        $messages = \get_posts( [
            'post_type' => 'sofir_message',
            'meta_key' => '_conversation_id',
            'meta_value' => $conversation_id,
            'posts_per_page' => 1,
            'orderby' => 'date',
            'order' => 'DESC',
        ] );

        return $messages[0] ?: new \WP_Post( (object) [ 'post_content' => '', 'post_date' => \current_time( 'mysql' ) ] );
    }

    private function get_unread_count( $conversation_id, $user_id ): int {
        $messages = $this->get_conversation_messages( $conversation_id );
        $count = 0;
        
        foreach ( $messages as $message ) {
            $is_admin = \get_post_meta( $message->ID, '_is_admin', true );
            $read_by_customer = \get_post_meta( $message->ID, '_read_by_customer', true );
            
            if ( $is_admin && ! $read_by_customer ) {
                $count++;
            }
        }
        
        return $count;
    }

    public function render_contact_form( $atts ): string {
        $atts = \shortcode_atts( [
            'title' => __( 'Contact Us', 'sofir' ),
            'description' => __( 'Send us a message and we will get back to you soon.', 'sofir' ),
        ], $atts );

        ob_start();
        ?>
        <div class="sofir-contact-form">
            <h2><?php echo esc_html( $atts['title'] ); ?></h2>
            <p><?php echo esc_html( $atts['description'] ); ?></p>
            
            <?php if ( \get_option( 'sofir_wc_addon_conversations_require_login', true ) && ! \is_user_logged_in() ): ?>
                <p><?php _e( 'Please login to send a message.', 'sofir' ); ?></p>
                <a href="<?php echo \wc_get_page_permalink( 'myaccount' ); ?>" class="button"><?php _e( 'Login', 'sofir' ); ?></a>
            <?php else: ?>
                <form id="sofir-contact-form" class="contact-form">
                    <div class="form-row">
                        <label for="contact-subject"><?php _e( 'Subject', 'sofir' ); ?></label>
                        <input type="text" id="contact-subject" name="subject" required>
                    </div>
                    
                    <div class="form-row">
                        <label for="contact-message"><?php _e( 'Message', 'sofir' ); ?></label>
                        <textarea id="contact-message" name="message" rows="6" required></textarea>
                    </div>
                    
                    <div class="form-row">
                        <button type="button" id="contact-attach-file" class="button"><?php _e( 'Attach File', 'sofir' ); ?></button>
                        <input type="file" id="contact-file-input" style="display: none;" accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx,.txt">
                        <button type="submit" class="button alt"><?php _e( 'Send Message', 'sofir' ); ?></button>
                    </div>
                </form>
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }

    public function ajax_send_message(): void {
        \check_ajax_referer( 'sofir_conversations_nonce', 'nonce' );

        if ( \get_option( 'sofir_wc_addon_conversations_require_login', true ) && ! \is_user_logged_in() ) {
            \wp_send_json_error( [ 'message' => __( 'Please login to send messages', 'sofir' ) ] );
        }

        $conversation_id = isset( $_POST['conversation_id'] ) ? intval( $_POST['conversation_id'] ) : 0;
        $subject = isset( $_POST['subject'] ) ? \sanitize_text_field( $_POST['subject'] ) : '';
        $message = isset( $_POST['message'] ) ? \sanitize_textarea_field( $_POST['message'] ) : '';
        $attachment_id = isset( $_POST['attachment_id'] ) ? intval( $_POST['attachment_id'] ) : 0;

        if ( empty( $message ) ) {
            \wp_send_json_error( [ 'message' => __( 'Message cannot be empty', 'sofir' ) ] );
        }

        $user_id = \get_current_user_id();
        $is_new_conversation = false;

        // Create new conversation if needed
        if ( ! $conversation_id ) {
            if ( empty( $subject ) ) {
                \wp_send_json_error( [ 'message' => __( 'Subject is required for new conversation', 'sofir' ) ] );
            }

            $conversation_id = \wp_insert_post( [
                'post_type' => 'sofir_conversation',
                'post_title' => $subject,
                'post_status' => 'publish',
                'meta_input' => [
                    '_customer_id' => $user_id,
                    '_admin_id' => 1, // Default admin
                    '_conversation_status' => 'open',
                ],
            ] );

            $is_new_conversation = true;
        }

        // Create message
        $message_id = \wp_insert_post( [
            'post_type' => 'sofir_message',
            'post_content' => $message,
            'post_status' => 'publish',
            'meta_input' => [
                '_conversation_id' => $conversation_id,
                '_customer_id' => $user_id,
                '_is_admin' => 0,
                '_attachment_id' => $attachment_id,
                '_read_by_customer' => 1, // Mark as read by sender
            ],
        ] );

        if ( $message_id ) {
            // Send admin notification
            if ( \get_option( 'sofir_wc_addon_conversations_admin_email_notifications', true ) ) {
                $this->send_admin_notification( $conversation_id, $message, $is_new_conversation );
            }

            // Send auto reply if enabled and it's a new conversation
            if ( $is_new_conversation && \get_option( 'sofir_wc_addon_conversations_auto_reply_enabled', false ) ) {
                $this->send_auto_reply( $conversation_id );
            }

            \wp_send_json_success( [
                'message' => __( 'Message sent successfully', 'sofir' ),
                'conversation_id' => $conversation_id,
                'message_id' => $message_id,
            ] );
        } else {
            \wp_send_json_error( [ 'message' => __( 'Error sending message', 'sofir' ) ] );
        }
    }

    public function ajax_get_conversations(): void {
        \check_ajax_referer( 'sofir_conversations_nonce', 'nonce' );

        if ( ! \is_user_logged_in() ) {
            \wp_send_json_error( [ 'message' => __( 'Permission denied', 'sofir' ) ] );
        }

        $user_id = \get_current_user_id();
        $conversations = $this->get_user_conversations( $user_id );
        $data = [];

        foreach ( $conversations as $conversation ) {
            $unread_count = $this->get_unread_count( $conversation->ID, $user_id );
            $last_message = $this->get_last_message( $conversation->ID );
            
            $data[] = [
                'id' => $conversation->ID,
                'title' => \get_the_title( $conversation->ID ),
                'last_message' => \wp_trim_words( $last_message->post_content, 15 ),
                'date' => \wc_format_datetime( $last_message->post_date ),
                'unread_count' => $unread_count,
            ];
        }

        \wp_send_json_success( $data );
    }

    public function ajax_get_messages(): void {
        \check_ajax_referer( 'sofir_conversations_nonce', 'nonce' );

        if ( ! \is_user_logged_in() ) {
            \wp_send_json_error( [ 'message' => __( 'Permission denied', 'sofir' ) ] );
        }

        $conversation_id = isset( $_POST['conversation_id'] ) ? intval( $_POST['conversation_id'] ) : 0;
        $user_id = \get_current_user_id();

        if ( ! $conversation_id ) {
            \wp_send_json_error( [ 'message' => __( 'Invalid conversation ID', 'sofir' ) ] );
        }

        // Verify user owns this conversation
        if ( \get_post_meta( $conversation_id, '_customer_id', true ) != $user_id ) {
            \wp_send_json_error( [ 'message' => __( 'Permission denied', 'sofir' ) ] );
        }

        $messages = $this->get_conversation_messages( $conversation_id );
        $data = [];

        foreach ( $messages as $message ) {
            $is_admin = \get_post_meta( $message->ID, '_is_admin', true );
            $attachment_id = \get_post_meta( $message->ID, '_attachment_id', true );
            $attachment = $attachment_id ? [
                'id' => $attachment_id,
                'url' => \wp_get_attachment_url( $attachment_id ),
                'name' => \basename( get_attached_file( $attachment_id ) ),
            ] : null;

            $data[] = [
                'id' => $message->ID,
                'content' => $message->post_content,
                'date' => \wc_format_datetime( $message->post_date ),
                'is_admin' => $is_admin,
                'attachment' => $attachment,
            ];
        }

        // Mark messages as read
        $this->mark_messages_read( $conversation_id, $user_id );

        \wp_send_json_success( $data );
    }

    public function ajax_mark_read(): void {
        \check_ajax_referer( 'sofir_conversations_nonce', 'nonce' );

        if ( ! \is_user_logged_in() ) {
            \wp_send_json_error( [ 'message' => __( 'Permission denied', 'sofir' ) ] );
        }

        $conversation_id = isset( $_POST['conversation_id'] ) ? intval( $_POST['conversation_id'] ) : 0;
        $user_id = \get_current_user_id();

        if ( ! $conversation_id ) {
            \wp_send_json_error( [ 'message' => __( 'Invalid conversation ID', 'sofir' ) ] );
        }

        $this->mark_messages_read( $conversation_id, $user_id );
        \wp_send_json_success( [ 'message' => __( 'Messages marked as read', 'sofir' ) ] );
    }

    public function ajax_upload_file(): void {
        \check_ajax_referer( 'sofir_conversations_nonce', 'nonce' );

        if ( ! \is_user_logged_in() ) {
            \wp_send_json_error( [ 'message' => __( 'Permission denied', 'sofir' ) ] );
        }

        if ( ! isset( $_FILES['file'] ) ) {
            \wp_send_json_error( [ 'message' => __( 'No file uploaded', 'sofir' ) ] );
        }

        $file = $_FILES['file'];
        $max_size = \get_option( 'sofir_wc_addon_conversations_max_file_size', 5 ) * 1024 * 1024;
        $allowed_types = \get_option( 'sofir_wc_addon_conversations_allowed_file_types', 'jpg,jpeg,png,gif,pdf,doc,docx,txt' );
        $allowed_extensions = explode( ',', $allowed_types );
        $file_extension = strtolower( pathinfo( $file['name'], PATHINFO_EXTENSION ) );

        if ( $file['size'] > $max_size ) {
            \wp_send_json_error( [ 'message' => __( 'File size exceeds limit', 'sofir' ) ] );
        }

        if ( ! in_array( $file_extension, $allowed_extensions ) ) {
            \wp_send_json_error( [ 'message' => __( 'File type not allowed', 'sofir' ) ] );
        }

        $upload = \wp_handle_upload( $file, [ 'test_form' => false ] );
        
        if ( isset( $upload['error'] ) ) {
            \wp_send_json_error( [ 'message' => $upload['error'] ] );
        }

        $attachment_id = \wp_insert_attachment( [
            'post_mime_type' => $upload['type'],
            'post_title' => \sanitize_file_name( $upload['file'] ),
            'post_content' => '',
            'post_status' => 'inherit',
        ], $upload['file'] );

        if ( $attachment_id ) {
            \wp_send_json_success( [
                'attachment_id' => $attachment_id,
                'url' => $upload['url'],
                'name' => \basename( $upload['file'] ),
            ] );
        } else {
            \wp_send_json_error( [ 'message' => __( 'Error uploading file', 'sofir' ) ] );
        }
    }

    private function mark_messages_read( $conversation_id, $user_id ): void {
        $messages = $this->get_conversation_messages( $conversation_id );
        
        foreach ( $messages as $message ) {
            $is_admin = \get_post_meta( $message->ID, '_is_admin', true );
            
            if ( $is_admin ) {
                \update_post_meta( $message->ID, '_read_by_customer', 1 );
            }
        }
    }

    private function send_admin_notification( $conversation_id, $message, $is_new ): void {
        $admin_email = \get_option( 'admin_email' );
        $subject = $is_new ? 
            sprintf( __( 'New Conversation Started - #%d', 'sofir' ), $conversation_id ) :
            sprintf( __( 'New Message in Conversation - #%d', 'sofir' ), $conversation_id );
        
        $body = sprintf(
            __( 'You have received a new message in conversation #%d. Message: %s. View conversation: %s', 'sofir' ),
            $conversation_id,
            $message,
            \admin_url( 'post.php?post=' . $conversation_id . '&action=edit' )
        );
        
        \wp_mail( $admin_email, $subject, $body );
    }

    private function send_auto_reply( $conversation_id ): void {
        $auto_reply_message = \get_option( 'sofir_wc_addon_conversations_auto_reply_message', __( 'Thank you for your message. We will get back to you soon.', 'sofir' ) );
        
        $message_id = \wp_insert_post( [
            'post_type' => 'sofir_message',
            'post_content' => $auto_reply_message,
            'post_status' => 'publish',
            'meta_input' => [
                '_conversation_id' => $conversation_id,
                '_is_admin' => 1,
                '_read_by_customer' => 0,
            ],
        ] );

        if ( $message_id && \get_option( 'sofir_wc_addon_conversations_customer_email_notifications', true ) ) {
            $customer_id = \get_post_meta( $conversation_id, '_customer_id', true );
            $customer = \get_userdata( $customer_id );
            
            if ( $customer ) {
                $subject = sprintf( __( 'Auto Reply - Conversation #%d', 'sofir' ), $conversation_id );
                \wp_mail( $customer->user_email, $subject, $auto_reply_message );
            }
        }
    }

    public function add_conversation_meta_boxes(): void {
        \add_meta_box(
            'sofir-conversation-details',
            __( 'Conversation Details', 'sofir' ),
            [ $this, 'render_conversation_meta_box' ],
            'sofir_conversation',
            'normal',
            'high'
        );
    }

    public function render_conversation_meta_box( $post ): void {
        $customer_id = \get_post_meta( $post->ID, '_customer_id', true );
        $status = \get_post_meta( $post->ID, '_conversation_status', true );
        
        $customer = $customer_id ? \get_userdata( $customer_id ) : null;
        
        echo '<div class="conversation-details">';
        echo '<p><strong>' . __( 'Customer:', 'sofir' ) . '</strong> ';
        if ( $customer ) {
            echo $customer->display_name . ' (' . $customer->user_email . ')';
        } else {
            echo __( 'Unknown', 'sofir' );
        }
        echo '</p>';
        
        echo '<p><strong>' . __( 'Status:', 'sofir' ) . '</strong> ' . \ucfirst( $status ?: 'open' ) . '</p>';
        echo '</div>';
    }

    public function save_conversation_meta_data( $post_id ): void {
        if ( \get_post_type( $post_id ) !== 'sofir_conversation' ) {
            return;
        }

        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        if ( ! \current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

        if ( isset( $_POST['conversation_status'] ) ) {
            \update_post_meta( $post_id, '_conversation_status', \sanitize_text_field( $_POST['conversation_status'] ) );
        }
    }

    public function add_conversation_columns( $columns ): array {
        $columns['customer'] = __( 'Customer', 'sofir' );
        $columns['status'] = __( 'Status', 'sofir' );
        $columns['last_message'] = __( 'Last Message', 'sofir' );
        return $columns;
    }

    public function display_conversation_columns( $column, $post_id ): void {
        switch ( $column ) {
            case 'customer':
                $customer_id = \get_post_meta( $post_id, '_customer_id', true );
                $customer = $customer_id ? \get_userdata( $customer_id ) : null;
                if ( $customer ) {
                    echo $customer->display_name . '<br>' . $customer->user_email;
                } else {
                    echo __( 'Unknown', 'sofir' );
                }
                break;
                
            case 'status':
                $status = \get_post_meta( $post_id, '_conversation_status', true );
                echo '<span class="status-' . esc_attr( $status ?: 'open' ) . '">' . \ucfirst( $status ?: 'open' ) . '</span>';
                break;
                
            case 'last_message':
                $last_message = $this->get_last_message( $post_id );
                echo \wc_format_datetime( $last_message->post_date );
                break;
        }
    }
}