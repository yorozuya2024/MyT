<?php
/* @var $this UserProjectController */
/* @var $model UserProject */
/* @var $projectId String */

$source = Yii::app()->user->getState('Project') === 'All' ? 'indexAll' : 'index';
$label = Yii::app()->user->getState('Project') === 'All' ? 'All ' : 'My ';
$projectName = Project::model()->findByPk($projectId)->name;

$this->breadcrumbs = array(
    Yii::t('nav', $label . 'Projects') => array($source),
    $projectName => array('project/view', 'id' => $projectId),
    Yii::t('nav', 'Users')
);

$this->menu = array(
    array('label' => 'List Users', 'url' => array('indexByProject', 'projectId' => $projectId)),
    array('label' => 'Associate New User', 'url' => array('createByProject', 'projectId' => $projectId)),
    array('label' => 'Manage Users', 'url' => array('adminByProject', 'projectId' => $projectId), 'active' => true),
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$('#user-project-grid').yiiGridView('update', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1><?php echo Yii::t('nav', 'Manage Users'); ?></h1>

<?php echo CHtml::link(Yii::t('app', 'Advanced Search'), '#', array('class' => 'search-button')); ?>
<div class="search-form" style="display:none">
  <p>
    You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>
    or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.
  </p>
  <?php
  $this->renderPartial('_searchByProject', array(
      'model' => $model,
  ));
  ?>
</div><!-- search-form -->

<?php
$this->beginWidget('CActiveForm', array(
    'id' => 'user-project-form',
    'enableAjaxValidation' => false,
));

$title = Yii::t('app', 'Project.User.export.{project}.{date}', array(
            '{project}' => $projectName,
            '{date}' => date('Ymd')));

$this->widget('ext.EExcelView', array(
    'title' => $title,
    'filename' => $title,
    'selectableRows' => 2,
    'id' => 'user-project-grid',
    'dataProvider' => $model->searchByProject($projectId),
    'filter' => $model,
    'columns' => array(
        array(
            'name' => 'user_id',
            'filter' => CHtml::listData(User::model()->findAll(), 'id', 'username'),
            'value' => '$data->user->username'
        ),
        array(
            'name' => 'rollon_date',
            'type' => 'date',
            'htmlOptions' => array('class' => 'col-date')
        ),
        array(
            'name' => 'rolloff_date',
            'type' => 'date',
            'htmlOptions' => array('class' => 'col-date')
        ),
        array(
            'class' => 'ext.myGridView.MyButtonColumn',
            'buttons' => array(
                'update' => array('visible' => 'Yii::app()->user->checkAccess("updateProject)'),
                'delete' => array('visible' => 'Yii::app()->user->checkAccess("updateProject")'),
            ),
        ),
    ),
));

$this->endWidget();
