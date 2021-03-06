jQuery( document ).ready( function( $ ) {


	//Form submission - capture event and fire ajax request
	jQuery( '.wppp-js-single-edit-form' ).submit( function( e ) {

		var endPoint = ( WPPPPollId ) ?  WPPPApiUrl + '/polls/' + WPPPPollId : WPPPApiUrl + '/polls';

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

	//Delete an option
	jQuery( '.wppp-js-single-edit-form' ).on( 'click', '.wppp-js-delete-option', function( e ) {

		jQuery( this ).closest( 'tr' ).remove();
	} );

	//Add an option via mouse click
	jQuery( '.wppp-js-add-option' ).click( function() {
		WPPPNewOption();
	} );

	//Add an option by pressing the return key
	jQuery( '.wppp-js-option-template input' ).keydown( function( e ) {

		if ( e.keyCode == 13 ) {
			e.preventDefault();
			WPPPNewOption();
		}
	} );

	//Switch to the advanced settings view
	jQuery( '.wppp-js-advanced-settings-toggle').click( function() {

		var advanced = jQuery( '.wppp-js-advanced-settings' );
		var simple = jQuery( '.wppp-js-simple-settings' );

		var self=  jQuery( this );

		if ( simple.is( ':visible' ) ) {

			simple.toggle( 0, function() {
				advanced.toggle();
			} );

			self.html( self.attr( 'wppp-data-simple-val' ) );

		} else {

			advanced.toggle( 0, function() {
				simple.toggle();
			} );

			self.html( self.attr( 'wppp-data-advanced-val' ) );
		}

	} );

} );

//Function to add a new option
function WPPPNewOption() {

	WPPPPollOptionCount++;

	var template = jQuery( '.wppp-js-option-template' );
	var newOption = template.clone();

	template.find( 'input' ).val( '' );

	newOption.find( 'label' ).html( 'Option ' + WPPPPollOptionCount );
	newOption.find( 'input' ).attr( 'name', 'wppp_option_' + WPPPPollOptionCount );
	newOption.find( '.wppp-js-add-option' ).attr( 'class', 'wppp-js-delete-option wppp-delete-option wppp-icon-delete' );
	newOption.removeClass( 'wppp-js-option-template' );

	jQuery( '.wppp-js-single-edit-form' ).find( 'tr.wppp-js-option-template' ).before( newOption );
}


