<?php
/* @var $this SiteController */

$this->pageTitle = Yii::app()->name . ' - Auth Config';
$this->breadcrumbs = array(
    'Auth Config',
);

// Auth Configuration //
/* @var $auth CDbAuthManager */
/* @var $role CAuthItem */

$auth = Yii::app()->authManager;
$auth->clearAll();

/*
  $auth->createOperation('createAuthItem', 'create an Auth Item');
  $auth->createOperation('deleteAuthItem', 'delete an Auth Item');
  $auth->createOperation('editAuthItem', 'edit an Auth Item');

  $role = $auth->createRole('authManager', 'Auth Manager');
  $role->addChild('createAuthItem');
  $role->addChild('deleteAuthItem');
  $role->addChild('editAuthItem');

  $role = $auth->createRole('admin', 'admin');
  $role->addChild('authManager');
 */

$entities = array('project', 'task', 'user');
foreach ($entities as $entity) {
    $auth->createOperation('create' . ucfirst($entity), ucfirst($entity) . ' Create');
    $auth->createOperation('delete' . ucfirst($entity), ucfirst($entity) . ' Delete');
    $auth->createOperation('update' . ucfirst($entity), ucfirst($entity) . ' Update');

    $role = $auth->createRole(ucfirst($entity) . ' Manager', ucfirst($entity) . ' Manager');
    $role->addChild('create' . ucfirst($entity));
    $role->addChild('delete' . ucfirst($entity));
    $role->addChild('update' . ucfirst($entity));
}

$auth->createOperation('indexAllProject', 'Project View All');
$auth->createOperation('indexAllTask', 'Task View All');
$auth->createOperation('indexAllUser', 'User View All');

$auth->createOperation('adminRole', 'Roles Configuration Management');
$role = $auth->createRole('Role Manager');
$role->addChild('adminRole');

$auth->createOperation('adminConfig', 'Application Configuration Management');
$role = $auth->createRole('Application Manager');
$role->addChild('adminConfig');

$userId = User::model()->findByAttributes(array('username' => 'francesco.colamonici'))->id;
$auth->assign('User Manager', $userId);
$auth->assign('Project Manager', $userId);
$auth->assign('Task Manager', $userId);
$auth->assign('Role Manager', $userId);
$auth->assign('Application Manager', $userId);

$auth->save();
// End //
?>
<h1>Auth Config</h1>

<?php
foreach ($auth->operations as $op)
    echo $op->name, ' &raquo; ', $op->description, '<br />';
?>
