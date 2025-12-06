<?php
namespace Sofir\WooCommerceAddon\Addons;

class Subaccounts extends Addon_Base {
    public function get_id(): string {
        return 'subaccounts';
    }

    public function get_name(): string {
        return __( 'Subaccounts', 'sofir' );
    }

    public function get_description(): string {
        return __( 'Let registered users create subaccounts that can perform permitted tasks on behalf of the main account.', 'sofir' );
    }

    public function get_category(): string {
        return 'customer';
    }

    public function get_icon(): string {
        return 'dashicons-groups';
    }

    public function get_settings(): array {
        return [
            'enable_subaccounts' => [
                'type' => 'checkbox',
                'label' => __( 'Enable Subaccounts', 'sofir' ),
                'default' => true,
            ],
            'max_subaccounts' => [
                'type' => 'number',
                'label' => __( 'Maximum Subaccounts per User', 'sofir' ),
                'default' => 10,
                'min' => 1,
                'max' => 100,
            ],
            'auto_approve' => [
                'type' => 'checkbox',
                'label' => __( 'Auto Approve Subaccount Requests', 'sofir' ),
                'default' => true,
            ],
            'allow_orders' => [
                'type' => 'checkbox',
                'label' => __( 'Allow Subaccounts to Place Orders', 'sofir' ),
                'default' => true,
            ],
            'allow_view_orders' => [
                'type' => 'checkbox',
                'label' => __( 'Allow Subaccounts to View Orders', 'sofir' ),
                'default' => true,
            ],
            'allow_manage_addresses' => [
                'type' => 'checkbox',
                'label' => __( 'Allow Subaccounts to Manage Addresses', 'sofir' ),
                'default' => false,
            ],
            'email_notifications' => [
                'type' => 'checkbox',
                'label' => __( 'Send Email Notifications', 'sofir' ),
                'default' => true,
            ],
        ];
    }

    public function enable(): void {
        parent::enable();
        
        \add_action( 'init', [ $this, 'register_subaccount_role' ] );
        \add_action( 'init', [ $this, 'register_subaccount_cpt' ] );
        \add_action( 'woocommerce_account_menu_items', [ $this, 'add_subaccounts_menu_item' ] );
        \add_action( 'init', [ $this, 'add_subaccounts_endpoint' ] );
        \add_action( 'woocommerce_account_subaccounts_endpoint', [ $this, 'render_subaccounts_page' ] );
        \add_action( 'woocommerce_register_form', [ $this, 'add_subaccount_registration_field' ] );
        \add_action( 'woocommerce_created_customer', [ $this, 'handle_subaccount_registration' ], 10, 2 );
        \add_filter( 'woocommerce_login_redirect', [ $this, 'subaccount_login_redirect' ], 10, 2 );
        \add_action( 'template_redirect', [ $this, 'restrict_subaccount_access' ] );
        \add_action( 'wp_ajax_sofir_create_subaccount', [ $this, 'ajax_create_subaccount' ] );
        \add_action( 'wp_ajax_sofir_delete_subaccount', [ $this, 'ajax_delete_subaccount' ] );
        \add_action( 'wp_ajax_sofir_toggle_subaccount_status', [ $this, 'ajax_toggle_subaccount_status' ] );
        \add_filter( 'woocommerce_my_account_my_orders_actions', [ $this, 'filter_order_actions_for_subaccounts' ], 10, 2 );
        \add_filter( 'woocommerce_available_payment_gateways', [ $this, 'filter_payment_gateways_for_subaccounts' ] );
    }

