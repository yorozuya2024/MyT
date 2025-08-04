<?php

Yii::import('application.controllers.ChargeAdminController');

class ChargeController extends ChargeAdminController {

  public function actionGetTasks() {
    /* @var $projectId integer */
    $projectId = CPropertyValue::ensureInteger(Yii::app()->getRequest()->getPost('project_id', 0));

    /* @var $selected string */
    $selected = Yii::app()->getRequest()->getPost('selected');

    $allCriteria = new CDbCriteria;
    $allCriteria->compare('chargeable_flg', true);
    $allCriteria->compare('par_project_id', $projectId, false);
    $allCriteria->select = 'id, title';
    $allCriteria->order = 'title';
    /* @var $tasks Task[] */
    $tasks = Task::model()->findAllHierarchical($allCriteria);
//    $tasks = Task::model()->findAllByAttributes(array('par_project_id' => $projectId, 'chargeable_flg' => true), array('order' => 'title'));

    /* @var $data array */
    $data = CHtml::listData($tasks, 'id', function($task) {
      return str_pad($task->title, strlen($task->title) + 2 * $task->level, '- ', STR_PAD_LEFT);
    });

    echo CHtml::tag('option', array('value' => ''), '', true);

    foreach ($data as $value => $name) {
      $opt = array('value' => $value);

      if (!empty($selected) && $selected === $value)
        $opt['selected'] = 'selected';

      echo CHtml::tag('option', $opt, CHtml::encode($name), true);
    }

    Yii::app()->end();
  }

  public function actionGetGrid() {
    $model = new Charge;

    $user = Yii::app()->getRequest()->getPost('user', Yii::app()->user->id);
    $project = Yii::app()->getRequest()->getPost('project');
    $mode = Yii::app()->getRequest()->getPost('mode');
    $date = Yii::app()->getRequest()->getPost('date');

    if ($mode !== null) {
      if ($mode === 'prev') {
        $date = ChargeHelper::getPrevQuindicina($date);
      } else {
        $date = ChargeHelper::getNextQuindicina($date);
      }
    }

    $arr = explode('/', $date);

    $searchDay = ChargeHelper::getLastDayOfQuindicina($arr[0], $arr[1], $arr[2]);

    /* @var $charges Charge[] */
    $charges = $this->getChargesCreate("{$arr[2]}-{$arr[1]}-{$searchDay}", $user, $project);
    $records = $this->getRecordsCreate($charges);

    /* @var $projectCriteria CDbCriteria */
    $projectCriteria = $this->getProjectsByUserCriteria($user);

    $this->renderPartial('_table', array(
        'model' => $model,
        'records' => $records,
//        'users' => User::model()->findAll(array('order' => 'username')),
        'projects' => Project::model()->findAllHierarchical($projectCriteria),
        'date' => $date,
        'isAjax' => true,
        'userId' => $user,
        'prjId' => $project,
    ));
  }

}
