<?php
ini_set('display_errors', 1);
ini_set('error_reporting', E_ALL);
require_once('php/config.php');

/*
  This array is a master list of function names and their human-readable
  values. A new array can be created for player tables that have different
  uses.
*/
$main_table_columns = array(
            "play"=>"Play/Stop",
            "title"=>"Title",
            "volume"=>"Volume",
            "keybind"=>"Key",
            "duration"=>"Time",
            "under"=>"Under",
            "loop"=>"Loop",
            "segue"=>"Segue",
            "delta_up"=>"Move Up",
            "delta_down"=>"Move Down",
            "send_to_list"=>"Send to list...",
            "delete"=>"DELETE"
            );
?>
<html>
<head>
<title>StudioPlayer-DEV</title>
<link rel="stylesheet" type="text/css" href="css/bootstrap/css/bootstrap.css" />
<link rel="stylesheet" type="text/css" href="css/android1-style.css" />
<link rel="shortcut icon" href="favicon.ico">
</head>
<body>
<div id="fullscreen"><strong>F11</strong> (FullScreen On/Off)</div>
<div id="container">

<?php require_once('header.php'); ?>

<div>
<?php include('php/formSelectList.php'); ?>
</div>

<div>
<?php
makePlayerTableAndroid($list,$main_table_columns);
$json_list = json_encode($list);
?>
</div>

<div>
<?php include('php/formAddClip.php'); ?>
</div>

<?php include('php/debugging/index.php'); ?>

<script type="text/javascript" src="js/players.js"></script>
<script type="text/javascript">loadList(<?php echo $lid; ?>);</script>
<script type="text/javascript">playerControl(<?php echo $lid; ?>);</script>
<script type="text/javascript">//getTableLabels(<?php echo $json_list; ?>);</script>

<?php require_once('footer.php'); ?>

</div><!--container-->

</body>
</html>

