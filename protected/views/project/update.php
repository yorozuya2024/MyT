<?php
/* @var $this ProjectController */
/* @var $model Project */
/* @var $user UserProject */

$source = Navigator::getProjectType() === 'all' ? 'indexAll' : 'index';
$label = ucfirst(Navigator::getProjectType());
Navigator::setProjectId($model->id);

$this->breadcrumbs = array(
    Yii::t('nav', $label . ' Projects') => array($source),
    $model->name => array('view', 'id' => $model->id),
    Yii::t('nav', 'Update'),
);

$this->menu = array(
    array('label' => 'List Project', 'url' => array('index')),
    array('label' => 'Create Project', 'url' => array('create')),
    array('label' => 'Manage Projects', 'url' => array('admin')),
    array('label' => 'Update Project', 'url' => array('update', 'id' => $model->id), 'active' => true),
    array('label' => 'Delete Project', 'url' => '#', 'linkOptions' => array('submit' => array('delete', 'id' => $model->id), 'confirm' => 'Are you sure you want to delete this item?')),
    array('label' => 'View Project', 'url' => array('project/view', 'id' => $model->id)),
    array('label' => 'Tasks', 'url' => array('project/viewTasks', 'id' => $model->id), 'items' => array(
            array('label' => 'Create Task', 'url' => array('task/create', 'projectId' => $model->id)),
        )),
    array('label' => 'Users', 'url' => array('userProject/indexByProject', 'projectId' => $model->id), 'items' => array(
            array('label' => 'Manage Users', 'url' => array('userProject/adminByProject', 'projectId' => $model->id)),
            array('label' => 'Associate New User', 'url' => array('userProject/createByProject', 'projectId' => $model->id)),
        )
    ),
);
?>

<h1><?php echo Yii::t('nav', 'Update Project'), ': ', $model->name; ?></h1>

<?php echo $this->renderPartial('_form', array('model' => $model, 'user' => $user)); ?>