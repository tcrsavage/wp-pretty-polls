<?php
/*
Plugin Name: WP Pretty Polls
Description:
Author: Theo Savage, HumanMade
Version: 0.1.1
*/

define( 'WPPP_ROOT_PATH', str_replace( str_replace( WP_HOME, '', WP_SITEURL ), '', ABSPATH ) );
define( 'WPPP_PATH', dirname( __FILE__ ) . '/' );

define( 'WPPP_URL', str_replace( WPPP_ROOT_PATH, WP_HOME . '/', WPPP_PATH ) );
define( 'WPPP_API_URL', untrailingslashit( home_url( '/wppp/api/' ) ) );
define( 'WPPP_VERSION', '0.1.1' );

require_once( WPPP_PATH . '/controllers/admin.common.php' );
require_once( WPPP_PATH . '/controllers/admin.list.php' );
require_once( WPPP_PATH . '/controllers/admin.single.php' );
require_once( WPPP_PATH . '/controllers/admin.settings.php' );
require_once( WPPP_PATH . '/controllers/shortcodes.php' );

require_once( WPPP_PATH . '/classes/admin.polls-list-table.php' );
require_once( WPPP_PATH . '/classes/poll.php' );
require_once( WPPP_PATH . '/classes/poll.renderer.php' );
require_once( WPPP_PATH . '/classes/poll.voting-manager.php' );
require_once( WPPP_PATH . '/classes/settings.php' );
require_once( WPPP_PATH . '/classes/polls-engine.php' );
require_once( WPPP_PATH . '/classes/widget.php' );

require_once( WPPP_PATH . '/includes/loader.php' );
require_once( WPPP_PATH . '/api/api.php' );

/**
 * Code to be run after plugins are loaded
 */
function wppp_plugin_init() {

	wppp_register_poll_post_type();
}
add_action( 'plugins_loaded', 'wppp_plugin_init' );

/**
 * Code to be run in the admin only
 */
function wppp_plugin_admin_init() {

	wp_enqueue_style( 'wppp-admin-css', WPPP_URL . '/assets/admin.styles.css', array(), WPPP_VERSION );
}
add_action( 'admin_init', 'wppp_plugin_admin_init' );

/**
 * Enqueue jquery on the front end
 */
function wppp_frontend_scripts() {

	if ( WPPP_Settings::get_instance()->is_default_styles_enabled() )
		wp_enqueue_style( 'wppp-default-styles', WPPP_URL . '/assets/front-end.styles.css', array(), WPPP_VERSION );

	if ( WPPP_Settings::get_instance()->is_default_scripts_enabled() )
		wp_enqueue_script( 'wppp-default-scripts', WPPP_URL . '/assets/front-end.scripts.js', array( 'jquery' ), WPPP_VERSION );
}
add_action( 'wp_enqueue_scripts', 'wppp_frontend_scripts' );

/**
 * Register the poll post type
 */
function wppp_register_poll_post_type() {

	$labels = array(
		'name'                  => _x( 'Polls', 'post type general name' ),
		'singular_name'         => _x( 'Poll', 'post type singular name' ),
		'add_new'               => _x( 'Add New', 'poll' ),
		'add_new_item'          => __( 'Add New Poll' ),
		'edit_item'             => __( 'Edit Poll' ),
		'new_item'              => __( 'New Poll' ),
		'all_items'             => __( 'All Polls' ),
		'view_item'             => __( 'View Polls' ),
		'search_items'          => __( 'Search Polls' ),
		'not_found'             =>  __('No forms found'),
		'not_found_in_trash'    => __('No forms found in Trash'),
		'parent_item_colon'     => '',
		'menu_name'             => __('Forms')
	);

	$args = array(
		'labels'             => $labels,
		'public'             => false,
		'publicly_queryable' => false,
		'show_ui'            => false,
		'show_in_menu'       => false,
		'query_var'          => false,
		'rewrite'            => false,
		'capability_type'    => 'post',
		'has_archive'        => false,
		'hierarchical'       => false,
		'menu_position'      => null,
	);

	register_post_type( 'wppp_poll',$args );

}
