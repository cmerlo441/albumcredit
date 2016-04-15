<?php

require_once( './header.inc' );

$id = $db->real_escape_string( $_REQUEST[ 'album' ] );

$album_query = 'select aa.id as artist_id, aa.name as artist, '
    . 'a.name as title, a.release_date, '
    . 'a.label as label_id, l.short_name as label, a.catalog '
	. 'from albums as a, album_artists as aa, labels as l '
	. 'where a.album_artist = aa.id '
	. "and a.id = \"$id\" "
    . 'and a.label = l.id';
$album_result = $db->query( $album_query );
$album = $album_result->fetch_object();
$album_result->close();

$prev_album_query = 'select id, name as title, release_date '
	. 'from albums '
	. "where album_artist = $album->artist_id "
	. "and release_date < \"$album->release_date\" "
    . 'order by release_date desc limit 1';
$prev_album_result = $db->query( $prev_album_query );
if( $prev_album_result->num_rows == 1 ) {
    $prev_album_row = $prev_album_result->fetch_object();
    $prev_album = "<a href=\"album.php?album=$prev_album_row->id\">"
        . "$prev_album_row->title</a> ("
        . date( 'Y', strtotime( $prev_album_row->release_date ) ) . ')';
}
$prev_album_result->close();

$next_album_query = 'select id, name as title, release_date '
    . 'from albums '
    . "where album_artist = $album->artist_id "
    . "and release_date > \"$album->release_date\" "
    . 'order by release_date limit 1';
$next_album_result = $db->query( $next_album_query );
if( $next_album_result->num_rows == 1 ) {
    $next_album_row = $next_album_result->fetch_object();
    $next_album = "<a href=\"album.php?album=$next_album_row->id\">"
    . "$next_album_row->title</a> ("
        . date( 'Y', strtotime( $next_album_row->release_date ) ) . ')';
}
$next_album_result->close();

?>
	<div class="container">
	    <div class='notifications bottom-left'></div>
		<div class="row">
			<div class="col-md-8">
<?php
$released = date( 'F jS, Y', strtotime( $album->release_date ) );
if( preg_match( "/00$/", $album->release_date ) )
    $released = date( 'F, Y', strtotime( substr( $album->release_date, 0, 8 ) . "01" ) );
if( preg_match( "/00-00$/", $album->release_date ) )
    $released = date( 'F, Y', strtotime( substr( $album->release_date, 0, 5 ) . "01-01" ) );
?>
				<h1><a href="album_artist.php?artist=<?php echo $album->artist_id; ?>"><?php echo "$album->artist</a><br /><i>$album->title</i>"; ?></h1>
				<h2>Released <?php echo $released; ?><br /><small id="label">
<?php
    if( $album->label_id != '' && $album->label_id != 0 ) {
        print "<a href=\"label.php?label=$album->label_id\">$album->label</a> $album->catalog";
    } else {
        print "We don't know on what label <i>$album->title</i> was released.";
    }
?>
                </small></h2>
		        <h2>Production</h2>
		        <div id="producers"></div>
<?php
if( isset( $prev_album ) or isset( $next_album ) ) {
    print "				<p>";
    if( isset( $prev_album ) )
        print "<span class=\"glyphicon glyphicon-circle-arrow-left\"></span> $prev_album";
    if( isset( $prev_album ) and isset( $next_album ) )
        print " // ";
    if( isset( $next_album ) )
        print "$next_album <span class=\"glyphicon glyphicon-circle-arrow-right\"></span>";
    print "</p>\n";
}
?>				
			</div>
			<div class="col-md-4" id="album_art"></div>
		</div>
		
		<div class="row">
			<div class="col-md-6">
				<h2>Songs</h2>
				<div id="songs"></div>
			</div>
			<div class="col-md-6">
			    <h2>Musicians</h2>
			    <div id="musicians"></div>
			</div>
		</div>

		<script type="text/javascript">
		$(function(){
			var album = <?php echo $id; ?>;

			$.get( 'album_art.php',
				{ album: album },
				function(data){
					$('div#album_art').html(data);
				}	
			)

			$.get( 'album_producers.php',
				{ album: album },
				function(data){
					$('div#producers').html(data);
				}
			)

			$.get( 'songs_on_an_album.php',
				{ album: album },
				function(data){
					$('div#songs').html(data);
				}
			)

			$.get( 'album_musicians.php',
					{ album: album },
					function(data){
						$('div#musicians').html(data);
					}
				)
			})
		</script>
<?php 

