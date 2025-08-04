<?php
/* @var $this SiteController */
/* @var $model ContactForm */
/* @var $form CActiveForm */

$this->pageTitle = Yii::app()->name . ' - Upgrade Completed';
$this->breadcrumbs = array(
    'Upgrade completed',
);
?>

<h1>Upgrade Completed</h1>

<p>
  MyT Upgrade completed, here is the result:
</p>

<div class="form">

  <?php if ($error): ?>
    <div class="flash-error">
      <strong>MyT Upgrade not completed successfully:</strong>
    </div>
  <?php else: ?>
    <div class="flash-success">
      Congratulations! MyT was successfully upgraded to version <?php echo $version; ?>, Enjoy your new version of Manage Your Team!.
    </div>	
  <?php endif; ?>
  
  <ul>
    <?php foreach ($messages as $v => $m): ?>
	  <li><?php echo $v; ?></li>
	  <ul>
		<?php foreach( $m as $message ): ?>
		<li><?php echo $message[0], ' ', $message[1] == true ? CHtml::image(Yii::app()->request->baseUrl.'/images/cross.png') : CHtml::image(Yii::app()->request->baseUrl.'/images/tick.png'); ?></li>
		<?php endforeach; ?>
	  </ul>
    <?php endforeach; ?>
  </ul>

  <?php if ($error): ?>
    <span class="actions"><?php
      echo CHtml::link('Retry Step', array('upgrade')),
      CHtml::image(Yii::app()->request->baseUrl.'/images/actions/database_refresh.png');
      ?></span>
  <?php else: ?>
    <span class="actions"><?php
      echo CHtml::link('Go To ' . Yii::app()->name, array('/')),
      CHtml::image(Yii::app()->request->baseUrl.'/images/accept.png');
      ?></span>
  <?php endif; ?>

</div><!-- form -->