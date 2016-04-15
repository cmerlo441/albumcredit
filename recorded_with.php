<?php

$no_header = 1;

require_once( './header.inc' );

$person = $db->real_escape_string( $_REQUEST[ 'person' ] );

$people = array();

$people_query = 'select p.id, p.first_name as fn, p.last_name as ln '
    . 'from people as p, musician_album_credits as mac1, '
    . 'musician_album_credits as mac2 '
    . 'where mac1.album = mac2.album '
    . "and mac1.musician = $person "
    . 'and mac2.musician = p.id '
    . "and p.id != $person "
    . 'group by p.id '
    . 'order by p.last_name, p.first_name ';
$people_result = $db->query( $people_query );
print "<ul>\n";
while( $row = $people_result->fetch_object() ) {
    print "<li><a href=\"person.php?person=$row->id\">$row->fn $row->ln</a></li>\n";
}
print "</ul>\n";

?>