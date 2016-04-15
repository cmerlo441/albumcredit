      <hr>

      <footer>
        <p>Today is <?php echo date( 'l, F j, Y' ); ?>.
<?php
$birthday_query = 'select id, first_name as f, last_name as l, birthdate, deathdate '
    . 'from people '
    . 'where birthdate like "%-' . date( 'm-d' ) .'" '
    . 'order by birthdate';
$birthday_result = $db->query( $birthday_query );
while( $person = $birthday_result->fetch_object() ) {
    $age = ( date( 'Y' ) * 1 ) - ( substr( $person->birthdate, 0, 4 ) * 1 );
    print "<br /><a href=\"person.php?person=$person->id\">$person->f $person->l</a> ";
    if( $person->deathdate != '0000-00-00' and $person->deathdate != '' )
        print 'would have turned';
    else
        print 'turns';
    print " $age years old today.\n";
}
$deaths_query = 'select id, first_name as f, last_name as l, birthdate, deathdate '
    . 'from people '
    . 'where deathdate like "%-' . date( 'm-d' ) . '" '
    . 'order by deathdate';
$deaths_result = $db->query( $deaths_query );
while( $person = $deaths_result->fetch_object() ) {
    $birth = new DateTime( $person->birthdate );
    $death = new DateTime( $person->deathdate );
    $interval = $death->diff( $birth );
    $died_at = $interval->format( '%y' );

    print "<br /><a href=\"person.php?person=$person->id\">$person->f $person->l</a> died on this date in " . date( 'Y', strtotime( $person->deathdate ) ) . " at age $died_at.\n";
}

$release_query = 'select aa.id as artist_id, aa.name as artist, '
    . 'a.id as album_id, a.name as title, a.release_date '
    . 'from album_artists as aa, albums as a '
    . 'where a.album_artist = aa.id '
    . 'and a.release_date like "%-' . date( 'm-d' ) . '" '
    . 'order by a.release_date';
$release_result = $db->query( $release_query );
while( $album = $release_result->fetch_object() ) {
    print "<br /><a href=\"album.php?album=$album->album_id\"><i>$album->title</i></a> "
        . " by <a href=\"album_artist.php?artist=$album->artist_id\">$album->artist</a> "
        . 'was released on this date in ' . substr( $album->release_date, 0, 4 ) . ".\n";
}
?>
		</p>

        <p>&copy; The Album Credit Project 2015</p>
      </footer>
    </div> <!-- /container -->
  </body>
</html>
