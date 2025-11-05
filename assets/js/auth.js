(function() {
    'use strict';

    document.addEventListener('DOMContentLoaded', function() {
        var registerForms = document.querySelectorAll('.sofir-register-form');

        registerForms.forEach(function(form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                var formData = new FormData(form);
                var phoneOnly = formData.get('sofir_phone_only') === '1';
                var data = {
                    phone_only: phoneOnly
                };

                if (phoneOnly) {
                    data.phone = formData.get('sofir_phone');
                } else {
                    data.username = formData.get('sofir_username');
                    data.email = formData.get('sofir_email');
                    data.phone = formData.get('sofir_phone');
                    data.password = formData.get('sofir_password');
                }

                wp.apiFetch({
                    path: '/sofir/v1/auth/register',
                    method: 'POST',
                    data: data
                }).then(function(response) {
                    if (response.status === 'success') {
                        var redirect = response.redirect || formData.get('sofir_redirect') || window.location.href;
                        window.location.href = redirect;
                    }
                }).catch(function(error) {
                    var errorMessage = error.message || 'Unknown error';
                    if (error.data && error.data.message) {
                        errorMessage = error.data.message;
                    }
                    alert('Registration error: ' + errorMessage);
                });
            });
        });

        var tabs = document.querySelectorAll('.sofir-tab-btn');
        tabs.forEach(function(tab) {
            tab.addEventListener('click', function() {
                var targetTab = tab.dataset.tab;

                tabs.forEach(function(t) { t.classList.remove('active'); });
                tab.classList.add('active');

                var contents = document.querySelectorAll('.sofir-tab-content');
                contents.forEach(function(content) {
                    if (content.dataset.content === targetTab) {
                        content.classList.add('active');
                    } else {
                        content.classList.remove('active');
                    }
                });
            });
        });
    });
})();
