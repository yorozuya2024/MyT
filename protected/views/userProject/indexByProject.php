<?php
/* @var $this UserProjectController */
/* @var $dataProvider CActiveDataProvider */
/* @var $projectId String */

$source = Yii::app()->user->getState('Project') === 'All' ? 'indexAll' : 'index';
$label = Yii::app()->user->getState('Project') === 'All' ? 'All ' : 'My ';

$this->breadcrumbs = array(
    Yii::t('nav', $label . 'Projects') => array($source),
    Project::model()->findByPk($projectId)->name => array('project/view', 'id' => $projectId),
    Yii::t('nav', 'Users'),
);

$this->menu = array(
    array('label' => 'List Users', 'url' => array('indexByProject', 'projectId' => $projectId), 'active' => true),
    array('label' => 'Associate New User', 'url' => array('createByProject', 'projectId' => $projectId)),
    array('label' => 'Manage Users', 'url' => array('adminByProject', 'projectId' => $projectId)),
);
?>

<h2><?php Yii::t('nav', 'Users'); ?></h2>

<?php
$this->widget('zii.widgets.CListView', array(
    'dataProvider' => $dataProvider,
    'itemView' => '_viewByProject'
));

