<?php
namespace Sofir\Admin;

use Sofir\Membership\Manager as MembershipManager;

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

        echo '</div>';
    }

    private function render_notice( string $notice ): void {
        $messages = [
            'plan_saved'                => \__( 'Membership plan saved.', 'sofir' ),
            'plan_deleted'              => \__( 'Membership plan deleted.', 'sofir' ),
            'membership_settings_saved' => \__( 'Membership settings updated.', 'sofir' ),
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
