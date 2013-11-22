jQuery( document ).ready( function() {

	//Form submission - capture event and fire ajax request
	jQuery( '.wppp-js-settings-submit' ).submit( function( e ) {

		var endPoint = WPPPApiUrl + '/settings';

		e.preventDefault();

		var theForm = jQuery( this );

		jQuery( '.wppp-js-ajax-status' ).addClass( 'wppp-ajax-loading' );

		jQuery.ajax( endPoint, {
			type 	: 'post',
			data	: theForm.serialize(),
			success	: function( data ) {

				jQuery( '.wppp-js-ajax-status' ).removeClass( 'wppp-ajax-loading' );
			}
		} );
	} );

} );