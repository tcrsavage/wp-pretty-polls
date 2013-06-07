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

		$option_count = $this->increment_options_counter();

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

		if ( ! array_key_exists( $id, $options ) ) {
			$this->increment_options_counter();
			error_log( 'nooo!' );
		}
		$options[$id] = $val;

		$this->set_meta( 'wppp_options', $options );

		return true;
	}

	/**
	 * Check if an option exists for the given id
	 *
	 * @param $id
	 */
	function option_exists( $id ) {

		$options = $this->get_options();

		return array_key_exists( $id, $options );
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
	 * This is used to choose unique ids for new options, it only ever increments, and ensures that new options don't inherit votes from deleted ones
	 *
	 * @return int
	 */
	function get_options_counter() {

		return ( $this->get_meta( 'wppp_options_counter' ) ) ? $this->get_meta( 'wppp_options_counter' ) : 0;
	}

	/**
	 * Add 1 to the current options count and return the result
	 *
	 * @return int
	 */
	function increment_options_counter() {

		$count = ( $this->get_options_counter() + 1 );

		$this->set_meta( 'wppp_options_counter', $count );

		return $count;
	}

	/**
	 * Delete all current options
	 */
	function clear_options() {

		$this->set_meta( 'wppp_options', array() );
	}

	/**
	 * Set the poll's description field
	 *
	 * @param $val
	 * @return bool
	 */
	function set_description( $val ) {

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

	function get_title() {

		return $this->get_post()->post_title;
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


	function settings() {

		return WPPP_Settings::get_instance();
	}
}