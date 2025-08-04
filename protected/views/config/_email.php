<?php
/* @var $this ConfigController */
/* @var $model ConfigForm */
?>
<fieldset>
  <legend><?php echo $form->labelEx($model, 'paramsEmail'); ?></legend>
  <div class="row">
    <?php echo $form->labelEx($model, 'adminEmail'); ?>
    <?php echo $form->emailField($model, 'adminEmail', array('size' => 50)); ?>
    <?php echo $form->error($model, 'adminEmail'); ?>
  </div>

  <div class="row">
    <?php echo $form->labelEx($model, 'subjectPrefixEmail'); ?>
    <?php echo $form->textField($model, 'subjectPrefixEmail', array('size' => 50)); ?>
    <?php echo $form->error($model, 'subjectPrefixEmail'); ?>
  </div>

  <div class="row">
    <?php echo $form->labelEx($model, 'paramsEmail[method]'); ?>
    <?php echo $form->dropDownList($model, 'paramsEmail[method]', array('php' => 'PHP', 'smtp' => 'SMTP')); ?>
    <?php echo $form->error($model, 'paramsEmail[method]'); ?>
  </div>
</fieldset>

<fieldset>
  <legend><?php echo $form->labelEx($model, 'paramsEmail[smtp]'); ?></legend>

  <div class="row">
    <?php echo $form->labelEx($model, 'paramsEmail[smtp][host]'); ?>
    <?php echo $form->textField($model, 'paramsEmail[smtp][host]', array('size' => 50)); ?>
    <?php echo $form->error($model, 'paramsEmail[smtp][host]'); ?>
  </div>

  <div class="row">
    <?php echo $form->labelEx($model, 'paramsEmail[smtp][port]'); ?>
    <?php echo $form->textField($model, 'paramsEmail[smtp][port]', array('size' => 50)); ?>
    <?php echo $form->error($model, 'paramsEmail[smtp][port]'); ?>
  </div>

  <div class="row">
    <?php echo $form->labelEx($model, 'paramsEmail[smtp][encryption]'); ?>
    <?php echo $form->textField($model, 'paramsEmail[smtp][encryption]', array('size' => 50)); ?>
    <?php echo $form->error($model, 'paramsEmail[smtp][encryption]'); ?>
  </div>

  <div class="row">
    <?php echo $form->labelEx($model, 'paramsEmail[smtp][username]'); ?>
    <?php echo $form->textField($model, 'paramsEmail[smtp][username]', array('size' => 50)); ?>
    <?php echo $form->error($model, 'paramsEmail[smtp][username]'); ?>
  </div>

  <div class="row">
    <?php echo $form->labelEx($model, 'paramsEmail[smtp][password]'); ?>
    <?php echo $form->textField($model, 'paramsEmail[smtp][password]', array('size' => 50)); ?>
    <?php echo $form->error($model, 'paramsEmail[smtp][password]'); ?>
  </div>

</fieldset>