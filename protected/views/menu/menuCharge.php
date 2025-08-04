<?php

return array(
    array(
        'label' => Yii::t('nav', 'My Charges'),
        'url' => array('charge/create'),
        'visible' => Yii::app()->user->checkAccess('createCharge')
    ),
    array(
        'label' => Yii::t('nav', 'Manage'),
        'url' => array('charge/admin'),
        'visible' => Yii::app()->user->checkAccess('adminCharge'),
    ),
    array(
        'label' => Yii::t('nav', 'Manage All'),
        'url' => array('charge/adminAll'),
        'visible' => Yii::app()->user->checkAccess('adminAllCharge'),
    ),
);
