<?php
/* @var $this UserController */
/* @var $model User */

$this->breadcrumbs = array(
    Yii::t('nav', 'Users') => array('index'),
    Yii::t('nav', 'Create'),
);

$this->menu = array(
    array('label' => 'List User', 'url' => array('index')),
    array('label' => 'Create User', 'url' => array('create'), 'active' => true),
    array('label' => 'Manage User', 'url' => array('admin')),
);
?>

<h1><?php echo Yii::t('nav', 'Create User'); ?></h1>

<?php echo $this->renderPartial('_form', array('model' => $model)); ?>