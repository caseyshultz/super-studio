<?php
/*
This makes a form/button to remove clips from the current list.
*/
function form_remove_clip($clip){
  $return = '        <form class="form_remove_clip" name="form_remove_'.$clip['cid'].'" method="post" action="'.SITE_HOME.'">'."\n";
  $return .= '          <input type="hidden" name="cid" value="'.$clip['cid'].'" />'."\n";
  $return .= '          <input type="hidden" name="lid" value="'.$clip['lid'].'" />'."\n";
  $return .= '          <input type="hidden" name="command" value="remove_clip" />'."\n";
  $return .= '          <input type="submit" id="remove_clip-'.$clip['cid'].'" class="remove_clip" value="X">'."\n";
  $return .= '        </form>'."\n";
    
  return $return;
}
/*
Makes a multi-purpose checkbox
*/
function form_check_box($clip){
  if($clip['cid']==0){
    $return ='    <th id="check-'.$clip['delta'].'" class="check heading">Sel</th>'."\n";
  }
  else{
    $return ='      <td id="check-'.$clip['delta'].'" class="check">'."\n";
    $return.='        <input type="checkbox" name="cid" value="'.$clip['cid'].'" >'."\n";
    $return.='      </td>'."\n";
  }
  return $return;
}

/*
This makes a form/button to move clips up and down in the current list.
*/
function form_mod_clip($clip,$command="none"){
  $shrtcmdtxt=str_replace("move_","",$command);
  if($clip['cid']==0){
    $return ='    <th id="'.$command.'-'.$clip['delta'].'" class="'.$command.
                  ' heading">'.$shrtcmdtxt.'</th>'."\n";
  }
  else{
    if($command=="move_up"){
      $shrtcmd='<i class="icon-arrow-up"></i>';
      $hint="up";
    }
    elseif($command=="move_down"){
      $shrtcmd='<i class="icon-arrow-down"></i>';
      $hint="dn";
    }
    $return ='      <td id="'.$command.'-'.$clip['delta'].'" class="'.$command.'">'."\n";
    $return.='        <form class="form_mod_clip '.$command.'" name="form_'.
                      $command.'_'.$clip['delta'].'" method="post" action="'.SITE_HOME.'">'."\n";
    $return.='          <input type="hidden" name="cid" value="'.$clip['cid'].'" />'."\n";
    $return.='          <input type="hidden" name="lid" value="'.$clip['lid'].'" />'."\n";
    $return.='          <input type="hidden" name="command" value="'.$command.'" />'."\n";
    $return.='          <button type="submit" id="'.$command.'-'.$clip['cid'].
                        '" class="move-'.$command.'" title="'.$shrtcmdtxt.'">'."\n";
    $return.='            '.$shrtcmd."\n";
    $return.='          </button>'."\n";
    $return.='        </form>'."\n";
    $return.='      </td>'."\n";
  }
  return $return;
}

/*
This is for fields like title and keybind.
$type should only contain letters, numbers and spaces
*/
function form_edit_text($clip,$type){
  $type_m = str_replace(" ","_",strtolower($type)); // machine readable


  $return ='      <form class="form_edit_text" name="form_edit_text_'.$type_m.
                  '_'.$clip['delta'].'" method="post" action="'.SITE_HOME.'">'."\n";
  $return.='        <input type="hidden" name="command" value="edit_text_'.$type_m.'">'."\n";
  $return.='        <input name="cid" value="'.$clip['cid'].'" type="hidden">'."\n";
  $return.='        <input name="lid" value="'.$clip['lid'].'" type="hidden">'."\n";
  $return.='        <input name="'.$type_m.'" id="'.$type_m.'-entry-'.$clip['delta'].
                    '" class="'.$type_m.'-text" type="text" value="'.$clip[$type_m].
                    '" onfocus="KeyCheckActive = false" onblur="KeyCheckActive = true" required size=25>'."\n";
  $return.='      </form>'."\n";


  return $return;
}
/*
This is for duration fields
*/
function duration_timer($clip){
  if($clip['delta'] == 0){
    $return ='    <th id="duration-'.$clip['delta'].'" class="duration">duration</th>'."\n";
  }
  else{
    $return ='    <td id="duration-'.$clip['delta'].'" class="duration">'.$clip['duration']."\n";
  }
  return $return;
}

/*
Pull-down list to move clip to a new list
*/
function form_send_clip_to_list($clip){
  if($clip['delta'] == 0){
    $return =''."\n";
  }
  else{
    
    $return ='      <form name="change_clip_list-'.$clip['delta'].'" method="post" action="'.SITE_HOME.'" enctype="multipart/form-data">'."\n";
    $return.='        <input type="hidden" name="command" value="change_clip_list">'."\n";
    $return.='        <input type="hidden" name="cid" value="'.$clip['cid'].'">'."\n";
    $return.='        <input type="hidden" name="oldlid" value="'.$clip['lid'].'">'."\n";
    $return.='        <select id="send_to_options-'.$clip['delta'].'" name="lid" class="change-list" onchange="this.form.submit()">'."\n";
    $return.='          <option value="">Move to...</option>'."\n";
    $list_ids = db_get_lists();
    foreach($list_ids as $k => $v){
      if($k!=$clip['lid']){
        $return.='    <option value="'.$k.'">'.$v.'</option>'."\n";
      }
    }
    $return.='        </select>'."\n";
    $return.='      </form>'."\n";
  }
  return $return;
}


