(function($) {
    'use strict';

    class SofirForms {
        constructor() {
            this.init();
        }

        init() {
            this.initRatingFields();
            this.initSignatureFields();
            this.initConditionalLogic();
            this.initFormValidation();
            this.handleFormSuccess();
        }

        initRatingFields() {
            $('.sofir-rating-field').each(function() {
                const $ratingField = $(this);
                const $stars = $ratingField.find('.sofir-star');
                const fieldName = $ratingField.data('field');
                const $input = $(`input[name="${fieldName}"]`);

                $stars.on('click', function() {
                    const value = $(this).data('value');
                    $input.val(value);
                    
                    $stars.removeClass('active');
                    $stars.slice(0, value).addClass('active');
                });

                $stars.on('mouseenter', function() {
                    const value = $(this).data('value');
                    $stars.slice(0, value).css('color', '#f0ad4e');
                });

                $ratingField.on('mouseleave', function() {
                    const currentValue = parseInt($input.val()) || 0;
                    $stars.css('color', '#ddd');
                    $stars.slice(0, currentValue).css('color', '#f0ad4e');
                });
            });
        }

        initSignatureFields() {
            $('.sofir-signature-pad').each(function() {
                const $pad = $(this);
                const canvas = $pad.find('canvas')[0];
                if (!canvas) return;

                const ctx = canvas.getContext('2d');
                let isDrawing = false;
                let lastX = 0;
                let lastY = 0;

                function getMousePos(e) {
                    const rect = canvas.getBoundingClientRect();
                    const scaleX = canvas.width / rect.width;
                    const scaleY = canvas.height / rect.height;
                    
                    return {
                        x: (e.clientX - rect.left) * scaleX,
                        y: (e.clientY - rect.top) * scaleY
                    };
                }

                canvas.addEventListener('mousedown', function(e) {
                    isDrawing = true;
                    const pos = getMousePos(e);
                    lastX = pos.x;
                    lastY = pos.y;
                });

                canvas.addEventListener('mousemove', function(e) {
                    if (!isDrawing) return;

                    const pos = getMousePos(e);
                    
                    ctx.beginPath();
                    ctx.moveTo(lastX, lastY);
                    ctx.lineTo(pos.x, pos.y);
                    ctx.strokeStyle = '#000';
                    ctx.lineWidth = 2;
                    ctx.lineCap = 'round';
                    ctx.stroke();

                    lastX = pos.x;
                    lastY = pos.y;
                });

                canvas.addEventListener('mouseup', function() {
                    isDrawing = false;
                    const fieldName = $pad.find('input[type="hidden"]').attr('name');
                    const dataURL = canvas.toDataURL();
                    $pad.find('input[type="hidden"]').val(dataURL);
                });

                canvas.addEventListener('mouseleave', function() {
                    isDrawing = false;
                });

                $pad.find('.sofir-clear-signature').on('click', function() {
                    ctx.clearRect(0, 0, canvas.width, canvas.height);
                    $pad.find('input[type="hidden"]').val('');
                });

                canvas.addEventListener('touchstart', function(e) {
                    e.preventDefault();
                    const touch = e.touches[0];
                    const mouseEvent = new MouseEvent('mousedown', {
                        clientX: touch.clientX,
                        clientY: touch.clientY
                    });
                    canvas.dispatchEvent(mouseEvent);
                });

                canvas.addEventListener('touchmove', function(e) {
                    e.preventDefault();
                    const touch = e.touches[0];
                    const mouseEvent = new MouseEvent('mousemove', {
                        clientX: touch.clientX,
                        clientY: touch.clientY
                    });
                    canvas.dispatchEvent(mouseEvent);
                });

                canvas.addEventListener('touchend', function(e) {
                    e.preventDefault();
                    const mouseEvent = new MouseEvent('mouseup', {});
                    canvas.dispatchEvent(mouseEvent);
                });
            });
        }

        initConditionalLogic() {
            $('[data-conditional-field]').each(function() {
                const $field = $(this);
                const conditionalFieldName = $field.data('conditional-field');
                const conditionalValue = $field.data('conditional-value');
                const $conditionalField = $(`[name="${conditionalFieldName}"]`);

                function checkCondition() {
                    let currentValue;
                    
                    if ($conditionalField.attr('type') === 'radio' || $conditionalField.attr('type') === 'checkbox') {
                        currentValue = $conditionalField.filter(':checked').val();
                    } else {
                        currentValue = $conditionalField.val();
                    }

                    if (currentValue === conditionalValue) {
                        $field.show();
                    } else {
                        $field.hide();
                    }
                }

                $conditionalField.on('change', checkCondition);
                checkCondition();
            });
        }

        initFormValidation() {
            $('.sofir-custom-form').on('submit', function(e) {
                const $form = $(this);
                let isValid = true;

                $form.find('[required]').each(function() {
                    const $field = $(this);
                    
                    if ($field.is('input[type="hidden"]') && $field.closest('.sofir-rating-field').length) {
                        if (!$field.val()) {
                            isValid = false;
                            $field.closest('.sofir-form-field').find('label').css('color', '#d63638');
                        }
                    } else if (!$field.val()) {
                        isValid = false;
                        $field.css('border-color', '#d63638');
                    } else {
                        $field.css('border-color', '#ddd');
                    }
                });

                if (!isValid) {
                    e.preventDefault();
                    $('.sofir-form-message')
                        .addClass('error')
                        .text('Please fill in all required fields.')
                        .show();
                    return false;
                }
            });
        }

        handleFormSuccess() {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('form_submitted') === '1') {
                $('.sofir-form-message')
                    .addClass('success')
                    .text('Thank you! Your form has been submitted successfully.')
                    .show();
            }
        }
    }

    $(document).ready(function() {
        new SofirForms();
    });

})(jQuery);
