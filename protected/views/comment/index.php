<?php
/* @var $this CommentController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	Yii::t('nav', 'Comments'),
);

$this->menu=array(
	array('label'=>'Create Comment', 'url'=>array('create')),
	array('label'=>'Manage Comment', 'url'=>array('admin')),
);
?>

<h1><?php echo Yii::t('nav', 'Comments'); ?></h1>
<?php


$this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
	'id' => 'commentList',
	'ajaxUpdate'=>true

)); 

?>
