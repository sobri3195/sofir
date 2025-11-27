<?php
namespace Sofir\Elementor\Widgets;

use Sofir\Elementor\BaseWidget;

class Restaurant_Delivery_Form extends BaseWidget {
    public function get_categories() {
        return [ 'sofir-booking' ];
    }

    public function get_name() {
        return 'sofir-restaurant-delivery-form';
    }

    public function get_title() {
        return \esc_html__( 'Restaurant Delivery Form', 'sofir' );
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
            'show_delivery_time',
            [
                'label' => \esc_html__( 'Show Delivery Time', 'sofir' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'button_text',
            [
                'label' => \esc_html__( 'Button Text', 'sofir' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => \esc_html__( 'Place Delivery Order', 'sofir' ),
            ]
        );

        $this->end_controls_section();

        $this->add_style_controls();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();

        echo '<div class="sofir-restaurant-delivery-form-widget">';
        echo '<form class="restaurant-delivery-form" data-order-type="delivery">';
        
        \wp_nonce_field( 'sofir_restaurant_order', 'restaurant_order_nonce' );
        
        echo '<div class="form-group">';
        echo '<label for="customer_name">' . \esc_html__( 'Name', 'sofir' ) . ' <span class="required">*</span></label>';
        echo '<input type="text" id="customer_name" name="customer_name" required>';
        echo '</div>';

        echo '<div class="form-group">';
        echo '<label for="customer_phone">' . \esc_html__( 'Phone', 'sofir' ) . ' <span class="required">*</span></label>';
        echo '<input type="tel" id="customer_phone" name="customer_phone" required>';
        echo '</div>';

        echo '<div class="form-group">';
        echo '<label for="customer_email">' . \esc_html__( 'Email', 'sofir' ) . '</label>';
        echo '<input type="email" id="customer_email" name="customer_email">';
        echo '</div>';

        echo '<div class="form-group">';
        echo '<label for="delivery_address">' . \esc_html__( 'Delivery Address', 'sofir' ) . ' <span class="required">*</span></label>';
        echo '<textarea id="delivery_address" name="delivery_address" rows="3" required></textarea>';
        echo '</div>';

        if ( $settings['show_delivery_time'] === 'yes' ) {
            echo '<div class="form-group">';
            echo '<label for="delivery_time">' . \esc_html__( 'Preferred Delivery Time', 'sofir' ) . '</label>';
            echo '<select id="delivery_time" name="delivery_time">';
            echo '<option value="">' . \esc_html__( 'ASAP', 'sofir' ) . '</option>';
            for ( $hour = 10; $hour <= 21; $hour++ ) {
                echo '<option value="' . sprintf( '%02d:00', $hour ) . '">' . sprintf( '%02d:00', $hour ) . '</option>';
                echo '<option value="' . sprintf( '%02d:30', $hour ) . '">' . sprintf( '%02d:30', $hour ) . '</option>';
            }
            echo '</select>';
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

        echo '<div class="form-group">';
        echo '<label for="special_requests">' . \esc_html__( 'Special Requests / Notes', 'sofir' ) . '</label>';
        echo '<textarea id="special_requests" name="special_requests" rows="4"></textarea>';
        echo '</div>';

        echo '<div class="form-group">';
        echo '<div class="order-summary">';
        echo '<h4>' . \esc_html__( 'Order Summary', 'sofir' ) . '</h4>';
        echo '<div class="summary-content"></div>';
        echo '<div class="delivery-fee"><strong>' . \esc_html__( 'Delivery Fee:', 'sofir' ) . '</strong> <span class="fee-amount">$5.00</span></div>';
        echo '<div class="total-price"><strong>' . \esc_html__( 'Total:', 'sofir' ) . '</strong> <span class="total-amount">$5.00</span></div>';
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
