<?php

require_once( './header.inc' );

?>

<div class="container">
	<h1>The Big List of People</h1>

	<p>Contained here are all the people &mdash; musicians, songwriters, producers,
	etc. &mdash; for whom entries exist in The Album Credits Project.</p>

    <p>
        <select id="country" name="country">
        </select>
    </p>

	<table id="people" class="table table-condensed">
	  <thead>
	  	<tr>
	  	  <th><a href="javascript:void(0)" class="sort" id="first">First Name</a></th>
	  	  <th><a href="javascript:void(0)" class="sort" id="last">Last Name</a></th>
	  	  <th><a href="javascript:void(0)" class="sort" id="bdate">Birthdate</a></th>
	  	  <th><a href="javascript:void(0)" class="sort" id="bplace">Birthplace</a></th>
	  	</tr>
	  </thead>

	  <tbody></tbody>
	</table>

	<script type="text/javascript">
$(function(){

    $.get( 'list_countries.php',
        function(data){
            $('select#country').html(data).change(function(){
                var country = $('select#country').val();
                $.get( 'list_people.php',
                    { filter_type: 'country', filter: country },
                    function( data ) {
                        $( 'table#people > tbody' ).html(data);
                    }
                )
            })
        }
    )

	$.get( 'list_people.php',
		function(data){
			$('table#people > tbody').html(data);
		}
	)

	$('a.sort').click(function(){
		var sort = $(this).attr('id');
        var country = $('select#country').val();
        var filter = '';
        var filter_type = '';
        if( country != 0 ) {
            filter_type = 'country';
            filter = country;
        }
		$.get( 'list_people.php',
			{ sort: sort, filter_type: filter_type, filter: filter },
			function(data){
				$('table#people > tbody').html(data);
			}
		)

	})
})
	</script>