if( isset( $user ) ) {
?>
		<div class="row">
			<div class="col-md-1"></div>
			<div class="col-md-10">
				<div class="panel panel-success">
					<div class="panel-heading text-center">
					    <p>Because you're logged in, you can add and edit songs and credits on this album.</p>
					</div>
					
					<div class="panel-body">

                        <div class="row">
                            <div class="col-md-12">
                                <p>Set <i><?php echo $album->title; ?></i>'s label information here:</p>
                                <form class="form form-inline">
                                    <div class="form-group">
                                        <label for="label">Label:</label>
                                        <select class="form-control" id="label" placeholder="album">
                                            <option id="0"<?php if( $album->label_id == 0 ) echo ' selected';?>>Unknown</option>
<?php
$labels_query = 'select id, short_name from labels '
    . 'where id > 0 '
    . 'order by short_name';
$labels_result = $db->query( $labels_query );
while( $label_row = $labels_result->fetch_object() ) {
    print "                                            "
        . "<option id=\"$label_row->id";
    if( $album->label_id == $label_row->id )
        print ' selected';
    print "\">$label_row->short_name</option>\n";
}
?>
                                       </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="cat_no">Catalog Number:</label>
                                        <input type="text" class="form-control" id="cat_no" placeholder="<?php echo $album->catalog;?>">
                                    </div>
                                    <a href="javascript:void(0)" id="updateLabel" type="submit" class="btn btn-default">Update Label Information</a>
                                </form>
                            </div>
                        </div>

                        <hr>

        				<div class="row">
        					<div class="col-md-6">
        					    <p>Add a new song here:</p>
                                <div class="form-inline">
        							<div class="form-group">
        								<label for="song">New Song Title:</label>
        								<input type="text" class="form-control" id="song" placeholder="Add a Song">
        							</div>
        							<a href="javascript:void(0)" id="addSong" type="submit" class="btn btn-default">Add This Song</a>
                                </div>
        					</div>
        					<div class="col-md-6">
        					    <p>Add a URL for album artwork here:</p>
        					    <div class="form-inline">
        					        <div class="form-group">
        					            <label for="url">Album Art URL:</label>
        					            <input type="text" class="form-control" id="url" placeholder="Add Album Artwork">
        					        </div>
        							<a href="javascript:void(0)" id="addURL" type="submit" class="btn btn-default">Add This Artwork</a>
    					        </div>
        					</div>
        				</div>

                        <hr>

                        <div class="row">
                            <div class="col-md-12">
                                <p>Add a new person to the database here:</p>
                                <div class="form-inline">
                                    <div class="form-group">
                                        <label for="firstName">First name:</label>
                                        <input type="text" class="form-control" id="firstName" placeholder="First Name">
                                    </div>
                                    <div class="form-group">
                                        <label for="lastName">Last name:</label>
                                        <input type="text" class="form-control" id="lastName" placeholder="Last Name">
                                    </div>
                                    <a href="javascript:void(0)" id="addPerson" type="submit" class="btn btn-default">Add This Person</a>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="alert alert-success alert-dismissible"
                                     style="display: none" role="alert" id="newPersonAlert"></div>
                            </div>
                        </div>
        				
        				<hr>
        				
        				<div class="row">
        				    <div class="col-md-6">
        				        <p>Add a credit for a musician who played an instrument <i>on the whole album</i> here:</p>
        				        <form class="form">
        				            <div class="form-group">
        				                <label for="album_musician">Musician:</label>
        				                <select class="form-control" id="album_musician" placeholder="Album Musician"></select>
        				            </div>
        				            <div class="form-group">
        				                <label for="album_musician_instrument">Instrument:</label>
        				                <select class="form-control" id="album_musician_instrument" placeholder="Instrument"></select>
        				            </div>
        							<a href="javascript:void(0)" id="addAlbumMusician" type="submit" class="btn btn-default">Add This Credit</a>
    				            </form>
        				    </div>

        				    <div class="col-md-6">
        				        <p>Add a credit for a musician who played an instrument <i>on part of the album</i> here:</p>
        				        <form class="form">
        				            <div class="form-group">
        				                <label for="song_musician">Musician:</label>
        				                <select class="form-control" id="song_musician" placeholder="Musician"></select>
        				            </div>
        				            <div class="form-group">
        				                <label for="song_musician_instrument">Instrument:</label>
        				                <select class="form-control" id="song_musician_instrument" placeholder="Instrument"></select>
        				            </div>
        				            <div class="form-group">
        				                <label for="song_credit">Song:</label>
        				                <select class="form-control" id="song_credit" placeholder="Song"></select>
        				            </div>
        				            <a href="javascript:void(0)" id="addSongMusician" type="submit" class="btn btn-default">Add This Credit</a>
    				            </form>
        				    </div>
    				    </div>

        				<hr>
        				
        				<div class="row">
        				    <div class="col-md-12">
        				        <p>Add a producer credit here:</p>
        				        <form class="form">
        				            <div class="form-group">
        				                <label for="producer">Producer:</label>
        				                <select class="form-control" id="producer" placeholder="Producer"></select>
        				            </div>
        							<a href="javascript:void(0)" id="addAlbumProducer" type="submit" class="btn btn-default">Add This Credit</a>
    				            </form>
        				    </div>
        				</div>

    				</div>
    			</div>
			</div>
			<div class="col-md-1"></div>
		</div>
	
	<script type="text/javascript">
		$(function(){
    		var album = <?php echo $id; ?>;
            $('select#label').select2({
                placeholder: 'Label',
                allowClear: true
            })

            $('a#updateLabel').click(function(){
                var label = $('select#label option:selected').attr('id')
                var catalog = $('input#cat_no').val();
                $.post( 'set_label.php',
                    { album: album, label: label, catalog: catalog },
                    function(data) {
                        $('small#label').html(data);
                    }
                )
            })
    
    		function addSong(){
    			var song = $('input#song').val();
    			$.post( 'user/add_song.php',
    				{ song: song, album: album },
    				function(){
    					$('input#song').val('');
    					$.get( 'songs_on_an_album.php',
    						{ album: album },
    						function(data){
    							$('div#songs').html(data);
    						}
    					)
    					$.post( 'select_songs.php',
    							{ album: album },
    							function(data){
    								$('select#song_credit').html(data).select2({
    			    				    placeholder: "Click here to search",
    			    				    allowClear: true
    			    				})
    							}
    						)
    				    }
    			);
    			$('input#song').focus();
    	    }
    
    		$('a#addSong').click(function(){
    			addSong();
    		})
			
			$('input#song').keydown(function(event){
				if( event.which == 13 ) {
					addSong();
				}
			})

            $('a#addPerson').click(function(){
                var first = $('input#firstName').val();
                var last = $('input#lastName').val();
                $.post( 'user/add_person.php',
                    { first: first, last: last },
                    function() {
                        $('input#firstName').val('');
                        $('input#lastName').val('');
                        $('div#newPersonAlert')
                            .html( '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><strong>' +
                                 first + ' ' + last + '</strong> was added to the database.' )
                            .slideDown().delay(5000).slideUp();
                        $.post( 'select_people.php',
                            function(data){
                                $('select#album_musician').html(data).select2({
                                    placeholder: "Click here to search",
                                    allowClear: true
                                })
                                $('select#song_musician').html(data).select2({
                                    placeholder: "Click here to search",
                                    allowClear: true
                                })
                                $('select#producer').html(data).select2({
                                    placeholder: "Click here to search",
                                    allowClear: true
                                })
                            }
                        )
                    }
                )
            })
			
			$('a#addURL').click(function(){
				var url = $('input#url').val();
				$.post( 'user/add_album_art.php',
			        { album: album, url: url },
			        function(data){
				        $.get( 'album_art.php',
							{ album: album },
							function(data){
								$('div#album_art').html(data);
							}
				        )
			        }
				)
			})
			
			$.post( 'select_people.php',
				function(data){
    			    $('select#album_musician').html(data).select2({
    				    placeholder: "Click here to search",
    				    allowClear: true
    			    })
    			    $('select#song_musician').html(data).select2({
    				    placeholder: "Click here to search",
    				    allowClear: true
    			    })
    			    $('select#producer').html(data).select2({
    				    placeholder: "Click here to search",
    				    allowClear: true
    			    })
			    }
			)

			$.post( 'select_instruments.php',
				function(data){
    			    $('select#album_musician_instrument').html(data).select2({
    				    placeholder: "Click here to search",
    				    allowClear: true
    			    })
    			    $('select#song_musician_instrument').html(data).select2({
    				    placeholder: "Click here to search",
    				    allowClear: true
    			    })
    			}
			)

			$.post( 'select_songs.php',
				{ album: album },
				function(data){
					$('select#song_credit').html(data).select2({
    				    placeholder: "Click here to search",
    				    allowClear: true
    				})
				}
			)
			
			$('a#addAlbumMusician').click(function(){
				var musician = $('select#album_musician').val();
				var instrument = $('select#album_musician_instrument').val();
				$.post( 'user/add_album_musician.php',
					{ album: album, musician: musician, instrument: instrument },
					function(data){
						$.get( 'album_musicians.php',
							{ album: album },
							function(data){
								$('div#musicians').html(data);
								$('select#album_musician_instrument').select2( 'val', '0' ).focus();
							}
						)
					}
				)
				// $('bottom-left').notify({
				// 	message: { text: 'Added' },
				// 	type: 'info',
				// 	fadeOut: {
				// 		 delay: Math.floor( Math.random() * 500 ) + 2500
				// 	}
				// }).show();
			})

			$('a#addSongMusician').click(function(){
				var musician = $('select#song_musician').val();
				var instrument = $('select#song_musician_instrument').val();
				var song = $('select#song_credit').val();
				$.post( 'user/add_song_musician.php',
					{ song: song, musician: musician, instrument: instrument },
					function(data){
						$.get( 'album_musicians.php',
							{ album: album },
							function(data){
								$('div#musicians').html(data);
							}
						)
					}
				)
			})

			$('a#addAlbumProducer').click(function(){
				var producer = $('select#producer').val();
				$.post( 'user/add_producer.php',
					{ album: album, producer: producer },
					function(data){
						$.get( 'album_producers.php',
							{ album: album, producer: producer },
							function(data){
								$('div#producers').html(data);
								$('select#producer').select2( 'val', '0' );
							}
						)
					}
				)
			})

		})
	</script>
<?php 
}

require_once ('./footer.inc' );
?>