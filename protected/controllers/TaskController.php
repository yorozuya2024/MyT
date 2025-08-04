<?php

Yii::import('application.controllers.TaskCrudController');

class TaskController extends TaskCrudController {

  /**
   * @return array action filters
   */
  public function filters() {
    return CMap::mergeArray(parent::filters(), array(
                'ajaxOnly + ajaxProjectDeps'
    ));
  }

  /**
   * Specifies the access control rules.
   * This method is used by the 'accessControl' filter.
   * @return array access control rules
   */
  public function accessRules() {
    return CMap::mergeArray(parent::accessRules(), array(
                array('allow',
                    'actions' => array('viewExternal', 'getUserOpenTasks', 'getMyOpenTasks'),
                    'users' => array('*'),
                ),
                array('allow',
                    'actions' => array('ajaxProjectUsers'),
                    'users' => array('@'),
                    'roles' => array('createTask')
                ),
                array('allow',
                    'actions' => array('updateMassive', 'ajaxProjectDeps'),
                    'users' => array('@'),
                    'roles' => array('updateTask')
                ),
                array('deny', // deny all users
                    'users' => array('*'),
                ),
    ));
  }

  public function actionGetUserOpenTasks() {
    $owner = Yii::app()->request->getParam('owner', null);
    $password = Yii::app()->request->getParam('password', null);
    if ($owner === null || $password === null)
      throw new CHttpException(401, Yii::t('app', 'Http.401'));
    $identity = new UserIdentity($owner, $password);
    if ($identity->authenticate()) {
      $criteria = new CDbCriteria;
      $criteria->compare('status', TaskStatus::model()->openStatusList);
      $criteria->together = true;
      $criteria->with = array('users' => array('select' => false));
      $criteria->compare('users.username', $owner);
      $tasks = Task::model()->findAll($criteria);
      $message = array();
      foreach ($tasks as $task) {
        $t = new stdClass;
        $t->id = $task->id;
        $t->calc_id = $task->calc_id;
        $t->title = $task->title;
        $t->description = $task->description;
        $t->project_id = $task->par_project_id;
        $t->project_name = $task->project->name;
        $t->status = $task->task_status->name;
        $t->end_date = empty($task->end_date) ? '' : $task->end_date;
        $t->start_date = empty($task->start_date) ? '' : $task->start_date;
        $t->created = empty($task->created) ? '' : $task->created;
        array_push($message, $t);
      }
      echo json_encode(array('openTasks' => $message));
    } else
      throw new CHttpException(401, Yii::t('app', 'Http.401'));
    Yii::app()->end();
  }

  /**
   * Log the User in and displays the model.
   * @param string $userName Username for Login
   * @param string $userPassword Password for Login
   * @param integer $_id the ID of the model to be displayed
   */
  public function actionViewExternal($_id, $userName, $userPassword) {
    $identity = new UserIdentity($userName, $userPassword);
    if ($identity->authenticate())
      Yii::app()->user->login($identity);
    $this->redirect(array('view', 'id' => $_id));
  }

  /**
   * Updates a particular model.
   * If update is successful, the browser will be redirected to the 'view' page.
   * @param integer $id the ID of the model to be updated
   */
  public function actionUpdateMassive() {
    $model = new TaskMassiveForm;

    if (isset($_POST['TaskMassiveForm'])) {
      foreach ($_POST['TaskMassiveForm'] as $key => $value)
        if( property_exists( $model, $key ) )
			$model->$key = $value;
      if ($model->validate()) {
        $ids = explode(',', $model->ids);
        foreach ($ids as $id) {
          $task = Task::model()->findByPk($id);
          if ($model->type !== '')
            $task->type = $model->type;
          if ($model->priority !== '')
            $task->priority = $model->priority;
          if ($model->par_project_id !== '')
            $task->par_project_id = $model->par_project_id;
          if ($model->start_date !== '')
            $task->start_date = $model->start_date;
          if ($model->end_date !== '')
            $task->end_date = $model->end_date;
          if ($model->status !== '') {
            $task->status = $model->status;
            $this->updateMassiveStatus($model->status, $task);
          }
          if ($model->owner !== '') {
            if (UserProject::model()->findByAttributes(array('user_id' => $model->owner, 'project_id' => $task->par_project_id)) !== null) {
              $newUsers = UserTask::merge($task->id, array($model->owner));
              $this->_sendAssociationNotification($newUsers, $task->id);
              $this->_sendEmailAssociationNotification($newUsers, $task->id);
            }
          }
          $task->save();
        }
      }
    }
  }

  private function updateMassiveStatus($status, &$task) {
    if (!$status)
      return false;
    $closed = TaskStatus::getStatusIdByGroup(Yii::t('constants', 'TaskStatus.group.closed'));
    if (intval($status) === (is_array($closed) ? $closed[0] : $closed)) {
      if (empty($task->end_date))
        $task->end_date = date('Y-m-d');
      if (empty($task->eff_start_date))
        $task->eff_start_date = date('Y-m-d');
      if (empty($task->eff_end_date))
        $task->eff_end_date = date('Y-m-d');
      $task->progress = 100;
      if ($task->validate())
        return true;
      Yii::trace(CVarDumper::dumpAsString($task->errors), 'errors');
    }
    return false;
  }

  /**
   * Builds a list of <option> for a <select> that displays Users associated with the Task Project.
   */
  public function actionAjaxProjectDeps() {
    $out = array('users' => '', 'tasks' => '');

    $modelName = isset($_POST['Task']) ? 'Task' : 'TaskMassiveForm';
    $model = Yii::app()->request->getPost($modelName, array());

    $userFilter = new CDbCriteria;
    $userFilter->alias = 'u';
    $userFilter->scopes = array('active', 'enrolled' => isset($model['par_project_id']) ? $model['par_project_id'] : 0);
    $userFilter->select = 'u.id, u.username';
    $userFilter->order = 'u.username';

    $data = CHtml::listData(User::model()->findAll($userFilter), 'id', 'username');

    $modelName !== 'Task' && $out['users'] .= CHtml::tag('option', array('value' => ''));
    foreach ($data as $value => $name)
      $out['users'] .= CHtml::tag('option', array('value' => $value, 'selected' => $value == Yii::app()->user->id ? 'selected' : false), CHtml::encode($name), true);

    $out['tasks'] .= CHtml::tag('option', array('value' => ''));
    if (!empty($model['par_project_id'])) {
      $tCriteria = new CDbCriteria();
      if (isset($model['id']))
        $tCriteria->addNotInCondition('id', array($model['id']));
      $tCriteria->compare('t.par_project_id', $model['par_project_id']);
      $tCriteria->scopes = 'open';
      $tCriteria->order = 't.title';
      $data = CHtml::listData(
                      Task::model()->findAll($tCriteria), 'id', function($task) {
                return $task->calc_id . ' - ' . $task->title;
              });

      foreach ($data as $value => $name)
        $out['tasks'] .= CHtml::tag('option', array('value' => $value, 'selected' => $modelName === 'Task' && $value == $model['parent_id'] ? 'selected' : false), CHtml::encode($name), true);
    }
    echo json_encode($out);
  }

}
