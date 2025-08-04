<?php

$workingModel = new Task('search');
$workingModel->unsetAttributes();
if (isset($_GET['Task']))
  $workingModel->attributes = $_GET['Task'];

$open = TaskStatus::getStatusIdByGroup(Yii::t('constants', 'TaskStatus.group.open'));
$typeList = CHtml::listData(TaskType::model()->active()->findAll(), 'id', 'name');

$startDateFilter = $this->createWidget('ext.datepicker.EJuiDatePicker', array(
    'model' => $workingModel,
    'attribute' => 'start_date',
    'key' => 'wip_4VwKa1Im_',
    'htmlOptions' => array(
        'class' => 'monthpicker',
    ),
        )
);

$endDateFilter = $this->createWidget('ext.datepicker.EJuiDatePicker', array(
    'model' => $workingModel,
    'attribute' => 'end_date',
    'key' => 'wip_4VwKa1Im_',
    'htmlOptions' => array(
        'class' => 'monthpicker',
    ),
        )
);

$this->widget('ext.HierGridView', array(
    'id' => 'working-on-task-grid',
    'dataProvider' => $workingModel->searchMyHierarchical($open[1]),
    'filter' => $workingModel,
    'treeColumn' => 1,
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
        