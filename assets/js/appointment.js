(function() {
    'use strict';
    
    var appointmentForms = document.querySelectorAll('.sofir-appointment-form');
    
    if (!appointmentForms.length) {
        return;
    }
    
    appointmentForms.forEach(function(form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            var submitButton = form.querySelector('.sofir-appointment-submit');
            var originalText = submitButton.textContent;
            
            submitButton.disabled = true;
            submitButton.textContent = 'Booking...';
            
            var formData = new FormData(form);
            formData.append('action', 'sofir_book_appointment');
            
            var xhr = new XMLHttpRequest();
            xhr.open('POST', sofirData.ajaxUrl);
            
            xhr.onload = function() {
                submitButton.disabled = false;
                submitButton.textContent = originalText;
                
                if (xhr.status === 200) {
                    try {
                        var response = JSON.parse(xhr.responseText);
                        
                        if (response.success) {
                            alert('Appointment booked successfully!');
                            form.reset();
                            
                            if (response.data && response.data.redirect) {
                                window.location.href = response.data.redirect;
                            }
                        } else {
                            alert('Error: ' + (response.data || 'Failed to book appointment'));
                        }
                    } catch (err) {
                        alert('Error: Invalid response from server');
                    }
                } else {
                    alert('Error: Failed to communicate with server');
                }
            };
            
            xhr.onerror = function() {
                submitButton.disabled = false;
                submitButton.textContent = originalText;
                alert('Network error. Please try again.');
            };
            
            xhr.send(formData);
        });
    });
    
    var datetimeInputs = document.querySelectorAll('input[type="datetime-local"]');
    
    datetimeInputs.forEach(function(input) {
        var now = new Date();
        var minDate = new Date(now.getTime() + (60 * 60 * 1000));
        var minDateString = minDate.toISOString().slice(0, 16);
        
        input.setAttribute('min', minDateString);
    });
})();
