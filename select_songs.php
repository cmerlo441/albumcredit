<?php

$no_header = 1;
require_once( './header.inc' );

$id = $db->real_escape_string( $_REQUEST[ 'album' ] );

$returnMe = "    <option value=\"0\"></option>\n";

$songs_query = 'select id, title, sequence '
	. 'from songs '
	. "where album = $id "
	. 'order by sequence';
$songs_result = $db->query( $songs_query );
while( $song = $songs_result->fetch_object() ) {
    $returnMe .= "    <option class=\"song\" value=\"$song->id\">$song->sequence: $song->title</option>\n";
}

print $returnMe;
?>