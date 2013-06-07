<?php

//Get easy access to the poll and the poll renderer objects while in the template

/* @var WPPP_Poll $poll */
$poll = $template_args['poll'];

/* @var WPPP_Front_End_Renderer $renderer */
$renderer = $template_args['renderer'];

/* @var array $args */
$args = $template_args['args']; ?>

<script type="text/javascript">
	jQuery( '.wppp-poll-<?php echo $poll->get_id(); ?>' ).ready( function() {

		if ( typeof( apiUrl ) == 'undefined' )
			var apiUrl = '<?php echo WPPP_API_URL; ?>';

		//Process a vote submission when the vote button is pressed
		jQuery( '.wppp-poll-<?php echo $poll->get_id(); ?> .wppp-js-submit' ).click( function( e ) {

			e.preventDefault();

			var selected_options = new Array();

			jQuery( '.wppp-poll-<?php echo $poll->get_id(); ?> .wppp-js-option' ).each( function() {

				if ( jQuery( this ).is( ':checked' ) )
					selected_options.push( jQuery( this ).val() );
			} );

			jQuery.ajax( '<?php echo WPPP_API_URL; ?>' + '/' + '<?php echo $poll->get_id(); ?>' + '/vote/', {
				type 	: 'post',
				data	: { selected_options: selected_options },
				success	: function( data ) {
				}
			} );
		} );

		//Handle switching between votes and results tab
		jQuery( '.wppp-js-vote-tab, .wppp-js-results-tab').click( function() {

			self = jQuery( this );

			if ( jQuery( this ).hasClass( 'wppp-js-vote-tab' ) ) {
				var hide = self.closest( '.wppp-poll').find( '.wppp-js-results' );
				var show = self.closest( '.wppp-poll').find( '.wppp-js-vote' );

			} else {

				var show = self.closest( '.wppp-poll').find( '.wppp-js-results' );
				var hide = self.closest( '.wppp-poll').find( '.wppp-js-vote' );
			}

			self.closest( '.wppp-js-tabs' ).find( 'div' ).each( function() {

				jQuery( this ).removeClass( 'wppp-tab-selected' );
			} );

			self.addClass( 'wppp-tab-selected' );

			hide.hide();
			show.show();

		} );

	} );
</script>

<div class="wppp-poll wppp-poll-<?php echo $poll->get_id(); ?>" style="<?php echo ( ! empty( $args['width'] ) ) ? 'width:' . $args['width'] . ';' : '' ?><?php echo ( ! empty( $args['height'] ) ) ? 'height:' . $args['height'] . ';' : '' ?>">

	<div class='wppp-tabs wppp-js-tabs'>
		<div class="wppp-js-vote-tab wppp-vote-tab wppp-tab-selected">Vote</div>
		<div class="wppp-js-results-tab wppp-results-tab">Results</div>
	</div>

	<div class="clearfix"></div>

	<?php if ( $args['show_title'] ) : ?>
		<div class="wppp-title">
			<h3><?php echo esc_textarea( $poll->get_post()->post_title ); ?></h3>
		</div>
	<?php endif; ?>

	<div class="wppp-description">
		<span><?php echo esc_textarea( $poll->get_post()->post_content ); ?></span>
	</div>

	<div id="wppp-poll-<?php echo $poll->get_id(); ?>-vote" class="wppp-js-vote" >
		<?php hm_get_template_part( WPPP_PATH . '/templates/standard/wppp-standard-vote.php', array( 'poll' => $poll, 'renderer' => $renderer ) ); ?>
	</div>

	<div style="display: none;" id="wppp-poll-<?php echo $poll->get_id(); ?>-results" class="wppp-js-results" >
		<?php hm_get_template_part( WPPP_PATH . '/templates/standard/wppp-standard-results.php', array( 'poll' => $poll, 'renderer' => $renderer ) ); ?>
	</div>

</div>

