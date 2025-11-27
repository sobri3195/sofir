<?php
namespace Sofir\Elementor\Widgets;

use Sofir\Elementor\BaseWidget;

class Restaurant_Order_Form extends BaseWidget {
    public function get_categories() {
        return [ 'sofir-booking' ];
    }

    public function get_name() {
        return 'sofir-restaurant-order-form';
    }

    public function get_title() {
        return \esc_html__( 'Restaurant Order Form (Dine-in)', 'sofir' );
    }

    public function get_icon() {
        return 'eicon-form-horizontal';
    }

    protected function register_controls() {
        $this->start_controls_section(
            'section_content',
            [
                'label' => \esc_html__( 'Content', 'sofir' ),
            ]
        );

        $this->add_control(
            'show_menu_selection',
            [
                'label' => \esc_html__( 'Show Menu Selection', 'sofir' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_table_number',
            [
                'label' => \esc_html__( 'Show Table Number', 'sofir' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_special_requests',
            [
                'label' => \esc_html__( 'Show Special Requests', 'sofir' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'button_text',
            [
                'label' => \esc_html__( 'Button Text', 'sofir' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => \esc_html__( 'Place Order', 'sofir' ),
            ]
        );

        $this->end_controls_section();

        $this->add_style_controls();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();

        echo '<div class="sofir-restaurant-order-form-widget">';
        echo '<form class="restaurant-order-form" data-order-type="dine-in">';
        
        \wp_nonce_field( 'sofir_restaurant_order', 'restaurant_order_nonce' );
        
        echo '<div class="form-group">';
        echo '<label for="customer_name">' . \esc_html__( 'Name', 'sofir' ) . ' <span class="required">*</span></label>';
        echo '<input type="text" id="customer_name" name="customer_name" required>';
        echo '</div>';

        if ( $settings['show_table_number'] === 'yes' ) {
            echo '<div class="form-group">';
            echo '<label for="table_number">' . \esc_html__( 'Table Number', 'sofir' ) . ' <span class="required">*</span></label>';
            echo '<input type="text" id="table_number" name="table_number" required>';
            echo '</div>';
        }

        if ( $settings['show_menu_selection'] === 'yes' ) {
            echo '<div class="form-group">';
            echo '<label>' . \esc_html__( 'Menu Items', 'sofir' ) . '</label>';
            echo '<div class="menu-items-list">';
            
            $menu_items = \get_posts( [
                'post_type' => 'menu_item',
                'posts_per_page' => -1,
                'orderby' => 'title',
                'order' => 'ASC',
            ] );

            if ( $menu_items ) {
                foreach ( $menu_items as $item ) {
                    $price = \get_post_meta( $item->ID, 'sofir_menu_price', true );
                    echo '<div class="menu-item">';
                    echo '<label>';
                    echo '<input type="checkbox" name="menu_items[]" value="' . esc_attr( $item->ID ) . '">';
                    echo ' ' . esc_html( $item->post_title );
                    if ( $price ) {
                        echo ' - ' . esc_html( $price );
                    }
                    echo '</label>';
                    echo '<input type="number" name="quantities[' . esc_attr( $item->ID ) . ']" min="1" value="1" class="quantity-input" disabled>';
                    echo '</div>';
                }
            } else {
                echo '<p>' . \esc_html__( 'No menu items available.', 'sofir' ) . '</p>';
            }
            
            echo '</div>';
            echo '</div>';
        }

        if ( $settings['show_special_requests'] === 'yes' ) {
            echo '<div class="form-group">';
            echo '<label for="special_requests">' . \esc_html__( 'Special Requests', 'sofir' ) . '</label>';
            echo '<textarea id="special_requests" name="special_requests" rows="4"></textarea>';
            echo '</div>';
        }

        echo '<div class="form-group">';
        echo '<div class="order-summary">';
        echo '<h4>' . \esc_html__( 'Order Summary', 'sofir' ) . '</h4>';
        echo '<div class="summary-content"></div>';
        echo '<div class="total-price"><strong>' . \esc_html__( 'Total:', 'sofir' ) . '</strong> <span class="total-amount">$0.00</span></div>';
        echo '</div>';
        echo '</div>';

        echo '<div class="form-actions">';
        echo '<button type="submit" class="submit-button">' . esc_html( $settings['button_text'] ) . '</button>';
        echo '</div>';

        echo '<div class="form-message" style="display: none;"></div>';

        echo '</form>';
        echo '</div>';
    }
}
