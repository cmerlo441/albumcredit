<?php

require_once( './header.inc' );

?>

    <div class="container">
        <h1>List of Instruments</h1>
        <div class="row">
            <div class="col-md-12">
                <table class="table table-condensed">
                    <thead>
                        <tr>
                            <th>Instrument</th>
                            <th>Number of Albums</th>
                        </tr>
                    </thead>
                    
                    <tbody>
<?php

$i_query = 'select id, instrument '
    . 'from instruments '
    . 'order by instrument';
$i_result = $db->query( $i_query );
while( $i = $i_result->fetch_object() ) {
?>
                        <tr>
                            <td><a href="instrument.php?instrument=<?php echo $i->id; ?>"><?php echo $i->instrument; ?></a></td>
<?php
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
    $count = $albums_result->num_rows + $songs_result->num_rows;
?>
                            <td><?php echo $count; ?></td>
                        </tr>
<?php
}

?>
                    </tbody>
                </table>
            </div>
        </div>
<?php

include_once( './footer.inc' );

?>