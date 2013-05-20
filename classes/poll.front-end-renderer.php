
<?php

/**
 * Class WPPP_Front_End_Renderer
 */
class WPPP_Front_End_Renderer {

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

		hm_get_template_part( WPPP_PATH . '/templates/standard/wppp-standard.php', array( 'poll' => $this->poll, 'renderer' => $this ) );
	}


}