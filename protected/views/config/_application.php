<?php
/* @var $this ConfigController */
/* @var $model ConfigForm */
?>

<div class="row">
  <?php echo $form->labelEx($model, 'name'); ?>
  <?php echo $form->textField($model, 'name', array('size' => 50)); ?>
  <?php echo $form->error($model, 'name'); ?>
</div>

<div class="row">
  <?php echo $form->labelEx($model, 'language'); ?>
  <?php echo $form->dropDownList($model, 'language', $model->languageList); ?>
  <?php echo $form->error($model, 'language'); ?>
</div>

<div class="row">
  <?php echo $form->labelEx($model, 'theme'); ?>
  <?php echo $form->dropDownList($model, 'theme', $model->themeList); ?>
  <?php echo $form->error($model, 'theme'); ?>
</div>

<div class="row">
  <?php echo $form->labelEx($model, 'pageSize'); ?>
  <?php echo $form->numberField($model, 'pageSize', array('size' => 3, 'step' => 1, 'min' => 1, 'max' => 255)); ?>
  <?php echo $form->error($model, 'pageSize'); ?>
</div>

<div class="row">
  <?php echo $form->labelEx($model, 'enableDebugToolbar'); ?>
  <?php echo $form->checkBox($model, 'enableDebugToolbar') ?>
</div>

<div class="row">
  <?php echo $form->labelEx($model, 'taskIdLength'); ?>
  <?php echo $form->numberField($model, 'taskIdLength', array('size' => 3, 'step' => 1, 'min' => 4, 'max' => 10)); ?>
  <?php echo $form->error($model, 'taskIdLength'); ?>
</div>
