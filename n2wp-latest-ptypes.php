<?php
/**
 * Plugin Name: Latest Posts For Custom Types Widget
 * Plugin URI: http://new2wp.com/pro/latest-custom-post-type-posts-sidebar-widget
 * Description: A New2WP sidebar Widget for displaying the latest posts of any post type including custom types. Control the widget title formatting and the number of posts to display. Plus the widget is completely localized for other languages.
 * Version: 1.1
 * Author: Jared Williams
 * Author URI: http://new2wp.com
 * Tags: custom post types, post types, latest posts, sidebar widget, plugin
 * License: GPL
 */
function n2wp_latest_cpt_init() {
	if ( !function_exists( 'register_sidebar_widget' ))
		return;

	function n2wp_latest_cpt($args) {
		global $post;
		extract($args);

		// These are our own options
		$options = get_option( 'n2wp_latest_cpt' );
		$title 	 = $options['title']; // Widget title
		$phead 	 = $options['phead']; // Heading format 		
		$ptype 	 = $options['ptype']; // Post type 		
		$pshow 	 = $options['pshow']; // Number of Tweets

		$beforetitle = '<'.$phead.'>';
		$aftertitle = '</'.$phead.'>';
		
        // Output
		echo $before_widget;
		
			if ($title) echo $beforetitle . $title . $aftertitle; 
			
			$pq = new WP_Query(array( 'post_type' => $ptype, 'showposts' => $pshow ));
			if( $pq->have_posts() ) : 
			?>
			<ul id="latest_cpt_list">
				<?php while($pq->have_posts()) : $pq->the_post(); ?>
					<li><a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a></li>
				<?php wp_reset_query(); 
				endwhile; ?>
			</ul>
			
			<?php endif; ?>
			
			<!-- NEEDS FIX: to display link to full list of posts page 
			<?php $obj = get_post_type_object($ptype); ?>
			<div class="latest_cpt_icon"><a href="<?php site_url('/'.$obj->query_var); ?>" rel="bookmark"><?php _e( 'View all ' . $obj->labels->name . ' posts' ); ?>&rarr;</a></div>
			//-->
			
		<?php
		// echo widget closing tag
		echo $after_widget;
	}

	/**
	 * Widget settings form function
	 */
	function n2wp_latest_cpt_control() {

		// Get options
		$options = get_option( 'n2wp_latest_cpt' );
		// options exist? if not set defaults
		if ( !is_array( $options ))
			$options = array( 
				'title' => 'Latest Posts',
				'phead' => 'h2',
				'ptype' => 'post',
				'pshow' => '5' 
			);
			// form posted?
			if ( $_POST['latest-cpt-submit'] ) {
				$options['title'] = strip_tags( $_POST['latest-cpt-title'] );
				$options['phead'] = $_POST['latest-cpt-phead'];
				$options['ptype'] = $_POST['latest-cpt-ptype'];
				$options['pshow'] = $_POST['latest-cpt-pshow'];
				update_option( 'n2wp_latest_cpt', $options );
			}	
			// Get options for form fields to show
			$title = $options['title'];
			$phead = $options['phead'];
			$ptype = $options['ptype'];
			$pshow = $options['pshow'];
	
			// The widget form fields
			?>
			<p>
			<label for="latest-cpt-title"><?php echo __( 'Widget Title' ); ?><br />
				<input id="latest-cpt-title" name="latest-cpt-title" type="text" value="<?php echo $title; ?>" size="30" />
			</label>
			</p>
			<p>
			<label for="latest-cpt-phead"><?php echo __( 'Widget Heading Format' ); ?><br />
			<select name="latest-cpt-phead">
				<option value="h2" <?php if ($phead == 'h2') { echo 'selected="selected"'; } ?>>H2 - &lt;h2&gt;&lt;/h2&gt;</option>
				<option value="h3" <?php if ($phead == 'h3') { echo 'selected="selected"'; } ?>>H3 - &lt;h3&gt;&lt;/h3&gt;</option>
				<option value="h4" <?php if ($phead == 'h4') { echo 'selected="selected"'; } ?>>H4 - &lt;h4&gt;&lt;/h4&gt;</option>
				<option value="strong" <?php if ($phead == 'strong') { echo 'selected="selected"'; } ?>>Bold - &lt;strong&gt;&lt;/strong&gt;</option>
			</select>
			</label>
			</p>
			<p>
			<label for="latest-cpt-ptype">
			<select name="latest-cpt-ptype">
				<option value=""> - <?php echo __( 'Select Post Type' ); ?> - </option>
				<?php $args = array( 'public' => true );
				$post_types = get_post_types( $args, 'names' );
				foreach ($post_types as $post_type ) { ?>
					<option value="<?php echo $post_type; ?>" <?php if( $options['ptype'] == $post_type) { echo 'selected="selected"'; } ?>><?php echo $post_type;?></option>
				<?php }	?>
			</select>
			</label>
			</p>
			<p>
			<label for="latest-cpt-pshow"><?php echo __( 'Number of posts to show' ); ?>
				<input id="latest-cpt-pshow" name="latest-cpt-pshow" type="text" value="<?php echo $pshow; ?>" size="2" />
			</label>
			</p>
			<input type="hidden" id="latest-cpt-submit" name="latest-cpt-submit" value="1" />
	<?php 
	}
	
	wp_register_sidebar_widget( 'widget_latest_cpt', __('Latest Custom Posts'), 'n2wp_latest_cpt' );
	wp_register_widget_control( 'widget_latest_cpt', __('Latest Custom Posts'), 'n2wp_latest_cpt_control', 300, 200 );
	
}
add_action( 'widgets_init', 'n2wp_latest_cpt_init' );

?>