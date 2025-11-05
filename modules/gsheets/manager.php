<?php
namespace Sofir\GSheets;

class Manager {
    private static ?Manager $instance = null;

    public static function instance(): Manager {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function boot(): void {
        \add_action( 'admin_menu', [ $this, 'add_settings_page' ] );
        \add_action( 'admin_init', [ $this, 'register_settings' ] );
        \add_action( 'rest_api_init', [ $this, 'register_rest_routes' ] );
        \add_action( 'sofir/payment/status_changed', [ $this, 'sync_payment_to_sheets' ], 10, 2 );
        \add_action( 'user_register', [ $this, 'sync_user_to_sheets' ] );
        \add_action( 'save_post', [ $this, 'sync_post_to_sheets' ], 10, 3 );
        \add_shortcode( 'sofir_sheets_export', [ $this, 'render_export_button' ] );
    }

    public function add_settings_page(): void {
        \add_submenu_page(
            'sofir-admin',
            \__( 'Google Sheets Integration', 'sofir' ),
            \__( 'Google Sheets', 'sofir' ),
            'manage_options',
            'sofir-gsheets',
            [ $this, 'render_settings_page' ]
        );
    }

    public function register_settings(): void {
        \register_setting( 'sofir_gsheets', 'sofir_gsheets_enabled' );
        \register_setting( 'sofir_gsheets', 'sofir_gsheets_api_key' );
        \register_setting( 'sofir_gsheets', 'sofir_gsheets_client_id' );
        \register_setting( 'sofir_gsheets', 'sofir_gsheets_client_secret' );
        \register_setting( 'sofir_gsheets', 'sofir_gsheets_spreadsheet_id' );
        \register_setting( 'sofir_gsheets', 'sofir_gsheets_auto_sync' );
        \register_setting( 'sofir_gsheets', 'sofir_gsheets_sync_users' );
        \register_setting( 'sofir_gsheets', 'sofir_gsheets_sync_orders' );
        \register_setting( 'sofir_gsheets', 'sofir_gsheets_sync_posts' );
    }

    public function render_settings_page(): void {
        if ( ! \current_user_can( 'manage_options' ) ) {
            return;
        }

        ?>
        <div class="wrap">
            <h1><?php echo \esc_html( \get_admin_page_title() ); ?></h1>
            
            <div class="notice notice-info">
                <p><?php \esc_html_e( 'Connect your WordPress site with Google Sheets to automatically sync data.', 'sofir' ); ?></p>
            </div>

            <form method="post" action="options.php">
                <?php
                \settings_fields( 'sofir_gsheets' );
                \do_settings_sections( 'sofir_gsheets' );
                ?>

                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="sofir_gsheets_enabled"><?php \esc_html_e( 'Enable Integration', 'sofir' ); ?></label>
                        </th>
                        <td>
                            <input type="checkbox" id="sofir_gsheets_enabled" name="sofir_gsheets_enabled" value="1" <?php \checked( \get_option( 'sofir_gsheets_enabled' ), '1' ); ?> />
                            <p class="description"><?php \esc_html_e( 'Enable Google Sheets integration.', 'sofir' ); ?></p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <label for="sofir_gsheets_api_key"><?php \esc_html_e( 'API Key', 'sofir' ); ?></label>
                        </th>
                        <td>
                            <input type="text" id="sofir_gsheets_api_key" name="sofir_gsheets_api_key" value="<?php echo \esc_attr( \get_option( 'sofir_gsheets_api_key' ) ); ?>" class="regular-text" />
                            <p class="description"><?php \esc_html_e( 'Google Cloud API Key.', 'sofir' ); ?></p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <label for="sofir_gsheets_client_id"><?php \esc_html_e( 'Client ID', 'sofir' ); ?></label>
                        </th>
                        <td>
                            <input type="text" id="sofir_gsheets_client_id" name="sofir_gsheets_client_id" value="<?php echo \esc_attr( \get_option( 'sofir_gsheets_client_id' ) ); ?>" class="regular-text" />
                            <p class="description"><?php \esc_html_e( 'OAuth 2.0 Client ID.', 'sofir' ); ?></p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <label for="sofir_gsheets_client_secret"><?php \esc_html_e( 'Client Secret', 'sofir' ); ?></label>
                        </th>
                        <td>
                            <input type="password" id="sofir_gsheets_client_secret" name="sofir_gsheets_client_secret" value="<?php echo \esc_attr( \get_option( 'sofir_gsheets_client_secret' ) ); ?>" class="regular-text" />
                            <p class="description"><?php \esc_html_e( 'OAuth 2.0 Client Secret.', 'sofir' ); ?></p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <label for="sofir_gsheets_spreadsheet_id"><?php \esc_html_e( 'Spreadsheet ID', 'sofir' ); ?></label>
                        </th>
                        <td>
                            <input type="text" id="sofir_gsheets_spreadsheet_id" name="sofir_gsheets_spreadsheet_id" value="<?php echo \esc_attr( \get_option( 'sofir_gsheets_spreadsheet_id' ) ); ?>" class="regular-text" />
                            <p class="description"><?php \esc_html_e( 'The ID of your Google Spreadsheet (from the URL).', 'sofir' ); ?></p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row"><?php \esc_html_e( 'Auto Sync Options', 'sofir' ); ?></th>
                        <td>
                            <fieldset>
                                <label>
                                    <input type="checkbox" name="sofir_gsheets_sync_users" value="1" <?php \checked( \get_option( 'sofir_gsheets_sync_users' ), '1' ); ?> />
                                    <?php \esc_html_e( 'Sync new users', 'sofir' ); ?>
                                </label><br>
                                <label>
                                    <input type="checkbox" name="sofir_gsheets_sync_orders" value="1" <?php \checked( \get_option( 'sofir_gsheets_sync_orders' ), '1' ); ?> />
                                    <?php \esc_html_e( 'Sync orders and payments', 'sofir' ); ?>
                                </label><br>
                                <label>
                                    <input type="checkbox" name="sofir_gsheets_sync_posts" value="1" <?php \checked( \get_option( 'sofir_gsheets_sync_posts' ), '1' ); ?> />
                                    <?php \esc_html_e( 'Sync published posts', 'sofir' ); ?>
                                </label>
                            </fieldset>
                        </td>
                    </tr>
                </table>

                <?php \submit_button(); ?>
            </form>

            <hr>

            <h2><?php \esc_html_e( 'Manual Export', 'sofir' ); ?></h2>
            <p><?php \esc_html_e( 'Export data manually to Google Sheets.', 'sofir' ); ?></p>
            
            <button type="button" class="button button-primary" id="sofir-export-users">
                <?php \esc_html_e( 'Export Users', 'sofir' ); ?>
            </button>
            
            <button type="button" class="button button-primary" id="sofir-export-orders">
                <?php \esc_html_e( 'Export Orders', 'sofir' ); ?>
            </button>
            
            <button type="button" class="button button-primary" id="sofir-export-posts">
                <?php \esc_html_e( 'Export Posts', 'sofir' ); ?>
            </button>

            <div id="sofir-export-status" style="margin-top: 20px;"></div>
        </div>

        <script>
        jQuery(document).ready(function($) {
            $('#sofir-export-users, #sofir-export-orders, #sofir-export-posts').on('click', function() {
                var type = $(this).attr('id').replace('sofir-export-', '');
                var $status = $('#sofir-export-status');
                
                $status.html('<p><?php \esc_html_e( 'Exporting...', 'sofir' ); ?></p>');
                
                $.ajax({
                    url: '<?php echo \esc_url( \rest_url( 'sofir/v1/gsheets/export' ) ); ?>',
                    method: 'POST',
                    data: { type: type },
                    beforeSend: function(xhr) {
                        xhr.setRequestHeader('X-WP-Nonce', '<?php echo \wp_create_nonce( 'wp_rest' ); ?>');
                    },
                    success: function(response) {
                        $status.html('<div class="notice notice-success"><p>' + response.message + '</p></div>');
                    },
                    error: function(xhr) {
                        $status.html('<div class="notice notice-error"><p>' + (xhr.responseJSON?.message || 'Export failed') + '</p></div>');
                    }
                });
            });
        });
        </script>
        <?php
    }

    public function register_rest_routes(): void {
        \register_rest_route(
            'sofir/v1',
            '/gsheets/export',
            [
                'methods' => 'POST',
                'callback' => [ $this, 'handle_export' ],
                'permission_callback' => function (): bool {
                    return \current_user_can( 'manage_options' );
                },
            ]
        );

        \register_rest_route(
            'sofir/v1',
            '/gsheets/import',
            [
                'methods' => 'POST',
                'callback' => [ $this, 'handle_import' ],
                'permission_callback' => function (): bool {
                    return \current_user_can( 'manage_options' );
                },
            ]
        );
    }

    public function handle_export( \WP_REST_Request $request ): \WP_REST_Response {
        $type = $request->get_param( 'type' );

        if ( ! $this->is_enabled() ) {
            return new \WP_REST_Response(
                [ 'message' => \__( 'Google Sheets integration is not enabled.', 'sofir' ) ],
                400
            );
        }

        $data = $this->get_export_data( $type );
        $result = $this->send_to_sheets( $data, $type );

        if ( $result ) {
            return new \WP_REST_Response(
                [ 'message' => \sprintf( \__( '%s exported successfully!', 'sofir' ), \ucfirst( $type ) ) ],
                200
            );
        }

        return new \WP_REST_Response(
            [ 'message' => \__( 'Export failed. Please check your settings.', 'sofir' ) ],
            500
        );
    }

    public function handle_import( \WP_REST_Request $request ): \WP_REST_Response {
        if ( ! $this->is_enabled() ) {
            return new \WP_REST_Response(
                [ 'message' => \__( 'Google Sheets integration is not enabled.', 'sofir' ) ],
                400
            );
        }

        $type = $request->get_param( 'type' );
        $data = $this->fetch_from_sheets( $type );

        if ( $data ) {
            $imported = $this->import_data( $data, $type );
            
            return new \WP_REST_Response(
                [
                    'message' => \sprintf( \__( '%d %s imported successfully!', 'sofir' ), $imported, $type ),
                    'count' => $imported,
                ],
                200
            );
        }

        return new \WP_REST_Response(
            [ 'message' => \__( 'Import failed. Please check your settings.', 'sofir' ) ],
            500
        );
    }

    private function is_enabled(): bool {
        return '1' === \get_option( 'sofir_gsheets_enabled' );
    }

    private function get_export_data( string $type ): array {
        switch ( $type ) {
            case 'users':
                return $this->get_users_data();
            case 'orders':
                return $this->get_orders_data();
            case 'posts':
                return $this->get_posts_data();
            default:
                return [];
        }
    }

    private function get_users_data(): array {
        $users = \get_users();
        $data = [ [ 'ID', 'Username', 'Email', 'Phone', 'Registered', 'Role' ] ];

        foreach ( $users as $user ) {
            $data[] = [
                $user->ID,
                $user->user_login,
                $user->user_email,
                \get_user_meta( $user->ID, 'sofir_phone', true ),
                $user->user_registered,
                \implode( ', ', $user->roles ),
            ];
        }

        return $data;
    }

    private function get_orders_data(): array {
        $orders = \get_option( 'sofir_payment_transactions', [] );
        $data = [ [ 'ID', 'Gateway', 'Amount', 'Status', 'User ID', 'Created', 'Updated' ] ];

        foreach ( $orders as $order ) {
            $data[] = [
                $order['id'] ?? '',
                $order['gateway'] ?? '',
                $order['amount'] ?? '',
                $order['status'] ?? '',
                $order['user_id'] ?? '',
                $order['created_at'] ?? '',
                $order['updated_at'] ?? '',
            ];
        }

        return $data;
    }

    private function get_posts_data(): array {
        $posts = \get_posts( [
            'post_type' => 'any',
            'post_status' => 'publish',
            'numberposts' => -1,
        ] );

        $data = [ [ 'ID', 'Title', 'Type', 'Author', 'Date', 'URL' ] ];

        foreach ( $posts as $post ) {
            $data[] = [
                $post->ID,
                $post->post_title,
                $post->post_type,
                \get_the_author_meta( 'display_name', $post->post_author ),
                $post->post_date,
                \get_permalink( $post->ID ),
            ];
        }

        return $data;
    }

    private function send_to_sheets( array $data, string $sheet_name ): bool {
        $api_key = \get_option( 'sofir_gsheets_api_key' );
        $spreadsheet_id = \get_option( 'sofir_gsheets_spreadsheet_id' );

        if ( ! $api_key || ! $spreadsheet_id ) {
            return false;
        }

        $url = \sprintf(
            'https://sheets.googleapis.com/v4/spreadsheets/%s/values/%s!A1:append?valueInputOption=RAW&key=%s',
            $spreadsheet_id,
            \ucfirst( $sheet_name ),
            $api_key
        );

        $response = \wp_remote_post(
            $url,
            [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'body' => \wp_json_encode( [
                    'values' => $data,
                ] ),
            ]
        );

        if ( \is_wp_error( $response ) ) {
            \error_log( 'SOFIR GSheets Error: ' . $response->get_error_message() );
            return false;
        }

        $code = \wp_remote_retrieve_response_code( $response );
        return 200 === $code;
    }

    private function fetch_from_sheets( string $sheet_name ): array {
        $api_key = \get_option( 'sofir_gsheets_api_key' );
        $spreadsheet_id = \get_option( 'sofir_gsheets_spreadsheet_id' );

        if ( ! $api_key || ! $spreadsheet_id ) {
            return [];
        }

        $url = \sprintf(
            'https://sheets.googleapis.com/v4/spreadsheets/%s/values/%s!A:Z?key=%s',
            $spreadsheet_id,
            \ucfirst( $sheet_name ),
            $api_key
        );

        $response = \wp_remote_get( $url );

        if ( \is_wp_error( $response ) ) {
            return [];
        }

        $body = \wp_remote_retrieve_body( $response );
        $result = \json_decode( $body, true );

        return $result['values'] ?? [];
    }

    private function import_data( array $data, string $type ): int {
        $count = 0;

        array_shift( $data );

        foreach ( $data as $row ) {
            if ( 'users' === $type ) {
                $count += $this->import_user( $row ) ? 1 : 0;
            } elseif ( 'posts' === $type ) {
                $count += $this->import_post( $row ) ? 1 : 0;
            }
        }

        return $count;
    }

    private function import_user( array $row ): bool {
        if ( empty( $row[1] ) || empty( $row[2] ) ) {
            return false;
        }

        $user_id = \wp_create_user( $row[1], \wp_generate_password(), $row[2] );

        if ( \is_wp_error( $user_id ) ) {
            return false;
        }

        if ( ! empty( $row[3] ) ) {
            \update_user_meta( $user_id, 'sofir_phone', \sanitize_text_field( $row[3] ) );
        }

        return true;
    }

    private function import_post( array $row ): bool {
        if ( empty( $row[1] ) ) {
            return false;
        }

        $post_id = \wp_insert_post( [
            'post_title' => \sanitize_text_field( $row[1] ),
            'post_type' => \sanitize_text_field( $row[2] ?? 'post' ),
            'post_status' => 'publish',
        ] );

        return ! \is_wp_error( $post_id ) && $post_id > 0;
    }

    public function sync_payment_to_sheets( string $transaction_id, string $status ): void {
        if ( ! $this->is_enabled() || '1' !== \get_option( 'sofir_gsheets_sync_orders' ) ) {
            return;
        }

        $transactions = \get_option( 'sofir_payment_transactions', [] );
        
        if ( isset( $transactions[ $transaction_id ] ) ) {
            $transaction = $transactions[ $transaction_id ];
            $data = [ [
                $transaction['id'],
                $transaction['gateway'],
                $transaction['amount'],
                $status,
                $transaction['user_id'],
                $transaction['created_at'],
                $transaction['updated_at'] ?? \current_time( 'mysql' ),
            ] ];

            $this->send_to_sheets( $data, 'Orders' );
        }
    }

    public function sync_user_to_sheets( int $user_id ): void {
        if ( ! $this->is_enabled() || '1' !== \get_option( 'sofir_gsheets_sync_users' ) ) {
            return;
        }

        $user = \get_userdata( $user_id );
        
        if ( ! $user ) {
            return;
        }

        $data = [ [
            $user->ID,
            $user->user_login,
            $user->user_email,
            \get_user_meta( $user->ID, 'sofir_phone', true ),
            $user->user_registered,
            \implode( ', ', $user->roles ),
        ] ];

        $this->send_to_sheets( $data, 'Users' );
    }

    public function sync_post_to_sheets( int $post_id, \WP_Post $post, bool $update ): void {
        if ( ! $this->is_enabled() || '1' !== \get_option( 'sofir_gsheets_sync_posts' ) ) {
            return;
        }

        if ( 'publish' !== $post->post_status || \wp_is_post_revision( $post_id ) ) {
            return;
        }

        $data = [ [
            $post->ID,
            $post->post_title,
            $post->post_type,
            \get_the_author_meta( 'display_name', $post->post_author ),
            $post->post_date,
            \get_permalink( $post->ID ),
        ] ];

        $this->send_to_sheets( $data, 'Posts' );
    }

    public function render_export_button( array $atts ): string {
        $atts = \shortcode_atts( [
            'type' => 'users',
            'text' => \__( 'Export to Sheets', 'sofir' ),
        ], $atts );

        \wp_enqueue_script( 'jquery' );

        ob_start();
        ?>
        <button class="sofir-sheets-export" data-type="<?php echo \esc_attr( $atts['type'] ); ?>">
            <?php echo \esc_html( $atts['text'] ); ?>
        </button>
        <script>
        jQuery(document).ready(function($) {
            $('.sofir-sheets-export').on('click', function() {
                var type = $(this).data('type');
                var $btn = $(this);
                
                $btn.prop('disabled', true).text('<?php \esc_html_e( 'Exporting...', 'sofir' ); ?>');
                
                $.ajax({
                    url: '<?php echo \esc_url( \rest_url( 'sofir/v1/gsheets/export' ) ); ?>',
                    method: 'POST',
                    data: { type: type },
                    beforeSend: function(xhr) {
                        xhr.setRequestHeader('X-WP-Nonce', '<?php echo \wp_create_nonce( 'wp_rest' ); ?>');
                    },
                    success: function() {
                        $btn.text('<?php \esc_html_e( 'Exported!', 'sofir' ); ?>');
                        setTimeout(function() {
                            $btn.prop('disabled', false).text('<?php echo \esc_js( $atts['text'] ); ?>');
                        }, 2000);
                    },
                    error: function() {
                        $btn.prop('disabled', false).text('<?php \esc_html_e( 'Failed', 'sofir' ); ?>');
                    }
                });
            });
        });
        </script>
        <?php
        return (string) ob_get_clean();
    }
}
