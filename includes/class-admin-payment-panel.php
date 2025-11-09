<?php
namespace Sofir\Admin;

use Sofir\Payments\Manager as PaymentManager;

class PaymentPanel {
    private static ?PaymentPanel $instance = null;

    public static function instance(): PaymentPanel {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function boot(): void {
        \add_action( 'admin_post_sofir_save_payment_settings', [ $this, 'handle_save_settings' ] );
    }

    public function render(): void {
        $manager = PaymentManager::instance();
        $settings = $manager->get_settings();
        $notice = isset( $_GET['sofir_notice'] ) ? \sanitize_key( $_GET['sofir_notice'] ) : '';

        if ( $notice ) {
            $this->render_notice( $notice );
        }

        echo '<div class="sofir-payment-panel">';
        
        $this->render_overview_section();
        $this->render_settings_form( $settings );
        $this->render_transactions_section();
        $this->render_documentation_section();

        echo '</div>';
    }

    private function render_overview_section(): void {
        echo '<div class="sofir-card" style="margin-bottom: 20px;">';
        echo '<h2>💳 ' . \esc_html__( 'Payment Gateway Integration', 'sofir' ) . '</h2>';
        echo '<p>' . \esc_html__( 'SOFIR mendukung integrasi dengan payment gateway lokal Indonesia untuk memudahkan pembayaran online.', 'sofir' ) . '</p>';
        
        echo '<div class="sofir-payment-features" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px; margin-top: 20px;">';
        
        $features = [
            [
                'icon' => '💵',
                'title' => \__( 'Manual Payment', 'sofir' ),
                'desc' => \__( 'Transfer bank manual dengan instruksi', 'sofir' ),
            ],
            [
                'icon' => '🏦',
                'title' => 'Duitku',
                'desc' => \__( 'Virtual Account, E-wallet, Minimarket', 'sofir' ),
            ],
            [
                'icon' => '💳',
                'title' => 'Xendit',
                'desc' => \__( 'VA, E-wallet, Cards, QRIS', 'sofir' ),
            ],
            [
                'icon' => '🛒',
                'title' => 'Midtrans',
                'desc' => \__( 'Snap Payment - All in One', 'sofir' ),
            ],
        ];

        foreach ( $features as $feature ) {
            echo '<div class="sofir-feature-box" style="background: #f8f9fa; padding: 20px; border-radius: 8px; text-align: center;">';
            echo '<div style="font-size: 32px; margin-bottom: 10px;">' . $feature['icon'] . '</div>';
            echo '<h3 style="margin: 0 0 8px 0; font-size: 16px;">' . \esc_html( $feature['title'] ) . '</h3>';
            echo '<p style="margin: 0; color: #666; font-size: 13px;">' . \esc_html( $feature['desc'] ) . '</p>';
            echo '</div>';
        }

        echo '</div>';
        echo '</div>';
    }

    private function render_settings_form( array $settings ): void {
        echo '<form method="post" action="' . \esc_url( \admin_url( 'admin-post.php' ) ) . '" class="sofir-payment-settings-form">';
        echo '<input type="hidden" name="action" value="sofir_save_payment_settings" />';
        \wp_nonce_field( 'sofir_payment_settings', '_sofir_nonce' );

        echo '<div class="sofir-grid" style="grid-template-columns: 1fr 1fr; gap: 20px;">';

        $this->render_general_settings( $settings );
        $this->render_manual_payment_settings( $settings );
        $this->render_duitku_settings( $settings );
        $this->render_xendit_settings( $settings );
        $this->render_midtrans_settings( $settings );
        $this->render_webhook_urls();

        echo '</div>';

        echo '<div style="margin-top: 20px;">';
        echo '<button type="submit" class="button button-primary button-large">💾 ' . \esc_html__( 'Save Payment Settings', 'sofir' ) . '</button>';
        echo '</div>';

        echo '</form>';
    }

    private function render_general_settings( array $settings ): void {
        echo '<div class="sofir-card">';
        echo '<h2>⚙️ ' . \esc_html__( 'General Settings', 'sofir' ) . '</h2>';
        
        echo '<div class="sofir-field-group">';
        
        echo '<label class="sofir-field">';
        echo '<span>' . \esc_html__( 'Currency', 'sofir' ) . '</span>';
        echo '<select name="payment_currency" class="regular-text">';
        $currencies = [ 'IDR' => 'Indonesian Rupiah (IDR)', 'USD' => 'US Dollar (USD)', 'MYR' => 'Malaysian Ringgit (MYR)' ];
        foreach ( $currencies as $code => $label ) {
            echo '<option value="' . \esc_attr( $code ) . '" ' . \selected( $settings['currency'] ?? 'IDR', $code, false ) . '>' . \esc_html( $label ) . '</option>';
        }
        echo '</select>';
        echo '</label>';

        echo '</div>';
        echo '</div>';
    }

    private function render_manual_payment_settings( array $settings ): void {
        echo '<div class="sofir-card">';
        echo '<div class="sofir-card-header" style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 15px;">';
        echo '<h2 style="margin: 0;">💵 ' . \esc_html__( 'Manual Payment', 'sofir' ) . '</h2>';
        echo '<label class="sofir-toggle-switch">';
        echo '<input type="checkbox" name="enable_manual" value="1" ' . \checked( $settings['manual_enabled'] ?? true, true, false ) . ' />';
        echo '<span class="slider"></span>';
        echo '</label>';
        echo '</div>';
        
        echo '<p class="description">' . \esc_html__( 'Pelanggan melakukan transfer manual dan mengirimkan bukti pembayaran.', 'sofir' ) . '</p>';
        
        echo '<div class="sofir-info-box" style="background: #e7f3ff; border-left: 4px solid #2196F3; padding: 12px; margin-top: 10px;">';
        echo '<strong>ℹ️ ' . \esc_html__( 'Info:', 'sofir' ) . '</strong> ';
        echo \esc_html__( 'Tidak memerlukan konfigurasi API. Aktifkan untuk menerima transfer bank manual.', 'sofir' );
        echo '</div>';
        
        echo '</div>';
    }

    private function render_duitku_settings( array $settings ): void {
        echo '<div class="sofir-card">';
        echo '<div class="sofir-card-header" style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 15px;">';
        echo '<h2 style="margin: 0;">🏦 Duitku</h2>';
        echo '<label class="sofir-toggle-switch">';
        echo '<input type="checkbox" name="enable_duitku" value="1" ' . \checked( $settings['duitku_enabled'] ?? false, true, false ) . ' />';
        echo '<span class="slider"></span>';
        echo '</label>';
        echo '</div>';
        
        echo '<p class="description">' . \esc_html__( 'Payment gateway Indonesia dengan berbagai metode pembayaran.', 'sofir' ) . '</p>';
        
        echo '<div class="sofir-field-group" style="margin-top: 15px;">';
        
        echo '<label class="sofir-field">';
        echo '<span>' . \esc_html__( 'Merchant Code', 'sofir' ) . ' <span class="required">*</span></span>';
        echo '<input type="text" name="duitku_merchant_code" class="regular-text" value="' . \esc_attr( $settings['duitku_merchant_code'] ?? '' ) . '" placeholder="D12345" />';
        echo '</label>';

        echo '<label class="sofir-field">';
        echo '<span>' . \esc_html__( 'API Key', 'sofir' ) . ' <span class="required">*</span></span>';
        echo '<input type="text" name="duitku_api_key" class="regular-text" value="' . \esc_attr( $settings['duitku_api_key'] ?? '' ) . '" placeholder="xxxxxxxxxxxxxxxx" />';
        echo '</label>';

        echo '</div>';
        
        echo '<div class="sofir-help-box" style="background: #fff3cd; border-left: 4px solid #ffc107; padding: 12px; margin-top: 10px;">';
        echo '<strong>📖 ' . \esc_html__( 'Cara mendapatkan:', 'sofir' ) . '</strong><br>';
        echo '1. Daftar di <a href="https://duitku.com" target="_blank">duitku.com</a><br>';
        echo '2. Login ke dashboard merchant<br>';
        echo '3. Buka menu <strong>Settings → API</strong><br>';
        echo '4. Copy Merchant Code dan API Key';
        echo '</div>';
        
        echo '</div>';
    }

    private function render_xendit_settings( array $settings ): void {
        echo '<div class="sofir-card">';
        echo '<div class="sofir-card-header" style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 15px;">';
        echo '<h2 style="margin: 0;">💳 Xendit</h2>';
        echo '<label class="sofir-toggle-switch">';
        echo '<input type="checkbox" name="enable_xendit" value="1" ' . \checked( $settings['xendit_enabled'] ?? false, true, false ) . ' />';
        echo '<span class="slider"></span>';
        echo '</label>';
        echo '</div>';
        
        echo '<p class="description">' . \esc_html__( 'Platform pembayaran dengan VA, e-wallet, kartu kredit, dan QRIS.', 'sofir' ) . '</p>';
        
        echo '<div class="sofir-field-group" style="margin-top: 15px;">';
        
        echo '<label class="sofir-field">';
        echo '<span>' . \esc_html__( 'API Key', 'sofir' ) . ' <span class="required">*</span></span>';
        echo '<input type="text" name="xendit_api_key" class="regular-text" value="' . \esc_attr( $settings['xendit_api_key'] ?? '' ) . '" placeholder="xnd_development_xxxx atau xnd_production_xxxx" />';
        echo '<small class="description">' . \esc_html__( 'Gunakan xnd_development_* untuk testing, xnd_production_* untuk live.', 'sofir' ) . '</small>';
        echo '</label>';

        echo '</div>';
        
        echo '<div class="sofir-help-box" style="background: #fff3cd; border-left: 4px solid #ffc107; padding: 12px; margin-top: 10px;">';
        echo '<strong>📖 ' . \esc_html__( 'Cara mendapatkan:', 'sofir' ) . '</strong><br>';
        echo '1. Daftar di <a href="https://dashboard.xendit.co" target="_blank">dashboard.xendit.co</a><br>';
        echo '2. Verifikasi akun bisnis Anda<br>';
        echo '3. Buka menu <strong>Settings → Developers → API Keys</strong><br>';
        echo '4. Copy Secret Key (gunakan yang Development untuk testing)';
        echo '</div>';
        
        echo '</div>';
    }

    private function render_midtrans_settings( array $settings ): void {
        echo '<div class="sofir-card">';
        echo '<div class="sofir-card-header" style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 15px;">';
        echo '<h2 style="margin: 0;">🛒 Midtrans</h2>';
        echo '<label class="sofir-toggle-switch">';
        echo '<input type="checkbox" name="enable_midtrans" value="1" ' . \checked( $settings['midtrans_enabled'] ?? false, true, false ) . ' />';
        echo '<span class="slider"></span>';
        echo '</label>';
        echo '</div>';
        
        echo '<p class="description">' . \esc_html__( 'Snap Payment - Semua metode pembayaran dalam satu halaman.', 'sofir' ) . '</p>';
        
        echo '<div class="sofir-field-group" style="margin-top: 15px;">';
        
        echo '<label class="sofir-field">';
        echo '<span>' . \esc_html__( 'Server Key', 'sofir' ) . ' <span class="required">*</span></span>';
        echo '<input type="text" name="midtrans_server_key" class="regular-text" value="' . \esc_attr( $settings['midtrans_server_key'] ?? '' ) . '" placeholder="SB-Mid-server-xxxxxxxx" />';
        echo '</label>';

        echo '<label class="sofir-field">';
        echo '<span>' . \esc_html__( 'Client Key', 'sofir' ) . ' <span class="required">*</span></span>';
        echo '<input type="text" name="midtrans_client_key" class="regular-text" value="' . \esc_attr( $settings['midtrans_client_key'] ?? '' ) . '" placeholder="SB-Mid-client-xxxxxxxx" />';
        echo '</label>';

        echo '<label class="sofir-toggle" style="display: flex; align-items: center; gap: 10px; margin-top: 10px;">';
        echo '<input type="checkbox" name="midtrans_sandbox" value="1" ' . \checked( $settings['midtrans_sandbox'] ?? true, true, false ) . ' />';
        echo '<span>' . \esc_html__( 'Sandbox Mode (Testing)', 'sofir' ) . '</span>';
        echo '</label>';

        echo '</div>';
        
        echo '<div class="sofir-help-box" style="background: #fff3cd; border-left: 4px solid #ffc107; padding: 12px; margin-top: 10px;">';
        echo '<strong>📖 ' . \esc_html__( 'Cara mendapatkan:', 'sofir' ) . '</strong><br>';
        echo '1. Daftar di <a href="https://dashboard.midtrans.com" target="_blank">dashboard.midtrans.com</a><br>';
        echo '2. Login dan pilih environment (Sandbox/Production)<br>';
        echo '3. Buka menu <strong>Settings → Access Keys</strong><br>';
        echo '4. Copy Server Key dan Client Key<br>';
        echo '5. Nonaktifkan Sandbox Mode saat go live';
        echo '</div>';
        
        echo '</div>';
    }

    private function render_webhook_urls(): void {
        $site_url = \home_url();
        
        echo '<div class="sofir-card" style="grid-column: 1 / -1;">';
        echo '<h2>🔗 ' . \esc_html__( 'Webhook URLs', 'sofir' ) . '</h2>';
        echo '<p>' . \esc_html__( 'Salin URL webhook berikut dan masukkan ke dashboard payment gateway Anda untuk notifikasi status pembayaran otomatis.', 'sofir' ) . '</p>';
        
        echo '<div class="sofir-webhook-urls" style="margin-top: 15px;">';
        
        $webhooks = [
            [
                'gateway' => 'Duitku',
                'url' => \rest_url( 'sofir/v1/payments/webhook/duitku' ),
                'field' => 'Callback URL / Return URL',
            ],
            [
                'gateway' => 'Xendit',
                'url' => \rest_url( 'sofir/v1/payments/webhook/xendit' ),
                'field' => 'Webhook URL (di Settings → Webhooks)',
            ],
            [
                'gateway' => 'Midtrans',
                'url' => \rest_url( 'sofir/v1/payments/webhook/midtrans' ),
                'field' => 'Notification URL (di Settings → Configuration)',
            ],
        ];

        foreach ( $webhooks as $webhook ) {
            echo '<div class="sofir-webhook-item" style="background: #f8f9fa; padding: 15px; border-radius: 6px; margin-bottom: 10px;">';
            echo '<div style="display: flex; align-items: center; justify-content: space-between;">';
            echo '<div>';
            echo '<strong>' . \esc_html( $webhook['gateway'] ) . '</strong>';
            echo '<br><small style="color: #666;">' . \esc_html( $webhook['field'] ) . '</small>';
            echo '</div>';
            echo '<button type="button" class="button button-small sofir-copy-webhook" data-url="' . \esc_attr( $webhook['url'] ) . '">📋 ' . \esc_html__( 'Copy', 'sofir' ) . '</button>';
            echo '</div>';
            echo '<input type="text" readonly value="' . \esc_attr( $webhook['url'] ) . '" style="width: 100%; margin-top: 10px; background: #fff; padding: 8px; border: 1px solid #ddd; border-radius: 4px; font-size: 12px; font-family: monospace;" />';
            echo '</div>';
        }

        echo '</div>';
        echo '</div>';
    }

    private function render_transactions_section(): void {
        $transactions = \get_option( 'sofir_payment_transactions', [] );
        
        echo '<div class="sofir-card" style="margin-top: 20px;">';
        echo '<h2>📊 ' . \esc_html__( 'Recent Transactions', 'sofir' ) . '</h2>';
        
        if ( empty( $transactions ) ) {
            echo '<div style="text-align: center; padding: 40px; color: #666;">';
            echo '<div style="font-size: 48px; margin-bottom: 15px;">💳</div>';
            echo '<p>' . \esc_html__( 'Belum ada transaksi pembayaran.', 'sofir' ) . '</p>';
            echo '<p class="description">' . \esc_html__( 'Transaksi akan muncul di sini setelah pelanggan melakukan pembayaran.', 'sofir' ) . '</p>';
            echo '</div>';
        } else {
            $recent_transactions = array_slice( array_reverse( $transactions ), 0, 10 );
            
            echo '<div style="overflow-x: auto;">';
            echo '<table class="widefat striped">';
            echo '<thead>';
            echo '<tr>';
            echo '<th>' . \esc_html__( 'Transaction ID', 'sofir' ) . '</th>';
            echo '<th>' . \esc_html__( 'Gateway', 'sofir' ) . '</th>';
            echo '<th>' . \esc_html__( 'Item', 'sofir' ) . '</th>';
            echo '<th>' . \esc_html__( 'Amount', 'sofir' ) . '</th>';
            echo '<th>' . \esc_html__( 'Status', 'sofir' ) . '</th>';
            echo '<th>' . \esc_html__( 'Date', 'sofir' ) . '</th>';
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';
            
            foreach ( $recent_transactions as $transaction ) {
                $status_class = '';
                $status_label = $transaction['status'];
                
                switch ( $transaction['status'] ) {
                    case 'completed':
                        $status_class = 'sofir-status-success';
                        $status_label = '✅ ' . \__( 'Completed', 'sofir' );
                        break;
                    case 'pending':
                        $status_class = 'sofir-status-pending';
                        $status_label = '⏳ ' . \__( 'Pending', 'sofir' );
                        break;
                    case 'failed':
                        $status_class = 'sofir-status-failed';
                        $status_label = '❌ ' . \__( 'Failed', 'sofir' );
                        break;
                }
                
                echo '<tr>';
                echo '<td><code>' . \esc_html( $transaction['id'] ) . '</code></td>';
                echo '<td><strong>' . \esc_html( ucfirst( $transaction['gateway'] ) ) . '</strong></td>';
                echo '<td>' . \esc_html( $transaction['item_name'] ) . '</td>';
                echo '<td><strong>' . \esc_html( $this->format_price( $transaction['amount'], $transaction ) ) . '</strong></td>';
                echo '<td><span class="' . \esc_attr( $status_class ) . '" style="padding: 4px 8px; border-radius: 4px; font-size: 12px; display: inline-block;">' . $status_label . '</span></td>';
                echo '<td>' . \esc_html( $transaction['created_at'] ) . '</td>';
                echo '</tr>';
            }
            
            echo '</tbody>';
            echo '</table>';
            echo '</div>';
            
            if ( count( $transactions ) > 10 ) {
                echo '<p style="text-align: center; margin-top: 15px;">';
                echo '<a href="' . \esc_url( \rest_url( 'sofir/v1/payments/transactions' ) ) . '" class="button" target="_blank">' . \esc_html__( 'View All Transactions (REST API)', 'sofir' ) . ' →</a>';
                echo '</p>';
            }
        }
        
        echo '</div>';
    }

    private function render_documentation_section(): void {
        echo '<div class="sofir-card" style="margin-top: 20px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">';
        echo '<h2 style="color: white;">📚 ' . \esc_html__( 'Documentation & Usage', 'sofir' ) . '</h2>';
        
        echo '<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 20px;">';
        
        echo '<div>';
        echo '<h3 style="color: white; font-size: 16px; margin-bottom: 10px;">📝 ' . \esc_html__( 'Shortcode Usage', 'sofir' ) . '</h3>';
        echo '<div style="background: rgba(255,255,255,0.1); padding: 15px; border-radius: 6px; font-family: monospace; font-size: 13px;">';
        echo '[sofir_payment_form<br>';
        echo '&nbsp;&nbsp;amount="100000"<br>';
        echo '&nbsp;&nbsp;item_name="Premium Package"<br>';
        echo '&nbsp;&nbsp;return_url="/thank-you"<br>';
        echo ']';
        echo '</div>';
        echo '</div>';
        
        echo '<div>';
        echo '<h3 style="color: white; font-size: 16px; margin-bottom: 10px;">⚡ ' . \esc_html__( 'REST API Endpoint', 'sofir' ) . '</h3>';
        echo '<div style="background: rgba(255,255,255,0.1); padding: 15px; border-radius: 6px;">';
        echo '<strong>POST</strong> /wp-json/sofir/v1/payments/create<br>';
        echo '<small style="opacity: 0.9;">' . \esc_html__( 'Create payment transaction', 'sofir' ) . '</small>';
        echo '</div>';
        echo '<div style="background: rgba(255,255,255,0.1); padding: 15px; border-radius: 6px; margin-top: 10px;">';
        echo '<strong>GET</strong> /wp-json/sofir/v1/payments/transactions<br>';
        echo '<small style="opacity: 0.9;">' . \esc_html__( 'Get all transactions (admin only)', 'sofir' ) . '</small>';
        echo '</div>';
        echo '</div>';
        
        echo '</div>';
        
        echo '<div style="margin-top: 20px; padding: 15px; background: rgba(255,255,255,0.1); border-radius: 6px;">';
        echo '<h3 style="color: white; font-size: 16px; margin-bottom: 10px;">🎣 ' . \esc_html__( 'Developer Hooks', 'sofir' ) . '</h3>';
        echo '<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">';
        echo '<div>';
        echo '<strong>Actions:</strong><br>';
        echo '<code style="background: rgba(0,0,0,0.2); padding: 2px 6px; border-radius: 3px; font-size: 12px;">sofir/payment/status_changed</code><br>';
        echo '<code style="background: rgba(0,0,0,0.2); padding: 2px 6px; border-radius: 3px; font-size: 12px;">sofir/payment/duitku_webhook</code><br>';
        echo '<code style="background: rgba(0,0,0,0.2); padding: 2px 6px; border-radius: 3px; font-size: 12px;">sofir/payment/xendit_webhook</code><br>';
        echo '<code style="background: rgba(0,0,0,0.2); padding: 2px 6px; border-radius: 3px; font-size: 12px;">sofir/payment/midtrans_webhook</code>';
        echo '</div>';
        echo '<div>';
        echo '<strong>Filters:</strong><br>';
        echo '<code style="background: rgba(0,0,0,0.2); padding: 2px 6px; border-radius: 3px; font-size: 12px;">sofir/payment/gateways</code>';
        echo '</div>';
        echo '</div>';
        echo '</div>';
        
        echo '<div style="margin-top: 15px; text-align: center;">';
        $readme_path = SOFIR_PLUGIN_DIR . 'modules/payments/README.md';
        $readme_exists = file_exists( $readme_path );
        if ( $readme_exists ) {
            echo '<a href="' . \esc_url( SOFIR_PLUGIN_URL . 'modules/payments/README.md' ) . '" class="button button-large" target="_blank" style="background: white; color: #667eea; border: none; font-weight: bold;">';
            echo '📖 ' . \esc_html__( 'Read Full Documentation', 'sofir' );
            echo '</a>';
        }
        echo '</div>';
        
        echo '</div>';
    }

    private function format_price( float $amount, array $transaction ): string {
        $settings = PaymentManager::instance()->get_settings();
        $currency = $settings['currency'] ?? 'IDR';
        return $currency . ' ' . \number_format_i18n( $amount, 0 );
    }

    public function handle_save_settings(): void {
        if ( ! \current_user_can( 'manage_options' ) ) {
            \wp_die( \esc_html__( 'Unauthorized', 'sofir' ) );
        }

        \check_admin_referer( 'sofir_payment_settings', '_sofir_nonce' );

        PaymentManager::instance()->handle_save_settings();
    }

    private function render_notice( string $notice ): void {
        $messages = [
            'payment_settings_saved' => \__( 'Payment settings saved successfully! 🎉', 'sofir' ),
        ];

        $message = $messages[ $notice ] ?? '';

        if ( $message ) {
            echo '<div class="notice notice-success is-dismissible"><p>' . \esc_html( $message ) . '</p></div>';
        }
    }
}
