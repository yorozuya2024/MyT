<?php
/* @var $this ConfigController */
/* @var $model ConfigForm */
?>

<fieldset>
  <?php echo CHtml::tag('legend', array(), 'Email'); ?>

  <div class="row clear">
    <span class="span-5 no-wrap"><?php echo $form->labelEx($model, 'notifications[taskAssociation][email]'); ?></span>
    <span class="span-1"><?php echo $form->checkBox($model, 'notifications[taskAssociation][email]'); ?></span>
  </div>

  <div class="row clear">
    <span class="span-5 no-wrap"><?php echo $form->labelEx($model, 'notifications[projectAssociation][email]'); ?></span>
    <span class="span-1"><?php echo $form->checkBox($model, 'notifications[projectAssociation][email]'); ?></span>
  </div>
</fieldset>

<fieldset>
  <?php echo CHtml::tag('legend', array(), 'Android'); ?>

  <div class="row clear">
    <span class="span-5 no-wrap"><?php echo $form->labelEx($model, 'notifications[taskAssociation][android]'); ?></span>
    <span class="span-1"><?php echo $form->checkBox($model, 'notifications[taskAssociation][android]'); ?></span>
  </div>

  <div class="row clear">
    <span class="span-5 no-wrap"><?php echo $form->labelEx($model, 'notifications[projectAssociation][android]'); ?></span>
    <span class="span-1"><?php echo $form->checkBox($model, 'notifications[projectAssociation][android]'); ?></span>
  </div>

  <hr />

  <div class="row clear">
    <?php echo $form->labelEx($model, 'notifications[googleApiKey]'); ?>
    <?php echo $form->textField($model, 'notifications[googleApiKey]', array('size' => 50)); ?>
  </div>
</fieldset>

