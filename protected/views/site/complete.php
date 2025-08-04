<?php
/* @var $this SiteController */
/* @var $model LoginForm */
/* @var $form CActiveForm  */

$title = Yii::t('nav', 'Complete');
$this->pageTitle = Yii::app()->name . ' - ' . $title;
//$this->breadcrumbs=array(
//	$title,
//);
?>
<hr class="hr-separator">
<h1><?php echo $title; ?></h1>
<?php if (Yii::app()->user->hasFlash('success')): ?>

  <div class="flash-success">
    <?php echo Yii::app()->user->getFlash('success'); ?>
  </div>

<?php endif; ?>

</div>
