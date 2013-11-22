<?php

/**
 * Class WPPP_Settings
 */
class WPPP_Settings {

	/**
	 * Store the class instance statically
	 *
	 * @var
	 */
	static $instance;

	/**
	 *
	 */
	function __construct() {

	}

	/**
	 * @return WPPP_Settings
	 */
	static function get_instance() {

		if ( empty( self::$instance ) )
			self::$instance = new WPPP_Settings();


		return self::$instance;
	}

	/**
	 * Check if we should load the default stylesheet
	 *
	 * @return mixed|void
	 */
	function is_default_styles_enabled() {

		return get_option( 'wppp_default_styles_enabled', 1 );
	}

	/**
	 * Update whether or not we should load the default stylesheet
	 *
	 * @param $enabled
	 */
	function set_default_styles_enabled( $enabled ) {

		update_option( 'wppp_default_styles_enabled', ( $enabled ) ? '1' : '0' );
	}

}