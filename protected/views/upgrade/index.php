<?php
/* @var $this UpgradeController */
/* @var $upgrades array */

$this->breadcrumbs = array(
    'Upgrade',
);
?>

<h2>Upgrade</h2>

<?php foreach ($upgrades as $upgrade) {
    echo CHtml::tag('br'), CHtml::link($upgrade->name, Yii::app()->controller->createUrl($upgrade->action)), CHtml::tag('br');
}