<?php
namespace Sofir\Elementor\Widgets;

use Sofir\Elementor\BaseWidget;

class Filmstrip_Gallery extends BaseWidget {
    public function get_categories() {
        return [ 'sofir' ];
    }

    public function get_name() {
        return 'sofir-filmstrip-gallery';
    }

    public function get_title() {
        return \esc_html__( 'Filmstrip Gallery', 'sofir' );
    }

    public function get_icon() {
        return 'eicon-carousel';
    }

    protected function register_controls() {
        $this->start_controls_section(
            'section_content',
            [
                'label' => \esc_html__( 'Content', 'sofir' ),
            ]
        );

        $this->add_control(
            'gallery_images',
            [
                'label' => \esc_html__( 'Add Images', 'sofir' ),
                'type' => \Elementor\Controls_Manager::GALLERY,
                'default' => [],
            ]
        );

        $this->add_control(
            'style',
            [
                'label' => \esc_html__( 'Style', 'sofir' ),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'filmstrip',
                'options' => [
                    'filmstrip' => \esc_html__( 'Filmstrip', 'sofir' ),
                    'sidescroll' => \esc_html__( 'Side Scroll', 'sofir' ),
                ],
            ]
        );

        $this->add_control(
            'image_size',
            [
                'label' => \esc_html__( 'Image Size', 'sofir' ),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'medium_large',
                'options' => [
                    'medium' => \esc_html__( 'Medium', 'sofir' ),
                    'medium_large' => \esc_html__( 'Medium Large', 'sofir' ),
                    'large' => \esc_html__( 'Large', 'sofir' ),
                    'full' => \esc_html__( 'Full', 'sofir' ),
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_carousel_settings',
            [
                'label' => \esc_html__( 'Carousel Settings', 'sofir' ),
            ]
        );

        $this->add_control(
            'items_to_show',
            [
                'label' => \esc_html__( 'Items to Show', 'sofir' ),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 4,
                'min' => 1,
                'max' => 10,
            ]
        );

        $this->add_control(
            'items_to_scroll',
            [
                'label' => \esc_html__( 'Items to Scroll', 'sofir' ),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 1,
                'min' => 1,
                'max' => 10,
            ]
        );

        $this->add_control(
            'autoplay',
            [
                'label' => \esc_html__( 'Autoplay', 'sofir' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'autoplay_speed',
            [
                'label' => \esc_html__( 'Autoplay Speed (ms)', 'sofir' ),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 3000,
                'condition' => [
                    'autoplay' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'scroll_speed',
            [
                'label' => \esc_html__( 'Scroll Speed (ms)', 'sofir' ),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 500,
                'min' => 100,
                'max' => 2000,
            ]
        );

        $this->add_control(
            'show_navigation',
            [
                'label' => \esc_html__( 'Show Navigation', 'sofir' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'loop',
            [
                'label' => \esc_html__( 'Loop', 'sofir' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'pause_on_hover',
            [
                'label' => \esc_html__( 'Pause on Hover', 'sofir' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'enable_lightbox',
            [
                'label' => \esc_html__( 'Enable Lightbox', 'sofir' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_captions',
            [
                'label' => \esc_html__( 'Show Captions', 'sofir' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'no',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_responsive',
            [
                'label' => \esc_html__( 'Responsive', 'sofir' ),
            ]
        );

        $this->add_control(
            'tablet_items',
            [
                'label' => \esc_html__( 'Tablet Items', 'sofir' ),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 3,
                'min' => 1,
                'max' => 8,
            ]
        );

        $this->add_control(
            'mobile_items',
            [
                'label' => \esc_html__( 'Mobile Items', 'sofir' ),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 2,
                'min' => 1,
                'max' => 4,
            ]
        );

        $this->end_controls_section();

        $this->add_style_controls();
        $this->add_filmstrip_style_controls();
    }

    protected function add_filmstrip_style_controls(): void {
        $this->start_controls_section(
            'section_filmstrip_style',
            [
                'label' => \esc_html__( 'Filmstrip Style', 'sofir' ),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'item_height',
            [
                'label' => \esc_html__( 'Item Height', 'sofir' ),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'range' => [
                    'px' => [
                        'min' => 100,
                        'max' => 800,
                    ],
                ],
                'default' => [
                    'size' => 300,
                ],
                'selectors' => [
                    '{{WRAPPER}} .sofir-filmstrip-item' => 'height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'item_gap',
            [
                'label' => \esc_html__( 'Item Gap', 'sofir' ),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 50,
                    ],
                ],
                'default' => [
                    'size' => 10,
                ],
                'selectors' => [
                    '{{WRAPPER}} .sofir-filmstrip-item' => 'margin-right: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'border_radius',
            [
                'label' => \esc_html__( 'Border Radius', 'sofir' ),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .sofir-filmstrip-item img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'item_border',
                'selector' => '{{WRAPPER}} .sofir-filmstrip-item',
            ]
        );

        $this->add_control(
            'filmstrip_effect',
            [
                'label' => \esc_html__( 'Filmstrip Effect', 'sofir' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
                'description' => \esc_html__( 'Add filmstrip perforation effect', 'sofir' ),
                'condition' => [
                    'style' => 'filmstrip',
                ],
            ]
        );

        $this->add_control(
            'navigation_position',
            [
                'label' => \esc_html__( 'Navigation Position', 'sofir' ),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'center',
                'options' => [
                    'top' => \esc_html__( 'Top', 'sofir' ),
                    'center' => \esc_html__( 'Center', 'sofir' ),
                    'bottom' => \esc_html__( 'Bottom', 'sofir' ),
                ],
                'condition' => [
                    'show_navigation' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'navigation_color',
            [
                'label' => \esc_html__( 'Navigation Color', 'sofir' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .sofir-filmstrip-nav' => 'color: {{VALUE}};',
                ],
                'condition' => [
                    'show_navigation' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'navigation_bg_color',
            [
                'label' => \esc_html__( 'Navigation Background', 'sofir' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => 'rgba(0,0,0,0.5)',
                'selectors' => [
                    '{{WRAPPER}} .sofir-filmstrip-nav' => 'background-color: {{VALUE}};',
                ],
                'condition' => [
                    'show_navigation' => 'yes',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $images = $settings['gallery_images'];

        if ( empty( $images ) ) {
            echo '<p>' . \esc_html__( 'No images selected.', 'sofir' ) . '</p>';
            return;
        }

        $carousel_settings = [
            'itemsToShow' => (int) $settings['items_to_show'],
            'itemsToScroll' => (int) $settings['items_to_scroll'],
            'autoplay' => $settings['autoplay'] === 'yes',
            'autoplaySpeed' => (int) $settings['autoplay_speed'],
            'scrollSpeed' => (int) $settings['scroll_speed'],
            'loop' => $settings['loop'] === 'yes',
            'pauseOnHover' => $settings['pause_on_hover'] === 'yes',
            'tabletItems' => (int) $settings['tablet_items'],
            'mobileItems' => (int) $settings['mobile_items'],
        ];

        $wrapper_class = 'sofir-filmstrip-gallery sofir-filmstrip-style-' . $settings['style'];
        $wrapper_class .= ' sofir-filmstrip-nav-' . $settings['navigation_position'];
        if ( $settings['filmstrip_effect'] === 'yes' && $settings['style'] === 'filmstrip' ) {
            $wrapper_class .= ' sofir-filmstrip-effect';
        }
        ?>
        <div class="<?php echo \esc_attr( $wrapper_class ); ?>" 
             data-settings="<?php echo \esc_attr( \wp_json_encode( $carousel_settings ) ); ?>">
            
            <div class="sofir-filmstrip-container">
                <div class="sofir-filmstrip-track">
                    <?php foreach ( $images as $index => $image ) : ?>
                        <?php
                        $image_url = \wp_get_attachment_image_url( $image['id'], $settings['image_size'] );
                        $image_full_url = \wp_get_attachment_image_url( $image['id'], 'full' );
                        $image_caption = \wp_get_attachment_caption( $image['id'] );
                        $image_alt = \get_post_meta( $image['id'], '_wp_attachment_image_alt', true );
                        ?>
                        <div class="sofir-filmstrip-item" data-index="<?php echo \esc_attr( $index ); ?>">
                            <?php if ( $settings['enable_lightbox'] === 'yes' ) : ?>
                                <a href="<?php echo \esc_url( $image_full_url ); ?>" 
                                   class="sofir-filmstrip-link"
                                   data-caption="<?php echo \esc_attr( $image_caption ); ?>">
                            <?php endif; ?>
                            
                            <img src="<?php echo \esc_url( $image_url ); ?>" 
                                 alt="<?php echo \esc_attr( $image_alt ); ?>"
                                 loading="lazy">
                            
                            <?php if ( $settings['enable_lightbox'] === 'yes' ) : ?>
                                </a>
                            <?php endif; ?>
                            
                            <?php if ( $settings['show_captions'] === 'yes' && $image_caption ) : ?>
                                <div class="sofir-filmstrip-caption">
                                    <?php echo \esc_html( $image_caption ); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <?php if ( $settings['show_navigation'] === 'yes' ) : ?>
                <button class="sofir-filmstrip-nav sofir-filmstrip-prev" aria-label="<?php echo \esc_attr__( 'Previous', 'sofir' ); ?>">
                    <i class="eicon-chevron-left"></i>
                </button>
                <button class="sofir-filmstrip-nav sofir-filmstrip-next" aria-label="<?php echo \esc_attr__( 'Next', 'sofir' ); ?>">
                    <i class="eicon-chevron-right"></i>
                </button>
            <?php endif; ?>
        </div>
        <?php
    }
}
