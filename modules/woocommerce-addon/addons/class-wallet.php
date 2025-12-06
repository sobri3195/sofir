<?php
namespace Sofir\WooCommerceAddon\Addons;

class Wallet extends Addon_Base {
    public function get_id(): string {
        return 'wallet';
    }

    public function get_name(): string {
        return __( 'Wallet', 'sofir' );
    }

    public function get_description(): string {
        return __( 'Enable store wallet system for customers to add funds and use as payment method.', 'sofir' );
    }

    public function get_category(): string {
        return 'payment';
    }

    public function get_icon(): string {
        return 'dashicons-wallet';
    }

    public function get_settings(): array {
        return [
            'enable_wallet' => [
                'type' => 'checkbox',
                'label' => __( 'Enable Wallet System', 'sofir' ),
                'default' => true,
            ],
            'min_topup_amount' => [
                'type' => 'number',
                'label' => __( 'Minimum Top-up Amount', 'sofir' ),
                'default' => 10,
                'min' => 1,
                'step' => 0.01,
            ],
            'max_topup_amount' => [
                'type' => 'number',
                'label' => __( 'Maximum Top-up Amount', 'sofir' ),
                'default' => 1000,
                'min' => 1,
                'step' => 0.01,
            ],
            'topup_bonus_enabled' => [
                'type' => 'checkbox',
                'label' => __( 'Enable Top-up Bonus', 'sofir' ),
                'default' => false,
            ],
            'topup_bonus_threshold' => [
                'type' => 'number',
                'label' => __( 'Bonus Threshold Amount', 'sofir' ),
                'default' => 100,
                'min' => 1,
                'step' => 0.01,
            ],
            'topup_bonus_percentage' => [
                'type' => 'number',
                'label' => __( 'Bonus Percentage (%)', 'sofir' ),
                'default' => 5,
                'min' => 0,
                'max' => 100,
                'step' => 0.1,
            ],
            'auto_withdraw_enabled' => [
                'type' => 'checkbox',
                'label' => __( 'Enable Auto Withdraw', 'sofir' ),
                'default' => false,
            ],
            'auto_withdraw_threshold' => [
                'type' => 'number',
                'label' => __( 'Auto Withdraw Threshold', 'sofir' ),
                'default' => 500,
                'min' => 1,
                'step' => 0.01,
            ],
            'email_notifications' => [
                'type' => 'checkbox',
                'label' => __( 'Email Notifications', 'sofir' ),
                'default' => true,
            ],
        ];
    }

    public function enable(): void {
        parent::enable();
        
        \add_action( 'init', [ $this, 'register_wallet_cpt' ] );
        \add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
        \add_action( 'woocommerce_account_menu_items', [ $this, 'add_wallet_menu_item' ] );
        \add_action( 'init', [ $this, 'add_wallet_endpoint' ] );
        \add_action( 'woocommerce_account_wallet_endpoint', [ $this, 'render_wallet_page' ] );
        \add_filter( 'woocommerce_payment_gateways', [ $this, 'add_wallet_payment_gateway' ] );
        \add_action( 'wp_ajax_sofir_wallet_topup', [ $this, 'ajax_wallet_topup' ] );
        \add_action( 'wp_ajax_sofir_wallet_withdraw', [ $this, 'ajax_wallet_withdraw' ] );
        \add_action( 'wp_ajax_sofir_wallet_history', [ $this, 'ajax_wallet_history' ] );
        \add_action( 'woocommerce_thankyou', [ $this, 'process_wallet_order' ], 10, 1 );
        \add_action( 'woocommerce_before_calculate_totals', [ $this, 'apply_wallet_discount' ], 10, 1 );
        \add_filter( 'woocommerce_get_shop_coupon_data', [ $this, 'filter_wallet_coupon_data' ], 10, 2 );
        \add_shortcode( 'sofir_wallet_balance', [ $this, 'render_wallet_balance' ] );
        \add_shortcode( 'sofir_wallet_topup', [ $this, 'render_wallet_topup_form' ] );
    }

