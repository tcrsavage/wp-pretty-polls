
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

	/**
	 * Draw the poll
	 *
	 * @param array $args
	 */
	function draw( $args = array() ) {

		?>
		<div class="wpp-poll wppp-poll-<?php echo $this->poll->get_id(); ?>">

			<?php $this->draw_scripts(); ?>

			<div id="wppp-poll-<?php echo $this->poll->get_id(); ?>-vote-tab" >
				<?php $this->draw_voting(); ?>
			</div>

			<div id="wppp-poll-<?php echo $this->poll->get_id(); ?>-results-tab" >
				<?php $this->draw_results(); ?>
			</div>

		</div>
		<?php
	}

	/**
	 * Draw the voting section of the poll
	 *
	 */
	function draw_voting() {
		?>
		<div>
			<div class="wppp-title">
				<span><?php echo esc_textarea( $this->poll->get_post()->post_title ); ?></span>
			</div>

			<div class="wppp-question">
				<span><?php echo esc_textarea( $this->poll->get_post()->post_content ); ?></span>
			</div>

			<ul class="wppp-options">
				<?php foreach ( $this->poll->get_options() as $key => $val ) : ?>
					<li class="wppp-option-container">
						<label for="wppp-vote-option-<?php echo $this->poll->get_id() . '-' . $key; ?>"><?php echo esc_textarea( $val['title'] ); ?></label>
						<input id="wppp-vote-option-<?php echo $this->poll->get_id() . '-' . $key; ?>" type="radio" class="wppp-option wppp-js-option" name="wppp_vote" value="<?php echo esc_attr( $key ); ?>" />
					</li>
				<?php endforeach; ?>
			</ul>

			<button class="wppp-js-submit">Vote</button>
		</div>
		<?php
	}

	/**
	 * Draw the results section of the poll
	 *
	 */
	function draw_results() {

		?>
		<div>
			<span>RESULTS:</span>
			<ul>
				<?php $results = $this->poll->voting()->get_votes_totals(); ?>

				<?php foreach ( $this->poll->get_options() as $option_key => $option_value ): ?>
					<li><?php echo $option_value['title'] . '->' . ( ( ! empty( $results[$option_key] ) ) ? $results[$option_key] : 0 ) ?></li>
				<?php endforeach; ?>

			</ul>
		</div>
		<?php
	}

	/**
	 * Draw the scripts required for the voting and results sections of the poll
	 *
	 */
	function draw_scripts() {
		?>
		<script type="text/javascript">
			jQuery( document ).ready( function() {

				var apiUrl = '<?php echo WPPP_API_URL; ?>';

				jQuery( '.wppp-poll-<?php echo $this->poll->get_id(); ?> .wppp-js-submit' ).click( function( e ) {

					e.preventDefault();

					var selected_options = new Array();

					jQuery( '.wppp-poll-<?php echo $this->poll->get_id(); ?> .wppp-js-option' ).each( function() {

						if ( jQuery( this ).is( ':checked' ) )
							selected_options.push( jQuery( this ).val() );
					} );

					jQuery.ajax( '<?php echo WPPP_API_URL; ?>' + '/' + '<?php echo $this->poll->get_id(); ?>' + '/vote/', {
						type 	: 'post',
						data	: { selected_options: selected_options },
						success	: function( data ) {

						}
					} );
				} );
			} );
		</script>
		<?php
	}
}