<?php
add_action( 'init', function() {

	hm_add_rewrite_rule( array(
		'regex' 			=> '^wppp/api/settings?$',
		'request_method' 	=> 'post',
		'request_callback' 	=> function() {

			wppp_authenticate_admin_api_request();

			$settings = WPPP_Settings::get_instance();

			if ( isset( $_POST['is_default_styles_enabled'] ) )
				$settings->set_default_styles_enabled( sanitize_text_field( $_POST['is_default_styles_enabled'] ) );

			if ( isset( $_POST['is_default_scripts_enabled'] ) )
				$settings->set_default_scripts_enabled( sanitize_text_field( $_POST['is_default_scripts_enabled'] ) );

			wppp_api_respond( 200, $settings->to_json() );
		}

	) );
} );