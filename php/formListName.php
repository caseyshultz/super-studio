<?php
$listlabel='      <span id="edit-list-name-label">List:</span>'."\n";
$listlabel.='      <form style="display:inline" id="form_edit_text_listname" name="form_edit_text_listname" method="post" action="'.SITE_HOME.'">'."\n";
$listlabel.='        <input type="hidden" name="command" value="edit_text_list_name">'."\n";
$listlabel.='        <input name="lid" value="'.$list[0]['lid'].'" type="hidden">'."\n";
$listlabel.='        <input name="list_name" class="list_name-text" type="text" value="'.$list[0]['name'].'" onfocus="KeyCheckActive = false" onblur="KeyCheckActive = true" required size=25>'."\n";
$listlabel.='      </form>'."\n";

echo $listlabel;
?>

