<?php

//note: QA stores up votes as post meta as _up_vote (key), userID (value)
//same with down votes, but _down_vote.
//I'm not checking caps here, assuming they've already been checked
//also, up vote = 2, down vote = 3, ignoring the rest

require_once( '../../../wp-load.php' );
require_once( 'functions.php' );

$xml = simplexml_load_file( 'Posts2Votes.xml' ); 

foreach ( $xml->row as $vote ) {
	
	$type = (int) $vote->VoteTypeId;
	
	if ( $type != 2 && $type != 3 )
		continue;
		
	$key = ( $type == 2 ) ? '_up_vote' : '_down_vote';

	$user = qa_get_se_author( (int) $vote->UserId );
	$post = qa_get_se_post( (int) $vote->PostId );

	if ( !$post || !$user )
		continue;
	
	add_post_meta( $post, $key, $user );
	
}