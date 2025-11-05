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
        \add_action( 'init', [ $this, 'register_submission_cpt' ] );
        \add_action( 'admin_menu', [ $this, 'add_forms_menu' ] );
        \add_action( 'rest_api_init', [ $this, 'register_rest_routes' ] );
        \add_action( 'admin_post_sofir_submit_form', [ $this, 'handle_form_submission' ] );
        \add_action( 'admin_post_nopriv_sofir_submit_form', [ $this, 'handle_form_submission' ] );
        \add_shortcode( 'sofir_form', [ $this, 'render_form' ] );
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

                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="success_message"><?php \esc_html_e( 'Success Message', 'sofir' ); ?></label>
                        </th>
                        <td>
                            <input type="text" id="success_message" name="success_message" value="<?php echo \esc_attr( $settings['success_message'] ?? 'Thank you for your submission!' ); ?>" class="regular-text" />
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <label for="button_text"><?php \esc_html_e( 'Submit Button Text', 'sofir' ); ?></label>
                        </th>
                        <td>
                            <input type="text" id="button_text" name="button_text" value="<?php echo \esc_attr( $settings['button_text'] ?? 'Submit' ); ?>" class="regular-text" />
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <label for="notification_email"><?php \esc_html_e( 'Notification Email', 'sofir' ); ?></label>
                        </th>
                        <td>
                            <input type="email" id="notification_email" name="notification_email" value="<?php echo \esc_attr( $settings['notification_email'] ?? \get_option( 'admin_email' ) ); ?>" class="regular-text" />
                            <p class="description"><?php \esc_html_e( 'Email address to receive form submissions.', 'sofir' ); ?></p>
                        </td>
                    </tr>
                </table>

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
                                    <select name="form_fields[${fieldIndex}][type]">
                                        <option value="text">Text</option>
                                        <option value="email">Email</option>
                                        <option value="tel">Phone</option>
                                        <option value="number">Number</option>
                                        <option value="textarea">Textarea</option>
                                        <option value="select">Select</option>
                                        <option value="radio">Radio</option>
                                        <option value="checkbox">Checkbox</option>
                                        <option value="date">Date</option>
                                        <option value="time">Time</option>
                                        <option value="file">File Upload</option>
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
                        <select name="form_fields[<?php echo \esc_attr( $index ); ?>][type]">
                            <option value="text" <?php \selected( $field['type'] ?? '', 'text' ); ?>>Text</option>
                            <option value="email" <?php \selected( $field['type'] ?? '', 'email' ); ?>>Email</option>
                            <option value="tel" <?php \selected( $field['type'] ?? '', 'tel' ); ?>>Phone</option>
                            <option value="number" <?php \selected( $field['type'] ?? '', 'number' ); ?>>Number</option>
                            <option value="textarea" <?php \selected( $field['type'] ?? '', 'textarea' ); ?>>Textarea</option>
                            <option value="select" <?php \selected( $field['type'] ?? '', 'select' ); ?>>Select</option>
                            <option value="radio" <?php \selected( $field['type'] ?? '', 'radio' ); ?>>Radio</option>
                            <option value="checkbox" <?php \selected( $field['type'] ?? '', 'checkbox' ); ?>>Checkbox</option>
                            <option value="date" <?php \selected( $field['type'] ?? '', 'date' ); ?>>Date</option>
                            <option value="time" <?php \selected( $field['type'] ?? '', 'time' ); ?>>Time</option>
                            <option value="file" <?php \selected( $field['type'] ?? '', 'file' ); ?>>File Upload</option>
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

        $fields = \get_post_meta( $form_id, 'sofir_form_fields', true ) ?: [];
        $settings = \get_post_meta( $form_id, 'sofir_form_settings', true ) ?: [];

        $submission_data = [];
        foreach ( $fields as $index => $field ) {
            $name = 'field_' . $index;
            $value = $_POST[ $name ] ?? '';
            
            if ( \is_array( $value ) ) {
                $value = \implode( ', ', \array_map( 'sanitize_text_field', $value ) );
            } else {
                $value = \sanitize_text_field( $value );
            }

            $submission_data[ $field['label'] ?? $name ] = $value;
        }

        $submission_id = \wp_insert_post( [
            'post_title' => $form->post_title . ' - ' . \current_time( 'mysql' ),
            'post_type' => 'sofir_submission',
            'post_status' => 'publish',
        ] );

        \update_post_meta( $submission_id, 'form_id', $form_id );
        \update_post_meta( $submission_id, 'submission_data', $submission_data );
        \update_post_meta( $submission_id, 'submission_ip', $_SERVER['REMOTE_ADDR'] ?? '' );
        \update_post_meta( $submission_id, 'submission_user_agent', $_SERVER['HTTP_USER_AGENT'] ?? '' );

        if ( \is_user_logged_in() ) {
            \update_post_meta( $submission_id, 'submission_user_id', \get_current_user_id() );
        }

        if ( ! empty( $settings['notification_email'] ) ) {
            $to = $settings['notification_email'];
            $subject = \sprintf( \__( 'New form submission: %s', 'sofir' ), $form->post_title );
            $message = \__( 'You have received a new form submission:', 'sofir' ) . "\n\n";
            
            foreach ( $submission_data as $label => $value ) {
                $message .= $label . ': ' . $value . "\n";
            }

            \wp_mail( $to, $subject, $message );
        }

        \do_action( 'sofir/form/submitted', $submission_id, $form_id, $submission_data );

        \wp_redirect( \add_query_arg( 'form_submitted', '1', \wp_get_referer() ) );
        exit;
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
}
