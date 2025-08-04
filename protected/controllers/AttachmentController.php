<?php

class AttachmentController extends Controller {

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
//            'postOnly + delete', // we only allow deletion via POST request
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
            'actions' => array('index', 'view', 'download', 'test'),
            'users' => array('@'),
        ),
        array('allow', // allow authenticated user to perform 'create' and 'update' actions
            'actions' => array('create', 'update'),
            'users' => array('@'),
            'roles' => array('updateTask'),
        ),
        array('allow', // allow admin user to perform 'admin' and 'delete' actions
            'actions' => array('admin', 'delete'),
            'users' => array('@'),
            'roles' => array('updateTask'),
        ),
        array('deny', // deny all users
            'users' => array('*'),
        ),
    );
  }

  public function actionTest() {
    Yii::app()->mega->test();
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

  public function actionDownload($id) {
    $model = $this->loadModel($id);
    if ($model->type === 'file') {
      $storage = Yii::app()->params['attachments']['storage'];
      switch ($storage) {
        case 'local':
          $filename = realpath(dirname(__FILE__) . '/../../' . $model->uri);
          $this->downloadFile($filename);
          break;
        case 'mega':
//                    Yii::app()->mega->downloadFile($model->mega_id);
          Yii::app()->mega->downloadFile($model->uri);
          break;
        default:
          throw new CHttpException(405);
      }
    }
  }

  protected function downloadFile($filename) {
    if (file_exists($filename)) {
      ob_start();
      header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
      header('Pragma: public');
      header('Content-type: ' . CFileHelper::getMimeTypeByExtension($filename));
      header('Content-Disposition: attachment; filename="' . basename($filename) . '"');
      header('Cache-Control: max-age=0');
      file_put_contents('php://output', file_get_contents($filename));
      ob_end_flush();
      Yii::app()->end();
    } else
      throw new CHttpException(404);
  }

  /**
   * Creates a new model.
   * If creation is successful, the browser will be redirected to the 'view' page.
   */
  public function actionCreate($task_id, $taskProject) {
    $model = new Attachment;
    $model->task_id = $task_id;
    $model->project_id = Task::model()->findByPk($task_id)->par_project_id;

    // Uncomment the following line if AJAX validation is needed
    // $this->performAjaxValidation($model);

    if (isset($_POST['Attachment'])) {
      $model->attributes = $_POST['Attachment'];
      if ($model->type === 'file') {
        $storage = Yii::app()->params['attachments']['storage'];
        $uploadedFile = CUploadedFile::getInstance($model, 'file');
        if ($uploadedFile === null) {
          $model->addError('type', Yii::t('app', 'Attachment.create.failure.file'));
        } else {
          switch ($storage) {
            case 'local':
              $model->uri = Yii::app()->params['attachments']['path'] . DIRECTORY_SEPARATOR . $uploadedFile->name;
              break;
            case 'mega':
              $model->uri = $uploadedFile->name;
              break;
            default:
              throw new CHttpException(405);
          }
        }
      }
      if ($model->type === 'link') {
        $model->uri = $model->link;
      }
      $upload = $model->type === 'link' || false;
      if ($model->type === 'file') {
        switch ($storage) {
          case 'local':
            $upload = $uploadedFile->saveAs($model->uri);
            break;
          case 'mega':
            $resp = Yii::app()->mega->uploadFile($uploadedFile->tempName, $uploadedFile->name);
            $upload = !is_int($resp);
            if (!$upload)
              throw new CHttpException(405);
            $files = $resp->f;
            $file = $files[0];
            $model->mega_id = $file->h;
            break;
          default:
            throw new CHttpException(405);
        }
      }
      if ($upload && $model->save()) {
        Yii::app()->user->setFlash('success', Yii::t('app', 'Attachment.create.success'));
        if ($taskProject === 'true')
          $this->redirect(array('/taskProject/view', 'id' => $model->task_id, 'projectId' => $model->project_id));
        else
          $this->redirect(array('/task/view', 'id' => $model->task_id));
      }
    }

    $this->render('create', array(
        'model' => $model,
        'taskProject' => $taskProject,
    ));
  }

  /**
   * Updates a particular model.
   * If update is successful, the browser will be redirected to the 'view' page.
   * @param integer $id the ID of the model to be updated
   */
  public function actionUpdate($id, $taskProject) {
    $model = $this->loadModel($id);

    // Uncomment the following line if AJAX validation is needed
    // $this->performAjaxValidation($model);

    if (isset($_POST['Attachment'])) {
      $model->attributes = $_POST['Attachment'];
      if ($model->type === 'link') {
        $model->uri = $model->link;
      }
      if ($model->save()) {
        Yii::app()->user->setFlash('success', Yii::t('app', 'Attachment.update.success'));
        if ($taskProject === 'true')
          $this->redirect(array('/taskProject/view', 'id' => $model->task_id, 'projectId' => $model->project_id));
        else
          $this->redirect(array('/task/view', 'id' => $model->task_id));
      }
    }

    $this->render('update', array(
        'model' => $model,
        'taskProject' => $taskProject,
    ));
  }

  /**
   * Deletes a particular model.
   * If deletion is successful, the browser will be redirected to the 'admin' page.
   * @param integer $id the ID of the model to be deleted
   */
  public function actionDelete($id) {
    $model = $this->loadModel($id);
    $uri = $model->uri;
    if ($model->delete()) {
      if ($model->type === 'file') {
        $storage = Yii::app()->params['attachments']['storage'];
        switch ($storage) {
          case 'local':
            $filename = realpath(dirname(__FILE__) . '/../../' . $uri);
            if (file_exists($filename))
              unlink($filename);
            break;
          case 'mega':
            Yii::app()->mega->deleteFile($uri);
            break;
          default:
            throw new CHttpException(405);
        }
      }
      Yii::app()->user->setFlash('success', Yii::t('app', 'Attachment.delete.success'));
    }

    // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
//        if (!isset($_GET['ajax']))
//            $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
  }

  /**
   * Lists all models.
   */
  public function actionIndex() {
    $dataProvider = new CActiveDataProvider('Attachment');
    $this->render('index', array(
        'dataProvider' => $dataProvider,
    ));
  }

  /**
   * Manages all models.
   */
  public function actionAdmin() {
    $model = new Attachment('search');
    $model->unsetAttributes();  // clear any default values
    if (isset($_GET['Attachment']))
      $model->attributes = $_GET['Attachment'];

    $this->render('admin', array(
        'model' => $model,
    ));
  }

  /**
   * Returns the data model based on the primary key given in the GET variable.
   * If the data model is not found, an HTTP exception will be raised.
   * @param integer $id the ID of the model to be loaded
   * @return Attachment the loaded model
   * @throws CHttpException
   */
  public function loadModel($id) {
    $model = Attachment::model()->findByPk($id);
    if ($model === null)
      throw new CHttpException(404, Yii::t('app', 'Http.404'));
    return $model;
  }

  /**
   * Performs the AJAX validation.
   * @param Attachment $model the model to be validated
   */
  protected function performAjaxValidation($model) {
    if (isset($_POST['ajax']) && $_POST['ajax'] === 'attachment-form') {
      echo CActiveForm::validate($model);
      Yii::app()->end();
    }
  }

}
