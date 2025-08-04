<?php
/* @var $this UserProjectController */
/* @var $data UserProject */
?>

<div class="view">
  <div class="row">
    <b><?php echo CHtml::encode($data->getAttributeLabel('user_id')); ?>:</b>
    <?php echo CHtml::link(CHtml::encode($data->user->username), array('user/view', 'id' => $data->user_id)); ?>

    <b><?php echo CHtml::encode($data->getAttributeLabel('user.email')); ?>:</b>
    <?php echo CHtml::mailto(CHtml::encode($data->user->email), CHtml::encode($data->user->email)); ?>
  </div>
  <div class="row">
    <b><?php echo CHtml::encode($data->getAttributeLabel('rollon_date')); ?>:</b>
    <?php echo $data->rollon_date ? CHtml::encode(Yii::app()->format->date($data->rollon_date)) : '-'; ?>

    <b><?php echo CHtml::encode($data->getAttributeLabel('rolloff_date')); ?>:</b>
    <?php echo $data->rolloff_date ? CHtml::encode(Yii::app()->format->date($data->rolloff_date)) : '-'; ?>
  </div>
</div>