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

<?php if (Yii::app()->user->hasFlash('resetpassword')): ?>

  <div class="flash-success">
    <?php echo Yii::app()->user->getFlash('resetpassword'); ?>
  </div>

<?php else: ?>

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
      <?php echo $form->labelEx($model, 'email'); ?>
      <?php echo $form->textField($model, 'email', array('size' => 60, 'maxlength' => 255)); ?>
      <?php echo $form->error($model, 'email'); ?>
    </div>

    <div class="row buttons">
      <?php echo CHtml::submitButton(Yii::t('app', 'Form.send')); ?>
    </div>

    <?php $this->endWidget(); ?>

  </div><!-- form -->

<?php endif; ?>