<?php

require_once ('./header.inc' );

$id = $db->real_escape_string( $_REQUEST[ 'instrument' ] );
$sort_by = $db->real_escape_string( $_REQUEST[ 'sort_by' ] );

$i_query = 'select instrument from instruments '
    . "where id = $id";
$i_result = $db->query( $i_query );
$i_row = $i_result->fetch_object();
$i = $i_row->instrument;
$i_result->close();

$albums = array();

$albums_query = 'select album '
    . 'from musician_album_credits '
    . "where instrument = $i->id "
    . 'group by album';
$albums_result = $db->query( $albums_query );
$songs_query = 'select s.album '
    . 'from musician_song_credits as msc, songs as s '
    . "where msc.instrument = $i->id "
    . 'and msc.song = s.id '
    . 'and s.album not in ( select album from musician_album_credits '
        . "where instrument = $i->id ) "
    . 'group by s.album';
$songs_result = $db->query( $songs_query );

$mac_query = 'select p.id as person_id, p.first_name as fn, p.last_name as ln, '
    . 'a.id as album_id, a.name as title, a.release_date as date, '
    . 'aa.id as artist_id, aa.name as artist, aa.sort '
    . 'from musician_album_credits as mac, people as p, '
    . 'albums as a, album_artists as aa '
    . "where mac.instrument = $id "
    . 'and mac.musician = p.id '
    . 'and mac.album = a.id '
    . 'and a.album_artist = aa.id '
    . 'order by a.release_date, p.last_name, p.first_name;';
$mac_result = $db->query( $mac_query );
while( $mac_row = $mac_result->fetch_object() ) {
    $album_id = $mac_row->album_id;
    $albums[ $album_id ][ 'title' ]        = $mac_row->title;
    $albums[ $album_id ][ 'date' ]         = $mac_row->date;
    $albums[ $album_id ][ 'artist' ]       = $mac_row->artist;
    $albums[ $album_id ][ 'artist_id' ]    = $mac_row->artist_id;
    $albums[ $album_id ][ 'artist_sort' ]  = $mac_row->sort;
    $albums[ $album_id ][ 'performers' ][][ 'last' ] = $mac_row->ln;
    $p_count = sizeof( $albums[ $album_id ][ 'performers' ] );
    $albums[ $album_id ][ 'performers' ][ $p_count - 1][ 'first' ] = $mac_row->fn;
    $albums[ $album_id ][ 'performers' ][ $p_count - 1 ][ 'id' ] = $mac_row->person_id;
}

$msc_query = 'select p.id as person_id, p.first_name as fn, p.last_name as ln, '
    . 'a.id as album_id, a.name as title, a.release_date as date, '
    . 'aa.id as artist_id, aa.name as artist, aa.sort '
    . 'from musician_song_credits as msc, people as p, '
    . 'songs as s, albums as a, album_artists as aa '
    . "where msc.instrument = $id "
    . 'and msc.musician = p.id '
    . 'and msc.song = s.id '
    . 'and s.album = a.id '
    . 'and a.album_artist = aa.id '
    . 'group by p.id '
    . 'order by a.release_date, p.last_name, p.first_name;';
$msc_result = $db->query( $msc_query );
while( $msc_row = $msc_result->fetch_object() ) {
    $album_id = $msc_row->album_id;
    $albums[ $album_id ][ 'title' ]        = $msc_row->title;
    $albums[ $album_id ][ 'date' ]         = $msc_row->date;
    $albums[ $album_id ][ 'artist' ]       = $msc_row->artist;
    $albums[ $album_id ][ 'artist_id' ]    = $msc_row->artist_id;
    $albums[ $album_id ][ 'artist_sort' ]  = $msc_row->sort;
    $albums[ $album_id ][ 'performers' ][][ 'last' ] = $msc_row->ln;
    $p_count = sizeof( $albums[ $album_id ][ 'performers' ] );
    $albums[ $album_id ][ 'performers' ][ $p_count - 1][ 'first' ] = $msc_row->fn;
    $albums[ $album_id ][ 'performers' ][ $p_count - 1 ][ 'id' ] = $msc_row->person_id;
}

if( $sort_by == 'album' ) {
    usort( $albums, function( $a, $b ) {
        if( $a[ 'title' ] == $b[ 'title' ] )
            return $a[ 'date' ] - $b[ 'date' ];
        return $a[ 'title' ] > $b[ 'title' ];
    });
} else if( $sort_by == 'performer' ) {
    usort( $albums, function( $a, $b ) {
        if( $a[ 'performers' ][ 0 ][ 'last' ] == $b[ 'performers' ][ 0 ][ 'last' ] )
            return $a[ 'date' ] - $b[ 'date' ];
        return $a[ 'performers' ][ 0 ][ 'last' ] > $b[ 'performers' ][ 0 ][ 'last' ];
    });
} else if( $sort_by == 'artist' ) {
    usort( $albums, function( $a, $b ) {
        if( $a[ 'artist' ] == $b[ 'artist' ] )
            return $a[ 'date' ] - $b[ 'date' ];
        return $a[ 'artist_sort' ] > $b[ 'artist_sort' ];
    });
} else {
    usort( $albums, function( $a, $b ) {
        return $a[ 'date' ] - $b[ 'date' ];
    });
}
?>

    <div class="container">
        <h1><?php echo $i; ?> Credits</h1>
        <div class="row">
            <div class="col-md-12">
<?php
$s = sizeof( $albums );
print "            <p>There ha" . ( $s == 1 ? 's' : 've' )
    . " been $s album" . ( $s == 1 ? '' : 's' )
    . " featuring $i.</p>\n";
$url = "{$_SERVER[ 'PHP_SELF' ]}?instrument=$id&sort_by=";
?>
                <table class="table table-condensed">
                    <thead>
                        <tr>
                            <th><a href="<?php echo $url; ?>year">Year</a></th>
                            <th><a href="<?php echo $url; ?>performer">Performers</a></th>
                            <th><a href="<?php echo $url; ?>artist">Artist</a></th>
                            <th><a href="<?php echo $url; ?>album">Album</a></th>
                        </tr>
                    </thead>
                    
                    <tbody>
<?php

foreach( $albums as $album_id => $data ) {
    print "                        <tr>\n";
    print "                            <td>"
        . date( 'Y', strtotime( $data[ 'date' ] ) ) . "</td>\n";
    print "                            <td>";
    for( $j = 0; $j < sizeof( $data[ 'performers' ] ); ++$j ) {
        if( $j != 0 )
            print ', ';
        print "<a href=\"person.php?person={$data[ 'performers' ][ $j ][ 'id' ]}\">"
            . $data[ 'performers' ][ $j ][ 'first' ] . ' '
            . $data[ 'performers' ][ $j ][ 'last' ];
    }
    print "</td>\n";
    print "                            <td>"
        . "<a href=\"album_artist.php?artist={$data[ 'artist_id' ]}\">"
        . "{$data[ 'artist' ]}</a></td>\n";
    print "                            <td>"
        . "<a href=\"album.php?album=$album_id\">"
        . "{$data[ 'title' ]}</a></td>\n";
    print "                        </tr>\n";
}

?>
                    </tbody>
                </table>
            </div>
        </div>

<?php
require_once( './footer.inc' );
?>