<?php

function wppp_add_admin_settings_menu_page() {

	add_submenu_page( 'wppp_polls', __( 'Settings', 'WPPP' ), __( 'Settings', 'WPPP' ), 'manage_options', 'wppp_settings', 'wppp_polls_admin_settings_page' );
}
add_action( 'admin_menu', 'wppp_add_admin_settings_menu_page', 11 );

function wppp_polls_admin_settings_page() {

	$settings = WPPP_Settings::get_instance(); ?>

	<div class="wrap wppp-single-wrap">
		<div id="icon-tools" class="icon32"><br></div>
		<h2><?php _e( 'Polls - Settings', 'WPPP' ); ?></h2>

		<form class="wppp-js-settings-submit">

			<table class="form-table">

				<tbody>

					<tr>
						<td><label for="wppp_default_styles"><?php _e( 'Use Default Style Sheet', 'WPPP' ); ?></label></td>
						<td><input type="checkbox" id="wppp_default_styles" name="is_default_styles_enabled" <?php checked( $settings->is_default_styles_enabled() ); ?> /></td>
						<td></td>
					</tr>

					<tr>
						<td></td>

						<td>
							<span class="wppp-right wppp-ajax-status wppp-js-ajax-status" ></span>
							<input type="submit" class="button-primary wppp-right wppp-submit" value="Submit" />
						</td>
					</tr>

				</tbody>

			</table>
		</form>
	</div>
	<?php
}

function wppp_admin_scripts_for_settings() {

	wp_enqueue_script( 'wppp-admin-single-js', WPPP_URL . '/assets/admin.settings.scripts.js', array(), WPPP_VERSION );
	add_action( 'admin_head', 'wppp_jscript_variables' );
}
add_action( 'load-polls_page_wppp_settings', 'wppp_admin_scripts_for_settings' );