    public function disable(): void {
        parent::disable();
        
        \remove_action( 'init', [ $this, 'register_subaccount_role' ] );
        \remove_action( 'init', [ $this, 'register_subaccount_cpt' ] );
        \remove_filter( 'woocommerce_account_menu_items', [ $this, 'add_subaccounts_menu_item' ] );
        \remove_action( 'init', [ $this, 'add_subaccounts_endpoint' ] );
        \remove_action( 'woocommerce_account_subaccounts_endpoint', [ $this, 'render_subaccounts_page' ] );
        \remove_action( 'woocommerce_register_form', [ $this, 'add_subaccount_registration_field' ] );
        \remove_action( 'woocommerce_created_customer', [ $this, 'handle_subaccount_registration' ], 10 );
        \remove_filter( 'woocommerce_login_redirect', [ $this, 'subaccount_login_redirect' ], 10 );
        \remove_action( 'template_redirect', [ $this, 'restrict_subaccount_access' ] );
        \remove_action( 'wp_ajax_sofir_create_subaccount', [ $this, 'ajax_create_subaccount' ] );
        \remove_action( 'wp_ajax_sofir_delete_subaccount', [ $this, 'ajax_delete_subaccount' ] );
        \remove_action( 'wp_ajax_sofir_toggle_subaccount_status', [ $this, 'ajax_toggle_subaccount_status' ] );
        \remove_filter( 'woocommerce_my_account_my_orders_actions', [ $this, 'filter_order_actions_for_subaccounts' ], 10 );
        \remove_filter( 'woocommerce_available_payment_gateways', [ $this, 'filter_payment_gateways_for_subaccounts' ] );
    }

    public function register_subaccount_role(): void {
        \add_role( 'sofir_subaccount', __( 'Subaccount', 'sofir' ), [
            'read' => true,
            'edit_posts' => false,
            'delete_posts' => false,
        ] );
    }

    public function register_subaccount_cpt(): void {
        \register_post_type( 'sofir_subaccount', [
            'label' => __( 'Subaccounts', 'sofir' ),
            'public' => false,
            'show_ui' => true,
            'capability_type' => 'post',
            'supports' => [ 'title', 'custom-fields' ],
            'show_in_menu' => 'sofir-woocommerce-addon',
        ] );
    }

    public function add_subaccounts_menu_item( $items ): array {
        $items['subaccounts'] = __( 'Subaccounts', 'sofir' );
        return $items;
    }

    public function add_subaccounts_endpoint(): void {
        \add_rewrite_endpoint( 'subaccounts', EP_ROOT | EP_PAGES );
    }

