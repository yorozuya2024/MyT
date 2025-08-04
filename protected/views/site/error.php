<?php
/* @var $this SiteController */
/* @var $error array */

$title = Yii::t('nav', 'Error');
$this->pageTitle = Yii::app()->name . ' - ' . $title;
$this->breadcrumbs = array(
    $title,
);
?>

<h2>Error <?php echo $code; ?></h2>

<div class="error">
  <?php echo CHtml::encode($message); ?>
</div>