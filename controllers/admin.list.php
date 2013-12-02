<?php

function wppp_add_admin_list_menu_page() {

	add_menu_page( __( 'Edit Polls', 'WPPP' ), __( 'Polls', 'WPPP' ), 'manage_options', 'wppp_polls', 'wppp_polls_admin_list_table', WPPP_URL . 'assets/images/icon.png', 48 );
}
add_action( 'admin_menu', 'wppp_add_admin_list_menu_page', 10 );

function wppp_polls_admin_list_table() {

	global $current_screen;

	$current_screen->post_type = 'wppp_poll';

	$table = new WPPP_Polls_List_Table( );
	$table->prepare_items();
	?>
	<div class="wrap">
		<div id="icon-tools" class="icon32"><br></div>
		<h2>Polls <a href="#" class="add-new-h2 wpsf-add-new-form">Add New</a></h2>

		<div class="wpsf-add-form-container">

			<form method="get" action="">
				<h2>Add new Form</h2>
				<label for="form_name">Title</label><input type="text" id="form_name" name="form_name" value="" />
				<input type="hidden" name="action" value="new" />
				<input type="hidden" name="page" value="wpsf_forms" />
				<div class="wpsf-clear"></div>
				<input type="submit" value="Create" />
			</form>
		</div>

		<?php $table->display(); ?>
	</div>

<?php
}

function wppp_admin_scripts_for_poll_list_table() {

	wp_enqueue_script( 'wppp-admin-list-js', WPPP_URL . '/assets/admin.list.scripts.js', array(), WPPP_VERSION );

	add_action( 'admin_head', 'wppp_jscript_variables' );
}
add_action( 'load-toplevel_page_wppp_polls', 'wppp_admin_scripts_for_poll_list_table' );
