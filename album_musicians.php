<?php

$no_header = 1;
require_once( './header.inc' );

$id = $db->real_escape_string( $_REQUEST[ 'album' ] );

$credits = array();

$full_album_query = 'select p.id, p.first_name, p.last_name, p.birthdate, '
    . 'a.release_date as ard '
    . 'from musician_album_credits as mac, people as p, albums as a '
    . 'where mac.musician = p.id '
    . "and mac.album = $id "
    . 'and mac.album = a.id '
    . 'group by p.id';
$full_album_result = $db->query( $full_album_query );
while( $credit = $full_album_result->fetch_object() ) {
    $name = "$credit->last_name, $credit->first_name";
    $credits[ $name ][ 'id' ] = $credit->id;
    $credits[ $name ][ 'name'] = "$credit->first_name $credit->last_name";

    if( preg_match( "/[0-9][1-9]|[1-9][0-9]$/", $credit->ard ) == 1 and
        preg_match( "/[0-9][1-9]|[1-9][0-9]$/", $credit->birthdate ) == 1 ) {
        $rd = new DateTime( $credit->ard );
        $bd = new DateTime( $credit->birthdate );
        $i = $rd->diff( $bd );
        $credits[ $name ][ 'age' ] = $i->format( '%y' );
    }

	$instruments_query = 'select i.instrument '
        . 'from musician_album_credits as mac, instruments as i '
        . "where mac.musician = $credit->id "
        . "and mac.album = $id "
        . 'and mac.instrument = i.id '
        . 'order by i.instrument';
	$instruments_result = $db->query( $instruments_query );
	$count = 0;
	while( $instrument = $instruments_result->fetch_object( ) ) {
	    if( $count != 0 )
	        $credits[ $name ][ 'instrument' ] .= ', ';
	    $credits[ $name ][ 'instrument' ] .= $instrument->instrument;
	    ++$count;
	}
}

$partials = array();

$song_credit_query = 'select p.id, p.first_name, p.last_name, p.birthdate, '
    . 'a.release_date as ard, '
    . 's.title, s.sequence, i.instrument '
    . 'from musician_song_credits as m, people as p, '
    . 'songs as s, instruments as i, albums as a '
    . 'where m.song = s.id '
    . "and s.album = $id "
    . 'and m.musician = p.id '
    . 'and m.instrument = i.id '
    . 'and s.album = a.id '
    . 'order by s.sequence';
$song_credit_result = $db->query( $song_credit_query );
while( $credit = $song_credit_result->fetch_object() ) {
    $name = "$credit->last_name, $credit->first_name";
    $credits[ $name ][ 'name' ] = "$credit->first_name $credit->last_name";
    if( ! isset( $credits[ $name ][ 'id ' ] ) )
        $credits[ $name ][ 'id' ] = $credit->id;
    if( isset( $partials[ $name ][ $credit->instrument ] ) ) {
        $partials[ $name ][ $credit->instrument ] .= ', ' . $credit->sequence;
    } else {
        $partials[ $name ][ $credit->instrument ] = $credit->sequence;
    }

    if( preg_match( "/[0-9][1-9]|[1-9][0-9]$/", $credit->ard ) == 1 and
        preg_match( "/[0-9][1-9]|[1-9][0-9]$/", $credit->birthdate ) == 1 ) {
        $rd = new DateTime( $credit->ard );
        $bd = new DateTime( $credit->birthdate );
        $i = $rd->diff( $bd );
        $credits[ $name ][ 'age' ] = $i->format( '%y' );
    }

}
$song_credit_result->close;

foreach( $partials as $name => $instrument ) {
    $add = '';
    $inst_count = 0;
    foreach( $instrument as $instrument_name => $tracks ) {
        if( $inst_count++ > 0 )
            $add .= ', ';
        $count = substr_count( $tracks, ',' ) + 1;
        if( $count == 1 ) {
            $add .= "$instrument_name (track $tracks)";
        } else if( $count == 2 ) {
            $add .= "$instrument_name (tracks ";
            $numbers = explode( ', ', $tracks );
            $add .= "{$numbers[ 0 ]} and {$numbers[ 1 ]})";
        } else {
            $add .= "$instrument_name (tracks ";
            $numbers = explode( ', ', $tracks );
            for( $i = 0; $i < sizeof( $numbers ) - 1; ++$i )
                $add .= $numbers[ $i ] . ', ';
            $add .= "and {$numbers[ $i ]})";
        }
    }
    if( ! isset( $credits[ $name ][ 'instrument' ] ) ) {
        $credits[ $name ][ 'instrument' ] = $add;
    } else {
        $credits[ $name ][ 'instrument' ] .= ", $add";
    }
}

?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Musician</th>
                        <th>Age</th>
                        <th>Instruments</th>
                    </tr>
                </thead>
                
                <tbody>
<?php
ksort( $credits );
foreach( $credits as $name => $credit ) {
?>
                    <tr>
                        <td>
                            <a href="person.php?person=<?php echo $credit[ 'id' ];?>">
                            <?php echo $credit[ 'name' ]; ?></a></td>
                            <td>
                            <?php
                                if( $credit[ 'age' ] > 0 )
                                    echo $credit[ 'age' ];
                            ?></td>
                        </td>
                        <td><?php echo $credit[ 'instrument' ]; ?></td>
                    </tr>
<?php
}
?>
                </tbody>
            </table>
