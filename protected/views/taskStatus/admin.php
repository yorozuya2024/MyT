<?php
/* @var $this TaskStatusController */
/* @var $model TaskStatus */

$this->breadcrumbs = array(
    Yii::t('nav', 'Task Status')
);

$this->menu = array(
    array('label' => 'List Task Status', 'url' => array('admin')),
    array('label' => 'Create Task Status', 'url' => array('create')),
);
?>

<h2><?php
  echo Yii::t('nav', 'Task Status');

  $this->widget('ActionsWidget', array(
      'data' => $model,
      'template' => '{create}',
      'createButtonVisible' => 'Yii::app()->user->checkAccess("adminConfig")',
  ));
  ?>
</h2>

<?php
$this->widget('zii.widgets.grid.CGridView', array(
    'id' => 'task-status-grid',
    'dataProvider' => $model->search(),
    'filter' => $model,
    'columns' => array(
        'name',
        'order_by:number',
        array(
            'name' => 'default_flg',
            'type' => 'boolean',
            'filter' => array(Yii::app()->format->boolean(0), Yii::app()->format->boolean(1)),
            'htmlOptions' => array('class' => 'col-date')
        ),
        array(
            'name' => 'active_flg',
            'type' => 'boolean',
            'filter' => array(Yii::app()->format->boolean(0), Yii::app()->format->boolean(1)),
            'htmlOptions' => array('class' => 'col-date')
        ),
        array(
            'name' => 'group_id',
            'filter' => $model->groupList,
            'value' => '$data->groupList[$data->group_id]',
        ),
        array(
            'class' => 'ext.myGridView.MyButtonColumn',
            'template' => '{update} {delete}',
            'afterDelete' => 'function(link,success,data){ if(success && !!data) alert(data); }',
        )
    ),
));
