<?php

return array(
    array('label' => Yii::t('nav', 'System'), 'items' => array(
            array('label' => Yii::t('nav', 'Application'), 'url' => array('/config')),
            array('label' => Yii::t('nav', 'Backups'), 'url' => array('/databaseBackup')),
        )),
    array('label' => Yii::t('nav', 'Tasks'), 'items' => array(
            array('label' => Yii::t('nav', 'Task Status'), 'url' => array('/taskStatus')),
            array('label' => Yii::t('nav', 'Task Type'), 'url' => array('/taskType')),
        )),
    array('label' => Yii::t('nav', 'Check for update'), 'url' => array('/checkUpdate')),
);
