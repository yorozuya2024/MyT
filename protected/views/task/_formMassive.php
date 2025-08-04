<?php
/* @var $this TaskController */
/* @var $model TaskMassiveForm */
/* @var $form CActiveForm */

$statusList = CHtml::listData(TaskStatus::model()->active()->findAll(), 'id', 'name', 'group');
$typeList = CHtml::listData(TaskType::model()->active()->findAll(), 'id', 'name');
?>

<div class="form">

  <?php
  $form = $this->beginWidget('CActiveForm', array(
      'id' => 'task-massive-form',
      'action' => array('task/updateMassive'),
      'enableAjaxValidation' => false,
  ));
  ?>

  <?php echo $form->errorSummary($model); ?>

  <div class="row">
    <?php echo $form->hiddenField($model, 'ids'); ?>
  </div>

  <div class="row">
    <?php echo $form->labelEx($model, 'type'); ?>
    <?php echo $form->dropDownList($model, 'type', $typeList, array('empty' => '')); ?>
    <?php echo $form->error($model, 'type'); ?>
  </div>

  <div class="row">
    <?php echo $form->labelEx($model, 'priority'); ?>
    <?php echo $form->dropDownList($model, 'priority', $model->getPriorityList(), array('empty' => '')); ?>
    <?php echo $form->error($model, 'priority'); ?>
  </div>

  <div class="row">
    <?php echo $form->labelEx($model, 'status'); ?>
    <?php echo $form->dropDownList($model, 'status', $statusList, array('empty' => '')); ?>
    <?php echo $form->error($model, 'status'); ?>
  </div>

  <div class="row">
    <?php
    $userFilter = new CDbCriteria;
    $userFilter->alias = 'u';
    $userFilter->select = 'u.id, u.username';
    $userFilter->scopes = array('active', 'enrolled');
    $userFilter->order = 'u.username';
    ?>
    <?php echo $form->labelEx($model, 'owner'); ?>
    <?php echo $form->dropDownList($model, 'owner', CHtml::listData(User::model()->findAll($userFilter), 'id', 'username'), array('empty' => '')); ?>
    <?php echo $form->error($model, 'owner'); ?>
  </div>

  <div class="row">
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
            }), array('empty' => '',
        'ajax' => array(
            'type' => 'POST',
            'dataType' => 'json',
            'url' => CController::createUrl('task/ajaxProjectDeps'),
//            'update' => '#TaskMassiveForm_owner',
            'success' => 'js:function(data){
                        $("#TaskMassiveForm_owner").html(data.users);
                    }',
    )));
    ?>
    <?php echo $form->error($model, 'par_project_id'); ?>
  </div>

  <div class="row">
    <?php echo $form->labelEx($model, 'start_date'); ?>
    <?php echo $form->hiddenField($model, 'start_date'); ?>
    <?php
    $this->widget('ext.datepicker.EJuiDatePicker', array(
        'model' => $model,
        'attribute' => 'start_date',
        'key' => 'massive_8ZIirtKr_',
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
        'key' => 'massive_8ZIirtKr_',
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

  <?php $this->endWidget($form->getId()); ?>

</div><!-- form -->