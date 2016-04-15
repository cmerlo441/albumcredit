<?php
$headers = 0;
require_once( './header.inc' );

if( isset( $user ) ) {
    $user_query = "select first_name, last_name from users where id = \"$user\"";
    $user_result = $db->query( $user_query );
    $user_row = $user_result->fetch_object();
    $user_result->close();

    print "<h2>$user_row->first_name $user_row->last_name</h2>\n";
    print "<p>You are logged in!</p>\n";
    print "<p><a class=\"btn btn-success\" role=\"button\" href=\"/user/\">Visit your user page &raquo;</a></p>\n";
} else {
    print "<h2>Users</h2>\n";
    $users_query = "select count(id) as count from users";
    $users_result = $db->query( $users_query );
    $users_row = $users_result->fetch_object();
    $users_result->close();
?>
          <p>There are <?php echo $users_row->count; ?> registered users of the site.</p>
          <p><a class="btn btn-default" href="#" role="button">Join now! &raquo;</a></p>
<?php
}
?>