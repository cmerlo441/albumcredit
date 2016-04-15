<?php

$no_header = 1;
require_once( '../header.inc' );

if( isset( $user ) ) {
	$song = $db->real_escape_string( $_REQUEST[ 'song' ] );
	$musician = $db->real_escape_string( $_REQUEST[ 'musician' ] );
	$instrument = $db->real_escape_string( $_REQUEST[ 'instrument' ] );
	
	$album_name_query = 'select s.sequence, s.title, a.name from albums as a, songs as s '
		. "where s.id = \"$song\" "
        . 'and s.album = a.id';
	$album_name_result = $db->query( $album_name_query );
	$album_name_row = $album_name_result->fetch_object();
	$album_name_result->close();
	$album_name = $album_name_row->name;
	$song_name = $album_name_row->title;
	$song_sequence = $album_name_row->sequence;
	
	$musician_name_query = 'select first_name, last_name from people '
        . "where id = \"$musician\"";
	$musician_name_result = $db->query( $musician_name_query );
	$musician_name_row = $musician_name_result->fetch_object();
	$musician_name_result->close();
	$musician_name = "$musician_name_row->first_name $musician_name_row->last_name";
	
	$instrument_name_query = 'select instrument from instruments '
		. "where id = \"$instrument\"";
	$instrument_name_result = $db->query( $instrument_name_query );
	$instrument_name_row = $instrument_name_result->fetch_object();
	$instrument_name_result->close();
	$instrument_name = $instrument_name_row->instrument;
	
	$db->query( 'lock tables musician_song_credits write' );
	$insert_query = "insert into musician_song_credits "
		. '( id, musician, song, instrument ) '
		. "values( null, \"$musician\", \"$song\", \"$instrument\" )";
	$insert_result = $db->query( $insert_query );
	$id = $db->insert_id;
	print $id;
	$db->query( 'unlock tables' );

	$add_query = 'insert into add_song_musician '
		. '( id, user, record, state, datetime ) '
		. "values( null, \"$user\", \"$id\", \"$musician_name performed $instrument_name on $album_name track $song_sequence\", "
		. '"' . date( 'Y-m-d H:i:s' ) . '" )';
	$add_result = $db->query( $add_query );

}
?>