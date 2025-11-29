<?php
namespace Sofir\Admin;

use Sofir\Cpt\Manager as CptManager;
use Sofir\Voxel\Manager as VoxelManager;

class VoxelPanel {
    private static ?VoxelPanel $instance = null;

    public static function instance(): VoxelPanel {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function boot(): void {
        \add_action( 'admin_post_sofir_save_voxel_settings', [ $this, 'handle_save_voxel_settings' ] );
    }

    public function render(): void {
        $voxel_manager = VoxelManager::instance();
        $is_voxel_active = $voxel_manager->is_voxel_active();
        $cpt_manager = CptManager::instance();
        $post_types = $cpt_manager->get_post_types();
        
        if ( isset( $_GET['cpt'] ) && isset( $post_types[ $_GET['cpt'] ] ) ) {
            $this->render_cpt_settings( \sanitize_key( $_GET['cpt'] ), $post_types[ $_GET['cpt'] ] );
            return;
        }

        ?>
        <div class="sofir-voxel-panel">
            <div class="sofir-voxel-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; border-radius: 12px; margin-bottom: 30px;">
                <h2 style="color: white; margin: 0 0 10px 0; font-size: 32px;">
                    <?php \esc_html_e( 'Voxel Theme Integration', 'sofir' ); ?>
                </h2>
                <p style="color: rgba(255,255,255,0.9); margin: 0; font-size: 16px;">
                    <?php \esc_html_e( 'Konfigurasi setiap CPT untuk integrasi penuh dengan Voxel Theme', 'sofir' ); ?>
                </p>
            </div>

            <?php if ( ! $is_voxel_active ) : ?>
                <div class="notice notice-warning">
                    <p>
                        <strong><?php \esc_html_e( 'Voxel Theme Tidak Aktif', 'sofir' ); ?></strong><br>
                        <?php \esc_html_e( 'Voxel theme tidak terdeteksi. Aktifkan Voxel theme untuk menggunakan fitur integrasi ini.', 'sofir' ); ?>
                    </p>
                </div>
            <?php else : ?>
                <div class="notice notice-success">
                    <p>
                        <strong><?php \esc_html_e( '✅ Voxel Theme Aktif', 'sofir' ); ?></strong><br>
                        <?php \esc_html_e( 'Voxel theme terdeteksi. Anda dapat mengkonfigurasi integrasi untuk setiap CPT.', 'sofir' ); ?>
                    </p>
                </div>
            <?php endif; ?>

            <div class="sofir-voxel-cpt-list">
                <h3><?php \esc_html_e( 'Custom Post Types', 'sofir' ); ?></h3>
                <p><?php \esc_html_e( 'Klik pada CPT untuk mengatur settings, fields, filters, dan template Elementor untuk integrasi Voxel.', 'sofir' ); ?></p>

                <div class="sofir-cpt-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px; margin-top: 20px;">
                    <?php foreach ( $post_types as $slug => $definition ) : ?>
                        <?php
                        $args = $definition['args'] ?? [];
                        $labels = $args['labels'] ?? [];
                        $singular = $labels['singular_name'] ?? ucfirst( $slug );
                        $voxel_settings = \get_option( "sofir_voxel_{$slug}_settings", [] );
                        $is_enabled = ! empty( $voxel_settings['enabled'] );
                        ?>
                        <div class="sofir-cpt-card" style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); transition: transform 0.2s;">
                            <div style="display: flex; align-items: center; margin-bottom: 15px;">
                                <span class="dashicons <?php echo \esc_attr( $args['menu_icon'] ?? 'dashicons-admin-post' ); ?>" style="font-size: 32px; width: 32px; height: 32px; margin-right: 15px; color: #667eea;"></span>
                                <div>
                                    <h4 style="margin: 0; font-size: 18px;"><?php echo \esc_html( $singular ); ?></h4>
                                    <code style="font-size: 12px; color: #666;"><?php echo \esc_html( $slug ); ?></code>
                                </div>
                            </div>

                            <?php if ( $is_enabled ) : ?>
                                <div class="sofir-voxel-status" style="padding: 8px 12px; background: #d4edda; color: #155724; border-radius: 4px; font-size: 13px; margin-bottom: 15px;">
                                    ✅ <?php \esc_html_e( 'Voxel Integration Enabled', 'sofir' ); ?>
                                </div>
                            <?php else : ?>
                                <div class="sofir-voxel-status" style="padding: 8px 12px; background: #f8f9fa; color: #6c757d; border-radius: 4px; font-size: 13px; margin-bottom: 15px;">
                                    ⚪ <?php \esc_html_e( 'Not Configured', 'sofir' ); ?>
                                </div>
                            <?php endif; ?>

                            <a href="<?php echo \esc_url( \add_query_arg( [ 'page' => 'sofir-dashboard', 'tab' => 'voxel', 'cpt' => $slug ], \admin_url( 'admin.php' ) ) ); ?>" class="button button-primary" style="width: 100%;">
                                <?php \esc_html_e( 'Configure', 'sofir' ); ?>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php
    }

