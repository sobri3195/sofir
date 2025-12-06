<?php
namespace Sofir\WooCommerceAddon\Addons;

class White_Label extends Addon_Base {
    public function get_id(): string {
        return 'white-label';
    }

    public function get_name(): string {
        return __( 'White Label', 'sofir' );
    }

    public function get_description(): string {
        return __( 'Add your own branding while building client sites with custom plugin name, email, and settings.', 'sofir' );
    }

    public function get_category(): string {
        return 'flexibility';
    }

    public function get_icon(): string {
        return 'dashicons-admin-customizer';
    }

    public function get_settings(): array {
        return [
            'enable_white_label' => [
                'type' => 'checkbox',
                'label' => __( 'Enable White Label', 'sofir' ),
                'default' => false,
            ],
            'plugin_name' => [
                'type' => 'text',
                'label' => __( 'Plugin Name', 'sofir' ),
                'default' => 'WooCommerce Addon',
                'description' => __( 'Display name for the plugin', 'sofir' ),
            ],
            'plugin_description' => [
                'type' => 'textarea',
                'label' => __( 'Plugin Description', 'sofir' ),
                'default' => __( 'Enhanced WooCommerce functionality with multiple addons', 'sofir' ),
                'rows' => 3,
            ],
            'plugin_author' => [
                'type' => 'text',
                'label' => __( 'Plugin Author', 'sofir' ),
                'default' => 'Your Company',
            ],
            'plugin_author_uri' => [
                'type' => 'url',
                'label' => __( 'Author Website', 'sofir' ),
                'default' => '',
            ],
            'plugin_version' => [
                'type' => 'text',
                'label' => __( 'Plugin Version', 'sofir' ),
                'default' => '1.0.0',
                'description' => __( 'Display version for the plugin', 'sofir' ),
            ],
            'admin_email' => [
                'type' => 'email',
                'label' => __( 'Support Email', 'sofir' ),
                'default' => \\get_option( 'admin_email' ),
                'description' => __( 'Email for support notifications', 'sofir' ),
            ],
            'hide_sofir_branding' => [
                'type' => 'checkbox',
                'label' => __( 'Hide SOFIR Branding', 'sofir' ),
                'default' => false,
                'description' => __( 'Remove all SOFIR references from admin interface', 'sofir' ),
            ],
            'custom_logo_url' => [
                'type' => 'url',
                'label' => __( 'Custom Logo URL', 'sofir' ),
                'default' => '',
                'description' => __( 'URL to custom logo for admin header', 'sofir' ),
            ],
            'custom_color_scheme' => [
                'type' => 'color',
                'label' => __( 'Primary Color', 'sofir' ),
                'default' => '#0073aa',
                'description' => __( 'Primary color for admin interface', 'sofir' ),
            ],
            'hide_developer_info' => [
                'type' => 'checkbox',
                'label' => __( 'Hide Developer Info', 'sofir' ),
                'default' => false,
                'description' => __( 'Hide developer credits and information', 'sofir' ),
            ],
            'custom_footer_text' => [
                'type' => 'text',
                'label' => __( 'Custom Footer Text', 'sofir' ),
                'default' => '',
                'description' => __( 'Custom text for admin footer', 'sofir' ),
            ],
        ];
    }

    public function enable(): void {
        parent::enable();
        
        \\add_action( 'admin_init', [ $this, 'apply_white_label_settings' ] );
        \\add_action( 'admin_menu', [ $this, 'modify_admin_menu' ], 999 );
        \\add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_white_label_styles' ] );
        \\add_filter( 'plugin_row_meta', [ $this, 'modify_plugin_row_meta' ], 10, 2 );
        \\add_filter( 'plugin_action_links_' . plugin_basename( SOFIR_PLUGIN_DIR . '/sofir.php' ), [ $this, 'modify_plugin_action_links' ] );
        \\add_action( 'admin_footer_text', [ $this, 'modify_admin_footer' ] );
        \\add_action( 'wp_head', [ $this, 'remove_generator_tags' ] );
        \\add_filter( 'admin_title', [ $this, 'modify_admin_title' ] );
        \\add_filter( 'update_footer', [ $this, 'modify_admin_footer_version' ], 99 );
        \\add_action( 'wp_dashboard_setup', [ $this, 'remove_dashboard_widgets' ] );
        \\add_action( 'admin_notices', [ $this, 'remove_admin_notices' ] );
        \\add_filter( 'gettext', [ $this, 'modify_admin_text' ], 10, 3 );
        \\add_filter( 'ngettext', [ $this, 'modify_admin_text_plural' ], 10, 5 );
    }

