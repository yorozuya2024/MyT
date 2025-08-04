<?php

class UpdateController extends Controller {

  private $latestVersion = "1.5.1";
  private $configDir = '';
  private $assetsDir = '';
  private $mytVersions = array('1.0.0', '1.1.0', '1.2.0', '1.3.0', '1.3.1', '1.4.0', '1.4.1', '1.4.2', 
								'1.5.0', '1.5.1');

  private function _getMytVersion() {
    return isset(Yii::app()->params['mytVersion']) ? Yii::app()->params['mytVersion'] : '1.0.0';
  }

  protected function beforeAction($action) {
    $this->configDir = Yii::app()->basePath . '/config/';
    $this->assetsDir = Yii::getPathOfAlias('webroot') . '/assets';

    return parent::beforeAction($action);
  }

  public function actionIndex() {
    $currVersion = $this->_getMytVersion();

    $requirements = array(
        array('MyT Version', true, version_compare($currVersion, $this->latestVersion, "<"), "Your MyT Version is {$currVersion} and there is no upgrade avaiable."),
        //File Permission Check
        array('Config directory Writable', true, is_writable($this->configDir), 'Please set write permission to ' . $this->configDir), //Config Dir Check
        array('Asset directory Writable', true, is_writable($this->assetsDir), 'Please set write permission to ' . $this->assetsDir), //Asset Dir Check
    );

    $error = 0;

    foreach ($requirements as $i => $requirement) {
      if ($requirement[1] && !$requirement[2])
        $error = 1; //Check Failed
    }

    if (!$error)
      Yii::app()->user->setState("UpgradeCheckPassed", "1");

    $this->render('index', array(
        'requirements' => $requirements,
        'error' => $error,
        'currVersion' => $currVersion,
        'latestVersion' => $this->latestVersion,
    ));
  }

  public function actionUpgrade() {
    $messages = array();

    if (Yii::app()->user->getState("UpgradeCheckPassed") == "1") {
      $error = 0;

      foreach ($this->mytVersions as $version) {
        if (version_compare($this->_getMytVersion(), $version, '<')) {
          $messages[$version] = $this->{'_doUpgrade' . str_replace('.', '', $version)}();

          foreach ($messages[$version] as $message) {
            if ($message[1])
              $error = 1;
          }

          if ($error)
            break;
        }
      }

      $this->render('upgrade', array(
          'messages' => $messages,
          'error' => $error,
          'version' => $this->latestVersion,
      ));
    }
  }

  private function _doUpgrade110() {
    $configFile = $this->configDir . 'main.php';
    $sampleFile = $this->configDir . 'main_sample.php';

    $messages = array();

    $this->_emptyFolder($this->assetsDir);
    $messages[] = array("Folder {$this->assetsDir} successfully emptied.", false);

    if (copy($configFile, $this->configDir . 'main.bak100.php')) {
      $messages[] = array('Config file backup created', false);

      if (@unlink($configFile)) {
        $messages[] = array('Removed old config file', false);

        $sampleConfig = @file_get_contents($sampleFile);

        if (!empty($sampleConfig)) {
          $data = $this->_getHostAndDbName(Yii::app()->db->connectionString);

          $vars = array('<APP_NAME>' => Yii::app()->name,
              '<APP_LANGUAGE>' => 'en',
              '<DATABASE_HOST>' => $data['host'],
              '<DATABASE_NAME>' => $data['dbname'],
              '<DATABASE_USER>' => Yii::app()->db->username,
              '<DATABASE_PASS>' => Yii::app()->db->password,
              '<DATABASE_PREFIX>' => Yii::app()->db->tablePrefix,
          );

          if (isset(Yii::app()->urlManager->urlFormat) && Yii::app()->urlManager->urlFormat == 'path') {
            $vars = array_merge($vars, array('/* -- REWRITE URL --' => '',
                '-- REWRITE URL -- */' => ''));
          }

          $sampleConfig = str_replace(array_keys($vars), array_values($vars), $sampleConfig);

          if (@file_put_contents($configFile, $sampleConfig)) {
            $messages[] = array('New config file created', false);

            $params = $this->_getParams();
            $params['language'] = "en";
            $params['mytVersion'] = "1.1.0";

            if ($this->_saveParams($params)) {
              $messages[] = array('New parameters added', false);
            } else {
              $messages[] = array('Cannot add new parameters', true);
            }
          } else {
            $messages[] = array('Cannot write new config file: ' . $configFile, true);
          }
        } else {
          $messages[] = array('Cannot open file ' . $sampleFile, true);
        }
      } else {
        $messages[] = array('Cannot remove old config file', true);
      }
    } else {
      $messages[] = array('Cannot backup ' . $configFile, true);
    }

    return $messages;
  }

