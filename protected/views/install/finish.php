<?php
/* @var $this SiteController */
/* @var $model ContactForm */
/* @var $form CActiveForm */

$this->pageTitle = Yii::app()->name . ' - Installation Completed';
$this->breadcrumbs = array(
    'Database Configuration',
    'Database Creation',
    'Application Configuration',
    'Installation completed',
);
?>

<!-- 2022/07/07 modified -->
<!-- <h1>Installation Completed</h1> -->
<h1>インストールが完了しました。</h1>

<p>
  <!-- MyT Installation has been finished, here is the result -->
  MyTのインストールが完了しました。結果を示します。
</p>

<div class="form">

  <?php if (!empty($errors)): ?>
    <div class="flash-error">
      <strong>Installation not completed successfully:</strong>
      <ul>
        <?php foreach ($errors as $error): ?>
          <li><?php echo $error; ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php else: ?>
    <div class="flash-success">
      <!-- 2022/07/07 modified -->
      <!-- Congratulations! MyT was successfully installed, Welcome to Manage Your Team!. -->
      おめでとうございます。MyTのインストールが完了しました。Manage Your Teamへようこそ。
    </div>	
  <?php endif; ?>

  <?php if (!empty($errors)): ?>
    <span class="actions"><?php
      echo CHtml::link('Retry Step', array('finish')),
      CHtml::image('images/actions/database_refresh.png');
      ?></span>
  <?php else: ?>
    <span class="actions"><?php
      echo CHtml::link('Go To ' . $appName, array('/')),
      CHtml::image('images/accept.png');
      ?></span>
  <?php endif; ?>

</div><!-- form -->