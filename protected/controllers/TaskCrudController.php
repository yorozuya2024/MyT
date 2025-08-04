<?php

/**
 * Description of TaskCrudController
 *
 * @author francesco.colamonici
 */
class TaskCrudController extends Controller {

  /**
   * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
   * using two-column layout. See 'protected/views/layouts/column2.php'.
   */
  public $layout = '//layouts/column1';

  /**
   * @return array action filters
   */
  public function filters() {
    return array(
        'accessControl', // perform access control for CRUD operations
        'postOnly + delete', // we only allow deletion via POST request
    );
  }

  /**
   * Specifies the access control rules.
   * This method is used by the 'accessControl' filter.
   * @return array access control rules
   */
  public function accessRules() {
    return array(
        array('allow',
            'actions' => array('index', 'view'),
            'users' => array('@'),
        ),
        array('allow',
            'actions' => array('indexAll'),
            'users' => array('@'),
            'roles' => array('indexAllTask')
        ),
        array('allow',
            'actions' => array('create'),
            'users' => array('@'),
            'roles' => array('createTask')
        ),
        array('allow',
            'actions' => array('update'),
            'users' => array('@'),
            'roles' => array('updateTask')
        ),
        array('allow',
            'actions' => array('delete'),
            'users' => array('@'),
            'roles' => array('deleteTask')
        ),
    );
  }

  /**
   * Displays a particular model.
   * @param integer $id the ID of the model to be displayed
   */
  public function actionView($id) {
    $this->render('view', array(
        'model' => $this->loadModel($id),
        'attach' => Attachment::model()->findAllByAttributes(array('task_id' => $id)),
    ));
  }

  /**
   * Creates a new model.
   * If creation is successful, the browser will be redirected to the 'view' page.
   */
  public function actionCreate() {
    $model = new Task;

    $reqModel = Yii::app()->getRequest()->getPost('Task');

    $user = new UserTask();
    $user->user_id = Yii::app()->user->id;
    $reqUser = Yii::app()->getRequest()->getPost('UserTask', array('user_id' => Yii::app()->user->id));

// Uncomment the following line if AJAX validation is needed
// $this->performAjaxValidation($model);

    if ($reqModel !== null) {
      $model->attributes = $reqModel;
      $user->attributes = $reqUser;
      if ($model->validate()) {
        if (is_array($reqUser['user_id'])) {
          if ($reqUser !== null) {
            $userIds = $reqUser['user_id'];
            $assoc = Yii::app()->getRequest()->getPost('Assoc', array('multiple' => '0'));
            if ($assoc['multiple'] === '1') {
              foreach ($userIds as $userId) {
                $task = new Task;
                $task->attributes = $reqModel;
                if ($task->save()) {
                  UserTask::associate($task->id, $userId);
                  Yii::app()->user->setFlash('success', Yii::t('app', 'Task.associate.success'));
                  $this->_sendAssociationNotification(array($userId), $task->id);
                  $this->_sendEmailAssociationNotification(array($userId), $task->id);
                }
              }
            } else {
              if ($model->save()) {
                foreach ($userIds as $userId) {
                  UserTask::associate($model->id, $userId);
                }
                Yii::app()->user->setFlash('success', Yii::t('app', 'Task.create.success'));
                $this->_sendAssociationNotification($userIds, $model->id);
                $this->_sendEmailAssociationNotification($userIds, $model->id);
              }
            }
          }
          $this->redirect(array('view', 'id' => $model->id));
        } else {
          //this is bad but we need to check this part
          $user->addError('user_id', Yii::t('app', 'Task.associate.failure.user'));
        }
      }
    } else {
      $model->start_date = date('Y-m-d');
      $model->status = TaskStatus::model()->default()->find()->id;
      $model->type = TaskType::model()->default()->find()->id;
    }

    $this->render('create', array(
        'model' => $model,
        'user' => $user,
        'projectId' => Yii::app()->getRequest()->getQuery('project_id')
    ));
  }

  /**
   * Updates a particular model.
   * If update is successful, the browser will be redirected to the 'view' page.
   * @param integer $id the ID of the model to be updated
   */
  public function actionUpdate($id) {
    $model = $this->loadModel($id);

    $reqModel = Yii::app()->getRequest()->getPost('Task');

    $user = new UserTask();

    $defaultUsers = array('user_id' => array());
    $assocUsers = UserTask::model()->findAllByAttributes(array('task_id' => $id));
    foreach ($assocUsers as $u)
      array_push($defaultUsers['user_id'], $u->user_id);

    $reqUser = Yii::app()->getRequest()->getPost('UserTask', $defaultUsers);
    $user->attributes = $reqUser;

// Uncomment the following line if AJAX validation is needed
// $this->performAjaxValidation($model);

    if ($reqModel !== null) {
      $model->attributes = $reqModel;
      if ($model->save()) {
        if ($reqUser !== null) {
          $user_ids = $reqUser['user_id'];
          $newUsers = UserTask::merge($model->id, $user_ids);
          $this->_sendAssociationNotification($newUsers, $model->id);
          $this->_sendEmailAssociationNotification($newUsers, $model->id);
        }
        Yii::app()->user->setFlash('success', Yii::t('app', 'Task.update.success.{id}', array('{id}' => $model->calc_id)));
        $this->redirect(array('view', 'id' => $model->id));
      }
    }

    $this->render('update', array(
        'model' => $model,
        'user' => $user,
    ));
  }

