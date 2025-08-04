<?php
/* @var $this TaskStatusController */
/* @var $model TaskStatus */

$this->breadcrumbs = array(
    Yii::t('nav', 'Task Status') => array('admin'),
    Yii::t('nav', 'Create'),
);

$this->menu = array(
    array('label' => 'List Task Status', 'url' => array('admin')),
);
?>

<h1><?php echo Yii::t('nav', 'Create Task Status'); ?></h1>

<?php $this->renderPartial('_form', array('model' => $model)); ?>