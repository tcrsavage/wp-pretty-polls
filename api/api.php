<?php

require_once( WPPP_PATH . 'api/api.polls.php' );
require_once( WPPP_PATH . 'api/api.settings.php' );
require_once( WPPP_PATH . 'api/api.votes.php' );

function wppp_authenticate_admin_api_request() {

	if ( ! is_user_logged_in() || ! current_user_can( 'manage_options' ) )
		wppp_api_respond( 403, __( 'Failed to authenticate user', 'WPPP' ) );
}

function wppp_api_respond( $response_code, $message = null ) {

	$headers = array(
		200 => 'HTTP/1.0 200 OK',
		201 => 'HTTP/1.0 201 Created',
		204 => 'HTTP/1.0 204 No Content',
		400 => 'HTTP/1.0 400 Bad Request',
		403 => 'HTTP/1.0 403 Forbidden',
	);

	header( isset( $headers[$response_code] ) ? $headers[$response_code] : 'HTTP/1.0 500 Internal Server Error' );

	if ( $message )
		echo $message;

	exit;
}