<?php
/* @var $this TaskController */
/* @var $model Task */
/* @var $user UserTask */
/* @var $projectId integer */

$source = Navigator::getTaskType() === 'all' ? 'indexAll' : 'index';
$label = ucfirst(Navigator::getTaskType());

$projectId = Navigator::getProjectId();
$projectBreadcrumb = empty($projectId) ? array() : array(
    Yii::t('nav', ucfirst(Navigator::getProjectType()) . ' Projects') => 'project/' . Navigator::getProjectType() === 'all' ? 'indexAll' : 'index',
    Project::model()->findByPk($projectId)->name => array('project/viewTasks', 'id' => $projectId, 'trkq' => 1)
);
$taskBreadcrumb = array(Yii::t('nav', $label . ' Tasks') => array($source, 'trkq' => 1));

$breadcrumb = empty($projectBreadcrumb) ? $taskBreadcrumb : $projectBreadcrumb;
$this->breadcrumbs = CMap::mergeArray($breadcrumb, array(
            Yii::t('nav', 'Create'),
        ));

$this->menu = array(
    array('label' => 'List Task', 'url' => array('index')),
    array('label' => 'Create Task', 'url' => array('create'), 'active' => true),
    array('label' => 'Manage Task', 'url' => array('admin')),
);
?>

<h1><?php echo Yii::t('nav', 'Create Task'); ?></h1>

<?php echo $this->renderPartial('_form', array('model' => $model, 'user' => $user, 'projectId' => $projectId)); ?>