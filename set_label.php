<?php

$no_header = 1;
require_once( './header.inc' );

if( isset( $user ) ) {
    $album = $db->real_escape_string( $_REQUEST[ 'album' ] );
    $label = $db->real_escape_string( $_REQUEST[ 'label' ] );
    $catalog = $db->real_escape_string( $_REQUEST[ 'catalog' ] );

    $query = 'update albums '
        . "set label = \"$label\", catalog = \"$catalog\" "
        . "where id = \"$album\"";
    $result = $db->query( $query );

    $query = 'select a.name as title, a.catalog, l.short_name as label, l.id as label_id '
        . 'from albums as a, labels as l '
        . 'where a.label = l.id '
        . "and a.id = $album";
    //rint "<pre>$query</pre>";
    $result = $db->query( $query );
    $album = $result->fetch_object();
    if( $album->label_id != '' && $album->label_id != 0 ) {
        print "<a href=\"label.php?label=$album->label_id\">$album->label</a> $album->catalog";
    } else {
        print "We don't know on what label <i>$album->title</i> was released.";
    }
}