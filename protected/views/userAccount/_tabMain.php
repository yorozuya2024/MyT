<?php
/* @var $this UserController */
/* @var $model User */
/* @var $form CActiveForm */
?>

<div class="row">
  <?php echo $form->labelEx($model, 'username'); ?>
  <?php echo $form->textField($model, 'username', array('size' => 60, 'maxlength' => 255)); ?>
  <?php echo $form->error($model, 'username'); ?>
</div>

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

<div class="row">
  <?php echo $form->labelEx($model, 'email'); ?>
  <?php echo $form->textField($model, 'email', array('size' => 60, 'maxlength' => 255)); ?>
  <?php echo $form->error($model, 'email'); ?>
</div>
