<?php

//Get easy access to the poll and the poll renderer objects while in the template

/* @var WPPP_Poll $poll */
$poll = $template_args['poll'];

/* @var WPPP_Renderer $renderer */
$renderer = $template_args['renderer']; ?>

<div class="wppp-vote-wrap">
	<form class="js-wppp-vote">
		<ul class="wppp-options">
			<?php foreach ( $poll->get_options() as $key => $val ) : ?>
				<li class="wppp-option-container">
					<input id="wppp-vote-option-<?php echo $poll->get_id() . '-' . $key; ?>" type="radio" class="wppp-option wppp-js-option" name="selected_options" value="<?php echo esc_attr( $key ); ?>" />
					<label for="wppp-vote-option-<?php echo $poll->get_id() . '-' . $key; ?>"><?php echo esc_textarea( $val['title'] ); ?></label>
				</li>
			<?php endforeach; ?>
		</ul>

		<input type="submit" class="wppp-js-submit" value="Vote" />
	</form>

	<div class="wppp-js-results-tab wppp-results-tab">Results</div>
</div>