    public function disable(): void {
        parent::disable();
        
        \remove_action( 'init', [ $this, 'register_wallet_cpt' ] );
        \remove_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
        \remove_filter( 'woocommerce_account_menu_items', [ $this, 'add_wallet_menu_item' ] );
        \remove_action( 'init', [ $this, 'add_wallet_endpoint' ] );
        \remove_action( 'woocommerce_account_wallet_endpoint', [ $this, 'render_wallet_page' ] );
        \remove_filter( 'woocommerce_payment_gateways', [ $this, 'add_wallet_payment_gateway' ] );
        \remove_action( 'wp_ajax_sofir_wallet_topup', [ $this, 'ajax_wallet_topup' ] );
        \remove_action( 'wp_ajax_sofir_wallet_withdraw', [ $this, 'ajax_wallet_withdraw' ] );
        \remove_action( 'wp_ajax_sofir_wallet_history', [ $this, 'ajax_wallet_history' ] );
        \remove_action( 'woocommerce_thankyou', [ $this, 'process_wallet_order' ], 10 );
        \remove_action( 'woocommerce_before_calculate_totals', [ $this, 'apply_wallet_discount' ], 10 );
        \remove_filter( 'woocommerce_get_shop_coupon_data', [ $this, 'filter_wallet_coupon_data' ], 10 );
        \remove_shortcode( 'sofir_wallet_balance' );
        \remove_shortcode( 'sofir_wallet_topup' );
    }

    public function register_wallet_cpt(): void {
        \register_post_type( 'sofir_wallet_transaction', [
            'label' => __( 'Wallet Transactions', 'sofir' ),
            'public' => false,
            'show_ui' => true,
            'capability_type' => 'post',
            'supports' => [ 'title', 'custom-fields' ],
            'show_in_menu' => 'sofir-woocommerce-addon',
            'has_archive' => false,
            'rewrite' => false,
            'menu_icon' => 'dashicons-wallet',
        ] );
    }

    public function enqueue_scripts(): void {
        if ( \get_option( 'sofir_wc_addon_wallet_enable_wallet', true ) ) {
            \wp_enqueue_style(
                'sofir-wallet',
                SOFIR_PLUGIN_URL . '/modules/woocommerce-addon/assets/wallet.css',
                [],
                '1.0.0'
            );
            
            \wp_enqueue_script(
                'sofir-wallet',
                SOFIR_PLUGIN_URL . '/modules/woocommerce-addon/assets/wallet.js',
                [ 'jquery' ],
                '1.0.0',
                true
            );
            
            \wp_localize_script( 'sofir-wallet', 'sofirWallet', [
                'ajaxurl' => \admin_url( 'admin-ajax.php' ),
                'nonce' => \wp_create_nonce( 'sofir_wallet_nonce' ),
                'i18n' => [
                    'topup' => __( 'Top-up', 'sofir' ),
                    'withdraw' => __( 'Withdraw', 'sofir' ),
                    'loading' => __( 'Loading...', 'sofir' ),
                    'success' => __( 'Success', 'sofir' ),
                    'error' => __( 'Error', 'sofir' ),
                    'insufficient_balance' => __( 'Insufficient balance', 'sofir' ),
                ],
            ] );
        }
    }

    public function add_wallet_menu_item( $items ): array {
        $items['wallet'] = __( 'Wallet', 'sofir' );
        return $items;
    }

    public function add_wallet_endpoint(): void {
        \add_rewrite_endpoint( 'wallet', EP_ROOT | EP_PAGES );
    }

    public function render_wallet_page(): void {
        if ( ! \is_user_logged_in() ) {
            echo '<p>' . __( 'Please login to view your wallet.', 'sofir' ) . '</p>';
            echo '<a href="' . \wc_get_page_permalink( 'myaccount' ) . '" class="button">' . __( 'Login', 'sofir' ) . '</a>';
            return;
        }

        $user_id = \get_current_user_id();
        $balance = $this->get_wallet_balance( $user_id );
        $tab = isset( $_GET['tab'] ) ? $_GET['tab'] : 'overview';

        echo '<div class="sofir-wallet-dashboard">';
        echo '<h2>' . __( 'My Wallet', 'sofir' ) . '</h2>';

        // Navigation tabs
        echo '<nav class="wallet-nav">';
        echo '<ul>';
        echo '<li><a href="' . \wc_get_account_endpoint_url( 'wallet' ) . '" class="' . ( $tab === 'overview' ? 'active' : '' ) . '">' . __( 'Overview', 'sofir' ) . '</a></li>';
        echo '<li><a href="' . \add_query_arg( 'tab', 'topup', \wc_get_account_endpoint_url( 'wallet' ) ) . '" class="' . ( $tab === 'topup' ? 'active' : '' ) . '">' . __( 'Top-up', 'sofir' ) . '</a></li>';
        echo '<li><a href="' . \add_query_arg( 'tab', 'withdraw', \wc_get_account_endpoint_url( 'wallet' ) ) . '" class="' . ( $tab === 'withdraw' ? 'active' : '' ) . '">' . __( 'Withdraw', 'sofir' ) . '</a></li>';
        echo '<li><a href="' . \add_query_arg( 'tab', 'history', \wc_get_account_endpoint_url( 'wallet' ) ) . '" class="' . ( $tab === 'history' ? 'active' : '' ) . '">' . __( 'History', 'sofir' ) . '</a></li>';
        echo '</ul>';
        echo '</nav>';

        // Tab content
        echo '<div class="wallet-content">';
        switch ( $tab ) {
            case 'overview':
                $this->render_wallet_overview( $balance );
                break;
            case 'topup':
                $this->render_wallet_topup();
                break;
            case 'withdraw':
                $this->render_wallet_withdraw( $balance );
                break;
            case 'history':
                $this->render_wallet_history();
                break;
        }
        echo '</div>';

        echo '</div>';
    }

