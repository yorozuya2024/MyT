<?php
/* @var $this UserController */
/* @var $model User */
/* @var $form CActiveForm */

/* @var $auth CDbAuthManager */

$auth = Yii::app()->authManager;

$baseRoles = array('Developer', 'Project Champion');

$roles = array();
$roles_row = $auth->getRoles();
if (Yii::app()->user->checkAccess('adminRole')) {
  foreach ($roles_row as $row)
    array_push($roles, $row->name);
} else {
  foreach ($roles_row as $row)
    in_array($row->name, $baseRoles) && array_push($roles, $row->name);
}

foreach ($roles as $role) {
  ?>
  <div class="row clear">
    <span class="span-5"><?php echo CHtml::label($role, 'AuthItem_' . preg_replace('/\s+/', '_', $role)); ?></span>
    <span class="span-1"><?php echo CHtml::checkBox('AuthItem[' . $role . ']', $auth->isAssigned($role, $model->id), array('uncheckValue' => 0)); ?></span>
  </div>
  <?php
}
?>
<div class="clear">&nbsp;</div>
