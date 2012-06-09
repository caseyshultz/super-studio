<?php
ini_set('display_errors', 1);
ini_set('error_reporting', E_ALL);
require_once('php/config.php');
?>
<html>
<head>
<title>StudioPlayer</title>
<link rel="stylesheet" type="text/css" href="css/bootstrap/css/bootstrap.css" />
<link rel="stylesheet" type="text/css" href="css/style.css" />
<script type="text/javascript" src="js/players.js"></script>
<link rel="shortcut icon" href="favicon.ico">
</head>
<body>
<div id="fullscreen"><strong>F11</strong> (FullScreen On/Off)</div>
<div id="container">
<h1><a href="<?php echo SITE_HOME; ?>">StudioPlayer</a></h1>
<?php include('php/formSelectList.php'); ?>
<?php echo tableHTML($list); ?>
<?php include('php/formAddClip.php'); ?>
<script type="text/javascript">playerControl(<?php echo $lid; ?>);</script>
<script type="text/javascript">//dumpList(<?php echo $lid; ?>);</script>

<?php echo tableHTML2($list); ?>

</div><!--container-->

<pre id="todo">
TODO:
Make all items part of the same form
Make sysmsg toggleable
Replace table structure with modern HTML5 tags
</pre>
<?php //sysmsg(); ?>
<div><?php //echo '$POST:'; ?><pre>
<?php //var_dump($_POST); ?>
</pre></div>
<div><?php //echo '$list:'; ?><pre>
<?php //var_dump($list); ?>
</pre></div>
</body>
</html>

