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
        \add_action( 'add_meta_boxes', [ $this, 'add_submission_meta_boxes' ] );
        \add_action( 'add_meta_boxes', [ $this, 'add_form_meta_boxes' ] );
        \add_action( 'admin_init', [ $this, 'redirect_form_edit' ] );
        \add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_frontend_assets' ] );
        \add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_assets' ] );
        \add_shortcode( 'sofir_form', [ $this, 'render_form' ] );
        
        $this->register_form_cron();
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
                                        <option value="rating">Rating (Star)</option>
                                        <option value="hidden">Hidden Field</option>
                                        <option value="html">HTML Block</option>
                                        <option value="section">Section Break</option>
                                        <option value="signature">Signature</option>
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
                            <option value="rating" <?php \selected( $field['type'] ?? '', 'rating' ); ?>>Rating (Star)</option>
                            <option value="hidden" <?php \selected( $field['type'] ?? '', 'hidden' ); ?>>Hidden Field</option>
                            <option value="html" <?php \selected( $field['type'] ?? '', 'html' ); ?>>HTML Block</option>
                            <option value="section" <?php \selected( $field['type'] ?? '', 'section' ); ?>>Section Break</option>
                            <option value="signature" <?php \selected( $field['type'] ?? '', 'signature' ); ?>>Signature</option>
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

        if ( ! empty( $settings['notification_email'] ) ) {
            $to = $settings['notification_email'];
            $subject = \sprintf( \__( 'New form submission: %s', 'sofir' ), $form->post_title );
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

        \do_action( 'sofir/form/submitted', $submission_id, $form_id, $submission_data );

        \wp_redirect( \add_query_arg( 'form_submitted', '1', \wp_get_referer() ) );
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
}
