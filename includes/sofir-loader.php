<?php
namespace Sofir;

use Sofir\Admin\Manager as AdminManager;
use Sofir\Cpt\Manager as CptManager;
use Sofir\Ai\Builder as AiBuilder;
use Sofir\Blocks\Registrar as BlocksRegistrar;
use Sofir\Directory\Manager as DirectoryManager;
use Sofir\Membership\Manager as MembershipManager;
use Sofir\Rest\Router as RestRouter;
use Sofir\Seo\Engine as SeoEngine;
use Sofir\Templates\Manager as TemplateManager;

class Loader {
    /** @var class-string[] */
    private array $modules = [];

    /** @var array<class-string, object> */
    private array $resolved = [];

    private static ?Loader $instance = null;

    private function __construct() {
        $this->modules = $this->discover_modules();
    }

    public static function instance(): Loader {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function boot(): void {
        foreach ( $this->modules as $module ) {
            $instance = $this->resolve_module( $module );

            if ( ! $instance ) {
                continue;
            }

            if ( method_exists( $instance, 'boot' ) ) {
                $instance->boot();
            } elseif ( method_exists( $instance, 'register' ) ) {
                $instance->register();
            }
        }
    }

    /**
     * @return class-string[]
     */
    private function discover_modules(): array {
        $modules = [
            AdminManager::class,
            CptManager::class,
            TemplateManager::class,
            SeoEngine::class,
            Importer::class,
            Enhancer::class,
            RestRouter::class,
            DirectoryManager::class,
            MembershipManager::class,
            AiBuilder::class,
            BlocksRegistrar::class,
        ];

        /** @var class-string[] $modules */
        $modules = \apply_filters( 'sofir/modules', $modules );

        return array_unique( array_filter( $modules, 'is_string' ) );
    }

    private function resolve_module( string $module ): ?object {
        if ( isset( $this->resolved[ $module ] ) ) {
            return $this->resolved[ $module ];
        }

        if ( ! class_exists( $module ) ) {
            return null;
        }

        $instance = null;

        if ( method_exists( $module, 'instance' ) ) {
            $instance = $module::instance();
        } else {
            $instance = new $module();
        }

        $this->resolved[ $module ] = $instance;

        return $instance;
    }
}
