<?php
require_once( 'header.inc' );

$artwork_count_query = 'select count(id) as c from album_art';
$artwork_count_result = $db->query( $artwork_count_query );
$artwork_count_row = $artwork_count_result->fetch_object();
$artwork_count = $artwork_count_row->c;
$artwork_count_result->close();

$artwork_query = 'select art.id as id, art.url, aa.id as artist_id, aa.name as band, '
    . 'a.id as album_id, a.name as title, a.release_date '
    . 'from album_art as art, album_artists as aa, albums as a '
    . "where art.album = a.id " 
    . 'and a.album_artist = aa.id '
    . "limit " . rand( 0, $artwork_count - 1 ) . ', 1';
$artwork_result = $db->query( $artwork_query );
$artwork_row = $artwork_result->fetch_object();
$artwork_result->close();
$url = $artwork_row->url;
$tooltip = "$artwork_row->band &quot;$artwork_row->title&quot; ("
    . date( 'Y', strtotime( $artwork_row->release_date) ) . ")";
?>

    <!-- Main jumbotron for a primary marketing message or call to action -->
    <div class="jumbotron">
      <div class="container">
        <div class="row">
          <div class="col-md-8">
            <h1>The Album Credit Project</h1>
            <p>Who played on that album?  Did these two musicians ever collaborate?  Find out here.</p>
            <p><a class="btn btn-primary btn-lg" href="#" role="button">Learn more &raquo;</a></p>
          </div>
          <div class="col-md-4 hidden-xs hidden-sm text-center">
            <a href="album.php?album=<?php echo $artwork_row->album_id; ?>">
              <img class="img-responsive img-rounded" alt='Album Cover of "<?php echo $tooltip; ?>' title="<?php echo $tooltip; ?>" src="<?php echo $url; ?>">
            </a>
          </div>
        </div>
      </div>
    </div>

    <div class="container">
      <!-- Example row of columns -->
      <div class="row">
        <div class="col-md-7">
          <h2>Search and Filter</h2>
          <p>Type in the name of a person, or a band, or an album here.</p>
          <!-- <p><input type="text" id="search" class="form-control" placeholder="Search"></p> -->
          <!-- <p><input id="search" class="form-control" /></p> -->
  	  	  <select id="search" class="select2 form-control"></select>
          <p>Recent searches: ...</p>

          <p>Or <a href="instruments.php">search by instrument</a>.</p>
        </div>
        <div class="col-md-5">
          <h2>Statistics</h2>
<?php
$people_query = "select count(id) as count from people";
$people_result = $db->query( $people_query );
$people_row = $people_result->fetch_object();
$people_result->close();

$recent_people_query = "select id, first_name, last_name "
  . "from people "
  . "order by id desc limit 3";
$recent_people_result = $db->query( $recent_people_query );
$recent_people = array();
$count = 0;
while( $row = $recent_people_result->fetch_object() ) {
  $recent_people[ $count ][ 'name' ] = "$row->first_name $row->last_name";
  $recent_people[ $count ][ 'id' ]   = $row->id;
  ++$count;
}
$recent_people_result->close();

$num_bands_query = "select count(id) as count from album_artists";
$num_bands_result = $db->query( $num_bands_query );
$num_bands_row = $num_bands_result->fetch_object();
$num_bands_result->close();

$list_bands_query = 'select id, name from album_artists '
  . 'order by name';
$list_bands_result = $db->query( $list_bands_query );
$bands = array();
$count = 0;
while( $band = $list_bands_result->fetch_object() ) {
  $bands[ $count ][ 'name' ] = $band->name;
  $bands[ $count ][ 'id' ]   = $band->id;
  ++$count;
}
$json = json_encode( $bands );

$recent_bands_query = "select id, name "
  . "from album_artists "
  . "order by id desc limit 3";
$recent_bands_result = $db->query( $recent_bands_query );
$recent_bands = array();
$count = 0;
while( $row = $recent_bands_result->fetch_object() ) {
  $recent_bands[ $count ][ 'name' ] = "$row->name";
  $recent_bands[ $count ][ 'id' ]   = $row->id;
  ++$count;
}
$recent_bands_result->close();

$num_albums_query = 'select count(id) as count from albums';
$num_albums_result = $db->query( $num_albums_query );
$num_albums_row = $num_albums_result->fetch_object();
$num_albums_result->close();