  protected function _sendAssociationNotification(array $newUsers, $taskId) {
    $enable = CPropertyValue::ensureBoolean(Yii::app()->params['notifications']['taskAssociation']['android']);
    if ($enable) {
      $task = Task::model()->findByPk($taskId);
      $message = array(
          'message' => Yii::t('app', 'Task.notification.title.{id}.{title}', array('{id}' => $task->calc_id, '{title}' => $task->title)),
          'pm_task' => array(
              'id' => $task->calc_id,
              'title' => $task->title,
              'project' => $task->project->name,
              'description' => $task->description,
              'priority' => $task->priority,
              'end_date' => $task->end_date === null ? '' : Yii::app()->format->date($task->end_date),
              'url' => Yii::app()->createAbsoluteUrl('task/viewExternal', array('_id' => $task->id))
          )
      );
      foreach ($newUsers as $newUser) {
        $user = User::model()->find(array(
            'select' => array('username', 'notifications'),
            'condition' => 'id = :id',
            'params' => array('id' => $newUser)
        ));
        if ($user->getNotification('taskAssociation', 'android')) {
          $userName = $user->username;
          AndroidNotification::sendNotification($userName, $message);
        }
      }
    }
  }

  protected function _sendEmailAssociationNotification(array $newUsers, $taskId) {
    $email = new EmailNotification;
    $email->sendTaskAssociationNotification($newUsers, $taskId);
  }

  /**
   * Deletes a particular model.
   * If deletion is successful, the browser will be redirected to the 'admin' page.
   * @param integer $id the ID of the model to be deleted
   */
  public function actionDelete($id) {
    $model = $this->loadModel($id);
    $model->status = TaskStatus::getStatusId('Cancelled');
    $model->save();
    Yii::app()->user->setFlash('success', Yii::t('app', 'Task.delete.success'));
//        $this->loadModel($id)->delete();
// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
//        if (!isset($_GET['ajax']))
//            $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('index'));
    if (!Yii::app()->getRequest()->getIsAjaxRequest()) {
      $projectId = Navigator::getProjectId();
      $redirect = empty($projectId) ? array(Navigator::getTaskType() === 'all' ? 'indexAll' : 'index') : array('/project/viewTasks',
          'id' => $projectId);
      $this->redirect(Yii::app()->getRequest()->getPost('returnUrl', $redirect));
    }
  }

  /**
   * Lists all <b>My</b> models.
   */
  public function actionIndex($trkq = null) {
    if (is_null($trkq))
      Yii::app()->user->setState('task_query', '');
//        $model = new Task('searchMy');
    $model = new Task('search');
    $model->unsetAttributes();
    if (!(isset($_GET['grid_mode']) && $_GET['grid_mode'] === 'export')) {
      if (isset($_GET['Task'])) {
        if (isset($_GET['Task']['status']) && is_array($_GET['Task']['status']) && empty($_GET['Task']['status'][0]))
          $_GET['Task']['status'] = '';
        $model->attributes = $_GET['Task'];
        Yii::app()->user->setState('task_query', serialize($_GET['Task']));
      } else {
        $taskQuery = Yii::app()->user->getState('task_query', '');
        if ($taskQuery !== '')
          $model->attributes = unserialize($taskQuery);
        else
          $model->status = TaskStatus::model()->openStatusList;
      }
    }

    $this->render('index', array(
        'model' => $model,
    ));
  }

  /**
   * Lists all models.
   */
  public function actionIndexAll($trkq = null) {
    if (is_null($trkq))
      Yii::app()->user->setState('task_query', '');

    $model = new Task('search');
    $model->unsetAttributes();  // clear any default values
    if (!(isset($_GET['grid_mode']) && $_GET['grid_mode'] === 'export')) {
      if (isset($_GET['Task'])) {
        if (isset($_GET['Task']['status']) && is_array($_GET['Task']['status']) && empty($_GET['Task']['status'][0]))
          $_GET['Task']['status'] = '';
        $model->attributes = $_GET['Task'];
        Yii::app()->user->setState('task_query', serialize($_GET['Task']));
      } else {
        $taskQuery = Yii::app()->user->getState('task_query', '');
        if ($taskQuery !== '')
          $model->attributes = unserialize($taskQuery);
        else
          $model->status = TaskStatus::model()->openStatusList;
      }
    }

    $this->render('indexAll', array(
        'model' => $model,
    ));
  }

  /**
   * Returns the data model based on the primary key given in the GET variable.
   * If the data model is not found, an HTTP exception will be raised.
   * @param integer $id the ID of the model to be loaded
   * @return Task the loaded model
   * @throws CHttpException
   */
  public function loadModel($id) {
    $model = Task::model()->findByPk($id);
    if ($model === null)
      throw new CHttpException(404, Yii::t('app', 'Http.404'));
    return $model;
  }

  /**
   * Performs the AJAX validation.
   * @param Task $model the model to be validated
   */
  protected function performAjaxValidation($model) {
    if (isset($_POST['ajax']) && $_POST['ajax'] === 'task-form') {
      echo CActiveForm::validate($model);
      Yii::app()->end();
    }
  }

}
