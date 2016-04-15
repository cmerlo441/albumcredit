<?php

$no_header = 1;
require_once( '../header.inc' );

if( isset( $user ) ) {
	$album = $db->real_escape_string( $_REQUEST[ 'album' ] );
	$url = $db->real_escape_string( $_REQUEST[ 'url' ] );

	$db->query( 'lock tables album_art write' );
	$insert_query = "insert into album_art "
		. "( id, album, url, added ) "
		. "values( null, \"$album\", \"$url\", "
		. '"' . date( 'Y-m-d H:i:s' ) . '" )';
	$insert_result = $db->query( $insert_query );
	$id = $db->insert_id;
	print $id;
	$db->query( 'unlock tables' );
	
	$album_query = 'select aa.name as band, a.name as title '
		. 'from album_artists as aa, albums as a '
		. "where a.id = \"$album\" and a.album_artist = aa.id";
	$album_result = $db->query( $album_query );
	$album = $album_result->fetch_object();
	$album_result->close();
	
	$add_query = 'insert into add_album_artwork '
		. '( id, user, record, state, datetime ) '
		. "values( null, \"$user\", \"$id\", \"$album->band _{$album->title}_\", "
		. '"' . date( 'Y-m-d H:i:s' ) . '" )';
	print $add_query;
	$add_result = $db->query( $add_query );
	
}
?>