<?php

$taskController = 'task';

return array(
    array('label' => Yii::t('nav', 'My Tasks'), 'url' => array($taskController . '/index')),
    array('label' => Yii::t('nav', 'All Tasks'), 'url' => array($taskController . '/indexAll'), 'visible' => Yii::app()->user->checkAccess('indexAllTask')),
    array('label' => Yii::t('nav', 'Create'), 'url' => array($taskController . '/create'), 'visible' => Yii::app()->user->checkAccess('createTask')),
);
