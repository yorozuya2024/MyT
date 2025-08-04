<?php
/* @var $this UserController */
/* @var $model User */
/* @var $form CActiveForm */

$this->pageTitle = Yii::app()->name . ' - ' . Yii::t('app', 'User.reset.password.title');
//$this->breadcrumbs = array(
//    'Reset Password',
//);
?>
<h1><?php echo Yii::t('app', 'User.reset.password.title'); ?></h1>

<div class="form">

  <?php
  $form = $this->beginWidget('CActiveForm', array(
      'id' => 'user-form',
      'enableAjaxValidation' => false,
      'clientOptions' => array(
          'validateOnSubmit' => true,
      ),
  ));
  ?>

  <?php echo $form->errorSummary($model); ?>

  <div class="row">
    <?php echo $form->labelEx($model, 'password'); ?>
    <?php echo $form->passwordField($model, 'password', array('size' => 60, 'maxlength' => 64)); ?>
    <?php echo $form->error($model, 'password'); ?>
  </div>

  <div class="row">
    <?php echo $form->labelEx($model, 'password_confirm'); ?>
    <?php echo $form->passwordField($model, 'password_confirm', array('size' => 60, 'maxlength' => 64)); ?>
    <?php echo $form->error($model, 'password_confirm'); ?>
  </div>

  <div class="row buttons">
    <?php echo CHtml::submitButton(Yii::t('app', 'Form.send')); ?>
  </div>

  <?php $this->endWidget(); ?>

</div><!-- form -->