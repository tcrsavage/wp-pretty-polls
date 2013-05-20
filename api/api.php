<?php

add_action( 'init', function() {

	hm_add_rewrite_rule( array(
		'regex' 			=> '^wppp/api/polls/?$',
		'request_method' 	=> 'post',
		'request_callback' 	=> function() {


			$title = ( isset( $_POST['wppp_title'] ) ) ? sanitize_text_field( stripslashes( $_POST['wppp_title'] ) ) : '';

			$description = ( isset( $_POST['wppp_description'] ) ) ? wp_kses_post( stripslashes( $_POST['wppp_description'] ) ) : '';

			$poll = WPPP_Poll::add( $title, $description );

			if ( ! $poll ) {
				header( "HTTP/1.0 500 Internal Server Error" );
				echo json_encode( array( 'success' => false, 'message' => __( 'There was an unexpected error when creating the poll', 'WPPP' ) ) );
				exit;
			}

			//Hack to make sure undesired options are not kept
			$poll->clear_options();

			$i = 1;

			while ( isset( $_POST['wppp_option_' . $i] ) ) {

				$poll->set_option( $i, array(
					'title' => sanitize_text_field( stripslashes( $_POST['wppp_option_' . $i] ) )
				) );

				$i++;
			}

			echo json_encode( array( 'success' => true, 'message' => __( 'Poll created', 'WPPP' ) ) );

			exit;
		}

	) );

	hm_add_rewrite_rule( array(
		'regex' => '^wppp/api/polls/([\d]+)/?$',
		'query' => 'p=$matches[1]',
		'request_methods' => array( 'post', 'delete' ),
		'request_callback' => function( WP $wp ) {

			$poll = WPPP_Poll::get( $wp->query_vars['p'] );

			if ( ! $poll ) {
				header( "HTTP/1.0 400 Bad Request" );
				echo json_encode( array( 'success' => false, 'message' => __( 'The poll requested does not exist', 'WPPP' ) ) );
				exit;
			}

			switch ( strtolower( $_SERVER['REQUEST_METHOD'] ) ) {

				case ( 'post' ) :

					if ( isset( $_POST['wppp_title'] ) )
						$poll->set_title( sanitize_text_field( stripslashes( $_POST['wppp_title'] ) ) );

					if ( isset( $_POST['wppp_description'] ) )
						$poll->set_description( wp_kses_post( stripslashes( $_POST['wppp_description'] ) ) );

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

					echo json_encode( array( 'success' => true, 'message' => __( 'Poll successfully updated', 'WPPP' ) ) );

					break;

				case ( 'delete' ) :

					WPPP_Poll::delete( $wp->query_vars['p'] );

					echo json_encode( array( 'success' => true, 'message' => __( 'Poll successfully deleted', 'WPPP' ) ) );

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

			$poll = WPPP_Poll::get( $wp->query_vars['p'] );

			if ( ! $poll || ! $vote ) {
				header( "HTTP/1.0 400 Bad Request" );
				echo json_encode( array( 'success' => false, 'message' => __( 'Missing vote or poll in submission', 'WPPP' ) ) );
				exit;
			}

			if ( $poll->voting()->can_vote() ) {

				$poll->voting()->vote( $vote );

				echo json_encode( array( 'success' => true, 'message' => __( 'Vote was successful', 'WPPP' ) ) );

			} else {

				echo json_encode( array( 'success' => false, 'message' => __( 'Sorry, you do not currently have permission to vote on this poll', 'WPPP' ) ) );
			}

			exit;
 		}
	) );

} );