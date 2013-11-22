<?php

function wppp_shortcode_inserter_btn( $context ) {

	$img = WPPP_URL . '/assets/images/icon.png';
	$container_id = 'wppp-insert-shortcode-container';
	$title = 'Insert poll';

	$context .= "<a class='thickbox' title='{$title}' href='#TB_inline?width=400&inlineId={$container_id}'><img src='{$img}' /></a>";

	return $context;
}
add_action('media_buttons_context', 'wppp_shortcode_inserter_btn');

function wppp_shortcode_modal() {
	?>
	<div id="wppp-insert-shortcode-container" style="display:none;">
		<h2>Insert Poll</h2>

		<form id="wppp-js-shortcode-insert-form" class="describe wppp-shortcode-insert-form" method="post" action="">
			<table>
				<tbody>
				<tr>
					<th>
						<label for="wppp_poll_id">Poll</label>
					</th>
					<td>
						<select id="wppp_poll_id" name="poll">
							<option value="0">Select poll</option>
							<?php foreach ( WPPP_Polls_Engine::get_polls() as $poll ) : ?>
								<option value="<?php echo $poll->get_id();?>"><?php echo $poll->get_title(); ?></option>
							<?php endforeach; ?>
						</select>
					</td>
				</tr>
				<tr>
					<th>
						<label for="wppp_show_title">Show title</label>
					</th>
					<td>
						<input id="wppp_show_title" type="checkbox" name="show_title" value="1" />
					</td>
				</tr>
				</tbody>
			</table>

			<input type="submit" class="alignright" value="Insert poll" />

		</form>
	</div>
<?php
}
add_action('admin_footer', 'wppp_shortcode_modal' );

function wppp_shortcode_inserter_js() {

	?>
	<script type="text/javascript">
        jQuery( document ).ready (function() {

			jQuery( "#wppp-shortcode-insert-form" ).submit( function( e ) {
				e.preventDefault();
           		send_to_editor( '[wppp ' + encodeURI( jQuery( this ).serialize() ).replace( /&/, ' ' ) + ' ]' );
           		return false;
		   });
        });
	</script>
	<?php
}
add_action('admin_head', 'wppp_shortcode_inserter_js');

/**
 * Draw a poll for the given args
 *
 * @param array $args
 */
function wppp_draw_poll( $args = array() ) {

	$args = is_array( $args ) ? $args : array();

	$poll = WPPP_Polls_Engine::get( $args['poll'] );

	if ( ! $poll )
		return;

	$poll->renderer()->draw( $args );
}
add_shortcode( 'wppp', 'wppp_draw_poll' );