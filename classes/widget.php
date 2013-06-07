<?php
/**
 * Adds WPPP_Widget widget.
 */
class WPPP_Widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		parent::__construct(
			'wppp_widget', // Base ID
			'WPPP_Widget', // Name
			array( 'description' => __( 'A WP Pretty Polls widget to display your poll', 'WPPP' ), ) // Args
		);
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {

		$poll = WPPP_Polls_Engine::get( $instance['poll_id'] );

		echo $args['before_widget'];

		if ( ! empty( $instance['widget_title'] ) )
			echo $args['before_title'] . $instance['widget_title'] . $args['after_title'];

		$draw_args = array_merge( $instance, array(
			'is_widget' => true,
			'widget_id'	=> $args['widget_id']
		) );

		$poll->renderer()->draw( $draw_args );

		echo $args['after_widget'];
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {

		$instance = array();

		$instance['poll_id'] = strip_tags( $new_instance['poll_id'] );
		$instance['widget_title'] = strip_tags( $new_instance['widget_title'] );
		$instance['show_title'] = ! empty( $new_instance['show_title'] ) ? true : false;

		return $instance;
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {

		$args = wp_parse_args( $instance, array(

			'poll_id'	=> '',
			'show_title' 	=> true,
			'widget_title'	=> '',

		) ); ?>

		<div>
			<label for="<?php echo $this->get_field_id( 'widget_title' ); ?>"><?php _e( 'Widget Title:', 'WPPP' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'widget_title' ); ?>" name="<?php echo $this->get_field_name( 'widget_title' ); ?>" value="<?php echo $args['widget_title']; ?>" />

			<label for="<?php echo $this->get_field_id( 'show_title' ); ?>"><?php _e( 'Show Poll Title:', 'WPPP' ); ?></label>
			<input type="checkbox" class="widefat" id="<?php echo $this->get_field_id( 'show_title' ); ?>" name="<?php echo $this->get_field_name( 'show_title' ); ?>" <?php checked($args['show_title'] ); ?> />


			<label for="<?php echo $this->get_field_id( 'poll_id' ); ?>"><?php _e( 'Poll:', 'WPPP' ); ?></label>
			<select class="widefat" id="<?php echo $this->get_field_id( 'poll_id' ); ?>" name="<?php echo $this->get_field_name( 'poll_id' ); ?>" >
				<option value="">Select Poll</option>
				<?php foreach( WPPP_Polls_Engine::get_ids() as $id => $post_id ) : ?>
					<?php if ( WPPP_Polls_Engine::get( $id ) ) : ?>
						<option <?php selected( $id == $args['poll_id'] ); ?> value="<?php echo $id; ?>"><?php echo WPPP_Polls_Engine::get( $id )->get_title(); ?></option>
					<?php endif; ?>
				<?php endforeach; ?>
			</select>
		</div>
		<?php
	}

}

// register widget
add_action( 'widgets_init', 'wppp_widget_init' );

function wppp_widget_init() {

	register_widget( "wppp_widget" );
}