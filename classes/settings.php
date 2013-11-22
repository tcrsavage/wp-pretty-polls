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

		return get_option( 'wppp_default_styles_enabled', '1' );
	}

	/**
	 * Update whether or not we should load the default stylesheet
	 *
	 * @param $enabled
	 */
	function set_default_styles_enabled( $bool ) {

		update_option( 'wppp_default_styles_enabled', ( $bool ) ? '1' : '0' );
	}

	/**
	 * Check if we should load the default stylesheet
	 *
	 * @return mixed|void
	 */
	function is_default_scripts_enabled() {

		return get_option( 'wppp_default_scripts_enabled', '1' );
	}

	/**
	 * Update whether or not we should load the default stylesheet
	 *
	 * @param $enabled
	 */
	function set_default_scripts_enabled( $bool ) {

		update_option( 'wppp_default_scripts_enabled', ( $bool ) ? '1' : '0' );
	}

	/**
	 * Get an array of class properties
	 *
	 * @return array
	 */
	function to_array() {

		return array(
			'is_default_styles_enabled' 	=> $this->is_default_styles_enabled(),
			'is_default_scripts_enabled'	=> $this->is_default_scripts_enabled()
		);
	}

	/**
	 * Get a JSON encoded array of class properties
	 *
	 * @return string
	 */
	function to_json() {

		return json_encode( $this->to_array() );
	}

}