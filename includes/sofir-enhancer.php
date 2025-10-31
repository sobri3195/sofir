<?php
namespace Sofir;

use Sofir\Enhancement\Auth;
use Sofir\Enhancement\Dashboard;
use Sofir\Enhancement\Performance;
use Sofir\Enhancement\Web;
use Sofir\Enhancement\Security;

class Enhancer {
    private static ?Enhancer $instance = null;

    public static function instance(): Enhancer {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function boot(): void {
        Security::instance()->boot();
        Performance::instance()->boot();
        Auth::instance()->boot();
        Dashboard::instance()->boot();
        Web::instance()->boot();

        \do_action( 'sofir/enhancer/booted' );
    }
}
