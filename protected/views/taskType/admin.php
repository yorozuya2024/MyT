<?php
/* @var $this TaskTypeController */
/* @var $model TaskType */

$this->breadcrumbs = array(
    Yii::t('nav', 'Task Type')
);

$this->menu = array(
    array('label' => 'List Task Types', 'url' => array('admin')),
    array('label' => 'Create Task Type', 'url' => array('create')),
);
?>

<h2><?php
  echo Yii::t('nav', 'Task Type');

  $this->widget('ActionsWidget', array(
      'data' => $model,
      'template' => '{create}',
      'createButtonVisible' => 'Yii::app()->user->checkAccess("adminConfig")',
  ));
  ?>
</h2>

<?php
$this->widget('zii.widgets.grid.CGridView', array(
    'id' => 'task-type-grid',
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
            'class' => 'ext.myGridView.MyButtonColumn',
            'template' => '{update} {delete}',
            'afterDelete' => 'function(link,success,data){ if(success && !!data) alert(data); }',
        )
    ),
));
