<?php

/**
 *
 * ConfigForm class.
 * ContactForm is the data structure for keeping Application params.
 * It is used by the 'index' action of 'ConfigController'.
 *
 * @author francesco.colamonici
 */
class ConfigForm extends CFormModel {

  public $adminEmail;
  public $attachments;
  public $subjectPrefixEmail;
  public $paramsEmail;
  public $avatarPath;
  public $imageDimension;
  public $language;
  public $theme;
  public $name;
  public $pageSize;
  public $tabs;
  public $enableDebugToolbar;
  public $notifications;
  public $mytVersion;
  public $taskIdLength;

  public function rules() {
    return array(
        array('adminEmail, avatarPath, language, theme, name, pageSize', 'required'),
        array('adminEmail', 'email'),
        array('attachments[enable]', 'default', 'value' => true),
        array('attachments[enable]', 'boolean'),
        array('attachments[storage], attachments[maxSize]', 'required'),
        array('attachments[maxSize]', 'numerical', 'integerOnly' => true, 'min' => 1),
        array('paramsEmail[method]', 'required'),
        array('imageDimension[maxSize], imageDimension[maxWidth], imageDimension[maxHeight], imageDimension[maxWidthThumb], imageDimension[maxHeightThumb]', 'required'),
        array('imageDimension[maxSize], imageDimension[maxWidth], imageDimension[maxHeight], imageDimension[maxWidthThumb], imageDimension[maxHeightThumb]', 'numerical', 'integerOnly' => true, 'min' => 0),
        array('notifications[taskAssociation][android], notifications[taskAssociation][email]', 'boolean'),
        array('notifications[projectAssociation][android], notifications[projectAssociation][email]', 'boolean'),
        array('enableDebugToolbar, pageSize', 'numerical', 'integerOnly' => true, 'min' => 1, 'max' => 255),
        array('tabs[Charge], tabs[Project], tabs[Task], tabs[User]', 'boolean'),
        array('language', 'in', 'range' => $this->languageList, 'allowEmpty' => false),
        array('theme', 'in', 'range' => $this->themeList, 'allowEmpty' => false),
        array('taskIdLength', 'numerical', 'integerOnly' => true, 'min' => 4, 'max' => 10),
        array('taskIdLength', 'default', 'value' => 5),
    );
  }

