<?php

return array(
    array('label' => 'Home', 'url' => array('/site/index'), 'visible' => !Yii::app()->user->isGuest),
    array(
        'label' => Yii::t('nav', 'Projects'),
//                            'url' => array('/project'),
        'visible' => !Yii::app()->user->isGuest && isset(Yii::app()->params['tabs']['Project']) && Yii::app()->params['tabs']['Project'],
//                            'active' => in_array(Yii::app()->controller->id, array('project', 'userProject', 'taskProject')),
        'items' => require(dirname(__FILE__) . '/menuProject.php')
    ),
    array(
        'label' => Yii::t('nav', 'Tasks'),
//                            'url' => array('/task'),
        'visible' => !Yii::app()->user->isGuest && isset(Yii::app()->params['tabs']['Task']) && Yii::app()->params['tabs']['Task'],
//                            'active' => Yii::app()->controller->id === 'task',
        'items' => require(dirname(__FILE__) . '/menuTask.php')
    ),
    array(
        'label' => Yii::t('nav', 'Users'),
//                            'url' => array('/user'),
        'visible' => !Yii::app()->user->isGuest && isset(Yii::app()->params['tabs']['User']) && Yii::app()->params['tabs']['User'] && $userVisible,
//                            'active' => Yii::app()->controller->id === 'user',
        'items' => require(dirname(__FILE__) . '/menuUser.php')
    ),
    array(
        'label' => Yii::t('nav', 'Charges'),
//                            'url' => array('/charge'),
        'visible' => !Yii::app()->user->isGuest && isset(Yii::app()->params['tabs']['Charge']) && Yii::app()->params['tabs']['Charge'],
//                            'active' => Yii::app()->controller->id === 'charge',
        'items' => require(dirname(__FILE__) . '/menuCharge.php')
    ),
    array(
        'label' => Yii::t('nav', 'Roles'),
        //   'url' => array('/authItem'),
        'visible' => !Yii::app()->user->isGuest && isset(Yii::app()->params['tabs']) && Yii::app()->params['tabs']['Authorization'] && Yii::app()->user->checkAccess('adminRole'),
//                            'active' => Yii::app()->controller->id === 'authItem',
        'items' => require(dirname(__FILE__) . '/menuAuth.php')
    ),
    array(
        'label' => Yii::t('nav', 'Config'),
        'visible' => !Yii::app()->user->isGuest && Yii::app()->user->checkAccess('adminConfig'),
//                            'active' => Yii::app()->controller->id === 'config',
        'items' => require(dirname(__FILE__) . '/menuConfig.php')
    ),
    array(
        'label' => Yii::app()->user->name,
        'visible' => !Yii::app()->user->isGuest,
        'itemOptions' => array('class' => 'right'),
        'items' => array(
            array('label' => Yii::t('nav', 'My Profile'), 'url' => array('/userAccount')),
            array('label' => Yii::t('nav', 'Edit Profile'), 'url' => array('/userAccount/update')),
            array('label' => Yii::t('nav', 'Logout'), 'url' => array('/site/logout'))
        )
    ),
);
