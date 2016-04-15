<?php

$no_header = 1;
require_once( '../header.inc' );

if( isset( $user ) ) {
	$band = $db->real_escape_string( $_REQUEST[ 'band' ] );

	$db->query( 'lock tables album_artists write' );
	$insert_query = "insert into album_artists "
		. "( id, name, sort, added ) "
		. "values( null, \"$band\", \"$band\", "
		. '"' . date( 'Y-m-d H:i:s' ) . '" )';
	$insert_result = $db->query( $insert_query );
	$id = $db->insert_id;
	print $id;
	$db->query( 'unlock tables' );

	$add_query = 'insert into add_band '
		. '( id, user, record, state, datetime ) '
		. "values( null, \"$user\", \"$id\", \"$band\", "
		. '"' . date( 'Y-m-d H:i:s' ) . '" )';
	$add_result = $db->query( $add_query );

}
?>