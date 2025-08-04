<?php
/* @var $this SiteController */

// 2024/10/11 debug
// すべてのエラーを表示
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


$this->pageTitle = Yii::app()->params['name'];
?>

<!-- 2022/07/07 modified -->
<h1>ようこそ<?php echo Yii::app()->params['name']; ?></h1>
<!-- <h1>Welcome to <?php echo Yii::app()->params['name']; ?></h1> -->

<!-- 2022/07/07 modified -->
<!--   Please configure the database that MyT will use, this information can usually be obtained from your webhost. -->
<p>
  MyTが使用するデータベースを構成してください。この情報は通常、ウェブホストから取得できます。
</p>

<div class="form">
  <?php if (!$error): ?>
    <!-- 2022/07/07 modified -->
    <!-- Congratulations, you meet all minimum requirements to install MyT! -->
    <div class="flash-success">
      おめでとうございます。MyTをインストールするためのすべての最小要件を満たしています。
    </div>
  <?php else: ?>
    <div class="flash-error">
      申し訳ありませんが、MyTをインストールするための最小要件を満たしていません。サーバの構成を確認してください。
    </div>
  <?php endif; ?>

  <table>
    <thead>
      <tr>
        <!-- 2022/07/07 modified -->
        <!-- Requirements Check -->
        <th colspan="3">動作要件確認</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($requirements as $req): ?>
        <tr>
          <td><?php echo $req[0]; ?></td>
          <?php if ($req[1] && !$req[2]): ?>
            <td><?php echo $req[3]; ?></td>
            <td><?php echo CHtml::image('images/cross.png'); ?></td>
          <?php elseif (!$req[1] && !$req[2]): ?>
            <td><?php echo $req[3]; ?></td>
            <td><?php echo CHtml::image('images/warn.png'); ?></td>
          <?php else: ?>
            <td>&nbsp;</td>
            <td><?php echo CHtml::image('images/tick.png'); ?>
            <?php endif; ?>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <?php if (!$error): ?>
    <span class="actions"><?php
      //2022/07/07 modified
      //echo CHtml::link('Next &gt;&gt;', array('configureDatabase')),
      echo CHtml::link('次へ &gt;&gt;', array('configureDatabase')),
      CHtml::image('images/actions/server_add.png');
      ?></span>
  <?php endif; ?>

</div>