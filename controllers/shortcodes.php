<?php

/**
 * Testing - Draw a poll
 *
 * @param array $args
 */
function wppp_draw_poll( $args = array() ) {

	$args = is_array( $args ) ? $args : array();

	$poll = WPPP_Poll::get( $args['poll'] );

	$poll->renderer()->draw( $args );

	exit;
}
add_shortcode( 'wppp', 'wppp_draw_poll' );

add_action( 'init', function() {

	//	add_action( 'wp_footer', function() {
	//		do_shortcode( '[wppp poll=11]' );
	//	} );

} );