    private function render_wallet_overview( $balance ): void {
        echo '<div class="wallet-overview">';
        echo '<div class="balance-card">';
        echo '<h3>' . __( 'Current Balance', 'sofir' ) . '</h3>';
        echo '<div class="balance-amount">' . \wc_price( $balance ) . '</div>';
        echo '</div>';

        echo '<div class="wallet-stats">';
        echo '<div class="stat-item">';
        echo '<h4>' . __( 'This Month', 'sofir' ) . '</h4>';
        $monthly_topup = $this->get_monthly_total( \get_current_user_id(), 'topup' );
        $monthly_spent = $this->get_monthly_total( \get_current_user_id(), 'spent' );
        echo '<p>' . __( 'Top-up:', 'sofir' ) . ' ' . \wc_price( $monthly_topup ) . '</p>';
        echo '<p>' . __( 'Spent:', 'sofir' ) . ' ' . \wc_price( $monthly_spent ) . '</p>';
        echo '</div>';

        echo '<div class="stat-item">';
        echo '<h4>' . __( 'Quick Actions', 'sofir' ) . '</h4>';
        echo '<a href="' . \add_query_arg( 'tab', 'topup', \wc_get_account_endpoint_url( 'wallet' ) ) . '" class="button">' . __( 'Add Funds', 'sofir' ) . '</a> ';
        echo '<a href="' . \add_query_arg( 'tab', 'withdraw', \wc_get_account_endpoint_url( 'wallet' ) ) . '" class="button">' . __( 'Withdraw', 'sofir' ) . '</a>';
        echo '</div>';
        echo '</div>';

        echo '</div>';
    }

    private function render_wallet_topup(): void {
        $min_amount = \get_option( 'sofir_wc_addon_wallet_min_topup_amount', 10 );
        $max_amount = \get_option( 'sofir_wc_addon_wallet_max_topup_amount', 1000 );
        $bonus_enabled = \get_option( 'sofir_wc_addon_wallet_topup_bonus_enabled', false );
        $bonus_threshold = \get_option( 'sofir_wc_addon_wallet_topup_bonus_threshold', 100 );
        $bonus_percentage = \get_option( 'sofir_wc_addon_wallet_topup_bonus_percentage', 5 );

        echo '<div class="wallet-topup">';
        echo '<h3>' . __( 'Add Funds to Wallet', 'sofir' ) . '</h3>';

        if ( $bonus_enabled ) {
            echo '<div class="bonus-info">';
            echo '<p><strong>' . __( 'Bonus Offer!', 'sofir' ) . '</strong></p>';
            echo '<p>' . sprintf( __( 'Get %d%% bonus on top-ups of %s or more!', 'sofir' ), $bonus_percentage, \wc_price( $bonus_threshold ) ) . '</p>';
            echo '</div>';
        }

        echo '<form id="wallet-topup-form" class="wallet-form">';
        echo '<div class="form-row">';
        echo '<label for="topup-amount">' . __( 'Amount', 'sofir' ) . '</label>';
        echo '<input type="number" id="topup-amount" name="amount" min="' . $min_amount . '" max="' . $max_amount . '" step="0.01" required>';
        echo '<small>' . sprintf( __( 'Minimum: %s, Maximum: %s', 'sofir' ), \wc_price( $min_amount ), \wc_price( $max_amount ) ) . '</small>';
        echo '</div>';

        echo '<div class="form-row">';
        echo '<label for="topup-payment-method">' . __( 'Payment Method', 'sofir' ) . '</label>';
        echo '<select id="topup-payment-method" name="payment_method" required>';
        
        $available_gateways = \WC()->payment_gateways->get_available_payment_gateways();
        foreach ( $available_gateways as $gateway ) {
            if ( $gateway->id !== 'sofir_wallet' ) { // Exclude wallet itself
                echo '<option value="' . esc_attr( $gateway->id ) . '">' . esc_html( $gateway->get_title() ) . '</option>';
            }
        }
        echo '</select>';
        echo '</div>';

        echo '<div class="form-row">';
        echo '<label for="topup-bonus-preview">' . __( 'Bonus Amount', 'sofir' ) . '</label>';
        echo '<input type="text" id="topup-bonus-preview" readonly placeholder="' . __( 'Bonus will be calculated automatically', 'sofir' ) . '">';
        echo '</div>';

        echo '<div class="form-row">';
        echo '<button type="submit" class="button alt">' . __( 'Add Funds', 'sofir' ) . '</button>';
        echo '</div>';
        echo '</form>';
        echo '</div>';
    }

