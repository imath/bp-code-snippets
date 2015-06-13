<?php
/* sidebar widgets */
// Exit if accessed directly

if ( !defined( 'ABSPATH' ) ) exit;

/* Register widgets for BP Code Snippets */
function bp_code_snippets_register_widgets() {
	add_action('widgets_init', create_function('', 'return register_widget("BP_Code_Snippets_Most_Fav");') );
	
}
add_action( 'bp_register_widgets', 'bp_code_snippets_register_widgets' );

/*** Fav snippets WIDGET *****************/

class BP_Code_Snippets_Most_Fav extends WP_Widget {

	function bp_code_snippets_most_fav() {
		$this->__construct();
	}

	function __construct() {
		$widget_ops = array( 'description' => __( 'The most favorited snippets', 'bp-code-snippets' ) );
		parent::__construct( false, $name = __( 'Most Favorited Snippets', 'bp-code-snippets' ), $widget_ops );
	}

	function widget( $args, $instance ) {
		global $bp;

		extract( $args );

		if ( !$instance['max_snippets'] )
			$instance['max_snippets'] = 5;

		echo $before_widget;
		echo $before_title
		   . $instance['title']
		   . $after_title; ?>

		<?php
		$fav_snippets = BP_Code_Snippets::widget_most_favorited( $instance['max_snippets'] );
		?>
		
		<?php if( count($fav_snippets) > 0 ):?>

			<ul id="snippets-list" class="item-list">
				<?php foreach ( $fav_snippets as $fav ): ?>
					<?php
					
					$permalink = bp_code_snippets_build_perma( array('id'           => $fav->id_cs, 
																	 'item_id'      => $fav->item_id,
																	 'object'       => $fav->object,
																	 'secondary_id' => $fav->secondary_id) );
																	
					$type = $fav->snippet_type;
					if($type == 'applescript')
						$type = 'Script';
					
					?>
					<li>
						<div class="snippet-avatar">
							<?php echo $type; ?></a>
						</div>

						<div class="item">
							<div class="item-title fn"><a href="<?php echo $permalink; ?>" title="<?php echo $fav->snippet_title; ?>"><?php echo $fav->snippet_title; ?></a></div>
							<div class="item-meta">
								<span class="activity">
									
									<?php printf( _n( 'favorited 1 time', 'favorited %1$s times', $fav->meta_value, 'bp-code-snippets' ), number_format_i18n( $fav->meta_value ) ); ?>
								</span>
							</div>
						</div>
					</li>

				<?php endforeach; ?>
			</ul>

		<?php else: ?>

			<div class="widget-error">
				<?php _e('No favorited snippet yet!', 'bp-code-snippets') ?>
			</div>

		<?php endif; ?>

		<?php echo $after_widget; ?>
	<?php
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['max_snippets'] = strip_tags( $new_instance['max_snippets'] );

		return $instance;
	}

	function form( $instance ) {
		$defaults = array(
			'title' => __( 'Most Favorited Snippets', 'bp-code-snippets' ),
			'max_snippets' => 5
		);
		$instance = wp_parse_args( (array) $instance, $defaults );

		$title = strip_tags( $instance['title'] );
		$max_snippets = strip_tags( $instance['max_snippets'] );
		?>

		<p><label for="bp-code-snippets-widget-title"><?php _e('Title:', 'bp-code-snippets'); ?> <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" style="width: 100%" /></label></p>

		<p><label for="bp-code-snippets-widget-snippets-max"><?php _e('Max snippets to show:', 'bp-code-snippets'); ?> <input class="widefat" id="<?php echo $this->get_field_id( 'max_snippets' ); ?>" name="<?php echo $this->get_field_name( 'max_snippets' ); ?>" type="text" value="<?php echo esc_attr( $max_snippets ); ?>" style="width: 30%" /></label></p>

	<?php
	}
}
?>