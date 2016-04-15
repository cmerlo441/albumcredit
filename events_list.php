<?php 

require_once( './header.inc' );

$events = array();

$albums_query = 'select aa.id as artist_id, aa.name as artist, '
    . 'a.id as album_id, a.name as title, a.release_date '
    . 'from album_artists as aa, albums as a '
    . 'where a.album_artist = aa.id';
$albums_result = $db->query( $albums_query );
while( $album = $albums_result->fetch_object() ) {
    if( preg_match( "/00$/", $album->release_date ) )
        $date = substr( $album->release_date, 5, 2 ) . "-00";
    else
        $date = date( 'm-d', strtotime( $album->release_date ) );
    $year = date( 'Y', strtotime( $album->release_date ) );
    $count = sizeof( $events[ $date ] );
    $events[ $date ][ $count ] = "$year: "
        . "<a href=\"album_artist.php?artist=$album->artist_id\">$album->artist</a>"
        . " released <a href=\"album.php?album=$album->album_id\">$album->title</a>";
    if( $count > 0 )
        sort( $events[ $date ] );
}

$people_query = 'select id, first_name as f, last_name as l, birthdate, birthplace, '
    . 'deathdate, deathplace from people';
$people_result = $db->query( $people_query );
while( $person = $people_result->fetch_object( ) ) {
    if( $person->birthdate != '0000-00-00' and $person->birthdate != '' ) {
        $date = date( 'm-d', strtotime( $person->birthdate ) );
        $year = date( 'Y', strtotime( $person->birthdate ) );
        $count = sizeof( $events[ $date ] );
        $events[ $date ][ $count++ ] = "$year: "
            . "<a href=\"person.php?person=$person->id\">$person->f $person->l</a>"
            . " was born" . ( $person->birthplace == 'Unknown' ? '' : " in $person->birthplace" );
        if( $count > 0 )
            sort( $events[ $date ] );
    }
    if( $person->deathdate != '0000-00-00' and $person->deathdate != '' ) {
        $date = date( 'm-d', strtotime( $person->deathdate ) );
        $year = date( 'Y', strtotime( $person->deathdate ) );
        $count = sizeof( $events[ $date ] );
        $events[ $date ][ $count++ ] = "$year: "
        . "<a href=\"person.php?person=$person->id\">$person->f $person->l</a>"
        . " died" . ( $person->deathplace == 'Unknown' ? '' : " in $person->deathplace" );
                if( $count > 0 )
                    sort( $events[ $date ] );
    }
}

ksort( $events );

//print "<pre>";
//print_r( $events );
//print "</pre>\n";

print "<h1>Events List</h1>\n";
$month = 0;
foreach( $events as $date => $list ) {
    $this_month = substr( $date, 0, 2 ) * 1;
    if( $this_month != $month ) {
        $month = $this_month;
        if( $month != 1 ) {
            print "</div>\n";
            print "</div>\n";
        }            
        print "<div class=\"panel panel-default\">\n";
        print "<div class=\"panel-heading\">" . date( 'F', strtotime( date( 'Y' ) . '-' . substr( $date, 0, 2 ) . "-01" ) ) . "</div>\n";
        print "<div class=\"panel-body\">";
    }
    $day = substr( $date, 3, 2 ) * 1;
    print "<div class=\"row\">\n";
    print "<div class=\"col-sm-1\"><p><b>" . ( $day > 0 ? $day : '' ) . "</b></p></div>\n";
    print "<div class=\"col-sm-11\"><ul class=\"list-group\">\n";
    foreach( $list as $event ) {
        print "<li class=\"list-group-item\">$event</li>\n";
    }
    print "</ul></div></div>";
}
?>