<?php

$no_header = 1;
require_once( './header.inc' );
require_once( './Stack.php' );

//$debug = 1;

$source = $db->real_escape_string( $_REQUEST[ 'source' ] );
$target = $db->real_escape_string( $_REQUEST[ 'target' ] );

// for teh d3bugz0rz
if( $debug ) {
    $source = 1;  // bruf
    $target = 336; // Alex Skolnick
}

$t1_query = 'create temporary table t1( '
    . 'id int auto_increment primary key, '
    . 'source_id int, '
    . 'source_name text, '
    . 'target_id int unique, '
    . 'target_name text, '
    . 'artist_id int, '
    . 'artist_name text, '
    . 'album_id int, '
    . 'album_name text, '
    . 'year int )';
if( $debug )
    print "<pre>$t1_query;</pre>\n";
$t1_result = $db->query( $t1_query );

$fill_t1_query = 'insert into t1( '
    . 'source_id, source_name, target_id, target_name, '
    . 'artist_id, artist_name, album_id, album_name, year ) '
    . 'select p1.id, concat( p1.first_name, " ", p1.last_name ), '
    . 'p2.id, concat( p2.first_name, " ", p2.last_name ), '
    . 'aa.id, aa.name, a.id, a.name, substring( a.release_date, 1, 4 ) '
    . 'from album_artists as aa, albums as a, people as p1, people as p2, '
    . 'musician_album_credits as c1, musician_album_credits as c2 '
    . 'where c1.album = a.id '
    . 'and c2.album = c1.album '
    . 'and a.album_artist = aa.id '
    . "and c1.musician = $source "
    . 'and c1.musician = p1.id '
    . 'and c2.musician = p2.id '
    . 'and c1.musician != c2.musician '
    . 'group by p2.id '
    . 'order by rand()';
if( $debug )
    print "<pre>$fill_t1_query;</pre>\n";
$fill_t1_result = $db->query( $fill_t1_query );

$fill_t1_query = 'insert ignore into t1( '
    . 'source_id, source_name, target_id, target_name, '
    . 'artist_id, artist_name, album_id, album_name, year ) '
    . 'select p1.id, concat( p1.first_name, " ", p1.last_name ), '
    . 'p2.id, concat( p2.first_name, " ", p2.last_name ), '
    . 'aa.id, aa.name, a.id, a.name, substring( a.release_date, 1, 4 ) '
    . 'from album_artists as aa, albums as a, people as p1, people as p2, '
    . 'musician_song_credits as c1, musician_song_credits as c2, songs as s1, songs as s2 '
    . 'where c1.song = s1.id and s1.album = a.id '
    . 'and c2.song = s2.id and s2.album = s1.album '
    . 'and c1.song = c2.song '
    . 'and a.album_artist = aa.id '
    . "and c1.musician = $source "
    . 'and c1.musician = p1.id '
    . 'and c2.musician = p2.id '
    . 'and c1.musician != c2.musician '
    . 'group by p2.id order by rand()';
if( $debug )
    print "<pre>$fill_t1_query;</pre>\n";
$fill_t1_result = $db->query( $fill_t1_query );

$fill_t1_query = 'insert ignore into t1( '
    . 'source_id, source_name, target_id, target_name, '
    . 'artist_id, artist_name, album_id, album_name, year ) '
    . 'select p1.id, concat( p1.first_name, " ", p1.last_name ), '
    . 'p2.id, concat( p2.first_name, " ", p2.last_name ), '
    . 'aa.id, aa.name, a.id, a.name, substring( a.release_date, 1, 4 ) '
    . 'from album_artists as aa, albums as a, people as p1, people as p2, '
    . 'musician_song_credits as c1, musician_album_credits as c2, songs as s '
    . 'where c1.song = s.id and s.album = a.id '
    . 'and c2.album = s.album '
    . 'and a.album_artist = aa.id '
    . "and c1.musician = $source "
    . 'and c1.musician = p1.id '
    . 'and c2.musician = p2.id '
    . 'and c1.musician != c2.musician '
    . 'group by p2.id order by rand()';
if( $debug )
    print "<pre>$fill_t1_query;</pre>\n";
$fill_t1_result = $db->query( $fill_t1_query );

