<?php

require_once( ABSPATH . '/wp-admin/includes/class-wp-list-table.php' );
require_once( ABSPATH . '/wp-admin/includes/class-wp-posts-list-table.php' );

/** Extend the WP_List_Table class to provide a list table of WPPP Polls
 *
 */
class WPPP_Polls_List_Table extends WP_List_Table {

	/**
	 * The construct
	 *
	 */
	function __construct() {

		parent::__construct ( array(
			'singular'  => 'poll',
			'plural'    => 'polls',
			'ajax'      => false
		) );
	}

	/**
	 * Prepare the items for the list table
	 *
	 * @param array $args
	 */
	public function prepare_items( $args = array() ) {

		// Define the columns
		$columns = $this->get_columns();
		$hidden = array();
		$sortable = $this->get_sortable_columns();

		$this->_column_headers = array( $columns, $hidden, $sortable );

		$args = wp_parse_args( $args, array(

			'post_type' => 'wppp_poll'

		) );
		$posts = new WP_Query( $args );

		$this->set_pagination_args( array(
			'total_items' => $posts->post_count,                    // number of items
			'per_page'    => 10,                                    // items to show on a page
			'total_pages' => ceil ($posts->post_count/10 )          // number of pages
		) );

		foreach ( $posts->get_posts() as $key => $postObject )
			$this->items[] = WPPP_Poll::get_by_post_id( $postObject->ID );
	}

	/**
	 * Define the columns in the list table
	 *
	 * @return array
	 */
	function get_columns() {

		$columns = array(
			'title'         => 'Title',
			'votes'   		=> 'Votes',
			'fields'        => 'Fields'
		);

		return $columns;
	}

	/**
	 * Define the sortable columns in the list table
	 *
	 * @return array
	 */
	function get_sortable_columns() {

		$sortable_columns = array(
			'title'         => array( 'title', true ),     //true means its already sorted
			'votes'   		=> array( 'votes', false ),
			'fields'        => array( 'fields', false )
		);

		return array();
	}

	/**
	 * The 'title' column
	 *
	 * @param WPPP_Poll $item
	 * @return string
	 */
	function column_title( $item ) {

		//Build row actions
		$actions = array(
			'edit'      => sprintf( __( '<a href="?page=%s&action=%s&poll=%s">Edit</a>', 'WPPP' ),'wppp_edit_poll','edit',$item->get_id() ),
			'delete'    => sprintf( __( '<a href="#" wppp-data-id="%s">Delete</a>', 'WPPP' ), $item->get_id() ),
		);

		//Return the title contents
		return sprintf('%1$s %2$s',
			/*$1%s*/ $item->get_post()->post_title,
			/*$2%s*/ $this->row_actions( $actions )
		);

	}

	/**
	 * The 'votes' column
	 *
	 * @param WPPP_Poll $item
	 * @return string
	 */
	function column_votes( $item ) {

		return count( array() );
	}

	/**
	 * The 'fields' column
	 *
	 * @param WPPP_Poll $item
	 * @return string
	 */
	function column_fields( $item ) {

		return count( $item->get_options() );
	}

	/**
	 * Get a list of bulk actions
	 *
	 * @return array
	 */
	function get_bulk_actions() {

		$actions = array(
			'delete'    => 'Delete'
		);

		return $actions;
	}

	/**
	 * Process bulk actions
	 */
	function process_bulk_action() {

		//Detect when a bulk action is being triggered...
		if( 'delete' === $this->current_action() ) {

		}

	}

}