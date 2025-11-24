<?php
namespace Sofir\Elementor;

use Elementor\Widget_Base;

abstract class BaseWidget extends Widget_Base {
    public function get_categories() {
        return [ 'sofir' ];
    }

    public function get_icon() {
        return 'eicon-posts-grid';
    }

    protected function add_layout_controls(): void {
        $this->add_control(
            'columns',
            [
                'label' => \esc_html__( 'Columns', 'sofir' ),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => '3',
                'options' => [
                    '1' => '1',
                    '2' => '2',
                    '3' => '3',
                    '4' => '4',
                    '6' => '6',
                ],
            ]
        );

        $this->add_control(
            'gap',
            [
                'label' => \esc_html__( 'Gap', 'sofir' ),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'size' => 20,
                ],
            ]
        );
    }

    protected function add_style_controls(): void {
        $this->start_controls_section(
            'section_style',
            [
                'label' => \esc_html__( 'Style', 'sofir' ),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'text_color',
            [
                'label' => \esc_html__( 'Text Color', 'sofir' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}}' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'typography',
                'selector' => '{{WRAPPER}}',
            ]
        );

        $this->end_controls_section();
    }

    protected function render_block_content( string $block_name, array $attributes = [] ): void {
        $block = \WP_Block_Type_Registry::get_instance()->get_registered( $block_name );
        
        if ( ! $block || ! isset( $block->render_callback ) ) {
            echo '<p>' . \esc_html__( 'Block not found or has no render callback.', 'sofir' ) . '</p>';
            return;
        }

        echo call_user_func( $block->render_callback, $attributes, '' );
    }
}
