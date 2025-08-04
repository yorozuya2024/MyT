<?php
/* @var $this ProjectController */
/* @var $model Project */
/* @var $user UserProject */

$source = Navigator::getProjectType() === 'all' ? 'indexAll' : 'index';
$label = ucfirst(Navigator::getProjectType());

$this->breadcrumbs = array(
    Yii::t('nav', $label . ' Projects') => array($source),
    Yii::t('nav', 'Create'),
);

$this->menu = array(
    array('label' => 'List Project', 'url' => array('index')),
    array('label' => 'Create Project', 'url' => array('create'), 'active' => true),
    array('label' => 'Manage Project', 'url' => array('admin')),
);
?>

<h1><?php echo Yii::t('nav', 'Create Project'); ?></h1>

<?php echo $this->renderPartial('_form', array('model' => $model, 'user' => $user)); ?>