<?php
/* @var $this Controller */
?>
<?php
$cs = Yii::app()->clientScript;

$cs->scriptMap = array(
    'jquery-ui.min.js' => Yii::app()->request->baseUrl . '/js/jquery-ui-1.10.3.custom.min.js',
    'jquery-ui.css' => Yii::app()->theme->baseUrl . '/css/jquery-ui-1.10.3.custom.min.css',
	
);

Yii::app()->clientScript->registerCoreScript('jquery.ui');
Yii::app()->clientScript->registerCssFile('jquery-ui.css');
Yii::app()->clientScript->registerCssFile(Yii::app()->theme->baseUrl . '/css/main.css');

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo Yii::app()->language; ?>" lang="<?php echo Yii::app()->language; ?>">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="language" content="<?php echo Yii::app()->language; ?>" />

    <link rel="icon" href="<?php echo Yii::app()->request->baseUrl; ?>/images/favicon.ico" type="image/x-icon" />
<!--<link rel="shortcut icon" href="<?php echo Yii::app()->request->baseUrl; ?>/images/favicon.ico" type="image/x-icon" />-->

    <!-- blueprint CSS framework -->
    <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/screen.css" media="screen, projection" />
    <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/print.css" media="print" />
    <!--[if lt IE 8]>
    <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/ie.css" media="screen, projection" />
    <![endif]-->

    <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->theme->baseUrl; ?>/css/normalize.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->theme->baseUrl; ?>/css/menu.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/form.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->theme->baseUrl; ?>/css/form.css" />
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-FJ1VTL1DMK"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());

      gtag('config', 'G-FJ1VTL1DMK');
    </script>

    <title><?php echo CHtml::encode($this->pageTitle); ?></title>
  </head>

  <body>
    <div id="mainMbMenu">
      <div id="header">
        <img id ="logoimg" src="<?php echo Yii::app()->request->baseUrl; ?>/images/press3.png" width="25" heigth="25"/>
        <span id="logo"><?php echo CHtml::encode(Yii::app()->params['name']); ?></span>
      </div><!-- heeader -->
      <?php
      $userVisible = Yii::app()->user->checkAccess('createUser') || Yii::app()->user->checkAccess('indexAllUser');
      $this->widget('ext.htmlMenu.HtmlMenu', array(
          'htmlOptions' => array('class' => 'drop'),
          'items' => require(Yii::getPathOfAlias('application.views.menu') . DIRECTORY_SEPARATOR . 'menu.php')
      ));
      ?>
      <div style="clear: both;"></div>
    </div><!-- mainmenu -->
    <div class="container" id="page">


      <?php echo $content; ?>

      <div class="clear"></div>

      <div id="footer">
        Copyright &copy; 2014 - <?php echo date('Y'); ?> by <?php echo CHtml::link('MyT Team', 'http://sourceforge.net/projects/myt/'); ?>.<br/>
        All Rights Reserved.<br/>
        <?php echo Yii::powered(); ?>
      </div><!-- footer -->

    </div><!-- page -->

  </body>
</html>
