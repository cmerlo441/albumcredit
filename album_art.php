<?php 

$no_header = 1;
require_once( './header.inc' );

$album = $db->real_escape_string( $_REQUEST[ 'album' ] );

$art_query = 'select url from album_art '
    . "where album=$album";
$art_result = $db->query( $art_query );
if( $art_result->num_rows ==1 ) {
    $art = $art_result->fetch_object();
    print "<img class=\"img-responsive img-rounded\" src=\"$art->url\" />\n";
}
$art_result->close();
?>