<?php

return array(
    'basePath' => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..',
    'name' => 'プロジェクト管理システム',
    'preload' => array('log'),
    // autoloading model and component classes
    'import' => array(
        'application.models.*',
        'application.components.*',
        'ext.easyimage.EasyImage',
        'ext.EExcelView',
        'ext.TaskExcelView',
    ),
    // i18n
    'language' => 'ja',
    'sourceLanguage' => 'en',
    'theme' => 'fluid',
    'modules' => array(
        'gii' => array(
            'class' => 'system.gii.GiiModule',
            'password' => 'Ah!',
            'ipFilters' => array('127.0.0.1', '::1', '*'),
        ),
    ),
    // application components
    'components' => array(
        'widgetFactory' => array(
            'widgets' => array(
                'CLinkPager' => array(
                    'cssFile' => false,
                    'header' => false
                )
            )
        ),
        'user' => array(
            'class' => 'RWebUser',
            'allowAutoLogin' => true,
        ),
        'cache' => array(
            'class' => 'system.caching.CFileCache',
        ),
        'counter' => array(
            'class' => 'ext.mySession.UserCounter',
        ),
        'mail' => array(
            'class' => 'ext.yii-mail.YiiMail',
            'transportType' => 'php',
            'logging' => false,
            'dryRun' => false
        ),
        
          'urlManager' => array(
          'urlFormat' => 'path',
          'showScriptName' => false,
          'rules' => array(
          '<controller:\w+>/<id:\d+>' => '<controller>/view',
          '<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
          '<controller:authItem>/<action:\w+>/<id:(?:[a-zA-Z0-9]+[ ]?)+[a-zA-Z0-9]+>' => '<controller>/<action>', //this is only for auth manager
          '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
          '<controller:charge>/<action:create>/<month:[\w-]+>/<user:\d+>/<project:\d+>' => '<controller>/<action>', //custom rule for months
          ),
          ),
          
        'db' => array(
            'connectionString' => 'mysql:host=localhost;dbname=prm',
            'emulatePrepare' => true,
            'username' => 'root',
            'password' => '123456',
            'tablePrefix' => 'myt_',
            'charset' => 'utf8',
            'enableProfiling' => true,
            'enableParamLogging' => true,
        ),
        'authManager' => array(
            'class' => 'CDbAuthManager',
            'connectionID' => 'db',
            'itemTable' => '{{auth_item}}',
            'itemChildTable' => '{{auth_item_child}}',
            'assignmentTable' => '{{auth_assignment}}',
        ),
        'errorHandler' => array(
            'errorAction' => 'site/error',
        ),
        'log' => array(
            'class' => 'CLogRouter',
            'routes' => array(
                array(
                    'class' => 'CFileLogRoute',
                    'levels' => 'error, warning',
                ),
                array(
                    'class' => 'ext.yii-debug-toolbar.YiiDebugToolbarRoute',
                    'ipFilters' => array('127.0.0.1', '*'),
                ),
            ),
        ),
        'session' => array(
            'class' => 'ext.mySession.DbHttpSession',
            'connectionID' => 'db',
            'sessionTableName' => '{{session}}',
            'autoCreateSessionTable' => false,
            'timeout' => 3600
        ),
        'format' => array(
            'class' => 'ELocalizedFormatter',
        ),
        'easyImage' => array(
            'class' => 'application.extensions.easyimage.EasyImage'
        ),
        'mega' => array(
            'class' => 'application.extensions.yii-mega-api.Mega'
        ),
        'messages' => array(
            'class' => 'CPhpMessageSource',
            'forceTranslation' => true,
            'language' => 'en',
            'cachingDuration' => 604800, // 1 week
        ),
    ),
    'params' => require(dirname(__FILE__) . '/params.php'),
);