    private function render_wallet_withdraw( $balance ): void {
        $auto_withdraw_enabled = \get_option( 'sofir_wc_addon_wallet_auto_withdraw_enabled', false );
        $auto_withdraw_threshold = \get_option( 'sofir_wc_addon_wallet_auto_withdraw_threshold', 500 );

        echo '<div class="wallet-withdraw">';
        echo '<h3>' . __( 'Withdraw Funds', 'sofir' ) . '</h3>';

        if ( $auto_withdraw_enabled && $balance >= $auto_withdraw_threshold ) {
            echo '<div class="auto-withdraw-notice">';
            echo '<p><strong>' . __( 'Auto Withdraw Available', 'sofir' ) . '</strong></p>';
            echo '<p>' . sprintf( __( 'Your balance exceeds the auto-withdraw threshold of %s. You can request automatic withdrawal.', 'sofir' ), \wc_price( $auto_withdraw_threshold ) ) . '</p>';
            echo '</div>';
        }

        echo '<form id="wallet-withdraw-form" class="wallet-form">';
        echo '<div class="form-row">';
        echo '<label for="withdraw-amount">' . __( 'Amount', 'sofir' ) . '</label>';
        echo '<input type="number" id="withdraw-amount" name="amount" min="1" max="' . $balance . '" step="0.01" required>';
        echo '<small>' . sprintf( __( 'Available balance: %s', 'sofir' ), \wc_price( $balance ) ) . '</small>';
        echo '</div>';

        echo '<div class="form-row">';
        echo '<label for="withdraw-method">' . __( 'Withdrawal Method', 'sofir' ) . '</label>';
        echo '<select id="withdraw-method" name="withdraw_method" required>';
        echo '<option value="bank_transfer">' . __( 'Bank Transfer', 'sofir' ) . '</option>';
        echo '<option value="paypal">' . __( 'PayPal', 'sofir' ) . '</option>';
        echo '<option value="check">' . __( 'Check', 'sofir' ) . '</option>';
        echo '</select>';
        echo '</div>';

        echo '<div class="form-row">';
        echo '<label for="withdraw-details">' . __( 'Withdrawal Details', 'sofir' ) . '</label>';
        echo '<textarea id="withdraw-details" name="withdraw_details" rows="4" placeholder="' . __( 'Enter your bank account details, PayPal email, or check mailing address', 'sofir' ) . '" required></textarea>';
        echo '</div>';

        echo '<div class="form-row">';
        echo '<button type="submit" class="button alt">' . __( 'Request Withdrawal', 'sofir' ) . '</button>';
        echo '</div>';
        echo '</form>';
        echo '</div>';
    }

