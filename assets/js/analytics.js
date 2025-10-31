( function () {
    const config = window.SOFIR_ANALYTICS_DATA || {};

    if ( ! config.enabled ) {
        return;
    }

    const endpoint = ( config.root || '' ).replace( /\/$/, '' ) + '/sofir/v1/analytics/event';

    function sendEvent( type, selector ) {
        if ( ! selector ) {
            return;
        }

        const payload = {
            type,
            selector,
            path: window.location.pathname,
            postId: config.postId || 0,
        };

        window.fetch( endpoint, {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify( payload ),
        } ).catch( function ( error ) {
            console.warn( 'SOFIR analytics error', error );
        } );
    }

    document.addEventListener( 'click', function ( event ) {
        const target = event.target.closest( '[data-sofir-track]' );

        if ( ! target ) {
            return;
        }

        const selector = target.dataset.sofirTrack || target.getAttribute( 'data-sofir-track' ) || '';
        sendEvent( 'click', selector );
    } );
} )();
