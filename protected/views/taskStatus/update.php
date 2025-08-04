<?php
/* @var $this TaskStatusController */
/* @var $model TaskStatus */

$this->breadcrumbs = array(
    Yii::t('nav', 'Task Status') => array('admin'),
    $model->name,
);

$this->menu = array(
    array('label' => 'List Task Status', 'url' => array('admin')),
    array('label' => 'Create Task Status', 'url' => array('create')),
);
?>

<h1><?php echo Yii::t('nav', 'Update Task Status "{name}"', array('{name}' => $model->name)); ?></h1>

<?php $this->renderPartial('_form', array('model' => $model)); ?>