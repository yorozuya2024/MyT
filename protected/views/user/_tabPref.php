<?php
/* @var $this UserController */
/* @var $model User */
/* @var $form CActiveForm */
?>

<div class="row">
  <?php echo $form->labelEx($model, 'page_size'); ?>
  <?php echo $form->numberField($model, 'page_size', array('size' => 3, 'step' => 1, 'min' => 1, 'max' => 255)); ?>
  <?php echo $form->error($model, 'page_size'); ?>
</div>
