<?php

add_action( 'init', function() {

	hm_add_rewrite_rule( array(
		'regex' 			=> '^wppp/api/settings?$',
		'request_method' 	=> 'post',
		'request_callback' 	=> function() {

			wppp_authenticate_admin_api_request();

			$settings = WPPP_Settings::get_instance();

			if ( ! empty( $_POST['wppp_default_styles'] ) )
				$settings->set_default_styles_enabled( true );
			else
				$settings->set_default_styles_enabled( false );

			_e( 'Settings updated successfully', 'WPPP' );

			exit;
		}

	) );

	hm_add_rewrite_rule( array(
		'regex' 			=> '^wppp/api/polls/?$',
		'request_method' 	=> 'post',
		'request_callback' 	=> function() {

			wppp_authenticate_admin_api_request();

			$title = ( isset( $_POST['wppp_title'] ) ) ? sanitize_text_field( stripslashes( $_POST['wppp_title'] ) ) : '';

			$description = ( isset( $_POST['wppp_description'] ) ) ? wp_kses_post( stripslashes( $_POST['wppp_description'] ) ) : '';

			$poll = WPPP_Polls_Engine::add( $title, $description );

			if ( ! $poll ) {
				header( "HTTP/1.0 500 Internal Server Error" );
				 _e( 'There was an unexpected error when creating the poll', 'WPPP' );
				exit;
			}

			$poll->voting()->set_is_voting_enabled( ! empty( $_POST['wppp_allow_voting'] ) );
			$poll->voting()->set_can_vote_multiple_times( ! empty( $_POST['wppp_allow_multiple_votes'] ) );

			//Get a clean keyed array of submitted options, where key is the id of the option and value is the option data
			$options_request = array();

			foreach ( $_POST as $post_field => $post_field_val ) {

				if ( strpos( $post_field, 'wppp_option_' ) === false || ! is_numeric( str_replace( 'wppp_option_', '', $post_field ) ) )
					continue;

				$index = (int) str_replace( 'wppp_option_', '', $post_field );

				$options_request[$index] = array( 'title' => sanitize_text_field( stripslashes( $post_field_val ) ) );
			}

			//Update/create the options which were submitted
			foreach ( $options_request as $index => $value ) {

				if ( $poll->option_exists( $index ) )
					$poll->set_option( $index, $value );

				else
					$poll->add_option( $value );
			}

			_e( 'Poll created', 'WPPP' );

			exit;
		}

	) );

	hm_add_rewrite_rule( array(
		'regex' => '^wppp/api/polls/([\d]+)/?$',
		'query' => 'p=$matches[1]',
		'request_methods' => array( 'post', 'delete' ),
		'request_callback' => function( WP $wp ) {

			wppp_authenticate_admin_api_request();

			$poll = WPPP_Polls_Engine::get( $wp->query_vars['p'] );

			if ( ! $poll ) {
				header( "HTTP/1.0 400 Bad Request" );
				_e( 'The poll requested does not exist', 'WPPP' );
				exit;
			}

			switch ( strtolower( $_SERVER['REQUEST_METHOD'] ) ) {

				case ( 'post' ) :

					if ( isset( $_POST['wppp_title'] ) )
						$poll->set_title( sanitize_text_field( stripslashes( $_POST['wppp_title'] ) ) );

					if ( isset( $_POST['wppp_description'] ) )
						$poll->set_description( wp_kses_post( stripslashes( $_POST['wppp_description'] ) ) );

					$poll->voting()->set_is_voting_enabled( ! empty( $_POST['wppp_allow_voting'] ) );
					$poll->voting()->set_can_vote_multiple_times( ! empty( $_POST['wppp_allow_multiple_votes'] ) );

					//Get a clean keyed array of submitted options, where key is the id of the option and value is the option data
					$options_request = array();

					foreach ( $_POST as $post_field => $post_field_val ) {

						if ( strpos( $post_field, 'wppp_option_' ) === false || ! is_numeric( str_replace( 'wppp_option_', '', $post_field ) ) )
							continue;

						$index = (int) str_replace( 'wppp_option_', '', $post_field );

						$options_request[$index] = array( 'title' => sanitize_text_field( stripslashes( $post_field_val ) ) );
					}

					//Delete any unwanted options
					foreach( $poll->get_options() as $index => $value )
						if ( ! array_key_exists( (int) $index, $options_request ) )
							$poll->delete_option( (int) $index );

					//Update/create the options which were submitted
					foreach ( $options_request as $index => $value ) {

						if ( $poll->option_exists( $index ) )
							$poll->set_option( $index, $value );

						else
							$poll->add_option( $value );
					}

					_e( 'Poll successfully updated', 'WPPP' );

					break;

				case ( 'delete' ) :

					WPPP_Polls_Engine::delete( $wp->query_vars['p'] );

					_e( 'Poll successfully deleted', 'WPPP' );

					break;
			}

			exit;
		}
	) );

	hm_add_rewrite_rule( array(
		'regex' => '^wppp/api/polls/([\d]+)/vote/?$',
		'query' => 'p=$matches[1]',
		'request_method' => 'post',
		'post_query_properties'	=> array( 'is_404' => false ),
		'request_callback' => function( WP $wp ) {

			$vote = ( ! empty( $_POST['selected_options'] ) ) ? $_POST['selected_options'] : array();

			if ( ! is_array( $vote ) )
				$vote = array( $vote );

			//Make sure the vote array is clean
			$vote = array_map( 'sanitize_text_field', (array) $_POST['selected_options'] );

			$poll = WPPP_Polls_Engine::get( $wp->query_vars['p'] );

			if ( ! $poll || ! $vote ) {
				header( "HTTP/1.0 400 Bad Request" );
				_e( 'Missing vote or poll in submission', 'WPPP' );
				exit;
			}

			if ( $poll->voting()->can_vote() ) {

				$poll->voting()->vote( $vote );

				_e( 'Vote was successful', 'WPPP' );

			} else {
				header( "HTTP/1.0 403 Forbidden" );
				_e( 'Sorry, you do not currently have permission to vote on this poll', 'WPPP' );
				exit;
			}

			exit;
 		}
	) );

} );

function wppp_authenticate_admin_api_request() {

	if ( ! is_user_logged_in() || ! current_user_can( 'manage_options' ) ) {
		header( "HTTP/1.0 403 Forbidden" );
		_e( 'Failed to authenticate user' );
		exit;
	}
}