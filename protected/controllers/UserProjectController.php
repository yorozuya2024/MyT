<?php

class UserProjectController extends Controller {
  /**
   * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
   * using two-column layout. See 'protected/views/layouts/column2.php'.
   */
  // public $layout = '//layouts/column2';

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
        array('allow', // allow all users to perform 'index' and 'view' actions
            'actions' => array('index', 'indexByProject'),
            'users' => array('*'),
        ),
        array('allow', // allow authenticated user to perform 'create' and 'update' actions
            'actions' => array('create', 'update', 'createByProject', 'delete'),
            'users' => array('@'),
            'roles' => array('createProject', 'updateProject')
        ),
//            array('allow', // allow admin user to perform 'admin' and 'delete' actions
//                'actions' => array('admin', 'adminByProject'),
//                'users' => array('@'),
//            ),
        array('deny', // deny all users
            'users' => array('*'),
        ),
    );
  }

  /**
   * Lists all models.
   */
  public function actionIndexByProject($projectId) {
    $this->render('indexByProject', array(
        'dataProvider' => UserProject::model()->searchByProject($projectId),
        'projectId' => $projectId
    ));
  }

  /**
   * Creates a new model.
   * If creation is successful, the browser will be redirected to the 'view' page.
   */
  public function actionCreateByProject($projectId) {
    $model = new UserProject();
    $model->rollon_date = date('Y-m-d');

    // Uncomment the following line if AJAX validation is needed
    // $this->performAjaxValidation($model);

    if (isset($_POST['UserProject'])) {
      $user_ids = $_POST['UserProject']['user_id'];
      unset($_POST['UserProject']['user_id']);
      foreach ($user_ids as $user_id) {
        $up = new UserProject;
        $up->attributes = $_POST['UserProject'];
        $up->project_id = $projectId;
        $up->user_id = $user_id;
        $up->save();
      }
      $this->sendAssociationNotification($user_ids, $projectId);
      $this->sendEmailAssociationNotification($user_ids, $projectId);
      $this->redirect(array('project/view', 'id' => $projectId));
    }

    $this->render('createByProject', array(
        'model' => $model,
        'projectId' => $projectId
    ));
  }

  protected function sendAssociationNotification(array $newUsers, $projectId) {
    if (Yii::app()->params['notifications']['projectAssociation']) {
      $project = Project::model()->findByPk($projectId);
      $message = array(
          'message' => Yii::t('app', 'Project.notification.title.{name}'),
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
        if ($user->notifications['projectAssociation'] === '1') {
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
   * Manages all models.
   */
  public function actionAdminByProject($projectId) {
    $model = new UserProject("searchByProject({$projectId})");
    $model->unsetAttributes();  // clear any default values
    if (isset($_GET['UserProject']))
      $model->attributes = $_GET['UserProject'];

    $this->render('adminByProject', array(
        'model' => $model,
        'projectId' => $projectId
    ));
  }

  /**
   * Deletes a particular model.
   * If deletion is successful, the browser will be redirected to the 'admin' page.
   * @param integer $id the ID of the model to be deleted
   */
  public function actionDelete($id) {
    $this->loadModel($id)->delete();

    // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
    if (!isset($_GET['ajax']))
      $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('indexByProject', 'projectId' => $projectId));
  }

  /**
   * Returns the data model based on the primary key given in the GET variable.
   * If the data model is not found, an HTTP exception will be raised.
   * @param integer $id the ID of the model to be loaded
   * @return UserProject the loaded model
   * @throws CHttpException
   */
  public function loadModel($id) {
    $model = UserProject::model()->findByPk($id);
    if ($model === null)
      throw new CHttpException(404, Yii::t('app', 'Http.404'));
    return $model;
  }

  /**
   * Updates a particular model.
   * If update is successful, the browser will be redirected to the 'view' page.
   * @param integer $id the ID of the model to be updated
   */
  public function actionUpdate($id) {
    $model = $this->loadModel($id);

    // Uncomment the following line if AJAX validation is needed
    // $this->performAjaxValidation($model);

    if (isset($_POST['UserProject'])) {
      $model->attributes = $_POST['UserProject'];
      if ($model->save())
//                $this->redirect(array('indexByProject', 'projectId' => $model->project_id));
        $this->redirect(array('project/view', 'id' => $model->project_id));
    }

    $this->render('update', array(
        'model' => $model,
        'projectId' => $model->project_id
    ));
  }

  /**
   * Performs the AJAX validation.
   * @param UserProject $model the model to be validated
   */
  protected function performAjaxValidation($model) {
    if (isset($_POST['ajax']) && $_POST['ajax'] === 'user-project-form') {
      echo CActiveForm::validate($model);
      Yii::app()->end();
    }
  }

}