    public function render_subaccounts_page(): void {
        $user_id = \get_current_user_id();
        $subaccounts = $this->get_user_subaccounts( $user_id );
        $max_subaccounts = \get_option( 'sofir_wc_addon_subaccounts_max_subaccounts', 10 );
        $current_count = count( $subaccounts );

        echo '<div class="sofir-subaccounts">';
        echo '<h2>' . __( 'Manage Subaccounts', 'sofir' ) . '</h2>';
        
        if ( $current_count < $max_subaccounts ) {
            echo '<div class="sofir-create-subaccount">';
            echo '<h3>' . __( 'Create New Subaccount', 'sofir' ) . '</h3>';
            echo '<form id="sofir-subaccount-form">';
            echo '<div class="form-row">';
            echo '<label>' . __( 'First Name', 'sofir' ) . '</label>';
            echo '<input type="text" name="first_name" required>';
            echo '</div>';
            echo '<div class="form-row">';
            echo '<label>' . __( 'Last Name', 'sofir' ) . '</label>';
            echo '<input type="text" name="last_name" required>';
            echo '</div>';
            echo '<div class="form-row">';
            echo '<label>' . __( 'Email', 'sofir' ) . '</label>';
            echo '<input type="email" name="email" required>';
            echo '</div>';
            echo '<div class="form-row">';
            echo '<label>' . __( 'Username', 'sofir' ) . '</label>';
            echo '<input type="text" name="username" required>';
            echo '</div>';
            echo '<div class="form-row">';
            echo '<label>' . __( 'Permissions', 'sofir' ) . '</label>';
            echo '<div class="permissions">';
            echo '<label><input type="checkbox" name="permissions[]" value="orders"> ' . __( 'Place Orders', 'sofir' ) . '</label>';
            echo '<label><input type="checkbox" name="permissions[]" value="view_orders"> ' . __( 'View Orders', 'sofir' ) . '</label>';
            echo '<label><input type="checkbox" name="permissions[]" value="addresses"> ' . __( 'Manage Addresses', 'sofir' ) . '</label>';
            echo '</div>';
            echo '</div>';
            echo '<button type="submit" class="button">' . __( 'Create Subaccount', 'sofir' ) . '</button>';
            echo '</form>';
            echo '</div>';
        } else {
            echo '<p>' . sprintf( __( 'You have reached the maximum number of subaccounts (%d).', 'sofir' ), $max_subaccounts ) . '</p>';
        }

        if ( ! empty( $subaccounts ) ) {
            echo '<div class="sofir-existing-subaccounts">';
            echo '<h3>' . __( 'Existing Subaccounts', 'sofir' ) . '</h3>';
            echo '<table class="woocommerce-table woocommerce-table--subaccounts">';
            echo '<thead><tr>';
            echo '<th>' . __( 'Name', 'sofir' ) . '</th>';
            echo '<th>' . __( 'Email', 'sofir' ) . '</th>';
            echo '<th>' . __( 'Status', 'sofir' ) . '</th>';
            echo '<th>' . __( 'Permissions', 'sofir' ) . '</th>';
            echo '<th>' . __( 'Actions', 'sofir' ) . '</th>';
            echo '</tr></thead>';
            echo '<tbody>';

            foreach ( $subaccounts as $subaccount ) {
                $status = $subaccount->post_status === 'publish' ? __( 'Active', 'sofir' ) : __( 'Inactive', 'sofir' );
                $permissions = \get_post_meta( $subaccount->ID, '_subaccount_permissions', true );
                $subaccount_user_id = \get_post_meta( $subaccount->ID, '_subaccount_user_id', true );
                
                echo '<tr>';
                echo '<td>' . \get_the_title( $subaccount->ID ) . '</td>';
                echo '<td>' . \get_post_meta( $subaccount->ID, '_subaccount_email', true ) . '</td>';
                echo '<td><span class="status-' . $subaccount->post_status . '">' . $status . '</span></td>';
                echo '<td>' . $this->format_permissions( $permissions ) . '</td>';
                echo '<td>';
                echo '<button class="button toggle-status" data-id="' . $subaccount->ID . '">' . __( 'Toggle Status', 'sofir' ) . '</button> ';
                echo '<button class="button delete-subaccount" data-id="' . $subaccount->ID . '">' . __( 'Delete', 'sofir' ) . '</button>';
                echo '</td>';
                echo '</tr>';
            }

            echo '</tbody></table>';
            echo '</div>';
        }

        echo '</div>';
    }

    private function get_user_subaccounts( $user_id ): array {
        return \get_posts( [
            'post_type' => 'sofir_subaccount',
            'post_status' => [ 'publish', 'draft' ],
            'meta_key' => '_parent_account_id',
            'meta_value' => $user_id,
            'posts_per_page' => -1,
        ] );
    }

    private function format_permissions( $permissions ): string {
        if ( ! is_array( $permissions ) ) {
            return '';
        }

        $labels = [
            'orders' => __( 'Orders', 'sofir' ),
            'view_orders' => __( 'View Orders', 'sofir' ),
            'addresses' => __( 'Addresses', 'sofir' ),
        ];

        $formatted = [];
        foreach ( $permissions as $permission ) {
            if ( isset( $labels[ $permission ] ) ) {
                $formatted[] = $labels[ $permission ];
            }
        }

        return implode( ', ', $formatted );
    }

