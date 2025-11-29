<?php
namespace Sofir\Elementor\Widgets;

use Sofir\Elementor\BaseWidget;

class Album extends BaseWidget {
    public function get_categories() {
        return [ 'sofir' ];
    }

    public function get_name() {
        return 'sofir-album';
    }

    public function get_title() {
        return \esc_html__( 'Album', 'sofir' );
    }

    public function get_icon() {
        return 'eicon-photo-library';
    }

    protected function register_controls() {
        $this->start_controls_section(
            'section_content',
            [
                'label' => \esc_html__( 'Content', 'sofir' ),
            ]
        );

        $repeater = new \Elementor\Repeater();

        $repeater->add_control(
            'album_title',
            [
                'label' => \esc_html__( 'Album Title', 'sofir' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => \esc_html__( 'Album Title', 'sofir' ),
            ]
        );

        $repeater->add_control(
            'album_description',
            [
                'label' => \esc_html__( 'Description', 'sofir' ),
                'type' => \Elementor\Controls_Manager::TEXTAREA,
                'default' => '',
            ]
        );

        $repeater->add_control(
            'album_images',
            [
                'label' => \esc_html__( 'Images', 'sofir' ),
                'type' => \Elementor\Controls_Manager::GALLERY,
                'default' => [],
            ]
        );

        $repeater->add_control(
            'cover_image',
            [
                'label' => \esc_html__( 'Cover Image', 'sofir' ),
                'type' => \Elementor\Controls_Manager::MEDIA,
                'default' => [],
            ]
        );

        $this->add_control(
            'albums',
            [
                'label' => \esc_html__( 'Albums', 'sofir' ),
                'type' => \Elementor\Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default' => [
                    [
                        'album_title' => \esc_html__( 'Album 1', 'sofir' ),
                        'album_description' => \esc_html__( 'Description for album 1', 'sofir' ),
                    ],
                    [
                        'album_title' => \esc_html__( 'Album 2', 'sofir' ),
                        'album_description' => \esc_html__( 'Description for album 2', 'sofir' ),
                    ],
                ],
                'title_field' => '{{{ album_title }}}',
            ]
        );

        $this->add_control(
            'layout',
            [
                'label' => \esc_html__( 'Layout', 'sofir' ),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'grid',
                'options' => [
                    'grid' => \esc_html__( 'Grid Album', 'sofir' ),
                    'list' => \esc_html__( 'List Album', 'sofir' ),
                    'masonry' => \esc_html__( 'Masonry', 'sofir' ),
                ],
            ]
        );

        $this->add_layout_controls();

        $this->add_control(
            'image_size',
            [
                'label' => \esc_html__( 'Cover Image Size', 'sofir' ),
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

        $this->add_control(
            'show_image_count',
            [
                'label' => \esc_html__( 'Show Image Count', 'sofir' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_description',
            [
                'label' => \esc_html__( 'Show Description', 'sofir' ),
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
            'enable_sub_albums',
            [
                'label' => \esc_html__( 'Enable Sub-Albums', 'sofir' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'no',
                'description' => \esc_html__( 'Allow albums to contain sub-albums', 'sofir' ),
            ]
        );

        $this->end_controls_section();

        $this->add_style_controls();
        $this->add_album_style_controls();
    }

    protected function add_album_style_controls(): void {
        $this->start_controls_section(
            'section_album_style',
            [
                'label' => \esc_html__( 'Album Style', 'sofir' ),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'album_bg_color',
            [
                'label' => \esc_html__( 'Background Color', 'sofir' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .sofir-album-item' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'album_border',
                'selector' => '{{WRAPPER}} .sofir-album-item',
            ]
        );

        $this->add_control(
            'album_border_radius',
            [
                'label' => \esc_html__( 'Border Radius', 'sofir' ),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .sofir-album-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'album_box_shadow',
                'selector' => '{{WRAPPER}} .sofir-album-item',
            ]
        );

        $this->add_responsive_control(
            'album_padding',
            [
                'label' => \esc_html__( 'Padding', 'sofir' ),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .sofir-album-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'album_hover_effect',
            [
                'label' => \esc_html__( 'Hover Effect', 'sofir' ),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'lift',
                'options' => [
                    'none' => \esc_html__( 'None', 'sofir' ),
                    'lift' => \esc_html__( 'Lift', 'sofir' ),
                    'zoom' => \esc_html__( 'Zoom', 'sofir' ),
                    'fade' => \esc_html__( 'Fade', 'sofir' ),
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_title_style',
            [
                'label' => \esc_html__( 'Title Style', 'sofir' ),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'title_color',
            [
                'label' => \esc_html__( 'Color', 'sofir' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .sofir-album-title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'title_typography',
                'selector' => '{{WRAPPER}} .sofir-album-title',
            ]
        );

        $this->add_responsive_control(
            'title_spacing',
            [
                'label' => \esc_html__( 'Spacing', 'sofir' ),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 50,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .sofir-album-title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_description_style',
            [
                'label' => \esc_html__( 'Description Style', 'sofir' ),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_description' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'description_color',
            [
                'label' => \esc_html__( 'Color', 'sofir' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .sofir-album-description' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'description_typography',
                'selector' => '{{WRAPPER}} .sofir-album-description',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_count_style',
            [
                'label' => \esc_html__( 'Image Count Style', 'sofir' ),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_image_count' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'count_color',
            [
                'label' => \esc_html__( 'Color', 'sofir' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .sofir-album-count' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'count_bg_color',
            [
                'label' => \esc_html__( 'Background Color', 'sofir' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => 'rgba(0,0,0,0.7)',
                'selectors' => [
                    '{{WRAPPER}} .sofir-album-count' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'count_typography',
                'selector' => '{{WRAPPER}} .sofir-album-count',
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $albums = $settings['albums'];

        if ( empty( $albums ) ) {
            echo '<p>' . \esc_html__( 'No albums configured.', 'sofir' ) . '</p>';
            return;
        }

        $wrapper_class = 'sofir-album sofir-album-' . $settings['layout'];
        $wrapper_class .= ' sofir-album-columns-' . $settings['columns'];
        $wrapper_class .= ' sofir-album-hover-' . $settings['album_hover_effect'];
        ?>
        <div class="<?php echo \esc_attr( $wrapper_class ); ?>" 
             data-lightbox="<?php echo $settings['enable_lightbox'] === 'yes' ? 'true' : 'false'; ?>">
            <?php foreach ( $albums as $index => $album ) : ?>
                <?php
                $images = $album['album_images'];
                $image_count = is_array( $images ) ? count( $images ) : 0;
                
                $cover_image_id = ! empty( $album['cover_image']['id'] ) 
                    ? $album['cover_image']['id'] 
                    : ( ! empty( $images[0]['id'] ) ? $images[0]['id'] : 0 );
                
                $cover_image_url = $cover_image_id 
                    ? \wp_get_attachment_image_url( $cover_image_id, $settings['image_size'] ) 
                    : '';
                ?>
                <div class="sofir-album-item" data-album-id="<?php echo \esc_attr( $index ); ?>">
                    <?php if ( $cover_image_url ) : ?>
                        <div class="sofir-album-cover">
                            <img src="<?php echo \esc_url( $cover_image_url ); ?>" 
                                 alt="<?php echo \esc_attr( $album['album_title'] ); ?>">
                            
                            <?php if ( $settings['show_image_count'] === 'yes' && $image_count > 0 ) : ?>
                                <div class="sofir-album-count">
                                    <i class="eicon-image-bold"></i>
                                    <?php echo \esc_html( sprintf( _n( '%d image', '%d images', $image_count, 'sofir' ), $image_count ) ); ?>
                                </div>
                            <?php endif; ?>
                            
                            <div class="sofir-album-overlay">
                                <span class="sofir-album-icon">
                                    <i class="eicon-photo-library"></i>
                                </span>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <div class="sofir-album-content">
                        <h3 class="sofir-album-title"><?php echo \esc_html( $album['album_title'] ); ?></h3>
                        
                        <?php if ( $settings['show_description'] === 'yes' && ! empty( $album['album_description'] ) ) : ?>
                            <p class="sofir-album-description"><?php echo \esc_html( $album['album_description'] ); ?></p>
                        <?php endif; ?>
                    </div>
                    
                    <?php if ( $settings['enable_lightbox'] === 'yes' && ! empty( $images ) ) : ?>
                        <div class="sofir-album-images" style="display:none;">
                            <?php foreach ( $images as $image ) : ?>
                                <?php 
                                $image_full_url = \wp_get_attachment_image_url( $image['id'], 'full' );
                                $image_caption = \wp_get_attachment_caption( $image['id'] );
                                ?>
                                <a href="<?php echo \esc_url( $image_full_url ); ?>" 
                                   data-caption="<?php echo \esc_attr( $image_caption ); ?>"
                                   class="sofir-album-image-link"></a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
        <?php
    }
}
