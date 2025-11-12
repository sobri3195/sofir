(function() {
    'use strict';

    var countdownIntervals = new WeakMap();

    function updateCountdown(element) {
        var targetDate = element.dataset.target;
        
        if (!targetDate) {
            element.innerHTML = '<span class="countdown-error">No target date set</span>';
            return;
        }

        var target = new Date(targetDate).getTime();
        
        if (isNaN(target)) {
            element.innerHTML = '<span class="countdown-error">Invalid date format</span>';
            return;
        }

        var now = new Date().getTime();
        var distance = target - now;

        if (distance < 0) {
            element.innerHTML = '<span class="countdown-expired">Expired</span>';
            var interval = countdownIntervals.get(element);
            if (interval) {
                clearInterval(interval);
                countdownIntervals.delete(element);
            }
            return;
        }

        var days = Math.floor(distance / (1000 * 60 * 60 * 24));
        var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        var seconds = Math.floor((distance % (1000 * 60)) / 1000);

        var daysEl = element.querySelector('.days');
        var hoursEl = element.querySelector('.hours');
        var minutesEl = element.querySelector('.minutes');
        var secondsEl = element.querySelector('.seconds');

        if (daysEl) daysEl.textContent = String(days).padStart(2, '0');
        if (hoursEl) hoursEl.textContent = String(hours).padStart(2, '0');
        if (minutesEl) minutesEl.textContent = String(minutes).padStart(2, '0');
        if (secondsEl) secondsEl.textContent = String(seconds).padStart(2, '0');
    }

    function initCountdown(element) {
        if (countdownIntervals.has(element)) {
            clearInterval(countdownIntervals.get(element));
        }

        updateCountdown(element);
        
        var interval = setInterval(function() {
            updateCountdown(element);
        }, 1000);
        
        countdownIntervals.set(element, interval);
    }

    function initAllCountdowns() {
        var countdowns = document.querySelectorAll('.sofir-countdown');
        countdowns.forEach(function(countdown) {
            initCountdown(countdown);
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        initAllCountdowns();
    });

    if (window.MutationObserver) {
        var observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                mutation.addedNodes.forEach(function(node) {
                    if (node.nodeType === 1) {
                        if (node.classList && node.classList.contains('sofir-countdown')) {
                            initCountdown(node);
                        }
                        var countdowns = node.querySelectorAll && node.querySelectorAll('.sofir-countdown');
                        if (countdowns && countdowns.length > 0) {
                            countdowns.forEach(function(countdown) {
                                initCountdown(countdown);
                            });
                        }
                    }
                });
            });
        });

        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
    }

    window.sofirInitCountdowns = initAllCountdowns;
})();