    public function ajax_create_subaccount(): void {
        \check_ajax_referer( 'sofir_wc_addon_nonce', 'nonce' );

        if ( ! \is_user_logged_in() ) {
            \wp_send_json_error( [ 'message' => __( 'You must be logged in', 'sofir' ) ] );
        }

        $user_id = \get_current_user_id();
        $max_subaccounts = \get_option( 'sofir_wc_addon_subaccounts_max_subaccounts', 10 );
        $current_count = count( $this->get_user_subaccounts( $user_id ) );

        if ( $current_count >= $max_subaccounts ) {
            \wp_send_json_error( [ 'message' => __( 'Maximum subaccounts reached', 'sofir' ) ] );
        }

        $first_name = isset( $_POST['first_name'] ) ? \sanitize_text_field( $_POST['first_name'] ) : '';
        $last_name = isset( $_POST['last_name'] ) ? \sanitize_text_field( $_POST['last_name'] ) : '';
        $email = isset( $_POST['email'] ) ? \sanitize_email( $_POST['email'] ) : '';
        $username = isset( $_POST['username'] ) ? \sanitize_user( $_POST['username'] ) : '';
        $permissions = isset( $_POST['permissions'] ) ? array_map( 'sanitize_text_field', $_POST['permissions'] ) : [];

        if ( empty( $first_name ) || empty( $last_name ) || empty( $email ) || empty( $username ) ) {
            \wp_send_json_error( [ 'message' => __( 'Please fill all required fields', 'sofir' ) ] );
        }

        if ( email_exists( $email ) ) {
            \wp_send_json_error( [ 'message' => __( 'Email already exists', 'sofir' ) ] );
        }

        if ( username_exists( $username ) ) {
            \wp_send_json_error( [ 'message' => __( 'Username already exists', 'sofir' ) ] );
        }

        // Create subaccount user
        $password = wp_generate_password( 12, false );
        $subaccount_user_id = \wp_create_user( $username, $password, $email );

        if ( is_wp_error( $subaccount_user_id ) ) {
            \wp_send_json_error( [ 'message' => $subaccount_user_id->get_error_message() ] );
        }

        // Assign subaccount role
        $user = new \WP_User( $subaccount_user_id );
        $user->set_role( 'sofir_subaccount' );

        // Create subaccount post
        $subaccount_id = \wp_insert_post( [
            'post_type' => 'sofir_subaccount',
            'post_title' => $first_name . ' ' . $last_name,
            'post_status' => \get_option( 'sofir_wc_addon_subaccounts_auto_approve', true ) ? 'publish' : 'draft',
            'meta_input' => [
                '_parent_account_id' => $user_id,
                '_subaccount_user_id' => $subaccount_user_id,
                '_subaccount_email' => $email,
                '_subaccount_permissions' => $permissions,
            ],
        ] );

        if ( \get_option( 'sofir_wc_addon_subaccounts_email_notifications', true ) ) {
            $this->send_subaccount_email( $email, $username, $password, $first_name );
        }

        \wp_send_json_success( [ 'message' => __( 'Subaccount created successfully', 'sofir' ) ] );
    }

    public function ajax_delete_subaccount(): void {
        \check_ajax_referer( 'sofir_wc_addon_nonce', 'nonce' );

        if ( ! \is_user_logged_in() ) {
            \wp_send_json_error( [ 'message' => __( 'Permission denied', 'sofir' ) ] );
        }

        $subaccount_id = isset( $_POST['subaccount_id'] ) ? intval( $_POST['subaccount_id'] ) : 0;
        $user_id = \get_current_user_id();

        $subaccount = \get_post( $subaccount_id );
        if ( ! $subaccount || $subaccount->post_type !== 'sofir_subaccount' ) {
            \wp_send_json_error( [ 'message' => __( 'Subaccount not found', 'sofir' ) ] );
        }

        if ( \get_post_meta( $subaccount_id, '_parent_account_id', true ) != $user_id ) {
            \wp_send_json_error( [ 'message' => __( 'Permission denied', 'sofir' ) ] );
        }

        $subaccount_user_id = \get_post_meta( $subaccount_id, '_subaccount_user_id', true );
        \wp_delete_post( $subaccount_id, true );
        \wp_delete_user( $subaccount_user_id );

        \wp_send_json_success( [ 'message' => __( 'Subaccount deleted successfully', 'sofir' ) ] );
    }

