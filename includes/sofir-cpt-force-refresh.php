<?php
/**
 * Force refresh CPT definitions
 * This file can be included once to force update CPT definitions with show_in_menu = true
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function sofir_force_refresh_cpt_definitions() {
    delete_option( 'sofir_cpt_definitions_version' );
    delete_option( 'sofir_multivendor_rewrite_version' );
    delete_option( 'sofir_multivendor_flush_notice_dismissed' );
    
    flush_rewrite_rules();
    
    if ( function_exists( 'add_action' ) ) {
        add_action( 'admin_notices', function() {
            ?>
            <div class="notice notice-success is-dismissible">
                <p><strong>SOFIR:</strong> CPT definitions telah di-refresh. Menu CPT sekarang akan tampil di admin sidebar.</p>
            </div>
            <?php
        } );
    }
}
