<?php

$no_header = 1;
require_once( '../header.inc' );

if( isset( $user ) ) {
	$add_band_query = "select id, record, state, datetime "
		. "from add_band "
		. "where user = \"$user\"";
	$add_band_result = $db->query( $add_band_query );
	if( $add_band_result->num_rows == 0 ) {
		print '<p>None.</p>';
	} else {
		print "<table class=\"table\">\n";
		print "  <thead>\n";
		print "    <tr>\n";
		print "      <th>Band</th>\n";
		print "      <th>Added</th>\n";
		print "    </tr>\n";
		print "  </thead>\n";
		print "  <tbody>\n";
		while( $band = $add_band_result->fetch_object() ) {
			print "    <tr>\n";
			print "      <td><a href=\"../band.php?band=$band->id\">$band->state</a></td>";
			print "<td>" . date( 'j M y g:i a', strtotime( $band->datetime ) )
				. "</td></tr>\n";
		}
		print "</table>\n";
	}
}