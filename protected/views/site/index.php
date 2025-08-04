<?php
/* @var $this SiteController */

$this->pageTitle = Yii::app()->params['name'];
Navigator::clear();
Navigator::home(true);
?>

<!--<h1>Welcome <i><?php echo Yii::app()->user->name; ?></i></h1>-->

<div style="width:100%;background-color:#FFFFFF;" class="grid-view">
  <table>
    <tr style="vertical-align: top;">
      <td>
        <h2><?php echo Yii::t('nav', 'My Open Tasks'); ?></h2>
        <?php include '_myOpenTasks.php'; ?>
        <hr />
        <h2><?php echo Yii::t('nav', 'My Working On Tasks'); ?></h2>
        <?php include '_myWorkingTasks.php'; ?>
      </td>
      <td rowspan="10">
        <div class="projects">
          <h2><?php echo Yii::t('nav', 'My Projects'); ?></h2>
          <?php include '_myProjects.php'; ?>
        </div>
        <div class="online">
          <h2><?php echo Yii::t('nav', 'Users Online'); ?></h2>
          <?php include '_online.php'; ?>
        </div>
      </td>
    </tr>
  </table>
</div>
