<?php

require_once( '../header.inc' );

?>

<div class="container">
	<div class="row">

<?php

if( isset( $user ) ) {
	$user_query = 'select * from users '
		. "where id = \"$user\"";
	$user_result = $db->query( $user_query );
	$user = $user_result->fetch_object();
	$user_result->close();

?>

		<div class="col-md-12">
			<h1><?php echo "$user->first_name $user->last_name"; ?></h1>
			<p>This is your user page.</p>
		</div>
	</div>

	<!-- About the User -->
	
	<div class="row">
		<h2>About You</h2>
	</div>
	
	<!-- Add Data -->

	<div class="row">
		<h2>Add Data</h2>
	</div>
	<div class="row">
		<div class="col-md-6 well">
			<h3>Add a Person</h3>
			<form class="form-horizontal">
				<div class="form-group">
					<label for="firstName" class="col-sm-3 control-label">First Name</label>
					<div class="col-sm-9">
						<input type="text" class="form-control" id="firstName" placeholder="First Name">
					</div>
				</div>
				<div class="form-group">
					<label for="lastName" class="col-sm-3 control-label">Last Name</label>
					<div class="col-sm-9">
						<input type="text" class="form-control" id="lastName" placeholder="Last Name">
					</div>
				</div>
				<div class="form-group">
					<label for="birthdate" class="col-sm-3 control-label">Birthdate</label>
					<div class="col-sm-9">
						<input type="text" class="form-control" id="birthdate" placeholder="Birthdate">
					</div>
				</div>
				<div class="form-group">
					<label for="birthplace" class="col-sm-3 control-label">Birthplace</label>
					<div class="col-sm-9">
						<input type="text" class="form-control" id="birthplace" placeholder="Birthplace">
					</div>
				</div>
				<div class="form-group">
					<label for="deathdate" class="col-sm-3 control-label">Date of Death</label>
					<div class="col-sm-9">
						<input type="text" class="form-control" id="deathdate" placeholder="Date of Birth">
					</div>
				</div>
				<div class="form-group">
					<label for="deathplace" class="col-sm-3 control-label">Place of Death</label>
					<div class="col-sm-9">
						<input type="text" class="form-control" id="deathplace" placeholder="Place of Death">
					</div>
				</div>
				<a href="javascript:void(0)" id="addPerson" type="submit" class="btn btn-default">Add This Person</a>
			</form>
			<p style="padding-top: 1em"><a href="javascript:void(0)" id="people_added">See People You've Added</a></p>
		</div>
		<div class="col-md-6 well">
			<h3>Add an Album</h3>
			<form class="form-horizontal">
				<div class="form-group">
					<label for="album_artist" class="col-sm-3 control-label">Band</label>
					<div class="col-sm-9">
						<select class="select2 form-control" id="album_artist" placeholder="Band"></select>
					</div>
				</div>
				<div class="form-group">
					<label for="album" class="col-sm-3 control-label">Album Name</label>
					<div class="col-sm-9">
						<input type="text" class="form-control" id="album" placeholder="Album Name">
					</div>
				</div>
				<div class="form-group">
					<label for="release_date" class="col-sm-3 control-label">Release Date</label>
					<div class="col-sm-9">
						<input type="text" class="form-control" id="release_date" placeholder="Release Date">
					</div>
				</div>
				<a href="javascript:void(0)" id="addAlbum" type="submit" class="btn btn-default">Add This Album</a>
			</div>
	</div>

<?php
} else {
?>
		<div class="col-md-12">
			<h1>No Dice</h1>
		</div>
<?php

}

?>

<script type="text/javascript">
	$(function(){

		/*
		$.get( 'people_youve_added.php',
			function(data){
				$('div#people_added').html(data);
			}
		)
		$.get( 'bands_youve_added.php',
			function(data){
				$('div#bands_added').html(data);
			}
		)
		*/

	    $.get( 'bands_select.php',
	        	function(data){
	    		    $('#album_artist').html(data).select2({
	    			    placeholder: "Band",
	    			    allowClear: true
	    			});
	        	}
	        )
		
		$('a#addPerson').click(function(){
			var first      = $('input#firstName').val();
			var last       = $('input#lastName').val();
			var birthdate  = $('input#birthdate').val();
			var birthplace = $('input#birthplace').val();
			$.post( "add_person.php",
				{ first: first, last: last, birthdate: birthdate, birthplace: birthplace },
				function(data){
					if( data != '' && data != '0' ) {
						$.get( 'people_youve_added.php',
							function(data){
								$('div#people_added').html(data);
							}
						)
					}
				}
			)
		})

		$('a#addAlbum').click(function(){
			var band = $('select#album_artist').val();  // numeric ID
			var album = $('input#album').val();
			var date = $('input#release_date').val();
			$.post( 'add_album.php',
				{ band: band, album: album, date: date }
			)
		})
	})
</script>

<?php

require_once( '../footer.inc' );

?>