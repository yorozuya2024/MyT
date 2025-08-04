<?php
/** @var $this EmailNotification */
/** @var $task Task */
/** @var $header string */
/** @var $taskDetails string */
/** @var $creator string */
/** @var $owner string */
?>
<style>
  h2, td, th {
    font-family: Arial;
    font-size: 10pt;
  }
  h2 {
    font-size: 16pt;
    color: #3D617E;
  }
</style>
<h2><?php echo $header; ?></h2>
<p>
<table bgcolor="#FFFFFF" cellpadding="1" cellspacing="2" width="60%">
  <tr>
    <td colspan="3" bgcolor="#6faccf" style="font-size: 11pt; color: #FFFFFF;">&nbsp;<b><?php echo $task->getAttributeLabel('description'); ?></b></td>
  </tr>
  <tr>
    <td colspan="3" style="vertical-align: top">
      <table cellpadding="2"><tr><td style="font-size: 11pt;"><?php echo $task->description; ?></td></tr></table>
    </td>
  </tr>
  <tr><td colspan="3">&nbsp;</td></tr>
<!--    <tr>
      <td bgcolor="#C4DAE2" style="font-size: 11pt;">&nbsp;<b>Details</b></td>
      <td bgcolor="#C4DAE2" style="font-size: 11pt;">&nbsp;<b>Assigned To</b></td>
      <td bgcolor="#C4DAE2" style="font-size: 11pt;">&nbsp;<b>Created By</b></td>
      <td style="font-size: 11pt;"><table width="100%"><tr><td bgcolor="#C4DAE2">&nbsp;<b>Details</b></td></tr></table></td>
      <td style="font-size: 11pt;"><table width="100%"><tr><td bgcolor="#C4DAE2">&nbsp;<b>Assigned To</b></td></tr></table></td>
      <td style="font-size: 11pt;"><table width="100%"><tr><td bgcolor="#C4DAE2">&nbsp;<b>Created By</b></td></tr></table></td>
  </tr>-->
  <tr>
    <td style="vertical-align: top" rowspan="2" width="40%"><?php echo $taskDetails; ?></td>
    <td style="vertical-align: top" width="59%"><?php echo $owner; ?></td>
    <td width="1%">&nbsp;</td>
  </tr>
  <tr>
    <td style="vertical-align: bottom"><?php echo $creator; ?></td>
    <td>&nbsp;</td>
  </tr>
  <tr><td colspan="3"><hr /></td></tr>
</table>
</p>