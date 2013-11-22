<?php

//Get easy access to the poll and the poll renderer objects while in the template

/* @var WPPP_Poll $poll */
$poll = $template_args['poll'];

/* @var WPPP_Renderer $renderer */
$renderer = $template_args['renderer'];

/* @var array $args */
$args = $template_args['args']; ?>

<div class="wppp-js-poll-<?php echo $poll->get_id(); ?> wppp-poll wppp-poll-<?php echo $poll->get_id(); ?>" style="<?php echo ( ! empty( $args['width'] ) ) ? 'width:' . $args['width'] . ';' : '' ?><?php echo ( ! empty( $args['height'] ) ) ? 'height:' . $args['height'] . ';' : '' ?>">

	<?php if ( $args['show_title'] ) : ?>
		<div class="wppp-title">
			<h4><?php echo esc_textarea( $poll->get_post()->post_title ); ?></h4>
		</div>
	<?php endif; ?>

	<?php if( $poll->get_post()->post_content  ) : ?>
		<div class="wppp-description">
			<span><?php echo esc_textarea( $poll->get_post()->post_content ); ?></span>
		</div>
	<?php endif; ?>

	<div id="wppp-poll-<?php echo $poll->get_id(); ?>-vote" class="wppp-js-vote" >
		<?php hm_get_template_part( WPPP_PATH . '/templates/standard/wppp-standard-vote.php', array( 'poll' => $poll, 'renderer' => $renderer ) ); ?>
	</div>

	<div style="display: none;" id="wppp-poll-<?php echo $poll->get_id(); ?>-results" class="wppp-js-results" >
		<?php hm_get_template_part( WPPP_PATH . '/templates/standard/wppp-standard-results.php', array( 'poll' => $poll, 'renderer' => $renderer ) ); ?>
	</div>

	<div class="wppp-js-response wppp-response wppp-js-error wppp-error">
		<span class="message"></span>
		<span class="close">Close</span>
	</div>

</div>

<script type="text/javascript">

	jQuery( '.wppp-poll-<?php echo $poll->get_id(); ?>' ).ready( function() {

		var WPPP_Poll = new function () {

			var self = this;

			self.id 	= <?php echo $poll->get_id(); ?>;
			self.apiurl = '<?php echo WPPP_API_URL; ?>' + '/polls/' + self.id;

			self.pollRendered = <?php echo time(); ?>;

			self.container 	= jQuery( '.wppp-js-poll-' + self.id );
			self.voteForm 	= self.container.find( '.js-wppp-vote' );

			self.tabVote 	= self.container.find( '.wppp-js-vote' );
			self.tabResults = self.container.find( '.wppp-js-results' );
			self.resultBars = self.container.find( '.wppp-js-result-bar' );
			self.tabVoteBtn = self.container.find( '.wppp-js-vote-tab' );

			self.errorContainer = self.container.find( '.wppp-js-error' );

			self.isMultipleVotesEnabled = <?php echo ( $poll->voting()->can_vote_multiple_times() ) ? 'true' : 'false'; ?>;
			self.isAjaxLoading 			= false;
			self.voteData 				= JSON.parse( '<?php echo json_encode( $poll->voting()->get_votes_data() ); ?>' );

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

				jQuery.ajax( self.apiurl + '/vote/', {
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

		WPPP_Poll.init();

	} );
</script>