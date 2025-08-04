<?php
/* @var $this AuthItemController */
/* @var $model AuthItem */

$this->breadcrumbs = array(
    Yii::t('nav', 'Roles') => array('index'),
    Yii::t('nav', 'Operations'),
);

$this->menu = array(
    array('label' => 'List AuthItem', 'url' => array('index')),
    array('label' => 'Create AuthItem', 'url' => array('create')),
);
?>

<h1><?php echo Yii::t('nav', 'Operations'); ?></h1>

<?php
$this->widget('zii.widgets.grid.CGridView', array(
    'id' => 'auth-item-grid',
    'dataProvider' => $model->searchOperation(),
    'filter' => $model,
    'columns' => array(
        'name',
        'description',
    ),
));
