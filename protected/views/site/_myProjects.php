<?php

$printed = array();
$criteria = new CDbCriteria();
$criteria->select = array('name');
$criteria->with = array('users' => array('select' => false));
$criteria->compare('users.id', Yii::app()->user->id);

$printProjects = function ($projects) use (&$criteria, &$printProjects, &$printed) {
  echo '<ul>', PHP_EOL;
  foreach ($projects as $p) {
    if (!in_array($p->id, $printed)) {
      $printed[] = $p->id;
      $tasks = Task::model()->public()->open()->count(array(
          'select' => 'id',
          'condition' => 'par_project_id = :project',
          'params' => array('project' => $p->id))
      );
      echo '<li>', CHtml::link($p->name, array('project/viewTasks', 'id' => $p->id)), ' (', $tasks, ')</li>', PHP_EOL;
      $child = new CDbCriteria();
      $child->compare('par_project_id', $p->id);
      $child->mergeWith($criteria);
      $subs = Project::model()->open()->findAll($child);
      if (!empty($subs))
        $printProjects($subs);
    }
  }
  echo '</ul>', PHP_EOL;
};

$root = new CDbCriteria();
$root->order = 'par_project_id, t.name';
$root->mergeWith($criteria);
$ps = Project::model()->open()->findAll($root);
if (!empty($ps))
  $printProjects($ps);
