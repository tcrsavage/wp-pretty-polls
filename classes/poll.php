<?php

/**
 * Class WPPP_Poll
 */
class WPPP_Poll  {

	/**
	 * Class instances are cached here
	 *
	 * @var array
	 */
	static $instances = array();

	/**
	 * Poll Id
	 *
	 * @var int
	 */
	var $id;

	/**
	 * Id of post associated to the poll
	 *
	 * @var int
	 */
	var $post_id;

	/**
	 * The post object of the poll
	 *
	 * @var stdClass
	 */
	var $post_object;

	/**
	 * The frontend rendering class
	 *
	 * @var WPPP_Front_End_Renderer
	 */
	var $renderer;

	/**
	 * @param $poll_id
	 */
	function __construct( $poll_id ) {

		$this->id = $poll_id;

		$polls_list = get_option( 'wppp_polls_list' );

		//If the wp post id cannot be found for the given poll id, throw exception
		if ( empty( $polls_list[$poll_id] ) )
			throw new Exception( __( 'Poll does not exist', 'WPPP' ) );

		$this->post_id = $polls_list[$poll_id];

		$this->post_object = get_post( $this->post_id );
	}

	/**
	 * Get an instance of this class
	 *
	 * @param $poll_id
	 * @return bool | WPPP_Poll
	 */
	static function get( $poll_id ) {

		//if instance hasn't already been created for this poll_id
		if ( empty( $instances[$poll_id] ) ) {

			//try to instantiate class object
			try {
				$instances[$poll_id] = new self( $poll_id );
			} catch ( Exception $e ) {
				//On failure, return false
				return false;
			}
		}

		//Return the class object
		return $instances[$poll_id];
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
	static function add() {

		$polls_count = get_option( 'wppp_polls_count', 0 );
		$polls_count++;

		$post = array(
			'post_title' => 'WP Pretty Poll ' . $polls_count,
			'post_content' => '',
			'post_status' => 'draft',
			'post_type' => 'wppp_poll'
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

	/**
	 * Get the post object of the current poll instance
	 *
	 * @return mixed
	 */
	function get_post() {

		return get_post( $this->get_post_id() );
	}

	/**
	 * Get the id of the current poll instance
	 *
	 * @return mixed
	 */
	function get_id() {

		return $this->id;
	}

	/**
	 * Get the post id of the current poll instance
	 *
	 * @return mixed
	 */
	function get_post_id() {

		return $this->post_id;
	}

	/**
	 * Add a new option to the poll's answer list
	 *
	 * @param $val
	 * @return int|mixed
	 */
	function add_option( $val ) {

		$option_count = ( $this->get_meta( 'wppp_option_count' ) ) ? $this->get_meta( 'wppp_option_count' ) : 0;
		$option_count++;

		$options = $this->get_options();

		$options[$option_count] = $val;

		$this->set_meta( 'wppp_options', $options );

		//return the option's ID;
		return $option_count;
	}

	/**
	 * Delete an option from the poll's answer list
	 *
	 * @param $id
	 * @return bool
	 */
	function delete_option( $id ) {

		$options = $this->get_options();

		if ( ! isset( $options[$id] ) )
			return false;

		unset( $options[$id] );

		$this->set_meta( 'wppp_options', $options );

		return true;
	}

	/**
	 * Update a given option id, or create that option if it doesn't exist
	 *
	 * @param $id
	 * @param $val
	 * @return bool
	 */
	function set_option( $id, $val ) {

		$options = $this->get_options();

		$options[$id] = $val;

		$this->set_meta( 'wppp_options', $options );

		return true;
	}

	/**
	 * Get an array of the poll's options
	 *
	 * @return array|mixed
	 */
	function get_options() {

		return ( $this->get_meta( 'wppp_options' ) ) ? $this->get_meta( 'wppp_options' ) : array();
	}

	/**
	 * Get the number of options currently available
	 *
	 * @return int
	 */
	function get_options_count() {

		return count( $this->get_meta( 'wppp_option_count' ) );
	}

	/**
	 * Delete all current options
	 */
	function clear_options() {

		$this->set_meta( 'wppp_options', array() );
	}

	/**
	 * Set the poll's question field
	 *
	 * @param $val
	 * @return bool
	 */
	function set_question( $val ) {

		$post = array(
			'post_content' => $val,
			'ID' => $this->get_post_id()
		);

		$post_id = wp_update_post( $post );

		return ( ! is_wp_error( $post_id ) ) ? true : false;
	}

	/**
	 * Set the poll's title field
	 *
	 * @param $val
	 * @return bool
	 */
	function set_title( $val ) {

		$post = array(
			'post_title' => $val,
			'ID' => $this->get_post_id()
		);

		$post_id = wp_update_post( $post );

		return ( ! is_wp_error( $post_id ) ) ? true : false;
	}

	/**
	 * Get postmeta for the poll
	 *
	 * @param $key
	 * @return mixed
	 */
	function get_meta( $key ) {

		return get_post_meta( $this->get_post_id(), $key, true );
	}

	/**
	 * Set postmeta for the poll
	 *
	 * @param $key
	 * @param $value
	 * @return bool
	 */
	function set_meta( $key, $value ) {

		return update_post_meta( $this->get_post_id(), $key, $value );
	}

	/**
	 * Get an instance of the votes class
	 *
	 * @return WPPP_Voting_Manager
	 */
	function voting() {

		if ( empty( $this->voting ) )
			$this->voting = new WPPP_Voting_Manager( $this );

		return $this->voting;
	}

	/**
	 * Get an instance of the poll's rendering class
	 *
	 * @return WPPP_Front_End_Renderer
	 */
	function renderer() {

		if ( empty( $this->renderer ) )
			$this->renderer = new WPPP_Front_End_Renderer( $this );

		return $this->renderer;
	}

}