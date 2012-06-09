<?php
/*
This makes a form/button to remove clips from the current list.
*/
function form_remove_clip($clip){
  if($clip['cid']==0){
    $return ='    <th id="remove-'.$clip['delta'].'" class="remove heading">Remove</th>'."\n";
  }
  else{
    $return  = '      <td id="remove-'.$clip['delta'].'" class="remove">'."\n";
    $return .= '        <form class="form_remove_clip" name="form_remove_'.$clip['cid'].'" method="post" action="'.SITE_HOME.'">'."\n";
    $return .= '          <input type="hidden" name="cid" value="'.$clip['cid'].'" />'."\n";
    $return .= '          <input type="hidden" name="lid" value="'.$clip['lid'].'" />'."\n";
    $return .= '          <input type="hidden" name="command" value="remove_clip" />'."\n";
    $return .= '          <input type="submit" id="remove_clip-'.$clip['cid'].'" class="remove_clip" value="X">'."\n";
    $return .= '        </form>'."\n";
    $return .= '      </td>'."\n";
  }
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
    $return ='    <th id="'.$command.'-'.$clip['delta'].'" class="'.$command.' heading">'.$shrtcmdtxt.'</th>'."\n";
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
    
    
    
    $return.='        <form class="form_mod_clip '.$command.'" name="form_'.$command.'_'.$clip['delta'].'" method="post" action="'.SITE_HOME.'">'."\n";
    $return.='          <input type="hidden" name="cid" value="'.$clip['cid'].'" />'."\n";
    $return.='          <input type="hidden" name="lid" value="'.$clip['lid'].'" />'."\n";
    $return.='          <input type="hidden" name="command" value="'.$command.'" />'."\n";
    $return.='          <button type="submit" id="'.$command.'-'.$clip['cid'].'" class="move '.$command.'" title="'.$shrtcmdtxt.'">'."\n";
    $return.='            '.$shrtcmd."\n";
    $return.='          </button>'."\n";
    $return.='        </form>'."\n";
    
    
    
    $return.='      </td>'."\n";
  }
  return $return;
}
/*
Makes buttons for the table that have 2 states or the table headers
$type should only contain letters, numbers and spaces
*/
function form_toggle($clip,$type){
  $type_m = str_replace(" ","_",strtolower($type)); // machine readable
  if($clip['delta'] == 0){
    $return ='    <th id="'.$type_m.'-'.$clip['delta'].'" class="'.$type_m.' heading">'.$type.'</th>'."\n";
  }
  else{
    if($type == "Out"){
      $return ='    <td id="'.$type_m.'-'.$clip['delta'].'" class="'.$type_m.'">000</td>'."\n";
    }
    else{
      $return ='    <td class="'.$type_m.'">'."\n";
      $return.='      <button onclick="'.$type_m.'Button(player['.$clip['delta'].'])" id="'.$type_m.'-'.$clip['delta'].'" type="button" class="'.$type_m.' btn btn-inverse">'.$type.'</button>'."\n";
      $return.='    </td>'."\n";
    }
  }
  return $return;
}
/*
  Make the play/stop button
*/
function play_button($clip,$type){
  $type_i='<i class="icon-play"></i>'; // bootstrap icon code
  $type_m = 'play'; // machine readable
  if($clip['delta'] == 0){
    $return ='    <th id="'.$type_m.'-'.$clip['delta'].'" class="'.$type_m.' heading">'.$type.'</th>'."\n";
  }
  else{
    $return ='    <td class="'.$type_m.'">'."\n";
    $return.='      <button onclick="'.$type_m.'Button(player['.$clip['delta'].'])" id="'.$type_m.'-'.$clip['delta'].'" type="button" class="'.$type_m.' btn btn-success">'.$type_i.'</button>'."\n";
    $return.='    </td>'."\n";
  }
  return $return;
}




