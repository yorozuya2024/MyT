<?php

Yii::import('application.controllers.ChargeBaseController');

/**
 * Description of ChargeCreateController
 *
 * @author francesco.colamonici
 */
abstract class ChargeCreateController extends ChargeBaseController {

  /**
   * Creates a new model.
   * If creation is successful, the browser will be redirected to the 'view' page.
   */
  public function actionCreate($month = null, $user = null, $project = null, $manage = null, $half = null) {
    /* @var $model Charge */
    $model = new Charge;
    if (!$user)
      $user = Yii::app()->user->id;
    if ($manage && $month) {
      if ($half == 1)
        $month = substr($month, 0, 8) . '15'; //this is dirty should be improved
      else
        $month = date('Y-m-t', strtotime($month)); // fix
    }

    // Uncomment the following line if AJAX validation is needed
    // $this->performAjaxValidation($model);

    $errors = $this->postChargeCreate($manage);

    $searchDay = date('d') > 15 ? 't' : '15';
    if (!$month) {
      $month = date('Y-m-' . $searchDay);
    }

    /* @var $charges Charge[] */
    $charges = $this->getChargesCreate($month, $user, $project);
    $records = $this->getRecordsCreate($charges);
    /* @var $projectCriteria CDbCriteria */
    $projectCriteria = $this->getProjectsByUserCriteria($user);

    $this->render('create', array(
        'model' => $model,
        'records' => $records,
        'errors' => $errors,
//            'users' => User::model()->findAll(array('order' => 'username')),
        'projects' => Project::model()->findAllHierarchical($projectCriteria),
        'date' => ChargeHelper::formatDBDateForGUI($month),
        'isManage' => $manage,
        'userId' => $user,
        'prjId' => $project,
        'queryMonth' => $month
    ));
  }

  protected function postChargeCreate($manage) {
    list($errors, $isSuccess, $formattedDate, $formattedMonth) = array(array(), true, '', '');

    /* @var $charge Charge[][][] */
    $charge = Yii::app()->getRequest()->getPost('Charge');

    if ($charge === null)
      return $errors;

    foreach ($charge['project_id'] as $row => $project_id) {
      $arr = explode('/', $charge['month'][$row]);
      $formattedDate = date('Y-m-d', mktime(0, 0, 0, $arr[1], $arr[0], $arr[2]));
      $formattedMonth = date('Y-m\-', mktime(0, 0, 0, $arr[1], $arr[0], $arr[2]));

//            foreach ($charge['charge_data'][$row] as $day => $hours) {
//                $id = CPropertyValue::ensureInteger($charge['charge_data_id'][$row][$day]);
//                $hours = CPropertyValue::ensureInteger($hours);
      $isEmpty = true;
      foreach ($charge['charge_data_id'][$row] as $day => $id) {
        $id = CPropertyValue::ensureInteger($id);
        $hours = CPropertyValue::ensureFloat(isset($charge['charge_data'][$row][$day]) ? $charge['charge_data'][$row][$day] : 0);
        $isNewCharge = $id === 0;
        if ($hours === 0.0 || empty($project_id)) {
          !$isNewCharge && $this->loadModel($id)->delete();
          continue;
        }

        $isEmpty = $isEmpty && ($hours === 0.0 && $isNewCharge);

        $model = $isNewCharge ? new Charge : $this->loadModel($id);
        $model->user_id = $charge['user_id'][$row];
        $model->project_id = $project_id;
        $model->task_id = $charge['task_id'][$row];
        $model->day = $formattedMonth . str_pad($day, 2, '0', STR_PAD_LEFT);
        $model->hours = $hours;
        if ($model->validate())
          $isSuccess = $isSuccess && $model->save();
        else {
          array_push($errors, $model);
          $isSuccess = false;
        }
      }
      if ($isEmpty && !empty($project_id)) {
        Yii::app()->user->setFlash('notice', Yii::t('app', 'Charge.notice.empty.rows'));
      }
    }

    if ($isSuccess) {
      Yii::app()->user->setFlash('success', Yii::t('app', 'Charge.create.success'));
      $this->redirect($manage ? array('admin') : array('create', 'month' => $formattedDate));
    }
    return $errors;
  }

  protected function getProjectsByUserCriteria($user) {
    $user = CPropertyValue::ensureInteger($user);
    $criteria = new CDbCriteria;
    $criteria->together = true;
    $criteria->with = array('users' => array('select' => false));
    $criteria->compare('users.id', $user);
    $criteria->scopes = 'charge';
	$criteria->order = 't.name';
    return $criteria;
  }

  protected function getChargesCreate($month, $user, $project = null) {
    $monthStart = substr($month, 0, 8) . (substr($month, 8) === '15' ? '01' : '16');
    $criteria = new CDbCriteria;
    $criteria->addBetweenCondition('day', $monthStart, $month);
    $criteria->compare('user_id', $user);
    if ($project)
      $criteria->compare('project_id', $project);
    $criteria->order = 'project_id, task_id, day';

    return Charge::model()->with('user', 'task', 'project'/* , 'alltask' */)->findAll($criteria);
  }

  protected function getRecordsCreate($charges) {
    list($oldProjectId, $newProjectId, $oldTaskId, $newTaskId, $records) = array('', '', '', '', array());
    $record = $this->initRecord();
    $add = false;

    foreach ($charges as $charge) {
      empty($oldProjectId) && $oldProjectId = $charge->project_id;
      $newProjectId = $charge->project_id;
      empty($oldTaskId) && $oldTaskId = $this->getChargeTaskId($charge);
      $newTaskId = $this->getChargeTaskId($charge);
      if ($oldTaskId !== $newTaskId || $oldProjectId !== $newProjectId) {
        array_push($records, $record);
        $add = false;
        $record = $this->initRecord();
        $oldProjectId = $newProjectId;
        $oldTaskId = $newTaskId;
      }
      $this->setRecord($record, $charge);
      $add = true;
    }
    $add && array_push($records, $record);

    return $records;
  }

  protected function initRecord() {
    return array(
        'project_id' => '',
        'task_id' => '',
        'charges' => array(
            0 => array(
                'id' => 0,
                'hours' => 0,
            )
        )
    );
  }

  protected function setRecord(&$record, &$charge) {
    $day = explode('-', $charge->day);
    empty($record['project_id']) && $record['project_id'] = $charge->project_id;
    empty($record['task_id']) && $record['task_id'] = $this->getChargeTaskId($charge);
    if (!isset($record['alltask'])) {
      $allCriteria = new CDbCriteria;
      $allCriteria->compare('chargeable_flg', true);
      $allCriteria->compare('par_project_id', $charge->project_id, false);
      $allCriteria->select = 'id, title';
      $allCriteria->order = 'title';
      $record['alltask'] = Task::model()->findAllHierarchical($allCriteria);
//      $record['alltask'] = Task::model()->findAllByAttributes(array('par_project_id' => $charge->project_id,
//          'chargeable_flg' => true), array('select' => 'id, title', 'order' => 'title'));
    }
    $record['charges'][CPropertyValue::ensureInteger($day[2])] = array(
        'id' => $charge->id,
        'hours' => $charge->hours,
    );
  }

  protected function getChargeTaskId(&$charge, $default = 'x') {
    return isset($charge->task_id) && $charge->task_id !== null && !empty($charge->task_id) ? $charge->task_id : $default;
    ;
  }

}