  private function _doUpgrade120() {
    $transaction = Yii::app()->getDb()->beginTransaction();
    $messages = array();

    try {
      Yii::app()->getDb()->createCommand()->addColumn('{{task}}', 'parent_id', 'integer');
      $messages[] = array('Column parent_id created', false);

      Yii::app()->getDb()->createCommand()->createIndex('task_ibfk_3', '{{task}}', 'parent_id');
      $messages[] = array('Index on parent_id created', false);

      Yii::app()->getDb()->createCommand()->addForeignKey(Yii::app()->getDb()->tablePrefix . 'task_ibfk_3', '{{task}}', 'parent_id', '{{task}}', 'id', NULL, NULL);
      $messages[] = array('Foreign key on parent_id created', false);

      $transaction->commit();
    } catch (Exception $ex) {
      $transaction->rollback();

      throw( $ex );
    }

    $params = $this->_getParams();
    $params['taskIdLength'] = 5;
    $params['mytVersion'] = "1.2.0";

    if ($this->_saveParams($params)) {
      $messages[] = array('New parameters added', false);
    } else {
      $messages[] = array('Cannot add new parameters', true);
    }

    return $messages;
  }

  private function _doUpgrade130() {
    $transaction = Yii::app()->getDb()->beginTransaction();
    $messages = array();

    $table = array(
        array('id' => 'pk',
            'name' => 'varchar(30) NOT NULL', //Need to check for other DBMS
            'order_by' => 'integer unsigned NOT NULL', //Need to check for other DBMS
            'default_flg' => 'boolean', //Need to check for other DBMS
            'active_flg' => 'boolean NOT NULL DEFAULT 1', //Need to check for other DBMS
        ),
        'ENGINE=InnoDB'
    );
    $taskTypes = array(
        array(
            'name' => 'Bug',
            'order_by' => 0,
            'default_flg' => false,
        ),
        array(
            'name' => 'Enhancement',
            'order_by' => 1,
            'default_flg' => false,
        ),
        array(
            'name' => 'Task',
            'order_by' => 2,
            'default_flg' => true,
        ),
    );

    try {
      Yii::app()->getDb()->createCommand()->createTable('{{task_type}}', $table[0], $table[1]);
      $messages[] = array('Table for Task Types created', false);

      Yii::app()->getDb()->createCommand()->update('{{task}}', array('type' => new CDbExpression('type + 1')));
      $messages[] = array('Current Task Types updated', false);

      Yii::app()->getDb()->createCommand()->createIndex('status', '{{task}}', 'status');
      $messages[] = array('Index on Task.status created', false);

      Yii::app()->getDb()->createCommand()->createIndex('type', '{{task}}', 'type');
      $messages[] = array('Index on Task.type created', false);

      foreach ($taskTypes as $type) {
        $taskStatus = new TaskType;
        $taskStatus->attributes = $type;
        $taskStatus->save(false);
      }
      $messages[] = array('Task Types loaded', false);

      $transaction->commit();
    } catch (Exception $ex) {
      $transaction->rollback();

      throw( $ex );
    }

    $params = $this->_getParams();
    $params['mytVersion'] = '1.3.0';

    if ($this->_saveParams($params)) {
      $messages[] = array('New parameters added', false);
    } else {
      $messages[] = array('Cannot add new parameters', true);
    }

    return $messages;
  }
  
  private function _doUpgrade131() {
    $transaction = Yii::app()->getDb()->beginTransaction();
    $messages = array();

    try {
      Yii::app()->getDb()->createCommand()->addColumn('{{project}}', 'chargeable_flg', 'boolean NOT NULL DEFAULT 0');
      $messages[] = array('Column Project.chargeable_flg created', false);

      Yii::app()->getDb()->createCommand()->update('{{project}}', array('chargeable_flg' => 1));
      $messages[] = array('Project.chargeable_flg initialized', false);

      $transaction->commit();
    } catch (Exception $ex) {
      $transaction->rollback();

      throw( $ex );
    }

    $params = $this->_getParams();
    $params['mytVersion'] = "1.3.1";

    if ($this->_saveParams($params)) {
      $messages[] = array('New parameters added', false);
    } else {
      $messages[] = array('Cannot add new parameters', true);
    }

    return $messages;
  }

  private function _doUpgrade140() {
    $transaction = Yii::app()->getDb()->beginTransaction();
    $messages = array();

    $chargeItemId = array_search('Charge Item', Project::model()->getStatusList());

    try {
      Yii::app()->getDb()->createCommand()->update('{{project}}', array('chargeable_flg' => 1), 'status = '.$chargeItemId);
      $messages[] = array('Project.chargeable_flg updated', false);

      $transaction->commit();
    } catch (Exception $ex) {
      $transaction->rollback();

      throw( $ex );
    }

    $params = $this->_getParams();
    $params['mytVersion'] = "1.4.0";

    if ($this->_saveParams($params)) {
      $messages[] = array('New parameters added', false);
    } else {
      $messages[] = array('Cannot add new parameters', true);
    }

    return $messages;
  }

  private function _doUpgrade141() {
    $messages = array();

    $params = $this->_getParams();
    $params['mytVersion'] = "1.4.1";

    if ($this->_saveParams($params)) {
      $messages[] = array('New parameters added', false);
    } else {
      $messages[] = array('Cannot add new parameters', true);
    }

    return $messages;
  }
  
