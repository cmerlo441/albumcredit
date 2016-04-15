<?php

$no_header = 1;
require_once( './header.inc' );

$artist = $db->real_escape_string( $_REQUEST[ 'artist' ] );

$people = array();
$album_credits_query = 'select p.id, p.first_name as f, p.last_name as l, '
    . 'a.id as album '
    . 'from people as p, albums as a, musician_album_credits as c '
    . 'where c.musician = p.id '
    . 'and c.album = a.id '
    . "and a.album_artist = $artist "
//    . 'group by a.id '
    . 'order by a.id, p.last_name, p.first_name';
$album_credits_result = $db->query( $album_credits_query );

while( $person = $album_credits_result->fetch_object( ) ) {
    $people[ $person->id ][ 'last' ] = $person->l;
    $people[ $person->id ][ 'first' ] = $person->f;
    $people[ $person->id ][ 'albums' ][] = $person->album;
    $people[ $person->id ][ 'albums' ] = array_unique( $people[ $person->id ][ 'albums' ] );
}

$song_credits_query = 'select p.id, p.first_name as f, p.last_name as l, '
    . 'a.id as album '
    . 'from people as p, albums as a, songs as s, musician_song_credits as c '
    . 'where c.musician = p.id '
    . 'and c.song = s.id '
    . 'and s.album = a.id '
    . "and a.album_artist = $artist "
    . 'order by p.last_name, p.first_name';
$song_credits_result = $db->query( $song_credits_query );
while( $person = $song_credits_result->fetch_object() ) {
    $people[ $person->id ][ 'last' ] = $person->l;
    $people[ $person->id ][ 'first' ] = $person->f;
    $people[ $person->id ][ 'albums' ][] = $person->album;
    $people[ $person->id ][ 'albums' ] = array_unique( $people[ $person->id ][ 'albums' ] );
}

$output = array();
foreach( $people as $id => $person ) {
    $output[] = "{$person[ 'last' ]}{$person[ 'first' ]}!" // added for sorting
        . "                <a href=\"person.php?person=$id\">"
        . "<li class=\"list-group-item\">"
        . "{$person[ 'first' ]} {$person[ 'last' ]}"
        . "<span class=\"badge\">" . sizeof( $person[ 'albums' ] )
        . " album" . ( sizeof( $person[ 'albums' ] ) > 1 ? 's' : '' )
        . "</span></li></a>\n";
}
sort( $output );

?>

            <ul class="list-group">
<?php
foreach( $output as $item )
    print substr( $item, strpos( $item, '!' ) + 1 );  // removes what was added for sorting
?>
            </ul>