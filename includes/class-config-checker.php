<?php
/**
 * Configuration Checker
 * 
 * Detects common WordPress configuration issues and provides admin notices
 * to help site administrators fix problems like duplicate constant definitions.
 * 
 * @package SOFIR
 */

declare( strict_types=1 );

namespace Sofir;

if ( ! \defined( 'ABSPATH' ) ) {
    exit;
}

class ConfigChecker {
    private static ?ConfigChecker $instance = null;
    private bool $script_enqueued = false;

    public static function instance(): ConfigChecker {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function __construct() {
        \add_action( 'admin_init', [ $this, 'check_configuration' ] );
        \add_action( 'admin_notices', [ $this, 'display_notices' ] );
    }

    /**
     * Check for common configuration issues
     */
    public function check_configuration(): void {
        // Only run checks for administrators
        if ( ! \current_user_can( 'manage_options' ) ) {
            return;
        }

        // Check if we've already shown notices in this session
        if ( \get_transient( 'sofir_config_check_done' ) ) {
            return;
        }

        $this->check_debug_output();
        
        // Set transient to avoid repeated checks (check once per day)
        \set_transient( 'sofir_config_check_done', true, DAY_IN_SECONDS );
    }

    /**
     * Check if debug output is being generated
     */
    private function check_debug_output(): void {
        // Check if output has been sent before WordPress headers
        $output_started = false;
        $output_file = '';
        $output_line = 0;

        if ( \headers_sent( $output_file, $output_line ) ) {
            $output_started = true;
        }

        // If output was sent from wp-config.php, it's likely a configuration issue
        if ( $output_started && false !== \strpos( $output_file, 'wp-config.php' ) ) {
            $this->add_notice(
                'error',
                'wp-config-output',
                sprintf(
                    '<strong>Configuration Error Detected:</strong> Output is being generated from your wp-config.php file (line %d), which can cause "headers already sent" errors. This is often caused by:<br><br>' .
                    '• Duplicate constant definitions (e.g., WP_DEBUG defined multiple times)<br>' .
                    '• Whitespace or characters before &lt;?php<br>' .
                    '• Echo or print statements in wp-config.php<br><br>' .
                    '<a href="%s" target="_blank" class="button button-primary">View Fix Guide</a> ' .
                    '<a href="#" class="button sofir-dismiss-notice" data-notice="wp-config-output">Dismiss</a>',
                    $output_line,
                    \esc_url( SOFIR_PLUGIN_URL . 'WP_CONFIG_FIX_GUIDE.md' )
                )
            );
        }

        // Check for WP_DEBUG warnings in error log
        $this->check_error_log_for_duplicates();
    }

    /**
     * Check error log for duplicate constant warnings
     */
    private function check_error_log_for_duplicates(): void {
        // Only check if WP_DEBUG_LOG is enabled and file exists
        if ( ! \defined( 'WP_DEBUG_LOG' ) || ! WP_DEBUG_LOG ) {
            return;
        }

        $log_file = WP_CONTENT_DIR . '/debug.log';
        
        if ( ! \file_exists( $log_file ) || ! \is_readable( $log_file ) ) {
            return;
        }

        // Read last 50 lines of log file
        $log_lines = $this->tail_file( $log_file, 50 );
        
        $has_duplicate_warning = false;
        foreach ( $log_lines as $line ) {
            if ( \preg_match( '/Constant\s+WP_DEBUG.*already\s+defined/i', $line ) ) {
                $has_duplicate_warning = true;
                break;
            }
        }

        if ( $has_duplicate_warning ) {
            $this->add_notice(
                'error',
                'wp-debug-duplicate',
                '<strong>Duplicate WP_DEBUG Constants Detected:</strong> Your error log shows that WP_DEBUG or related constants are being defined multiple times in wp-config.php. This causes warnings and can break your site.<br><br>' .
                '<strong>Quick Fix:</strong><br>' .
                '1. Open wp-config.php<br>' .
                '2. Search for all instances of "WP_DEBUG"<br>' .
                '3. Remove duplicate definitions (keep only one set)<br>' .
                '4. Use conditional checks: <code>if ( ! defined( \'WP_DEBUG\' ) ) { define( \'WP_DEBUG\', false ); }</code><br><br>' .
                '<a href="' . \esc_url( SOFIR_PLUGIN_URL . 'WP_CONFIG_FIX_GUIDE.md' ) . '" target="_blank" class="button button-primary">View Detailed Guide</a> ' .
                '<a href="' . \esc_url( SOFIR_PLUGIN_URL . 'wp-config-sample.php' ) . '" target="_blank" class="button">Download Sample Config</a> ' .
                '<a href="#" class="button sofir-dismiss-notice" data-notice="wp-debug-duplicate">Dismiss</a>'
            );
        }
    }

    /**
     * Read last N lines of a file
     * 
     * @param string $file File path
     * @param int    $lines Number of lines to read
     * @return array
     */
    private function tail_file( string $file, int $lines = 50 ): array {
        $handle = @\fopen( $file, 'r' );
        if ( ! $handle ) {
            return [];
        }

        $file_lines = [];
        while ( ! \feof( $handle ) ) {
            $line = \fgets( $handle );
            if ( false !== $line ) {
                $file_lines[] = $line;
                if ( \count( $file_lines ) > $lines ) {
                    \array_shift( $file_lines );
                }
            }
        }

        \fclose( $handle );

        return $file_lines;
    }

    /**
     * Add a notice to be displayed
     * 
     * @param string $type Type of notice (error, warning, info, success)
     * @param string $key Unique key for the notice
     * @param string $message Notice message
     */
    private function add_notice( string $type, string $key, string $message ): void {
        // Check if notice was dismissed
        $dismissed = \get_user_meta( \get_current_user_id(), 'sofir_dismissed_notices', true );
        if ( \is_array( $dismissed ) && \in_array( $key, $dismissed, true ) ) {
            return;
        }

        $notices = \get_transient( 'sofir_admin_notices' );
        if ( ! \is_array( $notices ) ) {
            $notices = [];
        }

        $notices[ $key ] = [
            'type'    => $type,
            'message' => $message,
        ];

        \set_transient( 'sofir_admin_notices', $notices, HOUR_IN_SECONDS );
    }

    /**
     * Display admin notices
     */
    public function display_notices(): void {
        if ( ! \current_user_can( 'manage_options' ) ) {
            return;
        }

        $notices = \get_transient( 'sofir_admin_notices' );
        if ( ! \is_array( $notices ) || empty( $notices ) ) {
            return;
        }

        foreach ( $notices as $key => $notice ) {
            printf(
                '<div class="notice notice-%s is-dismissible sofir-notice" data-notice-key="%s"><p>%s</p></div>',
                \esc_attr( $notice['type'] ),
                \esc_attr( $key ),
                $notice['message'] // Already escaped in add_notice
            );
        }

        // Add JavaScript to handle dismissal (only once)
        if ( ! $this->script_enqueued ) {
            \add_action( 'admin_print_footer_scripts', [ $this, 'add_dismissal_script' ] );
            $this->script_enqueued = true;
        }
    }

    /**
     * Add JavaScript for notice dismissal
     */
    public function add_dismissal_script(): void {
        $nonce = \wp_create_nonce( 'sofir_dismiss_notice' );
        ?>
        <script type="text/javascript">
        jQuery(document).ready(function($) {
            // Handle dismiss button click
            $('.sofir-dismiss-notice').on('click', function(e) {
                e.preventDefault();
                
                var noticeKey = $(this).data('notice');
                var $notice = $(this).closest('.sofir-notice');
                
                // Send AJAX request to dismiss notice
                $.post(ajaxurl, {
                    action: 'sofir_dismiss_notice',
                    notice_key: noticeKey,
                    nonce: <?php echo \wp_json_encode( $nonce ); ?>
                });
                
                // Hide the notice
                $notice.fadeOut();
            });
            
            // Handle WordPress native dismissal
            $('.sofir-notice').on('click', '.notice-dismiss', function() {
                var noticeKey = $(this).closest('.sofir-notice').data('notice-key');
                
                if (noticeKey) {
                    $.post(ajaxurl, {
                        action: 'sofir_dismiss_notice',
                        notice_key: noticeKey,
                        nonce: <?php echo \wp_json_encode( $nonce ); ?>
                    });
                }
            });
        });
        </script>
        <?php
    }

    /**
     * Initialize AJAX handlers
     */
    public function init_ajax(): void {
        \add_action( 'wp_ajax_sofir_dismiss_notice', [ $this, 'ajax_dismiss_notice' ] );
    }

    /**
     * Handle AJAX notice dismissal
     */
    public function ajax_dismiss_notice(): void {
        \check_ajax_referer( 'sofir_dismiss_notice', 'nonce' );

        if ( ! \current_user_can( 'manage_options' ) ) {
            \wp_die( 'Unauthorized', 403 );
        }

        $notice_key = isset( $_POST['notice_key'] ) ? \sanitize_key( $_POST['notice_key'] ) : '';
        
        if ( empty( $notice_key ) ) {
            \wp_die( 'Invalid notice key', 400 );
        }

        // Add to user's dismissed notices
        $dismissed = \get_user_meta( \get_current_user_id(), 'sofir_dismissed_notices', true );
        if ( ! \is_array( $dismissed ) ) {
            $dismissed = [];
        }

        if ( ! \in_array( $notice_key, $dismissed, true ) ) {
            $dismissed[] = $notice_key;
            \update_user_meta( \get_current_user_id(), 'sofir_dismissed_notices', $dismissed );
        }

        \wp_send_json_success();
    }
}
