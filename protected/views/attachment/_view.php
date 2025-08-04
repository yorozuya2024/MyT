<?php
/* @var $this AttachmentController */
/* @var $data Attachment */
?>

<div class="view">

  <b><?php echo CHtml::encode($data->getAttributeLabel('id')); ?>:</b>
  <?php echo CHtml::link(CHtml::encode($data->id), array('view', 'id' => $data->id)); ?>
  <br />

  <b><?php echo CHtml::encode($data->getAttributeLabel('name')); ?>:</b>
  <?php echo CHtml::encode($data->name); ?>
  <br />

  <b><?php echo CHtml::encode($data->getAttributeLabel('type')); ?>:</b>
  <?php echo CHtml::encode($data->type); ?>
  <br />

  <b><?php echo CHtml::encode($data->getAttributeLabel('uri')); ?>:</b>
  <?php echo CHtml::encode($data->uri); ?>
  <br />

  <b><?php echo CHtml::encode($data->getAttributeLabel('created')); ?>:</b>
  <?php echo CHtml::encode($data->created); ?>
  <br />

  <b><?php echo CHtml::encode($data->getAttributeLabel('task_id')); ?>:</b>
  <?php echo CHtml::encode($data->task_id); ?>
  <br />

  <b><?php echo CHtml::encode($data->getAttributeLabel('project_id')); ?>:</b>
  <?php echo CHtml::encode($data->project_id); ?>
  <br />

</div>