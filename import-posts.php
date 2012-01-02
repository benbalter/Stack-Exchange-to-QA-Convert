<?php

require_once( '../../../wp-load.php' );
require_once( 'functions.php' );

$xml = simplexml_load_file( 'Posts.xml' );

//note: we're not using post here, because we're in global scope.
foreach ( $xml->row as $p ) {

	//post already imported, go no further
	if ( qa_get_se_post( $p->Id ) )
		continue;
		
	echo "Importing post: " . $p->Id . " <br />";

	$wpPost = array();	
	$author = qa_get_se_author( $p->OwnerUserId );
	
	//author unknown, most likely spam
	if ( !$author )
		continue;
	
	//won't let us cast in an if apparently...
	$title = (string) $p->Title;
	
	//if post has a parent, it's really a reply, hold off until we process comments
	//if ( isset( $p->ParentId ) || empty( $title ) )
	//	continue;
		
	$wpPost['post_author'] = $author;
	$wpPost['post_content'] = (string) $p->Body;
	$wpPost['post_date'] = date( 'Y-m-d H:i:s', strtotime( $p->CreationDate ) );
	$wpPost['post_status'] = 'publish';
	$wpPost['post_title'] = (string) $p->Title;
	$wpPost['post_type'] = ( isset( $p->ParentId ) ) ? 'answer' : 'question';
	
	if ( isset( $p->ParentId ) )
		$wpPost['post_parent'] = qa_get_se_post( (int) $p->ParentId );

	$postID = wp_insert_post( $wpPost );
	
	//convert tags, if we've got 'em
	if ( isset( $p->Tags ) ) {
		$tags = str_replace( '"  "', ',', $p->Tags );
		$tags = str_replace( '"', '', $tags );
		$tags = str_replace( 'ö', ' ', $tags );
		$tags = explode( ',', $tags );
		foreach ( $tags as &$tag )
			$tag = trim ( $tag );
		
		wp_set_post_terms( $postID, $tags, 'question_tag' );
	}
	
	update_post_meta( $postID, '_stack_exchange_id', (int) $p->Id );

}