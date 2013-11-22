<?php

/**
 * Class WPPP_Voting_Manager
 */
class WPPP_Voting_Manager {

	/**
	 * @var WPPP_Poll
	 */
	var $poll;

	/**
	 * @param WPPP_Poll $poll
	 */
	function __construct( WPPP_Poll $poll ) {

		$this->poll = $poll;
	}

	/**
	 * Add a vote
	 *
	 * @param $vote
	 */
	function vote( $vote ) {

		if ( ! is_array( $vote ) )
			$vote = array( $vote );

		//Save a list of votes by ip address
		$votes = $this->get_votes_list();

		foreach ( $vote as $option_selected ) {
			$option_selected = (int) $option_selected;
			$votes[$_SERVER['REMOTE_ADDR']][$option_selected] = ( isset( $votes[$_SERVER['REMOTE_ADDR']][$option_selected] ) && is_numeric( $votes[$_SERVER['REMOTE_ADDR']][$option_selected] ) ) ? ++$votes[$_SERVER['REMOTE_ADDR']][$option_selected] : 1;
		}

		$this->set_meta( 'wppp_votes', $votes );

		//Save an increment to the list of totals so we don't have to count it up later
		$vote_totals = $this->get_votes_totals();

		foreach ( $vote as $option_selected )
			$vote_totals[$option_selected] = ( isset( $vote_totals[$option_selected] ) && is_numeric( $vote_totals[$option_selected] ) ) ? ++$vote_totals[$option_selected] : 1;

		$this->set_meta( 'wppp_vote_totals', $vote_totals );
	}

	/**
	 * Check if can vote
	 *
	 * @return bool
	 */
	function can_vote() {

		if ( ! $this->is_logged_out_voting_enabled() && ! is_user_logged_in()  )
			return false;

		if ( ! $this->has_voted() && $this->is_voting_enabled() )
			return true;

		else if ( $this->is_multiple_voting_enabled() && $this->is_voting_enabled() )
			return true;

		return false;
	}

	/**
	 * Check if logged out users can vote
	 *
	 * @return bool
	 */
	function is_logged_out_voting_enabled() {

		return ( $this->get_meta( 'wppp_can_vote_logged_out' ) || $this->get_meta( 'wppp_can_vote_logged_out' ) === '' ) ? true : false;
	}

	/**
	 * Set if logged out users can vote
	 *
	 * @param $bool
	 */
	function set_is_logged_out_voting_enabled( $bool ) {

		$bool = ( $bool ) ? '1' : '0';

		$this->set_meta( 'wppp_can_vote_logged_out', $bool );
	}

	/**
	 * Check if voting is enabled
	 *
	 * @return bool
	 */
	function is_voting_enabled() {

		return ( $this->get_meta( 'wppp_voting_enabled' ) || $this->get_meta( 'wppp_voting_enabled' ) === '' ) ? true : false;
	}

	/**
	 * Set if voting is enabled or not
	 *
	 * @param $bool
	 */
	function set_is_voting_enabled( $bool ) {

		$bool = ( $bool ) ? '1' : '0';

		$this->set_meta( 'wppp_voting_enabled', $bool );
	}

	/**
	 * Check if can vote multiple times
	 *
	 * @return bool
	 */
	function is_multiple_voting_enabled() {

		return ( $this->get_meta( 'wppp_can_vote_multiple_times' ) || $this->get_meta( 'wppp_can_vote_multiple_times' ) === '' ) ? true : false;
	}

	/**
	 * Set can vote multiple times
	 *
	 * @param $bool
	 */
	function set_is_multiple_voting_enabled( $bool ) {

		$bool = ( $bool ) ? '1' : '0';

		$this->set_meta( 'wppp_can_vote_multiple_times', $bool );
	}

	/**
	 * Check if has voted
	 *
	 * @return bool
	 */
	function has_voted() {

		$votes = $this->get_votes_list();

		return ( ! empty( $votes[$_SERVER['REMOTE_ADDR']] ) ) ? true : false;
	}

	/**
	 * Check if has voted to a specific option
	 *
	 * @param $option_id
	 * @return bool
	 */
	function has_voted_for_option( $option_id ) {

		$votes = $this->get_votes_list();

		return ( ! empty( $votes[$_SERVER['REMOTE_ADDR']][$option_id] ) ) ? true : false;
	}

	/**
	 * Get the list of votes
	 *
	 * @return array|mixed
	 */
	function get_votes_list() {

		$votes = $this->get_meta( 'wppp_votes', true );

		if ( empty( $votes ) || ! is_array( $votes ) )
			$votes = array();

		return $votes;

	}

	/**
	 * Get the votes totals
	 *
	 * @return array|mixed
	 */
	function get_votes_totals() {

		$vote_totals = $this->get_meta( 'wppp_vote_totals', true );

		if ( empty( $vote_totals ) || ! is_array( $vote_totals ) )
			$vote_totals = array();

		return $vote_totals;
	}

	/**
	 * Get a list of votes totals with percentages and counts
	 *
	 * @return array
	 */
	function get_votes_data() {

		$votes_data = array();

		$votes_totals = $this->get_votes_totals();
		$poll_options = $this->poll->get_options();

		foreach ( $votes_totals as $option => $val ){
			if ( ! array_key_exists( $option, $poll_options ) )
				unset( $votes_totals[$option] );
		}

		$votes_total = array_sum( $votes_totals );

		foreach ( $poll_options as $id => $val ) {
			$votes_data[$id] = array(
				'votes' => ( ! empty ( $votes_totals[$id] ) ) ? $votes_totals[$id] : 0,
				'percentage' => ( ! empty ( $votes_totals[$id] ) ) ? round( ( $votes_totals[$id]/$votes_total ) * 100, 2 ) : 0,
				'title' => $val['title']
			);
		}

		return $votes_data;
	}

	/**
	 * Set vote meta
	 *
	 * @param $key
	 * @param $value
	 */
	function set_meta( $key, $value ) {

		$this->poll->set_meta( $key, $value );
	}

	/**
	 * Get vote meta
	 *
	 * @param $key
	 * @return mixed
	 */
	function get_meta( $key ) {

		return $this->poll->get_meta( $key );
	}

	function to_array( $include_vote_list = false ) {

		$a = array(
			'is_voting_enabled'				=> $this->is_voting_enabled(),
			'is_multiple_voting_enabled'	=> $this->is_multiple_voting_enabled(),
			'is_logged_out_voting_enabled'	=> $this->is_logged_out_voting_enabled(),
			'votes_totals'					=> $this->get_votes_totals(),
			'votes_data'					=> $this->get_votes_data()
		);

		if ( $include_vote_list )
			$a['votes_list'] = $this->get_votes_list();

		return $a;
	}

	function to_json() {

		return json_encode( $this->to_array() );
	}

}