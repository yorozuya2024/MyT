<?php

$pCriteria = new CDbCriteria();
$pCriteria->select = array('id', 'name');
$pCriteria->order = 't.name';
if (!Yii::app()->user->checkAccess('indexAllProject')) {
  $pCriteria->together = true;
  $pCriteria->with = array('users' => array('select' => false));
  $pCriteria->compare('users.id', Yii::app()->user->id);
}

$open = TaskStatus::getStatusIdByGroup(Yii::t('constants', 'TaskStatus.group.open'));
$typeList = CHtml::listData(TaskType::model()->active()->findAll(), 'id', 'name');

$model = new Task('search');
$model->unsetAttributes();
if (isset($_GET['Task']))
  $model->attributes = $_GET['Task'];

$startDateFilter = $this->createWidget('ext.datepicker.EJuiDatePicker', array(
    'model' => $model,
    'attribute' => 'start_date',
    'key' => 'open_XS9rabCU_',
    'htmlOptions' => array(
        'class' => 'monthpicker',
    ),
        )
);

$endDateFilter = $this->createWidget('ext.datepicker.EJuiDatePicker', array(
    'model' => $model,
    'attribute' => 'end_date',
    'key' => 'open_XS9rabCU_',
    'htmlOptions' => array(
        'class' => 'monthpicker',
    ),
        )
);

$this->widget('ext.HierGridView', array(
    'id' => 'open-task-grid',
    'dataProvider' => $model->searchMyHierarchical($open[0]),
    'treeColumn' => 1,
    'filter' => $model,
    'afterAjaxUpdate' => "function(id, data){
        {$startDateFilter->js}
        {$endDateFilter->js}
        $('#' + id + ' .items').treeTable({treeColumn:1});
    }",
    'columns' => array(
        array(
            'name' => 'calc_id',
            'type' => 'html',
            'value' => function($data) {
              return CHtml::link($data->calc_id, array('task/view', 'id' => $data->id));
            },
                    'htmlOptions' => array('class' => 'col-date')
                ),
                'title',
                array(
                    'name' => 'type',
                    'filter' => $typeList,
                    'value' => '$data->getType()',
                    'htmlOptions' => array('class' => 'col-type'),
                ),
                array(
                    'name' => 'priority',
                    'filter' => $model->getPriorityList(),
                    'value' => '$data->getPriority()',
                    'htmlOptions' => array('class' => 'col-priority'),
                ),
                array(
                    'name' => 'start_date',
                    'type' => 'date',
                    'filter' => $startDateFilter->content,
                    'htmlOptions' => array('class' => 'col-date'),
                ),
                array(
                    'name' => 'end_date',
                    'type' => 'date',
                    'filter' => $endDateFilter->content,
                    'htmlOptions' => array('class' => 'col-date'),
                    'cssClassExpression' => '$data->expired ? "expired" : ""',
                ),
                array(
                    'name' => 'progress',
                    'value' => '$data->progress . " %"',
                    'htmlOptions' => array('class' => 'col-percent')
                ),
                array(
                    'name' => 'par_project_id',
                    'type' => 'html',
                    'filter' => CHtml::listData(Project::model()->findAllHierarchical($pCriteria), 'id', function($project) {
                              return str_pad($project->name, strlen($project->name) + 2 * $project->level, '- ', STR_PAD_LEFT);
                            }),
                    'value' => 'CHtml::link($data->project->name, array("project/viewTasks", "id" => $data->par_project_id))',
                    'htmlOptions' => array('class' => 'col-fixed no-wrap'),
                ),
            ),
        ));
        