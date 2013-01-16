<?php
/*
Database config
*/
define('DB_HOST',         "localhost"     );
define('DB_USER',         "root"          );
define('DB_PASS',         "q25r70zy"      );
define('DB_NAME',         "sp-dev"  );
/*
Deeper DB config
*/
define('DB_CLIP_TABLE',   "clip"          );
define('DB_LIST_TABLE',   "list"          );
define('DB_ASSIGN_TABLE', "assign"        );
//mysql connect
function db_conn(){
  $return = FALSE;
  $linkid = mysql_connect(DB_HOST,DB_USER,DB_PASS);
  if (!$linkid) {
    $GLOBALS['sysmsg'][]="Could not connect: ".mysql_error();
  }
  $db_selected = mysql_select_db(DB_NAME, $linkid);
  if (!$db_selected) {
    $GLOBALS['sysmsg'][]="Can not use ".DB_NAME.": ".mysql_error();
  }
  else{
    $return = $linkid;
  }
  return $return;
}

function tryQuery($q,$line="0",$function="func_not_defined"){
  $return=mysql_query($q);
  if($return){
    $GLOBALS['sysmsg'][]="Query success [".$function."][line ".$line."]:\n".$q;
  }
  else{
    $GLOBALS['sysmsg'][]="Query failed [".$function."][line ".$line."]:\n".$q." :".mysql_error();
  }
  return $return;
}

/* 
 * Add $array to $table.
 */
function db_insert($array){
  $return=FALSE;
  foreach($array as $table => $data){
    if($data){
      $q = 'INSERT INTO '.$table.' SET ';
      $i=0; // to leave the comma out of the first loop
      foreach($data as $k => $v){
        if($i==0){
          $q .= $k.'="'.$v.'"';
        }
        else{
          $q .= ', '.$k.'="'.$v.'"';
        }
        $i++;
      }
      $return = tryQuery($q,__LINE__,"db_insert");
    }
  }
  return $return;
}



// Get the records
function db_get_list($lid,$movedDelta){
  $return=FALSE;
  if(isset($lid)){
    $q='SELECT  '.DB_LIST_TABLE.'.lid,
                '.DB_LIST_TABLE.'.name,
                '.DB_LIST_TABLE.'.alias,
                '.DB_ASSIGN_TABLE.'.delta,
                '.DB_ASSIGN_TABLE.'.cid,
                '.DB_ASSIGN_TABLE.'.keybind,
                '.DB_CLIP_TABLE.'.type,
                '.DB_CLIP_TABLE.'.file,
                '.DB_CLIP_TABLE.'.duration,
                '.DB_CLIP_TABLE.'.title,
                '.DB_CLIP_TABLE.'.size
    FROM '.DB_LIST_TABLE.', '.DB_ASSIGN_TABLE.', '.DB_CLIP_TABLE.'
    WHERE '.DB_LIST_TABLE.'.lid = '.$lid.' AND
          '.DB_LIST_TABLE.'.lid = '.DB_ASSIGN_TABLE.'.lid AND
          '.DB_ASSIGN_TABLE.'.cid = '.DB_CLIP_TABLE.'.cid
    ORDER BY delta ASC
    ';
    $r=tryQuery($q,__LINE__,"db_get_list");
    if(mysql_num_rows($r) > 0){
      while ($row = mysql_fetch_assoc($r)) {
        $delta = $row['delta'];
        
        foreach($row as $k => $v){
          if($delta == 1){
            $return[0][$k]=$k;  // Makes a zero element for all keys
            if($k == 'lid'){
              $return[0][$k]= $v;
            }
            if($k == 'name'){
              $return[0][$k]= $v;
            }
            if($k == 'alias'){
              $return[0][$k]= $v;
            }
            if($k == 'delta'){
              $return[0][$k]= "0";
            }
            
          }
          $return[$delta][$k]=$v;
        }
      }
      $prev=0;
      foreach($return as $key => $value){
        if($prev<1){
          $first=$key;
        }
        $return[$key]['prev']=$prev;
        $prev=$key;
        $return[$first]['prev']=$key;// Makes the first item refer to the last item
        $previous=$return[$key]['prev'];
        $return[$previous]['next']=$key;
        $return[$key]['next']=$first;// Makes the last item refer to the first item
      }
      $return[0]['next']="next"; // Adds next and prev to the zero element
      $return[0]['prev']="prev"; // so they will have identifyable keys
      if($movedDelta){
        $return[0]['delta']=$movedDelta;
      }
      $xml = db_make_xml($return);
      db_save_xml($lid,$xml);
    }
  }
  return $return;
}
function db_save_xml($lid,$xml){
  $q='UPDATE '.DB_LIST_TABLE.'
      SET xml = "'.addslashes($xml).'"
      WHERE lid = '.$lid.'
      ';
  tryQuery($q,__LINE__,"db_save_xml");
}
function db_make_xml($list){
  $return= '<?xml version="1.0" encoding="ISO-8859-1"?>'."\n";
  $return.= '<list>'."\n";
  foreach($list as $k => $v){
    $return.= '  <clip>'."\n";
    foreach($v as $key => $value){
      if($value==""){
        $value = "n/a";
      }
      $return.= '    <'.$key.'>'.$value.'</'.$key.'>'."\n";
    }
    $return.= '  </clip>'."\n";
  }
  $return.= '</list>'."\n";
  $return.= '<audio_dir>'.AUDIO_DIR.'</audio_dir>'."\n";
  return $return;
}

