<?php

/**
 * Adds Insert Bar above Page Edit
 */
function wppp_shortcode_inserter() {

	$menu_ids = HT_Food_Menu::getMenuIds();
	?>
	<div id="ht-above-editor-insert-area">

		<strong><?php _e( 'Insert' ,'wppp' ) ?>:</strong>

		<a class="wppp-js-open-shortcode-modal wppp-shortcode-modal-button" href="#"><img src="<?php echo WPPP_URL . '/assets/images/food_20.png'?>"/><span>Poll</span></a>

		<div id="menu-menu-id-wrap" class="hidden">
			<select id="menu-menu-id">
				<option value="0"><?php _e( 'Select Poll', 'wppp' ) ?></option>

				<?php foreach ( $menu_ids as $menu_id ) : ?>
					<option value="<?php echo $menu_id ?>"><?php echo HT_Food_Menu::getMenu( $menu_id )->getTitle(); ?></option>
				<?php endforeach; ?>

			</select>
		</div>

		<?php do_action( 'wppp_above_editor_insert_items' ) ?>
	</div>
<?php
}
add_action( 'edit_page_form', 'wppp_shortcode_inserter' );

/**
 * Testing - Draw a poll
 *
 * @param array $args
 */
function wppp_draw_poll( $args = array() ) {

	$args = is_array( $args ) ? $args : array();

	$poll = WPPP_Polls_Engine::get( $args['poll'] );

	$poll->renderer()->draw( $args );

	exit;
}
add_shortcode( 'wppp', 'wppp_draw_poll' );

add_action( 'init', function() {

	add_action( 'wp_footer', function() {
		do_shortcode( '[wppp poll=13]' );
	} );

} );