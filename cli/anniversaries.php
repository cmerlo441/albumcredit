#! /usr/local/php54/bin/php
<?php

$no_header = 1;
require_once( '../header.inc' );

$birthdays_query = 'select id, first_name as f, last_name as l, birthdate, '
    . 'deathdate, deathplace '
    . 'from people '
    . 'where birthdate like "%-' . date( 'm-d' )
    . '" order by birthdate desc, last_name, first_name';
$birthdays_result = $db->query( $birthdays_query );
while( $birthday = $birthdays_result->fetch_object() ) {
    $age = ( date( 'Y' ) * 1 ) - ( substr( $birthday->birthdate, 0, 4 ) * 1 );
    print "$birthday->f $birthday->l ";
    if ( $birthday->deathdate == '' or $birthday->deathdate == '0000-00-00' )
        print 'turns';
    else
        print 'would have turned';
    print " $age years old today.\n";
}

$album_ann_query = 'select a.id, a.name as album, a.release_date, '
    . 'aa.name as band '
    . 'from albums as a, album_artists as aa '
    . 'where a.album_artist = aa.id '
    . 'and a.release_date like "%-' . date( 'm-d' )
    . '" order by release_date, aa.name';
$album_ann_result = $db->query( $album_ann_query );
while( $ann = $album_ann_result->fetch_object() ) {
    print "\"$ann->album\" by $ann->band was released on this date in "
        . substr( $ann->release_date, 0, 4 ) . '.  '
        . "http://www.albumcredit.org/album.php?album=$ann->id\n";
}

?>
