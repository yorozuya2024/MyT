<?php
/* @var $this ConfigController */
/* @var $model ConfigForm */
?>

<div class="row clear">
  <span class="span-3"><?php echo $form->labelEx($model, 'tabs[Project]'); ?></span>
  <span class="span-1"><?php echo $form->checkBox($model, 'tabs[Project]') ?></span>
</div>

<div class="row clear">
  <span class="span-3"><?php echo $form->labelEx($model, 'tabs[Task]'); ?></span>
  <span class="span-1"><?php echo $form->checkBox($model, 'tabs[Task]') ?></span>
</div>

<div class="row clear">
  <span class="span-3"><?php echo $form->labelEx($model, 'tabs[User]'); ?></span>
  <span class="span-1"><?php echo $form->checkBox($model, 'tabs[User]') ?></span>
</div>

<div class="row clear">
  <span class="span-3"><?php echo $form->labelEx($model, 'tabs[Charge]'); ?></span>
  <span class="span-1"><?php echo $form->checkBox($model, 'tabs[Charge]') ?></span>
</div>

<div class="row clear">
  <span class="span-3"><?php echo $form->labelEx($model, 'tabs[Authorization]'); ?></span>
  <span class="span-1"><?php echo $form->checkBox($model, 'tabs[Authorization]') ?></span>
</div>