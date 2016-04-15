<?php

$no_header = 1;
require_once( '../header.inc' );

$returnMe = "<option value=\"0\"></option>\n";

$bands_query = 'select id, name from album_artists '
	. "order by name ";
$bands_result = $db->query( $bands_query );

while( $band = $bands_result->fetch_object() ) {
    $returnMe .= "    <option class=\"band\" value=\"$band->id\">$band->name</option>\n";
}
$bands_result->close();

print $returnMe;

?>