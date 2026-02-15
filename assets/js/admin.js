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

    function handlePreview( element ) {
        if ( ! window.ajaxurl ) {
            notify( 'error', 'Ajax endpoint not found.' );
            return;
        }

        const slug = element.dataset.template;

        if ( ! slug ) {
            notify( 'error', 'Template slug missing.' );
            return;
        }

        const formData = new window.FormData();
        formData.append( 'action', 'sofir_preview_template' );
        formData.append( 'template', slug );
        formData.append( 'nonce', data.nonce || '' );

        if ( element.classList.contains( 'button' ) ) {
            setButtonBusy( element, true );
            element.textContent = 'Loading Preview…';
        }

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
                    const message = ( json && json.data && json.data.message ) || 'Preview failed.';
                    notify( 'error', message );
                    return;
                }

                const { data: payload } = json;
                showPreviewModal( payload );
            } )
            .catch( function ( error ) {
                console.error( 'SOFIR preview error', error );
                notify( 'error', 'Unexpected error while loading preview.' );
            } )
            .finally( function () {
                if ( element.classList.contains( 'button' ) ) {
                    setButtonBusy( element, false );
                    element.textContent = 'Preview';
                }
            } );
    }

    document.addEventListener( 'click', function ( event ) {
        const button = event.target.closest( '.sofir-template-preview' );
        const trigger = event.target.closest( '.sofir-template-preview-trigger' );

        if ( button ) {
            event.preventDefault();
            handlePreview( button );
        } else if ( trigger ) {
            event.preventDefault();
            handlePreview( trigger );
        }
    } );

    document.addEventListener( 'keydown', function ( event ) {
        if ( event.key !== 'Enter' && event.key !== ' ' ) {
            return;
        }

        const trigger = event.target.closest( '.sofir-template-preview-trigger' );

        if ( ! trigger ) {
            return;
        }

        event.preventDefault();
        handlePreview( trigger );
    } );

    function showPreviewModal( payload ) {
        const modal = document.createElement( 'div' );
        modal.className = 'sofir-preview-modal';

        modal.innerHTML = '<div class="sofir-preview-modal__content">' +
            '<div class="sofir-preview-modal__header">' +
            '<h2 class="sofir-preview-modal__title">' + escapeHtml( payload.title || 'Template Preview' ) + '</h2>' +
            '<button type="button" class="sofir-preview-modal__close" aria-label="Close">×</button>' +
            '</div>' +
            '<div class="sofir-preview-modal__body">' +
            '<div class="sofir-preview-loading">Loading preview...</div>' +
            '<iframe class="sofir-preview-modal__iframe" frameborder="0" style="opacity: 0;"></iframe>' +
            '</div>' +
            '</div>';

        document.body.appendChild( modal );

        const iframe = modal.querySelector( '.sofir-preview-modal__iframe' );
        const loading = modal.querySelector( '.sofir-preview-loading' );
        const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;

        iframeDoc.open();
        iframeDoc.write( '<!DOCTYPE html><html><head>' +
            '<meta name="viewport" content="width=device-width, initial-scale=1.0">' +
            '<style>' +
            'body{margin:0;padding:40px;font-family:system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,sans-serif;line-height:1.6;color:#1e293b;background:#f8fafc;}' +
            '*{box-sizing:border-box;}' +
            'img{max-width:100%;height:auto;display:block;}' +
            'h1,h2,h3,h4,h5,h6{margin-top:0;line-height:1.3;}' +
            '.wp-block-group{margin-bottom:2em;}' +
            '.wp-block-columns{display:flex;flex-wrap:wrap;gap:20px;}' +
            '.wp-block-column{flex:1;min-width:200px;}' +
            '.wp-block-button__link{display:inline-block;padding:12px 24px;border-radius:6px;text-decoration:none;background:#3858e9;color:#fff;font-weight:500;}' +
            '</style>' +
            '<link rel="stylesheet" href="' + ( data.themeStyleUrl || '' ) + '">' +
            '</head><body>' + payload.content + '</body></html>' );
        iframeDoc.close();

        setTimeout( function () {
            if ( loading && loading.parentNode ) {
                loading.style.opacity = '0';
                setTimeout( function () {
                    if ( loading.parentNode ) {
                        loading.parentNode.removeChild( loading );
                    }
                }, 300 );
            }
            iframe.style.transition = 'opacity 0.3s ease';
            iframe.style.opacity = '1';
        }, 500 );

        modal.addEventListener( 'click', function ( event ) {
            if ( event.target === modal || event.target.classList.contains( 'sofir-preview-modal__close' ) ) {
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

    document.addEventListener( 'click', function ( event ) {
        const button = event.target.closest( '.sofir-template-copy' );

        if ( ! button ) {
            return;
        }

        event.preventDefault();

        if ( ! window.ajaxurl ) {
            notify( 'error', 'Ajax endpoint not found.' );
            return;
        }

        const slug = button.dataset.template;

        if ( ! slug ) {
            notify( 'error', 'Template slug missing.' );
            return;
        }

        const formData = new window.FormData();
        formData.append( 'action', 'sofir_copy_pattern' );
        formData.append( 'template', slug );
        formData.append( 'nonce', data.nonce || '' );

        setButtonBusy( button, true );
        button.textContent = 'Copying…';

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
                    const message = ( json && json.data && json.data.message ) || 'Copy failed.';
                    notify( 'error', message );
                    return;
                }

                const { data: payload } = json;

                if ( navigator.clipboard && navigator.clipboard.writeText ) {
                    navigator.clipboard.writeText( payload.content ).then( function () {
                        notify( 'success', payload.message || 'Pattern copied to clipboard!' );
                        button.textContent = '✓ Copied!';
                        setTimeout( function () {
                            button.textContent = 'Copy Pattern';
                        }, 2000 );
                    } ).catch( function () {
                        showCopyTextarea( payload.content );
                    } );
                } else {
                    showCopyTextarea( payload.content );
                }
            } )
            .catch( function ( error ) {
                console.error( 'SOFIR copy error', error );
                notify( 'error', 'Unexpected error while copying pattern.' );
            } )
            .finally( function () {
                setButtonBusy( button, false );
            } );
    } );

    function showCopyTextarea( content ) {
        const modal = document.createElement( 'div' );
        modal.className = 'sofir-copy-modal';

        modal.innerHTML = '<div class="sofir-copy-modal__content">' +
            '<button type="button" class="sofir-copy-modal__close" aria-label="Close">×</button>' +
            '<h2>Copy Pattern Code</h2>' +
            '<p>Select all text and copy manually:</p>' +
            '<textarea class="sofir-copy-modal__textarea" readonly>' + escapeHtml( content ) + '</textarea>' +
            '<button type="button" class="button button-primary sofir-copy-select-all">Select All</button>' +
            '</div>';

        document.body.appendChild( modal );

        const textarea = modal.querySelector( '.sofir-copy-modal__textarea' );
        const selectBtn = modal.querySelector( '.sofir-copy-select-all' );

        selectBtn.addEventListener( 'click', function () {
            textarea.select();
        } );

        modal.addEventListener( 'click', function ( event ) {
            if ( event.target === modal || event.target.classList.contains( 'sofir-copy-modal__close' ) ) {
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

    document.addEventListener( 'click', function ( event ) {
        const button = event.target.closest( '.sofir-copy-webhook' );
        if ( ! button ) {
            return;
        }

        event.preventDefault();

        const url = button.dataset.url;
        if ( ! url ) {
            return;
        }

        if ( navigator.clipboard && navigator.clipboard.writeText ) {
            navigator.clipboard.writeText( url ).then( function () {
                const originalText = button.textContent;
                button.textContent = '✓ Copied!';
                button.style.backgroundColor = '#00a32a';
                button.style.color = '#fff';

                setTimeout( function () {
                    button.textContent = originalText;
                    button.style.backgroundColor = '';
                    button.style.color = '';
                }, 2000 );
            } ).catch( function ( error ) {
                console.error( 'Copy failed:', error );
                alert( 'Failed to copy. Please copy manually.' );
            } );
        } else {
            const input = button.parentElement.parentElement.querySelector( 'input[readonly]' );
            if ( input ) {
                input.select();
                try {
                    document.execCommand( 'copy' );
                    const originalText = button.textContent;
                    button.textContent = '✓ Copied!';
                    setTimeout( function () {
                        button.textContent = originalText;
                    }, 2000 );
                } catch ( error ) {
                    console.error( 'Copy failed:', error );
                    alert( 'Failed to copy. Please copy manually.' );
                }
            }
        }
    } );

    // Form Builder JavaScript
    if ( document.getElementById( 'sofir-form-builder' ) ) {
        let fieldIndex = 0;
        
        // Initialize form builder
        function initFormBuilder() {
            const container = document.getElementById( 'form-fields-container' );
            const addButton = document.getElementById( 'add-field' );
            
            if ( ! container || ! addButton ) return;
            
            // Add field button click
            addButton.addEventListener( 'click', function() {
                addNewField();
            } );
            
            // Remove field button click
            container.addEventListener( 'click', function( e ) {
                if ( e.target.classList.contains( 'remove-field' ) ) {
                    const fieldEditor = e.target.closest( '.field-editor' );
                    if ( fieldEditor ) {
                        fieldEditor.remove();
                    }
                }
            } );
            
            // Field type change
            container.addEventListener( 'change', function( e ) {
                if ( e.target.classList.contains( 'field-type-select' ) ) {
                    const fieldType = e.target.value;
                    const optionsRow = e.target.closest( '.field-editor' ).querySelector( '.field-options-row' );
                    
                    if ( optionsRow ) {
                        const showOptions = [ 'select', 'radio', 'checkbox', 'multiselect', 'payment_method' ].includes( fieldType );
                        optionsRow.style.display = showOptions ? '' : 'none';
                    }
                }
            } );
            
            // Get initial field count
            fieldIndex = container.querySelectorAll( '.field-editor' ).length;
        }
        
        // Add new field
        function addNewField() {
            const container = document.getElementById( 'form-fields-container' );
            if ( ! container ) return;
            
            const fieldHtml = `
                <div class="field-editor" data-index="${fieldIndex}">
                    <div class="field-header">
                        <h3>Field #${fieldIndex + 1}</h3>
                        <button type="button" class="button remove-field">Remove</button>
                    </div>
                    
                    <table class="form-table">
                        <tr>
                            <th><label>Field Type</label></th>
                            <td>
                                <select name="form_fields[${fieldIndex}][type]" class="field-type-select">
                                    <option value="text">Text</option>
                                    <option value="email">Email</option>
                                    <option value="tel">Phone</option>
                                    <option value="number">Number</option>
                                    <option value="textarea">Textarea</option>
                                    <option value="url">URL</option>
                                    <option value="password">Password</option>
                                    <option value="select">Select Dropdown</option>
                                    <option value="radio">Radio Buttons</option>
                                    <option value="checkbox">Checkbox</option>
                                    <option value="multiselect">Multi Select</option>
                                    <option value="date">Date</option>
                                    <option value="time">Time</option>
                                    <option value="datetime">Date & Time</option>
                                    <option value="file">File Upload</option>
                                    <option value="rating">Rating</option>
                                    <option value="range">Range Slider</option>
                                    <option value="calculation">Calculation</option>
                                    <option value="repeater">Repeater</option>
                                    <option value="terms">Terms & Conditions</option>
                                    <option value="payment_amount">Payment Amount</option>
                                    <option value="payment_method">Payment Method</option>
                                    <option value="hidden">Hidden</option>
                                    <option value="html">HTML</option>
                                    <option value="section">Section</option>
                                    <option value="signature">Signature</option>
                                </select>
                            </td>
                        </tr>
                        
                        <tr>
                            <th><label>Field Label</label></th>
                            <td><input type="text" name="form_fields[${fieldIndex}][label]" class="regular-text" /></td>
                        </tr>
                        
                        <tr>
                            <th><label>Field Name</label></th>
                            <td><input type="text" name="form_fields[${fieldIndex}][name]" class="regular-text" /></td>
                        </tr>
                        
                        <tr>
                            <th><label>Placeholder</label></th>
                            <td><input type="text" name="form_fields[${fieldIndex}][placeholder]" class="regular-text" /></td>
                        </tr>
                        
                        <tr class="field-options-row" style="display:none;">
                            <th><label>Options</label></th>
                            <td>
                                <textarea name="form_fields[${fieldIndex}][options]" rows="4" class="large-text" placeholder="Enter options one per line"></textarea>
                                <p class="description">Enter one option per line</p>
                            </td>
                        </tr>
                        
                        <tr>
                            <th><label>Required</label></th>
                            <td>
                                <label>
                                    <input type="checkbox" name="form_fields[${fieldIndex}][required]" value="1" />
                                    Make this field required
                                </label>
                            </td>
                        </tr>
                        
                        <tr>
                            <th><label>Description</label></th>
                            <td><textarea name="form_fields[${fieldIndex}][description]" rows="2" class="large-text"></textarea></td>
                        </tr>
                    </table>
                </div>
            `;
            
            container.insertAdjacentHTML( 'beforeend', fieldHtml );
            fieldIndex++;
        }
        
        // Initialize form builder
        initFormBuilder();
    }
    
    // Form settings tabs
    const tabs = document.querySelectorAll( '.sofir-form-settings-tabs .nav-tab' );
    const tabContents = document.querySelectorAll( '.tab-content' );
    
    if ( tabs.length > 0 && tabContents.length > 0 ) {
        tabs.forEach( function( tab ) {
            tab.addEventListener( 'click', function( e ) {
                e.preventDefault();
                
                const targetId = this.getAttribute( 'href' ).substring( 1 );
                
                // Remove active class from all tabs and contents
                tabs.forEach( function( t ) {
                    t.classList.remove( 'nav-tab-active' );
                } );
                tabContents.forEach( function( content ) {
                    content.style.display = 'none';
                } );
                
                // Add active class to clicked tab
                this.classList.add( 'nav-tab-active' );
                
                // Show target content
                const targetContent = document.getElementById( targetId );
                if ( targetContent ) {
                    targetContent.style.display = 'block';
                }
            } );
        } );
    }
    
    // Conditional logic for checkboxes
    const checkboxes = document.querySelectorAll( 'input[type="checkbox"][name*="enable_"]' );
    checkboxes.forEach( function( checkbox ) {
        const optionsDiv = checkbox.closest( 'tr' ).querySelector( 'div[class*="-options"]' );
        if ( optionsDiv ) {
            checkbox.addEventListener( 'change', function() {
                optionsDiv.style.display = this.checked ? '' : 'none';
            } );
            
            // Initialize visibility
            optionsDiv.style.display = checkbox.checked ? '' : 'none';
        }
    } );
    
    // Confirmation type change
    const confirmationType = document.querySelector( 'select[name="confirmation_type"]' );
    if ( confirmationType ) {
        function updateConfirmationOptions() {
            const type = confirmationType.value;
            const messageRow = document.querySelector( '.confirmation-message-row' );
            const redirectRow = document.querySelector( '.confirmation-redirect-row' );
            const pageRow = document.querySelector( '.confirmation-page-row' );
            
            if ( messageRow ) messageRow.style.display = type === 'message' ? '' : 'none';
            if ( redirectRow ) redirectRow.style.display = type === 'redirect' ? '' : 'none';
            if ( pageRow ) pageRow.style.display = type === 'page' ? '' : 'none';
        }
        
        confirmationType.addEventListener( 'change', updateConfirmationOptions );
        updateConfirmationOptions();
    }
} )();
