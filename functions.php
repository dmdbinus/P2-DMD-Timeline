<?php

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