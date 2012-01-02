<pre><?php

//after import, certain answers have no post_parent which causes problems.
//this script tries to re-unite orphaned answers with their parent questions
//not perfect, but based on GUID

require_once( '../../../wp-load.php' );
require_once( 'functions.php' );

$posts = get_posts( array( 
				'post_type' => 'answer',
				'post_parent' => 0,
				'numberposts' => -1,
				) );
			
foreach ( $posts as $post ) {
	
	echo " {$post->ID} ";
	
	$parts = explode( '#', $post->guid );	
	$guid = $parts[0];
	$parts = explode( '/', $guid );
	$guid = $parts[5];
	$parent = $wpdb->get_var( "SELECT ID from $wpdb->posts WHERE post_type LIKE 'question' AND guid like '%{$guid}%' LIMIT 1" );
	
	if ( !$parent )
		continue;
	
	wp_update_post( array( 
				'ID' => $post->ID,
				'post_parent' => $parent,
	) );
	
}