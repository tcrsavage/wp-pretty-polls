jQuery( document ).ready( function( $ ) {

	jQuery( '.row-actions .delete a' ).click( function() {

		var self = jQuery( this );

		var endPoint = ( self.attr( 'wppp-data-id' ) ) ?  WPPPApiUrl + '/' + self.attr( 'wppp-data-id' ) : WPPPApiUrl;

		if ( confirm( 'Delete Poll, are you sure? All Data relating to this poll will be permenently removed' ) ) {

			jQuery.ajax( endPoint, {
				type 	: 'delete',
				data	: {},
				success	: function( data ) {
					self.closest( 'tr' ).hide( 'slow').remove();
				}
			} );
		}
	} );

} );

