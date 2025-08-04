<?php
/* @var $this ConfigController */
/* @var $model ConfigForm */

$this->breadcrumbs = array(
    Yii::t('nav', 'Application'),
);
?>

<h2><?php echo Yii::t('nav', 'Application'); ?></h2>

<hr />

<div class="form">

  <?php
  $form = $this->beginWidget('CActiveForm', array(
      'id' => 'config-form',
      'enableAjaxValidation' => false,
  ));
  ?>

  <?php echo $form->errorSummary($model); ?>

  <?php
  $this->widget('zii.widgets.jui.CJuiTabs', array(
      'tabs' => array(
          Yii::t('app', 'ConfigForm.tabs.application') => $this->renderPartial('_application', array('form' => $form, 'model' => $model), true),
          Yii::t('app', 'ConfigForm.tabs.email') => $this->renderPartial('_email', array('form' => $form, 'model' => $model), true),
          Yii::t('app', 'ConfigForm.tabs.avatar') => $this->renderPartial('_avatar', array('form' => $form, 'model' => $model), true),
          Yii::t('app', 'ConfigForm.tabs.tabs') => $this->renderPartial('_tabs', array('form' => $form, 'model' => $model), true),
          Yii::t('app', 'ConfigForm.tabs.notifications') => $this->renderPartial('_notifications', array('form' => $form, 'model' => $model), true),
          Yii::t('app', 'ConfigForm.tabs.attachments') => $this->renderPartial('_attachments', array('form' => $form, 'model' => $model), true),
      ),
      'options' => array(
          'collapsible' => true,
          'heightStyle' => 'auto'
      ),
  ));
  ?>

  <?php
  echo CHtml::tag('script', array('type' => 'text/javascript'), '$("#config-form").css("visibility", "hidden");');
  Yii::app()->clientScript->registerScript('config-form-show', "
        $('#config-form').css('visibility', 'visible');
    ", CClientScript::POS_READY);
  ?>

  <div class="row buttons">
    <?php echo CHtml::submitButton(Yii::t('app', 'ConfigForm.save')); ?>
    <?php echo CHtml::button(Yii::t('app', 'ConfigForm.cancel'), array('onclick' => 'window.history.back()')); ?>
  </div>

  <?php $this->endWidget(); ?>
</div>

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