    private function render_cpt_settings( string $slug, array $definition ): void {
        $args = $definition['args'] ?? [];
        $labels = $args['labels'] ?? [];
        $singular = $labels['singular_name'] ?? ucfirst( $slug );
        $fields = $definition['fields'] ?? [];
        
        $voxel_settings = \get_option( "sofir_voxel_{$slug}_settings", [
            'enabled' => false,
            'post_type_settings' => [],
            'field_mapping' => [],
            'filters' => [],
            'templates' => [
                'archive' => '',
                'single' => '',
                'add_new' => '',
                'card' => '',
                'login' => '',
                'header' => '',
                'footer' => '',
                'order' => '',
                'dashboard' => '',
            ],
            'notifications' => [
                'user' => [],
                'admin' => [],
            ],
            'user_roles' => [],
        ] );

        $back_url = \add_query_arg( [ 'page' => 'sofir-dashboard', 'tab' => 'voxel' ], \admin_url( 'admin.php' ) );
        ?>
        <div class="sofir-voxel-cpt-settings">
            <div style="margin-bottom: 20px;">
                <a href="<?php echo \esc_url( $back_url ); ?>" class="button">
                    ← <?php \esc_html_e( 'Back to CPT List', 'sofir' ); ?>
                </a>
            </div>

            <div class="sofir-voxel-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; border-radius: 12px; margin-bottom: 30px;">
                <h2 style="color: white; margin: 0 0 10px 0; font-size: 28px;">
                    <?php echo \esc_html( $singular ); ?>
                    <code style="background: rgba(255,255,255,0.2); padding: 4px 8px; border-radius: 4px; font-size: 14px; margin-left: 10px;"><?php echo \esc_html( $slug ); ?></code>
                </h2>
                <p style="color: rgba(255,255,255,0.9); margin: 0;">
                    <?php \esc_html_e( 'Konfigurasi Voxel Integration untuk CPT ini', 'sofir' ); ?>
                </p>
            </div>

            <form method="post" action="<?php echo \esc_url( \admin_url( 'admin-post.php' ) ); ?>">
                <?php \wp_nonce_field( 'sofir_voxel_settings', 'sofir_voxel_nonce' ); ?>
                <input type="hidden" name="action" value="sofir_save_voxel_settings" />
                <input type="hidden" name="cpt_slug" value="<?php echo \esc_attr( $slug ); ?>" />
                <input type="hidden" name="redirect_to" value="<?php echo \esc_url( \add_query_arg( [ 'page' => 'sofir-dashboard', 'tab' => 'voxel', 'cpt' => $slug ], \admin_url( 'admin.php' ) ) ); ?>" />

                <!-- Enable Integration -->
                <div class="sofir-settings-section" style="background: white; padding: 25px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 20px;">
                    <h3 style="margin-top: 0;">
                        <?php \esc_html_e( 'Enable Voxel Integration', 'sofir' ); ?>
                    </h3>
                    <label style="display: flex; align-items: center; cursor: pointer;">
                        <input type="checkbox" name="voxel_settings[enabled]" value="1" <?php \checked( ! empty( $voxel_settings['enabled'] ) ); ?> style="width: 20px; height: 20px; margin-right: 10px;" />
                        <span><?php \esc_html_e( 'Enable Voxel theme integration for this CPT', 'sofir' ); ?></span>
                    </label>
                    <p class="description" style="margin: 10px 0 0 30px;">
                        <?php \esc_html_e( 'Aktifkan untuk menggunakan field mapping otomatis, template Voxel, dan filter advanced.', 'sofir' ); ?>
                    </p>
                </div>

                <!-- Post Type Settings -->
                <div class="sofir-settings-section" style="background: white; padding: 25px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 20px;">
                    <h3 style="margin-top: 0;">
                        <?php \esc_html_e( 'Post Type Settings', 'sofir' ); ?>
                    </h3>
                    
                    <table class="form-table">
                        <tr>
                            <th scope="row"><?php \esc_html_e( 'Voxel Post Type Key', 'sofir' ); ?></th>
                            <td>
                                <input type="text" name="voxel_settings[post_type_settings][key]" value="<?php echo \esc_attr( $voxel_settings['post_type_settings']['key'] ?? $slug ); ?>" class="regular-text" />
                                <p class="description"><?php \esc_html_e( 'Unique key untuk Voxel post type (default: slug CPT)', 'sofir' ); ?></p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><?php \esc_html_e( 'Voxel Icon', 'sofir' ); ?></th>
                            <td>
                                <input type="text" name="voxel_settings[post_type_settings][icon]" value="<?php echo \esc_attr( $voxel_settings['post_type_settings']['icon'] ?? 'admin-post' ); ?>" class="regular-text" />
                                <p class="description"><?php \esc_html_e( 'Icon untuk Voxel post type (e.g., location-alt, calendar-alt)', 'sofir' ); ?></p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><?php \esc_html_e( 'Enable Search', 'sofir' ); ?></th>
                            <td>
                                <label>
                                    <input type="checkbox" name="voxel_settings[post_type_settings][search_enabled]" value="1" <?php \checked( ! empty( $voxel_settings['post_type_settings']['search_enabled'] ) ); ?> />
                                    <?php \esc_html_e( 'Enable advanced search untuk post type ini', 'sofir' ); ?>
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><?php \esc_html_e( 'Enable Map', 'sofir' ); ?></th>
                            <td>
                                <label>
                                    <input type="checkbox" name="voxel_settings[post_type_settings][map_enabled]" value="1" <?php \checked( ! empty( $voxel_settings['post_type_settings']['map_enabled'] ) ); ?> />
                                    <?php \esc_html_e( 'Enable map view untuk post type ini', 'sofir' ); ?>
                                </label>
                            </td>
                        </tr>
                    </table>
                </div>

                <!-- Field Mapping -->
                <div class="sofir-settings-section" style="background: white; padding: 25px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 20px;">
                    <h3 style="margin-top: 0;">
                        <?php \esc_html_e( 'Field Mapping', 'sofir' ); ?>
                    </h3>
                    <p><?php \esc_html_e( 'Map SOFIR fields ke Voxel field types untuk kompatibilitas penuh.', 'sofir' ); ?></p>

                    <?php if ( ! empty( $fields ) ) : ?>
                        <table class="wp-list-table widefat striped">
                            <thead>
                                <tr>
                                    <th><?php \esc_html_e( 'SOFIR Field', 'sofir' ); ?></th>
                                    <th><?php \esc_html_e( 'Field Type', 'sofir' ); ?></th>
                                    <th><?php \esc_html_e( 'Voxel Type', 'sofir' ); ?></th>
                                    <th><?php \esc_html_e( 'Show in Card', 'sofir' ); ?></th>
                                    <th><?php \esc_html_e( 'Show in Single', 'sofir' ); ?></th>
                                    <th><?php \esc_html_e( 'Searchable', 'sofir' ); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ( $fields as $field_key => $field_config ) : ?>
                                    <?php
                                    $field_mapping = $voxel_settings['field_mapping'][ $field_key ] ?? [];
                                    $voxel_type_options = $this->get_voxel_field_types();
                                    $auto_mapped_type = $this->auto_map_field_type( $field_config['type'] ?? 'text' );
                                    ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo \esc_html( $field_config['label'] ?? $field_key ); ?></strong>
                                            <br><code style="font-size: 11px;"><?php echo \esc_html( $field_key ); ?></code>
                                        </td>
                                        <td><?php echo \esc_html( $field_config['type'] ?? 'text' ); ?></td>
                                        <td>
                                            <select name="voxel_settings[field_mapping][<?php echo \esc_attr( $field_key ); ?>][voxel_type]" class="regular-text">
                                                <?php foreach ( $voxel_type_options as $type_value => $type_label ) : ?>
                                                    <option value="<?php echo \esc_attr( $type_value ); ?>" <?php \selected( $field_mapping['voxel_type'] ?? $auto_mapped_type, $type_value ); ?>>
                                                        <?php echo \esc_html( $type_label ); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>
                                        <td>
                                            <input type="checkbox" name="voxel_settings[field_mapping][<?php echo \esc_attr( $field_key ); ?>][show_in_card]" value="1" <?php \checked( $field_mapping['show_in_card'] ?? true ); ?> />
                                        </td>
                                        <td>
                                            <input type="checkbox" name="voxel_settings[field_mapping][<?php echo \esc_attr( $field_key ); ?>][show_in_single]" value="1" <?php \checked( $field_mapping['show_in_single'] ?? true ); ?> />
                                        </td>
                                        <td>
                                            <input type="checkbox" name="voxel_settings[field_mapping][<?php echo \esc_attr( $field_key ); ?>][searchable]" value="1" <?php \checked( $field_mapping['searchable'] ?? ! empty( $field_config['filterable'] ) ); ?> />
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else : ?>
                        <p><?php \esc_html_e( 'No custom fields defined for this CPT.', 'sofir' ); ?></p>
                    <?php endif; ?>
                </div>

                <!-- Elementor Templates -->
                <div class="sofir-settings-section" style="background: white; padding: 25px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 20px;">
                    <h3 style="margin-top: 0;">
                        <?php \esc_html_e( 'Elementor Templates', 'sofir' ); ?>
                    </h3>
                    <p><?php \esc_html_e( 'Pilih Elementor template untuk setiap jenis page. Biarkan kosong untuk menggunakan default theme.', 'sofir' ); ?></p>

                    <?php
                    $elementor_templates = $this->get_elementor_templates();
                    $template_types = [
                        'archive' => \__( 'Archive Page', 'sofir' ),
                        'single' => \__( 'Single Page', 'sofir' ),
                        'add_new' => \__( 'Add New Page', 'sofir' ),
                        'card' => \__( 'Card Design', 'sofir' ),
                        'login' => \__( 'Login Page', 'sofir' ),
                        'header' => \__( 'Header', 'sofir' ),
                        'footer' => \__( 'Footer', 'sofir' ),
                        'order' => \__( 'Order Page', 'sofir' ),
                        'dashboard' => \__( 'Dashboard Page', 'sofir' ),
                    ];
                    ?>

                    <table class="form-table">
                        <?php foreach ( $template_types as $type_key => $type_label ) : ?>
                            <tr>
                                <th scope="row"><?php echo \esc_html( $type_label ); ?></th>
                                <td>
                                    <select name="voxel_settings[templates][<?php echo \esc_attr( $type_key ); ?>]" class="regular-text">
                                        <option value=""><?php \esc_html_e( '— Select Template —', 'sofir' ); ?></option>
                                        <?php foreach ( $elementor_templates as $template ) : ?>
                                            <option value="<?php echo \esc_attr( $template['id'] ); ?>" <?php \selected( $voxel_settings['templates'][ $type_key ] ?? '', $template['id'] ); ?>>
                                                <?php echo \esc_html( $template['title'] ); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <?php if ( ! empty( $voxel_settings['templates'][ $type_key ] ) ) : ?>
                                        <a href="<?php echo \esc_url( \admin_url( 'post.php?post=' . $voxel_settings['templates'][ $type_key ] . '&action=elementor' ) ); ?>" target="_blank" class="button button-small" style="margin-left: 10px;">
                                            <?php \esc_html_e( 'Edit with Elementor', 'sofir' ); ?>
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                </div>

                <!-- Filters -->
                <div class="sofir-settings-section" style="background: white; padding: 25px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 20px;">
                    <h3 style="margin-top: 0;">
                        <?php \esc_html_e( 'Advanced Filters', 'sofir' ); ?>
                    </h3>
                    <p><?php \esc_html_e( 'Konfigurasi filter yang tersedia di search form Voxel.', 'sofir' ); ?></p>

                    <?php
                    $filter_types = [
                        'keyword' => \__( 'Keyword Search', 'sofir' ),
                        'location' => \__( 'Location Filter', 'sofir' ),
                        'category' => \__( 'Category Filter', 'sofir' ),
                        'price' => \__( 'Price Range', 'sofir' ),
                        'rating' => \__( 'Rating Filter', 'sofir' ),
                        'date' => \__( 'Date Range', 'sofir' ),
                        'open_now' => \__( 'Open Now Filter', 'sofir' ),
                    ];
                    ?>

                    <table class="form-table">
                        <?php foreach ( $filter_types as $filter_key => $filter_label ) : ?>
                            <tr>
                                <th scope="row"><?php echo \esc_html( $filter_label ); ?></th>
                                <td>
                                    <label>
                                        <input type="checkbox" name="voxel_settings[filters][<?php echo \esc_attr( $filter_key ); ?>]" value="1" <?php \checked( ! empty( $voxel_settings['filters'][ $filter_key ] ) ); ?> />
                                        <?php \esc_html_e( 'Enable this filter', 'sofir' ); ?>
                                    </label>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                </div>

                <!-- Notifications -->
                <div class="sofir-settings-section" style="background: white; padding: 25px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 20px;">
                    <h3 style="margin-top: 0;">
                        <?php \esc_html_e( 'Notification Settings', 'sofir' ); ?>
                    </h3>

                    <h4><?php \esc_html_e( 'User Notifications', 'sofir' ); ?></h4>
                    <table class="form-table">
                        <tr>
                            <th scope="row"><?php \esc_html_e( 'New Post Published', 'sofir' ); ?></th>
                            <td>
                                <label>
                                    <input type="checkbox" name="voxel_settings[notifications][user][new_post]" value="1" <?php \checked( ! empty( $voxel_settings['notifications']['user']['new_post'] ) ); ?> />
                                    <?php \esc_html_e( 'Notify user when their post is published', 'sofir' ); ?>
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><?php \esc_html_e( 'Post Status Change', 'sofir' ); ?></th>
                            <td>
                                <label>
                                    <input type="checkbox" name="voxel_settings[notifications][user][status_change]" value="1" <?php \checked( ! empty( $voxel_settings['notifications']['user']['status_change'] ) ); ?> />
                                    <?php \esc_html_e( 'Notify user when post status changes', 'sofir' ); ?>
                                </label>
                            </td>
                        </tr>
                    </table>

                    <h4><?php \esc_html_e( 'Admin Notifications', 'sofir' ); ?></h4>
                    <table class="form-table">
                        <tr>
                            <th scope="row"><?php \esc_html_e( 'New Post Submitted', 'sofir' ); ?></th>
                            <td>
                                <label>
                                    <input type="checkbox" name="voxel_settings[notifications][admin][new_post]" value="1" <?php \checked( ! empty( $voxel_settings['notifications']['admin']['new_post'] ) ); ?> />
                                    <?php \esc_html_e( 'Notify admin when new post is submitted', 'sofir' ); ?>
                                </label>
                            </td>
                        </tr>
                    </table>
                </div>

                <!-- User Roles -->
                <div class="sofir-settings-section" style="background: white; padding: 25px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 20px;">
                    <h3 style="margin-top: 0;">
                        <?php \esc_html_e( 'User Role Settings', 'sofir' ); ?>
                    </h3>
                    <p><?php \esc_html_e( 'Pilih role yang dapat create/edit post type ini dari frontend.', 'sofir' ); ?></p>

                    <?php
                    $roles = \wp_roles()->get_names();
                    $allowed_roles = $voxel_settings['user_roles'] ?? [];
                    ?>

                    <table class="form-table">
                        <?php foreach ( $roles as $role_key => $role_name ) : ?>
                            <tr>
                                <th scope="row"><?php echo \esc_html( $role_name ); ?></th>
                                <td>
                                    <label>
                                        <input type="checkbox" name="voxel_settings[user_roles][]" value="<?php echo \esc_attr( $role_key ); ?>" <?php \checked( in_array( $role_key, $allowed_roles, true ) ); ?> />
                                        <?php \esc_html_e( 'Allow this role to manage posts', 'sofir' ); ?>
                                    </label>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                </div>

                <div style="position: sticky; bottom: 0; background: white; padding: 20px; box-shadow: 0 -2px 8px rgba(0,0,0,0.1); margin: 0 -20px -20px -20px; border-radius: 0 0 8px 8px;">
                    <button type="submit" class="button button-primary button-large">
                        <?php \esc_html_e( 'Save Voxel Settings', 'sofir' ); ?>
                    </button>
                    <a href="<?php echo \esc_url( $back_url ); ?>" class="button button-large" style="margin-left: 10px;">
                        <?php \esc_html_e( 'Cancel', 'sofir' ); ?>
                    </a>
                </div>
            </form>
        </div>
        <?php
    }

