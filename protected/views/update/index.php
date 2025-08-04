<?php
/* @var $this SiteController */

$this->pageTitle = Yii::app()->params['name'];
?>

<h1>Welcome to MyT Upgrader</h1>

<p>
  The following wizard will help you to update your MyT Installation
</p>

<div class="form">
  <?php if (!$error): ?>
    <div class="flash-success">
      Congratulations, you meet all minimum requirements to upgrade your MyT <?php echo $currVersion; ?> to <?php echo $latestVersion; ?>!
    </div>
  <?php else: ?>
    <div class="flash-error">
      Sorry, you don't meet the minimum requirements to upgrade your MyT <?php echo $currVersion; ?> to <?php echo $latestVersion; ?>, please check your configuration or post on our <a href="http://forum.manageyourteam.net">Support Forum</a>.
    </div>
  <?php endif; ?>

  <table>
    <thead>
      <tr>
        <th colspan="3">Requirements Check</th>
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
      echo CHtml::link('Next &gt;&gt;', array('upgrade')),
      CHtml::image('images/actions/server_add.png');
      ?></span>
  <?php endif; ?>

</div>