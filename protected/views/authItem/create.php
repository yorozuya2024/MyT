<?php
/* @var $this AuthItemController */
/* @var $model AuthItem */

$this->breadcrumbs = array(
    Yii::t('nav', 'Roles') => array('index'),
    Yii::t('nav', 'Create'),
);

$this->menu = array(
    array('label' => 'List AuthItem', 'url' => array('index')),
    array('label' => 'Manage AuthItem', 'url' => array('admin')),
);
?>

<h1><?php echo Yii::t('nav', 'Create Role'); ?></h1>

<?php echo $this->renderPartial('_form', array('model' => $model)); ?>