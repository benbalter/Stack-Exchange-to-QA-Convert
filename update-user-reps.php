<?php

/**
 * Update All User's Reputations
 */

require_once( '../../../wp-load.php' );
require_once( 'functions.php' );

$users = get_users( array( 'blog_id' => $GLOBALS['blog_id'], 'fields' => 'ID' ) );
QA_Votes::update_user_rep( $users );