// Shuffle the delta
function db_shift_delta($lid,$cid,$direction){
  $return=FALSE;
  if($lid && $cid && $direction){
    
    $q='SELECT delta
        FROM '.DB_ASSIGN_TABLE.'
        WHERE lid = '.$lid.' AND cid = '.$cid.'
        ';
    $r=mysql_query($q);
    if(mysql_num_rows($r) > 0){
      while ($row = mysql_fetch_assoc($r)) {
        foreach($row as $k => $v){
          $delta=$v;
        }
      }
    }
    $above=($delta - 1);
    $below=($delta + 1);
    

    $q='SELECT cid, lid, delta, keybind
        FROM '.DB_ASSIGN_TABLE.'
        WHERE lid = '.$lid.'
        AND delta >= '.$above.'
        AND delta <= '.$below.'
        ORDER BY delta ASC
        ';
    $r=mysql_query($q);
    if(mysql_num_rows($r) > 0){
      $i=1;
      while ($row = mysql_fetch_assoc($r)) {
        if($row['delta'] == $above){
          $j = 'above';
        }
        if($row['delta'] == $delta){
          $j = 'delta';
        }
        if($row['delta'] == $below){
          $j = 'below';
        }
        foreach($row as $k => $v){
          if($k=='cid'){
            $nd[$j]['cid']=$v;
          }
          if($k=='lid'){
            $nd[$j]['lid']=$v;
          }
          if($k=='delta'){
            $nd[$j]['delta']=$v;
          }
          if($k=='keybind'){
            $nd[$j]['keybind']=$v;
          }
        }
        $i++;
      }
      /*
      $nd[1] should be above
      $nd[2] should be delta
      $nd[3] should be below
      */
      if($direction == 'move_up' && isset($nd['delta']) && isset($nd['above'])){
        // Delete the row above
        $q0='DELETE FROM '.DB_ASSIGN_TABLE.'
            WHERE lid = '.$nd['above']['lid'].'
            AND delta = '.$nd['above']['delta'].'
            ';
        // Take the delta from the row above
        $q1='UPDATE '.DB_ASSIGN_TABLE.'
            SET
            delta = '.$nd['above']['delta'].'
            WHERE cid = '.$nd['delta']['cid'].' AND lid = '.$nd['delta']['lid'].'
            ';
        $newDelta = $nd['above']['delta'];
        // Make a new row below
        $q2='INSERT INTO '.DB_ASSIGN_TABLE.'
            SET
            cid = '.$nd['above']['cid'].',
            lid = '.$nd['above']['lid'].',
            delta = '.$nd['delta']['delta'].',
            keybind = "'.$nd['above']['keybind'].'"
            ';
      }
      if($direction == 'move_down' && isset($nd['delta']) && isset($nd['below'])){
        // Delete the row below
        $q0='DELETE FROM '.DB_ASSIGN_TABLE.'
            WHERE lid = '.$nd['below']['lid'].'
            AND delta = '.$nd['below']['delta'].'
            ';
        // Take the delta from the row below
        $q1='UPDATE '.DB_ASSIGN_TABLE.'
            SET
            delta = '.$nd['below']['delta'].'
            WHERE cid = '.$nd['delta']['cid'].' AND lid = '.$nd['delta']['lid'].'
            ';
        $newDelta = $nd['below']['delta'];
        // Make a new row above
        $q2='INSERT INTO '.DB_ASSIGN_TABLE.'
            SET
            cid = '.$nd['below']['cid'].',
            lid = '.$nd['below']['lid'].',
            delta = '.$nd['delta']['delta'].',
            keybind = "'.$nd['below']['keybind'].'"
            ';
      }
      if(isset($q0) && isset($q1) && isset($q2)){
        tryQuery($q0,__LINE__,"db_shift_delta");
        tryQuery($q1,__LINE__,"db_shift_delta");
        tryQuery($q2,__LINE__,"db_shift_delta");
        // Return the new delta
        return $newDelta;
      }
      else{
        $GLOBALS['sysmsg'][]="This item cannot be moved.";
      }
    }
    else{
      $GLOBALS['sysmsg'][]="Cannot move clip up: already at top.";
    }
  }
}

