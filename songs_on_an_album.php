<?php

$no_header = 1;
require_once( './header.inc' );

$id = $db->real_escape_string( $_REQUEST[ 'album' ] );

$songs_query = 'select id, title, sequence '
	. 'from songs '
	. "where album = $id "
	. 'order by sequence';
$songs_result = $db->query( $songs_query );
if( $songs_result->num_rows == 0 )
	print 'No songs have been entered for this album yet.';
else {
	$songs = array();
	while( $song = $songs_result->fetch_object() ) {
		$songs[ $song->id ][ 'sequence' ] = $song->sequence;
		$songs[ $song->id ][ 'title' ]   = $song->title; 
	}
?>
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Song</th>
                    </tr>
                </thead>
                
                <tbody>
<?php
    foreach( $songs as $id => $song ) {
?>
                    <tr>
                        <td><?php echo $song[ 'sequence' ]; ?></td>
                        <td><?php echo $song[ 'title' ]; ?></td>
                    </tr>
<?php
    }
?>
                </tbody>
            </table>
<?php
}
?>