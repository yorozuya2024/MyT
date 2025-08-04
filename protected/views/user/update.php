<?php
/* @var $this UserController */
/* @var $model User */

$this->breadcrumbs = array(
    Yii::t('nav', 'Users') => array('index'),
    $model->username => array('view', 'id' => $model->id),
    Yii::t('nav', 'Update'),
);

$this->menu = array(
    array('label' => 'List User', 'url' => array('index')),
    array('label' => 'Create User', 'url' => array('create')),
    array('label' => 'Manage User', 'url' => array('admin')),
    array('label' => 'Update User', 'url' => array('update', 'id' => $model->id), 'active' => true),
    array('label' => 'Delete User', 'url' => '#', 'linkOptions' => array('submit' => array('delete', 'id' => $model->id), 'confirm' => 'Are you sure you want to delete this item?')),
);
?>

<h1><?php echo Yii::t('nav', 'Update User {name}', array('{name}' => $model->username)); ?></h1>

<?php echo $this->renderPartial('_form', array('model' => $model)); ?>