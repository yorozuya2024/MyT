<?php
/* @var $this SiteController */

$this->pageTitle = Yii::app()->name . ' - Database Creation';
$this->breadcrumbs = array(
    'Database Configuration',
    'Database Creation'
);

$error = false;
?>

<!-- 2022/07/07 modified -->
<!-- <h1>Database Creation</h1> -->
<h1>データベース作成</h1>

<!-- 2022/07/07 modified -->
<!-- Database configuration worked, now it's time to create the database: -->
<p>
  データベースの構成が完了したので、データベースを作成します。

</p>

<div class="form">
  <?php if (Yii::app()->user->hasFlash('install-error')): ?>
    <div class="flash-error">
      <?php $error = true; ?>
      <!-- 2022/07/07 modified -->
      <!-- <strong>Error during database creation:</strong> -->
      <strong>データベース作成中のエラー:</strong>
      <p><?php echo Yii::app()->user->getFlash('install-error'); ?></p>
    </div>
  <?php else: ?>
    <div class="flash-success">
      <!-- 2022/07/07 modified -->
      <!-- Database succesfully created, click 'Next' to proceed with the installation. -->
      データベースが正常に作成されたら、[次へ]をクリックしてインストールを続行します。
    </div>	
  <?php endif; ?>

  <ul>
    <?php foreach ($messages as $message): ?>
      <li><?php echo $message; ?></li>
    <?php endforeach; ?>
  </ul>

  <?php if (!$error): ?>
    <span class="actions"><?php
      //2022/07/07 modified
      //echo CHtml::link('Next &gt;&gt;', array('ConfigureApp')),
      echo CHtml::link('次へ &gt;&gt;', array('ConfigureApp')),
      CHtml::image('images/actions/server_add.png');
      ?></span>
  <?php endif; ?>

</div>