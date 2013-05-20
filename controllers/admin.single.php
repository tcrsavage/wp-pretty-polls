<?php

function wppp_add_admin_single_menu_page() {

	add_submenu_page( 'wppp_polls', __( 'Add New', 'WPSF' ), __( 'Add New', 'WPSF' ), 'manage_options', 'wppp_edit_poll', 'wppp_polls_admin_single_page' );
}
add_action( 'admin_menu', 'wppp_add_admin_single_menu_page', 11 );

function wppp_polls_admin_single_page() {

	$poll = ( ! empty( $_GET['poll'] ) ) ? WPPP_Poll::get( $_GET['poll'] ) : false; ?>

	<div class="wrap wppp-single-wrap">
		<div id="icon-tools" class="icon32"><br></div>
		<h2>Polls - <?php echo ( $poll ) ? 'Edit'  : 'Create New Poll' ?></h2>

		<form class="wppp-js-single-edit-form">

			<table class="form-table wppp-js-simple-settings">

				<tbody>

					<tr>
						<td><label>Title</label></td>
						<td><input type="text" name="wppp_title" value="<?php echo ( $poll ) ? esc_textarea( $poll->get_post()->post_title ) : ''; ?>"/></td>
						<td></td>
					</tr>

					<tr>
						<td><label>Description (Optional)</label></td>
						<td><textarea name="wppp_description"><?php echo ( $poll ) ? esc_textarea( $poll->get_post()->post_content ) : ''; ?></textarea></td>
						<td></td>
					</tr>

					<?php if ( $poll ) : ?>
						<?php foreach ( $poll->get_options() as $key => $val  ) : ?>
							<tr>
								<td><label><?php echo 'Option ' . esc_attr( $key ) ; ?></label></td>
								<td><input type="text" name="wppp_option_<?php echo esc_attr( $key ); ?>" value="<?php echo ( isset( $val['title'] ) ) ? $val['title'] : '' ; ?>" /></td>
								<td><div class="wppp-js-delete-option wppp-delete-option wppp-icon-delete"></div></td>
							</tr>
						<?php endforeach; ?>
					<?php endif; ?>

					<tr class="wppp-js-option-template">
						<td><label>New Option</label></td>
						<td><input type="text" name="template_option" /></td>
						<td><div class="wppp-js-add-option wppp-add-option wppp-icon-add"></div></td>
					</tr>

				</tbody>
			</table>

			<table class="form-table wppp-js-advanced-settings" style="display: none;">

				<tbody>

				<tr>
					<td><label for="wppp_allow_voting">Allow voting</label></td>
					<td><input type="checkbox" id="wppp_allow_voting" name="wppp_allow_voting" value="1" <?php checked( $poll && $poll->voting()->is_voting_enabled() ); ?> /></td>
					<td></td>
				</tr>

				<tr>
					<td><label for="wppp_allow_multiple_votes">Allow multiple votes</label></td>
					<td><input type="checkbox" id="wppp_allow_multiple_votes" name="wppp_allow_multiple_votes" value="1" <?php checked( $poll && $poll->voting()->can_vote_multiple_times() ); ?> /></td>
					<td></td>
				</tr>

				<tr>
					<td><label for="wppp_save_vote_data_cookies">Save Vote data cookies</label></td>
					<td><input type="checkbox" id="wppp_save_vote_data_cookies" name="wppp_save_vote_data_cookies" value="1" <?php checked( $poll && $poll->voting()->is_voting_cookies_enabled() ); ?> /></td>
					<td></td>
				</tr>

				</tbody>
			</table>

			<table class="form-table">
				<tbody>
					<tr>
						<td>
							<button class="button-secondary wppp-js-advanced-settings-toggle" wppp-data-advanced-val="<?php _e( 'Advanced settings', 'WPPP' );?>" wppp-data-simple-val="<?php _e( 'Simple settings', 'WPPP' );?>">
								<?php _e( 'Advanced settings', 'WPPP' ); ?>
							</button></td>
						<td></td>
						<td><input type="submit" class="button-primary wppp-right wppp-submit" value="Submit" /></td>
					</tr>
				</tbody>
			</table>

		</form>
	</div>
<?php
}

function wppp_admin_scripts_for_single_poll() {

	wp_enqueue_script( 'wppp-admin-single-js', WPPP_URL . '/assets/admin.single.scripts.js', array(), WPPP_VERSION );

	add_action( 'admin_head', 'wppp_jscript_variables' );
}
add_action( 'load-polls_page_wppp_edit_poll', 'wppp_admin_scripts_for_single_poll' );