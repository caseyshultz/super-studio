<?php $list_ids = db_get_lists(); ?>

<?php
$thisform='<form style="display:inline;margin-right:1em" name="form_select_list" method="post" action="'.SITE_HOME.'" enctype="multipart/form-data">'."\n";
$options='    <option>Change...</option>'."\n";
foreach($list_ids as $k => $v){
  if($k!=$lid){
    $options.='    <option value="'.$k.'">'.$v.'</option>'."\n";
  }
  else{
    $list_name=$v;
    $list_id=$k;
  }
}
$thisform.='  <select name="lid" onchange="this.form.submit()">'."\n";
$thisform.=$options;
$thisform.='  </select>'."\n";
$thisform.='</form>'."\n";


echo $thisform;
?>

<form style="display:inline;margin-right:1em" name="form_create_list" method="post" action="<?php echo SITE_HOME; ?>" enctype="multipart/form-data">
  <input type="hidden" name="command" value="create_list">
  <input type="text" name="list_name" placeholder="Create new list" onfocus="KeyCheckActive = false" onblur="KeyCheckActive = true" required>
</form>