$fill_t1_query = 'insert ignore into t1( '
    . 'source_id, source_name, target_id, target_name, '
    . 'artist_id, artist_name, album_id, album_name, year ) '
    . 'select p1.id, concat( p1.first_name, " ", p1.last_name ), '
    . 'p2.id, concat( p2.first_name, " ", p2.last_name ), '
    . 'aa.id, aa.name, a.id, a.name, substring( a.release_date, 1, 4 ) '
    . 'from album_artists as aa, albums as a, people as p1, people as p2, '
    . 'musician_album_credits as c1, musician_song_credits as c2, songs as s '
    . 'where c1.album = a.id '
    . 'and c2.song = s.id and s.album = c1.album '
    . 'and a.album_artist = aa.id '
    . "and c1.musician = $source "
    . 'and c1.musician = p1.id '
    . 'and c2.musician = p2.id '
    . 'and c1.musician != c2.musician '
    . 'group by p2.id order by rand()';
if( $debug )
    print "<pre>$fill_t1_query;</pre>\n";
$fill_t1_result = $db->query( $fill_t1_query );

$stage = 0;
for( $stage = 2; $stage <= 6; ++$stage ) {
    $prev_table = 't' . ( $stage - 1 );
    $curr_table = 't' . $stage;

    $find_query = "select * from $prev_table where target_id = $target";
    $find_result = $db->query( $find_query );
    if( $find_result->num_rows > 0 ) {
        if( $debug )
            print "Got it in $prev_table\n";
        break;
    }
    $create_query = "create temporary table $curr_table( "
        . 'id int auto_increment primary key, '
        . 'prev int, '
        . 'source_id int, '
        . 'source_name text, '
        . 'target_id int unique, '
        . 'target_name text, '
        . 'artist_id int, '
        . 'artist_name text, '
        . 'album_id int, '
        . 'album_name text, '
        . 'year int )';
    if( $debug )
        print "<pre>$create_query;</pre>\n";
    $create_result = $db->query( $create_query );

    $select_query = "select * from $prev_table";
    $select_result = $db->query( $select_query );
    while( $row = $select_result->fetch_object() ) {
        $fill_query = "insert ignore into $curr_table( "
            . 'prev, source_id, source_name, target_id, target_name, '
            . 'artist_id, artist_name, album_id, album_name, year ) '
            . "select $row->id, p1.id, concat( p1.first_name, ' ', p1.last_name ), "
            . 'p2.id, concat( p2.first_name, " ", p2.last_name ), '
            . 'aa.id, aa.name, a.id, a.name, substring( a.release_date, 1, 4 ) '
            . 'from album_artists as aa, albums as a, people as p1, people as p2, '
            . 'musician_album_credits as c1, musician_album_credits as c2 '
            . 'where c1.album = a.id '
            . 'and c2.album = c1.album '
            . 'and a.album_artist = aa.id '
            . "and c1.musician = $row->target_id "
            . 'and c1.musician = p1.id '
            . 'and c2.musician = p2.id '
            . 'and c1.musician != c2.musician '
            . "and c1.musician != $source "
            . "and c2.musician != $source ";
        for( $i = $stage - 1; $i >= 1; $i-- )
            $fill_query .= "and c2.musician not in (select target_id from t$i) ";
        $fill_query .= 'group by p2.id order by rand()';
        if( $debug )
            print "<pre>$fill_query;</pre>\n";
        $fill_result = $db->query( $fill_query );

        $fill_query = "insert ignore into $curr_table( "
            . 'prev, source_id, source_name, target_id, target_name, '
            . 'artist_id, artist_name, album_id, album_name, year ) '
            . "select $row->id, p1.id, concat( p1.first_name, ' ', p1.last_name ), "
            . 'p2.id, concat( p2.first_name, " ", p2.last_name ), '
            . 'aa.id, aa.name, a.id, a.name, substring( a.release_date, 1, 4 ) '
            . 'from album_artists as aa, albums as a, people as p1, people as p2, '
            . 'musician_song_credits as c1, musician_song_credits as c2, songs as s1, songs as s2 '
            . 'where c1.song = s1.id and s1.album = a.id '
            . 'and c2.song = s2.id and s2.album = s1.album '
            . 'and c1.song = c2.song '
            . 'and a.album_artist = aa.id '
            . "and c1.musician = $row->target_id "
            . 'and c1.musician = p1.id '
            . 'and c2.musician = p2.id '
            . 'and c1.musician != c2.musician '
            . "and c1.musician != $source "
            . "and c2.musician != $source ";
        for( $i = $stage - 1; $i >= 1; $i-- )
            $fill_query .= "and c2.musician not in (select target_id from t$i) ";
        $fill_query .= 'group by p2.id order by rand()';
        if( $debug )
            print "<pre>$fill_query;</pre>\n";
        $fill_result = $db->query( $fill_query );

        $fill_query = "insert ignore into $curr_table( "
            . 'prev, source_id, source_name, target_id, target_name, '
            . 'artist_id, artist_name, album_id, album_name, year ) '
            . "select $row->id, p1.id, concat( p1.first_name, ' ', p1.last_name ), "
            . 'p2.id, concat( p2.first_name, " ", p2.last_name ), '
            . 'aa.id, aa.name, a.id, a.name, substring( a.release_date, 1, 4 ) '
            . 'from album_artists as aa, albums as a, people as p1, people as p2, '
            . 'musician_song_credits as c1, musician_album_credits as c2, songs as s '
            . 'where c1.song = s.id and s.album = a.id '
            . 'and c2.album = s.album '
            . 'and a.album_artist = aa.id '
            . "and c1.musician = $row->target_id "
            . 'and c1.musician = p1.id '
            . 'and c2.musician = p2.id '
            . 'and c1.musician != c2.musician '
            . "and c1.musician != $source "
            . "and c2.musician != $source ";
        for( $i = $stage - 1; $i >= 1; $i-- )
            $fill_query .= "and c2.musician not in (select target_id from t$i) ";
        $fill_query .= 'group by p2.id order by rand()';
        if( $debug )
            print "<pre>$fill_query;</pre>\n";
        $fill_result = $db->query( $fill_query );

        $fill_query = "insert ignore into $curr_table( "
            . 'prev, source_id, source_name, target_id, target_name, '
            . 'artist_id, artist_name, album_id, album_name, year ) '
            . "select $row->id, p1.id, concat( p1.first_name, ' ', p1.last_name ), "
            . 'p2.id, concat( p2.first_name, " ", p2.last_name ), '
            . 'aa.id, aa.name, a.id, a.name, substring( a.release_date, 1, 4 ) '
            . 'from album_artists as aa, albums as a, people as p1, people as p2, '
            . 'musician_album_credits as c1, musician_song_credits as c2, songs as s '
            . 'where c1.album = a.id '
            . 'and c2.song = s.id and s.album = c1.album '
            . 'and a.album_artist = aa.id '
            . "and c1.musician = $row->target_id "
            . 'and c1.musician = p1.id '
            . 'and c2.musician = p2.id '
            . 'and c1.musician != c2.musician '
            . "and c1.musician != $source "
            . "and c2.musician != $source ";
        for( $i = $stage - 1; $i >= 1; $i-- )
            $fill_query .= "and c2.musician not in (select target_id from t$i) ";
        $fill_query .= 'group by p2.id order by rand()';
        if( $debug )
            print "<pre>$fill_query;</pre>\n";
        $fill_result = $db->query( $fill_query );
    }
}