function db_clean_deltas($lid){
  $q='SELECT cid, lid, delta, keybind
      FROM '.DB_ASSIGN_TABLE.'
      WHERE lid = '.$lid.'
      ORDER BY delta ASC
      ';
  if($deltas=tryQuery($q,__LINE__,"db_clean_deltas")){
    $i=0;
    while ($row = mysql_fetch_assoc($deltas)) {
      $lists[$i]['cid']=$row['cid'];
      $lists[$i]['lid']=$row['lid'];
      $lists[$i]['delta']=$row['delta'];
      $lists[$i]['keybind']=$row['keybind'];
      $i++;
    }
    if(isset($lists)){
      foreach($lists as $k => $v){
        $q='UPDATE '.DB_ASSIGN_TABLE.'
            SET
            delta = '.($k + 1).'
            WHERE cid = '.$v['cid'].' AND lid = '.$v['lid'].'
            ';
        tryQuery($q,__LINE__,"db_clean_deltas");
      }
    }
  }
}

function db_get_assigned_lists($cid){
  $q='SELECT lid
      FROM '.DB_ASSIGN_TABLE.'
      WHERE cid = '.$cid.'
      ORDER BY lid ASC
      ';
  $r=mysql_query($q);
  if(mysql_num_rows($r) > 0){
    while ($row = mysql_fetch_assoc($r)) {
      $lists[]=$row['lid'];
    }
  }
  return $lists;
}

function db_get_lists(){
  $lists=false;
  $q='SELECT lid, name
      FROM '.DB_LIST_TABLE.'
      ORDER BY lid ASC
      ';
  $r=mysql_query($q);
  if(mysql_num_rows($r) > 0){
    while($row = mysql_fetch_array($r)){
      $i=$row['lid'];
      $lists[$i]=$row['name'];
    }
  }
  return $lists;
}


