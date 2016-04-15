<?php

require_once( './header.inc' );

$id = $db->real_escape_string( $_REQUEST[ 'label' ] );

$albums_query = 'select l.name as label, '
    . 'aa.name as artist, '
    . 'a.name as title, a.release_date, a.catalog '
    . 'from labels as l, album_artists as aa, albums as a '
    . 'where a.label = l.id '
    . "and l.id = $id "
    . 'and a.album_artist = aa.id '
    . 'order by a.release_date, aa.sort';
$albums_result = $db->query( $albums_query );

?>
	<div class="container">
		<div class="row">
			<div class="col-md-12">
<?php
print $albums_query;
?>            
            </div>
        </div>

<?php

require_once ('./footer.inc' );
?>