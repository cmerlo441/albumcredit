                <ul class="list-group">
<?php
require_once( './header.inc' );

$artists_query = 'select id, name, sort '
    . 'from album_artists '
    . 'order by sort';
$artists_result = $db->query( $artists_query );

if( $artists_result->num_rows == 0 ) {
    print "No artists.";
} else {
?>
        <table class="table table-condensed">
          <thead>
            <tr>
              <th>Artist</th>
              <th>Earliest Album</th>
              <th>Latest Album</th>
              <th>Album Count</th>
            </tr>
          </thead>

          <tbody>
<?php
    while( $artist = $artists_result->fetch_object() ) {
        $dates_query = 'select count( id ) as count, '
            . 'min( release_date ) as min, '
            . 'max( release_date ) as max '
            . 'from albums '
            . "where album_artist = $artist->id";
        $dates_result = $db->query( $dates_query );
        $dates_row = $dates_result->fetch_object();
        $min = $dates_row->min;
        $max = $dates_row->max;
        $count = $dates_row->count;
        print "          <tr>";
        print "            <td><a href=\"album_artist.php?artist=$artist->id\">$artist->name</a></td>\n";
        print "            <td>" . date( 'Y', strtotime( $min ) ) . "</td>\n";
        print "            <td>" . date( 'Y', strtotime( $max ) ) . "</td>\n";
        print "            <td>$count</td>\n";
        print "          </tr>\n";
    }
?>
          </tbody>
        </table>
<?php
}

/*

while( $artist = $artists_result->fetch_object() ) {
    $albums_query = 'select count(id) as count from albums '
        . "where album_artist = $artist->id";
    $albums_result = $db->query( $albums_query );
    $albums_row = $albums_result->fetch_object();
    $albums_result->close();
    $count = $albums_row->count;
    print "        <a class=\"list-group-item\" "
        . "href=\"album_artist.php?artist=$artist->id\">$artist->name"
        . " <span class=\"badge\">$count album"
        . ( $count == 1 ? '' : 's' )
        . "</span></a>\n";
}

*/

?>
                </ul>
