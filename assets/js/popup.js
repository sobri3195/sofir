(function() {
    'use strict';

    document.addEventListener('DOMContentLoaded', function() {
        var triggers = document.querySelectorAll('.sofir-popup-trigger');

        triggers.forEach(function(trigger) {
            trigger.addEventListener('click', function(e) {
                e.preventDefault();
                var popupId = trigger.dataset.popup;
                var popup = document.getElementById(popupId);
                if (popup) {
                    popup.style.display = 'flex';
                }
            });
        });

        var closeBtns = document.querySelectorAll('.sofir-popup-close');
        closeBtns.forEach(function(btn) {
            btn.addEventListener('click', function() {
                var popup = btn.closest('.sofir-popup-modal');
                if (popup) {
                    popup.style.display = 'none';
                }
            });
        });

        var modals = document.querySelectorAll('.sofir-popup-modal');
        modals.forEach(function(modal) {
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    modal.style.display = 'none';
                }
            });
        });
    });
})();
