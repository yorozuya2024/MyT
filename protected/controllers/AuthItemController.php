<?php

class AuthItemController extends Controller {

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
        array('allow', // allow all users to perform 'index' and 'view' actions
            'actions' => array('index', 'indexOperation', 'create', 'update', 'delete'),
            'users' => array('@'),
            'roles' => array('adminRole')
        ),
        array('deny', // deny all users
            'users' => array('*'),
        ),
    );
  }

  /**
   * Creates a new model.
   * If creation is successful, the browser will be redirected to the 'view' page.
   */
  public function actionCreate() {
    $model = new AuthItem;

    // Uncomment the following line if AJAX validation is needed
    // $this->performAjaxValidation($model);

    if (isset($_POST['AuthItem'])) {
      $model->attributes = $_POST['AuthItem'];
      $model->type = 2;
      if ($model->save()) {
        if (isset($_POST['AuthItemChild'])) {
          /* @var $auth CDbAuthManager */
          /* @var $role CAuthItem */
          $auth = Yii::app()->authManager;
          $role = $model->getItem();
          $postOps = array_keys($_POST['AuthItemChild']);
          $ops = AuthItem::getOperations();

          $mergeOps = array();
          foreach ($ops as $op)
            $mergeOps[$op] = in_array($op, $postOps);

          foreach ($mergeOps as $op => $assoc) {
            if ($assoc && !$role->hasChild($op))
              $role->addChild($op);
            elseif (!$assoc && $role->hasChild($op))
              $role->removeChild($op);
          }
          $auth->save();
        }
        $this->redirect(array('index'));
      }
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

    if (isset($_POST['AuthItem'])) {
      $model->attributes = $_POST['AuthItem'];
      if ($model->save()) {
        if (isset($_POST['AuthItemChild'])) {
          /* @var $auth CDbAuthManager */
          /* @var $role CAuthItem */
          $auth = Yii::app()->authManager;
          $role = $model->getItem();
          $postOps = array_keys($_POST['AuthItemChild']);
          $ops = AuthItem::getOperations();

          $mergeOps = array();
          foreach ($ops as $op)
            $mergeOps[$op] = in_array($op, $postOps);

          foreach ($mergeOps as $op => $assoc) {
            if ($assoc && !$role->hasChild($op))
              $role->addChild($op);
            elseif ($assoc == false && $role->hasChild($op))
              $role->removeChild($op);
          }
          $auth->save();
        }
        Yii::app()->cache->flush();
        $this->redirect(array('index'));
      }
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
    $this->loadModel($id)->delete();

    // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
    if (!isset($_GET['ajax']))
      $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('index'));
  }

  /**
   * Lists all models.
   */
  public function actionIndex() {
    $model = new AuthItem('searchRole');
    $model->unsetAttributes();  // clear any default values
    if (isset($_GET['AuthItem']))
      $model->attributes = $_GET['AuthItem'];

    $this->render('indexRole', array(
        'model' => $model,
    ));
  }

  /**
   * Lists all models.
   */
  public function actionIndexOperation() {
    $model = new AuthItem('searchOperation');
    $model->unsetAttributes();  // clear any default values
    if (isset($_GET['AuthItem']))
      $model->attributes = $_GET['AuthItem'];

    $this->render('indexOperation', array(
        'model' => $model,
    ));
  }

  /**
   * Returns the data model based on the primary key given in the GET variable.
   * If the data model is not found, an HTTP exception will be raised.
   * @param integer $id the ID of the model to be loaded
   * @return AuthItem the loaded model
   * @throws CHttpException
   */
  public function loadModel($id) {
    $model = AuthItem::model()->findByPk($id);
    if ($model === null)
      throw new CHttpException(404, Yii::t('app', 'Http.404'));
    return $model;
  }

  /**
   * Performs the AJAX validation.
   * @param AuthItem $model the model to be validated
   */
  protected function performAjaxValidation($model) {
    if (isset($_POST['ajax']) && $_POST['ajax'] === 'auth-item-form') {
      echo CActiveForm::validate($model);
      Yii::app()->end();
    }
  }

}
