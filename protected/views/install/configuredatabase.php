<?php
/* @var $this SiteController */
/* @var $model ContactForm */
/* @var $form CActiveForm */

$this->pageTitle = Yii::app()->name . ' - Database Configuration';
$this->breadcrumbs = array(
    'Database Configuration',
);

Yii::app()->clientScript->registerScript('toggleDB', "
$(document).ready(function(){

  function showFields()
  {
    $('.fields').hide();
    $('.' + $('#InstallForm_dbType').val()).show();

    var defaultVal = $('#InstallForm_dbType').val() == 'sqlite' ? '/path/to/file.db' : 'localhost';

    $('#InstallForm_dbHost').val(defaultVal);
  }

  $('#InstallForm_dbType').on('change',showFields);

  showFields();

});
");

?>

<!-- 2022/07/07 modified-->
<!-- <h1>Database Configuration</h1> -->
<h1>データベース構成</h1>

<!-- 2022/07/07 modified-->
<!-- Please configure the database that MyT will use, this information can usually be obtained from your webhost. -->
<p>
  MyTが使用するデータベースを構成してください。この情報は通常、ウェブホストから取得できます。
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
    <?php echo $form->labelEx($model, 'dbType'); ?>
    <?php echo $form->dropDownList($model, 'dbType', $dbTypes); ?>
    <?php echo $form->error($model, 'dbType'); ?>
  </div>

  <div class="row">
    <?php echo $form->labelEx($model, 'dbHost'); ?>
    <?php echo $form->textField($model, 'dbHost'); ?>
    <?php echo $form->error($model, 'dbHost'); ?>
  </div>

  <div class="row fields mysql">
    <?php echo $form->labelEx($model, 'dbName'); ?>
    <?php echo $form->textField($model, 'dbName'); ?>
    <?php echo $form->error($model, 'dbName'); ?>
  </div>

  <div class="row fields mysql">
    <?php echo $form->labelEx($model, 'dbUsername'); ?>
    <?php echo $form->textField($model, 'dbUsername'); ?>
    <?php echo $form->error($model, 'dbUsername'); ?>
  </div>

  <div class="row fields mysql">
    <?php echo $form->labelEx($model, 'dbPassword'); ?>
    <?php echo $form->passwordField($model, 'dbPassword'); ?>
    <?php echo $form->error($model, 'dbPassword'); ?>
  </div>

  <div class="row">
    <?php echo $form->labelEx($model, 'dbTablePrefix'); ?>
    <?php echo $form->textField($model, 'dbTablePrefix', array('value' => 'myt_')); ?>
    <?php echo $form->error($model, 'dbTablePrefix'); ?>
  </div>

  <div class="row buttons">
    <?php
        //echo CHtml::submitButton('Submit');
        echo CHtml::submitButton('実行');
    ?>
  </div>

  <?php $this->endWidget(); ?>

</div><!-- form -->