/**
 * SOFIR Blocks - Editor JavaScript
 * Gutenberg editor enhancements and live preview
 */

(function() {
    'use strict';

    var el = wp.element.createElement;
    var __ = wp.i18n.__;
    var registerBlockStyle = wp.blocks.registerBlockStyle;
    var unregisterBlockStyle = wp.blocks.unregisterBlockStyle;

    /**
     * Register block styles for SOFIR blocks
     */
    var blockStyles = {
        'sofir/action': [
            {
                name: 'default',
                label: __('Default', 'sofir'),
                isDefault: true
            },
            {
                name: 'outline',
                label: __('Outline', 'sofir')
            },
            {
                name: 'rounded',
                label: __('Rounded', 'sofir')
            }
        ],
        'sofir/cta-banner': [
            {
                name: 'gradient',
                label: __('Gradient', 'sofir'),
                isDefault: true
            },
            {
                name: 'solid',
                label: __('Solid Color', 'sofir')
            },
            {
                name: 'image',
                label: __('Image Background', 'sofir')
            }
        ],
        'sofir/pricing-table': [
            {
                name: 'default',
                label: __('Default', 'sofir'),
                isDefault: true
            },
            {
                name: 'minimal',
                label: __('Minimal', 'sofir')
            },
            {
                name: 'bold',
                label: __('Bold', 'sofir')
            }
        ]
    };

    // Register block styles on editor load
    wp.domReady(function() {
        Object.keys(blockStyles).forEach(function(blockName) {
            blockStyles[blockName].forEach(function(style) {
                try {
                    registerBlockStyle(blockName, style);
                } catch(e) {
                    // Style may already be registered
                }
            });
        });

        // Remove default WordPress block styles that conflict
        unregisterBlockStyle('core/button', 'fill');
        unregisterBlockStyle('core/button', 'outline');
    });

    /**
     * Add custom block categories icon
     */
    wp.hooks.addFilter(
        'blocks.registerBlockType',
        'sofir/block-icon',
        function(settings, name) {
            if (name.indexOf('sofir/') === 0) {
                settings.category = 'sofir';
                
                if (!settings.icon) {
                    settings.icon = el('svg', {
                        width: 24,
                        height: 24,
                        viewBox: '0 0 24 24'
                    },
                        el('path', {
                            fill: 'currentColor',
                            d: 'M12 2L2 7v10c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V7l-10-5z'
                        })
                    );
                }
            }
            return settings;
        }
    );

    /**
     * Add block wrapper for better editor experience
     */
    wp.hooks.addFilter(
        'editor.BlockListBlock',
        'sofir/with-block-wrapper',
        function(BlockListBlock) {
            return function(props) {
                if (props.name && props.name.indexOf('sofir/') === 0) {
                    var blockName = props.name.replace('sofir/', '');
                    
                    return el(
                        'div',
                        {
                            className: 'sofir-editor-block-wrapper sofir-editor-block-' + blockName,
                            'data-type': blockName
                        },
                        el(BlockListBlock, props)
                    );
                }
                
                return el(BlockListBlock, props);
            };
        }
    );

    /**
     * Live preview updates
     */
    wp.data.subscribe(function() {
        var editor = wp.data.select('core/editor');
        
        if (!editor) return;

        var blocks = editor.getBlocks();
        
        blocks.forEach(function(block) {
            if (block.name && block.name.indexOf('sofir/') === 0) {
                // Trigger custom event for SOFIR blocks
                var event = new CustomEvent('sofir:block:updated', {
                    detail: { block: block }
                });
                document.dispatchEvent(event);
            }
        });
    });

    /**
     * Add helpful notices for Templately compatibility
     */
    if (typeof Templately !== 'undefined') {
        wp.data.dispatch('core/notices').createNotice(
            'info',
            __('SOFIR blocks are fully compatible with Templately templates!', 'sofir'),
            {
                isDismissible: true,
                id: 'sofir-templately-notice'
            }
        );
    }

    /**
     * Add keyboard shortcuts
     */
    wp.data.dispatch('core/keyboard-shortcuts').registerShortcut({
        name: 'sofir/insert-block',
        category: 'block',
        description: __('Insert SOFIR block', 'sofir'),
        keyCombination: {
            modifier: 'primary',
            character: 'shift+s'
        }
    });

    /**
     * Auto-save optimization for SOFIR blocks
     */
    var autoSaveTimeout;
    document.addEventListener('sofir:block:updated', function() {
        clearTimeout(autoSaveTimeout);
        autoSaveTimeout = setTimeout(function() {
            if (wp.data.select('core/editor').isEditedPostDirty()) {
                wp.data.dispatch('core/editor').autosave();
            }
        }, 3000);
    });

    /**
     * Console log for debugging
     */
    if (window.location.search.indexOf('sofir-debug') !== -1) {
        console.log('[SOFIR] Editor JavaScript loaded');
        console.log('[SOFIR] Registered block styles:', blockStyles);
    }

})();
