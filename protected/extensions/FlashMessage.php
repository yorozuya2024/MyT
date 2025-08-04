<?php

/**
 * Description of FlashMessage
 *
 * @author francesco.colamonici
 */
class FlashMessage extends CWidget {

  public $classPrefix = 'flash-';
  public $classKeys = array('error', 'notice', 'success');

  /**
   * @Override
   */
  public function init() {
    
  }

  /**
   * @Override
   */
  public function run() {
    foreach (Yii::app()->user->getFlashes() as $key => $message) {
      if (!in_array($key, $this->classKeys))
        Yii::log('Missing Flash Class "' . $this->classPrefix . $key . '"', CLogger::LEVEL_WARNING);
      echo CHtml::tag('div', array('class' => $this->classPrefix . $key), $message), PHP_EOL;
    }
  }

}
