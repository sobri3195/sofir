<?php
namespace Sofir\Forms;

class Manager {
    private static ?Manager $instance = null;

    public static function instance(): Manager {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function boot(): void {
        \add_action( 'init', [ $this, 'register_form_cpt' ] );
        \add_action( 'admin_menu', [ $this, 'add_forms_menu' ] );
        \add_action( 'rest_api_init', [ $this, 'register_rest_routes' ] );
        \add_action( 'admin_post_sofir_submit_form', [ $this, 'handle_form_submission' ] );
        \add_action( 'admin_post_nopriv_sofir_submit_form', [ $this, 'handle_form_submission' ] );
        \add_action( 'wp_ajax_sofir_save_partial_submission', [ $this, 'save_partial_submission' ] );
        \add_action( 'wp_ajax_nopriv_sofir_save_partial_submission', [ $this, 'save_partial_submission' ] );
        \add_action( 'wp_ajax_sofir_load_partial_submission', [ $this, 'load_partial_submission' ] );
        \add_action( 'wp_ajax_nopriv_sofir_load_partial_submission', [ $this, 'load_partial_submission' ] );
        \add_action( 'wp_ajax_sofir_process_payment', [ $this, 'process_payment' ] );
        \add_action( 'wp_ajax_nopriv_sofir_process_payment', [ $this, 'process_payment' ] );
        \add_action( 'add_meta_boxes', [ $this, 'add_submission_meta_boxes' ] );
        \add_action( 'add_meta_boxes', [ $this, 'add_form_meta_boxes' ] );
        \add_action( 'admin_init', [ $this, 'redirect_form_edit' ] );
        \add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_frontend_assets' ] );
        \add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_assets' ] );
        \add_shortcode( 'sofir_form', [ $this, 'render_form' ] );
        
        $this->register_form_cron();
        $this->register_payment_webhooks();
    }

    public function register_form_cpt(): void {
        \register_post_type(
            'sofir_form',
            [
                'label' => \__( 'Forms', 'sofir' ),
                'public' => false,
                'show_ui' => true,
                'show_in_menu' => false,
                'supports' => [ 'title' ],
                'capability_type' => 'post',
            ]
        );

        \register_post_type(
            'sofir_submission',
            [
                'label' => \__( 'Form Submissions', 'sofir' ),
                'public' => false,
                'show_ui' => true,
                'show_in_menu' => false,
                'supports' => [ 'title', 'custom-fields' ],
                'capability_type' => 'post',
            ]
        );
    }

    public function add_forms_menu(): void {
        \add_menu_page(
            \__( 'Forms', 'sofir' ),
            \__( 'Forms', 'sofir' ),
            'manage_options',
            'sofir-forms',
            [ $this, 'render_forms_page' ],
            'dashicons-feedback',
            31
        );

        \add_submenu_page(
            'sofir-forms',
            \__( 'All Forms', 'sofir' ),
            \__( 'All Forms', 'sofir' ),
            'manage_options',
            'edit.php?post_type=sofir_form'
        );

        \add_submenu_page(
            'sofir-forms',
            \__( 'Add New', 'sofir' ),
            \__( 'Add New', 'sofir' ),
            'manage_options',
            'sofir-forms-new',
            [ $this, 'render_form_builder' ]
        );

        \add_submenu_page(
            'sofir-forms',
            \__( 'Submissions', 'sofir' ),
            \__( 'Submissions', 'sofir' ),
            'manage_options',
            'edit.php?post_type=sofir_submission'
        );
    }

    public function render_forms_page(): void {
        ?>
        <div class="wrap">
            <h1><?php \esc_html_e( 'Forms Overview', 'sofir' ); ?></h1>
            
            <div class="sofir-dashboard-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-top: 20px;">
                <?php
                $forms_count = \wp_count_posts( 'sofir_form' )->publish;
                $submissions_count = \wp_count_posts( 'sofir_submission' )->publish;
                ?>
                
                <div class="sofir-stat-card" style="background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                    <h3><?php \esc_html_e( 'Total Forms', 'sofir' ); ?></h3>
                    <p style="font-size: 32px; font-weight: bold; color: #0073aa; margin: 10px 0;"><?php echo \esc_html( $forms_count ); ?></p>
                </div>

                <div class="sofir-stat-card" style="background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                    <h3><?php \esc_html_e( 'Total Submissions', 'sofir' ); ?></h3>
                    <p style="font-size: 32px; font-weight: bold; color: #00a32a; margin: 10px 0;"><?php echo \esc_html( $submissions_count ); ?></p>
                </div>
            </div>

            <h2 style="margin-top: 40px;"><?php \esc_html_e( 'Recent Forms', 'sofir' ); ?></h2>
            <?php
            $forms = \get_posts( [
                'post_type' => 'sofir_form',
                'posts_per_page' => 10,
                'orderby' => 'date',
                'order' => 'DESC',
            ] );

            if ( $forms ) {
                echo '<table class="wp-list-table widefat fixed striped">';
                echo '<thead><tr>';
                echo '<th>' . \esc_html__( 'Form Name', 'sofir' ) . '</th>';
                echo '<th>' . \esc_html__( 'Shortcode', 'sofir' ) . '</th>';
                echo '<th>' . \esc_html__( 'Submissions', 'sofir' ) . '</th>';
                echo '<th>' . \esc_html__( 'Date', 'sofir' ) . '</th>';
                echo '</tr></thead><tbody>';

                foreach ( $forms as $form ) {
                    $submissions = \get_posts( [
                        'post_type' => 'sofir_submission',
                        'meta_key' => 'form_id',
                        'meta_value' => $form->ID,
                        'posts_per_page' => -1,
                    ] );

                    echo '<tr>';
                    echo '<td><strong><a href="' . \esc_url( \admin_url( 'admin.php?page=sofir-forms-new&form_id=' . $form->ID ) ) . '">' . \esc_html( $form->post_title ) . '</a></strong></td>';
                    echo '<td><code>[sofir_form id="' . \esc_attr( $form->ID ) . '"]</code></td>';
                    echo '<td>' . \count( $submissions ) . '</td>';
                    echo '<td>' . \esc_html( \get_the_date( '', $form->ID ) ) . '</td>';
                    echo '</tr>';
                }

                echo '</tbody></table>';
            } else {
                echo '<p>' . \esc_html__( 'No forms found.', 'sofir' ) . ' <a href="' . \esc_url( \admin_url( 'admin.php?page=sofir-forms-new' ) ) . '" class="button button-primary">' . \esc_html__( 'Create Your First Form', 'sofir' ) . '</a></p>';
            }
            ?>
        </div>
        <?php
    }

    public function render_form_builder(): void {
        $form_id = isset( $_GET['form_id'] ) ? (int) $_GET['form_id'] : 0;
        $form = $form_id ? \get_post( $form_id ) : null;

        if ( isset( $_POST['sofir_save_form'] ) && \check_admin_referer( 'sofir_save_form' ) ) {
            $title = \sanitize_text_field( $_POST['form_title'] ?? '' );
            $fields = $_POST['form_fields'] ?? [];
            $settings = [
                'success_message' => \sanitize_text_field( $_POST['success_message'] ?? '' ),
                'button_text' => \sanitize_text_field( $_POST['button_text'] ?? '' ),
                'notification_email' => \sanitize_email( $_POST['notification_email'] ?? '' ),
            ];

            $post_data = [
                'post_title' => $title,
                'post_type' => 'sofir_form',
                'post_status' => 'publish',
            ];

            if ( $form_id ) {
                $post_data['ID'] = $form_id;
                \wp_update_post( $post_data );
            } else {
                $form_id = \wp_insert_post( $post_data );
            }

            \update_post_meta( $form_id, 'sofir_form_fields', $fields );
            \update_post_meta( $form_id, 'sofir_form_settings', $settings );

            echo '<div class="notice notice-success"><p>' . \esc_html__( 'Form saved successfully!', 'sofir' ) . '</p></div>';
            $form = \get_post( $form_id );
        }

        $fields = $form ? \get_post_meta( $form->ID, 'sofir_form_fields', true ) : [];
        $settings = $form ? \get_post_meta( $form->ID, 'sofir_form_settings', true ) : [];

        ?>
        <div class="wrap">
            <h1><?php echo $form ? \esc_html__( 'Edit Form', 'sofir' ) : \esc_html__( 'Create New Form', 'sofir' ); ?></h1>

            <form method="post" id="sofir-form-builder">
                <?php \wp_nonce_field( 'sofir_save_form' ); ?>
                <input type="hidden" name="sofir_save_form" value="1" />

                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="form_title"><?php \esc_html_e( 'Form Name', 'sofir' ); ?></label>
                        </th>
                        <td>
                            <input type="text" id="form_title" name="form_title" value="<?php echo \esc_attr( $form->post_title ?? '' ); ?>" class="regular-text" required />
                        </td>
                    </tr>
                </table>

                <h2><?php \esc_html_e( 'Form Fields', 'sofir' ); ?></h2>
                
                <div id="form-fields-container">
                    <?php
                    if ( $fields && \is_array( $fields ) ) {
                        foreach ( $fields as $index => $field ) {
                            $this->render_field_editor( $index, $field );
                        }
                    }
                    ?>
                </div>

                <button type="button" id="add-field" class="button"><?php \esc_html_e( 'Add Field', 'sofir' ); ?></button>

                <h2><?php \esc_html_e( 'Form Settings', 'sofir' ); ?></h2>

                <div class="sofir-form-settings-tabs">
                    <ul class="nav-tab-wrapper">
                        <li><a href="#tab-general" class="nav-tab nav-tab-active"><?php \esc_html_e( 'General', 'sofir' ); ?></a></li>
                        <li><a href="#tab-notifications" class="nav-tab"><?php \esc_html_e( 'Notifications', 'sofir' ); ?></a></li>
                        <li><a href="#tab-confirmations" class="nav-tab"><?php \esc_html_e( 'Confirmations', 'sofir' ); ?></a></li>
                        <li><a href="#tab-actions" class="nav-tab"><?php \esc_html_e( 'Actions', 'sofir' ); ?></a></li>
                        <li><a href="#tab-restrictions" class="nav-tab"><?php \esc_html_e( 'Restrictions', 'sofir' ); ?></a></li>
                        <li><a href="#tab-payment" class="nav-tab"><?php \esc_html_e( 'Payment', 'sofir' ); ?></a></li>
                        <li><a href="#tab-advanced" class="nav-tab"><?php \esc_html_e( 'Advanced', 'sofir' ); ?></a></li>
                    </ul>

                    <div id="tab-general" class="tab-content active">
                        <table class="form-table">
                            <tr>
                                <th><label for="button_text"><?php \esc_html_e( 'Submit Button Text', 'sofir' ); ?></label></th>
                                <td><input type="text" id="button_text" name="button_text" value="<?php echo \esc_attr( $settings['button_text'] ?? 'Submit' ); ?>" class="regular-text" /></td>
                            </tr>
                            <tr>
                                <th><label><?php \esc_html_e( 'Multi-Step Form', 'sofir' ); ?></label></th>
                                <td>
                                    <label>
                                        <input type="checkbox" name="enable_multistep" value="1" <?php \checked( $settings['enable_multistep'] ?? '', '1' ); ?> />
                                        <?php \esc_html_e( 'Enable multi-step form', 'sofir' ); ?>
                                    </label>
                                </td>
                            </tr>
                            <tr>
                                <th><label><?php \esc_html_e( 'Save & Resume', 'sofir' ); ?></label></th>
                                <td>
                                    <label>
                                        <input type="checkbox" name="enable_save_resume" value="1" <?php \checked( $settings['enable_save_resume'] ?? '', '1' ); ?> />
                                        <?php \esc_html_e( 'Allow users to save progress and resume later', 'sofir' ); ?>
                                    </label>
                                </td>
                            </tr>
                            <tr>
                                <th><label><?php \esc_html_e( 'Form Scheduling', 'sofir' ); ?></label></th>
                                <td>
                                    <label>
                                        <input type="checkbox" name="enable_scheduling" value="1" <?php \checked( $settings['enable_scheduling'] ?? '', '1' ); ?> />
                                        <?php \esc_html_e( 'Schedule form availability', 'sofir' ); ?>
                                    </label>
                                    <div class="scheduling-options" style="<?php echo isset( $settings['enable_scheduling'] ) && '1' === $settings['enable_scheduling'] ? '' : 'display:none;'; ?> margin-top:10px;">
                                        <input type="datetime-local" name="schedule_start" value="<?php echo \esc_attr( $settings['schedule_start'] ?? '' ); ?>" />
                                        <span>to</span>
                                        <input type="datetime-local" name="schedule_end" value="<?php echo \esc_attr( $settings['schedule_end'] ?? '' ); ?>" />
                                        <p class="description"><?php \esc_html_e( 'Form will only be available during this period', 'sofir' ); ?></p>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>

                    <div id="tab-notifications" class="tab-content" style="display:none;">
                        <table class="form-table">
                            <tr>
                                <th><label><?php \esc_html_e( 'Admin Notification', 'sofir' ); ?></label></th>
                                <td>
                                    <label>
                                        <input type="checkbox" name="enable_admin_notification" value="1" <?php \checked( $settings['enable_admin_notification'] ?? '1', '1' ); ?> />
                                        <?php \esc_html_e( 'Send notification to admin', 'sofir' ); ?>
                                    </label>
                                </td>
                            </tr>
                            <tr>
                                <th><label for="notification_email"><?php \esc_html_e( 'Notification Email', 'sofir' ); ?></label></th>
                                <td>
                                    <input type="email" id="notification_email" name="notification_email" value="<?php echo \esc_attr( $settings['notification_email'] ?? \get_option( 'admin_email' ) ); ?>" class="regular-text" />
                                    <p class="description"><?php \esc_html_e( 'Separate multiple emails with commas', 'sofir' ); ?></p>
                                </td>
                            </tr>
                            <tr>
                                <th><label for="notification_subject"><?php \esc_html_e( 'Email Subject', 'sofir' ); ?></label></th>
                                <td><input type="text" id="notification_subject" name="notification_subject" value="<?php echo \esc_attr( $settings['notification_subject'] ?? 'New Form Submission' ); ?>" class="regular-text" /></td>
                            </tr>
                            <tr>
                                <th><label><?php \esc_html_e( 'User Notification', 'sofir' ); ?></label></th>
                                <td>
                                    <label>
                                        <input type="checkbox" name="enable_user_notification" value="1" <?php \checked( $settings['enable_user_notification'] ?? '', '1' ); ?> />
                                        <?php \esc_html_e( 'Send confirmation email to user', 'sofir' ); ?>
                                    </label>
                                    <div class="user-notification-options" style="<?php echo isset( $settings['enable_user_notification'] ) && '1' === $settings['enable_user_notification'] ? '' : 'display:none;'; ?> margin-top:10px;">
                                        <input type="text" name="user_notification_subject" value="<?php echo \esc_attr( $settings['user_notification_subject'] ?? 'Thank you for your submission' ); ?>" class="regular-text" placeholder="Subject" />
                                        <textarea name="user_notification_message" rows="5" class="large-text"><?php echo \esc_textarea( $settings['user_notification_message'] ?? 'Thank you for contacting us. We will get back to you soon.' ); ?></textarea>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>

                    <div id="tab-confirmations" class="tab-content" style="display:none;">
                        <table class="form-table">
                            <tr>
                                <th><label><?php \esc_html_e( 'Confirmation Type', 'sofir' ); ?></label></th>
                                <td>
                                    <select name="confirmation_type">
                                        <option value="message" <?php \selected( $settings['confirmation_type'] ?? 'message', 'message' ); ?>><?php \esc_html_e( 'Show Message', 'sofir' ); ?></option>
                                        <option value="redirect" <?php \selected( $settings['confirmation_type'] ?? '', 'redirect' ); ?>><?php \esc_html_e( 'Redirect to URL', 'sofir' ); ?></option>
                                        <option value="page" <?php \selected( $settings['confirmation_type'] ?? '', 'page' ); ?>><?php \esc_html_e( 'Redirect to Page', 'sofir' ); ?></option>
                                    </select>
                                </td>
                            </tr>
                            <tr class="confirmation-message-row">
                                <th><label for="success_message"><?php \esc_html_e( 'Success Message', 'sofir' ); ?></label></th>
                                <td><textarea id="success_message" name="success_message" rows="3" class="large-text"><?php echo \esc_textarea( $settings['success_message'] ?? 'Thank you for your submission!' ); ?></textarea></td>
                            </tr>
                            <tr class="confirmation-redirect-row" style="display:none;">
                                <th><label for="redirect_url"><?php \esc_html_e( 'Redirect URL', 'sofir' ); ?></label></th>
                                <td><input type="url" id="redirect_url" name="redirect_url" value="<?php echo \esc_attr( $settings['redirect_url'] ?? '' ); ?>" class="regular-text" /></td>
                            </tr>
                            <tr class="confirmation-page-row" style="display:none;">
                                <th><label for="redirect_page"><?php \esc_html_e( 'Redirect Page', 'sofir' ); ?></label></th>
                                <td>
                                    <?php
                                    \wp_dropdown_pages( [
                                        'name' => 'redirect_page',
                                        'selected' => $settings['redirect_page'] ?? 0,
                                        'show_option_none' => \__( 'Select Page', 'sofir' ),
                                    ] );
                                    ?>
                                </td>
                            </tr>
                        </table>
                    </div>

                    <div id="tab-actions" class="tab-content" style="display:none;">
                        <table class="form-table">
                            <tr>
                                <th><label><?php \esc_html_e( 'Create Post', 'sofir' ); ?></label></th>
                                <td>
                                    <label>
                                        <input type="checkbox" name="enable_post_creation" value="1" <?php \checked( $settings['enable_post_creation'] ?? '', '1' ); ?> />
                                        <?php \esc_html_e( 'Create WordPress post from submission', 'sofir' ); ?>
                                    </label>
                                    <div class="post-creation-options" style="<?php echo isset( $settings['enable_post_creation'] ) && '1' === $settings['enable_post_creation'] ? '' : 'display:none;'; ?> margin-top:10px;">
                                        <select name="post_type">
                                            <option value="post"><?php \esc_html_e( 'Post', 'sofir' ); ?></option>
                                            <option value="page"><?php \esc_html_e( 'Page', 'sofir' ); ?></option>
                                            <?php
                                            $post_types = \get_post_types( [ 'public' => true, '_builtin' => false ], 'objects' );
                                            foreach ( $post_types as $post_type ) {
                                                echo '<option value="' . \esc_attr( $post_type->name ) . '">' . \esc_html( $post_type->label ) . '</option>';
                                            }
                                            ?>
                                        </select>
                                        <select name="post_status">
                                            <option value="draft"><?php \esc_html_e( 'Draft', 'sofir' ); ?></option>
                                            <option value="pending"><?php \esc_html_e( 'Pending Review', 'sofir' ); ?></option>
                                            <option value="publish"><?php \esc_html_e( 'Published', 'sofir' ); ?></option>
                                        </select>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th><label><?php \esc_html_e( 'User Registration', 'sofir' ); ?></label></th>
                                <td>
                                    <label>
                                        <input type="checkbox" name="enable_user_registration" value="1" <?php \checked( $settings['enable_user_registration'] ?? '', '1' ); ?> />
                                        <?php \esc_html_e( 'Register WordPress user from submission', 'sofir' ); ?>
                                    </label>
                                    <div class="user-registration-options" style="<?php echo isset( $settings['enable_user_registration'] ) && '1' === $settings['enable_user_registration'] ? '' : 'display:none;'; ?> margin-top:10px;">
                                        <select name="user_role">
                                            <?php
                                            $roles = \wp_roles()->get_names();
                                            foreach ( $roles as $role => $label ) {
                                                echo '<option value="' . \esc_attr( $role ) . '">' . \esc_html( $label ) . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th><label><?php \esc_html_e( 'Webhooks', 'sofir' ); ?></label></th>
                                <td>
                                    <textarea name="webhook_urls" rows="3" class="large-text" placeholder="<?php \esc_attr_e( 'Enter webhook URLs (one per line)', 'sofir' ); ?>"><?php echo \esc_textarea( $settings['webhook_urls'] ?? '' ); ?></textarea>
                                    <p class="description"><?php \esc_html_e( 'Form data will be sent to these URLs via POST request', 'sofir' ); ?></p>
                                </td>
                            </tr>
                        </table>
                    </div>

                    <div id="tab-restrictions" class="tab-content" style="display:none;">
                        <table class="form-table">
                            <tr>
                                <th><label><?php \esc_html_e( 'Limit Submissions', 'sofir' ); ?></label></th>
                                <td>
                                    <label>
                                        <input type="checkbox" name="enable_submission_limit" value="1" <?php \checked( $settings['enable_submission_limit'] ?? '', '1' ); ?> />
                                        <?php \esc_html_e( 'Limit total number of submissions', 'sofir' ); ?>
                                    </label>
                                    <input type="number" name="submission_limit" value="<?php echo \esc_attr( $settings['submission_limit'] ?? '' ); ?>" style="width:100px; margin-left:10px;" placeholder="e.g., 100" />
                                </td>
                            </tr>
                            <tr>
                                <th><label><?php \esc_html_e( 'One Submission Per User', 'sofir' ); ?></label></th>
                                <td>
                                    <label>
                                        <input type="checkbox" name="one_submission_per_user" value="1" <?php \checked( $settings['one_submission_per_user'] ?? '', '1' ); ?> />
                                        <?php \esc_html_e( 'Allow only one submission per logged-in user', 'sofir' ); ?>
                                    </label>
                                </td>
                            </tr>
                            <tr>
                                <th><label><?php \esc_html_e( 'Require Login', 'sofir' ); ?></label></th>
                                <td>
                                    <label>
                                        <input type="checkbox" name="require_login" value="1" <?php \checked( $settings['require_login'] ?? '', '1' ); ?> />
                                        <?php \esc_html_e( 'Only logged-in users can submit', 'sofir' ); ?>
                                    </label>
                                </td>
                            </tr>
                            <tr>
                                <th><label><?php \esc_html_e( 'Google reCAPTCHA', 'sofir' ); ?></label></th>
                                <td>
                                    <label>
                                        <input type="checkbox" name="enable_recaptcha" value="1" <?php \checked( $settings['enable_recaptcha'] ?? '', '1' ); ?> />
                                        <?php \esc_html_e( 'Enable reCAPTCHA protection', 'sofir' ); ?>
                                    </label>
                                    <div class="recaptcha-options" style="<?php echo isset( $settings['enable_recaptcha'] ) && '1' === $settings['enable_recaptcha'] ? '' : 'display:none;'; ?> margin-top:10px;">
                                        <input type="text" name="recaptcha_site_key" value="<?php echo \esc_attr( $settings['recaptcha_site_key'] ?? '' ); ?>" class="regular-text" placeholder="Site Key" />
                                        <input type="text" name="recaptcha_secret_key" value="<?php echo \esc_attr( $settings['recaptcha_secret_key'] ?? '' ); ?>" class="regular-text" placeholder="Secret Key" />
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>

                    <div id="tab-payment" class="tab-content" style="display:none;">
                        <table class="form-table">
                            <tr>
                                <th><label><?php \esc_html_e( 'Enable Payment', 'sofir' ); ?></label></th>
                                <td>
                                    <label>
                                        <input type="checkbox" name="enable_payment" value="1" <?php \checked( $settings['enable_payment'] ?? '', '1' ); ?> />
                                        <?php \esc_html_e( 'Accept payments with this form', 'sofir' ); ?>
                                    </label>
                                </td>
                            </tr>
                            <tr class="payment-options" style="<?php echo isset( $settings['enable_payment'] ) && '1' === $settings['enable_payment'] ? '' : 'display:none;'; ?>">
                                <th><label><?php \esc_html_e( 'Payment Gateway', 'sofir' ); ?></label></th>
                                <td>
                                    <select name="payment_gateway">
                                        <option value="stripe" <?php \selected( $settings['payment_gateway'] ?? '', 'stripe' ); ?>>Stripe</option>
                                        <option value="paypal" <?php \selected( $settings['payment_gateway'] ?? '', 'paypal' ); ?>>PayPal</option>
                                        <option value="razorpay" <?php \selected( $settings['payment_gateway'] ?? '', 'razorpay' ); ?>>Razorpay</option>
                                        <option value="manual" <?php \selected( $settings['payment_gateway'] ?? '', 'manual' ); ?>>Manual/Bank Transfer</option>
                                    </select>
                                </td>
                            </tr>
                            <tr class="payment-options stripe-options" style="<?php echo isset( $settings['payment_gateway'] ) && 'stripe' === $settings['payment_gateway'] ? '' : 'display:none;'; ?>">
                                <th><label><?php \esc_html_e( 'Stripe Settings', 'sofir' ); ?></label></th>
                                <td>
                                    <input type="text" name="stripe_publishable_key" value="<?php echo \esc_attr( $settings['stripe_publishable_key'] ?? '' ); ?>" class="regular-text" placeholder="Publishable Key" /><br/>
                                    <input type="text" name="stripe_secret_key" value="<?php echo \esc_attr( $settings['stripe_secret_key'] ?? '' ); ?>" class="regular-text" placeholder="Secret Key" style="margin-top:5px;" />
                                </td>
                            </tr>
                            <tr class="payment-options paypal-options" style="<?php echo isset( $settings['payment_gateway'] ) && 'paypal' === $settings['payment_gateway'] ? '' : 'display:none;'; ?>">
                                <th><label><?php \esc_html_e( 'PayPal Settings', 'sofir' ); ?></label></th>
                                <td>
                                    <input type="email" name="paypal_email" value="<?php echo \esc_attr( $settings['paypal_email'] ?? '' ); ?>" class="regular-text" placeholder="PayPal Email" />
                                    <label style="margin-top:10px; display:block;">
                                        <input type="checkbox" name="paypal_sandbox" value="1" <?php \checked( $settings['paypal_sandbox'] ?? '', '1' ); ?> />
                                        <?php \esc_html_e( 'Enable sandbox mode', 'sofir' ); ?>
                                    </label>
                                </td>
                            </tr>
                            <tr class="payment-options" style="<?php echo isset( $settings['enable_payment'] ) && '1' === $settings['enable_payment'] ? '' : 'display:none;'; ?>">
                                <th><label><?php \esc_html_e( 'Payment Currency', 'sofir' ); ?></label></th>
                                <td>
                                    <select name="payment_currency">
                                        <option value="USD" <?php \selected( $settings['payment_currency'] ?? 'USD', 'USD' ); ?>>USD</option>
                                        <option value="EUR" <?php \selected( $settings['payment_currency'] ?? '', 'EUR' ); ?>>EUR</option>
                                        <option value="GBP" <?php \selected( $settings['payment_currency'] ?? '', 'GBP' ); ?>>GBP</option>
                                        <option value="IDR" <?php \selected( $settings['payment_currency'] ?? '', 'IDR' ); ?>>IDR</option>
                                        <option value="INR" <?php \selected( $settings['payment_currency'] ?? '', 'INR' ); ?>>INR</option>
                                    </select>
                                </td>
                            </tr>
                        </table>
                    </div>

                    <div id="tab-advanced" class="tab-content" style="display:none;">
                        <table class="form-table">
                            <tr>
                                <th><label><?php \esc_html_e( 'Quiz Mode', 'sofir' ); ?></label></th>
                                <td>
                                    <label>
                                        <input type="checkbox" name="enable_quiz_mode" value="1" <?php \checked( $settings['enable_quiz_mode'] ?? '', '1' ); ?> />
                                        <?php \esc_html_e( 'Enable quiz/survey scoring', 'sofir' ); ?>
                                    </label>
                                </td>
                            </tr>
                            <tr>
                                <th><label><?php \esc_html_e( 'Generate PDF', 'sofir' ); ?></label></th>
                                <td>
                                    <label>
                                        <input type="checkbox" name="enable_pdf_generation" value="1" <?php \checked( $settings['enable_pdf_generation'] ?? '', '1' ); ?> />
                                        <?php \esc_html_e( 'Generate PDF from submissions', 'sofir' ); ?>
                                    </label>
                                </td>
                            </tr>
                            <tr>
                                <th><label for="custom_css"><?php \esc_html_e( 'Custom CSS', 'sofir' ); ?></label></th>
                                <td><textarea id="custom_css" name="custom_css" rows="8" class="large-text code"><?php echo \esc_textarea( $settings['custom_css'] ?? '' ); ?></textarea></td>
                            </tr>
                            <tr>
                                <th><label for="custom_js"><?php \esc_html_e( 'Custom JavaScript', 'sofir' ); ?></label></th>
                                <td><textarea id="custom_js" name="custom_js" rows="8" class="large-text code"><?php echo \esc_textarea( $settings['custom_js'] ?? '' ); ?></textarea></td>
                            </tr>
                        </table>
                    </div>
                </div>

                <script>
                jQuery(document).ready(function($) {
                    $('.nav-tab').on('click', function(e) {
                        e.preventDefault();
                        var target = $(this).attr('href');
                        $('.nav-tab').removeClass('nav-tab-active');
                        $(this).addClass('nav-tab-active');
                        $('.tab-content').hide();
                        $(target).show();
                    });

                    $('input[name="enable_scheduling"]').on('change', function() {
                        $('.scheduling-options').toggle(this.checked);
                    });

                    $('input[name="enable_user_notification"]').on('change', function() {
                        $('.user-notification-options').toggle(this.checked);
                    });

                    $('select[name="confirmation_type"]').on('change', function() {
                        $('.confirmation-message-row, .confirmation-redirect-row, .confirmation-page-row').hide();
                        if ($(this).val() === 'message') {
                            $('.confirmation-message-row').show();
                        } else if ($(this).val() === 'redirect') {
                            $('.confirmation-redirect-row').show();
                        } else if ($(this).val() === 'page') {
                            $('.confirmation-page-row').show();
                        }
                    }).trigger('change');

                    $('input[name="enable_post_creation"]').on('change', function() {
                        $('.post-creation-options').toggle(this.checked);
                    });

                    $('input[name="enable_user_registration"]').on('change', function() {
                        $('.user-registration-options').toggle(this.checked);
                    });

                    $('input[name="enable_recaptcha"]').on('change', function() {
                        $('.recaptcha-options').toggle(this.checked);
                    });

                    $('input[name="enable_payment"]').on('change', function() {
                        $('.payment-options').toggle(this.checked);
                    });

                    $('select[name="payment_gateway"]').on('change', function() {
                        $('.stripe-options, .paypal-options').hide();
                        if ($(this).val() === 'stripe') {
                            $('.stripe-options').show();
                        } else if ($(this).val() === 'paypal') {
                            $('.paypal-options').show();
                        }
                    });

                    $(document).on('change', 'input[name*="[enable_conditional]"]', function() {
                        $(this).closest('tr').find('.conditional-rules').toggle(this.checked);
                    });

                    $(document).on('change', '.field-type-selector', function() {
                        var $editor = $(this).closest('.field-editor');
                        var type = $(this).val();
                        
                        $editor.find('.calculation-row, .min-max-row, .file-types-row').hide();
                        
                        if (type === 'calculation') {
                            $editor.find('.calculation-row').show();
                        }
                        if (['number', 'range'].includes(type)) {
                            $editor.find('.min-max-row').show();
                        }
                        if (type === 'file') {
                            $editor.find('.file-types-row').show();
                        }
                    });
                });
                </script>

                <?php \submit_button( \__( 'Save Form', 'sofir' ) ); ?>
            </form>

            <?php if ( $form ) : ?>
                <div class="sofir-form-shortcode" style="background: #fff; padding: 20px; border-left: 4px solid #0073aa; margin-top: 20px;">
                    <h3><?php \esc_html_e( 'Use this shortcode:', 'sofir' ); ?></h3>
                    <input type="text" readonly value='[sofir_form id="<?php echo \esc_attr( $form->ID ); ?>"]' style="width: 100%; padding: 10px;" onclick="this.select();" />
                </div>
            <?php endif; ?>
        </div>

        <script>
        jQuery(document).ready(function($) {
            var fieldIndex = <?php echo $fields ? \count( $fields ) : 0; ?>;

            $('#add-field').on('click', function() {
                var html = `
                    <div class="field-editor" style="background: #fff; padding: 15px; margin-bottom: 15px; border: 1px solid #ccc;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                            <h4 style="margin: 0;">Field ${fieldIndex + 1}</h4>
                            <button type="button" class="button remove-field" style="color: #dc3232;">Remove</button>
                        </div>
                        <table class="form-table">
                            <tr>
                                <th><label>Label</label></th>
                                <td><input type="text" name="form_fields[${fieldIndex}][label]" class="regular-text" /></td>
                            </tr>
                            <tr>
                                <th><label>Type</label></th>
                                <td>
                                    <select name="form_fields[${fieldIndex}][type]" class="field-type-selector">
                                        <optgroup label="Basic Fields">
                                            <option value="text">Text</option>
                                            <option value="email">Email</option>
                                            <option value="tel">Phone</option>
                                            <option value="number">Number</option>
                                            <option value="textarea">Textarea</option>
                                            <option value="url">URL</option>
                                            <option value="password">Password</option>
                                        </optgroup>
                                        <optgroup label="Choice Fields">
                                            <option value="select">Select Dropdown</option>
                                            <option value="radio">Radio Buttons</option>
                                            <option value="checkbox">Checkboxes</option>
                                            <option value="multiselect">Multi-Select</option>
                                        </optgroup>
                                        <optgroup label="Advanced Fields">
                                            <option value="date">Date</option>
                                            <option value="time">Time</option>
                                            <option value="datetime">Date & Time</option>
                                            <option value="file">File Upload</option>
                                            <option value="rating">Rating (Star)</option>
                                            <option value="range">Range Slider</option>
                                            <option value="calculation">Calculation</option>
                                            <option value="repeater">Repeater Field</option>
                                        </optgroup>
                                        <optgroup label="Content Fields">
                                            <option value="hidden">Hidden Field</option>
                                            <option value="html">HTML Block</option>
                                            <option value="section">Section Break</option>
                                            <option value="signature">Signature</option>
                                            <option value="terms">Terms & Conditions</option>
                                        </optgroup>
                                        <optgroup label="Payment Fields">
                                            <option value="payment_amount">Payment Amount</option>
                                            <option value="payment_method">Payment Method</option>
                                        </optgroup>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <th><label>Required</label></th>
                                <td><input type="checkbox" name="form_fields[${fieldIndex}][required]" value="1" /></td>
                            </tr>
                            <tr>
                                <th><label>Placeholder</label></th>
                                <td><input type="text" name="form_fields[${fieldIndex}][placeholder]" class="regular-text" /></td>
                            </tr>
                            <tr>
                                <th><label>Options</label></th>
                                <td>
                                    <textarea name="form_fields[${fieldIndex}][options]" rows="3" class="regular-text"></textarea>
                                    <p class="description">For select/radio/checkbox. One per line.</p>
                                </td>
                            </tr>
                            <tr class="conditional-logic-row">
                                <th><label>Conditional Logic</label></th>
                                <td>
                                    <input type="checkbox" name="form_fields[${fieldIndex}][enable_conditional]" value="1" />
                                    <span class="description">Show field based on other field values</span>
                                    <div class="conditional-rules" style="display:none; margin-top:10px; padding:10px; background:#f5f5f5;">
                                        <select name="form_fields[${fieldIndex}][conditional_field]">
                                            <option value="">Select field...</option>
                                        </select>
                                        <select name="form_fields[${fieldIndex}][conditional_operator]">
                                            <option value="equals">Equals</option>
                                            <option value="not_equals">Not Equals</option>
                                            <option value="contains">Contains</option>
                                            <option value="greater_than">Greater Than</option>
                                            <option value="less_than">Less Than</option>
                                        </select>
                                        <input type="text" name="form_fields[${fieldIndex}][conditional_value]" placeholder="Value" />
                                    </div>
                                </td>
                            </tr>
                            <tr class="calculation-row" style="display:none;">
                                <th><label>Calculation Formula</label></th>
                                <td>
                                    <input type="text" name="form_fields[${fieldIndex}][calculation_formula]" class="regular-text" placeholder="e.g., {field_1} * {field_2}" />
                                    <p class="description">Use {field_X} to reference other numeric fields</p>
                                </td>
                            </tr>
                            <tr class="min-max-row" style="display:none;">
                                <th><label>Min/Max Value</label></th>
                                <td>
                                    <input type="number" name="form_fields[${fieldIndex}][min_value]" placeholder="Min" style="width:100px;" />
                                    <input type="number" name="form_fields[${fieldIndex}][max_value]" placeholder="Max" style="width:100px;" />
                                </td>
                            </tr>
                            <tr class="file-types-row" style="display:none;">
                                <th><label>Allowed File Types</label></th>
                                <td>
                                    <input type="text" name="form_fields[${fieldIndex}][allowed_types]" class="regular-text" placeholder="jpg,png,pdf,doc" />
                                    <p class="description">Comma-separated file extensions</p>
                                </td>
                            </tr>
                        </table>
                    </div>
                `;
                
                $('#form-fields-container').append(html);
                fieldIndex++;
            });

            $(document).on('click', '.remove-field', function() {
                $(this).closest('.field-editor').remove();
            });
        });
        </script>
        <?php
    }

    private function render_field_editor( int $index, array $field ): void {
        ?>
        <div class="field-editor" style="background: #fff; padding: 15px; margin-bottom: 15px; border: 1px solid #ccc;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                <h4 style="margin: 0;">Field <?php echo \esc_html( $index + 1 ); ?></h4>
                <button type="button" class="button remove-field" style="color: #dc3232;">Remove</button>
            </div>
            <table class="form-table">
                <tr>
                    <th><label>Label</label></th>
                    <td><input type="text" name="form_fields[<?php echo \esc_attr( $index ); ?>][label]" value="<?php echo \esc_attr( $field['label'] ?? '' ); ?>" class="regular-text" /></td>
                </tr>
                <tr>
                    <th><label>Type</label></th>
                    <td>
                        <select name="form_fields[<?php echo \esc_attr( $index ); ?>][type]" class="field-type-selector">
                            <optgroup label="Basic Fields">
                                <option value="text" <?php \selected( $field['type'] ?? '', 'text' ); ?>>Text</option>
                                <option value="email" <?php \selected( $field['type'] ?? '', 'email' ); ?>>Email</option>
                                <option value="tel" <?php \selected( $field['type'] ?? '', 'tel' ); ?>>Phone</option>
                                <option value="number" <?php \selected( $field['type'] ?? '', 'number' ); ?>>Number</option>
                                <option value="textarea" <?php \selected( $field['type'] ?? '', 'textarea' ); ?>>Textarea</option>
                                <option value="url" <?php \selected( $field['type'] ?? '', 'url' ); ?>>URL</option>
                                <option value="password" <?php \selected( $field['type'] ?? '', 'password' ); ?>>Password</option>
                            </optgroup>
                            <optgroup label="Choice Fields">
                                <option value="select" <?php \selected( $field['type'] ?? '', 'select' ); ?>>Select Dropdown</option>
                                <option value="radio" <?php \selected( $field['type'] ?? '', 'radio' ); ?>>Radio Buttons</option>
                                <option value="checkbox" <?php \selected( $field['type'] ?? '', 'checkbox' ); ?>>Checkboxes</option>
                                <option value="multiselect" <?php \selected( $field['type'] ?? '', 'multiselect' ); ?>>Multi-Select</option>
                            </optgroup>
                            <optgroup label="Advanced Fields">
                                <option value="date" <?php \selected( $field['type'] ?? '', 'date' ); ?>>Date</option>
                                <option value="time" <?php \selected( $field['type'] ?? '', 'time' ); ?>>Time</option>
                                <option value="datetime" <?php \selected( $field['type'] ?? '', 'datetime' ); ?>>Date & Time</option>
                                <option value="file" <?php \selected( $field['type'] ?? '', 'file' ); ?>>File Upload</option>
                                <option value="rating" <?php \selected( $field['type'] ?? '', 'rating' ); ?>>Rating (Star)</option>
                                <option value="range" <?php \selected( $field['type'] ?? '', 'range' ); ?>>Range Slider</option>
                                <option value="calculation" <?php \selected( $field['type'] ?? '', 'calculation' ); ?>>Calculation</option>
                                <option value="repeater" <?php \selected( $field['type'] ?? '', 'repeater' ); ?>>Repeater Field</option>
                            </optgroup>
                            <optgroup label="Content Fields">
                                <option value="hidden" <?php \selected( $field['type'] ?? '', 'hidden' ); ?>>Hidden Field</option>
                                <option value="html" <?php \selected( $field['type'] ?? '', 'html' ); ?>>HTML Block</option>
                                <option value="section" <?php \selected( $field['type'] ?? '', 'section' ); ?>>Section Break</option>
                                <option value="signature" <?php \selected( $field['type'] ?? '', 'signature' ); ?>>Signature</option>
                                <option value="terms" <?php \selected( $field['type'] ?? '', 'terms' ); ?>>Terms & Conditions</option>
                            </optgroup>
                            <optgroup label="Payment Fields">
                                <option value="payment_amount" <?php \selected( $field['type'] ?? '', 'payment_amount' ); ?>>Payment Amount</option>
                                <option value="payment_method" <?php \selected( $field['type'] ?? '', 'payment_method' ); ?>>Payment Method</option>
                            </optgroup>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th><label>Required</label></th>
                    <td><input type="checkbox" name="form_fields[<?php echo \esc_attr( $index ); ?>][required]" value="1" <?php \checked( $field['required'] ?? '', '1' ); ?> /></td>
                </tr>
                <tr>
                    <th><label>Placeholder</label></th>
                    <td><input type="text" name="form_fields[<?php echo \esc_attr( $index ); ?>][placeholder]" value="<?php echo \esc_attr( $field['placeholder'] ?? '' ); ?>" class="regular-text" /></td>
                </tr>
                <tr>
                    <th><label>Options</label></th>
                    <td>
                        <textarea name="form_fields[<?php echo \esc_attr( $index ); ?>][options]" rows="3" class="regular-text"><?php echo \esc_textarea( $field['options'] ?? '' ); ?></textarea>
                        <p class="description">For select/radio/checkbox. One per line.</p>
                    </td>
                </tr>
            </table>
        </div>
        <?php
    }

    public function render_form( array $atts ): string {
        $atts = \shortcode_atts( [
            'id' => 0,
        ], $atts );

        $form_id = (int) $atts['id'];

        if ( ! $form_id ) {
            return '<p>' . \esc_html__( 'Form ID is required.', 'sofir' ) . '</p>';
        }

        $form = \get_post( $form_id );

        if ( ! $form || 'sofir_form' !== $form->post_type ) {
            return '<p>' . \esc_html__( 'Form not found.', 'sofir' ) . '</p>';
        }

        $fields = \get_post_meta( $form_id, 'sofir_form_fields', true ) ?: [];
        $settings = \get_post_meta( $form_id, 'sofir_form_settings', true ) ?: [];

        ob_start();
        ?>
        <div class="sofir-form-container">
            <form class="sofir-custom-form" method="post" action="<?php echo \esc_url( \admin_url( 'admin-post.php' ) ); ?>" enctype="multipart/form-data">
                <input type="hidden" name="action" value="sofir_submit_form" />
                <input type="hidden" name="form_id" value="<?php echo \esc_attr( $form_id ); ?>" />
                <?php \wp_nonce_field( 'sofir_form_' . $form_id, 'sofir_form_nonce' ); ?>

                <?php foreach ( $fields as $index => $field ) : ?>
                    <?php $this->render_form_field( $index, $field ); ?>
                <?php endforeach; ?>

                <div class="sofir-form-submit">
                    <button type="submit" class="button button-primary">
                        <?php echo \esc_html( $settings['button_text'] ?? 'Submit' ); ?>
                    </button>
                </div>
            </form>

            <div class="sofir-form-message" style="display: none;"></div>
        </div>
        <?php
        return (string) ob_get_clean();
    }

    private function render_form_field( int $index, array $field ): void {
        $type = $field['type'] ?? 'text';
        $label = $field['label'] ?? '';
        $required = isset( $field['required'] ) && '1' === $field['required'];
        $placeholder = $field['placeholder'] ?? '';
        $name = 'field_' . $index;

        echo '<div class="sofir-form-field">';
        
        if ( $label ) {
            echo '<label for="' . \esc_attr( $name ) . '">' . \esc_html( $label );
            if ( $required ) {
                echo ' <span class="required">*</span>';
            }
            echo '</label>';
        }

        switch ( $type ) {
            case 'textarea':
                echo '<textarea id="' . \esc_attr( $name ) . '" name="' . \esc_attr( $name ) . '" placeholder="' . \esc_attr( $placeholder ) . '" ' . ( $required ? 'required' : '' ) . '></textarea>';
                break;

            case 'select':
                echo '<select id="' . \esc_attr( $name ) . '" name="' . \esc_attr( $name ) . '" ' . ( $required ? 'required' : '' ) . '>';
                echo '<option value="">Select...</option>';
                $options = \explode( "\n", $field['options'] ?? '' );
                foreach ( $options as $option ) {
                    $option = \trim( $option );
                    if ( $option ) {
                        echo '<option value="' . \esc_attr( $option ) . '">' . \esc_html( $option ) . '</option>';
                    }
                }
                echo '</select>';
                break;

            case 'radio':
            case 'checkbox':
                $options = \explode( "\n", $field['options'] ?? '' );
                foreach ( $options as $option ) {
                    $option = \trim( $option );
                    if ( $option ) {
                        echo '<label><input type="' . \esc_attr( $type ) . '" name="' . \esc_attr( $name ) . ( 'checkbox' === $type ? '[]' : '' ) . '" value="' . \esc_attr( $option ) . '" ' . ( $required ? 'required' : '' ) . ' /> ' . \esc_html( $option ) . '</label><br>';
                    }
                }
                break;

            case 'file':
                echo '<input type="file" id="' . \esc_attr( $name ) . '" name="' . \esc_attr( $name ) . '" ' . ( $required ? 'required' : '' ) . ' />';
                break;

            case 'rating':
                echo '<div class="sofir-rating-field" data-field="' . \esc_attr( $name ) . '">';
                for ( $i = 1; $i <= 5; $i++ ) {
                    echo '<span class="sofir-star" data-value="' . $i . '">★</span>';
                }
                echo '</div>';
                echo '<input type="hidden" id="' . \esc_attr( $name ) . '" name="' . \esc_attr( $name ) . '" ' . ( $required ? 'required' : '' ) . ' />';
                break;

            case 'hidden':
                echo '<input type="hidden" id="' . \esc_attr( $name ) . '" name="' . \esc_attr( $name ) . '" value="' . \esc_attr( $field['default_value'] ?? '' ) . '" />';
                break;

            case 'html':
                echo '<div class="sofir-html-block">' . \wp_kses_post( $field['html_content'] ?? '' ) . '</div>';
                break;

            case 'section':
                echo '<div class="sofir-section-break">';
                if ( $label ) {
                    echo '<h3>' . \esc_html( $label ) . '</h3>';
                }
                if ( ! empty( $field['description'] ) ) {
                    echo '<p>' . \esc_html( $field['description'] ) . '</p>';
                }
                echo '</div>';
                break;

            case 'signature':
                echo '<div class="sofir-signature-pad">';
                echo '<canvas id="' . \esc_attr( $name ) . '_canvas" width="400" height="200" style="border: 1px solid #ccc; cursor: crosshair;"></canvas>';
                echo '<div class="sofir-signature-actions" style="margin-top: 10px;">';
                echo '<button type="button" class="button sofir-clear-signature" data-field="' . \esc_attr( $name ) . '">' . \esc_html__( 'Clear', 'sofir' ) . '</button>';
                echo '</div>';
                echo '<input type="hidden" id="' . \esc_attr( $name ) . '" name="' . \esc_attr( $name ) . '" ' . ( $required ? 'required' : '' ) . ' />';
                echo '</div>';
                break;

            case 'multiselect':
                echo '<select id="' . \esc_attr( $name ) . '" name="' . \esc_attr( $name ) . '[]" multiple ' . ( $required ? 'required' : '' ) . ' size="5">';
                $options = \explode( "\n", $field['options'] ?? '' );
                foreach ( $options as $option ) {
                    $option = \trim( $option );
                    if ( $option ) {
                        echo '<option value="' . \esc_attr( $option ) . '">' . \esc_html( $option ) . '</option>';
                    }
                }
                echo '</select>';
                echo '<p class="description">' . \esc_html__( 'Hold Ctrl (Windows) or Cmd (Mac) to select multiple', 'sofir' ) . '</p>';
                break;

            case 'datetime':
                echo '<input type="datetime-local" id="' . \esc_attr( $name ) . '" name="' . \esc_attr( $name ) . '" ' . ( $required ? 'required' : '' ) . ' />';
                break;

            case 'url':
                echo '<input type="url" id="' . \esc_attr( $name ) . '" name="' . \esc_attr( $name ) . '" placeholder="' . \esc_attr( $placeholder ) . '" ' . ( $required ? 'required' : '' ) . ' />';
                break;

            case 'password':
                echo '<input type="password" id="' . \esc_attr( $name ) . '" name="' . \esc_attr( $name ) . '" placeholder="' . \esc_attr( $placeholder ) . '" ' . ( $required ? 'required' : '' ) . ' />';
                break;

            case 'range':
                $min = $field['min_value'] ?? 0;
                $max = $field['max_value'] ?? 100;
                echo '<input type="range" id="' . \esc_attr( $name ) . '" name="' . \esc_attr( $name ) . '" min="' . \esc_attr( $min ) . '" max="' . \esc_attr( $max ) . '" ' . ( $required ? 'required' : '' ) . ' />';
                echo '<output for="' . \esc_attr( $name ) . '" id="' . \esc_attr( $name ) . '_output">50</output>';
                echo '<script>document.getElementById("' . \esc_js( $name ) . '").addEventListener("input", function() { document.getElementById("' . \esc_js( $name ) . '_output").value = this.value; });</script>';
                break;

            case 'calculation':
                echo '<input type="text" id="' . \esc_attr( $name ) . '" name="' . \esc_attr( $name ) . '" readonly class="sofir-calculation-field" data-formula="' . \esc_attr( $field['calculation_formula'] ?? '' ) . '" />';
                break;

            case 'terms':
                $terms_text = $field['html_content'] ?? \__( 'I agree to the terms and conditions', 'sofir' );
                echo '<label><input type="checkbox" id="' . \esc_attr( $name ) . '" name="' . \esc_attr( $name ) . '" value="1" ' . ( $required ? 'required' : '' ) . ' /> ' . \wp_kses_post( $terms_text ) . '</label>';
                break;

            case 'payment_amount':
                $currency = $field['currency'] ?? 'USD';
                echo '<div class="sofir-payment-amount">';
                echo '<span class="currency-symbol">' . \esc_html( $currency ) . '</span> ';
                echo '<input type="number" id="' . \esc_attr( $name ) . '" name="' . \esc_attr( $name ) . '" placeholder="0.00" step="0.01" min="0" ' . ( $required ? 'required' : '' ) . ' />';
                echo '</div>';
                break;

            case 'payment_method':
                echo '<select id="' . \esc_attr( $name ) . '" name="' . \esc_attr( $name ) . '" ' . ( $required ? 'required' : '' ) . '>';
                echo '<option value="">' . \esc_html__( 'Select payment method', 'sofir' ) . '</option>';
                $options = $field['options'] ?? "Credit Card\nPayPal\nBank Transfer";
                $payment_options = \explode( "\n", $options );
                foreach ( $payment_options as $option ) {
                    $option = \trim( $option );
                    if ( $option ) {
                        echo '<option value="' . \esc_attr( $option ) . '">' . \esc_html( $option ) . '</option>';
                    }
                }
                echo '</select>';
                break;

            case 'repeater':
                echo '<div class="sofir-repeater-field" data-field="' . \esc_attr( $name ) . '">';
                echo '<div class="sofir-repeater-items"></div>';
                echo '<button type="button" class="button sofir-add-repeater-item">' . \esc_html__( 'Add Item', 'sofir' ) . '</button>';
                echo '</div>';
                break;

            default:
                echo '<input type="' . \esc_attr( $type ) . '" id="' . \esc_attr( $name ) . '" name="' . \esc_attr( $name ) . '" placeholder="' . \esc_attr( $placeholder ) . '" ' . ( $required ? 'required' : '' ) . ' />';
                break;
        }

        echo '</div>';
    }

    public function handle_form_submission(): void {
        $form_id = isset( $_POST['form_id'] ) ? (int) $_POST['form_id'] : 0;

        if ( ! $form_id || ! \check_admin_referer( 'sofir_form_' . $form_id, 'sofir_form_nonce' ) ) {
            \wp_die( \esc_html__( 'Invalid form submission.', 'sofir' ) );
        }

        $form = \get_post( $form_id );
        if ( ! $form ) {
            \wp_die( \esc_html__( 'Form not found.', 'sofir' ) );
        }

        if ( ! $this->check_form_restrictions( $form_id ) ) {
            \wp_die( \esc_html__( 'Form submission not allowed at this time.', 'sofir' ) );
        }

        $fields = \get_post_meta( $form_id, 'sofir_form_fields', true ) ?: [];
        $settings = \get_post_meta( $form_id, 'sofir_form_settings', true ) ?: [];

        if ( isset( $settings['enable_recaptcha'] ) && '1' === $settings['enable_recaptcha'] ) {
            $recaptcha_response = $_POST['g-recaptcha-response'] ?? '';
            if ( ! $this->verify_recaptcha( $recaptcha_response ) ) {
                \wp_die( \esc_html__( 'reCAPTCHA verification failed.', 'sofir' ) );
            }
        }

        if ( $this->check_spam( $_POST ) ) {
            \wp_die( \esc_html__( 'Spam detected.', 'sofir' ) );
        }

        $submission_data = [];
        $attachments = [];
        
        foreach ( $fields as $index => $field ) {
            $name = 'field_' . $index;
            $type = $field['type'] ?? 'text';
            
            if ( 'file' === $type && isset( $_FILES[ $name ] ) && ! empty( $_FILES[ $name ]['name'] ) ) {
                require_once ABSPATH . 'wp-admin/includes/file.php';
                require_once ABSPATH . 'wp-admin/includes/media.php';
                require_once ABSPATH . 'wp-admin/includes/image.php';
                
                $file = $_FILES[ $name ];
                $upload = \wp_handle_upload( $file, [ 'test_form' => false ] );
                
                if ( isset( $upload['file'] ) ) {
                    $attachment_id = \wp_insert_attachment( [
                        'post_mime_type' => $upload['type'],
                        'post_title' => \sanitize_file_name( $file['name'] ),
                        'post_content' => '',
                        'post_status' => 'inherit',
                    ], $upload['file'] );
                    
                    \wp_update_attachment_metadata( $attachment_id, \wp_generate_attachment_metadata( $attachment_id, $upload['file'] ) );
                    
                    $file_url = \wp_get_attachment_url( $attachment_id );
                    $submission_data[ $field['label'] ?? $name ] = $file_url;
                    $attachments[] = [
                        'id' => $attachment_id,
                        'url' => $file_url,
                        'name' => \basename( $upload['file'] ),
                    ];
                }
            } else {
                $value = $_POST[ $name ] ?? '';
                
                if ( \is_array( $value ) ) {
                    $value = \implode( ', ', \array_map( 'sanitize_text_field', $value ) );
                } else {
                    $value = \sanitize_text_field( $value );
                }

                $submission_data[ $field['label'] ?? $name ] = $value;
            }
        }

        $submission_id = \wp_insert_post( [
            'post_title' => $form->post_title . ' - ' . \current_time( 'mysql' ),
            'post_type' => 'sofir_submission',
            'post_status' => 'publish',
        ] );

        \update_post_meta( $submission_id, 'form_id', $form_id );
        \update_post_meta( $submission_id, 'submission_data', $submission_data );
        \update_post_meta( $submission_id, 'submission_attachments', $attachments );
        \update_post_meta( $submission_id, 'submission_ip', $_SERVER['REMOTE_ADDR'] ?? '' );
        \update_post_meta( $submission_id, 'submission_user_agent', $_SERVER['HTTP_USER_AGENT'] ?? '' );

        if ( \is_user_logged_in() ) {
            \update_post_meta( $submission_id, 'submission_user_id', \get_current_user_id() );
        }

        if ( ! empty( $settings['enable_admin_notification'] ) && '1' === $settings['enable_admin_notification'] && ! empty( $settings['notification_email'] ) ) {
            $to = $settings['notification_email'];
            $subject = $settings['notification_subject'] ?? \sprintf( \__( 'New form submission: %s', 'sofir' ), $form->post_title );
            $message = \__( 'You have received a new form submission:', 'sofir' ) . "\n\n";
            
            foreach ( $submission_data as $label => $value ) {
                $message .= $label . ': ' . $value . "\n";
            }
            
            if ( ! empty( $attachments ) ) {
                $message .= "\n" . \__( 'Attachments:', 'sofir' ) . "\n";
                foreach ( $attachments as $attachment ) {
                    $message .= '- ' . $attachment['name'] . ': ' . $attachment['url'] . "\n";
                }
            }

            \wp_mail( $to, $subject, $message );
        }

        if ( ! empty( $settings['enable_user_notification'] ) && '1' === $settings['enable_user_notification'] ) {
            $user_email = '';
            foreach ( $submission_data as $label => $value ) {
                if ( \stripos( $label, 'email' ) !== false && \is_email( $value ) ) {
                    $user_email = $value;
                    break;
                }
            }

            if ( $user_email ) {
                $subject = $settings['user_notification_subject'] ?? \__( 'Thank you for your submission', 'sofir' );
                $message = $settings['user_notification_message'] ?? \__( 'Thank you for contacting us. We will get back to you soon.', 'sofir' );
                \wp_mail( $user_email, $subject, $message );
            }
        }

        \do_action( 'sofir/form/submitted', $submission_id, $form_id, $submission_data );

        $this->create_post_from_submission( $submission_id, $form_id, $submission_data );
        $this->register_user_from_submission( $submission_id, $form_id, $submission_data );
        $this->send_webhooks( $submission_id, $form_id, $submission_data );

        if ( ! empty( $settings['enable_pdf_generation'] ) && '1' === $settings['enable_pdf_generation'] ) {
            $pdf_url = $this->generate_pdf( $submission_id );
            if ( $pdf_url ) {
                \update_post_meta( $submission_id, 'submission_pdf', $pdf_url );
            }
        }

        $redirect_url = \wp_get_referer();

        if ( ! empty( $settings['confirmation_type'] ) ) {
            switch ( $settings['confirmation_type'] ) {
                case 'redirect':
                    if ( ! empty( $settings['redirect_url'] ) ) {
                        $redirect_url = $settings['redirect_url'];
                    }
                    break;
                case 'page':
                    if ( ! empty( $settings['redirect_page'] ) ) {
                        $redirect_url = \get_permalink( $settings['redirect_page'] );
                    }
                    break;
                default:
                    $redirect_url = \add_query_arg( 'form_submitted', '1', $redirect_url );
                    break;
            }
        } else {
            $redirect_url = \add_query_arg( 'form_submitted', '1', $redirect_url );
        }

        \wp_redirect( $redirect_url );
        exit;
    }

    public function redirect_form_edit(): void {
        global $pagenow;
        
        if ( 'post.php' === $pagenow && isset( $_GET['post'] ) ) {
            $post_id = (int) $_GET['post'];
            $post = \get_post( $post_id );
            
            if ( $post && 'sofir_form' === $post->post_type && isset( $_GET['action'] ) && 'edit' === $_GET['action'] ) {
                \wp_redirect( \admin_url( 'admin.php?page=sofir-forms-new&form_id=' . $post_id ) );
                exit;
            }
        }
    }

    public function add_form_meta_boxes(): void {
        \add_meta_box(
            'sofir_form_builder_info',
            \__( 'Form Builder', 'sofir' ),
            [ $this, 'render_form_builder_meta_box' ],
            'sofir_form',
            'normal',
            'high'
        );
    }

    public function render_form_builder_meta_box( \WP_Post $post ): void {
        echo '<div style="padding: 15px; background: #f0f0f1; border-left: 4px solid #2271b1;">';
        echo '<p style="margin: 0 0 10px 0;"><strong>' . \esc_html__( 'Use the Form Builder to edit this form.', 'sofir' ) . '</strong></p>';
        echo '<a href="' . \esc_url( \admin_url( 'admin.php?page=sofir-forms-new&form_id=' . $post->ID ) ) . '" class="button button-primary">';
        echo \esc_html__( 'Open Form Builder', 'sofir' );
        echo '</a>';
        echo '</div>';
    }

    public function add_submission_meta_boxes(): void {
        \add_meta_box(
            'sofir_submission_data',
            \__( 'Submission Data', 'sofir' ),
            [ $this, 'render_submission_data_meta_box' ],
            'sofir_submission',
            'normal',
            'high'
        );
    }

    public function render_submission_data_meta_box( \WP_Post $post ): void {
        $submission_data = \get_post_meta( $post->ID, 'submission_data', true );
        $attachments = \get_post_meta( $post->ID, 'submission_attachments', true );
        $form_id = \get_post_meta( $post->ID, 'form_id', true );
        $ip = \get_post_meta( $post->ID, 'submission_ip', true );
        $user_agent = \get_post_meta( $post->ID, 'submission_user_agent', true );
        $user_id = \get_post_meta( $post->ID, 'submission_user_id', true );

        echo '<div style="background: #fff; padding: 15px;">';
        
        if ( $form_id ) {
            $form = \get_post( $form_id );
            if ( $form ) {
                echo '<p><strong>' . \esc_html__( 'Form:', 'sofir' ) . '</strong> ';
                echo '<a href="' . \esc_url( \admin_url( 'admin.php?page=sofir-forms-new&form_id=' . $form_id ) ) . '">' . \esc_html( $form->post_title ) . '</a></p>';
            }
        }

        if ( $user_id ) {
            $user = \get_userdata( $user_id );
            if ( $user ) {
                echo '<p><strong>' . \esc_html__( 'Submitted by:', 'sofir' ) . '</strong> ';
                echo '<a href="' . \esc_url( \admin_url( 'user-edit.php?user_id=' . $user_id ) ) . '">' . \esc_html( $user->display_name ) . '</a></p>';
            }
        }

        echo '<h3>' . \esc_html__( 'Form Data', 'sofir' ) . '</h3>';
        
        if ( ! empty( $submission_data ) && \is_array( $submission_data ) ) {
            echo '<table class="widefat striped" style="margin-top: 10px;">';
            echo '<thead><tr><th>' . \esc_html__( 'Field', 'sofir' ) . '</th><th>' . \esc_html__( 'Value', 'sofir' ) . '</th></tr></thead>';
            echo '<tbody>';
            
            foreach ( $submission_data as $label => $value ) {
                echo '<tr>';
                echo '<td style="width: 30%; font-weight: bold;">' . \esc_html( $label ) . '</td>';
                
                if ( \filter_var( $value, FILTER_VALIDATE_URL ) && \preg_match( '/\.(jpg|jpeg|png|gif|pdf|doc|docx|txt)$/i', $value ) ) {
                    echo '<td><a href="' . \esc_url( $value ) . '" target="_blank">' . \esc_html( \basename( $value ) ) . '</a> <span class="dashicons dashicons-download"></span></td>';
                } else {
                    echo '<td>' . \nl2br( \esc_html( $value ) ) . '</td>';
                }
                
                echo '</tr>';
            }
            
            echo '</tbody></table>';
        } else {
            echo '<p>' . \esc_html__( 'No submission data available.', 'sofir' ) . '</p>';
        }

        if ( ! empty( $attachments ) && \is_array( $attachments ) ) {
            echo '<h3 style="margin-top: 20px;">' . \esc_html__( 'Attachments', 'sofir' ) . '</h3>';
            echo '<ul style="list-style: none; padding: 0;">';
            
            foreach ( $attachments as $attachment ) {
                echo '<li style="margin: 10px 0;">';
                echo '<span class="dashicons dashicons-media-default"></span> ';
                echo '<a href="' . \esc_url( $attachment['url'] ) . '" target="_blank">' . \esc_html( $attachment['name'] ) . '</a>';
                echo ' <a href="' . \esc_url( $attachment['url'] ) . '" download class="button button-small">' . \esc_html__( 'Download', 'sofir' ) . '</a>';
                echo '</li>';
            }
            
            echo '</ul>';
        }

        echo '<h3 style="margin-top: 20px;">' . \esc_html__( 'Submission Details', 'sofir' ) . '</h3>';
        echo '<table class="widefat">';
        echo '<tr><td style="width: 30%; font-weight: bold;">' . \esc_html__( 'IP Address', 'sofir' ) . '</td><td>' . \esc_html( $ip ) . '</td></tr>';
        echo '<tr><td style="width: 30%; font-weight: bold;">' . \esc_html__( 'User Agent', 'sofir' ) . '</td><td>' . \esc_html( $user_agent ) . '</td></tr>';
        echo '<tr><td style="width: 30%; font-weight: bold;">' . \esc_html__( 'Submitted At', 'sofir' ) . '</td><td>' . \esc_html( \get_the_date( 'Y-m-d H:i:s', $post->ID ) ) . '</td></tr>';
        echo '</table>';
        
        echo '</div>';
    }

    public function register_rest_routes(): void {
        \register_rest_route(
            'sofir/v1',
            '/forms',
            [
                'methods' => 'GET',
                'callback' => [ $this, 'get_forms' ],
                'permission_callback' => '__return_true',
            ]
        );

        \register_rest_route(
            'sofir/v1',
            '/forms/(?P<id>\d+)',
            [
                'methods' => 'GET',
                'callback' => [ $this, 'get_form' ],
                'permission_callback' => '__return_true',
            ]
        );

        \register_rest_route(
            'sofir/v1',
            '/forms/(?P<id>\d+)/submissions',
            [
                'methods' => 'GET',
                'callback' => [ $this, 'get_form_submissions' ],
                'permission_callback' => function (): bool {
                    return \current_user_can( 'manage_options' );
                },
            ]
        );
    }

    public function get_forms( \WP_REST_Request $request ): \WP_REST_Response {
        $forms = \get_posts( [
            'post_type' => 'sofir_form',
            'posts_per_page' => -1,
        ] );

        $data = array_map( function ( $form ) {
            return [
                'id' => $form->ID,
                'title' => $form->post_title,
                'shortcode' => '[sofir_form id="' . $form->ID . '"]',
            ];
        }, $forms );

        return new \WP_REST_Response( $data, 200 );
    }

    public function get_form( \WP_REST_Request $request ): \WP_REST_Response {
        $form_id = $request->get_param( 'id' );
        $form = \get_post( $form_id );

        if ( ! $form || 'sofir_form' !== $form->post_type ) {
            return new \WP_REST_Response(
                [ 'message' => \__( 'Form not found.', 'sofir' ) ],
                404
            );
        }

        return new \WP_REST_Response(
            [
                'id' => $form->ID,
                'title' => $form->post_title,
                'fields' => \get_post_meta( $form->ID, 'sofir_form_fields', true ),
                'settings' => \get_post_meta( $form->ID, 'sofir_form_settings', true ),
            ],
            200
        );
    }

    public function get_form_submissions( \WP_REST_Request $request ): \WP_REST_Response {
        $form_id = $request->get_param( 'id' );

        $submissions = \get_posts( [
            'post_type' => 'sofir_submission',
            'meta_key' => 'form_id',
            'meta_value' => $form_id,
            'posts_per_page' => -1,
        ] );

        $data = array_map( function ( $submission ) {
            return [
                'id' => $submission->ID,
                'date' => $submission->post_date,
                'data' => \get_post_meta( $submission->ID, 'submission_data', true ),
            ];
        }, $submissions );

        return new \WP_REST_Response( $data, 200 );
    }

    public function enqueue_frontend_assets(): void {
        \wp_register_style(
            'sofir-forms',
            SOFIR_ASSETS_URL . 'css/forms.css',
            [],
            SOFIR_VERSION
        );

        \wp_register_script(
            'sofir-forms',
            SOFIR_ASSETS_URL . 'js/forms.js',
            [ 'jquery', 'wp-api-fetch' ],
            SOFIR_VERSION,
            true
        );

        \wp_localize_script(
            'sofir-forms',
            'SOFIR_FORMS_DATA',
            [
                'restRoot' => \esc_url_raw( \rest_url() ),
                'nonce' => \wp_create_nonce( 'wp_rest' ),
                'ajaxUrl' => \admin_url( 'admin-ajax.php' ),
            ]
        );
    }

    public function enqueue_admin_assets( string $hook ): void {
        if ( ! \str_contains( $hook, 'sofir-forms' ) ) {
            return;
        }

        \wp_enqueue_style( 'wp-color-picker' );
        \wp_enqueue_script( 'wp-color-picker' );
        
        \wp_enqueue_style(
            'sofir-forms-admin',
            SOFIR_ASSETS_URL . 'css/forms-admin.css',
            [],
            SOFIR_VERSION
        );

        \wp_enqueue_script(
            'sofir-forms-admin',
            SOFIR_ASSETS_URL . 'js/forms-admin.js',
            [ 'jquery', 'jquery-ui-sortable', 'jquery-ui-datepicker', 'wp-color-picker' ],
            SOFIR_VERSION,
            true
        );
    }

    public function register_form_cron(): void {
        if ( ! \wp_next_scheduled( 'sofir_forms_daily_cleanup' ) ) {
            \wp_schedule_event( \time(), 'daily', 'sofir_forms_daily_cleanup' );
        }

        \add_action( 'sofir_forms_daily_cleanup', [ $this, 'cleanup_expired_drafts' ] );
    }

    public function cleanup_expired_drafts(): void {
        $drafts = \get_posts( [
            'post_type' => 'sofir_submission',
            'post_status' => 'draft',
            'posts_per_page' => -1,
            'date_query' => [
                [
                    'before' => '30 days ago',
                ],
            ],
        ] );

        foreach ( $drafts as $draft ) {
            \wp_delete_post( $draft->ID, true );
        }
    }

    public function get_form_templates(): array {
        return [
            'contact' => [
                'name' => \__( 'Contact Form', 'sofir' ),
                'fields' => [
                    [
                        'type' => 'text',
                        'label' => \__( 'Name', 'sofir' ),
                        'required' => '1',
                        'placeholder' => \__( 'Your Name', 'sofir' ),
                    ],
                    [
                        'type' => 'email',
                        'label' => \__( 'Email', 'sofir' ),
                        'required' => '1',
                        'placeholder' => \__( 'Your Email', 'sofir' ),
                    ],
                    [
                        'type' => 'textarea',
                        'label' => \__( 'Message', 'sofir' ),
                        'required' => '1',
                        'placeholder' => \__( 'Your Message', 'sofir' ),
                    ],
                ],
            ],
            'registration' => [
                'name' => \__( 'Registration Form', 'sofir' ),
                'fields' => [
                    [
                        'type' => 'text',
                        'label' => \__( 'Full Name', 'sofir' ),
                        'required' => '1',
                    ],
                    [
                        'type' => 'email',
                        'label' => \__( 'Email Address', 'sofir' ),
                        'required' => '1',
                    ],
                    [
                        'type' => 'tel',
                        'label' => \__( 'Phone Number', 'sofir' ),
                        'required' => '1',
                    ],
                    [
                        'type' => 'date',
                        'label' => \__( 'Date of Birth', 'sofir' ),
                        'required' => '1',
                    ],
                ],
            ],
            'survey' => [
                'name' => \__( 'Survey Form', 'sofir' ),
                'fields' => [
                    [
                        'type' => 'text',
                        'label' => \__( 'Name', 'sofir' ),
                        'required' => '1',
                    ],
                    [
                        'type' => 'rating',
                        'label' => \__( 'Rate Your Experience', 'sofir' ),
                        'required' => '1',
                    ],
                    [
                        'type' => 'radio',
                        'label' => \__( 'Would you recommend us?', 'sofir' ),
                        'required' => '1',
                        'options' => "Yes\nNo\nMaybe",
                    ],
                    [
                        'type' => 'textarea',
                        'label' => \__( 'Additional Comments', 'sofir' ),
                    ],
                ],
            ],
            'booking' => [
                'name' => \__( 'Booking Form', 'sofir' ),
                'fields' => [
                    [
                        'type' => 'text',
                        'label' => \__( 'Full Name', 'sofir' ),
                        'required' => '1',
                    ],
                    [
                        'type' => 'email',
                        'label' => \__( 'Email', 'sofir' ),
                        'required' => '1',
                    ],
                    [
                        'type' => 'tel',
                        'label' => \__( 'Phone', 'sofir' ),
                        'required' => '1',
                    ],
                    [
                        'type' => 'date',
                        'label' => \__( 'Booking Date', 'sofir' ),
                        'required' => '1',
                    ],
                    [
                        'type' => 'time',
                        'label' => \__( 'Booking Time', 'sofir' ),
                        'required' => '1',
                    ],
                    [
                        'type' => 'number',
                        'label' => \__( 'Number of People', 'sofir' ),
                        'required' => '1',
                    ],
                ],
            ],
            'payment' => [
                'name' => \__( 'Payment Form', 'sofir' ),
                'fields' => [
                    [
                        'type' => 'text',
                        'label' => \__( 'Full Name', 'sofir' ),
                        'required' => '1',
                    ],
                    [
                        'type' => 'email',
                        'label' => \__( 'Email', 'sofir' ),
                        'required' => '1',
                    ],
                    [
                        'type' => 'number',
                        'label' => \__( 'Amount', 'sofir' ),
                        'required' => '1',
                        'placeholder' => '0.00',
                    ],
                    [
                        'type' => 'select',
                        'label' => \__( 'Payment Method', 'sofir' ),
                        'required' => '1',
                        'options' => "Credit Card\nBank Transfer\nPayPal\nStripe",
                    ],
                ],
            ],
        ];
    }

    public function export_submissions_csv( int $form_id ): void {
        if ( ! \current_user_can( 'manage_options' ) ) {
            \wp_die( \esc_html__( 'Unauthorized', 'sofir' ) );
        }

        $submissions = \get_posts( [
            'post_type' => 'sofir_submission',
            'meta_key' => 'form_id',
            'meta_value' => $form_id,
            'posts_per_page' => -1,
        ] );

        if ( empty( $submissions ) ) {
            return;
        }

        $form = \get_post( $form_id );
        $filename = \sanitize_file_name( $form->post_title ) . '-submissions-' . \date( 'Y-m-d-His' ) . '.csv';

        \header( 'Content-Type: text/csv; charset=utf-8' );
        \header( 'Content-Disposition: attachment; filename=' . $filename );
        \header( 'Pragma: no-cache' );
        \header( 'Expires: 0' );

        $output = \fopen( 'php://output', 'w' );

        $first_submission = \get_post_meta( $submissions[0]->ID, 'submission_data', true );
        $headers = [ 'ID', 'Date' ];
        if ( \is_array( $first_submission ) ) {
            $headers = \array_merge( $headers, \array_keys( $first_submission ) );
        }
        $headers[] = 'IP Address';
        $headers[] = 'User Agent';

        \fputcsv( $output, $headers );

        foreach ( $submissions as $submission ) {
            $data = \get_post_meta( $submission->ID, 'submission_data', true );
            $ip = \get_post_meta( $submission->ID, 'submission_ip', true );
            $user_agent = \get_post_meta( $submission->ID, 'submission_user_agent', true );

            $row = [ $submission->ID, $submission->post_date ];
            if ( \is_array( $data ) ) {
                foreach ( $first_submission as $key => $value ) {
                    $row[] = $data[ $key ] ?? '';
                }
            }
            $row[] = $ip;
            $row[] = $user_agent;

            \fputcsv( $output, $row );
        }

        \fclose( $output );
        exit;
    }

    public function duplicate_form( int $form_id ): int {
        $original_form = \get_post( $form_id );

        if ( ! $original_form || 'sofir_form' !== $original_form->post_type ) {
            return 0;
        }

        $new_form_data = [
            'post_title' => $original_form->post_title . ' (Copy)',
            'post_type' => 'sofir_form',
            'post_status' => 'publish',
        ];

        $new_form_id = \wp_insert_post( $new_form_data );

        if ( \is_wp_error( $new_form_id ) ) {
            return 0;
        }

        $fields = \get_post_meta( $form_id, 'sofir_form_fields', true );
        $settings = \get_post_meta( $form_id, 'sofir_form_settings', true );
        $conditional_logic = \get_post_meta( $form_id, 'sofir_form_conditional_logic', true );
        $styling = \get_post_meta( $form_id, 'sofir_form_styling', true );

        \update_post_meta( $new_form_id, 'sofir_form_fields', $fields );
        \update_post_meta( $new_form_id, 'sofir_form_settings', $settings );
        \update_post_meta( $new_form_id, 'sofir_form_conditional_logic', $conditional_logic );
        \update_post_meta( $new_form_id, 'sofir_form_styling', $styling );

        return $new_form_id;
    }

    public function get_form_analytics( int $form_id ): array {
        $views = (int) \get_post_meta( $form_id, 'sofir_form_views', true );
        $submissions = \get_posts( [
            'post_type' => 'sofir_submission',
            'meta_key' => 'form_id',
            'meta_value' => $form_id,
            'posts_per_page' => -1,
            'fields' => 'ids',
        ] );

        $conversion_rate = $views > 0 ? ( \count( $submissions ) / $views ) * 100 : 0;

        return [
            'views' => $views,
            'submissions' => \count( $submissions ),
            'conversion_rate' => \round( $conversion_rate, 2 ),
        ];
    }

    public function track_form_view( int $form_id ): void {
        $views = (int) \get_post_meta( $form_id, 'sofir_form_views', true );
        \update_post_meta( $form_id, 'sofir_form_views', $views + 1 );
    }

    public function check_spam( array $data ): bool {
        if ( isset( $data['honeypot'] ) && ! empty( $data['honeypot'] ) ) {
            return true;
        }

        $spam_keywords = [ 'viagra', 'casino', 'porn', 'sex', 'cialis', 'levitra' ];
        $content = \implode( ' ', $data );

        foreach ( $spam_keywords as $keyword ) {
            if ( \stripos( $content, $keyword ) !== false ) {
                return true;
            }
        }

        return false;
    }

    public function save_partial_submission(): void {
        \check_ajax_referer( 'sofir_forms', 'nonce' );

        $form_id = isset( $_POST['form_id'] ) ? (int) $_POST['form_id'] : 0;
        $data = $_POST['data'] ?? [];

        if ( ! $form_id ) {
            \wp_send_json_error( [ 'message' => \__( 'Invalid form ID', 'sofir' ) ] );
        }

        $session_id = isset( $_COOKIE['sofir_session_id'] ) ? $_COOKIE['sofir_session_id'] : \wp_generate_password( 32, false );
        
        if ( ! isset( $_COOKIE['sofir_session_id'] ) ) {
            \setcookie( 'sofir_session_id', $session_id, \time() + ( 30 * DAY_IN_SECONDS ), '/' );
        }

        \update_option( 'sofir_partial_' . $session_id . '_' . $form_id, $data );

        \wp_send_json_success( [ 'message' => \__( 'Progress saved', 'sofir' ), 'session_id' => $session_id ] );
    }

    public function load_partial_submission(): void {
        \check_ajax_referer( 'sofir_forms', 'nonce' );

        $form_id = isset( $_POST['form_id'] ) ? (int) $_POST['form_id'] : 0;
        $session_id = isset( $_COOKIE['sofir_session_id'] ) ? $_COOKIE['sofir_session_id'] : '';

        if ( ! $form_id || ! $session_id ) {
            \wp_send_json_error( [ 'message' => \__( 'No saved progress found', 'sofir' ) ] );
        }

        $data = \get_option( 'sofir_partial_' . $session_id . '_' . $form_id, [] );

        if ( empty( $data ) ) {
            \wp_send_json_error( [ 'message' => \__( 'No saved progress found', 'sofir' ) ] );
        }

        \wp_send_json_success( [ 'data' => $data ] );
    }

    public function process_payment(): void {
        \check_ajax_referer( 'sofir_forms', 'nonce' );

        $form_id = isset( $_POST['form_id'] ) ? (int) $_POST['form_id'] : 0;
        $amount = isset( $_POST['amount'] ) ? (float) $_POST['amount'] : 0;
        $gateway = $_POST['gateway'] ?? '';

        if ( ! $form_id || ! $amount || ! $gateway ) {
            \wp_send_json_error( [ 'message' => \__( 'Invalid payment data', 'sofir' ) ] );
        }

        $settings = \get_post_meta( $form_id, 'sofir_form_settings', true );

        switch ( $gateway ) {
            case 'stripe':
                $result = $this->process_stripe_payment( $amount, $settings );
                break;
            case 'paypal':
                $result = $this->process_paypal_payment( $amount, $settings );
                break;
            case 'razorpay':
                $result = $this->process_razorpay_payment( $amount, $settings );
                break;
            default:
                $result = [ 'success' => false, 'message' => \__( 'Invalid gateway', 'sofir' ) ];
        }

        if ( $result['success'] ) {
            \wp_send_json_success( $result );
        } else {
            \wp_send_json_error( $result );
        }
    }

    private function process_stripe_payment( float $amount, array $settings ): array {
        try {
            if ( empty( $settings['stripe_secret_key'] ) ) {
                return [ 'success' => false, 'message' => \__( 'Stripe not configured', 'sofir' ) ];
            }

            $response = \wp_remote_post(
                'https://api.stripe.com/v1/payment_intents',
                [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $settings['stripe_secret_key'],
                        'Content-Type' => 'application/x-www-form-urlencoded',
                    ],
                    'body' => [
                        'amount' => (int) ( $amount * 100 ),
                        'currency' => \strtolower( $settings['payment_currency'] ?? 'usd' ),
                    ],
                ]
            );

            if ( \is_wp_error( $response ) ) {
                return [ 'success' => false, 'message' => $response->get_error_message() ];
            }

            $body = \json_decode( \wp_remote_retrieve_body( $response ), true );

            return [
                'success' => true,
                'client_secret' => $body['client_secret'] ?? '',
                'intent_id' => $body['id'] ?? '',
            ];
        } catch ( \Exception $e ) {
            return [ 'success' => false, 'message' => $e->getMessage() ];
        }
    }

    private function process_paypal_payment( float $amount, array $settings ): array {
        if ( empty( $settings['paypal_email'] ) ) {
            return [ 'success' => false, 'message' => \__( 'PayPal not configured', 'sofir' ) ];
        }

        $sandbox = isset( $settings['paypal_sandbox'] ) && '1' === $settings['paypal_sandbox'];
        $url = $sandbox ? 'https://www.sandbox.paypal.com/cgi-bin/webscr' : 'https://www.paypal.com/cgi-bin/webscr';

        return [
            'success' => true,
            'redirect_url' => \add_query_arg(
                [
                    'cmd' => '_xclick',
                    'business' => $settings['paypal_email'],
                    'amount' => $amount,
                    'currency_code' => $settings['payment_currency'] ?? 'USD',
                    'return' => \home_url( '?paypal_return=1' ),
                    'cancel_return' => \home_url( '?paypal_cancel=1' ),
                ],
                $url
            ),
        ];
    }

    private function process_razorpay_payment( float $amount, array $settings ): array {
        return [ 'success' => false, 'message' => \__( 'Razorpay integration coming soon', 'sofir' ) ];
    }

    public function register_payment_webhooks(): void {
        \add_action( 'init', function() {
            if ( isset( $_GET['sofir_stripe_webhook'] ) ) {
                $this->handle_stripe_webhook();
            }
            if ( isset( $_GET['sofir_paypal_ipn'] ) ) {
                $this->handle_paypal_ipn();
            }
        } );
    }

    private function handle_stripe_webhook(): void {
        $payload = @\file_get_contents( 'php://input' );
        $event = \json_decode( $payload, true );

        if ( isset( $event['type'] ) && 'payment_intent.succeeded' === $event['type'] ) {
            $intent_id = $event['data']['object']['id'] ?? '';
            
            \do_action( 'sofir/form/payment_completed', $intent_id, 'stripe', $event );
        }

        \http_response_code( 200 );
        exit;
    }

    private function handle_paypal_ipn(): void {
        $raw_post_data = \file_get_contents( 'php://input' );
        $raw_post_array = \explode( '&', $raw_post_data );
        $myPost = [];
        foreach ( $raw_post_array as $keyval ) {
            $keyval = \explode( '=', $keyval );
            if ( \count( $keyval ) === 2 ) {
                $myPost[ $keyval[0] ] = \urldecode( $keyval[1] );
            }
        }

        \do_action( 'sofir/form/payment_completed', $myPost['txn_id'] ?? '', 'paypal', $myPost );

        \http_response_code( 200 );
        exit;
    }

    public function create_post_from_submission( int $submission_id, int $form_id, array $data ): void {
        $settings = \get_post_meta( $form_id, 'sofir_form_settings', true );

        if ( empty( $settings['enable_post_creation'] ) || '1' !== $settings['enable_post_creation'] ) {
            return;
        }

        $post_type = $settings['post_type'] ?? 'post';
        $post_status = $settings['post_status'] ?? 'draft';

        $title = $data['Title'] ?? $data['Name'] ?? \__( 'Submission', 'sofir' ) . ' #' . $submission_id;
        $content = '';

        foreach ( $data as $label => $value ) {
            $content .= '<p><strong>' . \esc_html( $label ) . ':</strong> ' . \esc_html( $value ) . '</p>';
        }

        $post_id = \wp_insert_post( [
            'post_title' => \sanitize_text_field( $title ),
            'post_content' => $content,
            'post_type' => $post_type,
            'post_status' => $post_status,
        ] );

        if ( $post_id && ! \is_wp_error( $post_id ) ) {
            \update_post_meta( $submission_id, 'created_post_id', $post_id );
            \update_post_meta( $post_id, 'source_submission_id', $submission_id );
        }
    }

    public function register_user_from_submission( int $submission_id, int $form_id, array $data ): void {
        $settings = \get_post_meta( $form_id, 'sofir_form_settings', true );

        if ( empty( $settings['enable_user_registration'] ) || '1' !== $settings['enable_user_registration'] ) {
            return;
        }

        $username = $data['Username'] ?? $data['Email'] ?? '';
        $email = $data['Email'] ?? '';
        $password = $data['Password'] ?? \wp_generate_password();

        if ( ! $username || ! $email || ! \is_email( $email ) ) {
            return;
        }

        if ( \username_exists( $username ) || \email_exists( $email ) ) {
            return;
        }

        $user_id = \wp_create_user( $username, $password, $email );

        if ( ! \is_wp_error( $user_id ) ) {
            $role = $settings['user_role'] ?? 'subscriber';
            $user = new \WP_User( $user_id );
            $user->set_role( $role );

            if ( isset( $data['First Name'] ) ) {
                \update_user_meta( $user_id, 'first_name', \sanitize_text_field( $data['First Name'] ) );
            }
            if ( isset( $data['Last Name'] ) ) {
                \update_user_meta( $user_id, 'last_name', \sanitize_text_field( $data['Last Name'] ) );
            }

            \update_post_meta( $submission_id, 'created_user_id', $user_id );

            \wp_new_user_notification( $user_id, null, 'both' );
        }
    }

    public function send_webhooks( int $submission_id, int $form_id, array $data ): void {
        $settings = \get_post_meta( $form_id, 'sofir_form_settings', true );

        if ( empty( $settings['webhook_urls'] ) ) {
            return;
        }

        $urls = \explode( "\n", $settings['webhook_urls'] );

        foreach ( $urls as $url ) {
            $url = \trim( $url );
            if ( ! \filter_var( $url, FILTER_VALIDATE_URL ) ) {
                continue;
            }

            \wp_remote_post(
                $url,
                [
                    'body' => \wp_json_encode( [
                        'form_id' => $form_id,
                        'submission_id' => $submission_id,
                        'data' => $data,
                        'timestamp' => \current_time( 'mysql' ),
                    ] ),
                    'headers' => [
                        'Content-Type' => 'application/json',
                    ],
                    'timeout' => 30,
                ]
            );
        }
    }

    public function generate_pdf( int $submission_id ): string {
        $submission_data = \get_post_meta( $submission_id, 'submission_data', true );
        $form_id = \get_post_meta( $submission_id, 'form_id', true );
        $form = \get_post( $form_id );

        if ( ! $submission_data || ! $form ) {
            return '';
        }

        $html = '<html><head><style>';
        $html .= 'body { font-family: Arial, sans-serif; padding: 20px; }';
        $html .= 'h1 { color: #333; border-bottom: 2px solid #0073aa; padding-bottom: 10px; }';
        $html .= 'table { width: 100%; border-collapse: collapse; margin-top: 20px; }';
        $html .= 'table td { padding: 10px; border: 1px solid #ddd; }';
        $html .= 'table td:first-child { background: #f5f5f5; font-weight: bold; width: 30%; }';
        $html .= '</style></head><body>';
        $html .= '<h1>' . \esc_html( $form->post_title ) . '</h1>';
        $html .= '<p><strong>' . \__( 'Submission Date:', 'sofir' ) . '</strong> ' . \get_the_date( 'Y-m-d H:i:s', $submission_id ) . '</p>';
        $html .= '<table>';

        foreach ( $submission_data as $label => $value ) {
            $html .= '<tr><td>' . \esc_html( $label ) . '</td><td>' . \esc_html( $value ) . '</td></tr>';
        }

        $html .= '</table></body></html>';

        $upload_dir = \wp_upload_dir();
        $pdf_dir = $upload_dir['basedir'] . '/sofir-forms-pdfs';

        if ( ! \file_exists( $pdf_dir ) ) {
            \wp_mkdir_p( $pdf_dir );
        }

        $pdf_file = $pdf_dir . '/submission-' . $submission_id . '.html';
        \file_put_contents( $pdf_file, $html );

        return $upload_dir['baseurl'] . '/sofir-forms-pdfs/submission-' . $submission_id . '.html';
    }

    public function calculate_field_value( string $formula, array $field_values ): float {
        $formula = \preg_replace_callback(
            '/\{field_(\d+)\}/',
            function( $matches ) use ( $field_values ) {
                $field_name = 'field_' . $matches[1];
                return $field_values[ $field_name ] ?? 0;
            },
            $formula
        );

        try {
            $result = eval( 'return ' . $formula . ';' );
            return (float) $result;
        } catch ( \Exception $e ) {
            return 0;
        }
    }

    public function check_form_restrictions( int $form_id ): bool {
        $settings = \get_post_meta( $form_id, 'sofir_form_settings', true );

        if ( isset( $settings['require_login'] ) && '1' === $settings['require_login'] && ! \is_user_logged_in() ) {
            return false;
        }

        if ( isset( $settings['enable_scheduling'] ) && '1' === $settings['enable_scheduling'] ) {
            $now = \current_time( 'timestamp' );
            $start = isset( $settings['schedule_start'] ) ? \strtotime( $settings['schedule_start'] ) : 0;
            $end = isset( $settings['schedule_end'] ) ? \strtotime( $settings['schedule_end'] ) : 0;

            if ( ( $start && $now < $start ) || ( $end && $now > $end ) ) {
                return false;
            }
        }

        if ( isset( $settings['enable_submission_limit'] ) && '1' === $settings['enable_submission_limit'] ) {
            $limit = (int) ( $settings['submission_limit'] ?? 0 );
            $count = \count( \get_posts( [
                'post_type' => 'sofir_submission',
                'meta_key' => 'form_id',
                'meta_value' => $form_id,
                'posts_per_page' => -1,
                'fields' => 'ids',
            ] ) );

            if ( $limit && $count >= $limit ) {
                return false;
            }
        }

        if ( isset( $settings['one_submission_per_user'] ) && '1' === $settings['one_submission_per_user'] && \is_user_logged_in() ) {
            $existing = \get_posts( [
                'post_type' => 'sofir_submission',
                'meta_query' => [
                    [
                        'key' => 'form_id',
                        'value' => $form_id,
                    ],
                    [
                        'key' => 'submission_user_id',
                        'value' => \get_current_user_id(),
                    ],
                ],
                'posts_per_page' => 1,
                'fields' => 'ids',
            ] );

            if ( ! empty( $existing ) ) {
                return false;
            }
        }

        return true;
    }

    public function verify_recaptcha( string $response ): bool {
        $form_id = isset( $_POST['form_id'] ) ? (int) $_POST['form_id'] : 0;
        $settings = \get_post_meta( $form_id, 'sofir_form_settings', true );

        if ( empty( $settings['recaptcha_secret_key'] ) ) {
            return true;
        }

        $verify_response = \wp_remote_post(
            'https://www.google.com/recaptcha/api/siteverify',
            [
                'body' => [
                    'secret' => $settings['recaptcha_secret_key'],
                    'response' => $response,
                ],
            ]
        );

        if ( \is_wp_error( $verify_response ) ) {
            return false;
        }

        $result = \json_decode( \wp_remote_retrieve_body( $verify_response ), true );

        return isset( $result['success'] ) && true === $result['success'];
    }
}
