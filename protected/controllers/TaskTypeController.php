<?php

class TaskTypeController extends Controller {

  /**
   * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
   * using two-column layout. See 'protected/views/layouts/column2.php'.
   */
  public $layout = '//layouts/column1';
  public $defaultAction = 'admin';

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
            'actions' => array('admin', 'create', 'update', 'delete'),
            'users' => array('@'),
            'roles' => array('adminConfig')
        ),
        array('deny', // deny all users
            'users' => array('*'),
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
    ));
  }

  /**
   * Creates a new model.
   * If creation is successful, the browser will be redirected to the 'view' page.
   */
  public function actionCreate() {
    $model = new TaskType;

    // Uncomment the following line if AJAX validation is needed
    // $this->performAjaxValidation($model);

    if (isset($_POST['TaskType'])) {
      $model->attributes = $_POST['TaskType'];
      if ($model->save())
        $this->redirect(array('admin'));
    }

    $this->render('create', array(
        'model' => $model,
    ));
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

    if (isset($_POST['TaskType'])) {
      $model->attributes = $_POST['TaskType'];
      if ($model->save())
        $this->redirect(array('admin'));
    }

    $this->render('update', array(
        'model' => $model,
    ));
  }

  /**
   * Deletes a particular model.
   * If deletion is successful, the browser will be redirected to the 'admin' page.
   * @param integer $id the ID of the model to be deleted
   */
  public function actionDelete($id) {
//    $this->loadModel($id)->delete();
    $model = $this->loadModel($id);
    if ($model->default_flg) {
      if (isset($_GET['ajax']))
        echo Yii::t('app', 'TaskType.default.error');
      else
        Yii::app()->user->setFlash('error', Yii::t('app', 'TaskType.default.error'));
    } else
      $model->saveAttributes(array('active_flg' => false));

    // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
    if (!isset($_GET['ajax']))
      $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
  }

  /**
   * Manages all models.
   */
  public function actionAdmin() {
    $model = new TaskType('search');
    $model->unsetAttributes();  // clear any default values
    if (isset($_GET['TaskType']))
      $model->attributes = $_GET['TaskType'];

    $this->render('admin', array(
        'model' => $model,
    ));
  }

  /**
   * Returns the data model based on the primary key given in the GET variable.
   * If the data model is not found, an HTTP exception will be raised.
   * @param integer $id the ID of the model to be loaded
   * @return TaskType the loaded model
   * @throws CHttpException
   */
  public function loadModel($id) {
    $model = TaskType::model()->findByPk($id);
    if ($model === null)
      throw new CHttpException(404, Yii::t('app', 'Http.404'));
    return $model;
  }

  /**
   * Performs the AJAX validation.
   * @param TaskType $model the model to be validated
   */
  protected function performAjaxValidation($model) {
    if (isset($_POST['ajax']) && $_POST['ajax'] === 'task-type-form') {
      echo CActiveForm::validate($model);
      Yii::app()->end();
    }
  }

}
