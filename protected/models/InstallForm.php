<?php

/**
 *
 * InstallForm class.
 * Model Class for installation form ( db & general )
 *
 * @author paolo.paragliola
 */
class InstallForm extends CFormModel {

  public $dbType;
  public $dbHost;
  public $dbName;
  public $dbUsername;
  public $dbPassword;
  public $dbTablePrefix;
  public $appName;
  public $appLanguage;
  public $appUsername;
  public $appPassword;
  public $appPasswordConfirm;
  public $appEmail;

  public function rules() {
    return array(
        array('dbType, dbTablePrefix, dbHost', 'required', 'on' => 'dbConfig'),
        array('dbName, dbUsername', 'requiredByMysql', 'on' => 'dbConfig'),
        array('dbPassword', 'safe', 'on' => 'dbConfig'),
        array('dbHost', 'checkConnection', 'on' => 'dbConfig', 'skipOnError' => true),
        array('appName, appLanguage, appUsername, appPassword, appPasswordConfirm, appEmail', 'required', 'on' => 'appConfig'),
        array('appPasswordConfirm', 'compare', 'compareAttribute' => 'appPassword', 'on' => 'appConfig'),
        array('appEmail', 'email', 'on' => 'appConfig'),
    );
  }

  public function attributeLabels() {
    return array(
        'dbType' => Yii::t('attributes', 'InstallForm.dbType'),
        'dbHost' => Yii::t('attributes', 'InstallForm.dbHost'),
        'dbName' => Yii::t('attributes', 'InstallForm.dbName'),
        'dbUsername' => Yii::t('attributes', 'InstallForm.dbUsername'),
        'dbPassword' => Yii::t('attributes', 'InstallForm.dbPassword'),
        'dbTablePrefix' => Yii::t('attributes', 'InstallForm.dbTablePrefix'),
        'appName' => Yii::t('attributes', 'InstallForm.appName'),
        'appLanguage' => Yii::t('attributes', 'InstallForm.appLanguage'),
        'appUsername' => Yii::t('attributes', 'InstallForm.appUsername'),
        'appPassword' => Yii::t('attributes', 'InstallForm.appPassword'),
        'appPasswordConfirm' => Yii::t('attributes', 'InstallForm.appPasswordConfirm'),
        'appEmail' => Yii::t('attributes', 'InstallForm.appEmail'),
    );
  }

  public function requiredByMysql($attribute, $params) {

    if( $this->dbType == 'mysql')
    {
      $validator = CValidator::createValidator('required', $this, $attribute, $params);
      $validator->validate($this);
    }
  }

  public function checkConnection($attribute, $params) {

    switch( $this->dbType )
    {
      case 'sqlite':
        $dsn = 'sqlite:' . $this->dbHost;
      break;

      case 'mysql':
        $dsn = "mysql:host={$this->dbHost};dbname={$this->dbName}";
      break;
    }

    $connection = new CDbConnection($dsn, $this->dbUsername, $this->dbPassword);

    try {
      $connection->active = true;
    } catch (CDbException $e) {
      $this->addError(null, $e->getMessage());
    }

    $connection->active = false;
  }

  public function getLanguageList() {
    $messages = Yii::getPathOfAlias('application.messages');
    chdir($messages);
    $languages = array();
    foreach (scandir($messages) as $l) {
      if (is_dir($l) && $l[0] !== '.')
        $languages[$l] = ucfirst(Yii::app()->locale->getLanguage($l));
    }
    return $languages;
  }

}
