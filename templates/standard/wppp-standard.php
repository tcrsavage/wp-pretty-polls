<?php

//Get easy access to the poll and the poll renderer objects while in the template

/* @var WPPP_Poll $poll */
$poll = $template_args['poll'];

/* @var WPPP_Renderer $renderer */
$renderer = $template_args['renderer'];

/* @var array $args */
$args = $template_args['args']; ?>

<div class="wppp-js-poll-<?php echo $poll->get_id(); ?> wppp-js-poll wppp-poll wppp-poll-<?php echo $poll->get_id(); ?>" style="<?php echo ( ! empty( $args['width'] ) ) ? 'width:' . $args['width'] . ';' : '' ?><?php echo ( ! empty( $args['height'] ) ) ? 'height:' . $args['height'] . ';' : '' ?>" >

	<?php if ( $args['show_title'] ) : ?>
		<div class="wppp-title">
			<h4><?php echo esc_textarea( $poll->get_post()->post_title ); ?></h4>
		</div>
	<?php endif; ?>

	<?php if ( $poll->get_post()->post_content  ) : ?>
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

	jQuery( '.wppp-js-poll-<?php echo $poll->get_id(); ?>').ready( function() {

		if ( typeof( WPPP_Poll ) === 'undefined' )
			return;

		var WPPP_Current_Poll = new WPPP_Poll( <?php echo $poll->get_id(); ?>, {
			apiUrl					: '<?php echo WPPP_API_URL . '/polls/' . $poll->get_id(); ?>',
			isMultipleVotesEnabled	: '<?php echo ( $poll->voting()->is_multiple_voting_enabled() ) ? 'true' : 'false'; ?>',
			pollRendered			: '<?php echo time(); ?>',
			voteData 				: JSON.parse( '<?php echo  json_encode( $poll->voting()->get_votes_data() ); ?>' )
		} );

		WPPP_Current_Poll.init();
	} );

</script>