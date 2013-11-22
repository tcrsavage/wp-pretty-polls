<?php
/**
 * Class WPPP_Renderer
 */
class WPPP_Renderer {

	/**
	 * The poll used to instantiate the class
	 *
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
	 * Output buffer the poll rendered html
	 *
	 * @return string
	 */
	function get_draw() {

		ob_start() ;

		$this->draw();

		return ob_get_clean();
	}

	/**
	 * Draw the poll
	 *
	 * @param array $args
	 */
	function draw( $args = array() ) {

		$args = wp_parse_args( $args, array(

			'width' 			=> '',
			'height'			=> '',
			'template'			=> 'standard',
			'show_title' 		=> true,
			'show_description'	=> true,
		) );

		if ( $args['template'] == 'standard' )
			hm_get_template_part( WPPP_PATH . '/templates/standard/wppp-standard.php', array( 'poll' => $this->poll, 'renderer' => $this, 'args' => $args ) );

	}


}