<?php

define( 'WPPP_INCLUDES_PATH', WPPP_PATH . '/includes' );

add_action( 'plugins_loaded', 'wppp_includes_loader' );

function wppp_includes_loader() {

	require_once( WPPP_INCLUDES_PATH . '/tlc-transients/tlc-transients.php' );

	if ( defined( 'HM_CORE_PATH' ) || defined( 'HELPERPATH' ) )
		return;

	require_once( WPPP_INCLUDES_PATH . '/hm-core/hm-core.plugin.php' );
}