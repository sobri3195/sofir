( function () {
    const data = window.SOFIR_ADMIN_DATA || {};

    function notify( status, message ) {
        if ( window.wp && window.wp.data && window.wp.data.dispatch ) {
            window.wp.data
                .dispatch( 'core/notices' )
                .createNotice( status, message, { isDismissible: true } );
        } else {
            window.alert( message );
        }
    }

    function setButtonBusy( button, busy ) {
        if ( busy ) {
            button.dataset.originalLabel = button.textContent;
            button.textContent = 'Importing…';
            button.disabled = true;
        } else {
            if ( button.dataset.originalLabel ) {
                button.textContent = button.dataset.originalLabel;
            }
            button.disabled = false;
        }
    }

    document.addEventListener( 'click', function ( event ) {
        const button = event.target.closest( '.sofir-template-import' );

        if ( ! button ) {
            return;
        }

        event.preventDefault();

        if ( ! window.ajaxurl ) {
            notify( 'error', 'Ajax endpoint not found.' );
            return;
        }

        const slug = button.dataset.template;
        const context = button.dataset.context || 'page';

        if ( ! slug ) {
            notify( 'error', 'Template slug missing.' );
            return;
        }

        const formData = new window.FormData();
        formData.append( 'action', 'sofir_import_template' );
        formData.append( 'template', slug );
        formData.append( 'context', context );
        formData.append( 'nonce', data.nonce || '' );

        setButtonBusy( button, true );

        window.fetch( window.ajaxurl, {
            method: 'POST',
            credentials: 'same-origin',
            body: formData,
        } )
            .then( function ( response ) {
                return response.json();
            } )
            .then( function ( json ) {
                if ( ! json || ! json.success ) {
                    const message = ( json && json.data && json.data.message ) || 'Import failed.';
                    notify( 'error', message );
                    return;
                }

                const { data: payload } = json;
                showSuccessModal( payload );
            } )
            .catch( function ( error ) {
                console.error( 'SOFIR import error', error );
                notify( 'error', 'Unexpected error while importing template.' );
            } )
            .finally( function () {
                setButtonBusy( button, false );
            } );
    } );

    function showSuccessModal( payload ) {
        const modal = document.createElement( 'div' );
        modal.className = 'sofir-import-modal';

        let stepsHtml = '';
        if ( payload.steps && payload.steps.length > 0 ) {
            stepsHtml = '<div class="sofir-import-modal__steps"><ul>';
            payload.steps.forEach( function ( step ) {
                stepsHtml += '<li>' + escapeHtml( step ) + '</li>';
            } );
            stepsHtml += '</ul></div>';
        }

        let actionsHtml = '<div class="sofir-import-modal__actions">';
        if ( payload.editUrl ) {
            actionsHtml += '<a href="' + escapeHtml( payload.editUrl ) + '" class="button button-primary">Edit in Gutenberg</a>';
        }
        if ( payload.viewUrl ) {
            actionsHtml += '<a href="' + escapeHtml( payload.viewUrl ) + '" class="button" target="_blank">View Page</a>';
        }
        actionsHtml += '<button type="button" class="button sofir-modal-close">Close</button>';
        actionsHtml += '</div>';

        modal.innerHTML = '<div class="sofir-import-modal__content">' +
            '<button type="button" class="sofir-import-modal__close" aria-label="Close">×</button>' +
            '<div class="sofir-import-modal__header">' +
            '<div class="sofir-import-modal__icon">✓</div>' +
            '<h2 class="sofir-import-modal__title">Import Successful!</h2>' +
            '</div>' +
            '<p class="sofir-import-modal__message">' + escapeHtml( payload.message || 'Template imported successfully.' ) + '</p>' +
            stepsHtml +
            actionsHtml +
            '</div>';

        document.body.appendChild( modal );

        modal.addEventListener( 'click', function ( event ) {
            if ( event.target === modal || event.target.classList.contains( 'sofir-modal-close' ) || event.target.classList.contains( 'sofir-import-modal__close' ) ) {
                closeModal( modal );
            }
        } );

        document.addEventListener( 'keydown', function onEscape( event ) {
            if ( event.key === 'Escape' ) {
                closeModal( modal );
                document.removeEventListener( 'keydown', onEscape );
            }
        } );
    }

    function closeModal( modal ) {
        modal.style.opacity = '0';
        setTimeout( function () {
            if ( modal.parentNode ) {
                modal.parentNode.removeChild( modal );
            }
        }, 200 );
    }

    function escapeHtml( text ) {
        const div = document.createElement( 'div' );
        div.textContent = text;
        return div.innerHTML;
    }
} )();
