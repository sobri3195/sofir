<?php
namespace Sofir\Admin;

use Sofir\Membership\Manager as MembershipManager;
use Sofir\Loyalty\Manager as LoyaltyManager;

class UserPanel {
    private static ?UserPanel $instance = null;

    public static function instance(): UserPanel {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function render(): void {
        $manager   = MembershipManager::instance();
        $plans     = $manager->get_plans();
        $settings  = $manager->get_settings();
        $notice    = isset( $_GET['sofir_notice'] ) ? \sanitize_key( $_GET['sofir_notice'] ) : '';
        $role_list = \wp_roles()->roles;

        echo '<div class="sofir-admin">';

        if ( $notice ) {
            $this->render_notice( $notice );
        }

        echo '<div class="sofir-card">';
        echo '<h2>' . \esc_html__( 'Membership Plans', 'sofir' ) . '</h2>';

        if ( ! empty( $plans ) ) {
            echo '<table class="widefat">';
            echo '<thead><tr><th>' . \esc_html__( 'Plan', 'sofir' ) . '</th><th>' . \esc_html__( 'Price', 'sofir' ) . '</th><th>' . \esc_html__( 'Billing', 'sofir' ) . '</th><th>' . \esc_html__( 'Role', 'sofir' ) . '</th><th>' . \esc_html__( 'Members', 'sofir' ) . '</th><th>' . \esc_html__( 'Actions', 'sofir' ) . '</th></tr></thead><tbody>';

            foreach ( $plans as $plan ) {
                $delete_url = \wp_nonce_url(
                    \add_query_arg(
                        [
                            'action'  => 'sofir_delete_membership_plan',
                            'plan_id' => $plan['id'],
                        ],
                        \admin_url( 'admin-post.php' )
                    ),
                    'sofir_delete_plan',
                    '_sofir_nonce'
                );

                echo '<tr>';
                echo '<td>' . \esc_html( $plan['name'] ) . '</td>';
                echo '<td>' . \esc_html( $this->format_price( $plan['price'], $settings['currency'] ?? 'USD' ) ) . '</td>';
                echo '<td>' . \esc_html( $this->format_billing( $plan['billing'] ?? 'monthly' ) ) . '</td>';
                echo '<td>' . \esc_html( $plan['role'] ?? '' ) . '</td>';
                echo '<td>' . \esc_html( $this->count_members( $plan['id'] ) ) . '</td>';
                echo '<td><a class="button-link-delete" href="' . \esc_url( $delete_url ) . '">' . \esc_html__( 'Delete', 'sofir' ) . '</a></td>';
                echo '</tr>';
            }

            echo '</tbody></table>';
        } else {
            echo '<p>' . \esc_html__( 'No membership plans yet. Use the form below to create one.', 'sofir' ) . '</p>';
        }

        echo '<hr />';
        echo '<h3>' . \esc_html__( 'Add / Update Plan', 'sofir' ) . '</h3>';
        echo '<form method="post" action="' . \esc_url( \admin_url( 'admin-post.php' ) ) . '" class="sofir-plan-form">';
        echo '<input type="hidden" name="action" value="sofir_save_membership_plan" />';
        \wp_nonce_field( 'sofir_membership_plan', '_sofir_nonce' );

        echo '<div class="sofir-field-group">';
        echo '<label class="sofir-field"><span>' . \esc_html__( 'Plan ID (slug)', 'sofir' ) . '</span><input type="text" name="plan_id" /></label>';
        echo '<label class="sofir-field"><span>' . \esc_html__( 'Plan Name', 'sofir' ) . '</span><input type="text" name="plan_name" required /></label>';
        echo '<label class="sofir-field"><span>' . \esc_html__( 'Price', 'sofir' ) . '</span><input type="number" step="0.01" name="plan_price" value="0" /></label>';

        echo '<label class="sofir-field"><span>' . \esc_html__( 'Billing Cycle', 'sofir' ) . '</span><select name="plan_billing">';
        foreach ( [ 'monthly' => \__( 'Monthly', 'sofir' ), 'yearly' => \__( 'Yearly', 'sofir' ), 'lifetime' => \__( 'Lifetime', 'sofir' ) ] as $key => $label ) {
            echo '<option value="' . \esc_attr( $key ) . '">' . \esc_html( $label ) . '</option>';
        }
        echo '</select></label>';

        echo '<label class="sofir-field"><span>' . \esc_html__( 'Assign Role', 'sofir' ) . '</span><select name="plan_role">';
        foreach ( $role_list as $role_key => $role_data ) {
            echo '<option value="' . \esc_attr( $role_key ) . '">' . \esc_html( $role_data['name'] ) . '</option>';
        }
        echo '</select></label>';
        echo '</div>';

        echo '<label class="sofir-field"><span>' . \esc_html__( 'Features (one per line)', 'sofir' ) . '</span><textarea name="plan_features" rows="4"></textarea></label>';
        echo '<label class="sofir-toggle"><input type="checkbox" name="plan_highlight" value="1" /> <span>' . \esc_html__( 'Highlight this plan', 'sofir' ) . '</span></label>';
        echo '<p class="submit"><button type="submit" class="button button-primary">' . \esc_html__( 'Save Plan', 'sofir' ) . '</button></p>';
        echo '</form>';
        echo '</div>';

        echo '<div class="sofir-card">';
        echo '<h2>' . \esc_html__( 'Payment & Integration', 'sofir' ) . '</h2>';
        echo '<form method="post" action="' . \esc_url( \admin_url( 'admin-post.php' ) ) . '">';
        echo '<input type="hidden" name="action" value="sofir_save_membership_settings" />';
        \wp_nonce_field( 'sofir_membership_settings', '_sofir_nonce' );

        echo '<label class="sofir-field"><span>' . \esc_html__( 'Currency', 'sofir' ) . '</span><input type="text" name="membership_currency" value="' . \esc_attr( $settings['currency'] ?? 'USD' ) . '" /></label>';
        echo '<label class="sofir-field"><span>' . \esc_html__( 'Stripe Publishable Key', 'sofir' ) . '</span><input type="text" name="stripe_publishable" value="' . \esc_attr( $settings['stripe_publishable'] ?? '' ) . '" /></label>';
        echo '<label class="sofir-field"><span>' . \esc_html__( 'Stripe Secret Key', 'sofir' ) . '</span><input type="text" name="stripe_secret" value="' . \esc_attr( $settings['stripe_secret'] ?? '' ) . '" /></label>';

        echo '<p class="submit"><button type="submit" class="button">' . \esc_html__( 'Save Settings', 'sofir' ) . '</button></p>';
        echo '</form>';
        echo '<p class="description">' . \esc_html__( 'Integrate with Stripe or WooCommerce by mapping checkout completion to the SOFIR membership hooks.', 'sofir' ) . '</p>';
        echo '</div>';

        $this->render_loyalty_settings();

        echo '</div>';
    }

    private function render_loyalty_settings(): void {
        $loyalty_manager = LoyaltyManager::instance();
        $loyalty_settings = $loyalty_manager->get_settings();

        echo '<div class="sofir-card">';
        echo '<h2>' . \esc_html__( 'Loyalty Program', 'sofir' ) . '</h2>';
        echo '<form method="post" action="' . \esc_url( \admin_url( 'admin-post.php' ) ) . '">';
        echo '<input type="hidden" name="action" value="sofir_save_loyalty_settings" />';
        \wp_nonce_field( 'sofir_loyalty_settings', '_sofir_nonce' );

        echo '<label class="sofir-toggle">';
        echo '<input type="checkbox" name="loyalty_enabled" value="1" ' . \checked( $loyalty_settings['enabled'] ?? true, true, false ) . ' />';
        echo ' <span>' . \esc_html__( 'Enable Loyalty Program', 'sofir' ) . '</span>';
        echo '</label>';

        echo '<h3>' . \esc_html__( 'Point Rewards', 'sofir' ) . '</h3>';
        echo '<div class="sofir-field-group">';
        echo '<label class="sofir-field"><span>' . \esc_html__( 'Sign Up Bonus (points)', 'sofir' ) . '</span><input type="number" name="points_signup" value="' . \esc_attr( $loyalty_settings['points_signup'] ?? 100 ) . '" min="0" /></label>';
        echo '<label class="sofir-field"><span>' . \esc_html__( 'Daily Login Bonus (points)', 'sofir' ) . '</span><input type="number" name="points_login" value="' . \esc_attr( $loyalty_settings['points_login'] ?? 10 ) . '" min="0" /></label>';
        echo '<label class="sofir-field"><span>' . \esc_html__( 'Comment Posted (points)', 'sofir' ) . '</span><input type="number" name="points_comment" value="' . \esc_attr( $loyalty_settings['points_comment'] ?? 5 ) . '" min="0" /></label>';
        echo '<label class="sofir-field"><span>' . \esc_html__( 'Post Published (points)', 'sofir' ) . '</span><input type="number" name="points_post" value="' . \esc_attr( $loyalty_settings['points_post'] ?? 20 ) . '" min="0" /></label>';
        echo '<label class="sofir-field"><span>' . \esc_html__( 'Points per Currency Unit (purchase)', 'sofir' ) . '</span><input type="number" step="0.01" name="points_per_currency" value="' . \esc_attr( $loyalty_settings['points_per_currency'] ?? 1 ) . '" min="0" /></label>';
        echo '</div>';

        echo '<p class="submit"><button type="submit" class="button button-primary">' . \esc_html__( 'Save Loyalty Settings', 'sofir' ) . '</button></p>';
        echo '</form>';

        echo '<hr />';
        echo '<div style="background: #f0f6fc; border-left: 4px solid #2271b1; padding: 20px; margin-top: 20px; border-radius: 4px;">';
        echo '<h3 style="margin-top: 0;">' . \esc_html__( '📖 Cara Menggunakan Loyalty Program', 'sofir' ) . '</h3>';
        
        echo '<h4>' . \esc_html__( '1. Shortcodes', 'sofir' ) . '</h4>';
        echo '<div style="background: #fff; padding: 12px; border-radius: 4px; margin-bottom: 15px;">';
        echo '<p style="margin: 5px 0;"><strong><code>[sofir_loyalty_points]</code></strong></p>';
        echo '<p style="margin: 5px 0 0 20px; color: #666;">' . \esc_html__( 'Menampilkan saldo poin loyalty user yang sedang login. Taruh di halaman profile atau member area.', 'sofir' ) . '</p>';
        echo '</div>';
        
        echo '<div style="background: #fff; padding: 12px; border-radius: 4px; margin-bottom: 15px;">';
        echo '<p style="margin: 5px 0;"><strong><code>[sofir_loyalty_rewards]</code></strong></p>';
        echo '<p style="margin: 5px 0 0 20px; color: #666;">' . \esc_html__( 'Menampilkan katalog reward yang bisa ditukar dengan poin. User bisa klik tombol "Redeem" untuk menukar poin.', 'sofir' ) . '</p>';
        echo '</div>';

        echo '<h4>' . \esc_html__( '2. Cara Kerja Sistem Poin', 'sofir' ) . '</h4>';
        echo '<ul style="margin-left: 20px;">';
        echo '<li><strong>' . \esc_html__( 'Sign Up:', 'sofir' ) . '</strong> ' . \esc_html__( 'User otomatis mendapat poin saat pertama kali registrasi', 'sofir' ) . '</li>';
        echo '<li><strong>' . \esc_html__( 'Daily Login:', 'sofir' ) . '</strong> ' . \esc_html__( 'Poin diberikan maksimal 1x per hari saat login', 'sofir' ) . '</li>';
        echo '<li><strong>' . \esc_html__( 'Comment:', 'sofir' ) . '</strong> ' . \esc_html__( 'Poin diberikan saat komentar disetujui (bukan spam)', 'sofir' ) . '</li>';
        echo '<li><strong>' . \esc_html__( 'Post Published:', 'sofir' ) . '</strong> ' . \esc_html__( 'Poin diberikan saat user publish post/artikel', 'sofir' ) . '</li>';
        echo '<li><strong>' . \esc_html__( 'Purchase:', 'sofir' ) . '</strong> ' . \esc_html__( 'Poin otomatis dihitung dari total pembelian (terintegrasi dengan Payment Gateway)', 'sofir' ) . '</li>';
        echo '</ul>';

        echo '<h4>' . \esc_html__( '3. REST API Endpoints', 'sofir' ) . '</h4>';
        echo '<p style="color: #666; font-size: 13px;">' . \esc_html__( 'Gunakan REST API untuk integrasi dengan aplikasi mobile atau sistem eksternal:', 'sofir' ) . '</p>';
        echo '<ul style="list-style: none; padding-left: 0;">';
        echo '<li style="padding: 8px; background: #fff; margin-bottom: 5px; border-radius: 4px;"><code style="color: #10b981;">GET</code> <code>/wp-json/sofir/v1/loyalty/points/{user_id}</code></li>';
        echo '<li style="padding: 8px; background: #fff; margin-bottom: 5px; border-radius: 4px;"><code style="color: #10b981;">GET</code> <code>/wp-json/sofir/v1/loyalty/history/{user_id}</code></li>';
        echo '<li style="padding: 8px; background: #fff; margin-bottom: 5px; border-radius: 4px;"><code style="color: #f59e0b;">POST</code> <code>/wp-json/sofir/v1/loyalty/redeem</code></li>';
        echo '<li style="padding: 8px; background: #fff; margin-bottom: 5px; border-radius: 4px;"><code style="color: #10b981;">GET</code> <code>/wp-json/sofir/v1/loyalty/rewards</code></li>';
        echo '</ul>';

        echo '<h4>' . \esc_html__( '4. Default Rewards', 'sofir' ) . '</h4>';
        echo '<p style="color: #666;">' . \esc_html__( 'Sistem sudah dilengkapi 3 reward default:', 'sofir' ) . '</p>';
        echo '<ul style="margin-left: 20px;">';
        echo '<li><strong>Diskon 10%</strong> - 500 poin</li>';
        echo '<li><strong>Diskon 20%</strong> - 1000 poin</li>';
        echo '<li><strong>Gratis Ongkir</strong> - 750 poin</li>';
        echo '</ul>';
        echo '<p style="color: #666; font-size: 13px;">' . \esc_html__( 'Reward bisa dikustomisasi melalui kode di modules/loyalty/manager.php', 'sofir' ) . '</p>';

        echo '<h4>' . \esc_html__( '5. Contoh Penempatan', 'sofir' ) . '</h4>';
        echo '<ol style="margin-left: 20px;">';
        echo '<li>' . \esc_html__( 'Buat halaman baru dengan nama "My Rewards"', 'sofir' ) . '</li>';
        echo '<li>' . \esc_html__( 'Tambahkan shortcode <code>[sofir_loyalty_points]</code> di bagian atas', 'sofir' ) . '</li>';
        echo '<li>' . \esc_html__( 'Tambahkan shortcode <code>[sofir_loyalty_rewards]</code> di bawahnya', 'sofir' ) . '</li>';
        echo '<li>' . \esc_html__( 'Publish halaman dan tambahkan link ke menu atau member area', 'sofir' ) . '</li>';
        echo '</ol>';

        echo '<div style="background: #fff3cd; border-left: 4px solid #ffc107; padding: 12px; margin-top: 15px; border-radius: 4px;">';
        echo '<strong>💡 Tips:</strong> ' . \esc_html__( 'Integrasikan dengan Payment Gateway SOFIR (Duitku, Xendit, Midtrans) untuk memberikan poin otomatis saat user melakukan pembelian!', 'sofir' );
        echo '</div>';

        echo '</div>';

        echo '</div>';
    }

    private function render_notice( string $notice ): void {
        $messages = [
            'plan_saved'                => \__( 'Membership plan saved.', 'sofir' ),
            'plan_deleted'              => \__( 'Membership plan deleted.', 'sofir' ),
            'membership_settings_saved' => \__( 'Membership settings updated.', 'sofir' ),
            'loyalty_settings_saved'    => \__( 'Loyalty program settings saved.', 'sofir' ),
        ];

        if ( empty( $messages[ $notice ] ) ) {
            return;
        }

        echo '<div class="notice notice-success is-dismissible"><p>' . \esc_html( $messages[ $notice ] ) . '</p></div>';
    }

    private function count_members( string $plan_id ): int {
        $query = new \WP_User_Query(
            [
                'meta_key'   => 'sofir_membership_plan',
                'meta_value' => $plan_id,
                'fields'     => 'ID',
            ]
        );

        return count( (array) $query->get_results() );
    }

    private function format_price( float $price, string $currency ): string {
        if ( $price <= 0 ) {
            return \__( 'Free', 'sofir' );
        }

        return sprintf( '%s %s', $currency, \number_format_i18n( $price, 2 ) );
    }

    private function format_billing( string $billing ): string {
        switch ( $billing ) {
            case 'yearly':
                return \__( 'Yearly', 'sofir' );
            case 'lifetime':
                return \__( 'Lifetime', 'sofir' );
            default:
                return \__( 'Monthly', 'sofir' );
        }
    }
}
