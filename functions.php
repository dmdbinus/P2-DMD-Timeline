<?php

// Add Template Stylesheet
function p2_dmd_timeline_stylesheet(){
    wp_register_style('p2-style', get_bloginfo('template_url') . '/style.css', array(), false, 'screen');
    wp_enqueue_style('p2-style');    

    wp_register_style('p2-dmd-timeline-style', get_bloginfo('stylesheet_url'), array(), false, 'screen');
    wp_enqueue_style('p2-dmd-timeline-style');    

}
add_action('wp_head', 'p2_dmd_timeline_stylesheet', 1);

// DMD Title
function dmd_p2_title( $before = '<h2 class="title">', $after = '</h2>', $echo = true ) {
	if ( is_page() )
		return;

	if ( is_single() && false === p2_the_title( '', '', false ) ) { ?>
		<h2 class="transparent-title"><?php echo the_title(); ?></h2><?php
		return true;
	} else {
		p2_the_title( $before, $after, $echo );
	}
}

// DMD meta
function dmd_p2_meta(){
	?>
	<h4 class="dmd-p2-meta">
		<?php if ( ! is_page() ): ?>
			<a href="<?php echo esc_attr( $author_posts_url ); ?>" title="<?php echo esc_attr( $posts_by_title ); ?>"><?php the_author(); ?></a>
		<?php endif; ?>
		<span class="meta">
			<?php
			if ( ! is_page() ) {
				echo p2_date_time_with_microformat();
			} ?>

			<?php if ( ! is_page() ) : ?>
				<span class="tags">
					<?php tags_with_count( '', __( '<br />Tags:' , 'p2' ) .' ', ', ', ' &nbsp;' ); ?>&nbsp;
				</span>
			<?php endif; ?>
		</span>
	</h4>	
	<?php
}

// DMD Task Status
function dmd_task_status(){
	$statuses = get_the_terms($post->ID, 'status');
	foreach ($statuses as $status){
		$class = str_replace(' ','-',strtolower($status->name));
		$url = get_bloginfo('url') . '/status/' . $status->slug . '/';
		echo '<a href="'. $url .'" class="status '. $class .'"><span>'. $status->name .'</span></a>';
		echo '<span class="status-tail"></span>';
	}
}

// DMD Person In Charge
function dmd_pic(){
	$pics = get_the_terms($post->ID, 'person-in-charge');
	foreach ($pics as $pic){
		$url = get_bloginfo('url') . '/person-in-charge/' . $pic->slug . '/';		
		echo '<a href="'. $url .'" class="person-in-charge" title="'. $pic->name .'">'. get_avatar($pic->description, 30, "Mystery Man", $pic->name) .'</a>';
	}
}

// Add Custom Taxonomies for P2
add_action( 'init', 'dmd_custom_taxonomies', 0 );
function dmd_custom_taxonomies() {
  // Register 'Status' for 'Blog'
  $status = array(
    'name' => _x( 'Status', 'taxonomy general name' ),
    'singular_name' => _x( 'Status', 'taxonomy singular name' ),
    'search_items' =>  __( 'Search Status' ),
    'popular_items' => __( 'Popular Status' ),
    'all_items' => __( 'All Status' ),
    'parent_item' => null,
    'parent_item_colon' => null,
    'edit_item' => __( 'Edit Status' ), 
    'update_item' => __( 'Update Status' ),
    'add_new_item' => __( 'Add New Status' ),
    'new_item_name' => __( 'New Status' ),
    'separate_items_with_commas' => __( 'Separate statuses with commas' ),
    'add_or_remove_items' => __( 'Add or remove statuses' ),
    'choose_from_most_used' => __( 'Choose from the most mentioned statuses' )
  ); 

  register_taxonomy('status', array('post'),array(
    'hierarchical' => true,
    'labels' => $status,
    'show_ui' => true,
    'query_var' => true,
    'rewrite' => array( 'slug' => 'status' ),
  ));
  
  $pic = array(
    'name' => _x( 'Person in Charge', 'taxonomy general name' ),
    'singular_name' => _x( 'Person in Charge', 'taxonomy singular name' ),
    'search_items' =>  __( 'Search Person in Charge' ),
    'popular_items' => __( 'Popular Person in Charge' ),
    'all_items' => __( 'All Person in Charge' ),
    'parent_item' => null,
    'parent_item_colon' => null,
    'edit_item' => __( 'Edit Person in Charge' ), 
    'update_item' => __( 'Update Person in Charge' ),
    'add_new_item' => __( 'Add New Person in Charge' ),
    'new_item_name' => __( 'New Person in Charge' ),
    'separate_items_with_commas' => __( 'Separate persons in charge with commas' ),
    'add_or_remove_items' => __( 'Add or remove persons in charge' ),
    'choose_from_most_used' => __( 'Choose from the most mentioned persons in charge' )
  ); 

  register_taxonomy('person-in-charge', array('post'),array(
    'hierarchical' => true,
    'labels' => $pic,
    'show_ui' => true,
    'query_var' => true,
    'rewrite' => array( 'slug' => 'person-in-charge' ),
  ));    
}

