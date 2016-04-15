<?php 

require_once( './header.inc' );

?>

    <div class="container">
        <h1>List of Albums</h1>
        <div class="row">
            <div class="col-md-12">
                <table class="table table-condensed">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Artist</th>
                            <th>Year</th>
                        </tr>
                    </thead>
                    
                    <tbody>
<?php
$albums_query = 'select aa.id as artist_id, aa.name as artist, '
    . 'a.id as album_id, a.name as title, a.release_date '
    . 'from album_artists as aa, albums as a '
    . 'where a.album_artist = aa.id '
    . 'order by a.name, aa.name, a.release_date';
$albums_result = $db->query( $albums_query );
while( $album = $albums_result->fetch_object() ) {
    print "                        <tr>\n";
    print "                            <td><a href=\"album.php?album=$album->album_id\">"
        . "$album->title</a></td>\n";
    print "                            <td><a href=\"album_artist.php?artist=$album->artist_id\">"
        . "$album->artist</a></td>\n";
    print "                            <td>" . date( 'Y', strtotime( $album->release_date ) ) . "</td>\n";
    print "                         </tr>\n";
}
?>                        
                    </tbody>
                </table>
            </div>
        </div>
<?php 
require_once( './footer.inc' );
?>