/*
This is for fields like title and keybind.
$type should only contain letters, numbers and spaces
*/
function form_edit_text($clip,$type){
  $type_m = str_replace(" ","_",strtolower($type)); // machine readable
  if($clip['delta'] == 0){
    $return ='    <th id="'.$type_m.'-'.$clip['delta'].'" class="'.$type_m.' heading">'.$type.'</th>'."\n";
  }
  else{
    $return ='    <td class="'.$type_m.'">'."\n";
    $return.='      <form class="form_edit_text" name="form_edit_text_'.$type_m.'_'.$clip['delta'].'" method="post" action="'.SITE_HOME.'">'."\n";
    $return.='        <input type="hidden" name="command" value="edit_text_'.$type_m.'">'."\n";
    $return.='        <input name="cid" value="'.$clip['cid'].'" type="hidden">'."\n";
    $return.='        <input name="lid" value="'.$clip['lid'].'" type="hidden">'."\n";
    $return.='        <input name="'.$type_m.'" id="'.$type_m.'-'.$clip['delta'].'" class="'.$type_m.'-text" type="text" value="'.$clip[$type_m].'" onfocus="KeyCheckActive = false" onblur="KeyCheckActive = true" required size=25>'."\n";
    $return.='      </form>'."\n";
    $return.='    </td>'."\n";
  }
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
function form_change_clip_list($clip){
  if($clip['delta'] == 0){
    $return ='    <th>Change</th>'."\n";
  }
  else{
    $return ='    <td>'."\n";
    $return.='      <form name="change_clip_list-'.$clip['cid'].'" method="post" action="'.SITE_HOME.'" enctype="multipart/form-data">'."\n";
    $return.='        <input type="hidden" name="command" value="change_clip_list">'."\n";
    $return.='        <input type="hidden" name="cid" value="'.$clip['cid'].'">'."\n";
    $return.='        <input type="hidden" name="oldlid" value="'.$clip['lid'].'">'."\n";
    $return.='        <select name="lid" class="change-list" onchange="this.form.submit()">'."\n";
    $return.='          <option value="">Move to...</option>'."\n";
    $list_ids = db_get_lists();
    foreach($list_ids as $k => $v){
      if($k!=$clip['lid']){
        $return.='    <option value="'.$k.'">'.$v.'</option>'."\n";
      }
    }
    $return.='        </select>'."\n";
    $return.='      </form>'."\n";
    $return.='    </td>'."\n";
  }
  return $return;
}
/*
 * This assembles the HTML table.
 */

function tableHTML($list){
  if($list){
    $return='<table id="player-table">'."\n";
    foreach($list as $delta => $clip){
      /*
      Make sure volume is in the $clip array and set to 1.0
      */
      $clip['volume'] = 1.0;
      $return.='  <tr id="clip-'.$delta.'" class="clip-stopped">'."\n";
      $return.=     form_edit_text($clip,"Title");
      $return.=     form_edit_text($clip,"Keybind");
      $return.=     play_button($clip,"Play");
      $return.=     duration_timer($clip);
      $return.=     form_toggle($clip,"Under");
      $return.=     form_toggle($clip,"Out");
      $return.=     form_toggle($clip,"Loop");
      $return.=     form_toggle($clip,"Segue");
      $return.=     form_mod_clip($clip,"move_up");
      $return.=     form_mod_clip($clip,"move_down");
      $return.=     form_change_clip_list($clip);
      if($list[1]['lid']==1){
        $return.=     form_remove_clip($clip);
      }
      $return.='  </tr>'."\n";
    }
  $return.='</table>'."\n";
  }
  else{
    $return=false;
  }
  return $return;
}

function tableHTML2($list){
  /*
    This defines what columns we want in the table and in what order. There are
    some items that are not part of the $list array and some items we don't want
    to display.
  */
  $columns = array(
              "move_up",
              "move_down",
              "title",
              "keybind",
              "play",
              "duration",
              "under",
              "volume",
              "loop",
              "segue",
              "to_list"
              );
  
  $return='<table id="main-table">'."\n";
  foreach($list as $key => $value){
    // $key is the clip id
    if($key == 0){
      $tag="th";
      $key='head';
    }
    else{
      $tag="td";
    }
    $return.='<tr id="table-row-'.$key.'">'."\n";
    // This is to make the table based on the functionality needed.
    foreach($columns as $ckey => $label){
      $return.='<'.$tag.'  id="'.$label.'-'.$key.'" class="'.$label.'"></'.$tag.'>'."\n";
    }
#    // This is to make the table based on the $list array.
#    foreach($value as $k => $v){
#      
#      $return.='<'.$tag.'  id="'.$k.'-'.$key.'">'.$v.'</'.$tag.'>'."\n";
#    }
    $return.='</tr>'."\n";
  }
  $return.='</table>';
  return $return;
}

?>
