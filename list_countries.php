<?php

$no_header = 1;
require_once( './header.inc' );

$countries = array();
$country_query = 'select birthplace from people';
$country_result = $db->query( $country_query );
while( $birthplace = $country_result->fetch_object() ) {
    $array = explode( ',', $birthplace->birthplace );
    $country = trim( array_pop( $array ) );
    $countries[ $country ]++;
}
ksort( $countries );
print "            <option value=\"0\">Filter by country</option>\n";
foreach( $countries as $country=>$count ) {
    print "            <option value=\"$country\">$country</option>\n";
}

?>
