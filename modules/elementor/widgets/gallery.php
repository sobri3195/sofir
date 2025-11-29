<?php
namespace Sofir\Elementor\Widgets;

use Sofir\Elementor\BaseWidget;

class Gallery extends BaseWidget {
    public function get_categories() {
        return [ 'sofir' ];
    }

    public function get_name() {
        return 'sofir-gallery';
    }

    public function get_title() {
        return \esc_html__( 'Gallery', 'sofir' );
    }

    public function get_icon() {
        return 'eicon-gallery-grid';
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
            'layout',
            [
                'label' => \esc_html__( 'Layout', 'sofir' ),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'masonry',
                'options' => [
                    'masonry' => \esc_html__( 'Masonry Gallery', 'sofir' ),
                    'mosaic' => \esc_html__( 'Mosaic Gallery', 'sofir' ),
                    'tiled' => \esc_html__( 'Tiled Gallery', 'sofir' ),
                    'thumbnail' => \esc_html__( 'Thumbnail Grid', 'sofir' ),
                    'film' => \esc_html__( 'Film Gallery', 'sofir' ),
                    'blog' => \esc_html__( 'Blog Style', 'sofir' ),
                    'imagebrowser' => \esc_html__( 'Image Browser', 'sofir' ),
                ],
            ]
        );

        $this->add_layout_controls();

