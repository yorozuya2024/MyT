<?php

class UserAccountController extends Controller {

  /**
   * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
   * using two-column layout. See 'protected/views/layouts/column2.php'.
   */
  // public $layout = '//layouts/column2';
  public $defaultAction = 'view';

  /**
   * @return array action filters
   */
  public function filters() {
    return array(
        'accessControl', // perform access control for CRUD operations
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
            'actions' => array('update', 'view'),
            'users' => array('@'),
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
  public function actionView() {
    $this->render('view', array(
        'model' => $this->loadModel(Yii::app()->user->id),
    ));
  }

  /**
   * Updates a particular model.
   * If update is successful, the browser will be redirected to the 'view' page.
   * @param integer $id the ID of the model to be updated
   */
  public function actionUpdate() {
    $model = $this->loadModel(Yii::app()->user->id);
    $oldAvatar = $model->avatar;

    // Uncomment the following line if AJAX validation is needed
    // $this->performAjaxValidation($model);

    if (isset($_POST['User'])) {
      $model->attributes = $_POST['User'];
      $model->notifications = $_POST['User']['notifications'];

      Yii::app()->user->pageSize = intval($_POST['User']['page_size']) < 1 ? Yii::app()->params['pageSize'] : intval($_POST['User']['page_size']);

      if ($_POST['User']['optAvatar'] === 'Y') {
        $uploadedFile = CUploadedFile::getInstance($model, 'avatar');
        if ($uploadedFile !== null) {
          $fileName = time() . '_' . $uploadedFile->name;
          $model->avatar = $fileName;
          if (!empty($oldAvatar))
            @unlink(Yii::app()->params['avatarPath'] . $oldAvatar);
        }

        if ($model->save()) {
          if ($uploadedFile !== null) {
            $uploadedFile->saveAs(Yii::app()->params['avatarPath'] . $fileName);
            $image = new EasyImage(Yii::app()->params['avatarPath'] . $fileName);
            if ($image->image()->width > Yii::app()->params['imageDimension']['maxWidth'] || $image->image()->height > Yii::app()->params['imageDimension']['maxHeight'])
              $image->resize(Yii::app()->params['imageDimension']['maxWidth'], Yii::app()->params['imageDimension']['maxHeight']);
            $image->save(Yii::app()->params['avatarPath'] . $fileName);
          }
          $this->redirect(array('view'));
        }
      } else {
        $model->avatar = new CDbExpression('NULL');
        if ($model->save()) {
          if (!empty($oldAvatar))
            @unlink(Yii::app()->params['avatarPath'] . $oldAvatar);
          $this->redirect(array('view'));
        }
      }
    }

    $this->render('update', array(
        'model' => $model,
    ));
  }

  /**
   * Returns the data model based on the primary key given in the GET variable.
   * If the data model is not found, an HTTP exception will be raised.
   * @param integer $id the ID of the model to be loaded
   * @return User the loaded model
   * @throws CHttpException
   */
  public function loadModel($id) {
    $model = User::model()->findByPk($id);
    if ($model === null)
      throw new CHttpException(404, Yii::t('app', 'Http.404'));
    return $model;
  }

  /**
   * Performs the AJAX validation.
   * @param User $model the model to be validated
   */
  protected function performAjaxValidation($model) {
    if (isset($_POST['ajax']) && $_POST['ajax'] === 'user-form') {
      echo CActiveForm::validate($model);
      Yii::app()->end();
    }
  }

}
