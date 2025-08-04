<?php
/* @var $this TaskController */
/* @var $model Task */

$source = Yii::app()->user->getState('Task') === 'All' ? 'indexAll' : 'index';
$label = Yii::app()->user->getState('Task') === 'All' ? 'All ' : 'My ';

$this->breadcrumbs = array(
    Yii::t('nav', $label . 'Tasks') => array($source),
    Yii::t('nav', 'Manage'),
);

$this->menu = array(
    array('label' => 'List Task', 'url' => array('index')),
    array('label' => 'Create Task', 'url' => array('create')),
    array('label' => 'Manage Task', 'url' => array('admin'), 'active' => true),
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$('#task-grid').yiiGridView('update', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1><?php echo Yii::t('nav', 'Manage Tasks'); ?></h1>

<?php echo CHtml::link('Advanced Search', '#', array('class' => 'search-button')); ?>
<div class="search-form" style="display:none">
  <p>
    You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>
    or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.
  </p>
  <?php
  $this->renderPartial('_search', array(
      'model' => $model,
  ));
  ?>
</div><!-- search-form -->

<?php
$this->beginWidget('CActiveForm', array(
    'id' => 'task-form',
    'enableAjaxValidation' => false,
));

$title = Yii::t('app', 'Task.all.export.{date}', array(
            '{date}' => date('Ymd')
        ));

$this->widget('ext.EExcelView', array(
    'title' => $title,
    'filename' => $title,
    'selectableRows' => 2,
    'id' => 'task-grid',
    'dataProvider' => $model->search(),
    'filter' => $model,
    'columns' => array(
        array(
            'name' => 'calc_id',
            'type' => 'html',
            'value' => function($data) {
              return CHtml::link($data->calc_id, array('task/view', 'id' => $data->id));
            }
                ),
                'title',
                array(
                    'name' => 'owner',
                    'type' => 'html',
                    'sortable' => false,
                    'value' => function($data) {
                      $users = array();
                      $assoc_rows = UserTask::model()->with(array(
                                  'user' => array('select' => 'username'))
                              )->findAllByAttributes(array('task_id' => $data->id));
                      foreach ($assoc_rows as $row)
                        $users[] = $row->user->username;
                      return nl2br(implode(PHP_EOL, $users));
                    }
                        ),
                        array(
                            'name' => 'priority',
                            'filter' => $model->getPriorityList(),
                            'value' => '$data->getPriority()'
                        ),
                        array(
                            'name' => 'start_date',
                            'type' => 'date',
                            'htmlOptions' => array('class' => 'col-date')
                        ),
                        array(
                            'name' => 'end_date',
                            'type' => 'date',
                            'htmlOptions' => array('class' => 'col-date')
                        ),
                        array(
                            'name' => 'status',
                            'filter' => $model->getStatusList(),
                            'value' => '$data->getStatus()'
                        ),
                        array(
                            'name' => 'progress',
                            'value' => '$data->progress . " %"',
                            'htmlOptions' => array('class' => 'col-percent')
                        ),
                        array(
                            'name' => 'par_project_id',
                            'type' => 'html',
                            'filter' => CHtml::listData(Project::model()->findAll(), 'id', 'name'),
                            'value' => 'CHtml::link($data->project->name, array("project/viewTasks", "id" => $data->par_project_id))',
                            'htmlOptions' => array('class' => 'no-wrap'),
                        ),
                        array(
                            'class' => 'ext.myGridView.MyButtonColumn',
                            'buttons' => array(
                                'update' => array('visible' => 'Yii::app()->user->checkAccess("updateTask")'),
                                'delete' => array('visible' => 'Yii::app()->user->checkAccess("deleteTask")'),
                            ),
                        ),
                    ),
                ));

                $this->endWidget();