/*
for( $i = 1; $i < $stage; ++$i ) {
    print "<h2>$i</h2>\n";
    $show_query = "select * from t$i";
    $show_result = $db->query( $show_query );
    while( $row = $show_result->fetch_object() ) {
        print "<pre>";
        print_r( $row->target_name );
        print "</pre>\n";
    }
}
*/

// Found in stage 1?
if( --$stage == 1 ) {
    print "They played together.";
    die();
}

$s = new Stack();
$prev = -1;
for( $i = $stage; $i > 0; --$i ) {
    // print "<pre>Stage $i</pre>\n";
    $query = "select * from t$i where ";
    if( $i == $stage )
        $query .= "target_id = $target ";
    else
        $query .= "id = $prev ";
    if( $debug )
        print "<pre>$query</pre>\n";
    $result = $db->query( $query );
    if( $result->num_rows == 0 ) {
        print "There is no connection.";
        die();
    }
    $row = $result->fetch_object();

    $string = "<a href=\"person.php?person=$row->source_id\">"
        . "<strong>$row->source_name</strong></a> played on "
        . "<a href=\"album.php?album=$row->album_id\">"
        . "<i>$row->album_name</i></a>"
        . " by <a href=\"album_artist.php?artist=$row->artist_id\">"
        . "$row->artist_name</a> "
        . "with <a href=\"person.php?person=$row->target_id\">"
        . "<strong>$row->target_name</strong></a> in $row->year.";
    $s->push( $string );

    $prev = $row->prev;
    $db->query( "drop temporary table t$i" );
}

print "<ul class=\"list-group\">\n";
while( ! $s->isEmpty() ) {
    print "<li class=\"list-group-item\">" . $s->pop() . "</li>\n";
}
print "</ul>\n";

/*
$show_t1_query = 'select * from t2';
$show_t1_result = $db->query( $show_t1_query );
while( $row = $show_t1_result->fetch_object() ) {
    print "<pre>$row->source_name, $row->album_name, $row->target_name</pre>\n";
}
/* */

?>