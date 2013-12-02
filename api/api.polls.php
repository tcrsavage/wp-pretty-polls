<?php add_action( 'init', function() {

	hm_add_rewrite_rule( array(
		'regex' 			=> '^wppp/api/polls/?$',
		'request_method' 	=> 'post',
		'request_callback' 	=> function() {

			wppp_authenticate_admin_api_request();

			$title = ( isset( $_POST['wppp_title'] ) ) ? sanitize_text_field( stripslashes( $_POST['wppp_title'] ) ) : '';

			$description = ( isset( $_POST['wppp_description'] ) ) ? wp_kses_post( stripslashes( $_POST['wppp_description'] ) ) : '';

			try {

				$poll = WPPP_Polls_Engine::add( $title, $description );

				$poll->voting()->set_is_voting_enabled( ! empty( $_POST['wppp_allow_voting'] ) );
				$poll->voting()->set_is_multiple_voting_enabled( ! empty( $_POST['wppp_allow_multiple_votes'] ) );

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

			} catch ( Exception $e ) {

				wppp_api_respond_failure( 400, $e->getMessage() );
			}

			wppp_api_respond( 201, $poll->to_json() );
		}
	) );

	hm_add_rewrite_rule( array(
		'regex' => '^wppp/api/polls/([\d]+)/?$',
		'query' => 'p=$matches[1]',
		'request_methods' => array( 'post', 'delete', 'get' ),
		'request_callback' => function( WP $wp ) {

			wppp_authenticate_admin_api_request();

			switch ( strtolower( $_SERVER['REQUEST_METHOD'] ) ) {

				case ( 'post' ) :

					try {

						$poll = new WPPP_Poll( $wp->query_vars['p'] );

						if ( isset( $_POST['wppp_title'] ) )
							$poll->set_title( sanitize_text_field( stripslashes( $_POST['wppp_title'] ) ) );

						if ( isset( $_POST['wppp_description'] ) )
							$poll->set_description( wp_kses_post( stripslashes( $_POST['wppp_description'] ) ) );

						$poll->voting()->set_is_voting_enabled( ! empty( $_POST['wppp_allow_voting'] ) );
						$poll->voting()->set_is_multiple_voting_enabled( ! empty( $_POST['wppp_allow_multiple_votes'] ) );

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

					} catch (Exception $e ) {

						wppp_api_respond_failure( 400, $e->getMessage() );
					}

					wppp_api_respond( 201, $poll->to_json() );

					break;

				case ( 'get' ) :

					try {

						$poll = new WPPP_Poll( $wp->query_vars['p'] );

					} catch ( Exception $e ) {
						wppp_api_respond_failure( 400, $e->getMessage() );
					}

					wppp_api_respond( 200, $poll->to_json() );

					break;

				case ( 'delete' ) :

					try {

						new WPPP_Poll( $wp->query_vars['p'] );

						WPPP_Polls_Engine::delete( $wp->query_vars['p'] );

					} catch( Exception $e ) {
						wppp_api_respond_failure( 400, $e->getMessage() );
					}

					wppp_api_respond( 204 );

					break;
			}

			exit;
		}
	) );
} );