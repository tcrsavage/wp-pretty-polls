
var WPPP_Poll = function( id, args) {

	var self = this;

	self.id 					= id;
	self.apiurl 				= args.apiUrl;
	self.isMultipleVotesEnabled = args.isMultipleVotesEnabled;
	self.pollRendered 			= args.pollRendered;
	self.voteData 				= args.voteData;

	self.container 		= jQuery( '.wppp-js-poll-' + self.id );

	self.voteForm 		= self.container.find( '.js-wppp-vote' );
	self.tabVote 		= self.container.find( '.wppp-js-vote' );
	self.tabResults 	= self.container.find( '.wppp-js-results' );
	self.resultBars 	= self.container.find( '.wppp-js-result-bar' );
	self.tabVoteBtn		= self.container.find( '.wppp-js-vote-tab' );
	self.errorContainer = self.container.find( '.wppp-js-error' );
	self.isAjaxLoading 	= false;

	self.init = function() {

		//on vote submission
		self.container.find( '.js-wppp-vote' ).submit( function( e ) {
			e.preventDefault();
			self.vote( jQuery( this ).serializeArray() )
		} );

		//on tab click
		self.container.find( '.wppp-js-vote-tab, .wppp-js-results-tab' ).click( function( e ) {
			self.toggleTab();
		} );

		//on close error message click
		self.errorContainer.find( '.close' ).click( function( e ) {
			self.setDisplayError( false );
		} );

		self.updateVoteUIDisabled();

		if ( self.isVotingDisabled() )
			self.setActiveTab( 'results' );

	}

	self.vote = function( voteSerialized )  {

		jQuery.ajax( self.apiurl + '/votes/', {
			type 	: 'post',
			data	: voteSerialized,
			success	: function( data ) {

				var vote = {};

				self.setAjaxLoading( false );

				jQuery.each( voteSerialized, function ( key, val ) {

					vote[parseInt(val.value)] = 1;
				} );

				self.saveVote( vote );

				self.shuntVotes();

				self.toggleTab();
			},
			error : function ( data ) {

				self.setError( data.responseText );
				self.setDisplayError( true );

				self.setAjaxLoading( false );
				self.toggleTab();
			}

		} );

		self.setAjaxLoading( true );

	}

	self.isVotingDisabled = function() {

		return ( self.isAjaxLoading || ( ! self.isMultipleVotesEnabled && self.hasUserVoted() ) ) ? true : false
	}

	self.updateVoteUIDisabled = function () {

		( self.isVotingDisabled() ) ? self.setVotingUIDisabled( true ) : self.setVotingUIDisabled( false )
	}

	self.setVotingUIDisabled = function( bool ) {

		self.voteForm.find( 'input[type="submit"]' ).prop( 'disabled', (bool) );

		(bool) ? self.tabVoteBtn.hide() : self.tabVoteBtn.show();
	}

	self.setAjaxLoading = function( bool ) {

		self.isAjaxLoading = ( bool );

		self.updateVoteUIDisabled();

		if ( bool ) {
			self.container.append( '<div class="ajax-box"></div>' );
			self.container.addClass( 'wppp-ajax-loading' );
		} else {
			self.container.find( '.ajax-box' ).remove();
			self.container.removeClass( 'wppp-ajax-loading' )
		}
	}

	self.toggleTab = function() {

		if ( self.tabVote.is( ':visible' ) ) {
			self.tabVote.hide();
			self.tabResults.show();
		} else {
			self.tabVote.show();
			self.tabResults.hide();
		}
	}

	self.setActiveTab = function( tab ) {

		if ( tab == 'results' ) {
			self.tabVote.hide();
			self.tabResults.show();
		} else {
			self.tabVote.show();
			self.tabResults.hide();
		}
	}

	//todo: experimental, needs better handling of the html, this is easy to break
	self.shuntVotes = function() {

		var userVoteData = self.getSavedVotes();

		var totalCount = 0;
		var optionsCount = [];

		jQuery.each( self.voteData, function( key, val ) {
			optionsCount[key] = parseInt( val.votes );
			totalCount += parseInt( val.votes );
		} );

		jQuery.each( userVoteData, function( timestamp, userVote ) {

			if ( timestamp < self.pollRendered )
				return;

			jQuery.each( userVote, function( optionKey, value ) {

				if ( typeof( userVote[optionKey] ) == 'undefined' )
					return;

				optionsCount[optionKey] 		+= value;
				totalCount 						+= 1;

			} );

		} );

		self.resultBars.each( function( key, ele ) {

			ele = jQuery( ele );

			var count = optionsCount[ele.attr( 'wppp-data-id' )];

			var percentage = ( ( count / totalCount ) * 100 ).toFixed( 2 );

			ele.css( 'width', ( percentage + '%' ) );

			ele.closest( 'li' ).find( '.wppp-result-bar-detail' ).html( percentage + '% ' + '(' + count + ' votes)' );

		} );
	}

	self.getCookie = function( cookieName ) {

		if ( document.cookie.length > 0 ) {
			c_start = document.cookie.indexOf( cookieName + "=" );

			if ( c_start != -1 ) {
				c_start = c_start + cookieName.length + 1;
				c_end = document.cookie.indexOf( ";", c_start );

				if ( c_end == -1)
					c_end = document.cookie.length;

				return JSON.parse( unescape( document.cookie.substring(c_start, c_end) ) );
			}
		}
		return "";
	}

	self.setCookie = function( cookieName, cookieValue, cookieDays ) {

		if ( cookieDays ) {
			var date = new Date();
			date.setTime( date.getTime() + ( cookieDays * 24 * 60 * 60 * 1000 ) );
			var expires = "; expires=" + date.toGMTString();
		} else {
			var expires = "";
		}

		document.cookie = cookieName + "=" + JSON.stringify( cookieValue ) + expires + "; path=/";
	}

	self.saveVote = function( vote ) {

		var d = new Date();
		var n = d.getTime();

		var savedCookie = self.getSavedVotes();

		var voteData = ( savedCookie ) ? savedCookie : {};

		if ( typeof( voteData[self.pollRendered] ) == 'undefined' )
			voteData[self.pollRendered] = {};

		jQuery.each( vote, function( optionKey, value ) {

			if ( typeof( voteData[self.pollRendered][optionKey] ) == 'undefined' )
				voteData[self.pollRendered][optionKey] = 0;

			voteData[self.pollRendered][optionKey] += value;
		} );

		self.setSavedVotes( voteData );
	}

	self.getSavedVotes = function() {
		return self.getCookie( 'WPPP_Votes_' + self.id );
	}

	self.setSavedVotes = function( votes ) {
		self.setCookie( 'WPPP_Votes_' + self.id, votes, 999 );
	}

	self.hasUserVoted = function() {

		return ( self.getSavedVotes() ) ? true : false;
	}

	self.setError = function( errorMessage ) {

		self.currentError = errorMessage;
	}

	self.getError = function() {

		return self.currentError;
	}

	self.setDisplayError = function( bool ) {

		if ( bool ) {
			self.errorContainer.show().find( '.message' ).html( self.getError() );
		} else {
			self.errorContainer.hide();
		}
	}

};