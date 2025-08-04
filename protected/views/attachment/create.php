<?php
/* @var $this AttachmentController */
/* @var $model Attachment */
/* @var $taskProject string */

if ($taskProject === 'true') {
  $actionProject = Yii::app()->getGlobalState('Project') === 'All' ? 'indexAll' : 'index';
  $labelProject = Yii::app()->getGlobalState('Project') === 'All' ? 'All ' : 'My ';

  $this->breadcrumbs = array(
      Yii::t('nav', $labelProject . 'Projects') => array('project/' . $actionProject),
      Project::model()->findByPk($model->project_id)->name => array('/project/viewTasks', 'id' => $model->project_id),
      Task::model()->findByPk($model->task_id)->title => array('/taskProject/view', 'id' => $model->task_id, 'projectId' => $model->project_id),
      Yii::t('nav', 'Add Attachment'),
  );
} else {
  $source = Yii::app()->getGlobalState('Task') === 'All' ? 'indexAll' : 'index';
  $label = Yii::app()->getGlobalState('Task') === 'All' ? 'All ' : 'My ';

  $this->breadcrumbs = array(
      Yii::t('nav', $label . 'Tasks') => array('/task/' . $source),
      Task::model()->findByPk($model->task_id)->title => array('/task/view', 'id' => $model->task_id),
      Yii::t('nav', 'Add Attachment'),
  );
}

$this->menu = array(
    array('label' => 'List Attachment', 'url' => array('index')),
    array('label' => 'Manage Attachment', 'url' => array('admin')),
);
?>

<h1><?php echo Yii::t('nav', 'Add Attachment'); ?></h1>

<?php echo $this->renderPartial('_form', array('model' => $model)); ?>