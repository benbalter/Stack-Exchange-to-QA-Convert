<?php

function qa_get_se_author( $id ) {
	global $wpdb; 
	$sql = "SELECT user_id FROM $wpdb->usermeta WHERE meta_key = '_stack_exchange_id' AND meta_value = '%d'";
	return $wpdb->get_var( $wpdb->prepare( $sql, $id ) );
}

function qa_get_se_post( $id ) {
	$q = new WP_Query( array( 'meta_value' => (int) $id, 'meta_key' => '_stack_exchange_id', 'post_type' => array( 'question', 'answer' ) ) );

	while( $q->have_posts() ) : $q->the_post();
		return get_the_ID();
	endwhile;
	
	return false;
}

function qa_get_se_comment( $id ) {
	global $wpdb; 
	$sql = "SELECT comment_id FROM $wpdb->commentmeta WHERE meta_key = '_stack_exchange_id' AND meta_value = '%d'";
	return $wpdb->get_var( $wpdb->prepare( $sql, $id ) );
}

//DDoS prevention
if ( !current_user_can( 'manage_options' ) )
	wp_die( 'not authorized' );
	