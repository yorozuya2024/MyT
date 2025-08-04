<?php

/**
 * @link http://www.yiiframework.com/wiki/304/setting-application-parameters-dynamically-in-the-back-end/ Dynamic Parameters
 * @author francesco.colamonici
 */
class ConfigController extends Controller {

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
        'ajaxOnly + megaLogin',
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
            'actions' => array('index', 'megaLogin'),
            'users' => array('@'),
            'roles' => array('adminConfig')
        ),
        array('deny', // deny all users
            'users' => array('*'),
        ),
    );
  }

  public function actionMegaLogin($u, $p) {
    Yii::app()->mega->login($u, $p);
    $response = new stdClass();
    $response->key = serialize(Mega::$master_key);
    $response->sid = Mega::$sid;
    echo json_encode($response);
  }

  private function extractConfig(&$form) {
    if (empty($form['paramsEmail']['smtp']['port']))
      unset($form['paramsEmail']['smtp']['port']);
    if (empty($form['paramsEmail']['smtp']['encryption']))
      unset($form['paramsEmail']['smtp']['encryption']);
    return array(
        'adminEmail' => $form['adminEmail'],
        'subjectPrefixEmail' => $form['subjectPrefixEmail'],
        'paramsEmail' => $form['paramsEmail'],
        'avatarPath' => $form['avatarPath'],
        'imageDimension' => $form['imageDimension'],
        'language' => $form['language'],
        'theme' => $form['theme'],
        'taskIdLength' => $form['taskIdLength'],
        'enableDebugToolbar' => $form['enableDebugToolbar'],
        'name' => $form['name'],
        'notifications' => $form['notifications'],
        'pageSize' => $form['pageSize'],
        'tabs' => $form['tabs'],
        'attachments' => $form['attachments'],
		    'mytVersion' => Yii::app()->params['mytVersion'],
    );
  }

  public function actionIndex() {

    function mySetAttributes($value, $key, $model) {
      $model[$key] = $value;
    }

    $file = dirname(__FILE__) . '/../config/params.inc';
    $content = file_get_contents($file);
    $arr = unserialize(base64_decode($content));
    $model = new ConfigForm();
    array_walk($arr, 'mySetAttributes', $model);

    $form = Yii::app()->getRequest()->getPost('ConfigForm');
    if ($form !== null) {
      $config = $this->extractConfig($form);
      $str = base64_encode(serialize($config));
      file_put_contents($file, $str);
      array_walk($config, 'mySetAttributes', $model);

      $this->overrideLanguage($config['language']);
      $this->overrideTheme($config['theme']);
      Yii::app()->user->setFlash('success', Yii::t('app', 'Config.update.success'));
      $this->redirect(array('index'));
    }

    $this->render('index', array('model' => $model));
  }

}
