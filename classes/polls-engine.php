<?php

class WPPP_Polls_Engine {

	static $poll_instances;

	static function get_ids() {

		return get_option( 'wppp_polls_list' );
	}

	/**
	 * Get an instance of this class
	 *
	 * @param $poll_id
	 * @return bool | WPPP_Poll
	 */
	static function get( $poll_id ) {

		//if instance hasn't already been created for this poll_id
		if ( empty( self::$poll_instances[$poll_id] ) ) {

			//try to instantiate class object
			try {
				self::$poll_instances[$poll_id] = new WPPP_Poll( $poll_id );
			} catch ( Exception $e ) {
				//On failure, return false
				return false;
			}
		}

		//Return the class object
		return self::$poll_instances[$poll_id];
	}

	/**
	 * Get an instance of this class
	 *
	 * @param $poll_post_id
	 * @return bool|WPPP_Poll
	 */
	static function get_by_post_id( $poll_post_id ) {

		$polls_list = get_option( 'wppp_polls_list' );

		foreach ( $polls_list as $poll_id => $post_id  )
			if ( $post_id === $poll_post_id )
				return self::get( $poll_id );

		return false;
	}

	/**
	 * Create a new poll
	 *
	 * @return bool|WPPP_Poll
	 */
	static function add( $title, $description ) {

		$polls_count = get_option( 'wppp_polls_count', 0 );
		$polls_count++;

		$title = ( $title ) ? $title : 'WP Pretty Poll ' . $polls_count;

		$post = array(
			'post_title' => $title,
			'post_content' => $description,
			'post_status' => 'draft',
			'post_type' => 'wppp_poll',
			'post_name'	=> sanitize_title( 'WP Pretty Poll ' . $polls_count )
		);

		$post_id = wp_insert_post( $post );

		if ( is_wp_error( $post_id ) )
			return false;

		$polls_list = ( get_option(  'wppp_polls_list' ) ) ? get_option( 'wppp_polls_list' ) : array();
		$polls_list[$polls_count] = $post_id;

		update_option( 'wppp_polls_count', $polls_count );
		update_option( 'wppp_polls_list', $polls_list );

		return self::get( $polls_count );
	}

	/**
	 * Delete a poll
	 *
	 * @param $poll_id
	 * @return bool
	 */
	static function delete( $poll_id ) {

		$polls_list = ( get_option(  'wppp_polls_list' ) ) ? get_option( 'wppp_polls_list' ) : array();

		if ( ! isset( $polls_list[$poll_id] ) )
			return false;

		wp_delete_post( $polls_list[$poll_id] );

		unset( $polls_list[$poll_id] );

		update_option( 'wppp_polls_list', $polls_list );

		return true;
	}

}