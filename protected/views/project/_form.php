<?php
/* @var $this ProjectController */
/* @var $model Project */
/* @var $user UserProject */
/* @var $form CActiveForm */
?>

<div class="form">

  <?php

  $form = $this->beginWidget('CActiveForm', array(
      'id' => 'project-form',
      'enableAjaxValidation' => false,
  ));
  ?>

  <?php echo $form->errorSummary($model); ?>

  <div class="span-19">
    <fieldset id="Project_main">
      <legend><?php echo Yii::t('app', 'Project.form.main'); ?></legend>
      <div class="row">
        <?php echo $form->labelEx($model, 'name'); ?>
        <?php echo $form->textField($model, 'name', array('size' => 60, 'maxlength' => 63)); ?>
        <?php echo $form->error($model, 'name'); ?>
      </div>

      <div class="row">
        <?php echo $form->labelEx($model, 'par_project_id'); ?>
        <?php
        echo $form->dropDownList($model, 'par_project_id', CHtml::listData(Project::model()->findAllHierarchical($model->isNewRecord ?
                                        array('order' => 'name') : array('condition' => 'id <> :x', 'order' => 'name', 'params' => array('x' => $model->id))), 'id', function($project) {
                  return str_pad($project->name, strlen($project->name) + 2 * $project->level, '- ', STR_PAD_LEFT);
                }), array('empty' => ''));
        ?>
        <?php echo $form->error($model, 'par_project_id'); ?>
      </div>

      <div class="row">
        <?php echo $form->labelEx($model, 'champion_id'); ?>
        <?php echo $form->dropDownList($model, 'champion_id', CHtml::listData(User::model()->findAll(array('order' => 'username')), 'id', 'username'), array('empty' => '')); ?>
        <?php echo $form->error($model, 'champion_id'); ?>
      </div>

      <div class="row">
        <?php echo $form->labelEx($model, 'client'); ?>
        <?php echo $form->textField($model, 'client', array('size' => 60)); ?>
        <?php echo $form->error($model, 'client'); ?>
      </div>

      <div class="row">
        <?php echo $form->labelEx($user, 'user_id'); ?>
        <?php echo $form->dropDownList($user, 'user_id', CHtml::listData(User::model()->active()->findAll(array('order' => 'username')), 'id', 'username'), array('multiple' => 'multiple')); ?>
        <?php echo $form->error($user, 'user_id'); ?>
      </div>

      <div class="row">
        <?php echo $form->labelEx($model, 'description'); ?>
        <?php // 2024/9/24 modified echo $form->textArea($model, 'description', array('rows' => 6, 'cols' => 50, 'style' => 'display:none')); ?>
        <?php echo $form->textArea($model, 'description', array('rows' => 6, 'cols' => 50)); ?>
        <?php echo $form->error($model, 'description'); ?>
      </div>
      <?php
      // 2024/9/24 delete
      //Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . '/js/ckeditor/ckeditor.js', CClientScript::POS_HEAD);
      //Yii::app()->clientScript->registerScript('rich-editor', '
    //CKEDITOR.replace("Project_description", {
    //    customConfig: "config.js"
    //});
    //');
      //ob_flush();
      ?>
    </fieldset>
  </div>

  <div class="span-5 last">
    <fieldset id="Project_details">
      <legend><?php echo Yii::t('app', 'Project.form.details'); ?></legend>

      <div class="row">
        <?php echo $form->labelEx($model, 'prefix'); ?>
        <?php if($model->isNewRecord) { $model->prefix = 'PRJ'; }
			  echo $form->textField($model, 'prefix', array('size' => 5, 'maxlength' => 3)); ?>
        <?php echo $form->error($model, 'prefix'); ?>
      </div>

      <div class="row">
        <?php echo $form->labelEx($model, 'status'); ?>
        <?php echo $form->dropDownList($model, 'status', $model->getStatusList()); ?>
        <?php echo $form->error($model, 'status'); ?>
      </div>

      <div class="row">
        <?php echo $form->labelEx($model, 'progress'); ?>
        <?php echo $form->numberField($model, 'progress', array('size' => 5, 'min' => 0, 'max' => 100, 'step' => 5)); ?>
        <?php echo $form->error($model, 'progress'); ?>
      </div>

      <div class="row">
        <?php echo $form->labelEx($model, 'chargeable_flg'); ?>
        <?php echo $form->checkBox($model, 'chargeable_flg'); ?>
        <?php echo $form->error($model, 'chargeable_flg'); ?>
        <p class="hint"><?php echo Yii::t('app', 'Project.form.chargeable_flg.hint'); ?></p>
      </div>
    </fieldset>
    <fieldset>
      <legend><?php echo Yii::t('app', 'Project.form.dates'); ?></legend>
      <div class="row">
        <?php echo $form->labelEx($model, 'start_date'); ?>
        <?php echo $form->hiddenField($model, 'start_date'); ?>
        <?php
        $this->widget('ext.datepicker.EJuiDatePicker', array(
            'model' => $model,
            'attribute' => 'start_date',
            'key' => 'update_7zO9c4eQ_',
            'htmlOptions' => array(
                'size' => '10',
                'maxlength' => '10',
                'class' => 'monthpicker',
            ),
                )
        );
        ?>
        <?php echo $form->error($model, 'start_date'); ?>
      </div>

      <div class="row">
        <?php echo $form->labelEx($model, 'end_date'); ?>
        <?php echo $form->hiddenField($model, 'end_date'); ?>
        <?php
        $this->widget('ext.datepicker.EJuiDatePicker', array(
            'model' => $model,
            'attribute' => 'end_date',
            'key' => 'update_7zO9c4eQ_',
            'htmlOptions' => array(
                'size' => '10',
                'maxlength' => '10',
                'class' => 'monthpicker',
            ),
                )
        );
        ?>
        <?php echo $form->error($model, 'end_date'); ?>
      </div>

      <div class="row">
        <?php echo $form->labelEx($model, 'eff_start_date'); ?>
        <?php echo $form->hiddenField($model, 'eff_start_date'); ?>
        <?php
        $this->widget('ext.datepicker.EJuiDatePicker', array(
            'model' => $model,
            'attribute' => 'eff_start_date',
            'key' => 'update_7zO9c4eQ_',
            'htmlOptions' => array(
                'size' => '10',
                'maxlength' => '10',
                'class' => 'monthpicker',
            ),
                )
        );
        ?>
        <?php echo $form->error($model, 'eff_start_date'); ?>
      </div>

      <div class="row">
        <?php echo $form->labelEx($model, 'eff_end_date'); ?>
        <?php echo $form->hiddenField($model, 'eff_end_date'); ?>
        <?php
        $this->widget('ext.datepicker.EJuiDatePicker', array(
            'model' => $model,
            'attribute' => 'eff_end_date',
            'key' => 'update_7zO9c4eQ_',
            'htmlOptions' => array(
                'size' => '10',
                'maxlength' => '10',
                'class' => 'monthpicker',
            ),
                )
        );
        ?>
        <?php echo $form->error($model, 'eff_end_date'); ?>
      </div>
    </fieldset>
  </div>

  <div class="clear"></div>

  <div class="row buttons">
    <?php echo CHtml::submitButton($model->isNewRecord ? Yii::t('app', 'Form.create') : Yii::t('app', 'Form.save')); ?>
    <?php echo CHtml::button(Yii::t('app', 'Form.cancel'), array('onclick' => 'window.history.back()')); ?>
  </div>

  <?php $this->endWidget(); ?>

</div><!-- form -->

<?php
//Yii::app()->clientScript->registerScriptFile('js/tinymce/js/tinymce/tinymce.min.js');
//Yii::app()->clientScript->registerScript('rich-editor', '
//tinymce.init({
//    selector: "textarea",
//    plugins: "advlist autolink lists link charmap print preview searchreplace fullscreen table contextmenu paste",
//    toolbar: "undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link"
// });
//');
?>

<?php
Yii::app()->clientScript->registerCss('project-grid-style', '
    #Project_details select, #Project_details input:not([type=checkbox]) {width: 9em;}
    #Project_main {margin-right: 1em;}
    #Project_main input, #Project_main select {width:30em}
    #project-form fieldset {padding: 1em 1.5em;}
');
?>


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

Yii::app()->clientScript->registerCssFile(Yii::app()->theme->baseUrl . '/css/multi-select.css');
Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . '/js/jquery.multi-select.js', CClientScript::POS_HEAD);
Yii::app()->clientScript->registerScript('multi-select', '
    $("#UserProject_user_id").multiSelect();
', CClientScript::POS_READY);
