<?php
/* @var $this UserProjectController */
/* @var $model UserProject */
/* @var $projectId String */

$source = Yii::app()->user->getState('Project') === 'All' ? 'indexAll' : 'index';
$label = Yii::app()->user->getState('Project') === 'All' ? 'All ' : 'My ';

$this->breadcrumbs = array(
    Yii::t('nav', $label . 'Projects') => array($source),
    Project::model()->findByPk($projectId)->name => array('project/view', 'id' => $projectId),
    Yii::t('nav', 'Users') => array('indexByProject', 'projectId' => $projectId),
    Yii::t('nav', 'Create'),
);

$this->menu = array(
    array('label' => 'List Users', 'url' => array('indexByProject', 'projectId' => $projectId)),
    array('label' => 'Associate New User', 'url' => array('createByProject', 'projectId' => $projectId), 'active' => true),
    array('label' => 'Manage Users', 'url' => array('adminByProject', 'projectId' => $projectId)),
);
?>

<h1><?php echo Yii::t('nav', 'Associate New User'); ?></h1>

<?php echo $this->renderPartial('_formByProject', array('model' => $model, 'projectId' => $projectId)); ?>