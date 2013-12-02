<?php
add_action( 'init', function() {

	hm_add_rewrite_rule( array(
		'regex' => '^wppp/api/polls/([\d]+)/votes/?$',
		'query' => 'p=$matches[1]',
		'request_methods' => array( 'get', 'post' ),
		'post_query_properties'	=> array( 'is_404' => false ),
		'request_callback' => function( WP $wp ) {

			switch ( strtolower( $_SERVER['REQUEST_METHOD'] ) ) {

				case ( 'post' ) :

					try {

						$vote = ( ! empty( $_POST['selected_options'] ) ) ? $_POST['selected_options'] : array();

						if ( ! is_array( $vote ) )
							$vote = array( $vote );

						//Make sure the vote array is clean
						$vote = array_map( 'sanitize_text_field', $vote );

						//We don't currently support voting for multiple options, only take the user's first vote from their vote array
						$vote = array( reset( $vote ) );

						if ( ! $vote )
							throw new Exception( __( 'Missing vote in submission.' ) );

						$poll = new WPPP_Poll( $wp->query_vars['p'] );

						if ( ! $poll->voting()->can_vote() )
							throw new Exception( __( 'Sorry, you do not currently have permission to vote on this poll', 'WPPP' ) );

						$poll->voting()->vote( $vote );

					} catch ( Exception $e ) {
						wppp_api_respond_failure( 400, $e->getMessage() );
					}

					break ;

				case ( 'get' ) :

					try {

						$poll = new WPPP_Poll( $wp->query_vars['p'] );
					} catch ( Exception $e ) {
						wppp_api_respond_failure( 400, $e->getMessage() );
					}

					break;
			}

			wppp_api_respond( 200, $poll->voting()->to_json() );

			exit;
		}
	) );

} );