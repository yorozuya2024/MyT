<?php
/* @var $this Controller */
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo Yii::app()->language; ?>" lang="<?php echo Yii::app()->language; ?>">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="language" content="<?php echo Yii::app()->language; ?>" />

    <!-- blueprint CSS framework -->
    <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/screen.css" media="screen, projection" />
    <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/print.css" media="print" />
    <!--[if lt IE 8]>
    <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/ie.css" media="screen, projection" />
    <![endif]-->

    <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/main.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/form.css" />

    <title><?php echo CHtml::encode($this->pageTitle); ?></title>
  </head>

  <body>

    <div class="container" id="page">

      <div id="header">
        <div id="logo"><?php echo CHtml::encode(Yii::app()->name); ?></div>
      </div><!-- header -->

      <div id="mainmenu">
<?php
$this->widget('zii.widgets.CMenu', array(
    'lastItemCssClass' => 'right',
    'items' => array(
        array('label' => 'Home', 'url' => array('/site/index'), 'visible' => !Yii::app()->user->isGuest),
//                        array('label' => 'About', 'url' => array('/site/page', 'view' => 'about')),
//                        array('label' => 'Contact', 'url' => array('/site/contact')),
        array('label' => 'Projects', 'url' => array('/project'), 'visible' => !Yii::app()->user->isGuest, 'active' => in_array(Yii::app()->controller->id, array('project', 'userProject', 'taskProject'))),
        array('label' => 'Tasks', 'url' => array('/task'), 'visible' => !Yii::app()->user->isGuest, 'active' => Yii::app()->controller->id === 'task'),
        array('label' => 'Users', 'url' => array('/user'), 'visible' => !Yii::app()->user->isGuest, 'active' => Yii::app()->controller->id === 'user'),
        array('label' => 'Config', 'url' => array('/config'), 'visible' => !Yii::app()->user->isGuest, 'active' => Yii::app()->controller->id === 'config'),
        array('label' => 'Logout (' . Yii::app()->user->name . ')', 'url' => array('/site/logout'), 'visible' => !Yii::app()->user->isGuest)
    ),
));
?>
      </div><!-- mainmenu -->
        <?php if (isset($this->breadcrumbs)): ?>
        <?php
        $this->widget('zii.widgets.CBreadcrumbs', array(
            'links' => $this->breadcrumbs,
        ));
        ?><!-- breadcrumbs -->
      <?php endif ?>

      <?php echo $content; ?>

      <div class="clear"></div>

      <div id="footer">
        Copyright &copy; 2014 - <?php echo date('Y'); ?> by My Company.<br/>
        All Rights Reserved.<br/>
<?php echo Yii::powered(); ?>
      </div><!-- footer -->

    </div><!-- page -->

  </body>
</html>
