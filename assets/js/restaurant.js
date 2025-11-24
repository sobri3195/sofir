(function() {
    'use strict';

    if (typeof wp === 'undefined' || !wp.apiFetch) {
        console.error('SOFIR Restaurant: wp.apiFetch not available');
        return;
    }

    const { apiFetch } = wp;
    const { restRoot, nonce } = SOFIR_RESTAURANT_DATA || {};

    if (restRoot && nonce) {
        apiFetch.use(apiFetch.createNonceMiddleware(nonce));
        apiFetch.use(apiFetch.createRootURLMiddleware(restRoot));
    }

    document.addEventListener('DOMContentLoaded', function() {
        const orderForm = document.getElementById('sofir-restaurant-order-form');
        
        if (!orderForm) {
            return;
        }

        orderForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(orderForm);
            const data = {
                action: 'sofir_create_order',
                order_type: formData.get('order_type'),
                customer_name: formData.get('customer_name'),
                customer_phone: formData.get('customer_phone'),
                customer_address: formData.get('customer_address'),
                table_number: formData.get('table_number'),
                notes: formData.get('notes'),
                items: JSON.stringify([])
            };

            const submitButton = orderForm.querySelector('button[type="submit"]');
            if (submitButton) {
                submitButton.disabled = true;
                submitButton.textContent = 'Processing...';
            }

            fetch(ajaxurl || '/wp-admin/admin-ajax.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams(data)
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    alert(result.data.message || 'Order created successfully!');
                    orderForm.reset();
                } else {
                    alert(result.data || 'Failed to create order.');
                }
            })
            .catch(error => {
                console.error('Order error:', error);
                alert('An error occurred. Please try again.');
            })
            .finally(() => {
                if (submitButton) {
                    submitButton.disabled = false;
                    submitButton.textContent = 'Submit Order';
                }
            });
        });
    });
})();
