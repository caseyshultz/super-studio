<?php

function save_file($file){
  $return=FALSE;
  if($file['error']>0){
    $GLOBALS['sysmsg'][]="File error: ".$file['error'];
  }
  else{
    if(!copy($file['tmp_name'],AUDIO_PATH.$file['name'])){
      $GLOBALS['sysmsg'][]="File error: ".$file['name']." could not be saved.";
    }
    else{
      $return=TRUE;
      $GLOBALS['sysmsg'][]="File saved: ".AUDIO_PATH.$file['name'];
    }
  }
  return $return;
}



?>
