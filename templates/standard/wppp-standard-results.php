<?php

//Get easy access to the poll and the poll renderer objects while in the template

/* @var WPPP_Poll $poll */
$poll = $template_args['poll'];

/* @var WPPP_Renderer $renderer */
$renderer = $template_args['renderer']; ?>

<div>
	<ul class="wppp-result-list-container">
		<?php $votes_data = $poll->voting()->get_votes_data(); ?>

		<?php foreach ( $votes_data as $option_id => $option_data ) : ?>

			<li class="wppp-result-bar-container">
				<span><?php echo esc_textarea( $option_data['title'] ); ?></span>
				<div class="wppp-result-bar-fill-width">
					<div class="wppp-result-bar wppp-js-result-bar" wppp-data-id="<?php echo $option_id; ?>" wppp-data-count="<?php echo $option_data['votes']; ?>" style="width: <?php echo $option_data['percentage']; ?>%"></div>
				</div>

				<div class="wppp-result-bar-detail"><?php echo $option_data['percentage']; ?>% (<?php echo $option_data['votes']; ?> votes)</div>
				<div class="wppp-clearfix"></div>
			</li>
		<?php endforeach; ?>
	</ul>
	<div class="wppp-js-vote-tab wppp-vote-tab">Vote</div>

	<div class="wppp-clearfix"></div>
</div>