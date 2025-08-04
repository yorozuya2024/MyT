<?php
/* @var $this TaskStatusController */
/* @var $model TaskStatus */
/* @var $form CActiveForm */
?>

<div class="wide form">

  <?php
  $form = $this->beginWidget('CActiveForm', array(
      'id' => 'task-status-form',
      'enableAjaxValidation' => false,
  ));
  ?>

  <?php echo $form->errorSummary($model); ?>

  <div class="row">
    <?php echo $form->labelEx($model, 'name'); ?>
    <?php echo $form->textField($model, 'name', array('size' => 30, 'maxlength' => 30)); ?>
    <?php echo $form->error($model, 'name'); ?>
  </div>

  <div class="row">
    <?php echo $form->labelEx($model, 'order_by'); ?>
    <?php echo $form->numberField($model, 'order_by', array('size' => 11, 'maxlength' => 11, 'min' => 0)); ?>
    <?php echo $form->error($model, 'order_by'); ?>
  </div>

  <div class="row">
    <?php echo $form->labelEx($model, 'default_flg'); ?>
    <?php echo $form->checkBox($model, 'default_flg'); ?>
    <?php echo $form->error($model, 'default_flg'); ?>
  </div>

  <div class="row">
    <?php echo $form->labelEx($model, 'active_flg'); ?>
    <?php echo $form->checkBox($model, 'active_flg'); ?>
    <?php echo $form->error($model, 'active_flg'); ?>
  </div>

  <div class="row">
    <?php echo $form->labelEx($model, 'group_id'); ?>
    <?php echo $form->dropDownList($model, 'group_id', $model->groupList); ?>
    <?php echo $form->error($model, 'group_id'); ?>
  </div>

  <div class="row buttons">
    <?php echo CHtml::submitButton($model->isNewRecord ? Yii::t('app', 'Form.create') : Yii::t('app', 'Form.save')); ?>
    <?php echo CHtml::button(Yii::t('app', 'Form.cancel'), array('onclick' => 'window.history.back()')); ?>
  </div>

  <?php $this->endWidget(); ?>

</div><!-- form -->