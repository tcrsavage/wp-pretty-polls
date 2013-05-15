<?php

function wppp_jscript_variables() {

	$poll = ( ! empty( $_GET['poll'] ) ) ? WPPP_Poll::get( $_GET['poll'] ) : false; ?>

	<script type="text/javascript">
		var WPPPPollOptionCount = <?php echo ( $poll ) ? $poll->get_options_count(): 0; ?>;
		var WPPPPollId = <?php echo ( $poll ) ? $poll->get_id() : 0; ?>;
		var WPPPApiUrl = '<?php echo WPPP_API_URL; ?>';
	</script>
	<?php
}