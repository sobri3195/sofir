jQuery(document).ready(function($) {
    const addon = sofirWCAddon;

    // Toggle Addon
    $('#addon-toggle').on('change', function() {
        const enabled = $(this).is(':checked');
        
        $.ajax({
            url: addon.ajaxurl,
            type: 'POST',
            data: {
                action: 'sofir_toggle_addon',
                nonce: addon.nonce,
                enabled: enabled ? 1 : 0
            },
            success: function(response) {
                if (response.success) {
                    showNotice(response.data.message, 'success');
                    location.reload();
                }
            },
            error: function() {
                showNotice(addon.i18n.error, 'error');
            }
        });
    });

    // Category Filter
    $('.category-item').on('click', function(e) {
        e.preventDefault();
        const category = $(this).data('category');
        
        $('.category-item').removeClass('active');
        $(this).addClass('active');
        
        filterSnippets(category);
    });

    // Snippet Search
    $('#snippet-search').on('keyup', function() {
        const search = $(this).val().toLowerCase();
        filterSnippetsBySearch(search);
    });

    // View Snippet
    $(document).on('click', '.view-snippet', function() {
        const id = $(this).data('id');
        viewSnippet(id);
    });

    // Copy Snippet
    $(document).on('click', '.copy-snippet', function() {
        const id = $(this).data('id');
        copySnippet(id);
    });

    // Copy Code Button
    $('#copy-code-btn').on('click', function() {
        const code = $('#snippet-code code').text();
        copyToClipboard(code);
    });

    // Close Modal
    $('.close-modal').on('click', function() {
        closeModal();
    });

    // Close on background click
    $('#snippet-modal').on('click', function(e) {
        if (e.target === this) {
            closeModal();
        }
    });

    // Add Custom Snippet
    $('#add-snippet-btn').on('click', function() {
        showAddSnippetForm();
    });

    // Functions
    function filterSnippets(category) {
        if (category === 'all') {
            $('.snippet-card').show();
        } else {
            $('.snippet-card').hide();
            $('.snippet-card[data-category="' + category + '"]').show();
        }
    }

    function filterSnippetsBySearch(search) {
        $('.snippet-card').each(function() {
            const title = $(this).find('h4').text().toLowerCase();
            const description = $(this).find('.snippet-description').text().toLowerCase();
            
            if (title.includes(search) || description.includes(search)) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    }

    function viewSnippet(id) {
        $.ajax({
            url: addon.ajaxurl,
            type: 'POST',
            data: {
                action: 'sofir_fetch_code_snippet',
                nonce: addon.nonce,
                snippet_id: id
            },
            success: function(response) {
                if (response.success) {
                    const snippet = response.data;
                    $('#modal-title').text(snippet.name);
                    $('#snippet-code code').text(snippet.code);
                    openModal();
                }
            },
            error: function() {
                showNotice(addon.i18n.error, 'error');
            }
        });
    }

    function copySnippet(id) {
        $.ajax({
            url: addon.ajaxurl,
            type: 'POST',
            data: {
                action: 'sofir_fetch_code_snippet',
                nonce: addon.nonce,
                snippet_id: id
            },
            success: function(response) {
                if (response.success) {
                    copyToClipboard(response.data.code);
                }
            }
        });
    }

    function copyToClipboard(text) {
        const textarea = document.createElement('textarea');
        textarea.value = text;
        document.body.appendChild(textarea);
        textarea.select();
        document.execCommand('copy');
        document.body.removeChild(textarea);
        showNotice(addon.i18n.copied, 'success');
    }

    function openModal() {
        $('#snippet-modal').fadeIn();
        $('body').css('overflow', 'hidden');
    }

    function closeModal() {
        $('#snippet-modal').fadeOut();
        $('body').css('overflow', 'auto');
    }

    function showAddSnippetForm() {
        const html = `
            <div id="custom-snippet-form" style="background: white; padding: 20px; border-radius: 8px; margin: 20px 0;">
                <h3>Add Custom Snippet</h3>
                <div style="margin-bottom: 15px;">
                    <label for="snippet-name" style="display: block; margin-bottom: 5px; font-weight: 600;">
                        Snippet Name
                    </label>
                    <input type="text" id="snippet-name" placeholder="e.g., My Custom Hook" style="width: 100%; padding: 8px; border: 1px solid #e2e8f0; border-radius: 6px;">
                </div>
                <div style="margin-bottom: 15px;">
                    <label for="snippet-code" style="display: block; margin-bottom: 5px; font-weight: 600;">
                        Code
                    </label>
                    <textarea id="snippet-code" placeholder="Paste your PHP code here..." style="width: 100%; height: 300px; padding: 8px; border: 1px solid #e2e8f0; border-radius: 6px; font-family: monospace;"></textarea>
                </div>
                <div style="display: flex; gap: 10px;">
                    <button class="button button-primary" id="save-snippet-btn">Save Snippet</button>
                    <button class="button" id="cancel-snippet-btn">Cancel</button>
                </div>
            </div>
        `;

        $('.snippets-main').prepend(html);

        $('#save-snippet-btn').on('click', function() {
            const name = $('#snippet-name').val();
            const code = $('#snippet-code').val();

            if (!name || !code) {
                showNotice('Please fill in all fields', 'error');
                return;
            }

            $.ajax({
                url: addon.ajaxurl,
                type: 'POST',
                data: {
                    action: 'sofir_save_snippet',
                    nonce: addon.nonce,
                    name: name,
                    snippet: code
                },
                success: function(response) {
                    if (response.success) {
                        showNotice(response.data.message, 'success');
                        $('#custom-snippet-form').remove();
                        setTimeout(function() {
                            location.reload();
                        }, 1500);
                    }
                },
                error: function() {
                    showNotice(addon.i18n.error, 'error');
                }
            });
        });

        $('#cancel-snippet-btn').on('click', function() {
            $('#custom-snippet-form').remove();
        });
    }

    function showNotice(message, type) {
        const noticeClass = type === 'success' ? 'notice-success' : 'notice-error';
        const notice = $(`
            <div class="notice ${noticeClass} is-dismissible">
                <p>${message}</p>
                <button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss</span></button>
            </div>
        `);

        $('.wrap').prepend(notice);

        notice.find('.notice-dismiss').on('click', function() {
            notice.fadeOut(function() {
                notice.remove();
            });
        });

        setTimeout(function() {
            notice.fadeOut(function() {
                notice.remove();
            });
        }, 5000);
    }
});
