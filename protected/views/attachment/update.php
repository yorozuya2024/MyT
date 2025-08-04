<?php
/* @var $this AttachmentController */
/* @var $model Attachment */

if ($taskProject === 'true') {
  $actionProject = Yii::app()->getGlobalState('Project') === 'All' ? 'indexAll' : 'index';
  $labelProject = Yii::app()->getGlobalState('Project') === 'All' ? 'All ' : 'My ';

  $this->breadcrumbs = array(
      Yii::t('nav', $labelProject . 'Projects') => array('project/' . $actionProject),
      Project::model()->findByPk($model->project_id)->name => array('/project/viewTasks', 'id' => $model->project_id),
      Task::model()->findByPk($model->task_id)->title => array('/taskProject/view', 'id' => $model->task_id, 'projectId' => $model->project_id),
      $model->name,
  );
} else {
  $source = Yii::app()->getGlobalState('Task') === 'All' ? 'indexAll' : 'index';
  $label = Yii::app()->getGlobalState('Task') === 'All' ? 'All ' : 'My ';

  $this->breadcrumbs = array(
      Yii::t('nav', $label . 'Tasks') => array('/task/' . $source),
      Task::model()->findByPk($model->task_id)->title => array('/task/view', 'id' => $model->task_id),
      $model->name,
  );
}

$this->menu = array(
    array('label' => 'List Attachment', 'url' => array('index')),
    array('label' => 'Create Attachment', 'url' => array('create')),
    array('label' => 'View Attachment', 'url' => array('view', 'id' => $model->id)),
    array('label' => 'Manage Attachment', 'url' => array('admin')),
);
?>

<h1><?php echo Yii::t('nav', 'Update Attachment'), ': ', $model->name; ?></h1>

<?php echo $this->renderPartial('_form', array('model' => $model)); ?>