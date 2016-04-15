<?php

$no_header = 1;
require_once( './header.inc' );

$returnMe = "<option value=\"0\"></option>\n";

$bands_query = 'select id, name from album_artists '
	. "order by name ";
$bands_result = $db->query( $bands_query );

$returnMe .= "  <optgroup label=\"Bands\">\n";
while( $band = $bands_result->fetch_object() ) {
    $returnMe .= "    <option class=\"band\" value=\"b$band->id\">$band->name (artist)</option>\n";
}
$bands_result->close();

$returnMe .= "  <optgroup label=\"Albums\">\n";
$albums_query = 'select a.id, a.name as title, aa.name as artist, a.release_date '
	. 'from albums as a, album_artists as aa '
	. 'where a.album_artist = aa.id '
	. 'order by title, artist, release_date';
$albums_result = $db->query( $albums_query );

while( $album = $albums_result->fetch_object() ) {
	$returnMe .= "    <option class=\"album\" value=\"a$album->id\">$album->title ($album->artist album, "
		. date( 'Y', strtotime( $album->release_date ) )
		. ")</option>\n";
}
$albums_result->close();

$returnMe .= "  <optgroup label=\"People\">\n";
$people_query = 'select id, first_name, last_name from people '
	. 'order by last_name, first_name';
$people_result = $db->query( $people_query );

while( $person = $people_result->fetch_object() ) {
    $returnMe .= "    <option class=\"person\" value=\"p$person->id\">$person->first_name $person->last_name (person)</option>\n";
}
$people_result->close();

$returnMe .= "  </optgroup>\n";

print $returnMe;

?>
