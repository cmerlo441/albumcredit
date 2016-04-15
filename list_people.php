<?php

$no_header = 1;
require_once( './header.inc' );

$sort = $db->real_escape_string( $_REQUEST[ 'sort' ] );
$filter = $db->real_escape_string( $_REQUEST[ 'filter' ] );
$filter_type = $db->real_escape_string( $_REQUEST[ 'filter_type' ] );

$where = '';
$order = '';

$people_query = 'select id, first_name, last_name, birthdate, birthplace '
	. 'from people ';
if( $sort == '' or $sort == 'last' )
	$order = 'order by last_name, first_name, birthdate';
else if( $sort == 'first' )
	$order = ' order by first_name, last_name, birthdate';
else if( $sort == 'bdate' ) {
	$where = 'where birthdate > "0000-00-00"';
	$order = 'order by birthdate, last_name, first_name';
}
else if( $sort == 'bplace' )
	$order = 'order by birthplace, last_name, first_name ';

if( $filter_type != '' and $filter != '' ) {
	$where .= ( $where == '' ? 'where ' : 'and ' );
	if( $filter_type == 'country' ) {
		$where .= "birthplace like \"%$filter\"";
	}
}

if( $where != '' )
	$people_query .= "$where ";
if( $order != '' )
	$people_query .= $order;
$people_result = $db->query( $people_query );
$people = array();
while( $person = $people_result->fetch_object() ) {
	$people[ $person->id ][ 'first' ]       = $person->first_name;
	$people[ $person->id ][ 'last' ]        = $person->last_name;
	$people[ $person->id ][ 'birthdate' ]   = date( 'F j, Y', strtotime( $person->birthdate ) );
	if( $person->birthdate == '0000-00-00' )
	    $people[ $person->id ][ 'birthdate' ] = 'Unknown';
	$people[ $person->id ][ 'birthplace' ]  = $person->birthplace;
}
$people_result->close();

foreach( $people as $id=>$person ) {
	print "	  	 <tr>\n";
	print "        <td><a href=\"person.php?person=$id\">{$person[ 'first' ]}</a></td>\n";
	print "        <td><a href=\"person.php?person=$id\">{$person[ 'last' ]}</a></td>\n";
	print "        <td>{$person[ 'birthdate' ]}</td>\n";
	print "        <td>{$person[ 'birthplace' ]}</td>\n";
	print "      </tr>\n";
}

?>