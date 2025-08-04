<?php
/* @var $this TaskController */
/* @var $data Task */
?>

<div class="view">

  <b><?php echo CHtml::encode($data->getAttributeLabel('id')); ?>:</b>
  <?php echo CHtml::link(CHtml::encode($data->id), array('view', 'id' => $data->id)); ?>
  <br />

  <b><?php echo CHtml::encode($data->getAttributeLabel('created')); ?>:</b>
  <?php echo CHtml::encode(Yii::app()->format->datetime($data->created)); ?>
  <br />

  <b><?php echo CHtml::encode($data->getAttributeLabel('created_by')); ?>:</b>
  <?php echo CHtml::encode($data->creator->username); ?>
  <br />

  <b><?php echo CHtml::encode($data->getAttributeLabel('last_upd')); ?>:</b>
  <?php echo CHtml::encode(Yii::app()->format->datetime($data->last_upd)); ?>
  <br />

  <b><?php echo CHtml::encode($data->getAttributeLabel('title')); ?>:</b>
  <?php echo CHtml::encode($data->title); ?>
  <br />

  <b><?php echo CHtml::encode($data->getAttributeLabel('description')); ?>:</b>
  <?php echo CHtml::encode($data->description); ?>
  <br />

</div>