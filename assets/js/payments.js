(function() {
    'use strict';

    document.addEventListener('DOMContentLoaded', function() {
        var paymentForms = document.querySelectorAll('.sofir-payment-form');

        paymentForms.forEach(function(form) {
            var submitBtn = form.querySelector('.sofir-payment-submit');
            if (!submitBtn) return;

            submitBtn.addEventListener('click', function(e) {
                e.preventDefault();

                var gateway = form.querySelector('input[name="payment_gateway"]:checked');
                if (!gateway) {
                    alert('Please select a payment method');
                    return;
                }

                var data = {
                    gateway: gateway.value,
                    amount: parseFloat(form.dataset.amount),
                    item_name: form.dataset.item,
                    return_url: form.dataset.return
                };

                wp.apiFetch({
                    path: '/sofir/v1/payments/create',
                    method: 'POST',
                    data: data
                }).then(function(response) {
                    if (response.status === 'success' || response.status === 'pending') {
                        if (response.payment_url) {
                            window.location.href = response.payment_url;
                        } else {
                            alert(response.instructions || response.message);
                        }
                    } else if (response.status === 'redirect') {
                        window.location.href = response.payment_url;
                    }
                }).catch(function(error) {
                    alert('Payment error: ' + (error.message || 'Unknown error'));
                });
            });
        });
    });
})();
