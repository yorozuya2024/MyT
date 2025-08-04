<?php

class ProjectController extends Controller {

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
            'actions' => array('viewExternal'),
            'users' => array('*'),
        ),
        array('allow', // allow all users to perform 'index' and 'view' actions
            'actions' => array('index', 'view', 'viewTasks'),
            'users' => array('@'),
        ),
        array('allow',
            'actions' => array('indexAll'),
            'users' => array('@'),
            'roles' => array('indexAllProject')
        ),
        array('allow',
            'actions' => array('create'),
            'users' => array('@'),
            'roles' => array('createProject')
        ),
        array('allow',
            'actions' => array('update'),
            'users' => array('@'),
            'roles' => array('updateProject')
        ),
        array('allow',
            'actions' => array('delete'),
            'users' => array('@'),
            'roles' => array('deleteProject')
        ),
//            array('allow', // allow admin user to perform 'admin' and 'delete' actions
//                'actions' => array('admin'),
//                'users' => array('@'),
//            ),
        array('deny', // deny all users
            'users' => array('*'),
        ),
    );
  }

  /**
   * Log the User in and displays the model.
   * @param string $userName Username for Login
   * @param string $userPassword Password for Login
   * @param integer $id the ID of the model to be displayed
   */
  public function actionViewExternal($_id, $userName, $userPassword) {
    $identity = new UserIdentity($userName, $userPassword);
    if ($identity->authenticate())
      Yii::app()->user->login($identity);
    $this->redirect(array('view', 'id' => $_id));
  }

  /**
   * Displays a particular model.
   * @param integer $id the ID of the model to be displayed
   */
  public function actionView($id) {
    $usersModel = new UserProject("searchByProject($id)");
    $usersModel->unsetAttributes();
    if (isset($_GET['UserProject']))
      $usersModel->attributes = $_GET['UserProject'];

    $this->render('view', array(
        'model' => $this->loadModel($id),
        'users' => $usersModel
    ));
  }

  /**
   * Displays a particular model.
   * @param integer $id the ID of the model to be displayed
   */
  public function actionViewTasks($id, $trkq = null) {
    if (is_null($trkq))
      Yii::app()->user->setState('p_task_query', '');

    $tasksModel = new Task('search');
    $tasksModel->unsetAttributes();  // clear any default values
    if (!(isset($_GET['grid_mode']) && $_GET['grid_mode'] === 'export')) {
      if (isset($_GET['Task'])) {
        if (isset($_GET['Task']['status']) && is_array($_GET['Task']['status']) && empty($_GET['Task']['status'][0]))
          $_GET['Task']['status'] = '';
        $tasksModel->attributes = $_GET['Task'];
        Yii::app()->user->setState('p_task_query', serialize($_GET['Task']));
      } else {
        $taskQuery = Yii::app()->user->getState('p_task_query', '');
        if ($taskQuery !== '')
          $tasksModel->attributes = unserialize($taskQuery);
        else
          $tasksModel->status = TaskStatus::model()->openStatusList;
      }
    }
    $this->render('viewTasks', array(
        'model' => $this->loadModel($id),
        'tasks' => $tasksModel
    ));
  }

  private function manageUsers(Project $project, array $userProject) {
    $userIds = array();

    if ($userProject !== null && !empty($userProject)) {
      $userIds = $userProject['user_id'];
    }

    $champion = $project->champion_id;
    if (!empty($champion) && !in_array($champion, $userIds))
      array_push($userIds, $champion);

    $newUsers = UserProject::merge($project->id, $userIds);

    $this->sendAssociationNotification($newUsers, $project->id);
    $this->sendEmailAssociationNotification($newUsers, $project->id);
  }

  /**
   * Creates a new model.
   * If creation is successful, the browser will be redirected to the 'view' page.
   */
  public function actionCreate() {
    $model = new Project;
    $model->champion_id = Yii::app()->user->id;
    $reqModel = Yii::app()->getRequest()->getPost('Project');

    $user = new UserProject;
    $user->user_id = Yii::app()->user->id;
    $reqUser = Yii::app()->getRequest()->getPost('UserProject', array('user_id' => array(Yii::app()->user->id)));

// Uncomment the following line if AJAX validation is needed
// $this->performAjaxValidation($model);

    if ($reqModel !== null) {
      $model->attributes = $reqModel;
      if ($model->save()) {
        $this->manageUsers($model, $reqUser);
        Yii::app()->user->setFlash('success', Yii::t('app', 'Project.create.success.{name}', array('{name}' => $model->name)));
        $this->redirect(array('view', 'id' => $model->id));
      }
    }

    $this->render('create', array(
        'model' => $model,
        'user' => $user,
    ));
  }

  /**
   * Updates a particular model.
   * If update is successful, the browser will be redirected to the 'view' page.
   * @param integer $id the ID of the model to be updated
   */
  public function actionUpdate($id) {
    $model = $this->loadModel($id);
    $reqModel = Yii::app()->getRequest()->getPost('Project');

    $user = new UserProject;
    $defaultUsers = array('user_id' => array());
    $assocUsers = UserProject::model()->findAllByAttributes(array('project_id' => $id));
    foreach ($assocUsers as $u)
      array_push($defaultUsers['user_id'], $u->user_id);

    $reqUser = Yii::app()->getRequest()->getPost('UserProject', $defaultUsers);
    $user->attributes = $reqUser;

// Uncomment the following line if AJAX validation is needed
// $this->performAjaxValidation($model);

    if ($reqModel !== null) {
      $model->attributes = $reqModel;
      if ($model->save()) {
        $this->manageUsers($model, $reqUser);
        Yii::app()->user->setFlash('success', Yii::t('app', 'Project.update.success.{name}', array('{name}' => $model->name)));
        $this->redirect(array('view', 'id' => $model->id));
      }
    }

    $this->render('update', array(
        'model' => $model,
        'user' => $user,
    ));
  }

  protected function sendAssociationNotification(array $newUsers, $projectId) {
    $enable = CPropertyValue::ensureBoolean(Yii::app()->params['notifications']['projectAssociation']['android']);
    if ($enable) {
      $project = Project::model()->findByPk($projectId);
      $message = array(
          'message' => Yii::t('app', 'Project.notification.title.{name}', array('{name}' => $project->name)),
          'pm_project' => array(
              'name' => $project->name,
              'description' => $project->description,
              'champion' => $project->champion->calc_name,
              'url' => Yii::app()->createAbsoluteUrl('project/viewExternal', array('_id' => $project->id))
          )
      );
      foreach ($newUsers as $newUser) {
        $user = User::model()->find(array(
            'select' => array('username', 'notifications'),
            'condition' => 'id = :id',
            'params' => array('id' => $newUser)
        ));
        if ($user->getNotification('projectAssociation', 'android')) {
          $userName = $user->username;
          AndroidNotification::sendNotification($userName, $message);
        }
      }
    }
  }

  protected function sendEmailAssociationNotification(array $newUsers, $projectId) {
    $email = new EmailNotification;
    $email->sendProjectAssociationNotification($newUsers, $projectId);
  }

  /**
   * Deletes a particular model.
   * If deletion is successful, the browser will be redirected to the 'admin' page.
   * @param integer $id the ID of the model to be deleted
   */
  public function actionDelete($id) {
    $model = $this->loadModel($id);
    $model->status = 3; // 'Deleted'
	
	$deleteError = '';

	if(!$this->projectCanBeDeleted($model))
	{
		$deleteError = Yii::t('app', 'Project.delete.error.{name}', array('{name}' => $model->name));
		Yii::app()->user->setFlash('error', $deleteError);
	}
	else if ($model->save())
      Yii::app()->user->setFlash('success', Yii::t('app', 'Project.delete.success.{name}', array('{name}' => $model->name)));

//        $this->loadModel($id)->delete();
// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
    if (!Yii::app()->getRequest()->getIsAjaxRequest()) {
      $redirect = array(Navigator::getProjectType() === 'all' ? 'indexAll' : 'index');
      $this->redirect(Yii::app()->getRequest()->getPost('returnUrl', $redirect));
    }
	else
	{
		if($deleteError != '')
			echo $deleteError;
	}
  }

  /**
   * Lists all <b>My</b> models.
   */
  public function actionIndex() {
    $model = new Project('searchMy');
    $model->unsetAttributes();
    if (isset($_GET['Project']))
      $model->attributes = $_GET['Project'];
    else
      $model->status = 0;

    $this->render('index', array(
        'model' => $model
    ));
  }

  /**
   * Lists all models.
   */
  public function actionIndexAll() {
    $model = new Project('search');
    $model->unsetAttributes();  // clear any default values
    if (isset($_GET['Project']))
      $model->attributes = $_GET['Project'];
    else
      $model->status = 0;

    $this->render('indexAll', array(
        'model' => $model
    ));
  }

  /**
   * Manages all models.
   */
  public function actionAdmin() {
    $model = new Project('search');
    $model->unsetAttributes();  // clear any default values
    if (isset($_GET['Project']))
      $model->attributes = $_GET['Project'];

    $this->render('admin', array(
        'model' => $model,
    ));
  }

  /**
   * Returns the data model based on the primary key given in the GET variable.
   * If the data model is not found, an HTTP exception will be raised.
   * @param integer $id the ID of the model to be loaded
   * @return Project the loaded model
   * @throws CHttpException
   */
  public function loadModel($id) {
    $model = Project::model()->findByPk($id);
    if ($model === null)
      throw new CHttpException(404, Yii::t('app', 'Http.404'));
    return $model;
  }

  /**
   * Performs the AJAX validation.
   * @param Project $model the model to be validated
   */
  protected function performAjaxValidation($model) {
    if (isset($_POST['ajax']) && $_POST['ajax'] === 'project-form') {
      echo CActiveForm::validate($model);
      Yii::app()->end();
    }
  }
  
  protected function projectCanBeDeleted( $model )
  {
	foreach( $model->tasks as $task )
	{
		if( $task->task_status->group_id != 1 ) //Task is not in group closed
			return false;
	}
	
	
	foreach( $model->hasProject as $childProject )
	{
		if( $childProject->status != 2 && $childProject->status != 3 )
			return false;
	}
	
	return true;
  }

}
