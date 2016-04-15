<?php

$no_header = 1;
require_once './header.inc';

$id = $db->real_escape_string( $_REQUEST[ 'artist' ] );

$albums_query = 'select id, name, release_year, release_month, release_day '
    . ' from albums '
    . "where album_artist = \"$id\" "
	. 'order by release_year, release_month, release_day';
$albums_result = $db->query( $albums_query );
if( $albums_result->num_rows > 0 ) {
?>
                <ul class="list-group">
<?php
	while( $row = $albums_result->fetch_object() ) {
        $date = $row->release_year;
        if( $row->release_month > 0 ) {
            if( $row->release_day > 0 ) {
                $s = "{$row->release_year}-"
                    . ($row->release_month < 10 ? '0' : '' )
                    . "{$row->release_month}-"
                    . ( $row->release_day < 10 ? '0' : '' )
                    . "{$row->release_day}";
                $date = date( 'F jS, Y', strtotime( $s ) );
            } else {
                $s = "{$row->release_year}-"
                    . ( $row->release_month < 10 ? '0' : '' )
                    . "{$row->release_month}-01";
                $date = date( 'F Y', strtotime( $s ) );
            }
        }
		print "                <a href=\"album.php?album=$row->id\">"
		    . "<li class=\"list-group-item\">$row->name"
		    . "<span class=\"badge\">$date</li></a>\n";
	}
?>
				</ul>
<?php
	} else {
		print "None.";
	}

?>