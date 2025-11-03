(function() {
    'use strict';

    document.addEventListener('DOMContentLoaded', function() {
        var searches = document.querySelectorAll('.sofir-quick-search');

        searches.forEach(function(container) {
            var input = container.querySelector('input');
            var results = container.querySelector('.sofir-quick-search-results');
            var postType = container.dataset.postType || 'post';
            var timeout = null;

            input.addEventListener('input', function() {
                clearTimeout(timeout);
                var query = input.value.trim();

                if (query.length < 3) {
                    results.innerHTML = '';
                    return;
                }

                timeout = setTimeout(function() {
                    wp.apiFetch({
                        path: '/wp/v2/' + postType + '?search=' + encodeURIComponent(query) + '&per_page=5'
                    }).then(function(posts) {
                        results.innerHTML = '';
                        if (posts.length === 0) {
                            results.innerHTML = '<div class="no-results">No results found</div>';
                            return;
                        }

                        posts.forEach(function(post) {
                            var item = document.createElement('a');
                            item.className = 'sofir-search-result';
                            item.href = post.link;
                            item.innerHTML = '<strong>' + post.title.rendered + '</strong>';
                            results.appendChild(item);
                        });
                    }).catch(function(error) {
                        console.error('Search error:', error);
                    });
                }, 300);
            });
        });
    });
})();