    private function render_wallet_history(): void {
        $user_id = \get_current_user_id();
        $transactions = $this->get_wallet_transactions( $user_id );

        echo '<div class="wallet-history">';
        echo '<h3>' . __( 'Transaction History', 'sofir' ) . '</h3>';

        if ( ! empty( $transactions ) ) {
            echo '<div class="transactions-table">';
            echo '<table class="woocommerce-table woocommerce-table--wallet-transactions">';
            echo '<thead><tr>';
            echo '<th>' . __( 'Date', 'sofir' ) . '</th>';
            echo '<th>' . __( 'Type', 'sofir' ) . '</th>';
            echo '<th>' . __( 'Description', 'sofir' ) . '</th>';
            echo '<th>' . __( 'Amount', 'sofir' ) . '</th>';
            echo '<th>' . __( 'Balance', 'sofir' ) . '</th>';
            echo '<th>' . __( 'Status', 'sofir' ) . '</th>';
            echo '</tr></thead>';
            echo '<tbody>';

            foreach ( $transactions as $transaction ) {
                $type = \get_post_meta( $transaction->ID, '_transaction_type', true );
                $amount = \get_post_meta( $transaction->ID, '_amount', true );
                $balance_after = \get_post_meta( $transaction->ID, '_balance_after', true );
                $status = \get_post_meta( $transaction->ID, '_status', true );
                
                $amount_class = ( $type === 'topup' || $type === 'bonus' ) ? 'positive' : 'negative';
                $amount_prefix = ( $type === 'topup' || $type === 'bonus' ) ? '+' : '-';
                
                echo '<tr>';
                echo '<td>' . \wc_format_datetime( $transaction->post_date ) . '</td>';
                echo '<td><span class="transaction-type ' . esc_attr( $type ) . '">' . \ucfirst( $type ) . '</span></td>';
                echo '<td>' . \get_the_title( $transaction->ID ) . '</td>';
                echo '<td class="amount ' . esc_attr( $amount_class ) . '">' . $amount_prefix . \wc_price( $amount ) . '</td>';
                echo '<td>' . \wc_price( $balance_after ) . '</td>';
                echo '<td><span class="status ' . esc_attr( $status ) . '">' . \ucfirst( $status ) . '</span></td>';
                echo '</tr>';
            }

            echo '</tbody></table>';
            echo '</div>';
        } else {
            echo '<p>' . __( 'No transactions found.', 'sofir' ) . '</p>';
        }

        echo '</div>';
    }

    public function get_wallet_balance( $user_id ): float {
        $balance = \get_user_meta( $user_id, '_sofir_wallet_balance', true );
        return (float) $balance ?: 0;
    }

    public function update_wallet_balance( $user_id, $amount, $type ): bool {
        $current_balance = $this->get_wallet_balance( $user_id );
        
        if ( $type === 'topup' || $type === 'bonus' ) {
            $new_balance = $current_balance + $amount;
        } elseif ( $type === 'spent' || $type === 'withdraw' ) {
            if ( $current_balance < $amount ) {
                return false; // Insufficient balance
            }
            $new_balance = $current_balance - $amount;
        } else {
            return false;
        }

        \update_user_meta( $user_id, '_sofir_wallet_balance', $new_balance );
        return true;
    }

    public function create_wallet_transaction( $user_id, $type, $amount, $description, $status = 'completed' ): int {
        $balance_before = $this->get_wallet_balance( $user_id );
        $balance_after = $balance_before;

        if ( $type === 'topup' || $type === 'bonus' ) {
            $balance_after += $amount;
        } elseif ( $type === 'spent' || $type === 'withdraw' ) {
            $balance_after -= $amount;
        }

        $transaction_id = \wp_insert_post( [
            'post_type' => 'sofir_wallet_transaction',
            'post_title' => $description,
            'post_status' => 'publish',
            'post_author' => $user_id,
            'meta_input' => [
                '_user_id' => $user_id,
                '_transaction_type' => $type,
                '_amount' => $amount,
                '_balance_before' => $balance_before,
                '_balance_after' => $balance_after,
                '_status' => $status,
            ],
        ] );

        if ( $transaction_id && $status === 'completed' ) {
            \update_user_meta( $user_id, '_sofir_wallet_balance', $balance_after );
            
            // Send email notification if enabled
            if ( \get_option( 'sofir_wc_addon_wallet_email_notifications', true ) ) {
                $this->send_wallet_notification( $user_id, $type, $amount, $balance_after );
            }
        }

        return $transaction_id;
    }

    private function get_wallet_transactions( $user_id, $limit = 50 ): array {
        return \get_posts( [
            'post_type' => 'sofir_wallet_transaction',
            'author' => $user_id,
            'posts_per_page' => $limit,
            'orderby' => 'date',
            'order' => 'DESC',
        ] );
    }

