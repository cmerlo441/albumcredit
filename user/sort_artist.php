<?php

$no_header = 1;
require_once( '../header.inc' );

$artist = $db->real_escape_string( $_REQUEST[ 'artist' ] );
$sort = $db->real_escape_string( $_REQUEST[ 'sort' ] );

$update_query = 'update album_artists '
    . "set sort = \"$sort\" "
    . "where id = \"$artist\"";
$update_result = $db->query( $update_query );
print $db->affected_rows;

?>