<?php

/**
 * Description of ChargeBaseController
 *
 * @author francesco.colamonici
 */
abstract class ChargeBaseController extends Controller {

  /**
   * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
   * using two-column layout. See 'protected/views/layouts/column2.php'.
   */
  public $layout = '//layouts/column1';
  public $defaultAction = 'create';

  /**
   * @return array action filters
   */
  public function filters() {
    return array(
        'accessControl', // perform access control for CRUD operations
        'postOnly + delete', // we only allow deletion via POST request
        'ajaxOnly + getTasks, getGrid'
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
            'actions' => array('viewMonth', 'getTasks', 'getGrid'),
            'users' => array('*'),
        ),
        array('allow',
            'actions' => array('create'),
            'users' => array('@'),
            'roles' => array('createCharge')
        ),
        array('allow',
            'actions' => array('admin'),
            'users' => array('@'),
            'roles' => array('adminCharge')
        ),
        array('allow',
            'actions' => array('adminAll'),
            'users' => array('@'),
            'roles' => array('adminAllCharge')
        ),
        array('deny', // deny all users
            'users' => array('*'),
        ),
    );
  }

  /**
   * Returns the data model based on the primary key given in the GET variable.
   * If the data model is not found, an HTTP exception will be raised.
   * @param integer $id the ID of the model to be loaded
   * @return Charge the loaded model
   * @throws CHttpException
   */
  public function loadModel($id) {
    $model = Charge::model()->findByPk($id);
    if ($model === null)
      throw new CHttpException(404, Yii::t('app', 'Http.404'));
    return $model;
  }

  /**
   * Performs the AJAX validation.
   * @param Charge $model the model to be validated
   */
  protected function performAjaxValidation($model) {
    if (Yii::app()->getRequest()->getPost('ajax') === 'charge-form') {
      echo CActiveForm::validate($model);
      Yii::app()->end();
    }
  }

}
