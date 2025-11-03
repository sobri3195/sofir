(function() {
    'use strict';

    document.addEventListener('DOMContentLoaded', function() {
        var forms = document.querySelectorAll('.sofir-post-form');

        forms.forEach(function(form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                var formData = new FormData(form);
                var data = {
                    title: formData.get('post_title'),
                    content: formData.get('post_content'),
                    status: 'publish'
                };

                var postType = form.dataset.postType || 'post';

                wp.apiFetch({
                    path: '/wp/v2/' + postType,
                    method: 'POST',
                    data: data
                }).then(function(response) {
                    alert('Post created successfully!');
                    form.reset();
                    if (response.link) {
                        window.location.href = response.link;
                    }
                }).catch(function(error) {
                    alert('Error creating post: ' + (error.message || 'Unknown error'));
                });
            });
        });
    });
})();
