<?php

class UserController extends Controller {
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
            'actions' => array('index', 'view', 'viewSimple', 'resetpassword', 'confirmkey'),
            'users' => array('*'),
        ),
        array('allow',
            'actions' => array('create'),
            'users' => array('@'),
            'roles' => array('createUser')
        ),
        array('allow',
            'actions' => array('update'),
            'users' => array('@'),
            'roles' => array('updateUser')
        ),
        array('allow',
            'actions' => array('delete'),
            'users' => array('@'),
            'roles' => array('deleteUser')
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
   * Displays a particular model.
   * @param integer $id the ID of the model to be displayed
   */
  public function actionView($id) {
    if (!(Yii::app()->user->checkAccess('createUser') || Yii::app()->user->checkAccess('indexAllUser')))
      $this->redirect(array('viewSimple', 'id' => $id));

    $this->render('view', array(
        'model' => $this->loadModel($id),
    ));
  }

  /**
   * Displays a particular model.
   * @param integer $id the ID of the model to be displayed
   */
  public function actionViewSimple($id) {
    $this->render('viewSimple', array(
        'model' => $this->loadModel($id),
    ));
  }

  /**
   * Creates a new model.
   * If creation is successful, the browser will be redirected to the 'view' page.
   */
  public function actionCreate() {
    $model = new User;
    $model->gender = 'M';
    $model->daily_hours = 8.0;
    $model->notifications = Yii::app()->params['notifications'];
    $model->page_size = Yii::app()->params['pageSize'];

    // Uncomment the following line if AJAX validation is needed
    // $this->performAjaxValidation($model);

    if (isset($_POST['User'])) {
      $model->attributes = $_POST['User'];
      //$model->password = crypt($_POST['User']['password']);
      $model->notifications = $_POST['User']['notifications'];

      if ($_POST['User']['optAvatar'] === 'Y') {
        $uploadedFile = CUploadedFile::getInstance($model, 'avatar');
        if ($uploadedFile !== null) {
          $fileName = time() . '_' . $uploadedFile->name;
          $model->avatar = $fileName;
        }

        if ($model->save()) {

          if ($uploadedFile !== null) {
            $uploadedFile->saveAs(Yii::app()->params['avatarPath'] . $fileName);
            $image = new EasyImage(Yii::app()->params['avatarPath'] . $fileName);
            if ($image->image()->width > Yii::app()->params['imageDimension']['maxWidth'] || $image->image()->height > Yii::app()->params['imageDimension']['maxHeight'])
              $image->resize(Yii::app()->params['imageDimension']['maxWidth'], Yii::app()->params['imageDimension']['maxHeight']);
            $image->save(Yii::app()->params['avatarPath'] . $fileName);
          }
          $this->_mergeRoles($model);
          Yii::app()->user->setFlash('success', Yii::t('app', 'User.create.success.{name}', array('{name}' => $model->username)));
          $this->redirect(array('view', 'id' => $model->id));
        }
      } else {
        if ($model->save()) {
          $this->_mergeRoles($model);
          Yii::app()->user->setFlash('success', Yii::t('app', 'User.create.success.{name}', array('{name}' => $model->username)));
          $this->redirect(array('view', 'id' => $model->id));
        }
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
    $oldAvatar = $model->avatar;

    // Uncomment the following line if AJAX validation is needed
    // $this->performAjaxValidation($model);

    if (isset($_POST['User'])) {
      $model->attributes = $_POST['User'];
      $model->notifications = $_POST['User']['notifications'];

      if ($model->id === Yii::app()->user->id)
        Yii::app()->user->pageSize = intval($_POST['User']['page_size']) < 1 ? Yii::app()->params['pageSize'] : intval($_POST['User']['page_size']);

      if ($_POST['User']['optAvatar'] === 'Y') {
        $uploadedFile = CUploadedFile::getInstance($model, 'avatar');
        if ($uploadedFile !== null) {
          $fileName = time() . '_' . $uploadedFile->name;
          $model->avatar = $fileName;
        }
		else
		{
			$model->avatar = $oldAvatar;
		}

        if ($model->save()) {
          if ($uploadedFile !== null) {
			  
			  if(!empty($oldAvatar))
			  {
				@unlink(Yii::app()->params['avatarPath'] . $oldAvatar);
			  }
			  
            $uploadedFile->saveAs(Yii::app()->params['avatarPath'] . $fileName);
            $image = new EasyImage(Yii::app()->params['avatarPath'] . $fileName);
            if ($image->image()->width > Yii::app()->params['imageDimension']['maxWidth'] || $image->image()->height > Yii::app()->params['imageDimension']['maxHeight'])
              $image->resize(Yii::app()->params['imageDimension']['maxWidth'], Yii::app()->params['imageDimension']['maxHeight']);
            $image->save(Yii::app()->params['avatarPath'] . $fileName);
          }
          $this->_mergeRoles($model);
          Yii::app()->user->setFlash('success', Yii::t('app', 'User.update.success.{name}', array('{name}' => $model->username)));
          $this->redirect(array('view', 'id' => $model->id));
        }
      } else {
        $model->avatar = new CDbExpression('NULL');
        if ($model->save()) {
          if (!empty($oldAvatar))
            @unlink(Yii::app()->params['avatarPath'] . $oldAvatar);
          $this->_mergeRoles($model);
          Yii::app()->user->setFlash('success', Yii::t('app', 'User.update.success.{name}', array('{name}' => $model->username)));
          $this->redirect(array('view', 'id' => $model->id));
        }
      }
    }

    $this->render('update', array(
        'model' => $model,
    ));
  }

  /**
   * Merge Roles.
   * @param User $model
   */
  private function _mergeRoles($model) {
    if (isset($_POST['AuthItem'])) {
      /* @var $auth CDbAuthManager */
      $auth = Yii::app()->getAuthManager();

      foreach ($_POST['AuthItem'] as $role => $assoc) {
        $assoc = CPropertyValue::ensureBoolean($assoc);
        if ($assoc && !$auth->isAssigned($role, $model->id))
          $auth->assign($role, $model->id);
        elseif (!$assoc && $auth->isAssigned($role, $model->id))
          $auth->revoke($role, $model->id);
      }

      $auth->save();
      Yii::app()->cache->flush();
    }
  }

  /**
   * Deletes a particular model.
   * If deletion is successful, the browser will be redirected to the 'admin' page.
   * @param integer $id the ID of the model to be deleted
   */
  public function actionDelete($id) {
    $this->loadModel($id)->delete();
    Yii::app()->user->setFlash('success', Yii::t('app', 'User.delete.success.{id}', array('{id}' => $id)));

    // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
    if (!isset($_GET['ajax']))
      $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('index'));
  }

  /**
   * Lists all models.
   */
  public function actionIndex() {
    $model = new User('search');
    $model->unsetAttributes();  // clear any default values
    if (isset($_GET['User']))
      $model->attributes = $_GET['User'];

    $this->render('index', array(
        'model' => $model,
    ));
  }

  /**
   * Reset password management
   */
  public function actionResetPassword() {
    $model = new User('resetpass');

    if (isset($_POST['User'])) {
      $model->attributes = $_POST['User'];

      if ($model->validate() && $user = $model->findByAttributes(array('email' => $_POST['User']['email']))) {
        $user->confirm_key = $this->_generateKey($user->email);
        $user->save();

        $message = Yii::t('app', 'User.reset.password.email.{name}.{url}', array(
                    '{name}' => $user->username,
                    '{url}' => $this->createAbsoluteUrl('user/confirmKey', array('key' => $user->confirm_key, 'email' => $user->email))
        ));

        $title = Yii::t('app', 'User.reset.password.title');

        // 2024/9/15 debug modified 
        //$this->_sendMail($user->email, $title, $message);

        $email = $user->email;
        // 呼び出すスクリプトのURLを設定

        // 2024/9/20 modified
        // プロトコルとホスト名を含む完全なベースURLを取得
        $fullBaseUrl = Yii::app()->request->getHostInfo() . Yii::app()->request->getBaseUrl();
        //$url = 'http://localhost/prj/send_email.php?email=' . $email;
        $url = $fullBaseUrl . '/send_resetpassword_email.php?email=' . $email;

        // 2024/9/20 add
        $url = $url . '&body=' . urlencode($message);

        // cURLセッションを初期化
        $ch = curl_init();

        if ($ch === false) {
            // cURL初期化に失敗した場合のエラーハンドリング
            echo "cURLの初期化に失敗しました";
            return;
        }

        // cURLオプションを設定
        curl_setopt($ch, CURLOPT_URL, $url);          // リクエスト先のURL
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  // 実行結果を文字列で返す
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);  // リダイレクトを許可
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);           // タイムアウト設定（秒）
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);


        // 必要に応じてPOSTリクエストやパラメータを送信
        curl_setopt($ch, CURLOPT_POST, true);  // POSTメソッドを使用
        $postData = [
            'email' => $email,
            'subject' => 'Test Email',
            'message' => 'This is a test email.'
        ];
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData)); // パラメータを設定

        // cURLリクエストを実行
        $response = curl_exec($ch);

        // エラーがあるか確認
        if (curl_errno($ch)) {
            // エラー処理
            $error_msg = curl_error($ch);
            echo "cURL Error: " . $error_msg;
        } else {
            // 正常なレスポンスを取得
            echo "Response: " . $response;
        }

        // cURLセッションを閉じる
        curl_close($ch);


        Yii::app()->user->setFlash('resetpassword', Yii::t('app', 'User.reset.password.message'));
        $this->refresh();
      }
    }

    $this->render('requestreset', array(
        'model' => $model
    ));
  }

  public function actionConfirmKey($key, $email) {
    $user = User::model()->findByAttributes(array('email' => $email));

    if ($user != null && $user->confirm_key == $key) {
      if (isset($_POST['User'])) {
        $user->scenario = 'confirmkey';
        $user->attributes = $_POST['User'];

        $user->confirm_key = $this->_generateKey($user->email);

        if ($user->save()) {
          Yii::app()->user->setFlash('passwordchanged', Yii::t('app', 'User.change.password.message'));
          $this->redirect(array('site/login'));
        }
      }
    } else {
      throw new CHttpException(404, Yii::t('app', 'Http.404'));
    }

    $this->render('confirmkey', array(
        'model' => $user
    ));
  }

  function _sendMailLegacy($to, $subject, $body) {
    $adminEmail = Yii::app()->params['adminEmail'];
    $name = Yii::app()->params['name'];
    $subject = Yii::app()->params['subjectPrefixEmail'] . $subject;

    $name = '=?UTF-8?B?' . base64_encode($name) . '?=';
    $subject = '=?UTF-8?B?' . base64_encode($subject) . '?=';
    $headers = "From: $name <{$adminEmail}>\r\n" .
            "Reply-To: {$adminEmail}\r\n" .
            "MIME-Version: 1.0\r\n" .
            "Content-type: text/plain; charset=UTF-8";

    mail($to, $subject, $body, $headers);
  }
  
  function _sendMail( $to, $subject, $body )
  {
	$email = new EmailNotification;
    $email->sendEmail( $to, $subject, $body, $type = 'plain' );
  }

  function _generateKey($email) {
    return sha1(mt_rand(10000, 99999) . time() . $email);
  }

  /**
   * Manages all models.
   */
  public function actionAdmin() {
    $model = new User('search');
    $model->unsetAttributes();  // clear any default values
    if (isset($_GET['User']))
      $model->attributes = $_GET['User'];

    $this->render('admin', array(
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
