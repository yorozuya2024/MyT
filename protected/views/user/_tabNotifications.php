<?php
/* @var $this UserController */
/* @var $model User */
/* @var $form CActiveForm */
?>

<fieldset>
  <?php echo CHtml::tag('legend', array(), Yii::t('app', 'User.form.email')); ?>

  <div class="row clear">
    <span class="span-5"><?php echo $form->labelEx($model, 'notifications[taskAssociation][email]'); ?></span>
    <span class="span-1"><?php echo $form->checkBox($model, 'notifications[taskAssociation][email]'); ?></span>
  </div>

  <div class="row clear">
    <span class="span-5"><?php echo $form->labelEx($model, 'notifications[projectAssociation][email]'); ?></span>
    <span class="span-1"><?php echo $form->checkBox($model, 'notifications[projectAssociation][email]'); ?></span>
  </div>
</fieldset>

<fieldset>
  <?php echo CHtml::tag('legend', array(), Yii::t('app', 'User.form.android')); ?>

  <div class="row clear">
    <span class="span-5"><?php echo $form->labelEx($model, 'notifications[taskAssociation][android]'); ?></span>
    <span class="span-1"><?php echo $form->checkBox($model, 'notifications[taskAssociation][android]'); ?></span>
  </div>

  <div class="row clear">
    <span class="span-5"><?php echo $form->labelEx($model, 'notifications[projectAssociation][android]'); ?></span>
    <span class="span-1"><?php echo $form->checkBox($model, 'notifications[projectAssociation][android]'); ?></span>
  </div>
</fieldset>

<div class="clear">&nbsp;</div>