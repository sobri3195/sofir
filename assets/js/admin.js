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
                notify( 'success', payload.message || 'Template imported.' );

                if ( payload.editUrl ) {
                    button.dataset.editUrl = payload.editUrl;
                }

                if ( payload.viewUrl ) {
                    button.dataset.viewUrl = payload.viewUrl;
                }
            } )
            .catch( function ( error ) {
                console.error( 'SOFIR import error', error );
                notify( 'error', 'Unexpected error while importing template.' );
            } )
            .finally( function () {
                setButtonBusy( button, false );
            } );
    } );
} )();
