<?php
/* @var $this TaskTypeController */
/* @var $model TaskType */

$this->breadcrumbs = array(
    Yii::t('nav', 'Task Type') => array('admin'),
    $model->name,
);

$this->menu = array(
    array('label' => 'List Task Types', 'url' => array('admin')),
    array('label' => 'Create Task Type', 'url' => array('create')),
);
?>

<h1><?php echo Yii::t('nav', 'Update Task Type "{name}"', array('{name}' => $model->name)); ?></h1>

<?php $this->renderPartial('_form', array('model' => $model)); ?>