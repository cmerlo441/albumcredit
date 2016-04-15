<?php

$no_header = 1;
require_once( './header.inc' );

$bands_query = 'select id, name from album_artists '
	. "order by name ";
$bands_result = $db->query( $bands_query );

$data = array();
$count = 0;
while( $band = $bands_result->fetch_object() ) {
	$data[ $count ][ 'type' ] = 'band';
	$data[ $count ][ 'id' ]   = $band->id;
	$data[ $count ][ 'name' ] = $band->name;
	++$count;
}
$bands_result->close();

$people_query = 'select id, first_name, last_name from people '
	. 'order by last_name, first_name';
$people_result = $db->query( $people_query );

while( $person = $people_result->fetch_object() ) {
	$data[ $count ][ 'type' ] = 'person';
	$data[ $count ][ 'id' ]   = $person->id;
	$data[ $count ][ 'name' ] = "$person->first_name $person->last_name";
	++$count;
}
$people_result->close();

print json_encode( $data );

?>
