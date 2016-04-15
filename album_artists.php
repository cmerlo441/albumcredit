<?php 

require_once( './header.inc' );

?>
    <div class="container">
        <h1>List of Album Artists</h1>
        <div class="row">
            <div class="col-md-12" id="list">
            </div>
        </div>
        
        <script type="text/javascript">
$(function(){
    $.get( 'list_album_artists.php',
        function(data) {
            $('div#list').html(data);
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
					    <p>Because you're logged in, you can add artists.</p>
					</div>
					
					<div class="panel-body">
					    <div class="row">
					        <div class="col-md-12">
					            <form class="form-inline">
					                <div class="form-group">
					                    <label for="band">New album artist:</label>
					                    <input type="text" class="form-control" id="band" placeholder="New Album Artist">
					                </div>
					                <a href="javascript:void(0)" id="addBand" type="submit" class="btn btn-default">Add Album Artist</a>
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
	$('a#addBand').click(function(){
		var band = $('input#band').val();
		$.post( 'user/add_band.php',
			{ band: band },
			function(data){
				if( data != '' && data != '0' ) {
					$.get( 'list_album_artists.php',
						function(data){
							$('div#list').html(data);
						}
					)
				}
			}
		)
	})

})
    	</script>
<?php 
	}
require_once './footer.inc';

?>
