(function() {
    'use strict';

    function updateCountdown(element) {
        var target = new Date(element.dataset.target).getTime();
        var now = new Date().getTime();
        var distance = target - now;

        if (distance < 0) {
            element.innerHTML = '<span class="countdown-expired">Expired</span>';
            return;
        }

        var days = Math.floor(distance / (1000 * 60 * 60 * 24));
        var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        var seconds = Math.floor((distance % (1000 * 60)) / 1000);

        element.querySelector('.days').textContent = String(days).padStart(2, '0');
        element.querySelector('.hours').textContent = String(hours).padStart(2, '0');
        element.querySelector('.minutes').textContent = String(minutes).padStart(2, '0');
        element.querySelector('.seconds').textContent = String(seconds).padStart(2, '0');
    }

    document.addEventListener('DOMContentLoaded', function() {
        var countdowns = document.querySelectorAll('.sofir-countdown');

        countdowns.forEach(function(countdown) {
            updateCountdown(countdown);
            setInterval(function() {
                updateCountdown(countdown);
            }, 1000);
        });
    });
})();
