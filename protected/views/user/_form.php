<?php
/* @var $this UserController */
/* @var $model User */
/* @var $form CActiveForm */
?>

<div class="form">

  <?php
  $form = $this->beginWidget('CActiveForm', array(
      'id' => 'user-form',
      'enableAjaxValidation' => false,
      'htmlOptions' => array(
          'enctype' => 'multipart/form-data',
          'autocomplete' => 'off',
      )
  ));
  ?>

  <?php echo $form->errorSummary($model); ?>

  <?php
  $this->widget('zii.widgets.jui.CJuiTabs', array(
      'tabs' => array(
          Yii::t('app', 'UserForm.tabs.main') => $this->renderPartial('_tabMain', array('form' => $form, 'model' => $model), true),
          Yii::t('app', 'UserForm.tabs.profile') => $this->renderPartial('_tabProfile', array('form' => $form, 'model' => $model), true),
          Yii::t('app', 'UserForm.tabs.roles') => $this->renderPartial('_tabRoles', array('form' => $form, 'model' => $model), true),
          Yii::t('app', 'UserForm.tabs.preferences') => $this->renderPartial('_tabPref', array('form' => $form, 'model' => $model), true),
          Yii::t('app', 'UserForm.tabs.notifications') => $this->renderPartial('_tabNotifications', array('form' => $form, 'model' => $model), true),
      ),
      'options' => array(
          'collapsible' => false,
      // 'heightStyle' => 'auto'
      ),
  ));
  ?>

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
