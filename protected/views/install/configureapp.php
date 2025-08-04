<?php
/* @var $this SiteController */
/* @var $model ContactForm */
/* @var $form CActiveForm */

$this->pageTitle = Yii::app()->name . ' - Database Configuration';
$this->breadcrumbs = array(
    'Database Configuration',
    'Database Creation',
    'Application Configuration'
);
?>

<!-- 2022/07/07 modified -->
<!-- <h1>Application Configuration</h1> -->
<h1>アプリケーション構成</h1>

<p>
  <!-- 2022/07/07 modified -->
  <!-- Please configure application title and the administrator account. -->
  アプリケーションタイトルと管理者アカウントを設定してください。
</p>

<div class="form">

  <?php
  $form = $this->beginWidget('CActiveForm', array(
      'id' => 'configure-form',
      'enableClientValidation' => true,
      'clientOptions' => array(
          'validateOnSubmit' => true,
      ),
  ));
  ?>

  <p class="note">Fields with <span class="required">*</span> are required.</p>

  <?php echo $form->errorSummary($model); ?>

  <div class="row">
    <?php echo $form->labelEx($model, 'appName'); ?>
    <?php echo $form->textField($model, 'appName'); ?>
    <?php echo $form->error($model, 'appName'); ?>
  </div>

  <div class="row">
    <?php echo $form->labelEx($model, 'appLanguage'); ?>
    <?php
    $defaultLanguage = array(Yii::app()->language => array('selected' => 'selected'));
    echo $form->dropDownList($model, 'appLanguage', $model->languageList, array('options' => $defaultLanguage));
    ?>
    <?php echo $form->error($model, 'appLanguage'); ?>
  </div>

  <div class="row">
    <?php echo $form->labelEx($model, 'appUsername'); ?>
    <?php echo $form->textField($model, 'appUsername'); ?>
    <?php echo $form->error($model, 'appUsername'); ?>
  </div>

  <div class="row">
    <?php echo $form->labelEx($model, 'appPassword'); ?>
    <?php echo $form->passwordField($model, 'appPassword'); ?>
    <?php echo $form->error($model, 'appPassword'); ?>
  </div>

  <div class="row">
    <?php echo $form->labelEx($model, 'appPasswordConfirm'); ?>
    <?php echo $form->passwordField($model, 'appPasswordConfirm'); ?>
    <?php echo $form->error($model, 'appPasswordConfirm'); ?>
  </div>

  <div class="row">
    <?php echo $form->labelEx($model, 'appEmail'); ?>
    <?php echo $form->textField($model, 'appEmail'); ?>
    <?php echo $form->error($model, 'appEmail'); ?>
  </div>

  <div class="row buttons">
    <?php
        //2020/07/07 modified
        //echo CHtml::submitButton('Submit');
        echo CHtml::submitButton('実行');
    ?>
  </div>

  <?php $this->endWidget(); ?>

</div><!-- form -->