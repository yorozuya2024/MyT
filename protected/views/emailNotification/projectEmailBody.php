<?php
/** @var $this EmailNotification */
/** @var $project Project */
/** @var $header string */
/** @var $details string */
/** @var $champion string */
/** @var $associated string */
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
<h2><?php echo Yii::t('app', 'Project.email.assignment.title.{name}', array('{name}' => $project->name)); ?></h2>
<p>
<table bgcolor="#FFFFFF" cellpadding="1" cellspacing="2" width="60%">
  <tr>
    <td style="vertical-align: top" rowspan="2" width="40%"><?php echo $details; ?></td>
    <td width="1%">&nbsp;</td>
  </tr>
</table>
</p>