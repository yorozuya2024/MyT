<?php
/** @var $this EmailNotification */
/** @var $project Project */
/** @var $title string */
?>
<table width="100%" cellpadding="2" cellspacing="2" border-collapse="separate">
  <?php if ($title !== null): ?>
    <tr>
      <td bgcolor="#6faccf" colspan="2" style="font-size: 11pt; color: #FFFFFF;">&nbsp;<b><?php echo $title; ?></b></td>
    </tr>
  <?php endif; ?>
  <tr bgcolor="#e6eff3">
    <th align="right" nowrap>
      <?php echo $project->getAttributeLabel('name'); ?>&nbsp;
    </th>
    <td nowrap>
      &nbsp;<?php echo CHtml::link($project->name, $this->createAbsoluteUrl('project/view', array('id' => $project->id))); ?>
    </td>
  </tr>
  <tr bgcolor="#e6eff3">
    <th align="right" nowrap>
      <?php echo $project->getAttributeLabel('prefix'); ?>&nbsp;
    </th>
    <td nowrap>
      &nbsp;<?php echo $project->prefix; ?>
    </td>
  </tr>
  <tr bgcolor="#e6eff3">
    <th align="right" nowrap>
      <?php echo $project->getAttributeLabel('client'); ?>&nbsp;
    </th>
    <td nowrap>
      &nbsp;<?php echo $project->client; ?>
    </td>
  </tr>
  <tr bgcolor="#e6eff3">
    <th align="right" nowrap>
      <?php echo $project->getAttributeLabel('champion_id'); ?>&nbsp;
    </th>
    <td nowrap>
      &nbsp;<?php echo $project->champion->username; ?>
    </td>
  </tr>
</table>