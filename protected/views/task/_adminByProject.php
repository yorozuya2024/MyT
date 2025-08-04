<?php

/* @var $this ProjectController */
/* @var $project Project */
/* @var $tasks Task */
?>

<?php

$buttons = '{view}';
if (Yii::app()->user->checkAccess('updateTask'))
  $buttons .= ' {update}';
if (Yii::app()->user->checkAccess('deleteTask'))
  $buttons .= ' {delete}';
$statusList = CHtml::listData(TaskStatus::model()->active()->findAll(), 'id', 'name', 'group');
$typeList = CHtml::listData(TaskType::model()->active()->findAll(), 'id', 'name');

$startDateFilter = $this->createWidget('ext.datepicker.EJuiDatePicker', array(
    'model' => $tasks,
    'attribute' => 'start_date',
    'key' => 'filter_6uUOJ2Us_',
    'htmlOptions' => array(
        'class' => 'monthpicker',
    ),
        )
);

$endDateFilter = $this->createWidget('ext.datepicker.EJuiDatePicker', array(
    'model' => $tasks,
    'attribute' => 'end_date',
    'key' => 'filter_6uUOJ2Us_',
    'htmlOptions' => array(
        'class' => 'monthpicker',
    ),
        )
);

$this->beginWidget('CActiveForm', array(
    'id' => 'task-form',
    'enableAjaxValidation' => false,
));

$title = Yii::t('app', 'Task.export.{project}.{date}', array(
            '{project}' => $project->name,
            '{date}' => date('Ymd')
        ));

$this->widget('ext.HierTaskExcelView', array(
    'title' => $title,
    'filename' => $title,
    'selectableRows' => 2,
    'id' => 'task-grid',
//    'dataProvider' => $tasks->searchByProject($project->id),
    'dataProvider' => $tasks->searchByProjectHierarchical($project->id),
    'filter' => $tasks,
    'afterAjaxUpdate' => "function(id, data){
        {$startDateFilter->js}
        {$endDateFilter->js}
        jQuery('#Task_status').multiselect({
            selectedList: 2
        }); //.multiselectfilter();
        $('#' + id + ' .items').treeTable({treeColumn:2});
    }",
    'columns' => array(
        array(
            'name' => 'calc_id',
            'type' => 'html',
            'value' => function($data) {
              return CHtml::link($data->calc_id, array('task/view', 'id' => $data->id));
            },
                    'htmlOptions' => array('class' => 'col-date'),
                ),
                'title',
                array(
                    'name' => 'owner',
                    'type' => 'html',
                    'value' => function($data) {
                      $users = array();
//                $assoc_rows = UserTask::model()->with(array(
//                            'user' => array('select' => 'username'))
//                        )->findAllByAttributes(array('task_id' => $data->id));
                      $assoc_rows = $data->users;
                      foreach ($assoc_rows as $row)
                        $users[] = $row->username;
                      return nl2br(implode(PHP_EOL, $users));
                    },
                            'htmlOptions' => array('class' => 'col-fixed'),
                        ), /*
                          array(
                          'name' => 'author',
                          'value' => '$data->creator->username',
                          'htmlOptions' => array('class' => 'col-fixed'),
                          ), */
                        array(
                            'name' => 'type',
                            'filter' => $typeList,
                            'value' => '$data->getType()',
                            'htmlOptions' => array('class' => 'col-type'),
                        ),
                        array(
                            'name' => 'priority',
                            'filter' => $tasks->getPriorityList(),
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
                            'name' => 'status',
                            'filter' => CHtml::activeDropDownList($tasks, 'status', $statusList, array('multiple' => true, 'style' => 'display:none')),
                            'value' => '$data->getStatus()',
                        ),
                        array(
                            'name' => 'progress',
                            'value' => '$data->progress . " %"',
                            'htmlOptions' => array('class' => 'col-percent')
                        ),
                        array(
                            'class' => 'ext.myGridView.MyButtonColumn',
                            'template' => $buttons,
                            'viewButtonUrl' => 'array("task/view", "id" => $data->id)',
                            'deleteButtonUrl' => 'array("task/delete", "id" => $data->id)',
                            'updateButtonUrl' => 'array("task/update", "id" => $data->id)',
                        ),
                    ),
                ));

                $this->endWidget();

                $massive = new TaskMassiveForm;
                $this->beginWidget('zii.widgets.jui.CJuiDialog', array(
                    'id' => 'task-massive-update',
                    'htmlOptions' => array('style' => 'display:none'),
                    // additional javascript options for the dialog plugin
                    'options' => array(
                        'title' => Yii::t('nav', 'Update Tasks'),
                        'autoOpen' => false,
                        'dialogClass' => 'no-close',
                        'buttons' => array(
                            'Ok' => 'js:function() {
                            $.post(
                                $("#task-massive-form").attr("action"),
                                $("#task-massive-form").serialize(),
                                function(){$("#task-grid").yiiGridView("update");}
                            );
                            $(this).dialog("close");
                        }',
                            'Cancel' => 'js:function(){$(this).dialog("close");}',
                        ),
                    ),
                ));

                $this->renderPartial('/task/_formMassive', array('model' => $massive));

                $this->endWidget('zii.widgets.jui.CJuiDialog');

                $multiselectFolder = Yii::app()->baseUrl . '/js/multiselect/';
                Yii::app()->clientScript->registerScriptFile($multiselectFolder . 'jquery.multiselect.min.js', CClientScript::POS_END);
//Yii::app()->clientScript->registerScriptFile($multiselectFolder . 'jquery.multiselect.filter.js', CClientScript::POS_END);
                Yii::app()->clientScript->registerCSSFile($multiselectFolder . 'jquery.multiselect.css');
//Yii::app()->clientScript->registerCSSFile($multiselectFolder . 'jquery.multiselect.filter.css');
                Yii::app()->clientScript->registerScript('multiselect.filter', '
    $("#Task_status").multiselect({
        selectedList: 2
    }); //.multiselectfilter();
', CClientScript::POS_READY);
                