    public function handle_save_voxel_settings(): void {
        if ( ! \check_admin_referer( 'sofir_voxel_settings', 'sofir_voxel_nonce' ) ) {
            \wp_die( \esc_html__( 'Security check failed', 'sofir' ) );
        }

        if ( ! \current_user_can( 'manage_options' ) ) {
            \wp_die( \esc_html__( 'You do not have permission to access this page', 'sofir' ) );
        }

        $cpt_slug = isset( $_POST['cpt_slug'] ) ? \sanitize_key( $_POST['cpt_slug'] ) : '';
        if ( empty( $cpt_slug ) ) {
            \wp_die( \esc_html__( 'Invalid CPT slug', 'sofir' ) );
        }

        $voxel_settings = isset( $_POST['voxel_settings'] ) ? $_POST['voxel_settings'] : [];
        
        $sanitized_settings = [
            'enabled' => ! empty( $voxel_settings['enabled'] ),
            'post_type_settings' => [
                'key' => \sanitize_key( $voxel_settings['post_type_settings']['key'] ?? $cpt_slug ),
                'icon' => \sanitize_text_field( $voxel_settings['post_type_settings']['icon'] ?? '' ),
                'search_enabled' => ! empty( $voxel_settings['post_type_settings']['search_enabled'] ),
                'map_enabled' => ! empty( $voxel_settings['post_type_settings']['map_enabled'] ),
            ],
            'field_mapping' => [],
            'filters' => [],
            'templates' => [],
            'notifications' => [
                'user' => [],
                'admin' => [],
            ],
            'user_roles' => [],
        ];

        if ( isset( $voxel_settings['field_mapping'] ) && is_array( $voxel_settings['field_mapping'] ) ) {
            foreach ( $voxel_settings['field_mapping'] as $field_key => $mapping ) {
                $sanitized_settings['field_mapping'][ $field_key ] = [
                    'voxel_type' => \sanitize_key( $mapping['voxel_type'] ?? 'text' ),
                    'show_in_card' => ! empty( $mapping['show_in_card'] ),
                    'show_in_single' => ! empty( $mapping['show_in_single'] ),
                    'searchable' => ! empty( $mapping['searchable'] ),
                ];
            }
        }

        if ( isset( $voxel_settings['filters'] ) && is_array( $voxel_settings['filters'] ) ) {
            foreach ( $voxel_settings['filters'] as $filter_key => $enabled ) {
                if ( $enabled ) {
                    $sanitized_settings['filters'][ \sanitize_key( $filter_key ) ] = true;
                }
            }
        }

        if ( isset( $voxel_settings['templates'] ) && is_array( $voxel_settings['templates'] ) ) {
            foreach ( $voxel_settings['templates'] as $template_key => $template_id ) {
                $sanitized_settings['templates'][ \sanitize_key( $template_key ) ] = absint( $template_id );
            }
        }

        if ( isset( $voxel_settings['notifications']['user'] ) && is_array( $voxel_settings['notifications']['user'] ) ) {
            foreach ( $voxel_settings['notifications']['user'] as $notif_key => $enabled ) {
                if ( $enabled ) {
                    $sanitized_settings['notifications']['user'][ \sanitize_key( $notif_key ) ] = true;
                }
            }
        }

        if ( isset( $voxel_settings['notifications']['admin'] ) && is_array( $voxel_settings['notifications']['admin'] ) ) {
            foreach ( $voxel_settings['notifications']['admin'] as $notif_key => $enabled ) {
                if ( $enabled ) {
                    $sanitized_settings['notifications']['admin'][ \sanitize_key( $notif_key ) ] = true;
                }
            }
        }

        if ( isset( $voxel_settings['user_roles'] ) && is_array( $voxel_settings['user_roles'] ) ) {
            foreach ( $voxel_settings['user_roles'] as $role ) {
                $sanitized_settings['user_roles'][] = \sanitize_key( $role );
            }
        }

        \update_option( "sofir_voxel_{$cpt_slug}_settings", $sanitized_settings );

        $redirect_to = isset( $_POST['redirect_to'] ) ? \esc_url_raw( $_POST['redirect_to'] ) : \admin_url( 'admin.php?page=sofir-dashboard&tab=voxel' );
        $redirect_to = \add_query_arg( 'voxel_settings_saved', '1', $redirect_to );
        
        \wp_safe_redirect( $redirect_to );
        exit;
    }