  /**
   * Declares customized attribute labels.
   * If not declared here, an attribute would have a label that is
   * the same as its name with the first letter in upper case.
   */
  public function attributeLabels() {
    return array(
        'enableDebugToolbar' => Yii::t('attributes', 'ConfigForm.enableDebugToolbar'),
        'language' => Yii::t('attributes', 'ConfigForm.language'),
        'theme' => Yii::t('attributes', 'ConfigForm.theme'),
        'name' => Yii::t('attributes', 'ConfigForm.name'),
        'pageSize' => Yii::t('attributes', 'ConfigForm.pageSize'),
        'taskIdLength' => Yii::t('attributes', 'ConfigForm.taskIdLength'),
        'avatarPath' => Yii::t('attributes', 'ConfigForm.avatarPath'),
        'imageDimension' => Yii::t('attributes', 'ConfigForm.imageDimension'),
        'adminEmail' => Yii::t('attributes', 'ConfigForm.adminEmail'),
        'subjectPrefixEmail' => Yii::t('attributes', 'ConfigForm.subjectPrefixEmail'),
        'imageDimension' => Yii::t('attributes', 'ConfigForm.imageDimension'),
        'imageDimension' => Yii::t('attributes', 'ConfigForm.imageDimension'),
        'imageDimension' => Yii::t('attributes', 'ConfigForm.imageDimension'),
        'imageDimension' => Yii::t('attributes', 'ConfigForm.imageDimension'),
        'imageDimension' => Yii::t('attributes', 'ConfigForm.imageDimension'),
        //
        'attachments[apiKey]' => Yii::t('attributes', 'ConfigForm.attachments.apiKey'),
        'attachments[apiSID]' => Yii::t('attributes', 'ConfigForm.attachments.apiSID'),
        'attachments[apiUser]' => Yii::t('attributes', 'ConfigForm.attachments.apiUser'),
        'attachments[apiPassword]' => Yii::t('attributes', 'ConfigForm.attachments.apiPassword'),
        'attachments[enable]' => Yii::t('attributes', 'ConfigForm.attachments.enable'),
        'attachments[extList]' => Yii::t('attributes', 'ConfigForm.attachments.extList'),
        'attachments[maxSize]' => Yii::t('attributes', 'ConfigForm.attachments.maxSize'),
        'attachments[path]' => Yii::t('attributes', 'ConfigForm.attachments.path'),
        'attachments[storage]' => Yii::t('attributes', 'ConfigForm.attachments.storage'),
        'imageDimension[maxSize]' => Yii::t('attributes', 'ConfigForm.imageDimension.maxSize'),
        'imageDimension[maxWidth]' => Yii::t('attributes', 'ConfigForm.imageDimension.maxWidth'),
        'imageDimension[maxHeight]' => Yii::t('attributes', 'ConfigForm.imageDimension.maxHeight'),
        'imageDimension[maxWidthThumb]' => Yii::t('attributes', 'ConfigForm.imageDimension.maxWidthThumb'),
        'imageDimension[maxHeightThumb]' => Yii::t('attributes', 'ConfigForm.imageDimension.maxHeightThumb'),
        'notifications[googleApiKey]' => Yii::t('attributes', 'ConfigForm.notifications.googleApiKey'),
        'notifications[taskAssociation]' => Yii::t('attributes', 'ConfigForm.notifications.taskAssociation'),
        'notifications[taskAssociation][android]' => Yii::t('attributes', 'ConfigForm.notifications.taskAssociation.android'),
        'notifications[taskAssociation][email]' => Yii::t('attributes', 'ConfigForm.notifications.taskAssociation.email'),
        'notifications[projectAssociation]' => Yii::t('attributes', 'ConfigForm.notifications.projectAssociation'),
        'notifications[projectAssociation][android]' => Yii::t('attributes', 'ConfigForm.notifications.projectAssociation.android'),
        'notifications[projectAssociation][email]' => Yii::t('attributes', 'ConfigForm.notifications.projectAssociation.email'),
        'paramsEmail' => Yii::t('attributes', 'ConfigForm.paramsEmail'),
        'paramsEmail[method]' => Yii::t('attributes', 'ConfigForm.paramsEmail.method'),
        'paramsEmail[smtp]' => Yii::t('attributes', 'ConfigForm.paramsEmail.smtp'),
        'paramsEmail[smtp][host]' => Yii::t('attributes', 'ConfigForm.paramsEmail.smtp.host'),
        'paramsEmail[smtp][port]' => Yii::t('attributes', 'ConfigForm.paramsEmail.smtp.port'),
        'paramsEmail[smtp][encryption]' => Yii::t('attributes', 'ConfigForm.paramsEmail.smtp.encryption'),
        'paramsEmail[smtp][username]' => Yii::t('attributes', 'ConfigForm.paramsEmail.smtp.username'),
        'paramsEmail[smtp][password]' => Yii::t('attributes', 'ConfigForm.paramsEmail.smtp.password'),
        'tabs[Project]' => Yii::t('attributes', 'ConfigForm.tabs.project'),
        'tabs[Task]' => Yii::t('attributes', 'ConfigForm.tabs.task'),
        'tabs[User]' => Yii::t('attributes', 'ConfigForm.tabs.user'),
        'tabs[Charge]' => Yii::t('attributes', 'ConfigForm.tabs.charge'),
        'tabs[Authorization]' => Yii::t('attributes', 'ConfigForm.tabs.authorization'),
    );
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

  public function getThemeList()
  {
    $themes = array();

    $themePath = Yii::getPathOfAlias('webroot.themes');

    chdir( $themePath );

    foreach(scandir($themePath) as $t)
    {
      if( is_dir( $t ) && $t !== '.' && $t !== '..' )
      {
        $themes[$t] = ucfirst($t);
      }
    }

    return $themes;

  }

}
