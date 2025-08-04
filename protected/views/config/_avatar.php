<?php
/* @var $this ConfigController */
/* @var $model ConfigForm */
?>

<div class="row">
  <?php echo $form->labelEx($model, 'avatarPath'); ?>
  <?php echo $form->textField($model, 'avatarPath', array('size' => 50)); ?>
  <?php echo $form->error($model, 'avatarPath'); ?>
</div>

<fieldset>
  <legend><?php echo $form->labelEx($model, 'imageDimension'); ?></legend>

  <div class="row">
    <?php echo $form->labelEx($model, 'imageDimension[maxSize]'); ?>
    <?php echo $form->numberField($model, 'imageDimension[maxSize]', array('min' => 0, 'step' => 50)); ?>
    <?php echo $form->error($model, 'imageDimension[maxSize]'); ?>
  </div>
  <div class="row">
    <?php echo $form->labelEx($model, 'imageDimension[maxWidth]'); ?>
    <?php echo $form->numberField($model, 'imageDimension[maxWidth]', array('min' => 0, 'step' => 10)); ?>
    <?php echo $form->error($model, 'imageDimension[maxWidth]'); ?>
  </div>
  <div class="row">
    <?php echo $form->labelEx($model, 'imageDimension[maxHeight]'); ?>
    <?php echo $form->numberField($model, 'imageDimension[maxHeight]', array('min' => 0, 'step' => 10)); ?>
    <?php echo $form->error($model, 'imageDimension[maxHeight]'); ?>
  </div>
  <div class="row">
    <?php echo $form->labelEx($model, 'imageDimension[maxWidthThumb]'); ?>
    <?php echo $form->numberField($model, 'imageDimension[maxWidthThumb]', array('min' => 0, 'step' => 10)); ?>
    <?php echo $form->error($model, 'imageDimension[maxWidthThumb]'); ?>
  </div>
  <div class="row">
    <?php echo $form->labelEx($model, 'imageDimension[maxHeightThumb]'); ?>
    <?php echo $form->numberField($model, 'imageDimension[maxHeightThumb]', array('min' => 0, 'step' => 10)); ?>
    <?php echo $form->error($model, 'imageDimension[maxHeightThumb]'); ?>
  </div>
</fieldset>
