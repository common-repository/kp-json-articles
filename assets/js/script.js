jQuery( document ).ready( function( ) { 

    // manual sync button click
    jQuery( '.kpja-sync-button' ).on( 'click', function( _e ) {

        // prevent he default behavior
        _e.preventDefault( );

        // the message containner
        var _msg = jQuery( '.kpja-sync-message' );

        // setup our ajax settings
        var _set = {
            url: kp_ja_ao.ajax_url,
            type: 'get',
            data: {
                action: 'kp_ja_manual_sync',
                syncing: true
            },
            beforeSend: function( ) {

                // slide down the message container and show "Please wait"
                _msg.slideDown( 'fast', function( ) {

                    // show a please hold message
                    _msg.html( '<p>&nbsp;</p><div class="notice notice-success"><p>Please hold... <img src="/wp-admin/images/loading.gif" alt="Please hold" /></p></div>' );

                } );
            },
            success: function( response ) {

                // just in case the Hold message shows too quick
                setTimeout( function( ) { 

                    // throw a response message
                    _msg.html( '<p>&nbsp;</p>' + response );

                 }, 2000);

            },
            complete: function( ) {

                // in case this happens too fast, delay a couple seconds
                setTimeout( function( ) { 

                    // just slide it up
                    _msg.slideUp( 'fast' );

                 }, 5000);

            },
            error: function( jqXHR, textStatus, errorThrown ) {

                // throw a message error in there
		        _msg.html( '<p>&nbsp;</p><div class="notice notice-error"><h4>There was an error syncing your articles</h4><p>' + errorThrown + '</p></div>' );

            },
        }

        // post the data to our ajaxon functionality
        jQuery.ajax( _set );     


    } );

} );