    private function get_monthly_total( $user_id, $type ): float {
        $current_month = date( 'Y-m-01' );
        $current_month_end = date( 'Y-m-t' );

        $transactions = \get_posts( [
            'post_type' => 'sofir_wallet_transaction',
            'author' => $user_id,
            'posts_per_page' => -1,
            'orderby' => 'date',
            'order' => 'DESC',
            'meta_query' => [
                [
                    'key' => '_transaction_type',
                    'value' => $type,
                ],
            ],
            'date_query' => [
                [
                    'after' => $current_month,
                    'before' => $current_month_end,
                    'inclusive' => true,
                ],
            ],
        ] );

        $total = 0;
        foreach ( $transactions as $transaction ) {
            $total += (float) \get_post_meta( $transaction->ID, '_amount', true );
        }

        return $total;
    }

    public function ajax_wallet_topup(): void {
        \check_ajax_referer( 'sofir_wallet_nonce', 'nonce' );

        if ( ! \is_user_logged_in() ) {
            \wp_send_json_error( [ 'message' => __( 'Please login', 'sofir' ) ] );
        }

        $amount = isset( $_POST['amount'] ) ? floatval( $_POST['amount'] ) : 0;
        $payment_method = isset( $_POST['payment_method'] ) ? \sanitize_text_field( $_POST['payment_method'] ) : '';

        $min_amount = \get_option( 'sofir_wc_addon_wallet_min_topup_amount', 10 );
        $max_amount = \get_option( 'sofir_wc_addon_wallet_max_topup_amount', 1000 );

        if ( $amount < $min_amount || $amount > $max_amount ) {
            \wp_send_json_error( [ 'message' => __( 'Invalid amount', 'sofir' ) ] );
        }

        // Calculate bonus
        $bonus_amount = 0;
        if ( \get_option( 'sofir_wc_addon_wallet_topup_bonus_enabled', false ) ) {
            $bonus_threshold = \get_option( 'sofir_wc_addon_wallet_topup_bonus_threshold', 100 );
            $bonus_percentage = \get_option( 'sofir_wc_addon_wallet_topup_bonus_percentage', 5 );
            
            if ( $amount >= $bonus_threshold ) {
                $bonus_amount = $amount * ( $bonus_percentage / 100 );
            }
        }

        $user_id = \get_current_user_id();
        
        // Create top-up transaction
        $transaction_id = $this->create_wallet_transaction(
            $user_id,
            'topup',
            $amount,
            sprintf( __( 'Wallet top-up of %s', 'sofir' ), \wc_price( $amount ) ),
            'pending'
        );

        if ( $bonus_amount > 0 ) {
            $this->create_wallet_transaction(
                $user_id,
                'bonus',
                $bonus_amount,
                sprintf( __( 'Bonus for top-up of %s', 'sofir' ), \wc_price( $amount ) ),
                'pending'
            );
        }

        // Process payment (simplified - in real implementation, integrate with payment gateway)
        $this->process_wallet_payment( $transaction_id, $payment_method, $amount );

        \wp_send_json_success( [
            'message' => __( 'Top-up request submitted', 'sofir' ),
            'transaction_id' => $transaction_id,
            'bonus_amount' => $bonus_amount,
        ] );
    }

    public function ajax_wallet_withdraw(): void {
        \check_ajax_referer( 'sofir_wallet_nonce', 'nonce' );

        if ( ! \is_user_logged_in() ) {
            \wp_send_json_error( [ 'message' => __( 'Please login', 'sofir' ) ] );
        }

        $amount = isset( $_POST['amount'] ) ? floatval( $_POST['amount'] ) : 0;
        $withdraw_method = isset( $_POST['withdraw_method'] ) ? \sanitize_text_field( $_POST['withdraw_method'] ) : '';
        $withdraw_details = isset( $_POST['withdraw_details'] ) ? \sanitize_textarea_field( $_POST['withdraw_details'] ) : '';

        $user_id = \get_current_user_id();
        $balance = $this->get_wallet_balance( $user_id );

        if ( $amount > $balance ) {
            \wp_send_json_error( [ 'message' => __( 'Insufficient balance', 'sofir' ) ] );
        }

        $transaction_id = $this->create_wallet_transaction(
            $user_id,
            'withdraw',
            $amount,
            sprintf( __( 'Withdrawal via %s', 'sofir' ), $withdraw_method ),
            'pending'
        );

        // Store withdrawal details
        \update_post_meta( $transaction_id, '_withdraw_method', $withdraw_method );
        \update_post_meta( $transaction_id, '_withdraw_details', $withdraw_details );

        \wp_send_json_success( [
            'message' => __( 'Withdrawal request submitted', 'sofir' ),
            'transaction_id' => $transaction_id,
        ] );
    }

