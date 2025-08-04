<?php

Yii::import('system.web.CDbHttpSession');

/**
 * Description of MyDbHttpSession
 *
 * @author francesco.colamonici
 */
class DbHttpSession extends CDbHttpSession {

  /**
   * Creates the session DB table.
   * @param CDbConnection $db the database connection
   * @param string $tableName the name of the table to be created
   */
  protected function createSessionTable($db, $tableName) {
    $driver = $db->getDriverName();
    if ($driver === 'mysql')
      $blob = 'LONGBLOB';
    elseif ($driver === 'pgsql')
      $blob = 'BYTEA';
    else
      $blob = 'BLOB';
    $db->createCommand()->createTable($tableName, array(
        'id' => 'CHAR(32) PRIMARY KEY',
        'user_id' => 'integer(11) UNSIGNED NOT NULL',
        'expire' => 'integer',
        'data' => $blob,
    ));
    $db->createCommand()->createIndex('user_id', $tableName, 'user_id');
  }

  /**
   * Session write handler.
   * Do not call this method directly.
   * @param string $id session ID
   * @param string $data session data
   * @return boolean whether session write is successful
   */
  public function writeSession($id, $data) {
    // exception must be caught in session write handler
    // http://us.php.net/manual/en/function.session-set-save-handler.php
    try {
      $expire = time() + $this->getTimeout();
      $db = $this->getDbConnection();
      if ($db->createCommand()->select('id')->from($this->sessionTableName)->where('id=:id', array(':id' => $id))->queryScalar() === false)
        $db->createCommand()->insert($this->sessionTableName, array(
            'id' => $id,
            'user_id' => Yii::app()->user->id !== null ? Yii::app()->user->id : 0,
            'data' => $data,
            'expire' => $expire,
        ));
      else
        $db->createCommand()->update($this->sessionTableName, array(
            'user_id' => Yii::app()->user->id !== null ? Yii::app()->user->id : 0,
            'data' => $data,
            'expire' => $expire
                ), 'id=:id', array(':id' => $id));
    } catch (Exception $e) {
      if (YII_DEBUG)
        echo $e->getMessage();
      // it is too late to log an error message here
      return false;
    }
    return true;
  }

  public function getDbConnection() {
    return parent::getDbConnection();
  }

}
