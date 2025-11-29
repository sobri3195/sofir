<?php
namespace Sofir\Elementor\Widgets;

use Sofir\Elementor\BaseWidget;

class Slideshow extends BaseWidget {
    public function get_categories() {
        return [ 'sofir' ];
    }

    public function get_name() {
        return 'sofir-slideshow';
    }

    public function get_title() {
        return \esc_html__( 'Slideshow', 'sofir' );
    }

    public function get_icon() {
        return 'eicon-slideshow';
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
            'image_size',
            [
                'label' => \esc_html__( 'Image Size', 'sofir' ),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'large',
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
            'section_slideshow_settings',
            [
                'label' => \esc_html__( 'Slideshow Settings', 'sofir' ),
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
                'min' => 1000,
                'max' => 10000,
                'step' => 500,
                'condition' => [
                    'autoplay' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'transition_speed',
            [
                'label' => \esc_html__( 'Transition Speed (ms)', 'sofir' ),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 500,
                'min' => 100,
                'max' => 2000,
                'step' => 100,
            ]
        );

        $this->add_control(
            'transition_effect',
            [
                'label' => \esc_html__( 'Transition Effect', 'sofir' ),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'fade',
                'options' => [
                    'fade' => \esc_html__( 'Fade', 'sofir' ),
                    'slide' => \esc_html__( 'Slide', 'sofir' ),
                    'zoom' => \esc_html__( 'Zoom', 'sofir' ),
                    'flip' => \esc_html__( 'Flip', 'sofir' ),
                ],
            ]
        );

        $this->add_control(
            'show_navigation',
            [
                'label' => \esc_html__( 'Show Navigation Arrows', 'sofir' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_pagination',
            [
                'label' => \esc_html__( 'Show Pagination Dots', 'sofir' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'pagination_type',
            [
                'label' => \esc_html__( 'Pagination Type', 'sofir' ),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'dots',
                'options' => [
                    'dots' => \esc_html__( 'Dots', 'sofir' ),
                    'thumbnails' => \esc_html__( 'Thumbnails', 'sofir' ),
                    'numbers' => \esc_html__( 'Numbers', 'sofir' ),
                ],
                'condition' => [
                    'show_pagination' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'show_captions',
            [
                'label' => \esc_html__( 'Show Captions', 'sofir' ),
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
            'loop',
            [
                'label' => \esc_html__( 'Loop', 'sofir' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'enable_keyboard',
            [
                'label' => \esc_html__( 'Keyboard Navigation', 'sofir' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'enable_swipe',
            [
                'label' => \esc_html__( 'Touch Swipe', 'sofir' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->end_controls_section();

        $this->add_style_controls();
        $this->add_slideshow_style_controls();
    }

    protected function add_slideshow_style_controls(): void {
        $this->start_controls_section(
            'section_slideshow_style',
            [
                'label' => \esc_html__( 'Slideshow Style', 'sofir' ),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'slideshow_height',
            [
                'label' => \esc_html__( 'Height', 'sofir' ),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => [ 'px', 'vh' ],
                'range' => [
                    'px' => [
                        'min' => 200,
                        'max' => 1000,
                    ],
                    'vh' => [
                        'min' => 20,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'size' => 600,
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .sofir-slideshow' => 'height: {{SIZE}}{{UNIT}};',
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
                    '{{WRAPPER}} .sofir-slideshow-nav' => 'color: {{VALUE}};',
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
                    '{{WRAPPER}} .sofir-slideshow-nav' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'pagination_color',
            [
                'label' => \esc_html__( 'Pagination Color', 'sofir' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .sofir-slideshow-pagination .sofir-pagination-dot' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'pagination_active_color',
            [
                'label' => \esc_html__( 'Pagination Active Color', 'sofir' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#0073aa',
                'selectors' => [
                    '{{WRAPPER}} .sofir-slideshow-pagination .sofir-pagination-dot.active' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_caption_style',
            [
                'label' => \esc_html__( 'Caption Style', 'sofir' ),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_captions' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'caption_color',
            [
                'label' => \esc_html__( 'Text Color', 'sofir' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .sofir-slideshow-caption' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'caption_bg_color',
            [
                'label' => \esc_html__( 'Background Color', 'sofir' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => 'rgba(0,0,0,0.7)',
                'selectors' => [
                    '{{WRAPPER}} .sofir-slideshow-caption' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'caption_typography',
                'selector' => '{{WRAPPER}} .sofir-slideshow-caption',
            ]
        );

        $this->add_responsive_control(
            'caption_padding',
            [
                'label' => \esc_html__( 'Padding', 'sofir' ),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .sofir-slideshow-caption' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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

        $slideshow_settings = [
            'autoplay' => $settings['autoplay'] === 'yes',
            'autoplaySpeed' => (int) $settings['autoplay_speed'],
            'transitionSpeed' => (int) $settings['transition_speed'],
            'transitionEffect' => $settings['transition_effect'],
            'pauseOnHover' => $settings['pause_on_hover'] === 'yes',
            'loop' => $settings['loop'] === 'yes',
            'keyboard' => $settings['enable_keyboard'] === 'yes',
            'swipe' => $settings['enable_swipe'] === 'yes',
        ];

        $wrapper_class = 'sofir-slideshow sofir-slideshow-effect-' . $settings['transition_effect'];
        ?>
        <div class="<?php echo \esc_attr( $wrapper_class ); ?>" 
             data-settings="<?php echo \esc_attr( \wp_json_encode( $slideshow_settings ) ); ?>">
            
            <div class="sofir-slideshow-container">
                <?php foreach ( $images as $index => $image ) : ?>
                    <?php
                    $image_url = \wp_get_attachment_image_url( $image['id'], $settings['image_size'] );
                    $image_caption = \wp_get_attachment_caption( $image['id'] );
                    $is_active = $index === 0 ? 'active' : '';
                    ?>
                    <div class="sofir-slideshow-item <?php echo \esc_attr( $is_active ); ?>" 
                         data-index="<?php echo \esc_attr( $index ); ?>">
                        <img src="<?php echo \esc_url( $image_url ); ?>" 
                             alt="<?php echo \esc_attr( \get_post_meta( $image['id'], '_wp_attachment_image_alt', true ) ); ?>">
                        
                        <?php if ( $settings['show_captions'] === 'yes' && $image_caption ) : ?>
                            <div class="sofir-slideshow-caption">
                                <?php echo \esc_html( $image_caption ); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php if ( $settings['show_navigation'] === 'yes' ) : ?>
                <button class="sofir-slideshow-nav sofir-slideshow-prev" aria-label="<?php echo \esc_attr__( 'Previous', 'sofir' ); ?>">
                    <i class="eicon-chevron-left"></i>
                </button>
                <button class="sofir-slideshow-nav sofir-slideshow-next" aria-label="<?php echo \esc_attr__( 'Next', 'sofir' ); ?>">
                    <i class="eicon-chevron-right"></i>
                </button>
            <?php endif; ?>

            <?php if ( $settings['show_pagination'] === 'yes' ) : ?>
                <div class="sofir-slideshow-pagination sofir-pagination-<?php echo \esc_attr( $settings['pagination_type'] ); ?>">
                    <?php foreach ( $images as $index => $image ) : ?>
                        <?php if ( $settings['pagination_type'] === 'thumbnails' ) : ?>
                            <?php $thumb_url = \wp_get_attachment_image_url( $image['id'], 'thumbnail' ); ?>
                            <button class="sofir-pagination-thumbnail <?php echo $index === 0 ? 'active' : ''; ?>" 
                                    data-index="<?php echo \esc_attr( $index ); ?>"
                                    style="background-image: url('<?php echo \esc_url( $thumb_url ); ?>');">
                            </button>
                        <?php elseif ( $settings['pagination_type'] === 'numbers' ) : ?>
                            <button class="sofir-pagination-number <?php echo $index === 0 ? 'active' : ''; ?>" 
                                    data-index="<?php echo \esc_attr( $index ); ?>">
                                <?php echo \esc_html( $index + 1 ); ?>
                            </button>
                        <?php else : ?>
                            <button class="sofir-pagination-dot <?php echo $index === 0 ? 'active' : ''; ?>" 
                                    data-index="<?php echo \esc_attr( $index ); ?>"
                                    aria-label="<?php echo \esc_attr( sprintf( __( 'Go to slide %d', 'sofir' ), $index + 1 ) ); ?>">
                            </button>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        <?php
    }
}
