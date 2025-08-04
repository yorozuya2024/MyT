<?php
/* @var $this UpgradeController */
/* @var $upgrade Object */
/* @var $errors CActiveRecord[] */
/* @var $success string */

$this->breadcrumbs = array(
    'Upgrade' => array('index'),
    $upgrade->name
);
?>

<h2>Upgrade <?php echo $upgrade->name; ?></h2>

<?php echo CHtml::tag('br'), CHtml::errorSummary($errors), CHtml::tag('br'), CHtml::tag('p', array(), $success); ?>