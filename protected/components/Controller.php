<?php

/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class Controller extends CController {

  /**
   * @var string the default layout for the controller view. Defaults to '//layouts/column1',
   * meaning using a single column layout. See 'protected/views/layouts/column1.php'.
   */
  public $layout = '//layouts/column1';

  /**
   * @var array context menu items. This property will be assigned to {@link CMenu::items}.
   */
  public $menu = array();

  /**
   * @var array the breadcrumbs of the current page. The value of this property will
   * be assigned to {@link CBreadcrumbs::links}. Please refer to {@link CBreadcrumbs::links}
   * for more details on how to specify this property.
   */
  public $breadcrumbs = array();

  /**
   * @return string the page title. Defaults to the controller name and the action name.
   */
  public function getPageTitle() {
    $name = ucfirst(basename($this->getId()));
    if ($this->getAction() !== null && strcasecmp($this->getAction()->getId(), $this->defaultAction))
      return Yii::app()->params['name'] . ' - ' . ucfirst($this->getAction()->getId()) . ' ' . $name;
    else
      return Yii::app()->params['name'] . ' - ' . $name;
  }

  protected function overrideLanguage($lang = null) {
    if ($lang === null)
      $lang = Yii::app()->params['language'];
    if (!empty($lang))
      Yii::app()->language = $lang;
  }

  protected function overrideTheme($theme = null) {
    if ($theme === null)
      $theme = Yii::app()->params['theme'];
    if (!empty($theme))
      Yii::app()->theme = $theme;
  }

  protected function beforeAction($action) {
    $this->overrideLanguage();
    $this->overrideTheme();
    return parent::beforeAction($action);
  }

}
