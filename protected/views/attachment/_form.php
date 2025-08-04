<?php
/* @var $this AttachmentController */
/* @var $model Attachment */
/* @var $form CActiveForm */
?>

<div class="form">

  <?php
  $form = $this->beginWidget('CActiveForm', array(
      'id' => 'attachment-form',
      'enableAjaxValidation' => false,
      'htmlOptions' => array(
          'enctype' => 'multipart/form-data',
      ),
  ));
  ?>

  <?php echo $form->errorSummary($model); ?>

  <div class="row">
    <?php echo $form->labelEx($model, 'name'); ?>
    <?php echo $form->textField($model, 'name', array('size' => 60, 'maxlength' => 100)); ?>
    <?php echo $form->error($model, 'name'); ?>
  </div>

  <?php if ($model->isNewRecord): ?>
    <div class="row">
      <?php echo $form->labelEx($model, 'type'); ?>
      <?php echo $form->dropDownList($model, 'type', Attachment::$types); ?>
      <?php echo $form->error($model, 'type'); ?>
    </div>

    <div class="row" id="attachment-file">
      <?php echo $form->labelEx($model, 'file'); ?>
      <?php echo $form->fileField($model, 'file'); ?>
      <?php echo $form->error($model, 'file'); ?>
    </div>
  <?php else: ?>
    <div class="row">
      <?php echo $form->hiddenField($model, 'type'); ?>
    </div>
  <?php endif; ?>

  <div class="row" id="attachment-link">
    <?php echo $form->labelEx($model, 'link'); ?>
    <?php echo $form->textField($model, 'link', array('size' => 60)); ?>
    <?php echo $form->error($model, 'link'); ?>
  </div>

  <div class="row buttons">
    <?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
  </div>

  <?php $this->endWidget(); ?>

</div><!-- form -->

<?php
Yii::app()->clientScript->registerScript('switch-type', '
    var loadType = $("#Attachment_type").val();
    switch (loadType) {
        case "file":
            $("#attachment-link").hide();
            break;
        case "link":
            $("#attachment-file").hide();
            break;
    }
    $("#Attachment_type").on("change", function() {
        $("#attachment-link").slideToggle();
        $("#attachment-file").slideToggle();
    });
', CClientScript::POS_READY);

Yii::app()->clientScript->registerScript('file-name-trigger', '
    $("#Attachment_file").on("change", function(e) {
        var $name = $("#Attachment_name");
        if ($.trim($name.val()).length === 0)
            $name.val($(this)[0].files[0].name);
    });
', CClientScript::POS_READY);