  private function _doUpgrade142() {
    $transaction = Yii::app()->getDb()->beginTransaction();
    $messages = array();
	
	$count = 0;

    try {
      $rows = Yii::app()->getDb()->createCommand()
	  ->select('id, last_upd, start_date, created')
	  ->from('{{task}}')
	  ->where('created = \'0000-00-00 00:00:00\' OR last_upd = \'0000-00-00 00:00:00\'')
	  ->andWhere('start_date IS NOT NULL')
	  ->queryAll();
	  
	  foreach( $rows as $row )
	  {
		  $newCreated = date("Y-m-d H:i:s", strtotime($row['start_date']));
		  
		  $columnsToUpdate = array();
		  
		  if($row['created'] == '0000-00-00 00:00:00')
			$columnsToUpdate['created'] = $newCreated;
		
		  if($row['last_upd'] == '0000-00-00 00:00:00')
			$columnsToUpdate['last_upd'] = $newCreated;
		  
		  Yii::app()->getDb()->createCommand()->update('{{task}}', $columnsToUpdate, 'id = '.$row['id']);
		  
		  $count++;
	  }
	  
      $transaction->commit();
	  
	  $messages[] = array("Task creation date: {$count} records updated", false);
    } catch (Exception $ex) {
      $transaction->rollback();

      throw( $ex );
    }

    $params = $this->_getParams();
    $params['mytVersion'] = "1.4.2";

    if ($this->_saveParams($params)) {
      $messages[] = array('New parameters added', false);
    } else {
      $messages[] = array('Cannot add new parameters', true);
    }

    return $messages;
  }

  private function _doUpgrade150() {
    $messages = array();
	
	$table = array(
          array('id' => 'pk',
              'created' => 'datetime NOT NULL',
              'created_by' => 'integer NOT NULL',
              'last_upd' => 'timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP', //check
              'last_upd_by' => 'integer',
              'entity' => 'string NOT NULL',
			  'entity_id' => 'integer NOT NULL',
              'body' => 'text',
              'status' => 'boolean NOT NULL'
          ),
          'ENGINE=InnoDB'
      );
	
	$transaction = Yii::app()->getDb()->beginTransaction();
	
    try {
      Yii::app()->getDb()->createCommand()->createTable('{{comment}}', $table[0], $table[1]);
      $messages[] = array('Table for Comments created', false);
	  
	  Yii::app()->getDb()->createCommand()->truncateTable('{{session}}'); //need to logout all
	  $messages[] = array('Session Table truncated', false);
	  
      $transaction->commit();
    } catch (Exception $ex) {
      $transaction->rollback();

      throw( $ex );
    }
	

    $params = $this->_getParams();
    $params['theme'] = 'fluid';
    $params['mytVersion'] = "1.5.0";

    if ($this->_saveParams($params)) {
      $messages[] = array('New parameters added', false);
    } else {
      $messages[] = array('Cannot add new parameters', true);
    }
	
	Yii::app()->user->logout(); //Logout current user

    return $messages;
  }
  
  private function _doUpgrade151() {
    $messages = array();
	$transaction = Yii::app()->getDb()->beginTransaction();
	
	try
	{
		foreach(Project::model()->getChargeProjectsList() as $k => $chargeProject) 
		{
			$newPrefix = str_pad($k, 3, '-', STR_PAD_LEFT);
			
			Yii::app()->getDb()->createCommand()->update('{{project}}', array('prefix' => $newPrefix), 
																		"name = '{$chargeProject}'");
																			  
			$messages[] = array("Prefix for project '{$chargeProject}' updated to '{$newPrefix}'", false);
		}
		
		$transaction->commit();
    } catch (Exception $ex) {
      $transaction->rollback();

      throw( $ex );
    }

    $params = $this->_getParams();
    $params['mytVersion'] = "1.5.1";

    if ($this->_saveParams($params)) {
      $messages[] = array('New parameters added', false);
    } else {
      $messages[] = array('Cannot add new parameters', true);
    }

    return $messages;
  }

  private function _getParams() {
    return unserialize(base64_decode(@file_get_contents($this->configDir . 'params.inc')));
  }

  private function _saveParams($params) {
    $paramsFile = base64_encode(serialize($params));

    return @file_put_contents($this->configDir . 'params.inc', $paramsFile);
  }

  private function _getHostAndDbName($connectionString) {
    $data = array();
    $matches = array();

    preg_match("/host=([^;]*)/", $connectionString, $matches);

    $data['host'] = $matches[1];

    preg_match("/dbname=([^;]*)/", $connectionString, $matches);

    $data['dbname'] = $matches[1];

    return $data;
  }

  private function _emptyFolder($dir) {
    $handle = opendir($dir);

    if ($handle === false) {
      throw new Exception('Unable to open directory: ' . $dir);
    }

    while (($file = readdir($handle) ) !== false) {
      if ($file === '.' || $file === '..')
        continue;

      $file = $dir . DIRECTORY_SEPARATOR . $file;

      if (is_dir($file))
        CFileHelper::removeDirectory($file);
      else if (is_file($file))
        @unlink($file);
    }

    closedir($handle);
  }

}
