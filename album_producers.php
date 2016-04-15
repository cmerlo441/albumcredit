<?php 

$no_header = 1;
require_once( './header.inc' );

$album = $db->real_escape_string( $_REQUEST[ 'album' ] );

$query = 'select pro.id, p.id as person_id, p.first_name, p.last_name, pro.sequence '
    . 'from producers as pro, people as p '
    . "where pro.album = $album "
    . 'and pro.producer = p.id '
    . 'order by sequence';
$result = $db->query( $query );
if( $result->num_rows == 0 ) {
    print "The Album Credits Project does not have any information about who produced this album.</p>\n";
} else {
    $count = 0;
    while( $row = $result->fetch_object() ) {
        if( $count != 0 ) print ", ";
        print "<a href=\"person.php?person=$row->person_id\">$row->first_name $row->last_name</a>";
        ++$count;
    }
}

?>