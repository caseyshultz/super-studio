<?php
function sysmsg(){
  $return=FALSE;
  if(!empty($GLOBALS['sysmsg'])){
    echo '<div id="sysmsgs">'."\n";
    echo '  <div id="sysmsglabel">System Messages:</div>'."\n";
    foreach($GLOBALS['sysmsg'] as $msg){
      echo '  <div class="sysmsg"><pre>'.htmlentities($msg).'</pre></div>'."\n";
    }
    echo '</div>'."\n";
    $return=TRUE;
  }
  return $return;
}
?>
