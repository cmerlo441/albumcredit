<?php 

$no_header = 1;
require_once( './header.inc' );

$first = $db->real_escape_string( $_REQUEST[ 'first' ] );
$second = $db->real_escape_string( $_REQUEST[ 'second' ] );

$first_name_query = 'select first_name, last_name from people '
    . "where id = $first";
$first_name_result = $db->query( $first_name_query );
$first_name_row = $first_name_result->fetch_object();
$first_name_result->close();
$first_name = "$first_name_row->first_name $first_name_row->last_name";

$second_name_query = 'select first_name, last_name from people '
    . "where id = $second";
$second_name_result = $db->query( $second_name_query );
$second_name_row = $second_name_result->fetch_object();
$second_name_result->close();
$second_name = "$second_name_row->first_name $second_name_row->last_name";

$first_query = 'select aa.id as artist_id, aa.name as album_artist, '
    . 'a.id as album_id, a.name as title, a.release_date '
    . 'from musician_album_credits as c, album_artists as aa, albums as a '
    . "where c.musician = $first "
    . 'and c.album = a.id '
    . 'and a.album_artist = aa.id '
    . 'group by a.id '
    . 'order by a.release_date, aa.name, a.name';
$first_result = $db->query( $first_query );
$matches = array();
$count = 0;
while( $record = $first_result->fetch_object() ) {
    $second_query = 'select id '
        . 'from musician_album_credits '
        . "where album = $record->album_id "
        . "and musician = $second ";
    $second_result = $db->query( $second_query );
    if( $second_result->num_rows > 0 ) {
        $matches[ $count ][ 'album_id' ] = $record->album_id;
        $matches[ $count ][ 'album' ] = $record->title;
        $matches[ $count ][ 'artist_id' ] = $record->artist_id;
        $matches[ $count ][ 'artist' ] = $record->album_artist;
        $matches[ $count ][ 'year' ] = date( 'Y', strtotime( $record->release_date ) );
        ++$count;
    }
}

$collabs = sizeof( $matches );
if( $collabs > 0 ) {
    print "<p><b>Yes!</b>  $first_name and $second_name worked together "
        . "$collabs " . ( $collabs == 1 ? 'time' : 'times' ) . ":</p>\n";
?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Year</th>
                            <th>Artist</th>
                            <th>Album</th>
                        </tr>
                    </thead>
                    
                    <tbody>
<?php
    foreach( $matches as $collab ) {
?>
                        <tr>
                            <td><?php echo $collab[ 'year' ]; ?></td>
                            <td>
                                <a href="album_artist.php?artist=<?php echo $collab[ 'artist_id' ]; ?>">
                                <?php echo $collab[ 'artist' ]; ?></a>
                            </td>
                            <td>
                                <a href="album.php?album=<?php echo $collab[ 'album_id' ]; ?>">
                                <?php echo $collab[ 'album' ]; ?></a>
                        </tr>
<?php
    }
?>
                    </tbody>
                </table>
<?php
} else {
    print "<p>No, The Album Credits Project is not aware of $first_name and "
        . "$second_name ever working together.</p>\n";
}

?>