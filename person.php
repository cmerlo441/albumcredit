<?php

require_once( './header.inc' );

$person_id = $db->real_escape_string( $_REQUEST[ 'person' ] );

$person_query = 'select first_name, last_name, birthdate, birthplace, '
    . 'deathdate, deathplace '
	. 'from people '
	. "where id = \"$person_id\"";
$person_result = $db->query( $person_query );
if( $person_result->num_rows == 1 ) {
	$person = $person_result->fetch_object();
?>
	<div class="container">
		<h1><?php echo "$person->first_name $person->last_name"; ?></h1>
		<div class="row" id="birthdeath">
		    <div class="col-md-12">
                <span id="birth_death_data"></span>
                <script type="text/javascript">
                $(function(){
                    var person_id = <?php echo $person_id; ?>;
                    $.get( 'person_birthdeath.php',
                        { person: person_id },
                        function(data){
                            $('span#birth_death_data').html(data);
                        }
                    )
                })
                </script>
<?php
    if( isset( $user ) ) {
        print "<a href=\"javascript:void(0)\" id=\"show_edit_panel\">Edit $person->first_name's vital information</a>.\n";
?>
        <div class="row" id="edit_panel" style="display: none">
            <div class="col-md-1"></div>
            <div class="col-md-10">
                <div class="panel panel-success">
                    <div class="panel-heading text-center">
                        <p>Because you're logged in, you can add and edit information about <?php echo $person->first_name; ?>.</p>
                    </div>

                    <div class="panel-body">
                        <div class="row">
                            <div class="col-xl-6 col-lg-12">
                                <h3><?php echo $person->first_name; ?>'s Birth</h3>
                                <div class="form-inline">
                                    <div class="form-group">
                                        <label for="birthdate">Date of Birth</label>
                                        <?php $bd = $person->birthdate; ?>
                                        <input type="text" class="form-control" id="birthdate" placeholder="<?php echo $bd; ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="birthplace">Location</label>
                                        <?php $bp = $person->birthplace; ?>
                                        <input type="text" class="form-control" id="birthplace" placeholder="<?php echo $bp; ?>">
                                    </div>
                                    <a href="javascript:void(0)" id="birth_button" class="btn btn-default" type="submit">Update</a>
                                </div>
                            </div>
                            <div class="col-xl-6 col-lg-12">
                                <h3><?php echo $person->first_name; ?>'s Death</h3>
                                <div class="form-inline">
                                    <div class="form-group">
                                        <label for="deathdate">Date of Death</label>
                                        <?php $dd = $person->deathdate; ?>
                                        <input type="text" class="form-control" id="deathdate" placeholder="<?php echo $dd; ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="deathplace">Location</label>
                                        <?php $dp = $person->deathplace; ?>
                                        <input type="text" class="form-control" id="deathplace" placeholder="<?php echo $dp; ?>">
                                    </div>
                                    <a href="javascript:void(0)" id="death_button" class="btn btn-default" type="submit">Update</a>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        </p>

        <script type="text/javascript">
        $(function(){
            var person_id = <?php echo $person_id; ?>;
            $('a#show_edit_panel').click(function(){
                $('div#edit_panel').slideDown();
            })

            $('a#birth_button').click(function(){
                var date = $('input#birthdate').val();
                var place = $('input#birthplace').val();
                $.post( 'person_birthdeath.php',
                    { person: person_id, bd: date, bp: place },
                    function(data){
                        $('span#birth_death_data').html(data);
                        $('input#birthdate').val('');
                        $('input#birthplace').val('');
                    }
                )
            })

            $('a#death_button').click(function(){
                var date = $('input#deathdate').val();
                var place = $('input#deathplace').val();
                $.post( 'person_birthdeath.php',
                    { person: person_id, dd: date, dp: place },
                    function(data) {
                        $('span#birth_death_data').html(data);
                        $('input#deathdate').val('');
                        $('input#deathplace').val('');
                    }
                )
            })
        })
        </script>
<?php
	}
?>
            </div>
        </div>
		
		<div class="row">
		    <div class="col-md-6">
		        <h2>Performance Credits</h2>
<?php
    $albums = array();
    $full_album_query = 'select aa.id as artist_id, aa.name as artist, '
        . 'a.id as album_id, a.name as title, a.release_date '
        . 'from musician_album_credits as c, album_artists as aa, albums as a '
        . "where c.musician = $person_id "
	    . 'and c.album = a.id '
	    . 'and a.album_artist = aa.id '
        . 'group by c.album '
        . 'order by a.release_date';
    $full_album_result = $db->query( $full_album_query );
    while( $full_album_row = $full_album_result->fetch_object() ) {
        $albums[ $full_album_row->album_id ][ 'artist' ] = $full_album_row->artist;
        $albums[ $full_album_row->album_id ][ 'artist_id' ] = $full_album_row->artist_id;
        $albums[ $full_album_row->album_id ][ 'title' ] = $full_album_row->title; 
        $albums[ $full_album_row->album_id ][ 'year' ] = date( 'Y', strtotime( $full_album_row->release_date ) );
    	$instruments_query = 'select i.instrument '
        . 'from musician_album_credits as c, instruments as i '
        . "where c.musician = $person_id "
        . "and c.album = $full_album_row->album_id "
        . 'and c.instrument = i.id '
        . 'order by i.instrument';
    	$instruments_result = $db->query( $instruments_query );
    	$count = 0;
    	while( $instrument = $instruments_result->fetch_object( ) ) {
    	    if( $count != 0 )
    	        $albums[ $full_album_row->album_id ][ 'instrument' ] .= ', ';
    	    $albums[ $full_album_row->album_id ][ 'instrument' ] .= $instrument->instrument;
    	    ++$count;
    	}
    }
    
    $partials = array();
    
    $song_credit_query = 'select aa.id as artist_id, aa.name as artist, '
        . 'a.id as album_id, a.name as album_title, a.release_date, '
        . 's.title, s.sequence, i.instrument '
        . 'from musician_song_credits as m, album_artists as aa, '
        . 'songs as s, instruments as i, albums as a '
        . 'where m.song = s.id '
        . "and s.album = a.id "
        . 'and a.album_artist = aa.id '
        . "and m.musician = $person_id "
        . 'and m.instrument = i.id '
        . 'order by release_date';
    $song_credit_result = $db->query( $song_credit_query );
    while( $song_credit_row = $song_credit_result->fetch_object() ) {
        
        if( ! isset( $albums[ $song_credit_row->album_id ] ) ) {
            $albums[ $song_credit_row->album_id ][ 'artist' ] = $song_credit_row->artist;
            $albums[ $song_credit_row->album_id ][ 'artist_id' ] = $song_credit_row->artist_id;
            $albums[ $song_credit_row->album_id ][ 'title' ] = $song_credit_row->album_title; 
            $albums[ $song_credit_row->album_id ][ 'year' ] = date( 'Y', strtotime( $song_credit_row->release_date ) );
        }
        
        if( isset( $partials[ $song_credit_row->album_id ][ $credit->instrument ] ) ) {
            $partials[ $song_credit_row->album_id ][ $song_credit_row->instrument ] .= ', ' . $song_credit_row->sequence;
        } else {
            $partials[ $song_credit_row->album_id ][ $song_credit_row->instrument ] = $song_credit_row->sequence;
        }
    }
    $song_credit_result->close;
    
    foreach( $partials as $album_id => $instrument ) {
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
        if( ! isset( $albums[ $album_id ][ 'instrument' ] ) ) {
            $albums[ $album_id ][ 'instrument' ] = $add;
        } else {
            $albums[ $album_id ][ 'instrument' ] .= ", $add";
        }
    }

?>
                <p><?php echo $person->first_name; ?> has performed on <?php echo sizeof( $albums ); ?> albums.</p>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Year</th>
                            <th>Artist</th>
                            <th>Album</th>
                            <th>Performance</th>
                        </tr>
                    </thead>
                    
                    <tbody>
<?php
    $output = array();
    $count = 0;
    foreach( $albums as $id => $data ) {
        $output[ $count++ ] = <<<END
                        <tr>
                            <td>
                            {$data[ 'year' ]}
                            </td>
                            <td>
                                <a href="album_artist.php?artist={$data[ 'artist_id' ]}">
                                {$data[ 'artist' ]}</a>
                            </td>
                            <td>
                                <a href="album.php?album=$id">
                                {$data[ 'title' ]}</a>
                            </td>
                            <td>
                                {$data[ 'instrument' ]}
                            </td>
                        </tr>
END;
    }
    sort( $output );
    foreach( $output as $row )
        print $row;
    
?>
                    </tbody>
                </table>
            </div>

            <div class="col-md-6">
		        <h2>Production Credits</h2>
<?php
    $prod_query = 'select aa.id as artist_id, aa.name as album_artist, '
        . 'a.id as album_id, a.name as title, a.release_date '
        . 'from album_artists as aa, albums as a, producers as p '
        . "where p.producer = $person_id "
        . 'and p.album = a.id '
        . 'and a.album_artist = aa.id '
        . 'order by a.release_date';
    $prod_result = $db->query( $prod_query );
    if( $prod_result->num_rows == 0 ) {
        print "There are no producer credits for $person->first_name $person->last_name in The Album Credits Project.";
    } else {
?>
                <p><?php echo $person->first_name; ?> has produced or co-produced <?php echo $prod_result->num_rows; ?> albums.</p>
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
        while( $prod_row = $prod_result->fetch_object() ) {
?>
                        <tr>
                            <td>
                                <?php echo date( 'Y', strtotime( $prod_row->release_date ) ); ?>
                            </td>
                            <td>
                                <a href="album_artist.php?artist=<?php echo $prod_row->artist_id; ?>">
                                <?php echo $prod_row->album_artist; ?></a>
                            </td>
                            <td>
                                <a href="album.php?album=<?php echo $prod_row->album_id; ?>">
                                <?php echo $prod_row->title ;?>
                            </td>
                        </tr>
<?php
        }
?>
                        </tbody>
                    </table>
<?php
    }
?>
	            </div>
	        </div>

            <div class="row">
                <div class="col-md-6">
                    <h2>Collaborations</h2>
                    <?php echo $person->first_name; ?> has recorded with:
                    <div id="collab"></div>
                </div>
            </div>
            <script type="text/javascript">
                $(function(){
                    var person_id = <?php echo $person_id; ?>;
                    $.get( 'recorded_with.php',
                        { person: person_id },
                        function(data){
                            $('div#collab').html(data);
                        }
                    )
                })
                </script><?php
}
require_once( './footer.inc' )
?>
