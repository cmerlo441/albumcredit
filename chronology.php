<?php

require_once( './header.inc' );
require_once( './Event.php' );

$events = array();
$months = array( '', 'January', 'February', 'March', 'April', 'May', 'June',
            'July', 'August', 'September', 'October', 'November', 'December' );

function compareDates( $a, $b ) {
    return $a->getSortableDateString() < $b->getSortableDateString() ? -1 : 1;
}

$albums_query = 'select aa.id as artist_id, aa.name as artist, aa.sort, '
    . 'a.id as album_id, a.name as title, a.release_year as y, '
    . 'a.release_month as m, a.release_day as d, '
    . 'l.short_name as label '
    . 'from album_artists as aa, albums as a, labels as l '
    . 'where a.album_artist = aa.id '
    . 'and a.label = l.id';
$albums_result = $db->query( $albums_query );
while( $a = $albums_result->fetch_object() ) {
    $m = "<a href=\"album.php?album=$a->album_id\">$a->title</a> by "
        . "<a href=\"album_artist.php?artist=$a->artist_id\">"
        . "$a->artist</a> was released";
    $e = new Event( $a->y, $a->m, $a->d, $m, "a$a->sort" );
    $events[] = $e;
}

$birth_query = 'select id, first_name as f, last_name as l, birthdate, '
    . 'birthplace '
    . 'from people '
    . 'where birthdate != "0000-00-00"';
$birth_result = $db->query( $birth_query );
while( $b = $birth_result->fetch_object() ) {
    $m = "<a href=\"person.php?person=$b->id\">$b->f $b->l</a> was born";
    preg_match( "/([0-9]+)-([0-9]+)-([0-9]+)/", $b->birthdate, $matches );
    $e = new Event( $matches[ 1 ], $matches[ 2 ], $matches[ 3 ], $m,
                    "p$b->l $b->f" );
    $events[] = $e;
}

$death_query = 'select id, first_name as f, last_name as l, deathdate, '
    . 'deathplace '
    . 'from people '
    . 'where deathdate is not null and deathdate != "0000-00-00"';
$death_result = $db->query( $death_query );
while( $d = $death_result->fetch_object() ) {
    $m = "<a href=\"person.php?person=$d->id\">$d->f $d->l</a> died";
    preg_match( "/([0-9]+)-([0-9]+)-([0-9]+)/", $d->deathdate, $matches );
    $e = new Event( $matches[ 1 ], $matches[ 2 ], $matches[ 3 ], $m,
                    "p$d->l $d->f" );
    $events[] = $e;
}

usort( $events, "compareDates" );

$year = 0;
$month = 0;
$day = 0;

?>
    <div class="row">
      <div class="col-md-10 col-md-offset-1">
        <h1>The Complete Chronology</h1>
        <p>Presented here is a list of all events that the Album Credit Project is tracking - album release dates, birthdates, and dates of death - in chronological order.</p>
<?php

$first_year = true;

foreach( $events as $event ) {

    // Year change?  Close year panel and start new one
    if( $year != $event->getYear() ) {
        $year = $event->getYear();
        $month = 0;
        $date = 0;

        // Only close last month and year panel if there was one
        if( ! $first_year ) {
?>
          </div>  <!-- year's panel body -->
        </div>    <!-- year's panel -->
      </div>      <!-- years column -->
    </div>        <!-- year's row -->
<?php
        }  // if not first year
        $first_year = false;

        // Now open a new one
?>

    <!--  Happy New Year <?php echo $year; ?>! -->

    <div class="row">
      <div class="col-md-12">
        <div class="panel panel-default">
          <div class="panel-heading">
            <h3 class="panel-title"><?php echo $year; ?></h3>
          </div>
          <div class="panel-body">
<?php

    }  // if it's a year change

    // Month change?  Close month panel and start new one

    if( $month != $event->getMonth() ) {
        $day = 0;

        // If a month panel was open, close it
        if( $month != 0 ) {
?>
            </div>  <!--  month's panel body -->
          </div>    <!--  month's panel -->
<?php
        }
        $month = $event->getMonth();
?>
          <div class="panel panel-default">
            <div class="panel-heading">
              <h3 class="panel-title"><?php echo $months[ $month * 1 ]; ?></h3>
            </div>
            <div class="panel-body">
<?php
    }

    // Day change?
    if( $day != $event->getDay() ) {
        $day = $event->getDay();
?>
              <h4><?php if( $month > 0 && $day > 0 )
                            echo date( 'D, M j, Y', 
                                       strtotime( "$year-$month-$day" ) );
                  ?></h4>
<?php
    }

    // Print event
    //print "<pre>$year-$month-$day</pre>\n";
    print "              <div>" . $event->getMessage() . "</div>\n";
    //print "<pre>" . $event->getSortableDateString() . "</pre>\n";
}

print "      </div>\n    </div>\n  </div>\n</div>\n";
require_once ('./footer.inc' );

/*

update albums set release_year = date_format( release_date, '%Y' );
update albums set release_month = date_format( release_date, '%m' );
update albums set release_day = date_format( release_date, '%d' );

*/
?>