<?php

$no_header = 1;
require_once( '../header.inc' );

if( isset( $user ) ) {
	$album = $db->real_escape_string( $_REQUEST[ 'album' ] );
	$song = $db->real_escape_string( $_REQUEST[ 'song' ] );
	
	$album_name_query = 'select name from albums '
		. "where id = \"$album\"";
	$album_name_result = $db->query( $album_name_query );
	$album_name_row = $album_name_result->fetch_object();
	$album_name_result->close();

	$sequence_query = 'select sequence from songs '
		. "where album = \"$album\" "
		. 'order by sequence desc limit 1';
	$sequence_result = $db->query( $sequence_query );
	$sequence_row = $sequence_result->fetch_object();
	$sequence_result->close();
	$sequence = $sequence_row->sequence + 1;
	
	$db->query( 'lock tables songs write' );
	$insert_query = "insert into songs "
		. '( id, title, album, sequence ) '
		. "values( null, \"" . htmlentities( $song ) . "\", \"$album\", \"$sequence\" )";
	$insert_result = $db->query( $insert_query );
	$id = $db->insert_id;
	print $id;
	$db->query( 'unlock tables' );

	$add_query = 'insert into add_song '
		. '( id, user, record, state, datetime ) '
		. "values( null, \"$user\", \"$id\", \"$song ($album_name_row->name)\", "
		. '"' . date( 'Y-m-d H:i:s' ) . '" )';
	$add_result = $db->query( $add_query );

}
?>