<?php
/* @var $this AuthItemController */
/* @var $model AuthItem */

$this->breadcrumbs = array(
    Yii::t('nav', 'Roles') => array('index'),
    Yii::t('nav', 'Update'),
);
?>

<h1><?php echo Yii::t('nav', 'Update Role'), ': ', $model->name; ?></h1>

<?php echo $this->renderPartial('_form', array('model' => $model)); ?>