<?php

$userController = 'user';

return array(
    array('label' => Yii::t('nav', 'List'), 'url' => array($userController . '/index'), 'visible' => Yii::app()->user->checkAccess('createUser')),
    array('label' => Yii::t('nav', 'Create'), 'url' => array($userController . '/create'), 'visible' => Yii::app()->user->checkAccess('createUser')),
);
