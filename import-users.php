<?php

require_once( '../../../wp-load.php' );
require_once( 'functions.php' );

//SE field => WP Field
$mapping = array( 
	'WebsiteUrl' => 'user_url',
	'RealName' => 'display_name',
	'Email' => 'user_email',
	'AboutMe' => 'description',
);

//bio goes to meta, user_login and nicename is cleaned display name
//pass needs to be random

$xml = simplexml_load_file( 'Users.xml' );

foreach ( $xml->row as $user ) {

	//skip SE's interal users w/ ID of -1
	if ( $user->Id < 0 )
 		continue;
 	
 	echo "\n\nStarting New User\n";
 	
	echo "E-mail: " . $user->Email . "\n";

 	//if user is already registered, just assoc. SE id with user and move on
 	if ( $userID = get_user_by_email( $user->Email ) ) {
 	
 		echo "ID: "  . $userID->ID . " \n";
 		
 		//user already imported, go no further
 		if ( get_user_meta( $userID->ID, '_stack_exchange_id', true ) )
 			continue;
 			
 		echo "Adding Meta\n";
 		
 		//simply associate existing user with SE ID	
		add_user_meta( $userID->ID, '_stack_exchange_id', (int) $user->Id, true );
		continue;
		
 	}
 	
 	echo "Adding User\n";
 		
	$wpUser = array();
	 	
	foreach ( $mapping as $from => $to ) {
		if ( isset( $user->$from ) )
			$wpUser[$to] = trim( $user->$from );
	}	
	
	//format name and nicename
	$wpUser['user_login'] = str_replace( ' ', '', strtolower( trim( $user->DisplayNameCleaned ) ) );
	$wpUser['user_nicename'] = str_replace( ' ', '-', strtolower( trim( $user->DisplayNameCleaned ) ) );
	
	//random password, require reset
	$wpUser['user_pass'] = md5( time() );
	
	$userID = wp_insert_user( $wpUser );
	
	add_user_meta( $userID, '_stack_exchange_id', (int) $user->Id, true );
 	
}
