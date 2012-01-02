<?php

require_once( '../../../wp-load.php' );
require_once( 'functions.php' );

$xml = simplexml_load_file( 'PostComments.xml' );

foreach ( $xml->row as $comment ) {

	//comment already imported, skip
	if ( qa_get_se_comment( $comment->Id ) )
		continue;

	$wpComment = array();
	$postID = qa_get_se_post( (int) $comment->PostId );
	$author = qa_get_se_author( (int) $comment->UserId );
	
	//spam?
	if ( !$author )
		continue;
	
	if ( !$postID )
		continue;
		
	$wpComment['comment_post_ID'] = $postID;
	$wpComment['user_id'] = $author;
	$wpComment['comment_content'] = (string) $comment->Text;
	$wpComment['comment_date'] = date( 'Y-m-d H:i:s', strtotime( $comment->CreationDate ) );
	$wpComment['comment_approved'] = true;
	
	$commentID = wp_insert_comment( $wpComment );
	
	add_comment_meta( $commentID, '_stack_exchange_id', (int) $comment->Id, true );
	
}
die('done');