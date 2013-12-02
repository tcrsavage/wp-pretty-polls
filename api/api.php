<?php

require_once( WPPP_PATH . 'api/api.polls.php' );
require_once( WPPP_PATH . 'api/api.settings.php' );
require_once( WPPP_PATH . 'api/api.votes.php' );

function wppp_authenticate_admin_api_request() {

	if ( ! is_user_logged_in() || ! current_user_can( 'manage_options' ) )
		wppp_api_respond( 403, __( 'Failed to authenticate user', 'WPPP' ) );
}

function wppp_api_respond( $response_code, $message = null ) {

	status_header( $response_code );

	if ( $message )
		echo $message;

	exit;
}