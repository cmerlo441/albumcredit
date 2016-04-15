<?php

$no_header = 1;
require_once( './header.inc' );

$person_id = $db->real_escape_string( $_REQUEST[ 'person' ] );
$bd = $db->real_escape_string( $_REQUEST[ 'bd' ] );
$bp = $db->real_escape_string( $_REQUEST[ 'bp' ] );
$dd = $db->real_escape_string( $_REQUEST[ 'dd' ] );
$dp = $db->real_escape_string( $_REQUEST[ 'dp' ] );

if( isset( $user ) ) {
    if( $bd != '' ) {
        $q = 'update people '
            . "set birthdate = "
	    . '"' . date( 'Y-m-d', strtotime( $bd ) ) . '" '
            . "where id = $person_id";
        $r = $db->query( $q );
    }
    if( $bp != '' ) {
        $q = 'update people '
            . "set birthplace = \"$bp\" "
            . "where id = $person_id";
        $r = $db->query( $q );
    }
    if( $dd != '' ) {
        $q = 'update people '
            . "set deathdate = "
	    . '"' . date( 'Y-m-d', strtotime( $dd ) ) . '" '
            . "where id = $person_id";
        $r = $db->query( $q );
    }
    if( $dp != '' ) {
        $q = 'update people '
            . "set deathplace = \"$dp\" "
            . "where id = $person_id";
        $r = $db->query( $q );
    }
}

$person_query = 'select first_name, last_name, birthdate, birthplace, '
    . 'deathdate, deathplace '
    . 'from people '
    . "where id = \"$person_id\"";
$person_result = $db->query( $person_query );
if( $person_result->num_rows == 1 ) {

    $person = $person_result->fetch_object();
        
    // Do we know the person's birthday?    
    if( $person->birthdate != '0000-00-00' ) {
        print "<p>$person->first_name was born "
            . date( 'F jS, Y', strtotime( $person->birthdate ) )
            . " in $person->birthplace.<br />\n";
              
        $birth = new DateTime( $person->birthdate );
        $today = new DateTime();
        $interval = $today->diff( $birth );
        $age = $interval->format( '%y' );

        // Is the person still alive?
        if( $person->deathdate != '0000-00-00' and $person->deathdate != NULL ) {
            $death = new DateTime( $person->deathdate );
            $interval = $death->diff( $birth );
            $died_at = $interval->format( '%y' );
            print "$person->first_name died on "
                . date( 'F jS, Y', strtotime( $person->deathdate ) )
                . " in $person->deathplace at age $died_at.<br />\n";
        }
        print "$person->first_name " . ( isset( $death ) ? "would be" : "is" )
            . " $age years old.";
    } else {
        print "<p>We don't know $person->first_name's birthdate.\n";
    }
}
?>