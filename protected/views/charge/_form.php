<?php
/* @var $this ChargeController */
/* @var $model Charge */
/* @var $records Charge[] */
/* @var $projects Project[] */
/* @var $date string */
/* @var $userId integer */
/* @var $prjId integer */

/* @var $form CActiveForm */

Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl . '/js/timesheet.js', CClientScript::POS_END);
?>

<div class="form">

  <?php
  $form = $this->beginWidget('CActiveForm', array(
      'id' => 'charge-form',
      'enableAjaxValidation' => false,
  ));
  ?>

  <?php if (!empty($errors)) echo $form->errorSummary($errors); ?>

  <div id="charge-table">
    <?php
    echo $this->renderPartial('_table', array(
        'model' => $model,
        'projects' => $projects,
        'date' => $date,
        'records' => $records,
        'userId' => $userId,
        'prjId' => $prjId,
    ));
    ?>
  </div>

  <div class="row buttons">
    <?php echo CHtml::submitButton(Yii::t('app', 'Form.save')); ?>
    <?php echo CHtml::button(Yii::t('app', 'Form.cancel'), array('onclick' => 'window.history.back()')); ?>
  </div>

  <?php $this->endWidget(); ?>

</div><!-- form -->

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
