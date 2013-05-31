jQuery( document ).ready( function() {

	//Form submission - capture event and fire ajax request
	jQuery( '.wppp-js-settings-submit' ).submit( function( e ) {

		var endPoint = WPPPApiUrl + '/settings';

		e.preventDefault();

		var theForm = jQuery( this );

		jQuery.ajax( endPoint, {
			type 	: 'post',
			data	: theForm.serialize(),
			success	: function( data ) {

				console.log( data );
			}
		} );
	} );


} );