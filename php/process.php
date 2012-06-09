<?php


if(isset($_POST['lid'])){
  $lid = intval($_POST['lid']);
}
else{
  $lid = 2;
}


if(!empty($_FILES)){
  include_once('files.php');
  include_once('getid3/getid3.php');
  foreach($_FILES as $file){
    if(save_file($file)){
      
      $getID3 = new getID3;
      $tags = $getID3->analyze(AUDIO_PATH.$file['name']);
      
      $clip['clip']['file']=$file['name'];
      $clip['clip']['type']=$file['type'];
      $clip['clip']['size']=$file['size'];
      $clip['clip']['duration']=round($tags['playtime_seconds']);
      
      if(isset($_POST['title'])){
        $clip['clip']['title']=$_POST['title'];
      }
      if(isset($_POST['keybind'])){
        $keybind=$_POST['keybind'];
      }
      else{
        $keybind="";
      }
      if(db_insert($clip)){
        $cid = mysql_insert_id();
      }
      $delta = db_new_assign($cid,$keybind,$lid);
    }
  }
}
if(isset($_POST['command'])){
  
  switch($_POST['command']){
  
    case "move_down":
      db_shift_delta($_POST['lid'],$_POST['cid'],$_POST['command']);
      break;
      
    case "move_up":
      db_shift_delta($_POST['lid'],$_POST['cid'],$_POST['command']);
      break;
  
    case "edit_text_title":
      db_update_title($_POST['cid'],$_POST['title']);
      break;
  
    case "edit_text_keybind":
      if($_POST['cid'] != 0){
        $keybind=substr(trim($_POST['keybind']),0,1);
        db_update_keybind($_POST['cid'],$_POST['lid'],$keybind);
      }
      break;
  
    case "create_list":
        db_new_list($_POST['list_name']);
      break;
      
    case "remove_clip":
      db_remove_clip($_POST['cid'],$_POST['lid']);
      break;
    case "change_clip_list":
      db_change_list($_POST['cid'],$_POST['oldlid'],$_POST['lid']);
      $lid = $_POST['oldlid'];
      break;
    case "edit_text_list_name":
      db_mod_list($_POST['lid'],$_POST['list_name']);
      break;
  }
}
/*
Fetching the list is the last thing to do here.
*/
$list = db_get_list($lid);
?>
