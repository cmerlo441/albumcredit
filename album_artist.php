<?php

require_once( './header.inc' );

$id = $db->real_escape_string( $_REQUEST[ 'artist' ] );

$artist_query = 'select name '
	. 'from album_artists '
	. "where id = \"$id\"";
$artist_result = $db->query( $artist_query );
if( $artist_result->num_rows == 1 ) {
	$artist = $artist_result->fetch_object();
	
	$albums_query = 'select id, name, release_date from albums '
		. "where album_artist = \"$id\" "
		. 'order by release_date';
	$albums_result = $db->query( $albums_query );
?>
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<h1><?php echo "$artist->name"; ?></h1>
			</div>
		</div>
		
		<div class="row">
			<div class="col-md-12">
				The Album Credits Project has information about
				<?php echo "$albums_result->num_rows $artist->name"; ?> 
				<?php echo ( $albums_result->num_rows == 1 ? 'album' : 'albums' )?>.
			</div>
		</div>
		
		<div class="row">
			<div class="col-md-6">
				<h2>Albums <small>
                                    <?php echo "$albums_result->num_rows "; ?></small>
                                </h2>
				<div id="albums"></div>
			</div>
			<div class="col-md-6">
				<h2>Musicians</h2>
				<div id="musicians"></div>
			</div>
		</div>

        <script type="text/javascript">
        $(function(){
            var artist = <?php echo $id; ?>;

            $.get( 'artist_albums.php',
                { artist: artist },
                function(data){
                    $('div#albums').html(data);
                }
            )
            
            $.get( 'artist_musicians.php',
                { artist: artist },
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
					    <p>Because you're logged in, you can edit information and add albums.</p>
					</div>
					
					<div class="panel-body">
					    <div class="row">
					        <div class="col-md-12">
					            <p>Change <?php echo $artist->name; ?>'s sort order:</p>
					            <form class="form-inline">
					                <div class="form-group">
					                    <label for="sort">Sort <?php echo $artist->name; ?> by:</label>
					                    <input type="text" class="form-control" id="sort" placeholder="Sort <?php echo $artist->name; ?>">
					                </div>
					                <a href="javascript:void(0)" id="addSort" type="submit" class="btn btn-default">Sort</a>
					            </form>
					        </div>
					    </div>
        				<div class="row">
					        <div class="col-md-12">
					            <p id="sort-good" class="bg-success" style="display: none">Sort order has been added.</p>
					            <p id="sort-bad" class="bg-danger" style="display: none">Sort order couldn't be added.</p>
					        </div>
					    </div>
        				<div class="row">
        					<div class="col-md-12">
        					    <p>Add a new album here:</p>
        						<form class="form-inline">
        							<div class="form-group">
        								<label for="title">New Album Title:</label>
        								<input type="text" class="form-control" id="title" placeholder="Album Title">
        							</div>
                                    <br />
        							<div class="form-group">
        								<label for="year">Year:</label>
        								<input type="text" class="form-control" id="year" placeholder="Year">
        							</div>
                                    <div class="form-group">
                                        <label for="month">Month:</label>
                                        <input type="text" class="form-control" id="month" placeholder="Month">
                                    </div>
                                    <div class="form-group">
                                        <label for="date">Date:</label>
                                        <input type="text" class="form-control" id="date" placeholder="Date">
                                    </div>
        							<a href="javascript:void(0)" id="addAlbum" type="submit" class="btn btn-default">Add This Album</a>
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
    var artist = <?php echo $id; ?>;
    
    $('a#addSort').click(function(){
        var sort = $('input#sort').val();
        $.post( 'user/sort_artist.php',
            { artist: artist, sort: sort },
            function(data){
                if( data > 0 ) {
                    $('p#sort-good').slideDown();
                    setTimeout(function() { $("p#sort-good").slideUp(); }, 5000);
                } else {
                    $('p#sort-bad').slideDown();
                    setTimeout(function() { $("p#sort-bad").slideUp(); }, 5000);
                }
            }
        )
    })
    
    $('a#addAlbum').click(function(){
		var album = $('input#title').val();
		var date = $('input#date').val();
		$.post( 'user/add_album.php',
			{ band: artist, album: album, date: date },
			function(){
			    $.get( 'artist_albums.php',
		    		{ artist: artist },
		        	function(data){
		    	    	$('div#albums').html(data);
		        	}
		        )
		    }
		)
	})
})
    	</script>
<?php 
	}
}

require_once( './footer.inc' )
?>
