<?php
/* @var $this UserProjectController */
/* @var $model UserProject */
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
    <?php echo $form->label($model, 'user_id'); ?>
    <?php echo $form->dropDownList($model, 'user_id', CHtml::listData(User::model()->findAll(), 'id', 'username')); ?>
  </div>

  <div class="row">
    <?php echo $form->label($model, 'rollon_date'); ?>
    <?php echo $form->dateField($model, 'rollon_date'); ?>
  </div>

  <div class="row">
    <?php echo $form->label($model, 'rolloff_date'); ?>
    <?php echo $form->dateField($model, 'rolloff_date'); ?>
  </div>

  <div class="row buttons">
    <?php echo CHtml::submitButton(Yii::t('app', 'Form.search')); ?>
  </div>

  <?php $this->endWidget(); ?>

</div><!-- search-form -->