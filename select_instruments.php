<?php

$no_header = 1;
require_once( './header.inc' );

$returnMe = "    <option value=\"0\"></option>\n";

$instruments_query = 'select id, instrument as name from instruments '
	. 'order by name';
$instruments_result = $db->query( $instruments_query );

while( $instrument = $instruments_result->fetch_object() ) {
    $returnMe .= "    <option class=\"person\" value=\"$instrument->id\">$instrument->name</option>\n";
}
$instruments_result->close();

print $returnMe;

?>
