<?php
namespace Sofir\Multivendor;

class Manager {
    private static ?Manager $instance = null;

    public static function instance(): Manager {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function boot(): void {
        \add_action( 'init', [ $this, 'register_vendor_role' ] );
        \add_action( 'init', [ $this, 'register_vendor_cpt' ] );
        \add_action( 'init', [ $this, 'flush_rewrite_rules_on_first_activation' ] );
        \add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_frontend_assets' ] );
        \add_action( 'admin_menu', [ $this, 'add_vendor_menu' ] );
        \add_action( 'rest_api_init', [ $this, 'register_rest_routes' ] );
        \add_action( 'sofir/payment/status_changed', [ $this, 'calculate_commission' ], 10, 2 );
        \add_filter( 'wp_insert_post_data', [ $this, 'assign_vendor_to_product' ], 10, 2 );
        \add_filter( 'the_content', [ $this, 'render_vendor_single_template' ], 20 );
        \add_action( 'add_meta_boxes', [ $this, 'add_product_meta_boxes' ] );
        \add_action( 'save_post_vendor_product', [ $this, 'save_product_meta' ] );
        \add_shortcode( 'sofir_vendor_dashboard', [ $this, 'render_vendor_dashboard' ] );
        \add_shortcode( 'sofir_vendor_products', [ $this, 'render_vendor_products' ] );
        \add_shortcode( 'sofir_vendors_list', [ $this, 'render_vendors_list' ] );
        \add_shortcode( 'sofir_become_vendor', [ $this, 'render_become_vendor_form' ] );
    }

    public function enqueue_frontend_assets(): void {
        if ( \is_singular( [ 'vendor_store', 'vendor_product' ] ) || \is_post_type_archive( [ 'vendor_store', 'vendor_product' ] ) ) {
            \wp_enqueue_style(
                'sofir-multivendor',
                \plugins_url( 'assets/css/multivendor.css', dirname( __DIR__ ) ),
                [],
                '1.0.0'
            );
        }
    }

    public function flush_rewrite_rules_on_first_activation(): void {
        if ( \get_option( 'sofir_multivendor_rewrite_flushed' ) ) {
            return;
        }

        \flush_rewrite_rules();
        \update_option( 'sofir_multivendor_rewrite_flushed', '1' );
    }

    public function register_vendor_role(): void {
        if ( \get_role( 'sofir_vendor' ) ) {
            return;
        }

        \add_role(
            'sofir_vendor',
            \__( 'Vendor', 'sofir' ),
            [
                'read' => true,
                'edit_posts' => true,
                'edit_published_posts' => true,
                'publish_posts' => true,
                'delete_posts' => true,
                'delete_published_posts' => true,
                'upload_files' => true,
            ]
        );
    }

    public function register_vendor_cpt(): void {
        \register_post_type(
            'vendor_store',
            [
                'label' => \__( 'Vendor Stores', 'sofir' ),
                'public' => true,
                'publicly_queryable' => true,
                'show_ui' => true,
                'show_in_menu' => 'sofir-multivendor',
                'show_in_rest' => true,
                'supports' => [ 'title', 'editor', 'thumbnail', 'custom-fields' ],
                'has_archive' => true,
                'rewrite' => [ 'slug' => 'vendors', 'with_front' => false ],
                'menu_icon' => 'dashicons-store',
                'capability_type' => 'post',
                'map_meta_cap' => true,
            ]
        );

        \register_post_type(
            'vendor_product',
            [
                'label' => \__( 'Vendor Products', 'sofir' ),
                'public' => true,
                'publicly_queryable' => true,
                'show_ui' => true,
                'show_in_menu' => 'sofir-multivendor',
                'show_in_rest' => true,
                'supports' => [ 'title', 'editor', 'thumbnail' ],
                'has_archive' => true,
                'rewrite' => [ 'slug' => 'products', 'with_front' => false ],
                'menu_icon' => 'dashicons-products',
                'capability_type' => 'post',
                'map_meta_cap' => true,
            ]
        );
    }

    public function add_product_meta_boxes(): void {
        \add_meta_box(
            'vendor_product_details',
            \__( 'Product Details', 'sofir' ),
            [ $this, 'render_product_meta_box' ],
            'vendor_product',
            'normal',
            'high'
        );
    }

    public function render_product_meta_box( \WP_Post $post ): void {
        \wp_nonce_field( 'vendor_product_meta', 'vendor_product_meta_nonce' );

        $price = \get_post_meta( $post->ID, 'product_price', true );
        $sku = \get_post_meta( $post->ID, 'product_sku', true );
        $stock = \get_post_meta( $post->ID, 'product_stock', true );
        $vendor_id = \get_post_meta( $post->ID, 'vendor_id', true );

        ?>
        <table class="form-table">
            <tr>
                <th><label for="product_price"><?php \esc_html_e( 'Price', 'sofir' ); ?></label></th>
                <td>
                    <input type="text" id="product_price" name="product_price" value="<?php echo \esc_attr( $price ); ?>" class="regular-text" placeholder="Rp 100.000" />
                    <p class="description"><?php \esc_html_e( 'Product price (e.g., Rp 100.000 or $50)', 'sofir' ); ?></p>
                </td>
            </tr>
            <tr>
                <th><label for="product_sku"><?php \esc_html_e( 'SKU', 'sofir' ); ?></label></th>
                <td>
                    <input type="text" id="product_sku" name="product_sku" value="<?php echo \esc_attr( $sku ); ?>" class="regular-text" placeholder="PROD-001" />
                    <p class="description"><?php \esc_html_e( 'Product SKU (Stock Keeping Unit)', 'sofir' ); ?></p>
                </td>
            </tr>
            <tr>
                <th><label for="product_stock"><?php \esc_html_e( 'Stock', 'sofir' ); ?></label></th>
                <td>
                    <input type="number" id="product_stock" name="product_stock" value="<?php echo \esc_attr( $stock ); ?>" class="regular-text" min="0" placeholder="10" />
                    <p class="description"><?php \esc_html_e( 'Available stock quantity', 'sofir' ); ?></p>
                </td>
            </tr>
            <?php if ( $vendor_id ) : ?>
                <?php $vendor = \get_post( $vendor_id ); ?>
                <?php if ( $vendor ) : ?>
                    <tr>
                        <th><?php \esc_html_e( 'Vendor Store', 'sofir' ); ?></th>
                        <td>
                            <a href="<?php echo \esc_url( \get_edit_post_link( $vendor_id ) ); ?>">
                                <?php echo \esc_html( $vendor->post_title ); ?>
                            </a>
                        </td>
                    </tr>
                <?php endif; ?>
            <?php endif; ?>
        </table>
        <?php
    }

    public function save_product_meta( int $post_id ): void {
        if ( ! isset( $_POST['vendor_product_meta_nonce'] ) || ! \wp_verify_nonce( $_POST['vendor_product_meta_nonce'], 'vendor_product_meta' ) ) {
            return;
        }

        if ( \defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        if ( ! \current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

        if ( isset( $_POST['product_price'] ) ) {
            \update_post_meta( $post_id, 'product_price', \sanitize_text_field( $_POST['product_price'] ) );
        }

        if ( isset( $_POST['product_sku'] ) ) {
            \update_post_meta( $post_id, 'product_sku', \sanitize_text_field( $_POST['product_sku'] ) );
        }

        if ( isset( $_POST['product_stock'] ) ) {
            \update_post_meta( $post_id, 'product_stock', (int) $_POST['product_stock'] );
        }
    }

    public function add_vendor_menu(): void {
        \add_menu_page(
            \__( 'Multi-Vendor', 'sofir' ),
            \__( 'Multi-Vendor', 'sofir' ),
            'manage_options',
            'sofir-multivendor',
            [ $this, 'render_admin_page' ],
            'dashicons-store',
            30
        );

        \add_submenu_page(
            'sofir-multivendor',
            \__( 'Vendors', 'sofir' ),
            \__( 'Vendors', 'sofir' ),
            'manage_options',
            'edit.php?post_type=vendor_store'
        );

        \add_submenu_page(
            'sofir-multivendor',
            \__( 'Products', 'sofir' ),
            \__( 'Products', 'sofir' ),
            'manage_options',
            'edit.php?post_type=vendor_product'
        );

        \add_submenu_page(
            'sofir-multivendor',
            \__( 'Commission Settings', 'sofir' ),
            \__( 'Settings', 'sofir' ),
            'manage_options',
            'sofir-multivendor-settings',
            [ $this, 'render_settings_page' ]
        );
    }

    public function render_admin_page(): void {
        ?>
        <div class="wrap">
            <h1><?php \esc_html_e( 'Multi-Vendor Overview', 'sofir' ); ?></h1>
            
            <div class="sofir-dashboard-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-top: 20px;">
                <?php
                $vendors_count = \wp_count_posts( 'vendor_store' )->publish;
                $products_count = \wp_count_posts( 'vendor_product' )->publish;
                $pending_vendors = \count( \get_users( [ 'role' => 'sofir_vendor', 'meta_key' => 'vendor_approved', 'meta_value' => '0' ] ) );
                ?>
                
                <div class="sofir-stat-card" style="background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                    <h3><?php \esc_html_e( 'Total Vendors', 'sofir' ); ?></h3>
                    <p style="font-size: 32px; font-weight: bold; color: #0073aa; margin: 10px 0;"><?php echo \esc_html( $vendors_count ); ?></p>
                </div>

                <div class="sofir-stat-card" style="background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                    <h3><?php \esc_html_e( 'Total Products', 'sofir' ); ?></h3>
                    <p style="font-size: 32px; font-weight: bold; color: #00a32a; margin: 10px 0;"><?php echo \esc_html( $products_count ); ?></p>
                </div>

                <div class="sofir-stat-card" style="background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                    <h3><?php \esc_html_e( 'Pending Approval', 'sofir' ); ?></h3>
                    <p style="font-size: 32px; font-weight: bold; color: #f0b849; margin: 10px 0;"><?php echo \esc_html( $pending_vendors ); ?></p>
                </div>
            </div>

            <h2 style="margin-top: 40px;"><?php \esc_html_e( 'Recent Vendors', 'sofir' ); ?></h2>
            <?php
            $vendors = \get_posts( [
                'post_type' => 'vendor_store',
                'posts_per_page' => 10,
                'orderby' => 'date',
                'order' => 'DESC',
            ] );

            if ( $vendors ) {
                echo '<table class="wp-list-table widefat fixed striped">';
                echo '<thead><tr>';
                echo '<th>' . \esc_html__( 'Store Name', 'sofir' ) . '</th>';
                echo '<th>' . \esc_html__( 'Owner', 'sofir' ) . '</th>';
                echo '<th>' . \esc_html__( 'Products', 'sofir' ) . '</th>';
                echo '<th>' . \esc_html__( 'Status', 'sofir' ) . '</th>';
                echo '<th>' . \esc_html__( 'Date', 'sofir' ) . '</th>';
                echo '</tr></thead><tbody>';

                foreach ( $vendors as $vendor ) {
                    $owner_id = \get_post_meta( $vendor->ID, 'vendor_owner', true );
                    $owner = \get_userdata( $owner_id );
                    $products = \get_posts( [
                        'post_type' => 'vendor_product',
                        'meta_key' => 'vendor_id',
                        'meta_value' => $vendor->ID,
                        'posts_per_page' => -1,
                    ] );

                    echo '<tr>';
                    echo '<td><strong><a href="' . \esc_url( \get_edit_post_link( $vendor->ID ) ) . '">' . \esc_html( $vendor->post_title ) . '</a></strong></td>';
                    echo '<td>' . ( $owner ? \esc_html( $owner->display_name ) : '-' ) . '</td>';
                    echo '<td>' . \count( $products ) . '</td>';
                    echo '<td>' . \esc_html( $vendor->post_status ) . '</td>';
                    echo '<td>' . \esc_html( \get_the_date( '', $vendor->ID ) ) . '</td>';
                    echo '</tr>';
                }

                echo '</tbody></table>';
            } else {
                echo '<p>' . \esc_html__( 'No vendors found.', 'sofir' ) . '</p>';
            }
            ?>
        </div>
        <?php
    }

    public function render_settings_page(): void {
        if ( ! \current_user_can( 'manage_options' ) ) {
            return;
        }

        if ( isset( $_POST['sofir_multivendor_settings'] ) && \check_admin_referer( 'sofir_multivendor_settings' ) ) {
            \update_option( 'sofir_vendor_commission', \sanitize_text_field( $_POST['vendor_commission'] ?? '10' ) );
            \update_option( 'sofir_vendor_approval', \sanitize_text_field( $_POST['vendor_approval'] ?? 'auto' ) );
            \update_option( 'sofir_vendor_allow_products', isset( $_POST['vendor_allow_products'] ) ? '1' : '0' );
            \update_option( 'sofir_vendor_min_withdrawal', \sanitize_text_field( $_POST['vendor_min_withdrawal'] ?? '100000' ) );
            
            echo '<div class="notice notice-success"><p>' . \esc_html__( 'Settings saved successfully!', 'sofir' ) . '</p></div>';
        }

        ?>
        <div class="wrap">
            <h1><?php \esc_html_e( 'Multi-Vendor Settings', 'sofir' ); ?></h1>

            <form method="post">
                <?php \wp_nonce_field( 'sofir_multivendor_settings' ); ?>
                <input type="hidden" name="sofir_multivendor_settings" value="1" />

                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="vendor_commission"><?php \esc_html_e( 'Commission Rate (%)', 'sofir' ); ?></label>
                        </th>
                        <td>
                            <input type="number" id="vendor_commission" name="vendor_commission" value="<?php echo \esc_attr( \get_option( 'sofir_vendor_commission', '10' ) ); ?>" min="0" max="100" step="0.1" />
                            <p class="description"><?php \esc_html_e( 'Platform commission percentage from each sale.', 'sofir' ); ?></p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <label for="vendor_approval"><?php \esc_html_e( 'Vendor Approval', 'sofir' ); ?></label>
                        </th>
                        <td>
                            <select id="vendor_approval" name="vendor_approval">
                                <option value="auto" <?php \selected( \get_option( 'sofir_vendor_approval', 'auto' ), 'auto' ); ?>><?php \esc_html_e( 'Automatic', 'sofir' ); ?></option>
                                <option value="manual" <?php \selected( \get_option( 'sofir_vendor_approval' ), 'manual' ); ?>><?php \esc_html_e( 'Manual Review', 'sofir' ); ?></option>
                            </select>
                            <p class="description"><?php \esc_html_e( 'How new vendor applications should be handled.', 'sofir' ); ?></p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <label for="vendor_allow_products"><?php \esc_html_e( 'Allow Product Creation', 'sofir' ); ?></label>
                        </th>
                        <td>
                            <input type="checkbox" id="vendor_allow_products" name="vendor_allow_products" value="1" <?php \checked( \get_option( 'sofir_vendor_allow_products', '1' ), '1' ); ?> />
                            <label for="vendor_allow_products"><?php \esc_html_e( 'Allow vendors to create and manage products', 'sofir' ); ?></label>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <label for="vendor_min_withdrawal"><?php \esc_html_e( 'Minimum Withdrawal', 'sofir' ); ?></label>
                        </th>
                        <td>
                            <input type="number" id="vendor_min_withdrawal" name="vendor_min_withdrawal" value="<?php echo \esc_attr( \get_option( 'sofir_vendor_min_withdrawal', '100000' ) ); ?>" min="0" step="1000" />
                            <p class="description"><?php \esc_html_e( 'Minimum amount vendors can withdraw.', 'sofir' ); ?></p>
                        </td>
                    </tr>
                </table>

                <?php \submit_button(); ?>
            </form>
        </div>
        <?php
    }

    public function register_rest_routes(): void {
        \register_rest_route(
            'sofir/v1',
            '/vendors',
            [
                'methods' => 'GET',
                'callback' => [ $this, 'get_vendors' ],
                'permission_callback' => '__return_true',
            ]
        );

        \register_rest_route(
            'sofir/v1',
            '/vendors/(?P<id>\d+)',
            [
                'methods' => 'GET',
                'callback' => [ $this, 'get_vendor' ],
                'permission_callback' => '__return_true',
            ]
        );

        \register_rest_route(
            'sofir/v1',
            '/vendors/apply',
            [
                'methods' => 'POST',
                'callback' => [ $this, 'apply_vendor' ],
                'permission_callback' => function (): bool {
                    return \is_user_logged_in();
                },
            ]
        );

        \register_rest_route(
            'sofir/v1',
            '/vendors/(?P<id>\d+)/products',
            [
                'methods' => 'GET',
                'callback' => [ $this, 'get_vendor_products' ],
                'permission_callback' => '__return_true',
            ]
        );

        \register_rest_route(
            'sofir/v1',
            '/vendors/earnings',
            [
                'methods' => 'GET',
                'callback' => [ $this, 'get_vendor_earnings' ],
                'permission_callback' => function (): bool {
                    return \is_user_logged_in();
                },
            ]
        );
    }

    public function get_vendors( \WP_REST_Request $request ): \WP_REST_Response {
        $per_page = $request->get_param( 'per_page' ) ?? 10;
        $page = $request->get_param( 'page' ) ?? 1;

        $vendors = \get_posts( [
            'post_type' => 'vendor_store',
            'post_status' => 'publish',
            'posts_per_page' => $per_page,
            'paged' => $page,
        ] );

        $data = array_map( function ( $vendor ) {
            return $this->format_vendor_data( $vendor );
        }, $vendors );

        return new \WP_REST_Response( $data, 200 );
    }

    public function get_vendor( \WP_REST_Request $request ): \WP_REST_Response {
        $vendor_id = $request->get_param( 'id' );
        $vendor = \get_post( $vendor_id );

        if ( ! $vendor || 'vendor_store' !== $vendor->post_type ) {
            return new \WP_REST_Response(
                [ 'message' => \__( 'Vendor not found.', 'sofir' ) ],
                404
            );
        }

        return new \WP_REST_Response( $this->format_vendor_data( $vendor ), 200 );
    }

    public function apply_vendor( \WP_REST_Request $request ): \WP_REST_Response {
        $user_id = \get_current_user_id();
        $existing = \get_posts( [
            'post_type' => 'vendor_store',
            'meta_key' => 'vendor_owner',
            'meta_value' => $user_id,
            'posts_per_page' => 1,
        ] );

        if ( $existing ) {
            return new \WP_REST_Response(
                [ 'message' => \__( 'You already have a vendor store.', 'sofir' ) ],
                400
            );
        }

        $store_name = $request->get_param( 'store_name' );
        $store_description = $request->get_param( 'store_description' );

        if ( ! $store_name ) {
            return new \WP_REST_Response(
                [ 'message' => \__( 'Store name is required.', 'sofir' ) ],
                400
            );
        }

        $approval = \get_option( 'sofir_vendor_approval', 'auto' );
        $status = 'auto' === $approval ? 'publish' : 'pending';

        $vendor_id = \wp_insert_post( [
            'post_title' => \sanitize_text_field( $store_name ),
            'post_content' => \sanitize_textarea_field( $store_description ),
            'post_type' => 'vendor_store',
            'post_status' => $status,
        ] );

        if ( \is_wp_error( $vendor_id ) ) {
            return new \WP_REST_Response(
                [ 'message' => \__( 'Failed to create vendor store.', 'sofir' ) ],
                500
            );
        }

        \update_post_meta( $vendor_id, 'vendor_owner', $user_id );
        \update_post_meta( $vendor_id, 'vendor_commission', \get_option( 'sofir_vendor_commission', '10' ) );
        \update_post_meta( $vendor_id, 'vendor_earnings', '0' );

        $user = \wp_get_current_user();
        $user->add_role( 'sofir_vendor' );

        \do_action( 'sofir/vendor/applied', $vendor_id, $user_id );

        return new \WP_REST_Response(
            [
                'message' => \__( 'Vendor application submitted successfully!', 'sofir' ),
                'vendor_id' => $vendor_id,
                'status' => $status,
            ],
            201
        );
    }

    public function get_vendor_products( \WP_REST_Request $request ): \WP_REST_Response {
        $vendor_id = $request->get_param( 'id' );

        $products = \get_posts( [
            'post_type' => 'vendor_product',
            'meta_key' => 'vendor_id',
            'meta_value' => $vendor_id,
            'posts_per_page' => -1,
        ] );

        $data = array_map( function ( $product ) {
            return [
                'id' => $product->ID,
                'title' => $product->post_title,
                'price' => \get_post_meta( $product->ID, 'product_price', true ),
                'image' => \get_the_post_thumbnail_url( $product->ID, 'medium' ),
                'url' => \get_permalink( $product->ID ),
            ];
        }, $products );

        return new \WP_REST_Response( $data, 200 );
    }

    public function get_vendor_earnings( \WP_REST_Request $request ): \WP_REST_Response {
        $user_id = \get_current_user_id();

        $vendor = \get_posts( [
            'post_type' => 'vendor_store',
            'meta_key' => 'vendor_owner',
            'meta_value' => $user_id,
            'posts_per_page' => 1,
        ] );

        if ( ! $vendor ) {
            return new \WP_REST_Response(
                [ 'message' => \__( 'Vendor store not found.', 'sofir' ) ],
                404
            );
        }

        $vendor_id = $vendor[0]->ID;
        $earnings = \get_post_meta( $vendor_id, 'vendor_earnings', true ) ?? '0';
        $pending = \get_post_meta( $vendor_id, 'vendor_pending_earnings', true ) ?? '0';

        return new \WP_REST_Response(
            [
                'total_earnings' => (float) $earnings,
                'pending_earnings' => (float) $pending,
                'available_withdrawal' => (float) $earnings,
            ],
            200
        );
    }

    private function format_vendor_data( \WP_Post $vendor ): array {
        $owner_id = \get_post_meta( $vendor->ID, 'vendor_owner', true );
        $owner = \get_userdata( $owner_id );

        return [
            'id' => $vendor->ID,
            'name' => $vendor->post_title,
            'description' => $vendor->post_content,
            'logo' => \get_the_post_thumbnail_url( $vendor->ID, 'medium' ),
            'owner' => $owner ? $owner->display_name : '',
            'commission' => \get_post_meta( $vendor->ID, 'vendor_commission', true ),
            'earnings' => \get_post_meta( $vendor->ID, 'vendor_earnings', true ),
            'url' => \get_permalink( $vendor->ID ),
            'created' => $vendor->post_date,
        ];
    }

    public function assign_vendor_to_product( array $data, array $postarr ): array {
        if ( 'vendor_product' === $data['post_type'] && isset( $postarr['ID'] ) ) {
            $user_id = \get_current_user_id();
            
            if ( \in_array( 'sofir_vendor', \wp_get_current_user()->roles, true ) ) {
                $vendor = \get_posts( [
                    'post_type' => 'vendor_store',
                    'meta_key' => 'vendor_owner',
                    'meta_value' => $user_id,
                    'posts_per_page' => 1,
                ] );

                if ( $vendor ) {
                    \update_post_meta( $postarr['ID'], 'vendor_id', $vendor[0]->ID );
                }
            }
        }

        return $data;
    }

    public function calculate_commission( string $transaction_id, string $status ): void {
        if ( 'completed' !== $status ) {
            return;
        }

        $transactions = \get_option( 'sofir_payment_transactions', [] );
        
        if ( ! isset( $transactions[ $transaction_id ] ) ) {
            return;
        }

        $transaction = $transactions[ $transaction_id ];
        $product_id = $transaction['product_id'] ?? 0;

        if ( ! $product_id ) {
            return;
        }

        $vendor_id = \get_post_meta( $product_id, 'vendor_id', true );

        if ( ! $vendor_id ) {
            return;
        }

        $amount = (float) $transaction['amount'];
        $commission_rate = (float) \get_post_meta( $vendor_id, 'vendor_commission', true );
        $commission = $amount * ( $commission_rate / 100 );
        $vendor_earning = $amount - $commission;

        $current_earnings = (float) \get_post_meta( $vendor_id, 'vendor_earnings', true );
        $new_earnings = $current_earnings + $vendor_earning;

        \update_post_meta( $vendor_id, 'vendor_earnings', $new_earnings );

        \do_action( 'sofir/vendor/commission_calculated', $vendor_id, $transaction_id, $vendor_earning, $commission );
    }

    public function render_vendor_dashboard(): string {
        if ( ! \is_user_logged_in() ) {
            return '<p>' . \esc_html__( 'Please login to view your vendor dashboard.', 'sofir' ) . '</p>';
        }

        $user_id = \get_current_user_id();
        $vendor = \get_posts( [
            'post_type' => 'vendor_store',
            'meta_key' => 'vendor_owner',
            'meta_value' => $user_id,
            'posts_per_page' => 1,
        ] );

        if ( ! $vendor ) {
            return '<p>' . \esc_html__( 'You are not a vendor yet.', 'sofir' ) . ' <a href="#" class="sofir-become-vendor">' . \esc_html__( 'Apply Now', 'sofir' ) . '</a></p>';
        }

        $vendor = $vendor[0];
        $earnings = \get_post_meta( $vendor->ID, 'vendor_earnings', true ) ?? '0';
        
        $products = \get_posts( [
            'post_type' => 'vendor_product',
            'meta_key' => 'vendor_id',
            'meta_value' => $vendor->ID,
            'posts_per_page' => -1,
        ] );

        ob_start();
        ?>
        <div class="sofir-vendor-dashboard">
            <h2><?php echo \esc_html( $vendor->post_title ); ?></h2>
            
            <div class="sofir-vendor-stats">
                <div class="sofir-stat-box">
                    <h3><?php \esc_html_e( 'Total Earnings', 'sofir' ); ?></h3>
                    <p class="sofir-stat-value"><?php echo \esc_html( \number_format_i18n( (float) $earnings ) ); ?></p>
                </div>
                
                <div class="sofir-stat-box">
                    <h3><?php \esc_html_e( 'Total Products', 'sofir' ); ?></h3>
                    <p class="sofir-stat-value"><?php echo \esc_html( \count( $products ) ); ?></p>
                </div>
            </div>

            <h3><?php \esc_html_e( 'Your Products', 'sofir' ); ?></h3>
            <div class="sofir-vendor-products">
                <?php
                if ( $products ) {
                    foreach ( $products as $product ) {
                        ?>
                        <div class="sofir-product-item">
                            <?php if ( \has_post_thumbnail( $product->ID ) ) : ?>
                                <?php echo \get_the_post_thumbnail( $product->ID, 'thumbnail' ); ?>
                            <?php endif; ?>
                            <h4><?php echo \esc_html( $product->post_title ); ?></h4>
                            <p><?php echo \esc_html( \get_post_meta( $product->ID, 'product_price', true ) ); ?></p>
                            <a href="<?php echo \esc_url( \get_edit_post_link( $product->ID ) ); ?>"><?php \esc_html_e( 'Edit', 'sofir' ); ?></a>
                        </div>
                        <?php
                    }
                } else {
                    echo '<p>' . \esc_html__( 'No products yet.', 'sofir' ) . '</p>';
                }
                ?>
            </div>
        </div>
        <?php
        return (string) ob_get_clean();
    }

    public function render_vendor_products( array $atts ): string {
        $atts = \shortcode_atts( [
            'vendor_id' => 0,
            'limit' => 12,
        ], $atts );

        $vendor_id = (int) $atts['vendor_id'];

        if ( ! $vendor_id ) {
            return '';
        }

        $products = \get_posts( [
            'post_type' => 'vendor_product',
            'meta_key' => 'vendor_id',
            'meta_value' => $vendor_id,
            'posts_per_page' => (int) $atts['limit'],
        ] );

        if ( ! $products ) {
            return '<p>' . \esc_html__( 'No products found.', 'sofir' ) . '</p>';
        }

        ob_start();
        echo '<div class="sofir-vendor-products-grid">';
        
        foreach ( $products as $product ) {
            ?>
            <div class="sofir-product-card">
                <?php if ( \has_post_thumbnail( $product->ID ) ) : ?>
                    <a href="<?php echo \esc_url( \get_permalink( $product->ID ) ); ?>">
                        <?php echo \get_the_post_thumbnail( $product->ID, 'medium' ); ?>
                    </a>
                <?php endif; ?>
                <h3><a href="<?php echo \esc_url( \get_permalink( $product->ID ) ); ?>"><?php echo \esc_html( $product->post_title ); ?></a></h3>
                <p class="sofir-product-price"><?php echo \esc_html( \get_post_meta( $product->ID, 'product_price', true ) ); ?></p>
            </div>
            <?php
        }
        
        echo '</div>';
        
        return (string) ob_get_clean();
    }

    public function render_vendors_list( array $atts ): string {
        $atts = \shortcode_atts( [
            'limit' => 12,
        ], $atts );

        $vendors = \get_posts( [
            'post_type' => 'vendor_store',
            'post_status' => 'publish',
            'posts_per_page' => (int) $atts['limit'],
        ] );

        if ( ! $vendors ) {
            return '<p>' . \esc_html__( 'No vendors found.', 'sofir' ) . '</p>';
        }

        ob_start();
        echo '<div class="sofir-vendors-grid">';
        
        foreach ( $vendors as $vendor ) {
            $products_count = \count( \get_posts( [
                'post_type' => 'vendor_product',
                'meta_key' => 'vendor_id',
                'meta_value' => $vendor->ID,
                'posts_per_page' => -1,
            ] ) );

            ?>
            <div class="sofir-vendor-card">
                <?php if ( \has_post_thumbnail( $vendor->ID ) ) : ?>
                    <a href="<?php echo \esc_url( \get_permalink( $vendor->ID ) ); ?>">
                        <?php echo \get_the_post_thumbnail( $vendor->ID, 'medium' ); ?>
                    </a>
                <?php endif; ?>
                <h3><a href="<?php echo \esc_url( \get_permalink( $vendor->ID ) ); ?>"><?php echo \esc_html( $vendor->post_title ); ?></a></h3>
                <p><?php echo \esc_html( \sprintf( \__( '%d Products', 'sofir' ), $products_count ) ); ?></p>
            </div>
            <?php
        }
        
        echo '</div>';
        
        return (string) ob_get_clean();
    }

    public function render_vendor_single_template( string $content ): string {
        global $post;

        if ( ! $post || ! \is_singular() ) {
            return $content;
        }

        if ( ! \in_array( $post->post_type, [ 'vendor_store', 'vendor_product' ], true ) ) {
            return $content;
        }

        if ( 'vendor_store' === $post->post_type ) {
            $vendor_id = $post->ID;
            $owner_id = \get_post_meta( $vendor_id, 'vendor_owner', true );
            $owner = \get_userdata( $owner_id );
            $commission = \get_post_meta( $vendor_id, 'vendor_commission', true );
            
            $products = \get_posts( [
                'post_type' => 'vendor_product',
                'meta_key' => 'vendor_id',
                'meta_value' => $vendor_id,
                'posts_per_page' => -1,
            ] );

            ob_start();
            ?>
            <div class="sofir-vendor-single">
                <?php if ( \has_post_thumbnail( $vendor_id ) ) : ?>
                    <div class="vendor-logo">
                        <?php echo \get_the_post_thumbnail( $vendor_id, 'large' ); ?>
                    </div>
                <?php endif; ?>

                <div class="vendor-content">
                    <?php echo $content; ?>
                </div>

                <div class="vendor-meta">
                    <?php if ( $owner ) : ?>
                        <p><strong><?php \esc_html_e( 'Store Owner:', 'sofir' ); ?></strong> <?php echo \esc_html( $owner->display_name ); ?></p>
                    <?php endif; ?>
                    <p><strong><?php \esc_html_e( 'Total Products:', 'sofir' ); ?></strong> <?php echo \count( $products ); ?></p>
                </div>

                <h2><?php \esc_html_e( 'Products from this Vendor', 'sofir' ); ?></h2>
                <div class="sofir-vendor-products-grid">
                    <?php
                    if ( $products ) {
                        foreach ( $products as $product ) {
                            ?>
                            <div class="sofir-product-card">
                                <?php if ( \has_post_thumbnail( $product->ID ) ) : ?>
                                    <a href="<?php echo \esc_url( \get_permalink( $product->ID ) ); ?>">
                                        <?php echo \get_the_post_thumbnail( $product->ID, 'medium' ); ?>
                                    </a>
                                <?php endif; ?>
                                <h3>
                                    <a href="<?php echo \esc_url( \get_permalink( $product->ID ) ); ?>">
                                        <?php echo \esc_html( $product->post_title ); ?>
                                    </a>
                                </h3>
                                <?php
                                $price = \get_post_meta( $product->ID, 'product_price', true );
                                if ( $price ) :
                                ?>
                                    <p class="sofir-product-price">
                                        <?php echo \esc_html( $price ); ?>
                                    </p>
                                <?php endif; ?>
                                <a href="<?php echo \esc_url( \get_permalink( $product->ID ) ); ?>" class="button">
                                    <?php \esc_html_e( 'View Product', 'sofir' ); ?>
                                </a>
                            </div>
                            <?php
                        }
                    } else {
                        echo '<p>' . \esc_html__( 'No products available yet.', 'sofir' ) . '</p>';
                    }
                    ?>
                </div>
            </div>
            <?php
            return (string) ob_get_clean();
        }

        if ( 'vendor_product' === $post->post_type ) {
            $vendor_id = \get_post_meta( $post->ID, 'vendor_id', true );
            $price = \get_post_meta( $post->ID, 'product_price', true );
            $sku = \get_post_meta( $post->ID, 'product_sku', true );
            $stock = \get_post_meta( $post->ID, 'product_stock', true );

            ob_start();
            ?>
            <div class="sofir-product-single">
                <?php if ( \has_post_thumbnail( $post->ID ) ) : ?>
                    <div class="product-image">
                        <?php echo \get_the_post_thumbnail( $post->ID, 'large' ); ?>
                    </div>
                <?php endif; ?>

                <div class="product-content">
                    <?php echo $content; ?>
                </div>

                <div class="product-meta">
                    <?php if ( $price ) : ?>
                        <p style="font-size: 28px; color: #0073aa; font-weight: bold; margin: 10px 0;">
                            <strong><?php \esc_html_e( 'Price:', 'sofir' ); ?></strong> <?php echo \esc_html( $price ); ?>
                        </p>
                    <?php endif; ?>

                    <?php if ( $sku ) : ?>
                        <p><strong><?php \esc_html_e( 'SKU:', 'sofir' ); ?></strong> <?php echo \esc_html( $sku ); ?></p>
                    <?php endif; ?>

                    <?php if ( $stock ) : ?>
                        <p><strong><?php \esc_html_e( 'Stock:', 'sofir' ); ?></strong> <?php echo \esc_html( $stock ); ?></p>
                    <?php endif; ?>

                    <?php if ( $vendor_id ) : ?>
                        <?php $vendor = \get_post( $vendor_id ); ?>
                        <?php if ( $vendor ) : ?>
                            <p>
                                <strong><?php \esc_html_e( 'Sold by:', 'sofir' ); ?></strong> 
                                <a href="<?php echo \esc_url( \get_permalink( $vendor_id ) ); ?>">
                                    <?php echo \esc_html( $vendor->post_title ); ?>
                                </a>
                            </p>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
            <?php
            return (string) ob_get_clean();
        }

        return $content;
    }

    public function render_become_vendor_form(): string {
        if ( ! \is_user_logged_in() ) {
            return '<p>' . \esc_html__( 'Please login to become a vendor.', 'sofir' ) . '</p>';
        }

        \wp_enqueue_script( 'jquery' );

        ob_start();
        ?>
        <div class="sofir-become-vendor-form">
            <h3><?php \esc_html_e( 'Apply to Become a Vendor', 'sofir' ); ?></h3>
            
            <form id="sofir-vendor-application">
                <div class="form-field">
                    <label for="store_name"><?php \esc_html_e( 'Store Name', 'sofir' ); ?> <span class="required">*</span></label>
                    <input type="text" id="store_name" name="store_name" required />
                </div>

                <div class="form-field">
                    <label for="store_description"><?php \esc_html_e( 'Store Description', 'sofir' ); ?></label>
                    <textarea id="store_description" name="store_description" rows="5"></textarea>
                </div>

                <button type="submit" class="button button-primary"><?php \esc_html_e( 'Submit Application', 'sofir' ); ?></button>
            </form>

            <div id="vendor-application-message"></div>
        </div>

        <script>
        jQuery(document).ready(function($) {
            $('#sofir-vendor-application').on('submit', function(e) {
                e.preventDefault();
                
                var $form = $(this);
                var $message = $('#vendor-application-message');
                var data = {
                    store_name: $('#store_name').val(),
                    store_description: $('#store_description').val()
                };

                $.ajax({
                    url: '<?php echo \esc_url( \rest_url( 'sofir/v1/vendors/apply' ) ); ?>',
                    method: 'POST',
                    data: data,
                    beforeSend: function(xhr) {
                        xhr.setRequestHeader('X-WP-Nonce', '<?php echo \wp_create_nonce( 'wp_rest' ); ?>');
                        $form.find('button').prop('disabled', true);
                    },
                    success: function(response) {
                        $message.html('<div class="notice notice-success"><p>' + response.message + '</p></div>');
                        $form[0].reset();
                    },
                    error: function(xhr) {
                        $message.html('<div class="notice notice-error"><p>' + (xhr.responseJSON?.message || '<?php \esc_html_e( 'Application failed', 'sofir' ); ?>') + '</p></div>');
                    },
                    complete: function() {
                        $form.find('button').prop('disabled', false);
                    }
                });
            });
        });
        </script>
        <?php
        return (string) ob_get_clean();
    }
}
