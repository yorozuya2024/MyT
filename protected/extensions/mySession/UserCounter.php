<?php

/**
 * Description of UserCounter
 *
 * @author francesco.colamonici
 */
class UserCounter {

  /**
   * @var array List of distinct users online
   */
  private $_online;

  /**
   * @var CDbHttpSession Session handler
   */
  private $_session = null;

  public function __construct() {
    
  }

  public function init() {
    $this->_online = array();
    $this->_session = Yii::app()->getSession();
  }

  public function getOnline() {
    return $this->_online;
  }

  public function refresh() {

    $timeFunc = $this->_session->getDbConnection()->getDriverName() == 'sqlite' ? 'strftime(\'%s\', \'now\')' : 'UNIX_TIMESTAMP()';

    $this->_online = $this->_session->getDbConnection()
            ->createCommand()
            ->selectDistinct('user_id')
            ->from($this->_session->sessionTableName)
            ->where("user_id <> 0 AND expire > {$timeFunc}")
            ->queryAll();
  }

}
