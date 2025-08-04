<?php
/* @var $this CommentController */
/* @var $model Comment */

$this->breadcrumbs=array(
	Yii::t('nav', 'Comments') => array('index'),
	$model->id=>array('view','id'=>$model->id),
	Yii::t('nav', 'Update'),
);

$this->menu=array(
	array('label'=>'List Comment', 'url'=>array('index')),
	array('label'=>'Create Comment', 'url'=>array('create')),
	array('label'=>'View Comment', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage Comment', 'url'=>array('admin')),
);
?>

<h1><?php echo Yii::t('nav', 'Update Comment'); ?></h1>

<?php $this->renderPartial('_form', array('model'=>$model, 'entity' => $model->entity, 'entityId' => $model->entity_id, 'handler' => '')); ?>