function db_remove_clip($cid,$lid){
  if($lid != 1){
    // check to see if this clip belongs to any other lists
    $in_lists=db_get_assigned_lists($cid);
    // put the clip in list 1 if it is not there already
    // keybind info will be disregarded
    if(!in_array(1,$in_lists)){
      // Add clip to list 1
      db_new_assign($cid,"",1);
    }

  }
  else{ // list one (Trash) is handled differently that all other lists.
    // if something is in list one it is because it is NOT in any other lists
    // removing a clip from list one deletes it from the database.
    $dq='DELETE FROM '.DB_CLIP_TABLE.'
        WHERE cid = '.$cid.'
        ';
    tryQuery($dq,__LINE__,"db_remove_clip");
  }
  
  // Remove the assignment to this list
  $q='DELETE FROM '.DB_ASSIGN_TABLE.'
      WHERE lid = '.$lid.'
      AND cid = '.$cid.'
      ';
  tryQuery($q,__LINE__,"db_remove_clip");
  if($lid != 1){ // no need to clean list one twice
    db_clean_deltas($lid);
  }
  // Manage list one as we make changes to other lists
  db_clean_deltas(1);
  db_get_list(1);
}
/*
 * Move a clip from one list to another
 */
function db_change_list($cid,$oldlid,$newlid){
  $return=FALSE;
  // Add assignment to the new list
  $new = db_new_assign($cid,"",$newlid);
  $old = db_remove_assign($cid,$oldlid);
  if($new && $old){
    $return=TRUE;
  }
  return $return;
}
/*
 * Add a clip to a list
 */
function db_new_assign($cid,$keybind,$lid){
  $return=FALSE;
  // Get the largest delta in this list that is not this clip


  $q='SELECT delta FROM assign WHERE lid = "'.$lid.'" AND cid <> '.$cid.' ORDER BY delta DESC LIMIT 1';
  
  
  $r=mysql_query($q);
  if(mysql_num_rows($r) > 0){
    while($row2 = mysql_fetch_assoc($r)){
      $delta = ($row2['delta']+1);
    }
  }
  else{
    $delta = 1;
  }
  $array['assign']['cid']=$cid;
  $array['assign']['lid']=$lid;
  $array['assign']['keybind']=$keybind;
  $array['assign']['delta']=$delta;
  
  $return=db_insert($array);
  $clean = db_clean_deltas($lid);
  return $return;
}
/*
 * Remove a clip from a list
 */
function db_remove_assign($cid,$lid){
  $return=FALSE;
  // Remove the assignment to the old list
  $q='DELETE FROM '.DB_ASSIGN_TABLE.'
      WHERE lid = '.$lid.'
      AND cid = '.$cid.'
      ';
  if(tryQuery($q,__LINE__,"db_remove_assign")){
    $clean = db_clean_deltas($lid);
    $return=TRUE;
  }
  return $return;
}

function db_update_keybind($cid,$lid,$value){
  $q='UPDATE '.DB_ASSIGN_TABLE.'
      SET
      keybind = "'.$value.'"
      WHERE cid = '.$cid.'
      AND lid = '.$lid.'
      ';
  tryQuery($q,__LINE__,"db_update_keybind");
}

function db_update_title($cid,$value){
  $q='UPDATE '.DB_CLIP_TABLE.'
      SET
      title = "'.$value.'"
      WHERE cid = '.$cid.'
      ';
  tryQuery($q,__LINE__,"db_update_title");
}
//////////////////////////////////////////////////////////////////////////////
function db_new_list($name){
  $q='INSERT INTO '.DB_LIST_TABLE.'
      SET
      name = "'.trim($name).'",
      alias = "'.str_replace(" ","-",strtolower(trim($name))).'"
      ';
  tryQuery($q,__LINE__,"db_new_list");
}
function db_mod_list($lid,$name){
  $q='UPDATE '.DB_LIST_TABLE.'
      SET
      name = "'.trim($name).'"
      WHERE lid = "'.$lid.'"
      ';
  tryQuery($q,__LINE__,"db_mod_list");
}
// Get the list_id for the list labeled static
function db_get_static(){
  $q='SELECT lid
      FROM '.DB_LIST_TABLE.'
      WHERE static = 1
      LIMIT 1
      ';
  $r=mysql_query($q);
  if(mysql_num_rows($r) == 1){
    while ($row = mysql_fetch_assoc($r)) {
      $return=$row['lid'];
    }
  }
  return $return;
}

/*
This starts up the DB
*/
if(!db_conn()){
  $GLOBALS['sysmsg'][]="Cannot connect to DB.";
}
?>
