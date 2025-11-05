<?php
namespace Sofir\Webhooks;

/**
 * Bit Integration Compatibility
 * 
 * Provides integration with Bit Integration plugin for advanced automation workflows.
 * 
 * This class registers SOFIR as a trigger source in Bit Integration, allowing
 * users to create automated workflows based on SOFIR events.
 * 
 * @package Sofir\Webhooks
 */
class BitIntegration {
    private static ?BitIntegration $instance = null;

    public static function instance(): BitIntegration {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function __construct() {}

    public function boot(): void {
        \add_filter( 'btcbi_trigger', [ $this, 'register_triggers' ] );
        \add_filter( 'btcbi_action', [ $this, 'register_actions' ] );
        
        \add_action( 'user_register', [ $this, 'handle_user_register' ], 999 );
        \add_action( 'profile_update', [ $this, 'handle_user_update' ], 999, 2 );
        \add_action( 'wp_login', [ $this, 'handle_user_login' ], 999, 2 );
        \add_action( 'sofir/payment/status_changed', [ $this, 'handle_payment_status_changed' ], 999, 2 );
        \add_action( 'sofir/form/submission', [ $this, 'handle_form_submission' ], 999, 2 );
        \add_action( 'publish_post', [ $this, 'handle_post_publish' ], 999, 2 );
        \add_action( 'comment_post', [ $this, 'handle_comment_post' ], 999, 3 );
        \add_action( 'sofir/membership/changed', [ $this, 'handle_membership_changed' ], 999, 2 );
        \add_action( 'sofir/appointment/created', [ $this, 'handle_appointment_created' ], 999 );
        \add_action( 'sofir/appointment/updated', [ $this, 'handle_appointment_updated' ], 999, 2 );
    }

    public function register_triggers( array $triggers ): array {
        if ( ! isset( $triggers['sofir'] ) ) {
            $triggers['sofir'] = [];
        }

        $triggers['sofir'] = [
            'name' => 'SOFIR',
            'type' => 'trigger',
            'is_active' => true,
            'activation_url' => '',
            'author' => 'SOFIR Team',
            'description' => \__( 'SOFIR provides comprehensive triggers for users, payments, forms, appointments, and more', 'sofir' ),
            'doc_link' => '',
            'category' => 'CRM',
            'icon' => SOFIR_PLUGIN_URL . 'assets/images/sofir-icon.svg',
            'triggers' => [
                'user_register' => [
                    'label' => \__( 'User Registered', 'sofir' ),
                    'action' => 'user_register',
                    'fields' => [
                        [
                            'key' => 'user_id',
                            'label' => \__( 'User ID', 'sofir' ),
                            'required' => false,
                        ],
                        [
                            'key' => 'user_login',
                            'label' => \__( 'Username', 'sofir' ),
                            'required' => false,
                        ],
                        [
                            'key' => 'user_email',
                            'label' => \__( 'Email', 'sofir' ),
                            'required' => false,
                        ],
                        [
                            'key' => 'display_name',
                            'label' => \__( 'Display Name', 'sofir' ),
                            'required' => false,
                        ],
                        [
                            'key' => 'user_roles',
                            'label' => \__( 'User Roles', 'sofir' ),
                            'required' => false,
                        ],
                        [
                            'key' => 'first_name',
                            'label' => \__( 'First Name', 'sofir' ),
                            'required' => false,
                        ],
                        [
                            'key' => 'last_name',
                            'label' => \__( 'Last Name', 'sofir' ),
                            'required' => false,
                        ],
                        [
                            'key' => 'phone',
                            'label' => \__( 'Phone Number', 'sofir' ),
                            'required' => false,
                        ],
                    ],
                ],
                'user_update' => [
                    'label' => \__( 'User Profile Updated', 'sofir' ),
                    'action' => 'profile_update',
                    'fields' => [
                        [
                            'key' => 'user_id',
                            'label' => \__( 'User ID', 'sofir' ),
                            'required' => false,
                        ],
                        [
                            'key' => 'user_login',
                            'label' => \__( 'Username', 'sofir' ),
                            'required' => false,
                        ],
                        [
                            'key' => 'user_email',
                            'label' => \__( 'Email', 'sofir' ),
                            'required' => false,
                        ],
                        [
                            'key' => 'display_name',
                            'label' => \__( 'Display Name', 'sofir' ),
                            'required' => false,
                        ],
                        [
                            'key' => 'first_name',
                            'label' => \__( 'First Name', 'sofir' ),
                            'required' => false,
                        ],
                        [
                            'key' => 'last_name',
                            'label' => \__( 'Last Name', 'sofir' ),
                            'required' => false,
                        ],
                    ],
                ],
                'user_login' => [
                    'label' => \__( 'User Logged In', 'sofir' ),
                    'action' => 'wp_login',
                    'fields' => [
                        [
                            'key' => 'user_id',
                            'label' => \__( 'User ID', 'sofir' ),
                            'required' => false,
                        ],
                        [
                            'key' => 'user_login',
                            'label' => \__( 'Username', 'sofir' ),
                            'required' => false,
                        ],
                        [
                            'key' => 'user_email',
                            'label' => \__( 'Email', 'sofir' ),
                            'required' => false,
                        ],
                        [
                            'key' => 'login_time',
                            'label' => \__( 'Login Time', 'sofir' ),
                            'required' => false,
                        ],
                    ],
                ],
                'payment_completed' => [
                    'label' => \__( 'Payment Completed', 'sofir' ),
                    'action' => 'sofir/payment/status_changed',
                    'fields' => [
                        [
                            'key' => 'transaction_id',
                            'label' => \__( 'Transaction ID', 'sofir' ),
                            'required' => false,
                        ],
                        [
                            'key' => 'gateway',
                            'label' => \__( 'Payment Gateway', 'sofir' ),
                            'required' => false,
                        ],
                        [
                            'key' => 'amount',
                            'label' => \__( 'Amount', 'sofir' ),
                            'required' => false,
                        ],
                        [
                            'key' => 'item_name',
                            'label' => \__( 'Item Name', 'sofir' ),
                            'required' => false,
                        ],
                        [
                            'key' => 'user_id',
                            'label' => \__( 'User ID', 'sofir' ),
                            'required' => false,
                        ],
                        [
                            'key' => 'status',
                            'label' => \__( 'Payment Status', 'sofir' ),
                            'required' => false,
                        ],
                    ],
                ],
                'form_submission' => [
                    'label' => \__( 'Form Submitted', 'sofir' ),
                    'action' => 'sofir/form/submission',
                    'fields' => [
                        [
                            'key' => 'form_id',
                            'label' => \__( 'Form ID', 'sofir' ),
                            'required' => false,
                        ],
                        [
                            'key' => 'form_data',
                            'label' => \__( 'Form Data', 'sofir' ),
                            'required' => false,
                        ],
                    ],
                ],
                'post_publish' => [
                    'label' => \__( 'Post Published', 'sofir' ),
                    'action' => 'publish_post',
                    'fields' => [
                        [
                            'key' => 'post_id',
                            'label' => \__( 'Post ID', 'sofir' ),
                            'required' => false,
                        ],
                        [
                            'key' => 'post_title',
                            'label' => \__( 'Post Title', 'sofir' ),
                            'required' => false,
                        ],
                        [
                            'key' => 'post_type',
                            'label' => \__( 'Post Type', 'sofir' ),
                            'required' => false,
                        ],
                        [
                            'key' => 'post_author',
                            'label' => \__( 'Author ID', 'sofir' ),
                            'required' => false,
                        ],
                        [
                            'key' => 'permalink',
                            'label' => \__( 'Permalink', 'sofir' ),
                            'required' => false,
                        ],
                    ],
                ],
                'comment_post' => [
                    'label' => \__( 'Comment Posted', 'sofir' ),
                    'action' => 'comment_post',
                    'fields' => [
                        [
                            'key' => 'comment_id',
                            'label' => \__( 'Comment ID', 'sofir' ),
                            'required' => false,
                        ],
                        [
                            'key' => 'post_id',
                            'label' => \__( 'Post ID', 'sofir' ),
                            'required' => false,
                        ],
                        [
                            'key' => 'comment_author',
                            'label' => \__( 'Comment Author', 'sofir' ),
                            'required' => false,
                        ],
                        [
                            'key' => 'comment_author_email',
                            'label' => \__( 'Author Email', 'sofir' ),
                            'required' => false,
                        ],
                        [
                            'key' => 'comment_content',
                            'label' => \__( 'Comment Content', 'sofir' ),
                            'required' => false,
                        ],
                    ],
                ],
                'membership_changed' => [
                    'label' => \__( 'Membership Changed', 'sofir' ),
                    'action' => 'sofir/membership/changed',
                    'fields' => [
                        [
                            'key' => 'user_id',
                            'label' => \__( 'User ID', 'sofir' ),
                            'required' => false,
                        ],
                        [
                            'key' => 'old_plan',
                            'label' => \__( 'Old Plan', 'sofir' ),
                            'required' => false,
                        ],
                        [
                            'key' => 'new_plan',
                            'label' => \__( 'New Plan', 'sofir' ),
                            'required' => false,
                        ],
                    ],
                ],
                'appointment_created' => [
                    'label' => \__( 'Appointment Created', 'sofir' ),
                    'action' => 'sofir/appointment/created',
                    'fields' => [
                        [
                            'key' => 'appointment_id',
                            'label' => \__( 'Appointment ID', 'sofir' ),
                            'required' => false,
                        ],
                        [
                            'key' => 'appointment_datetime',
                            'label' => \__( 'Appointment Date/Time', 'sofir' ),
                            'required' => false,
                        ],
                        [
                            'key' => 'appointment_duration',
                            'label' => \__( 'Duration (minutes)', 'sofir' ),
                            'required' => false,
                        ],
                        [
                            'key' => 'appointment_status',
                            'label' => \__( 'Status', 'sofir' ),
                            'required' => false,
                        ],
                        [
                            'key' => 'appointment_provider',
                            'label' => \__( 'Provider', 'sofir' ),
                            'required' => false,
                        ],
                        [
                            'key' => 'appointment_client',
                            'label' => \__( 'Client', 'sofir' ),
                            'required' => false,
                        ],
                    ],
                ],
                'appointment_updated' => [
                    'label' => \__( 'Appointment Updated', 'sofir' ),
                    'action' => 'sofir/appointment/updated',
                    'fields' => [
                        [
                            'key' => 'appointment_id',
                            'label' => \__( 'Appointment ID', 'sofir' ),
                            'required' => false,
                        ],
                        [
                            'key' => 'appointment_datetime',
                            'label' => \__( 'Appointment Date/Time', 'sofir' ),
                            'required' => false,
                        ],
                        [
                            'key' => 'appointment_status',
                            'label' => \__( 'Status', 'sofir' ),
                            'required' => false,
                        ],
                        [
                            'key' => 'old_status',
                            'label' => \__( 'Old Status', 'sofir' ),
                            'required' => false,
                        ],
                    ],
                ],
            ],
        ];

        return $triggers;
    }

    public function register_actions( array $actions ): array {
        if ( ! isset( $actions['sofir'] ) ) {
            $actions['sofir'] = [];
        }

        $actions['sofir'] = [
            'name' => 'SOFIR',
            'type' => 'action',
            'is_active' => true,
            'activation_url' => '',
            'author' => 'SOFIR Team',
            'description' => \__( 'SOFIR provides actions to create/update users, posts, and more', 'sofir' ),
            'doc_link' => '',
            'category' => 'CRM',
            'icon' => SOFIR_PLUGIN_URL . 'assets/images/sofir-icon.svg',
            'actions' => [
                'create_user' => [
                    'label' => \__( 'Create User', 'sofir' ),
                    'action' => 'sofir_create_user',
                    'fields' => [
                        [
                            'key' => 'user_login',
                            'label' => \__( 'Username', 'sofir' ),
                            'required' => true,
                        ],
                        [
                            'key' => 'user_email',
                            'label' => \__( 'Email', 'sofir' ),
                            'required' => true,
                        ],
                        [
                            'key' => 'user_pass',
                            'label' => \__( 'Password', 'sofir' ),
                            'required' => false,
                        ],
                        [
                            'key' => 'display_name',
                            'label' => \__( 'Display Name', 'sofir' ),
                            'required' => false,
                        ],
                        [
                            'key' => 'first_name',
                            'label' => \__( 'First Name', 'sofir' ),
                            'required' => false,
                        ],
                        [
                            'key' => 'last_name',
                            'label' => \__( 'Last Name', 'sofir' ),
                            'required' => false,
                        ],
                        [
                            'key' => 'phone',
                            'label' => \__( 'Phone Number', 'sofir' ),
                            'required' => false,
                        ],
                    ],
                ],
                'update_user' => [
                    'label' => \__( 'Update User', 'sofir' ),
                    'action' => 'sofir_update_user',
                    'fields' => [
                        [
                            'key' => 'user_id',
                            'label' => \__( 'User ID', 'sofir' ),
                            'required' => true,
                        ],
                        [
                            'key' => 'user_email',
                            'label' => \__( 'Email', 'sofir' ),
                            'required' => false,
                        ],
                        [
                            'key' => 'display_name',
                            'label' => \__( 'Display Name', 'sofir' ),
                            'required' => false,
                        ],
                        [
                            'key' => 'first_name',
                            'label' => \__( 'First Name', 'sofir' ),
                            'required' => false,
                        ],
                        [
                            'key' => 'last_name',
                            'label' => \__( 'Last Name', 'sofir' ),
                            'required' => false,
                        ],
                    ],
                ],
                'create_post' => [
                    'label' => \__( 'Create Post', 'sofir' ),
                    'action' => 'sofir_create_post',
                    'fields' => [
                        [
                            'key' => 'post_title',
                            'label' => \__( 'Post Title', 'sofir' ),
                            'required' => true,
                        ],
                        [
                            'key' => 'post_content',
                            'label' => \__( 'Post Content', 'sofir' ),
                            'required' => false,
                        ],
                        [
                            'key' => 'post_type',
                            'label' => \__( 'Post Type', 'sofir' ),
                            'required' => false,
                        ],
                        [
                            'key' => 'post_status',
                            'label' => \__( 'Post Status', 'sofir' ),
                            'required' => false,
                        ],
                    ],
                ],
            ],
        ];

        return $actions;
    }

    public function handle_user_register( int $user_id ): void {
        if ( ! $this->is_bit_integration_active() ) {
            return;
        }

        $user = \get_userdata( $user_id );
        if ( ! $user ) {
            return;
        }

        $data = [
            'user_id' => $user_id,
            'user_login' => $user->user_login,
            'user_email' => $user->user_email,
            'display_name' => $user->display_name,
            'user_roles' => implode( ', ', $user->roles ),
            'first_name' => \get_user_meta( $user_id, 'first_name', true ),
            'last_name' => \get_user_meta( $user_id, 'last_name', true ),
            'phone' => \get_user_meta( $user_id, 'sofir_phone', true ),
        ];

        \do_action( 'btcbi_trigger_execute', 'sofir', 'user_register', $data );
    }

    public function handle_user_update( int $user_id, ?\WP_User $old_user_data ): void {
        if ( ! $this->is_bit_integration_active() ) {
            return;
        }

        $user = \get_userdata( $user_id );
        if ( ! $user ) {
            return;
        }

        $data = [
            'user_id' => $user_id,
            'user_login' => $user->user_login,
            'user_email' => $user->user_email,
            'display_name' => $user->display_name,
            'first_name' => \get_user_meta( $user_id, 'first_name', true ),
            'last_name' => \get_user_meta( $user_id, 'last_name', true ),
        ];

        \do_action( 'btcbi_trigger_execute', 'sofir', 'user_update', $data );
    }

    public function handle_user_login( string $user_login, \WP_User $user ): void {
        if ( ! $this->is_bit_integration_active() ) {
            return;
        }

        $data = [
            'user_id' => $user->ID,
            'user_login' => $user->user_login,
            'user_email' => $user->user_email,
            'login_time' => \current_time( 'mysql' ),
        ];

        \do_action( 'btcbi_trigger_execute', 'sofir', 'user_login', $data );
    }

    public function handle_payment_status_changed( string $transaction_id, string $status ): void {
        if ( ! $this->is_bit_integration_active() || $status !== 'completed' ) {
            return;
        }

        $transactions = \get_option( 'sofir_payment_transactions', [] );
        $transaction = $transactions[ $transaction_id ] ?? null;

        if ( ! $transaction ) {
            return;
        }

        $data = [
            'transaction_id' => $transaction_id,
            'gateway' => $transaction['gateway'] ?? '',
            'amount' => $transaction['amount'] ?? 0,
            'item_name' => $transaction['item_name'] ?? '',
            'user_id' => $transaction['user_id'] ?? 0,
            'status' => $status,
        ];

        \do_action( 'btcbi_trigger_execute', 'sofir', 'payment_completed', $data );
    }

    public function handle_form_submission( string $form_id, array $form_data ): void {
        if ( ! $this->is_bit_integration_active() ) {
            return;
        }

        $data = [
            'form_id' => $form_id,
            'form_data' => $form_data,
        ];

        \do_action( 'btcbi_trigger_execute', 'sofir', 'form_submission', $data );
    }

    public function handle_post_publish( int $post_id, \WP_Post $post ): void {
        if ( ! $this->is_bit_integration_active() ) {
            return;
        }

        $data = [
            'post_id' => $post_id,
            'post_title' => $post->post_title,
            'post_type' => $post->post_type,
            'post_author' => $post->post_author,
            'permalink' => \get_permalink( $post_id ),
        ];

        \do_action( 'btcbi_trigger_execute', 'sofir', 'post_publish', $data );
    }

    public function handle_comment_post( int $comment_id, int $comment_approved, array $commentdata ): void {
        if ( ! $this->is_bit_integration_active() ) {
            return;
        }

        $data = [
            'comment_id' => $comment_id,
            'post_id' => $commentdata['comment_post_ID'],
            'comment_author' => $commentdata['comment_author'],
            'comment_author_email' => $commentdata['comment_author_email'],
            'comment_content' => $commentdata['comment_content'],
        ];

        \do_action( 'btcbi_trigger_execute', 'sofir', 'comment_post', $data );
    }

    public function handle_membership_changed( int $user_id, array $membership_data ): void {
        if ( ! $this->is_bit_integration_active() ) {
            return;
        }

        $data = [
            'user_id' => $user_id,
            'old_plan' => $membership_data['old_plan'] ?? '',
            'new_plan' => $membership_data['new_plan'] ?? '',
        ];

        \do_action( 'btcbi_trigger_execute', 'sofir', 'membership_changed', $data );
    }

    public function handle_appointment_created( int $appointment_id ): void {
        if ( ! $this->is_bit_integration_active() ) {
            return;
        }

        $data = [
            'appointment_id' => $appointment_id,
            'appointment_datetime' => \get_post_meta( $appointment_id, 'sofir_appointment_datetime', true ),
            'appointment_duration' => \get_post_meta( $appointment_id, 'sofir_appointment_duration', true ),
            'appointment_status' => \get_post_meta( $appointment_id, 'sofir_appointment_status', true ),
            'appointment_provider' => \get_post_meta( $appointment_id, 'sofir_appointment_provider', true ),
            'appointment_client' => \get_post_meta( $appointment_id, 'sofir_appointment_client', true ),
        ];

        \do_action( 'btcbi_trigger_execute', 'sofir', 'appointment_created', $data );
    }

    public function handle_appointment_updated( int $appointment_id, array $old_data ): void {
        if ( ! $this->is_bit_integration_active() ) {
            return;
        }

        $data = [
            'appointment_id' => $appointment_id,
            'appointment_datetime' => \get_post_meta( $appointment_id, 'sofir_appointment_datetime', true ),
            'appointment_status' => \get_post_meta( $appointment_id, 'sofir_appointment_status', true ),
            'old_status' => $old_data['status'] ?? '',
        ];

        \do_action( 'btcbi_trigger_execute', 'sofir', 'appointment_updated', $data );
    }

    private function is_bit_integration_active(): bool {
        return \function_exists( 'btcbi_trigger_execute' ) || \defined( 'BTCBI_PLUGIN_VERSION' );
    }
}
