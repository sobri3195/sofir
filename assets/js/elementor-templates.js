/**
 * SOFIR Elementor Template Library
 * Inspired by Slider Revolution template system
 */

(function($) {
    'use strict';

    class SofirTemplateLibrary {
        constructor() {
            this.templates = window.sofirElementorTemplates || {};
            this.modal = null;
            this.init();
        }

        init() {
            if (typeof elementor === 'undefined') {
                return;
            }

            elementor.on('preview:loaded', () => {
                this.addTemplateButton();
            });
        }

        addTemplateButton() {
            const self = this;
            
            elementor.getPanelView().addButton({
                name: 'sofir-templates',
                icon: 'eicon-folder',
                title: 'SOFIR Templates',
                callback: () => {
                    self.openTemplateLibrary();
                }
            });
        }

        openTemplateLibrary() {
            if (this.modal) {
                this.modal.show();
                return;
            }

            this.createModal();
        }

        createModal() {
            const self = this;
            
            const modalHtml = `
                <div id="sofir-template-modal" class="sofir-template-modal">
                    <div class="sofir-modal-overlay"></div>
                    <div class="sofir-modal-container">
                        <div class="sofir-modal-header">
                            <h2>
                                <span class="sofir-logo">✨</span>
                                SOFIR Template Library
                            </h2>
                            <button class="sofir-modal-close">&times;</button>
                        </div>
                        <div class="sofir-modal-tabs">
                            <button class="sofir-tab-btn active" data-tab="all">All Templates</button>
                            <button class="sofir-tab-btn" data-tab="popup">Popups</button>
                            <button class="sofir-tab-btn" data-tab="card">Cards</button>
                            <button class="sofir-tab-btn" data-tab="page">Pages</button>
                            <button class="sofir-tab-btn" data-tab="post">Single</button>
                            <button class="sofir-tab-btn" data-tab="archive">Archives</button>
                            <button class="sofir-tab-btn" data-tab="header">Headers</button>
                            <button class="sofir-tab-btn" data-tab="footer">Footers</button>
                        </div>
                        <div class="sofir-modal-search">
                            <input type="text" id="sofir-template-search" placeholder="Search templates..." />
                        </div>
                        <div class="sofir-modal-body">
                            <div class="sofir-templates-grid" id="sofir-templates-grid">
                                ${this.renderTemplates('all')}
                            </div>
                        </div>
                    </div>
                </div>
            `;

            $('body').append(modalHtml);
            this.modal = $('#sofir-template-modal');
            this.bindEvents();
        }

        renderTemplates(category) {
            const templates = this.getTemplatesByCategory(category);
            let html = '';

            if (templates.length === 0) {
                return '<div class="sofir-no-templates">No templates found</div>';
            }

            templates.forEach(template => {
                html += `
                    <div class="sofir-template-card" data-id="${template.id}" data-type="${template.type}">
                        <div class="sofir-template-preview">
                            <img src="${template.preview}" alt="${template.title}" />
                            <div class="sofir-template-overlay">
                                <button class="sofir-template-btn sofir-btn-preview" data-id="${template.id}">
                                    <i class="eicon-device-desktop"></i> Preview
                                </button>
                                <button class="sofir-template-btn sofir-btn-insert" data-id="${template.id}">
                                    <i class="eicon-file-download"></i> Insert
                                </button>
                            </div>
                            ${template.pro ? '<span class="sofir-pro-badge">PRO</span>' : ''}
                        </div>
                        <div class="sofir-template-info">
                            <h3>${template.title}</h3>
                            <p>${template.description}</p>
                            <div class="sofir-template-tags">
                                ${this.renderTags(template.tags)}
                            </div>
                        </div>
                    </div>
                `;
            });

            return html;
        }

        renderTags(tags) {
            if (!tags || tags.length === 0) return '';
            
            return tags.map(tag => `<span class="sofir-tag">${tag}</span>`).join('');
        }

        getTemplatesByCategory(category) {
            const templates = this.templates.templates || {};
            let result = [];

            if (category === 'all') {
                Object.keys(templates).forEach(cat => {
                    if (Array.isArray(templates[cat])) {
                        result = result.concat(templates[cat]);
                    }
                });
            } else {
                result = templates[category] || [];
            }

            return result;
        }

        bindEvents() {
            const self = this;

            $('.sofir-modal-close, .sofir-modal-overlay').on('click', function() {
                self.modal.hide();
            });

            $('.sofir-tab-btn').on('click', function() {
                const tab = $(this).data('tab');
                $('.sofir-tab-btn').removeClass('active');
                $(this).addClass('active');
                
                $('#sofir-templates-grid').html(self.renderTemplates(tab));
            });

            $('#sofir-template-search').on('input', function() {
                self.filterTemplates($(this).val());
            });

            $(document).on('click', '.sofir-btn-insert', function(e) {
                e.preventDefault();
                const templateId = $(this).data('id');
                self.insertTemplate(templateId);
            });

            $(document).on('click', '.sofir-btn-preview', function(e) {
                e.preventDefault();
                const templateId = $(this).data('id');
                self.previewTemplate(templateId);
            });
        }

        filterTemplates(query) {
            const cards = $('.sofir-template-card');
            
            if (!query) {
                cards.show();
                return;
            }

            query = query.toLowerCase();
            
            cards.each(function() {
                const $card = $(this);
                const title = $card.find('h3').text().toLowerCase();
                const description = $card.find('p').text().toLowerCase();
                const tags = $card.find('.sofir-tag').text().toLowerCase();
                
                if (title.includes(query) || description.includes(query) || tags.includes(query)) {
                    $card.show();
                } else {
                    $card.hide();
                }
            });
        }

        insertTemplate(templateId) {
            const self = this;
            
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'sofir_import_elementor_template',
                    nonce: this.templates.nonce,
                    template_id: templateId
                },
                beforeSend: function() {
                    elementor.loading.show();
                },
                success: function(response) {
                    if (response.success && response.data.content) {
                        self.importContent(response.data.content);
                        self.modal.hide();
                        elementor.notifications.showToast({
                            message: 'Template imported successfully!'
                        });
                    } else {
                        elementor.notifications.showToast({
                            message: response.data.message || 'Failed to import template',
                            type: 'error'
                        });
                    }
                },
                error: function() {
                    elementor.notifications.showToast({
                        message: 'Error importing template',
                        type: 'error'
                    });
                },
                complete: function() {
                    elementor.loading.hide();
                }
            });
        }

        importContent(content) {
            if (!content || !content.content) {
                return;
            }

            const elements = content.content;
            
            elements.forEach(element => {
                elementor.getPreviewView().addChildElement(element);
            });
        }

        previewTemplate(templateId) {
            const template = this.findTemplateById(templateId);
            
            if (!template) {
                return;
            }

            const previewHtml = `
                <div class="sofir-preview-modal">
                    <div class="sofir-preview-overlay"></div>
                    <div class="sofir-preview-container">
                        <button class="sofir-preview-close">&times;</button>
                        <div class="sofir-preview-content">
                            <img src="${template.preview}" alt="${template.title}" />
                        </div>
                        <div class="sofir-preview-footer">
                            <h3>${template.title}</h3>
                            <button class="elementor-button elementor-button-success sofir-btn-insert" data-id="${template.id}">
                                Insert Template
                            </button>
                        </div>
                    </div>
                </div>
            `;

            if ($('.sofir-preview-modal').length) {
                $('.sofir-preview-modal').remove();
            }

            $('body').append(previewHtml);

            $('.sofir-preview-close, .sofir-preview-overlay').on('click', function() {
                $('.sofir-preview-modal').remove();
            });
        }

        findTemplateById(templateId) {
            const templates = this.templates.templates || {};
            
            for (const category in templates) {
                if (Array.isArray(templates[category])) {
                    const template = templates[category].find(t => t.id === templateId);
                    if (template) {
                        return template;
                    }
                }
            }
            
            return null;
        }
    }

    $(window).on('elementor:init', function() {
        new SofirTemplateLibrary();
    });

})(jQuery);
