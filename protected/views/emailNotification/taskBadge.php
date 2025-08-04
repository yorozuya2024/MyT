<?php
/** @var $this EmailNotification */
/** @var $task Task */
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
      <?php echo $task->getAttributeLabel('type'); ?>&nbsp;
    </th>
    <td nowrap>
      &nbsp;<?php echo $task->getType(); ?>
    </td>
  </tr>
  <tr bgcolor="#e6eff3">
    <th align="right" nowrap>
      <?php echo $task->getAttributeLabel('calc_id'); ?>&nbsp;
    </th>
    <td nowrap>
      &nbsp;<?php echo CHtml::link($task->calc_id, $this->createAbsoluteUrl('task/view', array('id' => $task->id))); ?>
    </td>
  </tr>
  <tr bgcolor="#e6eff3">
    <th align="right" nowrap>
      <?php echo $task->getAttributeLabel('par_project_id'); ?>&nbsp;
    </th>
    <td nowrap>
      &nbsp;<?php echo CHtml::link($task->project->name, $this->createAbsoluteUrl('project/viewTasks', array('id' => $task->par_project_id))); ?>
    </td>
  </tr>
  <tr bgcolor="#e6eff3">
    <th align="right" nowrap>
      <?php echo $task->getAttributeLabel('status'); ?>&nbsp;
    </th>
    <td nowrap>
      &nbsp;<?php echo $task->getStatus(); ?>
    </td>
  </tr>
  <tr bgcolor="#e6eff3">
    <th align="right" nowrap>
      <?php echo $task->getAttributeLabel('progress'); ?>&nbsp;
    </th>
    <td nowrap>
      &nbsp;<?php echo $task->progress; ?> %
    </td>
  </tr>
  <tr bgcolor="#e6eff3">
    <th align="right" nowrap>
      <?php echo $task->getAttributeLabel('end_date'); ?>&nbsp;
    </th>
    <td nowrap>
      &nbsp;<?php echo $task->end_date === null ? '' : Yii::app()->format->date($task->end_date); ?>
    </td>
  </tr>
  <tr bgcolor="#e6eff3">
    <th align="right" nowrap>
      <?php echo $task->getAttributeLabel('created'); ?>&nbsp;
    </th>
    <td nowrap>
      &nbsp;<?php echo Yii::app()->format->dateTime($task->created); ?>
    </td>
  </tr>
</table>