    public function disable(): void {
        parent::disable();
        
        \\remove_action( 'admin_init', [ $this, 'apply_white_label_settings' ] );
        \\remove_action( 'admin_menu', [ $this, 'modify_admin_menu' ], 999 );
        \\remove_action( 'admin_enqueue_scripts', [ $this, 'enqueue_white_label_styles' ] );
        \\remove_filter( 'plugin_row_meta', [ $this, 'modify_plugin_row_meta' ], 10 );
        \\remove_filter( 'plugin_action_links_' . plugin_basename( SOFIR_PLUGIN_DIR . '/sofir.php' ), [ $this, 'modify_plugin_action_links' ] );
        \\remove_action( 'admin_footer_text', [ $this, 'modify_admin_footer' ] );
        \\remove_action( 'wp_head', [ $this, 'remove_generator_tags' ] );
        \\remove_filter( 'admin_title', [ $this, 'modify_admin_title' ] );
        \\remove_filter( 'update_footer', [ $this, 'modify_admin_footer_version' ], 99 );
        \\remove_action( 'wp_dashboard_setup', [ $this, 'remove_dashboard_widgets' ] );
        \\remove_action( 'admin_notices', [ $this, 'remove_admin_notices' ] );
        \\remove_filter( 'gettext', [ $this, 'modify_admin_text' ], 10 );
        \\remove_filter( 'ngettext', [ $this, 'modify_admin_text_plural' ], 10 );
    }

    public function apply_white_label_settings(): void {
        if ( ! \\get_option( 'sofir_wc_addon_white_label_enable_white_label', false ) ) {
            return;
        }

        global $wp_filter;
        
        // Store original plugin data for restoration
        if ( ! isset( $wp_filter['sofir_original_plugin_data'] ) ) {
            $wp_filter['sofir_original_plugin_data'] = [
                'Name' => 'SOFIR',
                'PluginURI' => 'https://sofir.com',
                'Version' => '1.0.0',
                'Author' => 'SOFIR Team',
                'AuthorURI' => 'https://sofir.com',
                'Description' => __( 'SOFIR WordPress Plugin', 'sofir' ),
            ];
        }
    }

    public function modify_admin_menu(): void {
        if ( ! \\get_option( 'sofir_wc_addon_white_label_enable_white_label', false ) ) {
            return;
        }

        $plugin_name = \\get_option( 'sofir_wc_addon_white_label_plugin_name', 'WooCommerce Addon' );
        
        // Modify the main menu item
        global $menu;
        foreach ( $menu as $key => $item ) {
            if ( $item[2] === 'sofir-woocommerce-addon' ) {
                $menu[$key][0] = $plugin_name;
                break;
            }
        }

        // Modify submenu items
        global $submenu;
        if ( isset( $submenu['sofir-woocommerce-addon'] ) ) {
            foreach ( $submenu['sofir-woocommerce-addon'] as $key => $item ) {
                if ( $item[2] === 'sofir-woocommerce-addon' ) {
                    $submenu['sofir-woocommerce-addon'][$key][0] = __( 'Dashboard', 'sofir' );
                }
            }
        }
    }

    public function enqueue_white_label_styles(): void {
        if ( ! \\get_option( 'sofir_wc_addon_white_label_enable_white_label', false ) ) {
            return;
        }

        $custom_color = \\get_option( 'sofir_wc_addon_white_label_custom_color_scheme', '#0073aa' );
        $custom_logo = \\get_option( 'sofir_wc_addon_white_label_custom_logo_url', '' );
        $hide_branding = \\get_option( 'sofir_wc_addon_white_label_hide_sofir_branding', false );

        $css = '';
        
        if ( ! empty( $custom_color ) ) {
            $css .= "
                #adminmenu .toplevel_page_sofir-woocommerce-addon .wp-menu-image,
                #adminmenu .toplevel_page_sofir-woocommerce-addon:hover .wp-menu-image {
                    color: {$custom_color} !important;
                }
                .sofir-admin-header,
                .sofir-button-primary {
                    background-color: {$custom_color} !important;
                }
                .sofir-accent {
                    color: {$custom_color} !important;
                }
            ";
        }

        if ( ! empty( $custom_logo ) ) {
            $css .= "
                .toplevel_page_sofir-woocommerce-addon .wp-menu-image img {
                    content: url('{$custom_logo}') !important;
                    width: 20px;
                    height: 20px;
                }
            ";
        }

