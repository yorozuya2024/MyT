<?php
/* @var $this AttachmentController */
/* @var $model Attachment */
/* @var $form CActiveForm */
?>

<div class="wide form">

  <?php
  $form = $this->beginWidget('CActiveForm', array(
      'action' => Yii::app()->createUrl($this->route),
      'method' => 'get',
  ));
  ?>

  <div class="row">
    <?php echo $form->label($model, 'id'); ?>
    <?php echo $form->textField($model, 'id'); ?>
  </div>

  <div class="row">
    <?php echo $form->label($model, 'name'); ?>
    <?php echo $form->textField($model, 'name', array('size' => 60, 'maxlength' => 100)); ?>
  </div>

  <div class="row">
    <?php echo $form->label($model, 'type'); ?>
    <?php echo $form->textField($model, 'type', array('size' => 30, 'maxlength' => 30)); ?>
  </div>

  <div class="row">
    <?php echo $form->label($model, 'uri'); ?>
    <?php echo $form->textArea($model, 'uri', array('rows' => 6, 'cols' => 50)); ?>
  </div>

  <div class="row">
    <?php echo $form->label($model, 'created'); ?>
    <?php echo $form->textField($model, 'created'); ?>
  </div>

  <div class="row">
    <?php echo $form->label($model, 'task_id'); ?>
    <?php echo $form->textField($model, 'task_id'); ?>
  </div>

  <div class="row">
    <?php echo $form->label($model, 'project_id'); ?>
    <?php echo $form->textField($model, 'project_id'); ?>
  </div>

  <div class="row buttons">
    <?php echo CHtml::submitButton('Search'); ?>
  </div>

  <?php $this->endWidget(); ?>

</div><!-- search-form -->