// Add Person in Charge Widget
add_action( 'widgets_init', 'dmd_person_in_charge_widget_init' );
function dmd_person_in_charge_widget_init(){
	register_widget('dmd_person_in_charge_widget');
}

class dmd_person_in_charge_widget extends WP_Widget{
	
	// Widget Setup
	function dmd_person_in_charge_widget(){
		// Widget Settings
		$widget_ops = array('classname' => 'person-in-charge-widget', 'description' => __('Person in Charge', 'p2'));
		
		// Widget Control Settings
		$control_ops = array('width' => 300, 'height' => 350, 'id_base' => 'person-in-charge-widget');
		
		//Create The Widget
		$this->WP_Widget('person-in-charge-widget', __('DMD - Person in Charge', 'p2'), $widget_ops, $control_ops);
	}
	
	
	// Front End Widget Interface
	function widget( $args, $instance ) {
		extract( $args );

		// Our variables from the widget settings.
		$person_in_charge_widgettitle = $instance['dmd_person_in_charge_widgettitle'];		

		
		/* Before widget (defined by themes). */
		echo $before_widget;
		
		echo '<h2 class="widgettitle">'.$person_in_charge_widgettitle .'</h2>';
		
		echo '<table class="p2-person-in-charge" cellspacing="0" cellpadding="0" border="0">';
			$pics = get_categories('taxonomy=person-in-charge');
			foreach($pics as $pic){
				echo '<tr>';
				echo '<td class="avatar" style="height: 32px; width: 32px" title="'. $pic->cat_name .'">' . get_avatar($pic->description, 32) . '</td>';
				echo '<td class="text"><a href="' . get_bloginfo('url') . '/person-in-charge/' . $pic->slug . '">' . $pic->cat_name . ' (' . dmd_get_numbers_of_task_left_based_on_pic($pic->slug) . ')</a></td>';
				echo '</tr>';
			}		
		echo '</table>';
		
		/* After widget (defined by themes). */
		echo $after_widget;
	}

	// Update the widget settings.
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		/* Strip tags for title and name to remove HTML (important for text inputs). */
		$instance['dmd_person_in_charge_widgettitle'] = strip_tags( $new_instance['dmd_person_in_charge_widgettitle'] );

		return $instance;
	}

	/**
	 * Displays the widget settings controls on the widget panel.
	 * Make use of the get_field_id() and get_field_name() function
	 * when creating your form elements. This handles the confusing stuff.
	 */
	function form( $instance ) {

		/* Set up some default widget settings. */
		$defaults = array(
				'dmd_person_in_charge_widgettitle' => 'Person in Charge'
				);
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>

		<?php // Widget Title ?>
		<p>
			<label for="<?php echo $this->get_field_id( 'dmd_person_in_charge_widgettitle' ); ?>"><?php _e('Title:', 'p2'); ?></label>
			<input id="<?php echo $this->get_field_id( 'dmd_person_in_charge_widgettitle' ); ?>" name="<?php echo $this->get_field_name( 'dmd_person_in_charge_widgettitle' ); ?>" value="<?php echo $instance['dmd_person_in_charge_widgettitle']; ?>" class="widefat" />
		</p>
		
	<?php
	}
}


// Add Status Widget
add_action( 'widgets_init', 'dmd_task_status_widget_init' );
function dmd_task_status_widget_init(){
	register_widget('dmd_task_status_widget');
}

class dmd_task_status_widget extends WP_Widget{
	