        if ( $hide_branding ) {
            $css .= "
                .sofir-branding,
                .sofir-powered-by,
                .sofir-footer-info,
                .toplevel_page_sofir-woocommerce-addon .wp-menu-name .sofir-badge {
                    display: none !important;
                }
                .sofir-admin-header .site-title {
                    font-size: 0;
                }
                .sofir-admin-header .site-title:after {
                    content: '" . \\get_option( 'sofir_wc_addon_white_label_plugin_name', 'WooCommerce Addon' ) . "';
                    font-size: 14px;
                }
            ";
        }

        if ( ! empty( $css ) ) {
            \\wp_add_inline_style( 'admin', $css );
        }
    }

    public function modify_plugin_row_meta( $links, $file ): array {
        if ( ! \\get_option( 'sofir_wc_addon_white_label_enable_white_label', false ) ) {
            return $links;
        }

        if ( strpos( $file, 'sofir.php' ) !== false ) {
            $hide_branding = \\get_option( 'sofir_wc_addon_white_label_hide_sofir_branding', false );
            
            if ( $hide_branding ) {
                // Remove all meta links
                return [];
            }
            
            // Modify meta links
            $author_uri = \\get_option( 'sofir_wc_addon_white_label_plugin_author_uri', '' );
            if ( ! empty( $author_uri ) ) {
                $links = [
                    '<a href="' . esc_url( $author_uri ) . '">' . __( 'Author', 'sofir' ) . '</a>',
                ];
            }
        }

        return $links;
    }

    public function modify_plugin_action_links( $links ): array {
        if ( ! \\get_option( 'sofir_wc_addon_white_label_enable_white_label', false ) ) {
            return $links;
        }

        $hide_branding = \\get_option( 'sofir_wc_addon_white_label_hide_sofir_branding', false );
        
        if ( $hide_branding ) {
            // Remove SOFIR-specific action links
            unset( $links['settings'] );
            unset( $links['documentation'] );
            
            // Add custom support link
            $support_email = \\get_option( 'sofir_wc_addon_white_label_admin_email', \\get_option( 'admin_email' ) );
            if ( ! empty( $support_email ) ) {
                $links['support'] = '<a href="mailto:' . esc_attr( $support_email ) . '">' . __( 'Support', 'sofir' ) . '</a>';
            }
        }

        return $links;
    }

    public function modify_admin_footer( $text ): string {
        if ( ! \\get_option( 'sofir_wc_addon_white_label_enable_white_label', false ) ) {
            return $text;
        }

        $custom_footer = \\get_option( 'sofir_wc_addon_white_label_custom_footer_text', '' );
        
        if ( ! empty( $custom_footer ) ) {
            return $custom_footer;
        }

        $hide_branding = \\get_option( 'sofir_wc_addon_white_label_hide_sofir_branding', false );
        
        if ( $hide_branding ) {
            $plugin_name = \\get_option( 'sofir_wc_addon_white_label_plugin_name', 'WooCommerce Addon' );
            $plugin_version = \\get_option( 'sofir_wc_addon_white_label_plugin_version', '1.0.0' );
            return sprintf( __( 'Thank you for using %s version %s', 'sofir' ), $plugin_name, $plugin_version );
        }

        return $text;
    }

    public function remove_generator_tags(): void {
        if ( ! \\get_option( 'sofir_wc_addon_white_label_enable_white_label', false ) ) {
            return;
        }

        $hide_branding = \\get_option( 'sofir_wc_addon_white_label_hide_sofir_branding', false );
        
        if ( $hide_branding ) {
            \\remove_action( 'wp_head', 'wp_generator' );
            \\remove_action( 'wp_head', 'wlwmanifest_link' );
            \\remove_action( 'wp_head', 'rsd_link' );
        }
    }

    public function modify_admin_title( $admin_title ): string {
        if ( ! \\get_option( 'sofir_wc_addon_white_label_enable_white_label', false ) ) {
            return $admin_title;
        }

        $hide_branding = \\get_option( 'sofir_wc_addon_white_label_hide_sofir_branding', false );
        
        if ( $hide_branding ) {
            $plugin_name = \\get_option( 'sofir_wc_addon_white_label_plugin_name', 'WooCommerce Addon' );
            $admin_title = str_replace( 'SOFIR', $plugin_name, $admin_title );
        }

        return $admin_title;
    }

    public function modify_admin_footer_version( $content ): string {
        if ( ! \\get_option( 'sofir_wc_addon_white_label_enable_white_label', false ) ) {
            return $content;
        }

        $hide_branding = \\get_option( 'sofir_wc_addon_white_label_hide_sofir_branding', false );
        
        if ( $hide_branding ) {
            $plugin_version = \\get_option( 'sofir_wc_addon_white_label_plugin_version', '1.0.0' );
            return $plugin_version;
        }

        return $content;
    }

    public function remove_dashboard_widgets(): void {
        if ( ! \\get_option( 'sofir_wc_addon_white_label_enable_white_label', false ) ) {
            return;
        }

        $hide_branding = \\get_option( 'sofir_wc_addon_white_label_hide_sofir_branding', false );
        
        if ( $hide_branding ) {
            // Remove SOFIR dashboard widgets
            \\remove_meta_box( 'sofir_dashboard_widget', 'dashboard', 'normal' );
            \\remove_meta_box( 'sofir_news_widget', 'dashboard', 'side' );
        }
    }

    public function remove_admin_notices(): void {
        if ( ! \\get_option( 'sofir_wc_addon_white_label_enable_white_label', false ) ) {
            return;
        }

        $hide_branding = \\get_option( 'sofir_wc_addon_white_label_hide_sofir_branding', false );
        
        if ( $hide_branding ) {
            // Remove SOFIR admin notices
            \\remove_action( 'admin_notices', 'sofir_admin_notices' );
            \\remove_action( 'network_admin_notices', 'sofir_network_admin_notices' );
        }
    }

    public function modify_admin_text( $translated_text, $original_text, $domain ): string {
        if ( ! \\get_option( 'sofir_wc_addon_white_label_enable_white_label', false ) ) {
            return $translated_text;
        }

        $hide_branding = \\get_option( 'sofir_wc_addon_white_label_hide_sofir_branding', false );
        
        if ( ! $hide_branding || $domain !== 'sofir' ) {
            return $translated_text;
        }

        $plugin_name = \\get_option( 'sofir_wc_addon_white_label_plugin_name', 'WooCommerce Addon' );
        
        // Replace SOFIR references
        $replacements = [
            'SOFIR' => $plugin_name,
            'Sofir' => $plugin_name,
            'sofir' => strtolower( str_replace( ' ', '', $plugin_name ) ),
        ];

        foreach ( $replacements as $search => $replace ) {
            $translated_text = str_replace( $search, $replace, $translated_text );
        }

        return $translated_text;
    }

    public function modify_admin_text_plural( $translated_text, $single, $plural, $number, $domain ): string {
        if ( ! \\get_option( 'sofir_wc_addon_white_label_enable_white_label', false ) ) {
            return $translated_text;
        }

        return $this->modify_admin_text( $translated_text, $single, $domain );
    }

    public function get_white_label_plugin_data(): array {
        if ( ! \\get_option( 'sofir_wc_addon_white_label_enable_white_label', false ) ) {
            return [];
        }

        return [
            'Name' => \\get_option( 'sofir_wc_addon_white_label_plugin_name', 'WooCommerce Addon' ),
            'PluginURI' => \\get_option( 'sofir_wc_addon_white_label_plugin_author_uri', '' ),
            'Version' => \\get_option( 'sofir_wc_addon_white_label_plugin_version', '1.0.0' ),
            'Author' => \\get_option( 'sofir_wc_addon_white_label_plugin_author', 'Your Company' ),
            'AuthorURI' => \\get_option( 'sofir_wc_addon_white_label_plugin_author_uri', '' ),
            'Description' => \\get_option( 'sofir_wc_addon_white_label_plugin_description', __( 'Enhanced WooCommerce functionality with multiple addons', 'sofir' ) ),
        ];
    }

    public function is_white_label_active(): bool {
        return \\get_option( 'sofir_wc_addon_white_label_enable_white_label', false );
    }

    public function get_branding_hidden(): bool {
        return \\get_option( 'sofir_wc_addon_white_label_hide_sofir_branding', false );
    }

    public function get_support_email(): string {
        return \\get_option( 'sofir_wc_addon_white_label_admin_email', \\get_option( 'admin_email' ) );
    }

    public function get_plugin_name(): string {
        return \\get_option( 'sofir_wc_addon_white_label_plugin_name', 'WooCommerce Addon' );
    }

    public function get_plugin_version(): string {
        return \\get_option( 'sofir_wc_addon_white_label_plugin_version', '1.0.0' );
    }
}
