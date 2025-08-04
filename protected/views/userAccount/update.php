<?php
/* @var $this UserController */
/* @var $model User */

$this->breadcrumbs = array(
    $model->username => array('view', 'id' => $model->id),
    Yii::t('nav', 'Update'),
);
?>

<h1><?php echo Yii::t('nav', 'Update User {name}', array('{name}' => $model->username)); ?></h1>

<?php echo $this->renderPartial('_form', array('model' => $model)); ?>