<?php

class AndroidUdidController extends Controller {

  /**
   * @return array action filters
   */
  public function filters() {
    return array(
        'postOnly + register',
    );
  }

  public function actionRegister() {
    $userName = Yii::app()->getRequest()->getPost('userName', '');
    $userPassword = Yii::app()->getRequest()->getPost('userPassword', '');
    $regId = Yii::app()->getRequest()->getPost('regId', '');
    if ($regId === '')
      throw new CHttpException(400, Yii::t('app', 'Android.register.failure.params'));
    $identity = new UserIdentity($userName, $userPassword);
    if ($identity->authenticate()) {
      if (AndroidUdid::model()->findByAttributes(
                      array('user_name' => $userName, 'registration_id' => $regId)
              ) === null) {
        $model = new AndroidUdid;
        $model->user_name = $userName;
        $model->registration_id = $regId;
        if ($model->save())
          throw new CHttpException(201, Yii::t('app', 'Android.register.success'));
        else
          throw new CHttpException(500, Yii::t('app', 'Android.register.failure'));
      } else
        throw new CHttpException(412, Yii::t('app', 'Android.register.notice'));
    } else
      throw new CHttpException(401, Yii::t('app', 'Android.register.failure.login'));
  }

}
