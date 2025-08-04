<?php

Yii::import('application.components.navigation.*');

/**
 * Description of Navigator
 *
 * @author francesco.colamonici
 */
class Navigator extends ChargeNavigation {

  public static function clearProject() {
    $navigation = self::getNavigation();
    $navigation['project']['type'] = 'my';
    $navigation['project']['id'] = '';
    Yii::app()->user->setState('navigation', $navigation);
  }

  public static function clearTask() {
    $navigation = self::getNavigation();
    $navigation['task']['type'] = 'my';
    $navigation['task']['id'] = '';
    Yii::app()->user->setState('navigation', $navigation);
  }

  public static function clearCharge() {
    $navigation = self::getNavigation();
    $navigation['charge']['type'] = 'my';
    Yii::app()->user->setState('navigation', $navigation);
  }

  public static function clear() {
    self::clearCharge();
    self::clearTask();
    self::clearProject();
    self::home(false);
  }

}
