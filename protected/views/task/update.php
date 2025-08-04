<?php
/* @var $this TaskController */
/* @var $model Task */

$source = Navigator::getTaskType() === 'all' ? 'indexAll' : 'index';
$label = ucfirst(Navigator::getTaskType());
Navigator::setTaskId($model->id);

$projectId = Navigator::getProjectId();
$projectBreadcrumb = empty($projectId) ? array() : array(
    Yii::t('nav', ucfirst(Navigator::getProjectType()) . ' Projects') => 'project/' . Navigator::getProjectType() === 'all' ? 'indexAll' : 'index',
    Project::model()->findByPk($projectId)->name => array('project/viewTasks', 'id' => $projectId, 'trkq' => 1)
);

$taskBreadcrumb = array(Yii::t('nav', $label . ' Tasks') => array($source, 'trkq' => 1));

$breadcrumb = empty($projectBreadcrumb) ? $taskBreadcrumb : $projectBreadcrumb;
$this->breadcrumbs = CMap::mergeArray($breadcrumb, array(
            $model->title => array('view', 'id' => $model->id),
            Yii::t('nav', 'Update'),
        ));

$this->menu = array(
    array('label' => 'List Task', 'url' => array('index')),
    array('label' => 'Create Task', 'url' => array('create')),
    array('label' => 'Manage Task', 'url' => array('admin')),
    array('label' => 'Update Task', 'url' => array('update', 'id' => $model->id), 'active' => true),
    array('label' => 'Delete Task', 'url' => '#', 'linkOptions' => array('submit' => array('delete', 'id' => $model->id),
            'confirm' => 'Are you sure you want to delete this item?')),
);
?>

<h1><?php echo Yii::t('nav', 'Update Task'); ?></h1>

<?php echo $this->renderPartial('_form', array('model' => $model, 'user' => $user)); ?>