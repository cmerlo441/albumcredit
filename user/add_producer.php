<?php

$no_header = 1;
require_once( '../header.inc' );

if( isset( $user ) ) {
	$album = $db->real_escape_string( $_REQUEST[ 'album' ] );
	$producer = $db->real_escape_string( $_REQUEST[ 'producer' ] );
	
	$album_name_query = 'select name from albums '
		. "where id = \"$album\"";
	$album_name_result = $db->query( $album_name_query );
	$album_name_row = $album_name_result->fetch_object();
	$album_name_result->close();
	$album_name = $album_name_row->name;
	
	$producer_name_query = 'select first_name, last_name '
        . 'from people '
        . "where id = $producer";
	$producer_name_result = $db->query( $producer_name_query );
	$producer_name_row = $producer_name_result->fetch_object();
	$producer_name_result->close();
	$producer_name = "$producer_name_row->first_name $producer_name_row->last_name";

	$sequence_query = 'select sequence from producers '
		. "where album = \"$album\" "
		. 'order by sequence desc limit 1';
	$sequence_result = $db->query( $sequence_query );
	$sequence_row = $sequence_result->fetch_object();
	$sequence_result->close();
	$sequence = $sequence_row->sequence + 1;
	
	$db->query( 'lock tables producers write' );
	$insert_query = "insert into producers "
		. '( id, album, producer, sequence ) '
		. "values( null, \"$album\", \"$producer\", \"$sequence\" )";
	$insert_result = $db->query( $insert_query );
	$id = $db->insert_id;
	print $id;
	$db->query( 'unlock tables' );

	$add_query = 'insert into add_producer '
		. '( id, user, album, state, datetime ) '
		. "values( null, \"$user\", \"$album\", \"$producer_name produced $album_name\", "
		. '"' . date( 'Y-m-d H:i:s' ) . '" )';
	$add_result = $db->query( $add_query );

}
?>