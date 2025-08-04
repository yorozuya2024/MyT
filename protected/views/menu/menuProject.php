<?php

$isProject = in_array(Yii::app()->controller->id, array('project'));
$projectId = isset($_GET['projectId']) ? $_GET['projectId'] : ($isProject ? (isset($_GET['id']) ? $_GET['id'] : null) : null);

return array(
    array('label' => Yii::t('nav', 'My Projects'), 'url' => array('/project')),
    array('label' => Yii::t('nav', 'All Projects'), 'url' => array('project/indexAll'), 'visible' => Yii::app()->user->checkAccess('indexAllProject')),
    array('label' => Yii::t('nav', 'Create'), 'url' => array('project/create'), 'visible' => Yii::app()->user->checkAccess('createProject')),
//    array('label' => 'Manage', 'url' => array('project/admin')),
//    array('label' => 'Update', 'url' => array('project/update', 'id' => $projectId), 'visible' => $projectId !== null && Yii::app()->user->checkAccess('updateProject')),
//    array('label' => 'Delete', 'url' => '#', 'linkOptions' => array('submit' => array('project/delete', 'id' => $projectId), 'confirm' => 'Are you sure you want to delete this item?'), 'visible' => $projectId !== null && Yii::app()->user->checkAccess('deleteProject')),
    array(
        'label' => Yii::t('nav', 'Tasks'),
//        'url' => array('taskProject/index', 'projectId' => $projectId),
        'url' => array('project/viewTasks', 'id' => $projectId, 'trkq' => 1),
        'visible' => $projectId !== null,
//        'items' => array(
//            array(
//                'label' => 'Create Task',
//                'url' => array('taskProject/create', 'projectId' => $projectId), 'visible' => Yii::app()->user->checkAccess('createTask')),
//        )
    ),
    array(
        'label' => Yii::t('nav', 'Users'),
        'url' => array('project/view', 'id' => $projectId),
//        'url' => array('userProject/indexByProject', 'projectId' => $projectId),
        'visible' => $projectId !== null,
//        'items' => array(
//            array(
//                'label' => 'Associate New User',
//                'url' => array('userProject/createByProject',
//                    'projectId' => $projectId),
//                'visible' => Yii::app()->user->checkAccess('updateProject')
//            ),
//        )
    ),
);
