<?php
/* @var $this TaskController */
/* @var $model Task */
/* @var $user UserTask */
/* @var $form CActiveForm */
/* @var $projectId integer */
?>

<div class="form">

  <?php
  $form = $this->beginWidget('CActiveForm', array(
      'id' => 'task-form',
      'enableAjaxValidation' => false,
  ));
  ?>

  <?php echo $form->errorSummary($model); ?>

  <div class="span-19">
    <fieldset id="Task_main">
      <legend><?php echo Yii::t('app', 'Task.form.main'); ?></legend>
      <div class="row">
        <?php echo $form->hiddenField($model, 'id'); ?>

        <?php $projectId = ($model->isNewRecord && $projectId !== null) ? $projectId : $model->par_project_id; ?>
        <?php echo $form->labelEx($model, 'par_project_id'); ?>
        <?php
        $pCriteria = new CDbCriteria();
        $pCriteria->scopes = 'open';
        $pCriteria->order = 't.name';
        if (!Yii::app()->user->checkAccess('indexAllProject')) {
          $pCriteria->together = true;
          $pCriteria->with = array('users' => array('select' => false));
          $pCriteria->compare('users.id', Yii::app()->user->id);
        }
        echo $form->dropDownList($model, 'par_project_id', CHtml::listData(Project::model()->findAllHierarchical($pCriteria), 'id', function($project) {
                  return str_pad($project->name, strlen($project->name) + 2 * $project->level, '- ', STR_PAD_LEFT);
                }), array('empty' => '', 'style' => 'width:30em;', 'options' => array($projectId => array('selected' => 'selected')),
            'ajax' => array(
                'type' => 'POST',
                'dataType' => 'json',
                'url' => CController::createUrl('task/ajaxProjectDeps'),
//                'update' => '#UserTask_user_id',
                'success' => 'js:function(data){
                        $("#UserTask_user_id").html(data.users);
                        $("#UserTask_user_id").multiSelect("refresh");

                        $("#Task_parent_id").html(data.tasks);
                    }',
        )));
        ?>
        <?php echo $form->error($model, 'par_project_id'); ?>
      </div>

      <div class="row">
        <?php echo $form->labelEx($model, 'title'); ?>
        <?php echo $form->textField($model, 'title', array('size' => 63, 'maxlength' => 63, 'style' => 'width:30em;')); ?>
        <?php echo $form->error($model, 'title'); ?>
      </div>

      <div class="row">
        <?php echo $form->labelEx($model, 'parent_id'); ?>
        <?php
        $tData = array();
        if (!empty($projectId)) {
          $tCriteria = new CDbCriteria();
          $tCriteria->addNotInCondition('id', array($model->id));
          $tCriteria->compare('t.par_project_id', $projectId);
          $tCriteria->scopes = 'open';
          $tCriteria->order = 't.title';
          $tData = CHtml::listData(
                          Task::model()->findAll($tCriteria), 'id', function($task) {
                    return $task->calc_id . ' - ' . $task->title;
                  }
          );
        }
        echo $form->dropDownList($model, 'parent_id', $tData, array('empty' => '', 'style' => 'width:30em;'));
        ?>
        <?php echo $form->error($model, 'parent_id'); ?>
      </div>

      <div class="row">
        <?php
        $htmlOptions = array();
        $htmlOptions['multiple'] = 'multiple';

        $userFilter = new CDbCriteria;
        $userFilter->alias = 'u';
        $userFilter->select = 'u.id, u.username';
        $userFilter->scopes = array('active', 'enrolled' => $projectId);
        $userFilter->order = 'u.username';
        ?>
        <?php echo $form->labelEx($user, 'user_id'); ?>
        <?php echo $form->dropDownList($user, 'user_id', CHtml::listData(User::model()->findAll($userFilter), 'id', 'username'), $htmlOptions); ?>
        <?php echo $form->error($user, 'user_id'); ?>
      </div>

      <hr />

      <div class="row">
        <?php echo $form->labelEx($model, 'description'); ?>
        <?php echo $form->textArea($model, 'description', array('rows' => 6, 'cols' => 50)); ?>
        <?php echo $form->error($model, 'description'); ?>
      </div>
      <?php
      // 2024/9/15 debug
      //Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . //'/js/ckeditor/ckeditor.js', CClientScript::POS_HEAD);
    //  Yii::app()->clientScript->registerScript('rich-editor', '
    //CKEDITOR.replace("Task_description", {
    //    customConfig: "config.js"
    //});
    //');
    //  Yii::app()->clientScript->registerScript('rich-editor', //'CKEDITOR.replace("Task_description");');

      ?>
    </fieldset>

  </div>

  <div class="span-5 last">
    <fieldset id="Task_details">
      <legend><?php echo Yii::t('app', 'Task.form.details'); ?></legend>
      <div class="row">
        <?php echo $form->labelEx($model, 'type'); ?>
        <?php
        // 2024/9/11 modified
        //$typeList = CHtml::listData(TaskType::model()->active()->findAll(), 'id', 'name');
        $typeList = array_unique(CHtml::listData(TaskType::model()->active()->findAll(), 'id', 'name'));
        echo $form->dropDownList($model, 'type', $typeList);
        ?>
        <?php echo $form->error($model, 'type'); ?>
      </div>

      <div class="row">
        <?php echo $form->labelEx($model, 'priority'); ?>
        <?php
        $defaultPriority = $model->isNewRecord ? array(1 => array('selected' => 'selected')) : array();
        echo $form->dropDownList($model, 'priority', $model->getPriorityList(), array('options' => $defaultPriority));
        ?>
        <?php echo $form->error($model, 'priority'); ?>
      </div>

      <div class="row">
        <?php echo $form->labelEx($model, 'status'); ?>
        <?php
            // 2024/9/11 modified
            //$statusList = CHtml::listData(TaskStatus::model()->active()->findAll(), 'id', 'name');
            $statusList = array_unique(CHtml::listData(TaskStatus::model()->active()->findAll(), 'id', 'name'));
        ?>
        <?php echo $form->dropDownList($model, 'status', $statusList); ?>
        <?php echo $form->error($model, 'status'); ?>
      </div>

      <div class="row">
        <?php echo $form->labelEx($model, 'progress'); ?>
        <?php echo $form->numberField($model, 'progress', array('size' => 5, 'min' => 0, 'max' => 100, 'step' => 5)); ?>
        <?php /*
          echo $form->textField($model, 'progress', array('size' => 5));
          Yii::app()->clientScript->registerScript('progress_spinner', '
          $("#Task_progress").spinner({max: 100, min: 0, step: 5});
          '); */
        ?>
        <?php echo $form->error($model, 'progress'); ?>
      </div>
    </fieldset>
    <fieldset>
      <legend><?php echo Yii::t('app', 'Task.form.options'); ?></legend>

      <div class="row">
        <?php echo $form->labelEx($model, 'private_flg'); ?>
        <?php echo $form->checkBox($model, 'private_flg'); ?>
        <?php echo $form->error($model, 'private_flg'); ?>
      </div>
      <div class="row">
        <?php echo $form->labelEx($model, 'chargeable_flg'); ?>
        <?php echo $form->checkBox($model, 'chargeable_flg'); ?>
        <?php echo $form->error($model, 'chargeable_flg'); ?>
        <p class="hint"><?php echo Yii::t('app', 'Task.form.chargeable_flg.hint'); ?></p>
      </div>

      <?php if ($model->isNewRecord): ?>
        <div class="row">
          <?php
          echo CHtml::label(Yii::t('app', 'Task.form.multipleAssociation'), 'Assoc_multiple');
          echo CHtml::checkBox('Assoc[multiple]');
          ?>
        </div>
      <?php endif; ?>
    </fieldset>
    <fieldset>
      <legend><?php echo Yii::t('app', 'Task.form.dates'); ?></legend>

      <div class="row">
        <?php echo $form->labelEx($model, 'start_date'); ?>
        <?php echo $form->hiddenField($model, 'start_date'); ?>
        <?php
        $this->widget('ext.datepicker.EJuiDatePicker', array(
            'model' => $model,
            'attribute' => 'start_date',
            'key' => 'update_nU8QHG8H_',
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
            'key' => 'update_nU8QHG8H_',
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
            'key' => 'update_nU8QHG8H_',
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
            'key' => 'update_nU8QHG8H_',
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
Yii::app()->clientScript->registerCss('task-grid-style', '
    #Task_details select, #Task_details input {width: 9em;}
    #Task_main {margin-right: 1em;}
    #task-form fieldset {padding: 1em 1.5em;}
');

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

$closed = TaskStatus::getStatusIdByGroup(Yii::t('constants', 'TaskStatus.group.closed'));
Yii::app()->clientScript->registerScript('trigger-status', '
var closed = ' . (is_array($closed) ? $closed[0] : $closed) . ';
function close() {
    var now = new Date();
    if ($("#update_nU8QHG8H_Task_end_date_container").val() == "")
        $("#update_nU8QHG8H_Task_end_date_container").datepicker("setDate", now);
    if ($("#update_nU8QHG8H_Task_eff_start_date_container").val() == "")
        $("#update_nU8QHG8H_Task_eff_start_date_container").datepicker("setDate", now);
    if ($("#update_nU8QHG8H_Task_eff_end_date_container").val() == "")
        $("#update_nU8QHG8H_Task_eff_end_date_container").datepicker("setDate", now);
}
$("#Task_status").change(function() {
    if ($(this).val() == closed) {
        $("#Task_progress").val(100);
        close();
    }
});
// $("#Task_progress").on("spinstop", function() {
$("#Task_progress").change(function() {
//    if ($(this).spinner("value") == 100) {
    if ($(this).val() == 100) {
        $("#Task_status").val(closed);
        close();
    }
});
');

Yii::app()->clientScript->registerScript('trigger-dates', '
$("#update_nU8QHG8H_Task_start_date_container").change(function() {
    $("#update_nU8QHG8H_Task_end_date_container").datepicker("option", "minDate", $(this).datepicker("getDate"));
});
$("#update_nU8QHG8H_Task_eff_start_date_container").change(function() {
    $("#update_nU8QHG8H_Task_eff_end_date_container").datepicker("option", "minDate", $(this).datepicker("getDate"));
});
');

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
    $("#UserTask_user_id").multiSelect();
', CClientScript::POS_READY);
