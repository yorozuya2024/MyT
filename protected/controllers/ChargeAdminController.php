<?php

Yii::import('application.controllers.ChargeCreateController');

/**
 * Description of ChargeAdminController
 *
 * @author francesco.colamonici
 */
class ChargeAdminController extends ChargeCreateController {

  /**
   * Manages all models.
   */
  public function actionAdmin($trkq = null) {
    if (is_null($trkq))
      Yii::app()->user->setState('charge_query', '');

    $model = new Charge('search');
    $model->unsetAttributes();  // clear any default values
    $charge = Yii::app()->getRequest()->getParam('Charge');
    if ($charge === null) {
      $query = Yii::app()->user->getState('charge_query', '');
      if ($query !== '')
        $model->attributes = unserialize($query);
    } else {
      $model->attributes = $charge;
      Yii::app()->user->setState('charge_query', serialize($charge));
    }

    $this->render('admin', array(
        'model' => $model,
    ));
  }

  /**
   * Manages all models.
   */
  public function actionAdminAll($trkq = null) {
    if (is_null($trkq))
      Yii::app()->user->setState('charge_query', '');

    $model = new Charge('search');
    $model->unsetAttributes();  // clear any default values
    $charge = Yii::app()->getRequest()->getParam('Charge');
    if ($charge === null) {
      $query = Yii::app()->user->getState('charge_query', '');
      if ($query !== '')
        $model->attributes = unserialize($query);
    } else {
      $model->attributes = $charge;
      Yii::app()->user->setState('charge_query', serialize($charge));
    }

    $this->render('adminAll', array(
        'model' => $model,
    ));
  }

}
