<?php
require_once('../php/mysql.php');
if($_GET){
  $lid = $_GET['id'];
  $q='SELECT xml
      FROM '.DB_LIST_TABLE.'
      WHERE lid = '.$lid.'
      LIMIT 1
      ';
  $r=mysql_query($q);
  while ($row = mysql_fetch_assoc($r)) {
    echo $row['xml'];
  }
}
?>