$recent_albums_query = 'select a.id, aa.name as band, a.name as title, a.release_date as date '
	. 'from album_artists as aa, albums as a '
	. "where a.album_artist = aa.id "
	. "order by a.id desc limit 3";
$recent_albums_result = $db->query( $recent_albums_query );
$recent_albums = array();
$count = 0;
while( $row = $recent_albums_result->fetch_object() ) {
	$recent_albums[ $count ][ 'id' ]    = $row->id;
	$recent_albums[ $count ][ 'band' ]  = $row->band;
	$recent_albums[ $count ][ 'title' ] = $row->title;
	$recent_albums[ $count ][ 'year' ]  = date( 'Y', strtotime( $row->date ) );
	++$count;
}

?>
          <p>
              Right now, The Album Credit Project is tracking:
              
              <p><b><a href="people.php"><?php echo $people_row->count; ?> people</a></b> (musicians, producers, lyricists, etc.)<br />
              Recent additions: 
<?php
for( $i = 0; $i < 3; ++$i ) {
    print "<a href=\"person.php?person={$recent_people[ $i ][ 'id' ]}\">"
        . "{$recent_people[ $i ][ 'name' ]}</a>";
    if( $i < 2 ) {
        print ", ";
    }
}
?>
              </p>
              <p><b><a href="album_artists.php"><?php echo $num_bands_row->count; ?> bands</a></b> and album artists<br />
              Recent additions: 
<?php
for( $i = 0; $i < 3; ++$i ) {
  print "<a href=\"album_artist.php?artist={$recent_bands[ $i ][ 'id' ]}\">"
    . "{$recent_bands[ $i ][ 'name' ]}</a>";
  if( $i < 2 ) {
    print ", ";
  }
}
?>
              </p>
              <p><b><a href="albums.php"><?php echo $num_albums_row->count; ?> albums</a></b><br />
              Recent additions: 
<?php
for( $i = 0; $i < 3; ++$i ) {
  print "<a href=\"album.php?album={$recent_albums[ $i ][ 'id' ]}\">"
    . "{$recent_albums[ $i ][ 'band' ]} <i>{$recent_albums[ $i ][ 'title' ]}</i> "
    . "({$recent_albums[ $i ][ 'year' ]})</a>";
  if( $i < 2 ) {
    print ", ";
  }
}
?>
              </p>
          <p><b><a href="chronology.php">View the complete chronology</a></b> of album releases, births, and deaths.</p>
          </p>
       </div>
<!--        <div class="col-md-4">
           <?php include_once( './third_column.php' ); ?>
       </div>
 -->      </div>
      
      <div class="row">
          <div class="col-md-6">
              <h2>Six Degrees</h2>
              <p>How connected are these two people?</p>
              <p><select class="form-control" id="first"></select>
              <select class="form-control" id="second"></select></p>
              <p><a href="javascript:void(0)" class="btn btn-info" id="collab">Connect</a></p>
              <div class="list-group" id="answer"></div>
          </div>

	  <div class="col-md-6">
<a class="twitter-timeline" href="https://twitter.com/AlbumCredit" data-widget-id="611226542115540992">Tweets by @AlbumCredit</a> <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+"://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
	  </div>
      </div>

<?php
require_once( './footer.inc' );
?>

<script type="text/javascript">
$(function(){
    $.get( 'select.php',
    	function(data){
		    $('#search').html(data).select2({
			    placeholder: "Click here to search",
			    allowClear: true
			}).change(function(){
				var id = $('#search').val();
				var type = id.substring( 0, 1 );
				if( type == 'p' ) {
					window.location = "person.php?person=" + id.substring( 1 );
				} else if( type == 'b' ) {
					window.location = "album_artist.php?artist=" + id.substring( 1 );
				} else if( type == 'a' ) {
					window.location = "album.php?album=" + id.substring( 1 );
				}
			});
    	}
    )

    $.get( 'select_people.php',
	    function(data){
            $('select#first').html(data).select2({
			    placeholder: "Click here to search",
			    allowClear: true
            });
            $('select#second').html(data).select2({
			    placeholder: "Click here to search",
			    allowClear: true
            });
        }
    )

    $('a#collab').click(function(){
        $('div#answer').html('Working...');
        var first = $('select#first').val();
        var second = $('select#second').val();
        $.get( 'table_search.php',
      		{ source: first, target: second },
      		function(data){
          		$('div#answer').html(data);
      		}
        )
    })
    
})
</script>