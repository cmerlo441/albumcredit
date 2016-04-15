<?php

function array_debug( $array ) {
    print "<pre>";
    print_r( $array );
    print "</pre>\n";
}

function name( $person ) {
    $name = ucwords( $person[ 'first' ] ) . ' ';
    if( $person[ 'middle' ] != '' ) {
        $name .= ucwords( $person[ 'middle' ] );
    }
    if( strlen( $person[ 'middle' ] ) == 1 )
	$name .= '.';
    $name .= ' ' . ucwords( $person[ 'last' ] );
    return $name;
}

$home = "/";

require_once( ".htpasswd" );

// Just for now
$user = 1;

// DB connection has been made.  Let's get some initial info:

if( ! isset( $no_header ) ) {
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <meta http-equiv="pragma" content="no-cache">
    <link rel="icon" href="favicon.ico">

    <script src="//code.jquery.com/jquery-1.11.2.min.js"></script>
    <script src="//code.jquery.com/jquery-migrate-1.2.1.min.js"></script>

    <!-- Log in with Facebook -->
    <script src="includes/facebooksdk.js"></script>

    <!-- Select2 -->
    <link href="<?php echo $home;?>plugins/select2-3.5.2/select2.css" rel="stylesheet" />
    <link href="<?php echo $home;?>plugins/select2-3.5.2/select2-bootstrap.css" rel="stylesheet" />
    <script src="<?php echo $home;?>plugins/select2-3.5.2/select2.js"></script>
    <script src="https://apis.google.com/js/client:platform.js" async defer></script>

    <!-- Bootstrap Notify -->
    <link href="<?php echo $home;?>plugins/bootstrap-notify-master/css/bootstrap-notify.css" rel="stylesheet" />
    <link href="<?php echo $home;?>plugins/bootstrap-notify-master/css/styles/alert-bangtidy.css" rel="stylesheet" />
    <link href="<?php echo $home;?>plugins/bootstrap-notify-master/css/styles/alert-blackgloss.css" rel="stylesheet" />
    <script src="<?php echo $home;?>plugins/bootstrap-notify-master/js/bootstrap-notify.js"></script>

    <title>The Album Credit Project</title>

	 <!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css">

	<!-- Optional theme -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap-theme.min.css">

	<!-- Latest compiled and minified JavaScript -->
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>

  </head>

  <body>

    <nav class="navbar navbar-inverse navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="/">The Album Credit Project</a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
          <form class="navbar-form navbar-right">
            <div class="form-group">
              <input type="text" placeholder="Email" class="form-control">
            </div>
            <div class="form-group">
              <input type="password" placeholder="Password" class="form-control">
            </div>
            <button type="submit" class="btn btn-success">Sign in</button>
          </form>
        </div><!--/.navbar-collapse -->
      </div>
    </nav>

    <br /><br />
<?php
}
?>
