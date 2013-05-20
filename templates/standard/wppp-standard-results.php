<?php

//Get easy access to the poll and the poll renderer objects while in the template

/* @var WPPP_Poll $poll */
$poll = $template_args['poll'];

/* @var WPPP_Front_End_Renderer $renderer */
$renderer = $template_args['renderer']; ?>

<div>
	<span>RESULTS:</span>
	<ul>
		<?php $votes_data = $poll->voting()->get_votes_data(); ?>

		<?php foreach ( $votes_data as $option_id => $option_data ) : ?>
			<li class="wppp-result-bar-container">
				<span><?php echo esc_textarea( $option_data['title'] ); ?></span>
				<div class="wppp-result-bar" style="width: <?php echo $option_data['percentage']; ?>%"></div>
			</li>
		<?php endforeach; ?>
	</ul>
</div>