    public function ajax_toggle_subaccount_status(): void {
        \check_ajax_referer( 'sofir_wc_addon_nonce', 'nonce' );

        if ( ! \is_user_logged_in() ) {
            \wp_send_json_error( [ 'message' => __( 'Permission denied', 'sofir' ) ] );
        }

        $subaccount_id = isset( $_POST['subaccount_id'] ) ? intval( $_POST['subaccount_id'] ) : 0;
        $user_id = \get_current_user_id();

        $subaccount = \get_post( $subaccount_id );
        if ( ! $subaccount || $subaccount->post_type !== 'sofir_subaccount' ) {
            \wp_send_json_error( [ 'message' => __( 'Subaccount not found', 'sofir' ) ] );
        }

        if ( \get_post_meta( $subaccount_id, '_parent_account_id', true ) != $user_id ) {
            \wp_send_json_error( [ 'message' => __( 'Permission denied', 'sofir' ) ] );
        }

        $new_status = $subaccount->post_status === 'publish' ? 'draft' : 'publish';
        \wp_update_post( [
            'ID' => $subaccount_id,
            'post_status' => $new_status,
        ] );

        $status_text = $new_status === 'publish' ? __( 'activated', 'sofir' ) : __( 'deactivated', 'sofir' );
        \wp_send_json_success( [ 'message' => sprintf( __( 'Subaccount %s successfully', 'sofir' ), $status_text ) ] );
    }

    private function send_subaccount_email( $email, $username, $password, $first_name ): void {
        $subject = __( 'Your Subaccount Has Been Created', 'sofir' );
        $message = sprintf(
            __( 'Hello %s, your subaccount has been created. Username: %s, Password: %s', 'sofir' ),
            $first_name,
            $username,
            $password
        );
        
        \wp_mail( $email, $subject, $message );
    }

    public function subaccount_login_redirect( $redirect, $user ): string {
        if ( in_array( 'sofir_subaccount', (array) $user->roles ) ) {
            return \wc_get_account_endpoint_url( 'dashboard' );
        }
        return $redirect;
    }

    public function restrict_subaccount_access(): void {
        if ( ! is_user_logged_in() ) {
            return;
        }

        $user = \wp_get_current_user();
        if ( ! in_array( 'sofir_subaccount', (array) $user->roles ) ) {
            return;
        }

        // Get parent account
        $subaccount_posts = \get_posts( [
            'post_type' => 'sofir_subaccount',
            'meta_key' => '_subaccount_user_id',
            'meta_value' => $user->ID,
            'posts_per_page' => 1,
        ] );

        if ( empty( $subaccount_posts ) ) {
            return;
        }

        $permissions = \get_post_meta( $subaccount_posts[0]->ID, '_subaccount_permissions', true );
        
        // Restrict access based on permissions
        if ( ! is_array( $permissions ) ) {
            return;
        }

        $current_page = \get_query_var( 'pagename' );
        
        // Check if user has permission to access current page
        if ( $current_page === 'orders' && ! in_array( 'view_orders', $permissions ) ) {
            \wp_redirect( \wc_get_account_endpoint_url( 'dashboard' ) );
            exit;
        }

        if ( $current_page === 'edit-address' && ! in_array( 'addresses', $permissions ) ) {
            \wp_redirect( \wc_get_account_endpoint_url( 'dashboard' ) );
            exit;
        }
    }

    public function filter_order_actions_for_subaccounts( $actions, $order ): array {
        if ( ! is_user_logged_in() ) {
            return $actions;
        }

        $user = \wp_get_current_user();
        if ( ! in_array( 'sofir_subaccount', (array) $user->roles ) ) {
            return $actions;
        }

        // Remove certain actions for subaccounts
        unset( $actions['cancel'] );
        
        return $actions;
    }

    public function filter_payment_gateways_for_subaccounts( $gateways ): array {
        if ( ! is_user_logged_in() ) {
            return $gateways;
        }

        $user = \wp_get_current_user();
        if ( ! in_array( 'sofir_subaccount', (array) $user->roles ) ) {
            return $gateways;
        }

        // Get subaccount permissions
        $subaccount_posts = \get_posts( [
            'post_type' => 'sofir_subaccount',
            'meta_key' => '_subaccount_user_id',
            'meta_value' => $user->ID,
            'posts_per_page' => 1,
        ] );

        if ( empty( $subaccount_posts ) ) {
            return $gateways;
        }

        $permissions = \get_post_meta( $subaccount_posts[0]->ID, '_subaccount_permissions', true );
        
        if ( ! is_array( $permissions ) || ! in_array( 'orders', $permissions ) ) {
            return [];
        }

        return $gateways;
    }
}