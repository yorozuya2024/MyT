<?php
/* @var $this UserProjectController */
/* @var $model UserProject */
/* @var $form CActiveForm */
/* @var $projectId String */
?>

<div class="wide form">

  <?php
  $form = $this->beginWidget('CActiveForm', array(
      'id' => 'user-project-form',
      'enableAjaxValidation' => false,
  ));
  ?>

  <?php echo $form->errorSummary($model); ?>

  <?php if ($model->isNewRecord): ?>
    <div class="row">
      <?php
      $assocUsers = UserProject::model()->findAll(array('select' => 'user_id', 'condition' => 'project_id = :id', 'params' => array('id' => $projectId)));
      $assocIds = array();
      foreach ($assocUsers as $assocUser)
        array_push($assocIds, $assocUser->user_id);

      $userCriteria = new CDbCriteria();
      $userCriteria->addNotInCondition('id', $assocIds);
      $userCriteria->order = 'username';
      ?>
      <?php echo $form->labelEx($model, 'user_id'); ?>
      <?php echo $form->dropDownList($model, 'user_id', CHtml::listData(User::model()->active()->findAll($userCriteria), 'id', 'username'), array('multiple' => 'multiple')); ?>
      <?php echo $form->error($model, 'user_id'); ?>
    </div>
  <?php endif; ?>

  <div class="row">
    <?php echo $form->labelEx($model, 'rollon_date'); ?>
    <?php echo $form->hiddenField($model, 'rollon_date'); ?>
    <?php
    $this->widget('ext.datepicker.EJuiDatePicker', array(
        'model' => $model,
        'attribute' => 'rollon_date',
        'key' => 'update_dbI0WO0A_',
        'htmlOptions' => array(
            'size' => '10',
            'maxlength' => '10',
            'class' => 'monthpicker',
        ),
            )
    );
    ?>
    <?php echo $form->error($model, 'rollon_date'); ?>
  </div>

  <div class="row">
    <?php echo $form->labelEx($model, 'rolloff_date'); ?>
    <?php echo $form->hiddenField($model, 'rolloff_date'); ?>
    <?php
    $this->widget('ext.datepicker.EJuiDatePicker', array(
        'model' => $model,
        'attribute' => 'rolloff_date',
        'key' => 'update_dbI0WO0A_',
        'htmlOptions' => array(
            'size' => '10',
            'maxlength' => '10',
            'class' => 'monthpicker',
        ),
            )
    );
    ?>
    <?php echo $form->error($model, 'rolloff_date'); ?>
  </div>

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

Yii::app()->clientScript->registerScript('trigger-dates', '
$("#update_dbI0WO0A_UserProject_rollon_date_container").change(function() {
    $("#update_dbI0WO0A_UserProject_rolloff_date_container").datepicker("option", "minDate", $(this).datepicker("getDate"));
});
');

Yii::app()->clientScript->registerCssFile(Yii::app()->theme->baseUrl . '/css/multi-select.css');
Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . '/js/jquery.multi-select.js', CClientScript::POS_HEAD);
Yii::app()->clientScript->registerScript('multi-select', '
    $("#UserProject_user_id").multiSelect();
', CClientScript::POS_READY);
