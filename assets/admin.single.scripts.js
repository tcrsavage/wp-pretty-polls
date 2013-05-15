jQuery( document ).ready( function( $ ) {

	jQuery( '.wppp-js-single-edit-form' ).submit( function( e ) {

		var endPoint = ( WPPPPollId ) ?  WPPPApiUrl + '/' + WPPPPollId : WPPPApiUrl;

		e.preventDefault();

		var theForm = jQuery( this );

		jQuery.ajax( endPoint, {
			type 	: 'post',
			data	: theForm.serialize(),
			success	: function( data ) {
			}
		} );
	} );

	jQuery( '.wppp-js-single-edit-form' ).on( 'click', '.wppp-js-delete-option', function( e ) {

		jQuery( this ).closest( 'tr' ).remove();
	} );

	jQuery( '.wppp-js-add-option' ).click( function() {

		WPPPPollOptionCount++;

		var newOption = jQuery( '.wppp-js-option-template' ).clone();

		newOption.find( 'label' ).html( 'Option ' + WPPPPollOptionCount );
		newOption.find( 'input' ).attr( 'name', 'wppp_option_' + WPPPPollOptionCount );
		newOption.removeClass( 'wppp-js-option-template' ).show();

		jQuery( '.wppp-js-single-edit-form' ).find( 'tr:last-child' ).before( newOption );
	} );

} );

