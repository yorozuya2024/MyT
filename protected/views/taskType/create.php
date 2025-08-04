<?php
/* @var $this TaskTypeController */
/* @var $model TaskType */

$this->breadcrumbs = array(
    Yii::t('nav', 'Task Type') => array('admin'),
    Yii::t('nav', 'Create'),
);

$this->menu = array(
    array('label' => 'List Task Types', 'url' => array('admin')),
);
?>

<h1><?php echo Yii::t('nav', 'Create Task Type'); ?></h1>

<?php $this->renderPartial('_form', array('model' => $model)); ?>