<?php

require_once 'protected/extensions/yii-mail/vendors/swiftMailer/vendor/autoload.php'; // 正しいパスに合わせてください

class RegistrationController extends Controller
{


    // 新規登録用アクション
    public function actionRegister()
    {
        $model = new RegistrationUser;

        if (isset($_POST['RegistrationUser'])) {
            $model->attributes = $_POST['RegistrationUser'];

            // メール認証キーを生成
            $model->confirm_key = md5(uniqid(rand(), true));

            if ($model->save()) {
                // 2024/9/20 add
                // ユーザの作成に成功した後にロールを追加
                $auth = Yii::app()->authManager;
                // ロール名を指定
                $roleName = 'Developer';
            
                // ロールの割り当てを試行
                if ($auth->assign($roleName, $model->id)) {
                    echo "ロール '{$roleName}' がユーザーID {$model->id} に正常に割り当てられました。";
                } else {
                    echo "ロールの割り当てに失敗しました。";
                    die('end');
                }

                //$this->redirect(array('view','id'=>$model->id));

                // メール送信処理
                // 2024/9/13 動かないのであきらめる
                $this->sendConfirmationEmail($model);
                Yii::app()->user->setFlash('success', 'Registration successful! Please check your email to confirm your account.');
                $this->redirect(array('site/complete'));
            }
        }

        $this->render('register', array('model' => $model));
    }

    // メール送信処理
    protected function sendConfirmationEmail($model)
    {
        $email = $model->email;
        $subject = 'Account Confirmation';
        $confirmationUrl = $this->createAbsoluteUrl('confirm', array('key' => $model->confirm_key));

        $body = "Please click the following link to confirm your account: " . $confirmationUrl;

        $recipientEmail = $email;

	//include $_SERVER['DOCUMENT_ROOT'] . '/myt/send_email.php';

        //Yii::import('application.send_email'); 

        //require_once(Yii::getPathOfAlias('application.send_email') . '.php');
        //require_once(Yii::getPathOfAlias('application.extensions.send_email') . '.php');
        //これは動かない
        //exec('C:/inetpub/wwwroot/myt/send_email.php');

        // 2024/9/20 modified
        // プロトコルとホスト名を含む完全なベースURLを取得
        $fullBaseUrl = Yii::app()->request->getHostInfo() . Yii::app()->request->getBaseUrl();
        // 2024/9/15 add

        // 呼び出すスクリプトのURLを設定
        //$url = 'http://localhost/prj/send_newmember_email.php?email=' . $email;
        //$url = 'http://localhost/prj/send_newmember_email.php?email=' . $email;
        $url = $fullBaseUrl  . '/send_newmember_email.php?email=' . $email;


        $url = $url . '&body=' . urlencode($body);

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
            //echo "Response: " . $response;
        }

        // cURLセッションを閉じる
        curl_close($ch);



    }

    // メール認証用アクション
    public function actionConfirm($key)
    {
        $model = RegistrationUser::model()->findByAttributes(array('confirm_key' => $key));

        if ($model) {
            $model->active = 1; // アカウントを有効化
            $model->confirm_key = null; // 認証キーを無効化
            $model->password_repeat = $model->password;

//Yii::log('Before Save: ' . print_r($model->attributes, true), CLogger::LEVEL_INFO);



if (!$model->save()) {

    // バリデーションエラーやその他のエラーが発生している場合、エラーメッセージを表示
    Yii::log('Save failed with errors: ' . print_r($model->getErrors(), true), CLogger::LEVEL_ERROR);
    Yii::app()->user->setFlash('error', 'Failed to confirm account. Please contact support.');
} else {
    Yii::log('Save failed with errors: ' . print_r($model->getErrors(), true), CLogger::LEVEL_ERROR);
    Yii::app()->user->setFlash('success', 'Your account has been confirmed.');
    $this->redirect(array('site/complete'));
}

//Yii::log('After Save: ' . print_r($model->attributes, true), CLogger::LEVEL_INFO);

            //Yii::app()->user->setFlash('success', 'Your account has been confirmed.');

            $this->redirect(array('site/complete'));
        } else {
            Yii::app()->user->setFlash('error', 'Invalid confirmation key.');

            $this->redirect(array('site/index'));
        }
    }
}