    public function ajax_wallet_history(): void {
        \check_ajax_referer( 'sofir_wallet_nonce', 'nonce' );

        if ( ! \is_user_logged_in() ) {
            \wp_send_json_error( [ 'message' => __( 'Please login', 'sofir' ) ] );
        }

        $user_id = \get_current_user_id();
        $transactions = $this->get_wallet_transactions( $user_id );
        $data = [];

        foreach ( $transactions as $transaction ) {
            $type = \get_post_meta( $transaction->ID, '_transaction_type', true );
            $amount = \get_post_meta( $transaction->ID, '_amount', true );
            $balance_after = \get_post_meta( $transaction->ID, '_balance_after', true );
            $status = \get_post_meta( $transaction->ID, '_status', true );
            
            $data[] = [
                'id' => $transaction->ID,
                'date' => \wc_format_datetime( $transaction->post_date ),
                'type' => $type,
                'description' => \get_the_title( $transaction->ID ),
                'amount' => $amount,
                'balance_after' => $balance_after,
                'status' => $status,
            ];
        }

        \wp_send_json_success( $data );
    }

    private function process_wallet_payment( $transaction_id, $payment_method, $amount ): void {
        // This is a simplified payment processing
        // In real implementation, integrate with the actual payment gateway
        
        // For demo purposes, mark as completed immediately
        $this->update_transaction_status( $transaction_id, 'completed' );
    }

    private function update_transaction_status( $transaction_id, $status ): void {
        \update_post_meta( $transaction_id, '_status', $status );
        
        if ( $status === 'completed' ) {
            $user_id = \get_post_meta( $transaction_id, '_user_id', true );
            $type = \get_post_meta( $transaction_id, '_transaction_type', true );
            $amount = \get_post_meta( $transaction_id, '_amount', true );
            
            $this->update_wallet_balance( $user_id, $amount, $type );
        }
    }

    private function send_wallet_notification( $user_id, $type, $amount, $balance ): void {
        $user = \get_userdata( $user_id );
        if ( ! $user ) {
            return;
        }

        $subject = sprintf( __( 'Wallet %s Notification', 'sofir' ), \ucfirst( $type ) );
        
        switch ( $type ) {
            case 'topup':
                $message = sprintf( __( 'Your wallet has been topped up with %s. Current balance: %s', 'sofir' ), \wc_price( $amount ), \wc_price( $balance ) );
                break;
            case 'spent':
                $message = sprintf( __( '%s has been spent from your wallet. Current balance: %s', 'sofir' ), \wc_price( $amount ), \wc_price( $balance ) );
                break;
            case 'bonus':
                $message = sprintf( __( 'You have received a bonus of %s. Current balance: %s', 'sofir' ), \wc_price( $amount ), \wc_price( $balance ) );
                break;
            case 'withdraw':
                $message = sprintf( __( 'Withdrawal of %s has been processed. Current balance: %s', 'sofir' ), \wc_price( $amount ), \wc_price( $balance ) );
                break;
            default:
                $message = sprintf( __( 'Your wallet balance is now %s', 'sofir' ), \wc_price( $balance ) );
        }

        \wp_mail( $user->user_email, $subject, $message );
    }

    public function add_wallet_payment_gateway( $gateways ): array {
        if ( \get_option( 'sofir_wc_addon_wallet_enable_wallet', true ) ) {
            $gateways[] = new \Sofir\WooCommerceAddon\Gateways\Wallet_Gateway();
        }
        return $gateways;
    }

    public function render_wallet_balance( $atts ): string {
        if ( ! \is_user_logged_in() ) {
            return '';
        }

        $atts = \shortcode_atts( [
            'user_id' => \get_current_user_id(),
        ], $atts );

        $balance = $this->get_wallet_balance( $atts['user_id'] );
        return '<span class="sofir-wallet-balance">' . \wc_price( $balance ) . '</span>';
    }

    public function render_wallet_topup_form( $atts ): string {
        ob_start();
        $this->render_wallet_topup();
        return ob_get_clean();
    }
}