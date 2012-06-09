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

$listlabel='      <span id="edit-list-name-label">List:</span>'."\n";
$listlabel.='      <form style="display:inline" id="form_edit_text_listname" name="form_edit_text_listname" method="post" action="'.SITE_HOME.'">'."\n";
$listlabel.='        <input type="hidden" name="command" value="edit_text_list_name">'."\n";
$listlabel.='        <input name="lid" value="'.$list_id.'" type="hidden">'."\n";
$listlabel.='        <input name="list_name" class="list_name-text" type="text" value="'.$list_name.'" onfocus="KeyCheckActive = false" onblur="KeyCheckActive = true" required size=25>'."\n";
$listlabel.='      </form>'."\n";

echo $listlabel;
echo $thisform;
?>

<form style="display:inline;margin-right:1em" name="form_create_list" method="post" action="<?php echo SITE_HOME; ?>" enctype="multipart/form-data">
  <input type="hidden" name="command" value="create_list">
  <input type="text" name="list_name" placeholder="Create new list" onfocus="KeyCheckActive = false" onblur="KeyCheckActive = true" required>
</form>

