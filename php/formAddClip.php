<form name="form_add_clip" method="post" action="<?php echo SITE_HOME; ?>" enctype="multipart/form-data">
  <input type="hidden" name="command" value="add_clip">
  <input type="hidden" name="list_id" value="<?php echo $list_id; ?>">
  <table border="1">
    <tr>
      <th>Title</th>
      <th>Select a file</th>
      <th>Click</th>
    </tr>
    <tr>
      <td><input type="text" name="title" size="10" id="add_clip_title" required onfocus="KeyCheckActive = false" onblur="KeyCheckActive = true"></td>
      <td>
        <input type="file" name="file" id="add_clip_file" required accept="<?php echo AUDIO_FORMATS; ?>">
        Max: <?php echo $upload_mb; ?>
      </td>
      <td><input type="submit" value="Upload"></td>
    </tr>
  </table>
</form>

