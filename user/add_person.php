<?php

$no_header = 1;
require_once( '../header.inc' );

if( isset( $user ) ) {
	$first      = $db->real_escape_string( $_REQUEST[ 'first' ] );
	$last       = $db->real_escape_string( $_REQUEST[ 'last' ] );
	$birthdate  = $db->real_escape_string( $_REQUEST[ 'birthdate' ] );
	$birthplace = $db->real_escape_string( $_REQUEST[ 'birthplace' ] );
	$deathdate  = $db->real_escape_string( $_REQUEST[ 'deathdate' ] );
	$deathplace = $db->real_escape_string( $_REQUEST[ 'deathplace' ] );

	if( $birthdate == '' )
		$birthdate = '0000-00-00';
	if( $birthplace == '' )
		$birthplace = 'Unknown';
	if( $deathdate == '' )
		$deathdate = 'null';
	if( $deathplace == '' )
		$deathplace = 'null';
	
	$db->query( 'lock tables people write' );
	$insert_query = "insert into people "
		. "( id, first_name, last_name, birthdate, birthplace, "
		. "deathdate, deathplace, added ) "
		. "values( null, \"$first\", \"$last\", \"$birthdate\", \"$birthplace\", "
		. "\"$deathdate\", \"$deathplace\", "
        . '"' . date( 'Y-m-d H:i:s' ) . '" )';
	$insert_result = $db->query( $insert_query );
	$id = $db->insert_id;
	print $id;
	$db->query( 'unlock tables' );

	$add_query = 'insert into add_person '
		. '( id, user, record, state, datetime ) '
		. "values( null, \"$user\", \"$id\", \"$first $last\", "
		. '"' . date( 'Y-m-d H:i:s' ) . '" )';
	$add_result = $db->query( $add_query );

}
?>