<?php

/*
 * Database Backup Handler
 */

class DatabaseBackup {

  public $backupPath;
  public $error;
  public $dropIfExists = false;
  public $maxRowsInsert = 200;

  public function __construct() {
    $this->backupPath = YiiBase::getPathOfAlias('application.backups');
  }

  public function createBackup() {
    $date = date('d/m/Y H:i:s');
    $filename = $this->backupPath . '/' . date('Y-m-d_H-i-s') . '_' . $this->_generateRandomNumbers() . '.sql';

    if (function_exists('bzopen')) {
      $filename .= '.bz2';

      $writeFunc = 'bzwrite';
      $closeFunc = 'bzclose';

      $fp = @bzopen($filename, 'w');
    } else if (function_exists('gzopen')) {
      $filename .= '.gz';

      $writeFunc = 'gzwrite';
      $closeFunc = 'gzclose';

      $fp = @gzopen($filename, 'w');
    } else {
      $writeFunc = 'fwrite';
      $closeFunc = 'fclose';

      $fp = @fopen($filename, 'w');
    }

    if ($fp === false) {
      $this->error = 'Error while creating backup file: ' . $filename;
      return false;
    }

    $writeFunc($fp, '/* MyT Database Backup for ' . Yii::app()->getBaseUrl(true) . "\n *  at {$date} \n */\n");
    $writeFunc($fp, "\n" . 'SET FOREIGN_KEY_CHECKS = 0;' . "\n");
    $writeFunc($fp, "\n" . 'SET NAMES \'utf8\';' . "\n\n");

    $db = Yii::app()->db;
    $tables = $db->schema->getTables();

    foreach ($tables as $table) {
      //$row = $db->createCommand('SHOW CREATE TABLE ' . $table->rawName)->queryRow();
      $row = $this->_getCreateTable( $db, $table->rawName );

      if (!isset($row['Create Table'])) {
        $this->error = 'Error while trying to obtain schema for ' . $table->name;
        $closeFunc($fp);
        @unlink($fp);
        return false;
      }

      $writeFunc($fp, '/* Scheme for table ' . $row['Table'] . " */\n");

      if ($this->dropIfExists)
        $writeFunc($fp, 'DROP TABLE IF EXISTS `' . $row['Table'] . '`;' . "\n");

      $writeFunc($fp, $row['Create Table'] . ";\n\n");

      $tableData = $db->createCommand('SELECT * FROM ' . $table->rawName)->queryAll();
      $count = count($tableData);
      $lines = explode("\n", $row['Create Table']);

      if ($count > 0) {
        $writeFunc($fp, 'INSERT INTO ' . $table->rawName . " VALUES\n");

        $i = 1;
        foreach ($tableData as $data) {
          $content = '(';

          foreach ($data as $field => $value) {
            if (!empty($value))
              $content .= $db->quoteValue($value) . ',';
            else {
              foreach ($lines as $line) {
                if (strpos($line, '`' . $field . '`') !== false) {
                  if (preg_match('/(.*NOT NULL.*)/Ui', $line))
                    $content .= $value == "0" ? "'0'," : "'',";
                  else
                    $content .= 'NULL,';
                  break;
                }
              }
            }
          }

          $content = rtrim($content, ',');

          if ($i % $this->maxRowsInsert == 0 && $i < $count)
            $content .= ");\nINSERT INTO " . $table->rawName . " VALUES\n";
          elseif ($i < $count)
            $content .= "),\n";
          else
            $content .= ");\n";

          $writeFunc($fp, $content);
          $i++;
        }
      }
    }

    $closeFunc($fp);

    return true;
  }

  public function getBackupDataProvider() {
    $backups = array();

    $dirHandle = @opendir($this->backupPath);

    if ($dirHandle !== false) {
      while (($filename = readdir($dirHandle)) !== false) {
        $file = $this->backupPath . '/' . $filename;

        if (filetype($file) == 'file') {
          $backups[] = array('filename' => $filename,
              'filedate' => @filemtime($file),
              'filetype' => pathinfo($file, PATHINFO_EXTENSION),
              'filesize' => filesize($file));
        }
      }

      closedir($dirHandle);
    } else {
      $this->error = 'Cannot open directory: ' . $this->backupPath;
    }

    return new CArrayDataProvider($backups, array(
        'id' => 'backups',
        'keyField' => 'filename',
        'sort' => array(
            'attributes' => array(
                'filename', 'filedate', 'filetype', 'filesize',
            ),
            'defaultOrder' => array('filedate' => true)
        ),
        'pagination' => array(
            'pageSize' => 10,
        ),
            )
    );
  }

  public function deleteFile($filename) {
    return @unlink($this->backupPath . '/' . basename($filename));
  }

  public function downloadBackup($filename) {
    $file = $this->backupPath . '/' . basename($filename);
    $fileExt = pathinfo($file, PATHINFO_EXTENSION);

    if (!file_exists($file) || filetype($file) != 'file') {
      $this->error = 'Cannot found file: ' . $file;
      return false;
    }

    if (!in_array($fileExt, array('bz2', 'gz', 'sql'))) {
      $this->error = 'Invalid file type';
      return false;
    }

    header('Content-disposition: attachment; filename=' . $filename);
    header('Content-type: ' . pathinfo($file, PATHINFO_EXTENSION));
    header('Content-length: ' . filesize($file));
    echo file_get_contents($file);
  }

  private function _generateRandomNumbers() {
    return dechex(mt_rand(0, min(0xffffffff, mt_getrandmax())));
  }

  private function _getCreateTable( $db, $tableName ) {

  	if( $db->getDriverName() == 'sqlite' )
  	{
  		$row = $db->createCommand("SELECT name, sql FROM sqlite_master where name = {$tableName}")->queryRow();
  		$row['Create Table'] = $row['sql'];
  		$row['Table'] = $row['name'];
  	}
  	else
  	{
  		$row = $db->createCommand('SHOW CREATE TABLE ' . $tableName)->queryRow();
  	}

  	return $row;

  }

}
