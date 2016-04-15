<?php

$no_header = 1;
require_once( '../header.inc' );

if( isset( $user ) ) {
	$add_person_query = "select id, record, state, datetime "
		. "from add_person "
		. "where user = \"$user\"";
	$add_person_result = $db->query( $add_person_query );
	if( $add_person_result->num_rows == 0 ) {
		print '<p>None.</p>';
	} else {
		print "<table class=\"table\">\n";
		print "  <thead>\n";
		print "    <tr>\n";
		print "      <th>Person</th>\n";
		print "      <th>Added</th>\n";
		print "    </tr>\n";
		print "  </thead>\n";
		print "  <tbody>\n";
		while( $person = $add_person_result->fetch_object() ) {
			print "    <tr>\n";
			print "      <td><a href=\"../person.php?person=$person->id\">$person->state</a></td>";
			print "<td>" . date( 'j M y g:i a', strtotime( $person->datetime ) )
				. "</td></tr>\n";
		}
		print "</table>\n";
	}
}