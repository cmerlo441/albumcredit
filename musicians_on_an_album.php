<?php

$no_header = 1;
require_once( './header.inc' );

$id = $db->real_escape_string( $_REQUEST[ 'album' ] );

$mac_query = 'select p.id, p.first_name, p.last_name '
    . 'from musician_album_credits as mac, people as p '
    . 'where mac.musician = p.id '
    . "and mac.album = $id "
    . 'group by p.id '
    . 'order by p.last_name, p.first_name';
$mac_result = $db->query( $mac_query );

$credits = array();
while( $mac = $mac_result->fetch_object() ) {
	$credits[ $mac->id ][ 'musician' ] = "$mac->first_name $mac->last_name";
	$instruments_query = 'select i.instrument '
        . 'from musician_album_credits as mac, instruments as i '
        . "where mac.musician = $mac->id "
        . "and mac.album = $id "
        . 'and mac.instrument = i.id '
        . 'order by i.instrument';
	$instruments_result = $db->query( $instruments_query );
	$count = 0;
	while( $instrument = $instruments_result->fetch_object( ) ) {
	    if( $count != 0 )
	        $credits[ $mac->id ][ 'instrument' ] .= ', ';
	    $credits[ $mac->id ][ 'instrument' ] .= $instrument->instrument;
	    ++$count;
	}
}


if( sizeof( $credits ) == 0 ) {
    print "No musician credits have been entered for this album yet.";
} else {
?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Musician</th>
                        <th>Instruments</th>
                    </tr>
                </thead>
                
                <tbody>
<?php
    foreach( $credits as $id => $mac ) {
?>
                    <tr>
                        <td>
                            <a href="person.php?person=<?php echo $id;?>">
                            <?php echo $mac[ 'musician' ]; ?></a>
                        </td>
                        <td><?php echo $mac[ 'instrument' ]; ?></td>
                    </tr>
<?php
    }
?>
                </tbody>
            </table>
<?php 
}
?>