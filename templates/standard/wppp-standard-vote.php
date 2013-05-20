<?php

//Get easy access to the poll and the poll renderer objects while in the template

/* @var WPPP_Poll $poll */
$poll = $template_args['poll'];

/* @var WPPP_Front_End_Renderer $renderer */
$renderer = $template_args['renderer']; ?>

<div>

	<ul class="wppp-options">
		<?php foreach ( $poll->get_options() as $key => $val ) : ?>
			<li class="wppp-option-container">
				<label for="wppp-vote-option-<?php echo $poll->get_id() . '-' . $key; ?>"><?php echo esc_textarea( $val['title'] ); ?></label>
				<input id="wppp-vote-option-<?php echo $poll->get_id() . '-' . $key; ?>" type="radio" class="wppp-option wppp-js-option" name="wppp_vote" value="<?php echo esc_attr( $key ); ?>" />
			</li>
		<?php endforeach; ?>
	</ul>

	<button class="wppp-js-submit">Vote</button>
</div>