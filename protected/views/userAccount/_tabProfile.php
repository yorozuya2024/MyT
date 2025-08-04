<?php
/* @var $this UserController */
/* @var $model User */
/* @var $form CActiveForm */
?>

<div class="row">
  <?php echo $form->labelEx($model, 'name'); ?>
  <?php echo $form->textField($model, 'name'); ?>
  <?php echo $form->error($model, 'name'); ?>
</div>

<div class="row">
  <?php echo $form->labelEx($model, 'surname'); ?>
  <?php echo $form->textField($model, 'surname'); ?>
  <?php echo $form->error($model, 'surname'); ?>
</div>

<div class="row">
  <style>#User_gender label {display: inherit;}</style>
  <?php echo $form->labelEx($model, 'gender'); ?>
  <?php echo $form->radioButtonList($model, 'gender', array('M' => 'M', 'F' => 'F'), array('separator' => '&nbsp;')); ?>
  <?php echo $form->error($model, 'gender'); ?>
</div>

<div class="row">
  <?php echo $form->labelEx($model, 'level'); ?>
  <?php echo $form->textField($model, 'level'); ?>
  <?php echo $form->error($model, 'level'); ?>
</div>

<div class="row">
  <?php echo $form->labelEx($model, 'phone'); ?>
  <?php echo $form->textField($model, 'phone'); ?>
  <?php echo $form->error($model, 'phone'); ?>
</div>

<div class="row">
  <?php echo $form->labelEx($model, 'mobile'); ?>
  <?php echo $form->textField($model, 'mobile'); ?>
  <?php echo $form->error($model, 'mobile'); ?>
</div>

<div class="row">
  <?php echo $form->labelEx($model, 'avatar'); ?>
  <label><?php echo CHtml::radioButton('User[optAvatar]', false, array('id' => 'noAvatar', 'value' => 'N')); ?> No Avatar</label>
  <label><?php echo CHtml::radioButton('User[optAvatar]', true, array('id' => 'yesAvatar', 'value' => 'Y')); ?> <?php echo CHtml::activeFileField($model, 'avatar'); ?></label>
  <?php echo $form->error($model, 'avatar'); ?>
</div>
<?php if (!$model->isNewRecord) { ?>
  <div class="row">
    <?php
    $gender = $model->gender === 'F' ? 'F' : 'M';
    $avatar = $model->avatar ? Yii::app()->baseUrl . '/' . Yii::app()->params['avatarPath'] . $model->avatar : Yii::app()->baseUrl . '/' . Yii::app()->params['avatarPath'] . 'default_avatar_' . $gender . '.jpg';
    echo CHtml::image($avatar);
    ?>
  </div>
<?php } ?>
<div class="hint">
  <?php echo Yii::t('attributes', 'ConfigForm.imageDimension.maxSize'), ' = ', Yii::app()->params['imageDimension']['maxSize']; ?>
</div>
