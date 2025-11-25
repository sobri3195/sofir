(function() {
    'use strict';

    if (typeof wp === 'undefined' || !wp.apiFetch) {
        console.error('SOFIR E-Course: wp.apiFetch not available');
        return;
    }

    const { apiFetch } = wp;
    const { restRoot, nonce, userId } = SOFIR_ECOURSE_DATA || {};

    if (restRoot && nonce) {
        apiFetch.use(apiFetch.createNonceMiddleware(nonce));
        apiFetch.use(apiFetch.createRootURLMiddleware(restRoot));
    }

    document.addEventListener('DOMContentLoaded', function() {
        const enrollButtons = document.querySelectorAll('.sofir-enroll-btn');
        const completeLessonButtons = document.querySelectorAll('.sofir-complete-lesson-btn');

        enrollButtons.forEach(function(button) {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                
                const courseId = button.getAttribute('data-course-id');
                
                if (!userId) {
                    alert('Please log in to enroll in courses.');
                    return;
                }

                button.disabled = true;
                button.textContent = 'Enrolling...';

                apiFetch({
                    path: '/sofir/v1/ecourse/enrollment',
                    method: 'POST',
                    data: {
                        course_id: courseId
                    }
                })
                .then(response => {
                    alert(response.message || 'Successfully enrolled!');
                    location.reload();
                })
                .catch(error => {
                    console.error('Enrollment error:', error);
                    alert(error.message || 'Failed to enroll. Please try again.');
                    button.disabled = false;
                    button.textContent = 'Enroll Now';
                });
            });
        });

        completeLessonButtons.forEach(function(button) {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                
                const lessonId = button.getAttribute('data-lesson-id');
                
                button.disabled = true;
                button.textContent = 'Marking complete...';

                apiFetch({
                    path: `/sofir/v1/ecourse/lesson/${lessonId}/complete`,
                    method: 'POST'
                })
                .then(response => {
                    button.textContent = '✓ Completed';
                    button.classList.add('completed');
                    
                    const progressBar = document.querySelector('.sofir-course-progress');
                    if (progressBar && response.progress) {
                        const progressFill = progressBar.querySelector('div > div');
                        if (progressFill) {
                            progressFill.style.width = response.progress + '%';
                        }
                        const progressText = progressBar.querySelector('.sofir-points-label');
                        if (progressText) {
                            progressText.textContent = Math.round(response.progress) + '%';
                        }
                    }
                })
                .catch(error => {
                    console.error('Complete lesson error:', error);
                    alert(error.message || 'Failed to mark as complete.');
                    button.disabled = false;
                    button.textContent = 'Mark as Complete';
                });
            });
        });
    });
})();
