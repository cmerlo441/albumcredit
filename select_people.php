<?php

$no_header = 1;
require_once( './header.inc' );

$returnMe = "    <option value=\"0\"></option>\n";

$people_query = 'select id, first_name, last_name from people '
	. 'order by last_name, first_name';
$people_result = $db->query( $people_query );

while( $person = $people_result->fetch_object() ) {
    $returnMe .= "    <option class=\"person\" value=\"$person->id\">$person->first_name $person->last_name</option>\n";
}
$people_result->close();

print $returnMe;

?>
