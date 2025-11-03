<?php
/**
 * Plugin Name:       SOFIR
 * Plugin URI:        https://example.com/sofir
 * Description:       Complete WordPress solution with 28 Gutenberg blocks, directory, membership, payments (Duitku/Xendit/Midtrans), webhooks (Bit Integration), loyalty program, and mobile support.
 * Version:           0.1.0
 * Author:            SOFIR Team
 * Author URI:        https://example.com
 * Text Domain:       sofir
 * Domain Path:       /languages
 * Requires at least: 6.3
 * Requires PHP:      8.0
 */

declare( strict_types=1 );

namespace Sofir {

if ( ! \defined( 'ABSPATH' ) ) {
    exit;
}

require_once __DIR__ . '/includes/sofir-bootstrap-lifecycle.php';

final class Plugin {
    private const VERSION = '0.1.0';

    private static ?Plugin $instance = null;

    private function __construct() {
        $this->define_constants();
        $this->register_autoloader();

        \add_action( 'init', [ $this, 'load_textdomain' ], 0 );
        \add_action( 'init', [ $this, 'bootstrap' ], 1 );
    }

    public function load_textdomain(): void {
        \load_plugin_textdomain( 'sofir', false, \dirname( \plugin_basename( SOFIR_PLUGIN_FILE ) ) . '/languages' );
    }

    public static function instance(): Plugin {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function define_constants(): void {
        if ( ! \defined( 'SOFIR_VERSION' ) ) {
            \define( 'SOFIR_VERSION', self::VERSION );
        }

        if ( ! \defined( 'SOFIR_PLUGIN_FILE' ) ) {
            \define( 'SOFIR_PLUGIN_FILE', __FILE__ );
        }

        if ( ! \defined( 'SOFIR_PLUGIN_DIR' ) ) {
            \define( 'SOFIR_PLUGIN_DIR', \plugin_dir_path( __FILE__ ) );
        }

        if ( ! \defined( 'SOFIR_PLUGIN_URL' ) ) {
            \define( 'SOFIR_PLUGIN_URL', \plugin_dir_url( __FILE__ ) );
        }

        if ( ! \defined( 'SOFIR_ASSETS_URL' ) ) {
            \define( 'SOFIR_ASSETS_URL', SOFIR_PLUGIN_URL . 'assets/' );
        }
    }

    private function register_autoloader(): void {
        spl_autoload_register( static function ( string $class ): void {
            if ( 0 !== strpos( $class, __NAMESPACE__ . '\\' ) ) {
                return;
            }

            $relative           = substr( $class, strlen( __NAMESPACE__ ) + 1 );
            $relative_lower     = strtolower( $relative );
            $relative_path      = str_replace( '\\', DIRECTORY_SEPARATOR, $relative_lower );
            $slug               = str_replace( DIRECTORY_SEPARATOR, '-', $relative_path );

            $segments           = explode( '\\', $relative );
            $kebab_segments     = array_map(
                static function ( string $segment ): string {
                    $segment = preg_replace( '/(?<!^)[A-Z]/', '-$0', $segment );
                    $segment = str_replace( '_', '-', $segment );

                    return strtolower( $segment );
                },
                $segments
            );
            $kebab_relative_path = implode( DIRECTORY_SEPARATOR, $kebab_segments );
            $kebab_slug          = implode( '-', $kebab_segments );

            $candidates = [
                SOFIR_PLUGIN_DIR . 'includes/class-' . $slug . '.php',
                SOFIR_PLUGIN_DIR . 'includes/sofir-' . $slug . '.php',
                SOFIR_PLUGIN_DIR . 'includes/' . $relative_path . '.php',
                SOFIR_PLUGIN_DIR . 'modules/' . $relative_path . '.php',
                SOFIR_PLUGIN_DIR . 'modules/' . $relative_path . '/index.php',
                SOFIR_PLUGIN_DIR . 'includes/class-' . $kebab_slug . '.php',
                SOFIR_PLUGIN_DIR . 'includes/sofir-' . $kebab_slug . '.php',
                SOFIR_PLUGIN_DIR . 'includes/' . $kebab_relative_path . '.php',
                SOFIR_PLUGIN_DIR . 'modules/' . $kebab_relative_path . '.php',
                SOFIR_PLUGIN_DIR . 'modules/' . $kebab_relative_path . '/index.php',
            ];

            foreach ( array_unique( $candidates ) as $file ) {
                if ( file_exists( $file ) ) {
                    require_once $file;

                    return;
                }
            }
        } );
    }

    public function bootstrap(): void {
        \do_action( 'sofir/before_bootstrap' );

        $loader = Loader::instance();
        $loader->boot();

        \do_action( 'sofir/after_bootstrap' );
    }
}

function plugin(): Plugin {
    return Plugin::instance();
}

} // end namespace Sofir

namespace {
    \Sofir\plugin();

    \register_activation_hook( __FILE__, [ \Sofir\Bootstrap\Lifecycle::class, 'activate' ] );
    \register_deactivation_hook( __FILE__, [ \Sofir\Bootstrap\Lifecycle::class, 'deactivate' ] );
}
