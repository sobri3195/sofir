(function($) {
    'use strict';

    const SofirSeoAI = {
        productList: [],

        init() {
            this.bindEvents();
            this.handleArticleTypeChange();
        },

        bindEvents() {
            $('.sofir-ai-tab').on('click', this.handleTabSwitch);
            $('.sofir-slider').on('input', this.handleSliderChange);
            $('#sofir-article-type').on('change', this.handleArticleTypeChange.bind(this));
            $('#sofir-ai-article-form').on('submit', this.handleArticleGeneration.bind(this));
            $('#sofir-ai-keywords-form').on('submit', this.handleKeywordResearch.bind(this));
            $(document).on('click', '.sofir-ai-create-post', this.handleCreatePost.bind(this));
            $(document).on('click', '.sofir-ai-copy-text', this.handleCopyText);
            $(document).on('click', '#sofir-get-products-btn', this.handleGetProductSuggestions.bind(this));
            $(document).on('click', '#sofir-add-product-btn', this.handleAddProduct.bind(this));
            $(document).on('click', '.sofir-remove-product', this.handleRemoveProduct.bind(this));
            $(document).on('input', '.sofir-product-field', this.handleProductFieldChange.bind(this));
        },

        handleArticleTypeChange() {
            const selectedType = $('#sofir-article-type').val() || 'general';
            
            $('.sofir-conditional-field').each(function() {
                const $field = $(this);
                const showWhen = $field.data('show-when');
                const showValues = String($field.data('show-value')).split(',');
                
                if (showWhen && showValues.length > 0) {
                    if (showValues.includes(selectedType)) {
                        $field.slideDown(300);
                    } else {
                        $field.slideUp(300);
                    }
                }
            });
        },

        handleTabSwitch(e) {
            e.preventDefault();
            const $tab = $(this);
            const tabName = $tab.data('tab');

            $('.sofir-ai-tab').removeClass('active');
            $('.sofir-ai-tab-content').removeClass('active');

            $tab.addClass('active');
            $(`.sofir-ai-tab-content[data-content="${tabName}"]`).addClass('active');
        },

        handleSliderChange(e) {
            const value = $(this).val();
            $(this).siblings('span').find('.creativity-value').text(value);
        },

        async handleArticleGeneration(e) {
            e.preventDefault();
            
            const $form = $(e.target);
            const $loading = $('#sofir-ai-loading');
            const $result = $('#sofir-ai-result');
            const $button = $form.find('button[type="submit"]');

            const formData = new FormData($form[0]);
            const data = {
                action: 'sofir_generate_seo_article',
                nonce: $('#sofir_ai_nonce').val(),
                title: formData.get('title'),
                keyword: formData.get('keyword'),
                article_type: formData.get('article_type'),
                purpose: formData.get('purpose'),
                tone: formData.get('tone'),
                word_count: formData.get('word_count'),
                pov: formData.get('pov'),
                readability: formData.get('readability'),
                creativity: formData.get('creativity'),
                include_faq: formData.get('include_faq') ? 1 : 0,
                include_toc: formData.get('include_toc') ? 1 : 0,
                product_names: formData.get('product_names'),
                product_features: formData.get('product_features'),
                comparison_criteria: formData.get('comparison_criteria'),
                list_count: formData.get('list_count'),
                product_list: JSON.stringify(this.productList)
            };

            $button.prop('disabled', true);
            $result.hide();
            $loading.show();

            try {
                const response = await $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: data
                });

                $loading.hide();

                if (response.success) {
                    this.renderArticleResult(response.data);
                    $result.show();
                } else {
                    this.showError(response.data.message || 'Failed to generate article');
                }
            } catch (error) {
                $loading.hide();
                this.showError('An error occurred. Please try again.');
            } finally {
                $button.prop('disabled', false);
            }
        },

        async handleKeywordResearch(e) {
            e.preventDefault();
            
            const $form = $(e.target);
            const $loading = $('#sofir-keywords-loading');
            const $result = $('#sofir-keywords-result');
            const $button = $form.find('button[type="submit"]');

            const formData = new FormData($form[0]);
            const data = {
                action: 'sofir_research_keywords',
                nonce: $('#sofir_ai_nonce_keywords').val(),
                keyword: formData.get('keyword')
            };

            $button.prop('disabled', true);
            $result.hide();
            $loading.show();

            try {
                const response = await $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: data
                });

                $loading.hide();

                if (response.success) {
                    this.renderKeywordResult(response.data.keywords);
                    $result.show();
                } else {
                    this.showError(response.data.message || 'Failed to research keywords');
                }
            } catch (error) {
                $loading.hide();
                this.showError('An error occurred. Please try again.');
            } finally {
                $button.prop('disabled', false);
            }
        },

        renderArticleResult(data) {
            const $result = $('#sofir-ai-result');
            
            let html = '<div class="sofir-ai-result-content">';
            
            html += '<div class="sofir-ai-result-header">';
            html += '<h3>' + this.escapeHtml(data.title) + '</h3>';
            html += '<div class="sofir-ai-result-actions">';
            html += '<button type="button" class="button button-primary sofir-ai-create-post" data-status="draft">Save as Draft</button>';
            html += '<button type="button" class="button sofir-ai-create-post" data-status="publish">Publish</button>';
            html += '</div>';
            html += '</div>';

            if (data.seo_score) {
                html += this.renderSeoScore(data.seo_score);
            }

            html += '<div class="sofir-ai-tabs-result">';
            html += '<button type="button" class="sofir-ai-tab-result active" data-tab-result="content">Content</button>';
            html += '<button type="button" class="sofir-ai-tab-result" data-tab-result="meta">Meta Data</button>';
            html += '<button type="button" class="sofir-ai-tab-result" data-tab-result="outline">Outline</button>';
            html += '<button type="button" class="sofir-ai-tab-result" data-tab-result="keywords">Keywords</button>';
            html += '<button type="button" class="sofir-ai-tab-result" data-tab-result="seo">SEO Analysis</button>';
            html += '<button type="button" class="sofir-ai-tab-result" data-tab-result="schema">Schema</button>';
            html += '</div>';

            html += '<div class="sofir-ai-tab-result-content active" data-content-result="content">';
            html += '<div class="sofir-ai-section">';
            html += '<h4>Introduction</h4>';
            html += '<div class="sofir-ai-content-box">' + this.escapeHtml(data.introduction) + '</div>';
            html += '</div>';
            html += '<div class="sofir-ai-section">';
            html += '<h4>Full Content</h4>';
            html += '<div class="sofir-ai-content-box sofir-ai-html-content">' + data.content + '</div>';
            html += '<button type="button" class="button sofir-ai-copy-text" data-content="' + this.escapeAttr(data.content) + '">Copy Content</button>';
            html += '</div>';
            if (data.conclusion) {
                html += '<div class="sofir-ai-section">';
                html += '<h4>Conclusion</h4>';
                html += '<div class="sofir-ai-content-box">' + this.escapeHtml(data.conclusion) + '</div>';
                html += '</div>';
            }
            html += '</div>';

            html += '<div class="sofir-ai-tab-result-content" data-content-result="meta">';
            html += '<div class="sofir-ai-section">';
            html += '<h4>Meta Title</h4>';
            html += '<div class="sofir-ai-content-box">' + this.escapeHtml(data.meta_title) + '</div>';
            html += '<p class="description">Length: ' + (data.meta_title || '').length + ' characters</p>';
            html += '</div>';
            html += '<div class="sofir-ai-section">';
            html += '<h4>Meta Description</h4>';
            html += '<div class="sofir-ai-content-box">' + this.escapeHtml(data.meta_description) + '</div>';
            html += '<p class="description">Length: ' + (data.meta_description || '').length + ' characters</p>';
            html += '</div>';
            html += '<div class="sofir-ai-section">';
            html += '<h4>URL Slug</h4>';
            html += '<div class="sofir-ai-content-box"><code>' + this.escapeHtml(data.slug) + '</code></div>';
            html += '</div>';
            html += '</div>';

            html += '<div class="sofir-ai-tab-result-content" data-content-result="outline">';
            if (data.outline && data.outline.length > 0) {
                html += '<div class="sofir-ai-section">';
                html += '<h4>Article Outline</h4>';
                html += '<ol class="sofir-ai-outline">';
                data.outline.forEach(item => {
                    html += '<li>' + this.escapeHtml(item) + '</li>';
                });
                html += '</ol>';
                html += '</div>';
            }
            if (data.headings && data.headings.length > 0) {
                html += '<div class="sofir-ai-section">';
                html += '<h4>Heading Structure</h4>';
                html += '<ul class="sofir-ai-headings">';
                data.headings.forEach(heading => {
                    html += '<li class="heading-' + heading.level + '">' + this.escapeHtml(heading.text) + '</li>';
                });
                html += '</ul>';
                html += '</div>';
            }
            if (data.talking_points && data.talking_points.length > 0) {
                html += '<div class="sofir-ai-section">';
                html += '<h4>Key Talking Points</h4>';
                html += '<ul>';
                data.talking_points.forEach(point => {
                    html += '<li>' + this.escapeHtml(point) + '</li>';
                });
                html += '</ul>';
                html += '</div>';
            }
            if (data.faqs && data.faqs.length > 0) {
                html += '<div class="sofir-ai-section">';
                html += '<h4>FAQ Section</h4>';
                data.faqs.forEach(faq => {
                    html += '<div class="sofir-ai-faq">';
                    html += '<strong>Q: ' + this.escapeHtml(faq.question) + '</strong>';
                    html += '<p>A: ' + this.escapeHtml(faq.answer) + '</p>';
                    html += '</div>';
                });
                html += '</div>';
            }
            html += '</div>';

            html += '<div class="sofir-ai-tab-result-content" data-content-result="keywords">';
            if (data.keywords && data.keywords.length > 0) {
                html += '<div class="sofir-ai-section">';
                html += '<h4>Target Keywords</h4>';
                html += '<div class="sofir-ai-keywords">';
                data.keywords.forEach(keyword => {
                    html += '<span class="sofir-keyword-tag">' + this.escapeHtml(keyword) + '</span>';
                });
                html += '</div>';
                html += '</div>';
            }
            if (data.contextual_terms && data.contextual_terms.length > 0) {
                html += '<div class="sofir-ai-section">';
                html += '<h4>Contextual Terms & LSI Keywords</h4>';
                html += '<div class="sofir-ai-keywords">';
                data.contextual_terms.forEach(term => {
                    html += '<span class="sofir-keyword-tag sofir-lsi">' + this.escapeHtml(term) + '</span>';
                });
                html += '</div>';
                html += '</div>';
            }
            if (data.inline_suggestions && data.inline_suggestions.length > 0) {
                html += '<div class="sofir-ai-section">';
                html += '<h4>Inline Suggested Reads</h4>';
                html += '<ul>';
                data.inline_suggestions.forEach(suggestion => {
                    html += '<li>' + this.escapeHtml(suggestion) + '</li>';
                });
                html += '</ul>';
                html += '</div>';
            }
            if (data.internal_links && data.internal_links.length > 0) {
                html += '<div class="sofir-ai-section">';
                html += '<h4>Internal Link Suggestions</h4>';
                html += '<ul>';
                data.internal_links.forEach(link => {
                    html += '<li><a href="' + link.url + '" target="_blank">' + this.escapeHtml(link.title) + '</a> <span class="description">(Relevance: ' + link.relevance + ')</span></li>';
                });
                html += '</ul>';
                html += '</div>';
            }
            if (data.featured_image_description) {
                html += '<div class="sofir-ai-section">';
                html += '<h4>Featured Image Suggestion</h4>';
                html += '<p>' + this.escapeHtml(data.featured_image_description) + '</p>';
                html += '</div>';
            }
            html += '</div>';

            html += '<div class="sofir-ai-tab-result-content" data-content-result="seo">';
            if (data.seo_suggestions && data.seo_suggestions.length > 0) {
                html += '<div class="sofir-ai-section">';
                html += '<h4>SEO Suggestions</h4>';
                data.seo_suggestions.forEach(suggestion => {
                    const icon = suggestion.type === 'error' ? '❌' : 
                                 suggestion.type === 'warning' ? '⚠️' : 
                                 suggestion.type === 'success' ? '✅' : 'ℹ️';
                    html += '<div class="sofir-seo-suggestion sofir-seo-' + suggestion.type + '">';
                    html += '<span class="sofir-seo-icon">' + icon + '</span>';
                    html += '<span>' + this.escapeHtml(suggestion.message) + '</span>';
                    html += '</div>';
                });
                html += '</div>';
            }
            html += '</div>';

            html += '<div class="sofir-ai-tab-result-content" data-content-result="schema">';
            if (data.schema) {
                html += '<div class="sofir-ai-section">';
                html += '<h4>JSON-LD Schema</h4>';
                html += '<pre class="sofir-ai-code">' + JSON.stringify(data.schema, null, 2) + '</pre>';
                html += '<button type="button" class="button sofir-ai-copy-text" data-content="' + this.escapeAttr(JSON.stringify(data.schema)) + '">Copy Schema</button>';
                html += '</div>';
            }
            html += '</div>';

            html += '</div>';

            $result.html(html);
            $result.data('article', data);

            $('.sofir-ai-tab-result').on('click', function() {
                const tab = $(this).data('tab-result');
                $('.sofir-ai-tab-result').removeClass('active');
                $('.sofir-ai-tab-result-content').removeClass('active');
                $(this).addClass('active');
                $(`.sofir-ai-tab-result-content[data-content-result="${tab}"]`).addClass('active');
            });
        },

        renderSeoScore(score) {
            const percentage = score.percentage || 0;
            const color = percentage >= 80 ? '#46b450' : percentage >= 60 ? '#ffb900' : '#dc3232';
            
            let html = '<div class="sofir-seo-score">';
            html += '<div class="sofir-seo-score-circle" style="--score-color: ' + color + '; --score-percentage: ' + percentage + '%;">';
            html += '<svg width="120" height="120">';
            html += '<circle cx="60" cy="60" r="54" fill="none" stroke="#e0e0e0" stroke-width="8"></circle>';
            html += '<circle cx="60" cy="60" r="54" fill="none" stroke="' + color + '" stroke-width="8" stroke-dasharray="339.292" stroke-dashoffset="' + (339.292 - (339.292 * percentage / 100)) + '" transform="rotate(-90 60 60)"></circle>';
            html += '</svg>';
            html += '<div class="sofir-seo-score-text">';
            html += '<span class="sofir-seo-score-number">' + percentage + '</span>';
            html += '<span class="sofir-seo-score-label">SEO Score</span>';
            html += '</div>';
            html += '</div>';
            
            if (score.checks && score.checks.length > 0) {
                html += '<div class="sofir-seo-checks">';
                score.checks.forEach(check => {
                    const icon = check.status === 'pass' ? '✓' : '✗';
                    const className = check.status === 'pass' ? 'pass' : 'fail';
                    html += '<div class="sofir-seo-check ' + className + '">';
                    html += '<span class="sofir-seo-check-icon">' + icon + '</span>';
                    html += '<span class="sofir-seo-check-label">' + this.escapeHtml(check.item) + '</span>';
                    html += '<span class="sofir-seo-check-points">' + check.points + ' pts</span>';
                    html += '</div>';
                });
                html += '</div>';
            }
            
            html += '</div>';
            return html;
        },

        renderKeywordResult(keywords) {
            const $result = $('#sofir-keywords-result');
            
            let html = '<div class="sofir-keywords-result-content">';
            
            html += '<div class="sofir-keywords-header">';
            html += '<h3>Keyword Research Results</h3>';
            html += '<p class="description">Primary Keyword: <strong>' + this.escapeHtml(keywords.primary_keyword || '') + '</strong></p>';
            html += '<p class="description">Search Intent: <strong>' + this.escapeHtml(keywords.search_intent || '') + '</strong></p>';
            html += '</div>';

            if (keywords.keyword_variations && keywords.keyword_variations.length > 0) {
                html += '<div class="sofir-keywords-section">';
                html += '<h4>Keyword Variations</h4>';
                html += '<table class="widefat">';
                html += '<thead><tr><th>Keyword</th><th>Difficulty</th><th>Est. Volume</th></tr></thead>';
                html += '<tbody>';
                keywords.keyword_variations.forEach(kw => {
                    const diffClass = kw.difficulty === 'easy' ? 'easy' : kw.difficulty === 'medium' ? 'medium' : 'hard';
                    html += '<tr>';
                    html += '<td>' + this.escapeHtml(kw.keyword) + '</td>';
                    html += '<td><span class="sofir-difficulty sofir-difficulty-' + diffClass + '">' + this.escapeHtml(kw.difficulty) + '</span></td>';
                    html += '<td>' + this.escapeHtml(kw.volume) + '</td>';
                    html += '</tr>';
                });
                html += '</tbody></table>';
                html += '</div>';
            }

            const sections = [
                { key: 'long_tail_keywords', title: 'Long-Tail Keywords' },
                { key: 'related_keywords', title: 'Related Keywords' },
                { key: 'lsi_keywords', title: 'LSI Keywords' },
                { key: 'competitor_keywords', title: 'Competitor Keywords' },
                { key: 'question_keywords', title: 'Question Keywords' },
                { key: 'trending_topics', title: 'Trending Topics' }
            ];

            sections.forEach(section => {
                if (keywords[section.key] && keywords[section.key].length > 0) {
                    html += '<div class="sofir-keywords-section">';
                    html += '<h4>' + section.title + '</h4>';
                    html += '<div class="sofir-ai-keywords">';
                    keywords[section.key].forEach(keyword => {
                        html += '<span class="sofir-keyword-tag">' + this.escapeHtml(keyword) + '</span>';
                    });
                    html += '</div>';
                    html += '</div>';
                }
            });

            html += '</div>';

            $result.html(html);
        },

        async handleCreatePost(e) {
            e.preventDefault();
            
            const $button = $(e.target);
            const status = $button.data('status');
            const $result = $('#sofir-ai-result');
            const article = $result.data('article');

            if (!article) {
                this.showError('No article data found');
                return;
            }

            $button.prop('disabled', true).text('Creating...');

            try {
                const response = await $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'sofir_create_post_from_ai',
                        nonce: $('#sofir_ai_nonce').val(),
                        article: JSON.stringify(article),
                        status: status
                    }
                });

                if (response.success) {
                    this.showSuccess('Post created successfully!');
                    if (response.data.edit_url) {
                        window.location.href = response.data.edit_url;
                    }
                } else {
                    this.showError(response.data.message || 'Failed to create post');
                }
            } catch (error) {
                this.showError('An error occurred. Please try again.');
            } finally {
                $button.prop('disabled', false).text(status === 'draft' ? 'Save as Draft' : 'Publish');
            }
        },

        handleCopyText(e) {
            e.preventDefault();
            const $button = $(e.target);
            const text = $button.data('content');

            if (navigator.clipboard) {
                navigator.clipboard.writeText(text).then(() => {
                    const originalText = $button.text();
                    $button.text('Copied!');
                    setTimeout(() => {
                        $button.text(originalText);
                    }, 2000);
                });
            } else {
                const $temp = $('<textarea>').val(text).appendTo('body').select();
                document.execCommand('copy');
                $temp.remove();
                const originalText = $button.text();
                $button.text('Copied!');
                setTimeout(() => {
                    $button.text(originalText);
                }, 2000);
            }
        },

        showError(message) {
            const html = '<div class="notice notice-error"><p>' + this.escapeHtml(message) + '</p></div>';
            $('.sofir-ai-generator-card').prepend(html);
            setTimeout(() => {
                $('.notice').fadeOut();
            }, 5000);
        },

        showSuccess(message) {
            const html = '<div class="notice notice-success"><p>' + this.escapeHtml(message) + '</p></div>';
            $('.sofir-ai-generator-card').prepend(html);
            setTimeout(() => {
                $('.notice').fadeOut();
            }, 3000);
        },

        escapeHtml(text) {
            if (!text) return '';
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.toString().replace(/[&<>"']/g, m => map[m]);
        },

        escapeAttr(text) {
            if (!text) return '';
            return this.escapeHtml(text).replace(/"/g, '&quot;');
        },

        async handleGetProductSuggestions(e) {
            e.preventDefault();
            
            const $button = $(e.target);
            const query = $('#sofir-product-search').val();
            
            if (!query) {
                this.showError('Please enter a product search query');
                return;
            }

            $button.prop('disabled', true).text('Loading...');

            try {
                const response = await $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'sofir_get_product_suggestions',
                        nonce: $('#sofir_ai_nonce').val(),
                        query: query
                    }
                });

                if (response.success && response.data.products) {
                    this.productList = response.data.products;
                    this.renderProductList();
                    this.showSuccess(response.data.products.length + ' products loaded from AI suggestions!');
                } else {
                    this.showError(response.data.message || 'Failed to get product suggestions');
                }
            } catch (error) {
                this.showError('An error occurred. Please try again.');
            } finally {
                $button.prop('disabled', false).text('Get AI Suggestions');
            }
        },

        handleAddProduct(e) {
            e.preventDefault();
            
            this.productList.push({
                name: '',
                url: '',
                description: ''
            });
            
            this.renderProductList();
        },

        handleRemoveProduct(e) {
            e.preventDefault();
            
            const index = $(e.target).data('index');
            this.productList.splice(index, 1);
            this.renderProductList();
        },

        handleProductFieldChange(e) {
            const $input = $(e.target);
            const index = $input.data('index');
            const field = $input.data('field');
            
            if (this.productList[index]) {
                this.productList[index][field] = $input.val();
            }
        },

        renderProductList() {
            const $container = $('#sofir-product-list-container');
            
            if (this.productList.length === 0) {
                $container.html('<p class="description">No products added yet. Click "Add Product" or get AI suggestions.</p>');
                return;
            }

            let html = '<div class="sofir-product-count"><strong>' + this.productList.length + ' products</strong></div>';
            html += '<table class="widefat sofir-product-table">';
            html += '<thead><tr>';
            html += '<th style="width: 5%;">#</th>';
            html += '<th style="width: 25%;">Product Name</th>';
            html += '<th style="width: 30%;">Product URL</th>';
            html += '<th style="width: 30%;">Description</th>';
            html += '<th style="width: 10%;">Actions</th>';
            html += '</tr></thead>';
            html += '<tbody>';

            this.productList.forEach((product, index) => {
                html += '<tr>';
                html += '<td>' + (index + 1) + '</td>';
                html += '<td><input type="text" class="sofir-product-field regular-text" data-index="' + index + '" data-field="name" value="' + this.escapeAttr(product.name) + '" placeholder="Product name" /></td>';
                html += '<td><input type="url" class="sofir-product-field regular-text" data-index="' + index + '" data-field="url" value="' + this.escapeAttr(product.url) + '" placeholder="https://example.com/product" /></td>';
                html += '<td><input type="text" class="sofir-product-field regular-text" data-index="' + index + '" data-field="description" value="' + this.escapeAttr(product.description) + '" placeholder="Brief description" /></td>';
                html += '<td><button type="button" class="button button-link-delete sofir-remove-product" data-index="' + index + '">Remove</button></td>';
                html += '</tr>';
            });

            html += '</tbody></table>';

            $container.html(html);
        }
    };

    $(document).ready(() => {
        SofirSeoAI.init();
    });

})(jQuery);
