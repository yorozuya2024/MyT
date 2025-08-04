<?php
/* @var $this AuthItemController */
/* @var $model AuthItem */
/* @var $form CActiveForm */
?>

<div class="form">

  <?php
  $form = $this->beginWidget('CActiveForm', array(
      'id' => 'auth-item-form',
      'enableAjaxValidation' => false,
  ));
  ?>

  <?php echo $form->errorSummary($model); ?>

  <div class="row">
    <?php echo $form->labelEx($model, 'name'); ?>
    <?php echo $form->textField($model, 'name', array('size' => 60, 'maxlength' => 64)); ?>
    <?php echo $form->error($model, 'name'); ?>
  </div>

  <div class="row">
    <?php echo $form->labelEx($model, 'description'); ?>
    <?php echo $form->textArea($model, 'description', array('rows' => 6, 'cols' => 50, 'style' => 'display:none')); ?>
    <?php echo $form->error($model, 'description'); ?>
  </div>
  <?php
  Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . '/js/ckeditor/ckeditor.js', CClientScript::POS_HEAD);
  Yii::app()->clientScript->registerScript('rich-editor', '
    CKEDITOR.replace("AuthItem_description", {
        customConfig: "config.js"
    });
    ');
  ob_flush();
  ?>

  <fieldset>
    <legend>Operations</legend>
    <?php
    $this->renderPartial('_formOperation', array(
        'model' => $model,
        'form' => $form
    ));
    ?>
  </fieldset>

  <div class="row buttons">
    <?php echo CHtml::submitButton($model->isNewRecord ? Yii::t('app', 'Form.create') : Yii::t('app', 'Form.save')); ?>
    <?php echo CHtml::button(Yii::t('app', 'Form.cancel'), array('onclick' => 'window.history.back()')); ?>
  </div>

  <?php $this->endWidget(); ?>

</div><!-- form -->

<?php
Yii::app()->clientScript->registerScript('hotkeys', "
var isCtrl = false;
$(document).keyup(function(e) {
    if(e.which == 17)
        isCtrl = false;
}).keydown(function(e) {
    if(e.which == 17)
        isCtrl = true;
    if(e.which == 83 && isCtrl) {
        $('input[type=submit]').click();
 	return false;
    }
});
");
?>

<?php
Yii::app()->clientScript->registerScript('readonly', '
var submitted = false;
$("form").submit(function(e) {
    if (submitted) {
        e.preventDefault();
        return false;
    }
    submitted = true;
    $("<div id=\'form-busy\'/>").css({
        opacity: 0.5, 
        position: "fixed",
        top: 0,
        left: 0,
        width: "100%",
        height: $(window).height() + "px",
        background: "grey"
    }).appendTo("body").show();
});
');
?>