    private function get_voxel_field_types(): array {
        return [
            'text' => \__( 'Text', 'sofir' ),
            'textarea' => \__( 'Textarea', 'sofir' ),
            'number' => \__( 'Number', 'sofir' ),
            'email' => \__( 'Email', 'sofir' ),
            'url' => \__( 'URL', 'sofir' ),
            'date' => \__( 'Date', 'sofir' ),
            'select' => \__( 'Select', 'sofir' ),
            'location' => \__( 'Location', 'sofir' ),
            'work-hours' => \__( 'Work Hours', 'sofir' ),
            'image' => \__( 'Image', 'sofir' ),
            'file' => \__( 'File', 'sofir' ),
            'repeater' => \__( 'Repeater', 'sofir' ),
        ];
    }

    private function auto_map_field_type( string $sofir_type ): string {
        $mapping = [
            'location' => 'location',
            'hours' => 'work-hours',
            'rating' => 'number',
            'status' => 'select',
            'price' => 'number',
            'contact' => 'email',
            'gallery' => 'image',
            'attributes' => 'repeater',
            'event_date' => 'date',
            'event_capacity' => 'number',
            'appointment_datetime' => 'date',
            'appointment_duration' => 'number',
            'appointment_status' => 'select',
        ];

        return $mapping[ $sofir_type ] ?? 'text';
    }

    private function get_elementor_templates(): array {
        if ( ! class_exists( '\Elementor\Plugin' ) ) {
            return [];
        }

        $templates = \get_posts( [
            'post_type' => 'elementor_library',
            'posts_per_page' => -1,
            'post_status' => 'publish',
            'orderby' => 'title',
            'order' => 'ASC',
        ] );

        $result = [];
        foreach ( $templates as $template ) {
            $result[] = [
                'id' => $template->ID,
                'title' => $template->post_title,
            ];
        }

        return $result;
    }
}