/*
This makes a form/button to move clips up and down in the current list. It will
not return a form to move the first item up or the last item down.
*/
function form_delta_change($clip,$command){
  
  $return="";
  if( !($clip['delta'] == "1" && $command == "up")
      &&
      !($clip['delta'] > $clip['next'] && $command == "down")
    ){
    $return.='        <form class="delta_change '.$command.'" name="form_'.$command.'_'.$clip['delta'].'" method="post" action="'.SITE_HOME.'">'."\n";
    $return.='          <input type="hidden" name="cid" value="'.$clip['cid'].'" />'."\n";
    $return.='          <input type="hidden" name="lid" value="'.$clip['lid'].'" />'."\n";
    $return.='          <input type="hidden" name="command" value="move_'.$command.'" />'."\n";
    $return.='          <button type="submit" id="move-'.$command.'-'.$clip['delta'].'" class="move-'.$command.' btn" title="'.$command.'">'."\n";
    $return.='            <i class="icon-arrow-'.$command.'"></i>'."\n";
    $return.='          </button>'."\n";
    $return.='        </form>'."\n";
  }
  return $return;
}

/*
  Make the play/stop button
*/
function button_play_stop($cid,$file){
  $return=  '<button id="play-button-'.$cid.
            '" type="button" class="play btn btn-success"title="'.$file.'"><i class="icon-play"></i></button>';
  return $return;
}
function button_volume_under($cid){
  $return=  '<button id="under-button-'.$cid.
            '" type="button" class="under btn disabled">Under</button>';
  return $return;
}
function button_loop($cid){
  $return=  '<button id="loop-button-'.$cid.
            '" type="button" class="loop btn btn-inverse"><i class="icon-repeat icon-white"></i></button>';
  return $return;
}
function button_segue($cid){
  $return=  '<button id="segue-button-'.$cid.
            '" type="button" class="segue btn btn-inverse"><i class="icon-play-circle icon-white"></i></button>';
  return $return;
}
/*
  Make the volume display
*/
function display_volume(){
  $return= '<meter value="000" min="000" max="100" low="090">000</meter>';
  return $return;
}
function makePlayerTable(&$list,$columns){
  /*
    This defines what columns we want in the table and in what order. There are
    some items that are not part of the $list array and some items we don't want
    to display.
  */
  if(!isset($list) || $list===FALSE || !isset($columns)){
    $return = '<div class="alert">There is nothing in the list.</div>'."\n";
  }
  else{
    $return='<table id="'.$list[0]['alias'].'-table" class="player-table">'."\n";
    foreach($list as $key => $value){
      // $key is the delta
      if($key == 0){
        $tag="th";
        $return.='  <tr id="'.$list[0]['alias'].'-'.$key.'-row" class="table-head">'."\n";
      }
      else{
        $tag="td";
        $return.='  <tr id="row-'.$key.'" class="clip-stopped">'."\n";
      }
      /*
        This is to make the table based on the functionality needed.
        $ckey is the machine name of the column.
      */
      foreach($columns as $ckey => $label){
        //$ckey is the cell type
        if($key == 0){
        
          if( !($list[$key]['lid']==1) && $ckey=="delete"){
            $return.='';
          }
          else{
            $return.='    <'.$tag.'  id="'.$list[0]['alias'].'-'.$ckey.'-'.$key.'" class="'.$ckey.'">'.$label.'</'.$tag.'>'."\n";
          }
        }
        else{
          if( !($list[$key]['lid']==1) && $ckey=="delete"){
            $return.='';
          }
          else{
            $return.='    <'.$tag.'  id="'.$ckey.'-'.$key.
                          '" class="'.$ckey.'">';
            switch($ckey){
              case "delta_up":
                $return .= form_delta_change($list[$key],"up");
                break;
              case "delta_down":
                $return .= form_delta_change($list[$key],"down");
                break;
              case "title":
                $return .= form_edit_text($list[$key],"Title");
                break;
              case "keybind":
                $return .= form_edit_text($list[$key],"Keybind");
                break;
              case "play":
                $return .= button_play_stop($key,$list[$key]['file']);
                break;
              case "under":
                $return .= button_volume_under($key);
                break;
              case "loop":
                $return .= button_loop($key);
                break;
              case "segue":
                $return .= button_segue($key);
                break;
              case "send_to_list":
                $return .= form_send_clip_to_list($list[$key]);
                break;
              case "delete":
                if($list[$key]['lid']==1){
                  $return .= form_remove_clip($list[$key]);
                }
                break;
              case "duration":
                
                break;
              case "volume":
                
                break;
              default:
                $return .= "??";
            }
          }
        
          $return.='</'.$tag.'>'."\n";
        }
      }
      $return.='  </tr>'."\n";
    }
    $return.='</table>'."\n";
  }
  echo $return;
}

?>
