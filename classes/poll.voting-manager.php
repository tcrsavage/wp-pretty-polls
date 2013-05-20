<?php

class WPPP_Voting_Manager {

	var $poll;

	function __construct( WPPP_Poll $poll ) {

		$this->poll = $poll;
	}

	function vote( $vote ) {

		if ( ! is_array( $vote ) )
			$vote = array( $vote );

		//Save a list of votes by ip address
		$votes = $this->get_votes_list();

		foreach ( $vote as $option_selected ) {

			$option_selected = (int) $option_selected;

			$votes[$_SERVER['REMOTE_ADDR']][$option_selected] = ( isset( $votes[$_SERVER['REMOTE_ADDR']][$option_selected] ) && is_numeric( $votes[$_SERVER['REMOTE_ADDR']][$option_selected] ) ) ? ++$votes[$_SERVER['REMOTE_ADDR']][$option_selected] : 1;
		}

		$this->poll->set_meta( 'wppp_votes', $votes );

		//Save an increment to the list of totals so we don't have to count it up later
		$vote_totals = $this->get_votes_totals();

		foreach ( $vote as $option_selected )
			$vote_totals[$option_selected] = ( isset( $vote_totals[$option_selected] ) && is_numeric( $vote_totals[$option_selected] ) ) ? ++$vote_totals[$option_selected] : 1;

		$this->poll->set_meta( 'wppp_vote_totals', $vote_totals );
	}

	function can_vote( $option_id = null ) {

		if ( ! $this->has_voted() && $this->is_voting_enabled() )
			return true;

		else if ( $this->can_vote_multiple_times() && $this->is_voting_enabled() )
			return true;

		else if ( $this->can_vote_multiple_options() && ! $this->has_voted_for_option( $option_id ) )
			return true;

		return false;

	}

	function is_voting_cookies_enabled() {

		return true;
	}

	function is_voting_enabled() {

		return true;
	}

	function can_vote_multiple_options() {

		return true;
	}

	function can_vote_multiple_times() {

		return true;
	}

	function has_voted() {

		$votes = $this->get_votes_list();

		return ( ! empty( $votes[$_SERVER['REMOTE_ADDR']] ) ) ? true : false;
	}

	function has_voted_for_option( $option_id ) {

		$votes = $this->get_votes_list();

		return ( ! empty( $votes[$_SERVER['REMOTE_ADDR']][$option_id] ) ) ? true : false;
	}

	function get_votes_list() {

		$votes = $this->poll->get_meta( 'wppp_votes', true );

		if ( empty( $votes ) || ! is_array( $votes ) )
			$votes = array();

		return $votes;

	}

	function get_votes_totals() {

		$vote_totals = $this->poll->get_meta( 'wppp_vote_totals', true );

		if ( empty( $vote_totals ) || ! is_array( $vote_totals ) )
			$vote_totals = array();

		return $vote_totals;
	}

	function get_votes_data() {

		$votes_data = array();

		$votes_totals = $this->get_votes_totals();
		$poll_options = $this->poll->get_options();

		foreach ( $votes_totals as $option => $val )
			if ( ! array_key_exists( $option, $poll_options ) )
				unset( $votes_totals[$option] );

		$votes_total = array_sum( $votes_totals );

		foreach ( $poll_options as $id => $val ) {

			$votes_data[$id] = array(
				'votes' => ( ! empty ( $votes_totals[$id] ) ) ? $votes_totals[$id] : 0,
				'percentage' => ( ! empty ( $votes_totals[$id] ) ) ? ( $votes_totals[$id]/$votes_total ) * 100 : 0,
				'title' => $val['title']
			);

		}

		return $votes_data;
	}

}