	// Widget Setup
	function dmd_task_status_widget(){
		// Widget Settings
		$widget_ops = array('classname' => 'task-status-widget', 'description' => __('Task Status', 'p2'));
		
		// Widget Control Settings
		$control_ops = array('width' => 300, 'height' => 350, 'id_base' => 'task-status-widget');
		
		//Create The Widget
		$this->WP_Widget('task-status-widget', __('DMD - Task Status', 'p2'), $widget_ops, $control_ops);
	}
	
	
	// Front End Widget Interface
	function widget( $args, $instance ) {
		extract( $args );

		// Our variables from the widget settings.
		$task_status_widgettitle = $instance['dmd_task_status_widgettitle'];		

		
		/* Before widget (defined by themes). */
		echo $before_widget;
		
		echo '<h2 class="widgettitle">'.$task_status_widgettitle .'</h2>';
		
		echo '<ul>';
			$statuses = get_categories('taxonomy=status');
			foreach($statuses as $status){
				echo '<li><a href="' . get_bloginfo('url') . '/status/' . $status->slug . '">' . $status->cat_name . ' (' . $status->category_count . ')</a></li>';
			}		
		echo '</ul>';
		
		/* After widget (defined by themes). */
		echo $after_widget;
	}

	// Update the widget settings.
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		/* Strip tags for title and name to remove HTML (important for text inputs). */
		$instance['dmd_task_status_widgettitle'] = strip_tags( $new_instance['dmd_task_status_widgettitle'] );

		return $instance;
	}

	/**
	 * Displays the widget settings controls on the widget panel.
	 * Make use of the get_field_id() and get_field_name() function
	 * when creating your form elements. This handles the confusing stuff.
	 */
	function form( $instance ) {

		/* Set up some default widget settings. */
		$defaults = array(
				'dmd_task_status_widgettitle' => 'Task Status'
				);
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>

		<?php // Widget Title ?>
		<p>
			<label for="<?php echo $this->get_field_id( 'dmd_task_status_widgettitle' ); ?>"><?php _e('Title:', 'p2'); ?></label>
			<input id="<?php echo $this->get_field_id( 'dmd_task_status_widgettitle' ); ?>" name="<?php echo $this->get_field_name( 'dmd_task_status_widgettitle' ); ?>" value="<?php echo $instance['dmd_task_status_widgettitle']; ?>" class="widefat" />
		</p>
		
	<?php
	}
}

// Adding Custom Taxonomy into Dashboard Post Column
add_filter("manage_edit-post_columns", "title_p2dmdtimeline_columns");
add_action("manage_posts_custom_column", "p2dmdtimeline_columns");

function title_p2dmdtimeline_columns($columns){
	$columns = array(
		"cb" => "<input type=\"checkbox\" />",
                "title" => __("Title"),
		"author" => __("Author"),
                "categories" => __("Categories"),
                "tags" => __("Tags"),
		"status" => __("Status"),
		"pic" => __("Person In Charge"),
		"comments" => '<span class="vers"><img alt="' . esc_attr__( 'Comments' ) . '" src="' . esc_url( admin_url( 'images/comment-grey-bubble.png' ) ) . '" /></span>',
		"date" => __('Date')
	);
	return $columns;	
}

function p2dmdtimeline_columns($column){
	global $post;
	switch($column){
		case "status":
			echo get_the_term_list( $post->ID, 'status', '', ', ' );
		break;
	
		case "pic":
			echo get_the_term_list( $post->ID, 'person-in-charge', '', ', ' );
		break;
	}
}


// Get numbers of on-progress tasks based on person in charge
function dmd_get_numbers_of_task_left_based_on_pic($pic_slug){
	global $post;
	$args = array(
		'posts_per_page' => -1,
		'tax_query' => array(
			'relation' => 'AND',
			array(
				'taxonomy' => 'status',
				'field' => 'slug',
				'terms' => get_term_by('name', 'On Progress', 'status')->slug
			),
			array(
				'taxonomy' => 'person-in-charge',
				'field' => 'slug',
				'terms' => $pic_slug
			)
		)
	);
    
	$the_query = new WP_Query( $args );    

	while ( $the_query->have_posts() ) : $the_query->the_post();	
		$ids[] .= get_the_ID();
	endwhile;
	
	wp_reset_postdata();
	
	$amount_of_id = count($ids);
	if ($amount_of_id == 0) {
		$message = __('No task left', 'p2');
		return $message;
	} elseif ($amount_of_id < 2){
		$message = $amount_of_id . __(' task left', 'p2');
		return $message;
	} else{
		$message = $amount_of_id . __(' tasks left', 'p2');
		return $message;		
	}

}