        $this->add_control(
            'image_size',
            [
                'label' => \esc_html__( 'Image Size', 'sofir' ),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'medium_large',
                'options' => [
                    'thumbnail' => \esc_html__( 'Thumbnail', 'sofir' ),
                    'medium' => \esc_html__( 'Medium', 'sofir' ),
                    'medium_large' => \esc_html__( 'Medium Large', 'sofir' ),
                    'large' => \esc_html__( 'Large', 'sofir' ),
                    'full' => \esc_html__( 'Full', 'sofir' ),
                ],
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
            'show_caption',
            [
                'label' => \esc_html__( 'Show Caption', 'sofir' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'no',
            ]
        );

        $this->add_control(
            'show_title',
            [
                'label' => \esc_html__( 'Show Title', 'sofir' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'no',
            ]
        );

        $this->add_control(
            'lazy_load',
            [
                'label' => \esc_html__( 'Lazy Load', 'sofir' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->end_controls_section();

        $this->add_lightbox_controls();
        $this->add_style_controls();
        $this->add_image_style_controls();
    }

    protected function add_lightbox_controls(): void {
        $this->start_controls_section(
            'section_lightbox',
            [
                'label' => \esc_html__( 'Lightbox Settings', 'sofir' ),
                'condition' => [
                    'enable_lightbox' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'lightbox_enable_share',
            [
                'label' => \esc_html__( 'Enable Social Sharing', 'sofir' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'lightbox_enable_fullscreen',
            [
                'label' => \esc_html__( 'Enable Fullscreen', 'sofir' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'lightbox_enable_zoom',
            [
                'label' => \esc_html__( 'Enable Zoom', 'sofir' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'lightbox_enable_autoplay',
            [
                'label' => \esc_html__( 'Enable Autoplay', 'sofir' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'no',
            ]
        );

        $this->add_control(
            'lightbox_autoplay_speed',
            [
                'label' => \esc_html__( 'Autoplay Speed (ms)', 'sofir' ),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 3000,
                'condition' => [
                    'lightbox_enable_autoplay' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'lightbox_enable_counter',
            [
                'label' => \esc_html__( 'Show Image Counter', 'sofir' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'lightbox_enable_download',
            [
                'label' => \esc_html__( 'Enable Download', 'sofir' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'no',
            ]
        );

        $this->end_controls_section();
    }

    protected function add_image_style_controls(): void {
        $this->start_controls_section(
            'section_image_style',
            [
                'label' => \esc_html__( 'Image Style', 'sofir' ),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'image_border_radius',
            [
                'label' => \esc_html__( 'Border Radius', 'sofir' ),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .sofir-gallery-item img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'image_border',
                'selector' => '{{WRAPPER}} .sofir-gallery-item img',
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'image_box_shadow',
                'selector' => '{{WRAPPER}} .sofir-gallery-item',
            ]
        );

        $this->add_control(
            'image_hover_effect',
            [
                'label' => \esc_html__( 'Hover Effect', 'sofir' ),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'zoom',
                'options' => [
                    'none' => \esc_html__( 'None', 'sofir' ),
                    'zoom' => \esc_html__( 'Zoom In', 'sofir' ),
                    'zoom-out' => \esc_html__( 'Zoom Out', 'sofir' ),
                    'grayscale' => \esc_html__( 'Grayscale', 'sofir' ),
                    'blur' => \esc_html__( 'Blur', 'sofir' ),
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

        $lightbox_settings = [
            'enable' => $settings['enable_lightbox'] === 'yes',
            'share' => $settings['lightbox_enable_share'] === 'yes',
            'fullscreen' => $settings['lightbox_enable_fullscreen'] === 'yes',
            'zoom' => $settings['lightbox_enable_zoom'] === 'yes',
            'autoplay' => $settings['lightbox_enable_autoplay'] === 'yes',
            'autoplaySpeed' => (int) $settings['lightbox_autoplay_speed'],
            'counter' => $settings['lightbox_enable_counter'] === 'yes',
            'download' => $settings['lightbox_enable_download'] === 'yes',
        ];

        $wrapper_class = 'sofir-gallery sofir-gallery-' . $settings['layout'];
        $wrapper_class .= ' sofir-gallery-columns-' . $settings['columns'];
        $wrapper_class .= ' sofir-gallery-hover-' . $settings['image_hover_effect'];

        ?>
        <div class="<?php echo \esc_attr( $wrapper_class ); ?>" 
             data-lightbox="<?php echo \esc_attr( \wp_json_encode( $lightbox_settings ) ); ?>"
             data-layout="<?php echo \esc_attr( $settings['layout'] ); ?>">
            <?php foreach ( $images as $index => $image ) : ?>
                <?php
                $image_url = \wp_get_attachment_image_url( $image['id'], $settings['image_size'] );
                $image_full_url = \wp_get_attachment_image_url( $image['id'], 'full' );
                $image_alt = \get_post_meta( $image['id'], '_wp_attachment_image_alt', true );
                $image_title = \get_the_title( $image['id'] );
                $image_caption = \wp_get_attachment_caption( $image['id'] );
                ?>
                <div class="sofir-gallery-item">
                    <?php if ( $settings['enable_lightbox'] === 'yes' ) : ?>
                        <a href="<?php echo \esc_url( $image_full_url ); ?>" 
                           class="sofir-gallery-link" 
                           data-caption="<?php echo \esc_attr( $image_caption ); ?>"
                           data-title="<?php echo \esc_attr( $image_title ); ?>"
                           data-index="<?php echo \esc_attr( $index ); ?>">
                    <?php endif; ?>
                    
                    <img src="<?php echo \esc_url( $image_url ); ?>" 
                         alt="<?php echo \esc_attr( $image_alt ); ?>"
                         <?php if ( $settings['lazy_load'] === 'yes' ) : ?>
                             loading="lazy"
                         <?php endif; ?>>
                    
                    <div class="sofir-gallery-overlay">
                        <span class="sofir-gallery-icon">
                            <i class="eicon-zoom-in-bold"></i>
                        </span>
                    </div>
                    
                    <?php if ( $settings['enable_lightbox'] === 'yes' ) : ?>
                        </a>
                    <?php endif; ?>
                    
                    <?php if ( $settings['show_title'] === 'yes' && $image_title ) : ?>
                        <div class="sofir-gallery-title"><?php echo \esc_html( $image_title ); ?></div>
                    <?php endif; ?>
                    
                    <?php if ( $settings['show_caption'] === 'yes' && $image_caption ) : ?>
                        <div class="sofir-gallery-caption"><?php echo \esc_html( $image_caption ); ?></div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
        <?php
    }
}
