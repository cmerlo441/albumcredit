<?php

$no_header = 1;
require_once( '../header.inc' );

if( isset( $user ) ) {
	$band = $db->real_escape_string( $_REQUEST[ 'band' ] );
	$album = $db->real_escape_string( $_REQUEST[ 'album' ] );
	$year = $db->real_escape_string( $_REQUEST[ 'year' ] );
	$month = $db->real_escape_string( $_REQUEST[ 'month' ] );
	$date = $db->real_escape_string( $_REQUEST[ 'date' ] );

	if( $month == '' ) $momth = 0;
	if( $date == '' ) $date = 0;

	$db->query( 'lock tables albums write' );
	$insert_query = "insert into albums "
		. "( id, album_artist, name, release_year, release_month, "
		. "release_day, added ) "
		. "values( null, \"$band\", \"$album\", \"$year\", \"$month\", "
		. "\"$date\", "
		. '"' . date( 'Y-m-d H:i:s' ) . '" )';
	$insert_result = $db->query( $insert_query );
	$id = $db->insert_id;
	print $id;
	$db->query( 'unlock tables' );
	
	$album_query = 'select aa.name as band, a.name as title '
		. 'from album_artists as aa, albums as a '
		. "where a.id = \"$id\" and a.album_artist = aa.id";
	$album_result = $db->query( $album_query );
	$album = $album_result->fetch_object();
	$album_result->close();
	
	$add_query = 'insert into add_album '
		. '( id, user, record, state, datetime ) '
		. "values( null, \"$user\", \"$id\", \"$album->band _{$album->title}_\", "
		. '"' . date( 'Y-m-d H:i:s' ) . '" )';
	$add_result = $db->query( $add_query );
	
}
?>