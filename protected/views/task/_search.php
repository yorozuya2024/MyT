<?php
/* @var $this TaskController */
/* @var $model Task */
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
    <?php echo $form->label($model, 'created'); ?>
    <?php echo $form->textField($model, 'created'); ?>
  </div>

  <div class="row">
    <?php echo $form->label($model, 'created_by'); ?>
    <?php echo $form->textField($model, 'created_by'); ?>
  </div>

  <div class="row">
    <?php echo $form->label($model, 'last_upd'); ?>
    <?php echo $form->textField($model, 'last_upd'); ?>
  </div>

  <div class="row">
    <?php echo $form->label($model, 'par_project_id'); ?>
    <?php echo $form->textField($model, 'par_project_id'); ?>
  </div>

  <div class="row">
    <?php echo $form->label($model, 'title'); ?>
    <?php echo $form->textField($model, 'title', array('size' => 60, 'maxlength' => 63)); ?>
  </div>

  <div class="row">
    <?php echo $form->label($model, 'description'); ?>
    <?php echo $form->textArea($model, 'description', array('rows' => 6, 'cols' => 50)); ?>
  </div>

  <div class="row">
    <?php echo $form->label($model, 'status'); ?>
    <?php echo $form->textField($model, 'status', array('size' => 60, 'maxlength' => 63)); ?>
  </div>

  <div class="row">
    <?php echo $form->label($model, 'progress'); ?>
    <?php echo $form->textField($model, 'progress'); ?>
  </div>

  <div class="row">
    <?php echo $form->label($model, 'start_date'); ?>
    <?php echo $form->textField($model, 'start_date'); ?>
  </div>

  <div class="row">
    <?php echo $form->label($model, 'end_date'); ?>
    <?php echo $form->textField($model, 'end_date'); ?>
  </div>

  <div class="row">
    <?php echo $form->label($model, 'eff_start_date'); ?>
    <?php echo $form->textField($model, 'eff_start_date'); ?>
  </div>

  <div class="row">
    <?php echo $form->label($model, 'eff_end_date'); ?>
    <?php echo $form->textField($model, 'eff_end_date'); ?>
  </div>

  <div class="row buttons">
    <?php echo CHtml::submitButton(Yii::t('app', 'Form.search')); ?>
  </div>

  <?php $this->endWidget(); ?>

</div><!-- search-form -->