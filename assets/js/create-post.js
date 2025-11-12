(function() {
    'use strict';

    function initCreatePostForms() {
        var forms = document.querySelectorAll('.sofir-post-form');

        forms.forEach(function(form) {
            if (form.dataset.sofirInitialized) {
                return;
            }
            form.dataset.sofirInitialized = 'true';

            form.addEventListener('submit', function(e) {
                e.preventDefault();

                var submitButton = form.querySelector('button[type="submit"]');
                var originalText = submitButton ? submitButton.textContent : '';
                
                if (submitButton) {
                    submitButton.disabled = true;
                    submitButton.textContent = 'Creating...';
                }

                var formData = new FormData(form);
                var title = formData.get('post_title');
                var content = formData.get('post_content');
                
                if (!title || title.trim() === '') {
                    alert('Please enter a title');
                    if (submitButton) {
                        submitButton.disabled = false;
                        submitButton.textContent = originalText;
                    }
                    return;
                }

                var data = {
                    title: title,
                    content: content || '',
                    status: 'publish'
                };

                var postType = form.dataset.postType || 'post';
                var restBase = postType;
                
                if (postType === 'post') {
                    restBase = 'posts';
                }

                if (typeof wp !== 'undefined' && wp.apiFetch) {
                    wp.apiFetch({
                        path: '/wp/v2/' + restBase,
                        method: 'POST',
                        data: data
                    }).then(function(response) {
                        alert('Post created successfully!');
                        form.reset();
                        if (response.link) {
                            window.location.href = response.link;
                        }
                    }).catch(function(error) {
                        console.error('Create post error:', error);
                        alert('Error creating post: ' + (error.message || 'Unknown error'));
                        if (submitButton) {
                            submitButton.disabled = false;
                            submitButton.textContent = originalText;
                        }
                    });
                } else {
                    var xhr = new XMLHttpRequest();
                    xhr.open('POST', wpApiSettings.root + 'wp/v2/' + restBase);
                    xhr.setRequestHeader('Content-Type', 'application/json');
                    xhr.setRequestHeader('X-WP-Nonce', wpApiSettings.nonce);
                    
                    xhr.onload = function() {
                        if (submitButton) {
                            submitButton.disabled = false;
                            submitButton.textContent = originalText;
                        }
                        
                        if (xhr.status >= 200 && xhr.status < 300) {
                            var response = JSON.parse(xhr.responseText);
                            alert('Post created successfully!');
                            form.reset();
                            if (response.link) {
                                window.location.href = response.link;
                            }
                        } else {
                            var error = JSON.parse(xhr.responseText);
                            alert('Error creating post: ' + (error.message || 'Unknown error'));
                        }
                    };
                    
                    xhr.onerror = function() {
                        if (submitButton) {
                            submitButton.disabled = false;
                            submitButton.textContent = originalText;
                        }
                        alert('Error creating post: Network error');
                    };
                    
                    xhr.send(JSON.stringify(data));
                }
            });
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        initCreatePostForms();
    });

    if (window.MutationObserver) {
        var observer = new MutationObserver(function(mutations) {
            var shouldInit = false;
            mutations.forEach(function(mutation) {
                mutation.addedNodes.forEach(function(node) {
                    if (node.nodeType === 1) {
                        if (node.classList && node.classList.contains('sofir-post-form')) {
                            shouldInit = true;
                        }
                        var forms = node.querySelectorAll && node.querySelectorAll('.sofir-post-form');
                        if (forms && forms.length > 0) {
                            shouldInit = true;
                        }
                    }
                });
            });
            if (shouldInit) {
                initCreatePostForms();
            }
        });

        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
    }

    window.sofirInitCreatePostForms = initCreatePostForms;
})();
