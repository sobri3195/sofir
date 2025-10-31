( function () {
    const globalData = window.SOFIR_DIRECTORY_DATA || {};

    function getApiRoot() {
        if ( window.wpApiSettings && window.wpApiSettings.root ) {
            return window.wpApiSettings.root.replace( /\/$/, '' );
        }

        if ( globalData.restRoot ) {
            return globalData.restRoot.replace( /\/$/, '' );
        }

        return window.location.origin + '/wp-json';
    }

    function fetchListings( restBase, params ) {
        const path = `/wp/v2/${ restBase }?${ params.toString() }`;
        return window.wp.apiFetch( { path } );
    }

    function buildParams( filters, formData ) {
        const params = new window.URLSearchParams();
        params.set( 'per_page', '50' );

        filters.forEach( function ( filter ) {
            if ( ! filter ) {
                return;
            }

            const value = formData.get( filter );

            if ( value && value.toString().trim() !== '' ) {
                params.set( filter, value );
            }
        } );

        return params;
    }

    function getMeta( item, key ) {
        if ( ! item || ! item.meta ) {
            return undefined;
        }

        return item.meta[ key ];
    }

    function renderList( container, items, postType ) {
        let list = container.nextElementSibling;

        if ( ! list || ! list.classList.contains( 'sofir-directory-list' ) ) {
            list = document.createElement( 'div' );
            list.className = 'sofir-directory-list';
            container.insertAdjacentElement( 'afterend', list );
        }

        if ( ! items.length ) {
            list.innerHTML = '<p>' + ( window.wp?.i18n?.__( 'No entries found.', 'sofir' ) || 'No entries found.' ) + '</p>';
            return;
        }

        const fragments = items.map( function ( item ) {
            const location = getMeta( item, `sofir_${ postType }_location` ) || {};
            const ratingMeta = getMeta( item, 'sofir_review_average' ) || getMeta( item, `sofir_${ postType }_rating` );
            const rating = ratingMeta ? Number( ratingMeta ).toFixed( 1 ) : '';

            return `
                <article class="sofir-directory-card">
                    <h4><a href="${ item.link }">${ item.title.rendered }</a></h4>
                    ${ location.address ? `<p>${ location.address }</p>` : '' }
                    ${ rating ? `<p class="sofir-directory-rating">⭐ ${ rating }</p>` : '' }
                </article>
            `;
        } );

        list.innerHTML = fragments.join( '' );
    }

    function initMap( element, items, postType ) {
        const provider = globalData.provider || 'mapbox';

        if ( 'mapbox' === provider && window.mapboxgl && globalData.mapboxToken ) {
            initializeMapbox( element, items, postType );
            return;
        }

        if ( 'google' === provider && window.google && window.google.maps && globalData.googleKey ) {
            initializeGoogle( element, items );
            return;
        }

        element.innerHTML = '<p>' + ( window.wp?.i18n?.__( 'Map provider not available. Please set API keys.', 'sofir' ) || 'Map provider not available. Please set API keys.' ) + '</p>';
    }

    function initializeMapbox( element, items, postType ) {
        try {
            window.mapboxgl.accessToken = globalData.mapboxToken;
            const map = new window.mapboxgl.Map( {
                container: element,
                style: 'mapbox://styles/mapbox/streets-v11',
                center: [ 106.816666, -6.200000 ],
                zoom: Number( element.dataset.zoom || 12 ),
            } );

            items.forEach( function ( item ) {
                const location = getMeta( item, `sofir_${ postType }_location` );

                if ( ! location || ! location.lng || ! location.lat ) {
                    return;
                }

                const popupHtml = `<strong>${ item.title.rendered }</strong><br/>${ location.address || '' }`;

                const marker = new window.mapboxgl.Marker().setLngLat( [ location.lng, location.lat ] );
                marker.setPopup( new window.mapboxgl.Popup().setHTML( popupHtml ) );
                marker.addTo( map );
            } );
        } catch ( error ) {
            console.error( 'SOFIR mapbox error', error );
            element.innerHTML = '<p>Map error.</p>';
        }
    }

    function initializeGoogle( element, items ) {
        try {
            const map = new window.google.maps.Map( element, {
                zoom: Number( element.dataset.zoom || 12 ),
                center: { lat: -6.200000, lng: 106.816666 },
            } );

            items.forEach( function ( item ) {
                const postType = element.dataset.postType;
                const location = getMeta( item, `sofir_${ postType }_location` );

                if ( ! location || ! location.lng || ! location.lat ) {
                    return;
                }

                const marker = new window.google.maps.Marker( {
                    position: { lat: Number( location.lat ), lng: Number( location.lng ) },
                    map,
                    title: item.title.rendered,
                } );

                const info = new window.google.maps.InfoWindow( {
                    content: `<strong>${ item.title.rendered }</strong><br/>${ location.address || '' }`,
                } );

                marker.addListener( 'click', function () {
                    info.open( map, marker );
                } );
            } );
        } catch ( error ) {
            console.error( 'SOFIR google map error', error );
            element.innerHTML = '<p>Map error.</p>';
        }
    }

    function attachFilterListener( form, callback ) {
        if ( ! form ) {
            return;
        }

        form.addEventListener( 'submit', function ( event ) {
            event.preventDefault();
            const formData = new window.FormData( form );
            callback( formData );
        } );
    }

    function initDirectoryInstance( element ) {
        const postType = element.dataset.postType || 'listing';
        const restBase = element.dataset.restBase || postType;
        const filters   = JSON.parse( element.dataset.filters || '[]' );
        const form      = document.querySelector( `.sofir-directory-filters[data-post-type="${ postType }"]` );

        function load( formData ) {
            const params = buildParams( filters, formData || new window.FormData() );

            fetchListings( restBase, params )
                .then( function ( items ) {
                    initMap( element, items, postType );
                    renderList( element, items, postType );
                } )
                .catch( function ( error ) {
                    console.error( 'SOFIR directory fetch failed', error );
                    element.innerHTML = '<p>Failed to load listings.</p>';
                } );
        }

        attachFilterListener( form, function ( formData ) {
            load( formData );
        } );

        load( form ? new window.FormData( form ) : new window.FormData() );
    }

    document.addEventListener( 'DOMContentLoaded', function () {
        if ( ! window.wp || ! window.wp.apiFetch ) {
            console.warn( 'wp.apiFetch not available' );
            return;
        }

        const maps = document.querySelectorAll( '.sofir-directory-map' );

        maps.forEach( function ( element ) {
            initDirectoryInstance( element );
        } );
    } );
} )();
