<?php
namespace Sofir\Bootstrap;

class Lifecycle {
    public static function activate(): void {
        \do_action( 'sofir/activate/before_flush' );
        \flush_rewrite_rules();
        \do_action( 'sofir/activate/after_flush' );
    }

    public static function deactivate(): void {
        \do_action( 'sofir/deactivate/before_flush' );
        \flush_rewrite_rules();
        \do_action( 'sofir/deactivate/after_flush' );
    }
}
