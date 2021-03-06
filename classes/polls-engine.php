<?php

/**
 * Management class for getting, creating and deleting polls
 *
 * Class WPPP_Polls_Engine
 */
class WPPP_Polls_Engine {

	/**
	 * @var
	 */
	static $poll_instances;

	/**
	 * Get a array of all current poll ids
	 *
	 * @return array
	 */
	static function get_ids() {

		return get_option( 'wppp_polls_list', array() );
	}

	/**
	 * Get a array of all poll objects
	 *
	 * @return WPPP_Poll[]
	 */
	static function get_polls() {

		$polls = array();

		foreach ( self::get_ids() as $id ) {

			if ( self::get_by_post_id( $id ) )
				$polls[] = self::get_by_post_id( $id );
		}

		return $polls;
	}

	/**
	 * Get an instance of a given poll
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
	 * Get an instance of a poll for a given post id
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
			throw new Exception( $post_id->get_error_message() );

		$polls_list = ( get_option(  'wppp_polls_list' ) ) ? get_option( 'wppp_polls_list' ) : array();
		$polls_list[$polls_count] = $post_id;

		update_option( 'wppp_polls_count', $polls_count );
		update_option( 'wppp_polls_list', $polls_list );

		$poll = self::get( $polls_count );

		if ( ! $poll )
			throw new Exception( __( 'Error getting poll after creation.', 'WPPP' ) );
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