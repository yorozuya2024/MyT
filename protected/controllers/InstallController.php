<?php

class InstallController extends Controller {

  private $version = "1.5.1";
  private $configDir = '';
  private $userImgPath = 'images/user/';
  private $notifications = array(
      'projectAssociation' => array(
          'android' => false,
          'email' => true,
      ),
      'taskAssociation' => array(
          'android' => false,
          'email' => true,
      ),
  );
  private $tables = array('{{android_udid}}' => array(
          array('user_name' => 'string NOT NULL',
              'registration_id' => 'string NOT NULL',
              'PRIMARY KEY (registration_id)',
           //   'KEY user_name (user_name)'
          ),
          'ENGINE=InnoDB'
      ),
      '{{auth_assignment}}' => array(
          array('itemname' => 'string NOT NULL',
              'userid' => 'string NOT NULL',
              'bizrule' => 'text',
              'data' => 'text',
              'PRIMARY KEY (itemname,userid)',
          ),
          'ENGINE=InnoDB'
      ),
      '{{auth_item}}' => array(
          array('name' => 'string NOT NULL',
              'type' => 'integer NOT NULL',
              'description' => 'text',
              'bizrule' => 'text',
              'data' => 'text',
              'PRIMARY KEY (name)',
          ),
          'ENGINE=InnoDB'
      ),
      '{{auth_item_child}}' => array(
          array('parent' => 'string NOT NULL',
              'child' => 'string NOT NULL',
              'PRIMARY KEY (parent,child)',
          ),
          'ENGINE=InnoDB'
      ),
      '{{attachment}}' => array(
          array('id' => 'pk',
              'name' => 'string NOT NULL',
              'type' => 'varchar(30)', //Need to check for other DBMS
              'uri' => 'text',
              'created' => 'date',
              'task_id' => 'integer',
              'project_id' => 'integer',
              'mega_id' => 'char(8)', //Need to check for other DBMS
              'UNIQUE (mega_id)',
        //      'KEY task_id (task_id)',
        //      'KEY project_id (project_id)',
          ),
          'ENGINE=InnoDB'
      ),
      '{{charge}}' => array(
          array('id' => 'pk',
              'created' => 'datetime NOT NULL',
              'created_by' => 'integer NOT NULL',
              'last_upd' => 'datetime',
              'last_upd_by' => 'integer',
              'user_id' => 'integer',
              'project_id' => 'integer',
              'task_id' => 'integer',
              'day' => 'date',
              'hours' => 'float',
//             'KEY created_by (created_by)',
//              'KEY last_upd_by (last_upd_by)',
//              'KEY user_id (user_id)',
//              'KEY project_id (project_id)',
//              'KEY task_id (task_id)'
          ),
          'ENGINE=InnoDB'
      ),
      '{{comment}}' => array(
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
      ),
      '{{counter_save}}' => array(
          array('save_name' => 'varchar(10) NOT NULL', //Need to check for other DBMS
              'save_value' => 'integer unsigned NOT NULL', //Need to check for other DBMS
          ),
          'ENGINE=InnoDB'
      ),
      '{{counter_users}}' => array(
          array('user_ip' => 'varchar(39) NOT NULL', //Need to check for other DBMS
              'user_time' => 'integer unsigned NOT NULL', //Need to check for other DBMS
              'UNIQUE (user_ip)',
          ),
          'ENGINE=InnoDB'
      ),
      '{{project}}' => array(
          array('id' => 'pk',
              'created' => 'datetime NOT NULL',
              'created_by' => 'integer NOT NULL',
              'last_upd' => 'timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP', //check
              'last_upd_by' => 'integer',
              'name' => 'string NOT NULL',
              'prefix' => 'varchar(3) DEFAULT NULL', //Need to check for other DBMS
              'description' => 'text',
              'champion_id' => 'integer DEFAULT NULL', //Need to check for other DBMS
              'status' => 'varchar(3) DEFAULT NULL', //Need to check for other DBMS
              'progress' => 'smallint(5) unsigned DEFAULT NULL', //Need to check for other DBMS
              'par_project_id' => 'integer DEFAULT NULL', //Need to check for other DBMS
              'start_date' => 'date DEFAULT NULL',
              'end_date' => 'date DEFAULT NULL',
              'eff_start_date' => 'date DEFAULT NULL',
              'eff_end_date' => 'date DEFAULT NULL',
              'client' => 'tinytext', //Need to check for other DBMS
              'chargeable_flg' => 'boolean NOT NULL DEFAULT 0',
              //'UNIQUE KEY name (name)',
//              'KEY par_project (par_project_id)',
//              'KEY champion (champion_id)',
//              'KEY created_by (created_by)',
//              'KEY status (status)',
          ),
          'ENGINE=InnoDB'
      ),
      '{{session}}' => array(
          array('id' => 'char(32) NOT NULL', //Need to check for other DBMS
              'user_id' => 'integer unsigned NOT NULL', //Need to check for other DBMS
              'expire' => 'integer DEFAULT NULL', //Need to check for other DBMS
              'data' => 'longblob', //Need to check for other DBMS
              'PRIMARY KEY (id)',
//              'KEY user_id (user_id)',
          ),
          'ENGINE=InnoDB'
      ),
      '{{task}}' => array(
          array('id' => 'pk',
              'created' => 'datetime NOT NULL',
              'created_by' => 'integer NOT NULL',
              'last_upd' => 'timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP', //check
              'last_upd_by' => 'integer',
              'par_project_id' => 'integer NOT NULL',
              'parent_id' => 'integer',
              'title' => 'string NOT NULL',
              'description' => 'text',
              'status' => 'integer DEFAULT NULL',
              'progress' => 'smallint(5) unsigned DEFAULT NULL', //Need to check for other DBMS
              'start_date' => 'date DEFAULT NULL',
              'end_date' => 'date DEFAULT NULL',
              'eff_start_date' => 'date DEFAULT NULL',
              'eff_end_date' => 'date DEFAULT NULL',
              'priority' => 'smallint(6) DEFAULT NULL', //Need to check for other DBMS
              'type' => 'smallint(6) DEFAULT NULL', //Need to check for other DBMS
              'private_flg' => 'boolean NOT NULL DEFAULT 0', //Need to check for other DBMS
              'chargeable_flg' => 'boolean NOT NULL',
//              'KEY task_ibfk_3 (parent_id)',
//              'KEY task_ibfk_2 (created_by)',
//              'KEY task_ibfk_1 (par_project_id)',
//              'KEY title (title, par_project_id)',
//              'KEY status (status)',
//              'KEY type (type)',
          ),
          'ENGINE=InnoDB'
      ),
      '{{task_status}}' => array(
          array('id' => 'pk',
              'name' => 'varchar(30) NOT NULL', //Need to check for other DBMS
              'group_id' => 'integer unsigned NOT NULL', //Need to check for other DBMS
              'order_by' => 'integer unsigned NOT NULL', //Need to check for other DBMS
              'default_flg' => 'boolean', //Need to check for other DBMS
              'active_flg' => 'boolean NOT NULL DEFAULT 1', //Need to check for other DBMS
          ),
          'ENGINE=InnoDB'
      ),
      '{{task_type}}' => array(
          array('id' => 'pk',
              'name' => 'varchar(30) NOT NULL', //Need to check for other DBMS
              'order_by' => 'integer unsigned NOT NULL', //Need to check for other DBMS
              'default_flg' => 'boolean', //Need to check for other DBMS
              'active_flg' => 'boolean NOT NULL DEFAULT 1', //Need to check for other DBMS
          ),
          'ENGINE=InnoDB'
      ),
      '{{user}}' => array(
          array('id' => 'pk',
              'created' => 'datetime NOT NULL',
              'created_by' => 'integer NOT NULL DEFAULT 1',
              'last_upd' => 'datetime NOT NULL',
              'last_upd_by' => 'integer NOT NULL DEFAULT 1',
              'username' => 'string NOT NULL',
              'password' => 'varchar(64) NOT NULL', //Need to check for other DBMS
              'email' => 'string NOT NULL',
              'active' => 'boolean NOT NULL DEFAULT 1', //Need to check for other DBMS
              'name' => 'varchar(63) DEFAULT NULL', //Need to check for other DBMS
              'surname' => 'varchar(63) DEFAULT NULL', //Need to check for other DBMS
              'gender' => 'char(1) NOT NULL',
              'level' => 'varchar(63) DEFAULT NULL', //Need to check for other DBMS
              'phone' => 'varchar(63) DEFAULT NULL', //Need to check for other DBMS
              'mobile' => 'varchar(63) DEFAULT NULL', //Need to check for other DBMS
              'load_cost' => 'float(7,3) DEFAULT NULL', //Need to check for other DBMS
              'bill_code' => 'smallint(6) DEFAULT NULL', //Need to check for other DBMS
              'seat_charge' => 'float(7,3) DEFAULT NULL', //Need to check for other DBMS
              'daily_hours' => 'float(3,1) DEFAULT NULL', //Need to check for other DBMS
              'profile_id' => 'integer',
              'note' => 'text',
              'confirm_key' => 'varchar(40)', //Need to check for other DBMS
              'avatar' => 'text',
              'page_size' => 'tinyint(4) DEFAULT NULL', //Need to check for other DBMS
              'notifications' => 'text',
              'UNIQUE (username)',
//              'KEY profile (profile_id)',
          ),
          'ENGINE=InnoDB'
      ),
      '{{user_project}}' => array(
          array('id' => 'pk',
              'created' => 'datetime NOT NULL',
              'last_upd' => 'timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP', //check
              'user_id' => 'integer NOT NULL',
              'project_id' => 'integer NOT NULL',
              'rollon_date' => 'date DEFAULT NULL', //Need to check for other DBMS
              'rolloff_date' => 'date DEFAULT NULL', //Need to check for other DBMS
              'UNIQUE (user_id,project_id)',
//              'KEY user_project_ibfk_2 (project_id)',
          ),
          'ENGINE=InnoDB'
      ),
      '{{user_task}}' => array(
          array('id' => 'pk',
              'created' => 'datetime NOT NULL',
              'last_upd' => 'timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP', //check
              'user_id' => 'integer NOT NULL',
              'task_id' => 'integer NOT NULL',
              'UNIQUE (user_id,task_id)',
              //'KEY user_task_ibfk_2 (task_id)',
          ),
          'ENGINE=InnoDB'
      ),
  );
  private $indexes = array(
  	  array('user_name', '{{android_udid}}', 'user_name', false),
  	  array('task_id', '{{attachment}}', 'task_id', false),
  	  array('project_id', '{{attachment}}', 'project_id', false),
  	  array('created_by', '{{charge}}', 'created_by', false),
  	  array('last_upd_by', '{{charge}}', 'last_upd_by', false),
  	  array('user_id', '{{charge}}', 'user_id', false),
  	  array('project_id_charge', '{{charge}}', 'project_id', false),
  	  array('task_id_charge', '{{charge}}', 'task_id', false),
  	  array('par_project', '{{project}}', 'par_project_id', false),
  	  array('champion', '{{project}}', 'champion_id', false),
  	  array('created_by_project', '{{project}}', 'created_by', false),
  	  array('status', '{{project}}', 'status', false),
  	  array('user_id_session', '{{session}}', 'user_id', false),
  	  array('task_ibfk_3', '{{task}}', 'parent_id', false),
  	  array('task_ibfk_2', '{{task}}', 'created_by', false),
  	  array('task_ibfk_1', '{{task}}', 'par_project_id', false),
  	  array('title', '{{task}}', 'title, par_project_id', false),
  	  array('status_task', '{{task}}', 'status', false),
  	  array('type', '{{task}}', 'type', false),
  	  array('profile', '{{user}}', 'profile_id', false),
  	  array('user_project_ibfk_2', '{{user_project}}', 'project_id', false),
	  array('user_task_ibfk_2', '{{user_task}}', 'task_id', false),
  );
  private $fks = array(
      array('auth_assignment_ibfk_1', '{{auth_assignment}}', 'itemname', '{{auth_item}}', 'name', 'CASCADE', 'CASCADE'),
      array('auth_item_child_ibfk_1', '{{auth_item_child}}', 'parent', '{{auth_item}}', 'name', 'CASCADE', 'CASCADE'),
      array('auth_item_child_ibfk_2', '{{auth_item_child}}', 'child', '{{auth_item}}', 'name', 'CASCADE', 'CASCADE'),
      array('attachment_ibfk_1', '{{attachment}}', 'task_id', '{{task}}', 'id', 'CASCADE', 'CASCADE'),
      array('attachment_ibfk_2', '{{attachment}}', 'project_id', '{{project}}', 'id', 'CASCADE', 'CASCADE'),
      array('project_ibfk_3', '{{project}}', 'created_by', '{{user}}', 'id', NULL, NULL),
      array('project_ibfk_4', '{{project}}', 'champion_id', '{{user}}', 'id', NULL, NULL),
      array('project_ibfk_5', '{{project}}', 'par_project_id', '{{project}}', 'id', NULL, NULL),
      array('task_ibfk_1', '{{task}}', 'par_project_id', '{{project}}', 'id', 'CASCADE', NULL),
      array('task_ibfk_2', '{{task}}', 'created_by', '{{user}}', 'id', NULL, NULL),
      array('task_ibfk_3', '{{task}}', 'parent_id', '{{task}}', 'id', NULL, NULL),
      array('user_project_ibfk_2', '{{user_project}}', 'project_id', '{{project}}', 'id', 'CASCADE', NULL),
      array('user_project_ibfk_3', '{{user_project}}', 'user_id', '{{project}}', 'id', 'CASCADE', NULL),
      array('user_task_ibfk_1', '{{user_task}}', 'user_id', '{{user}}', 'id', 'CASCADE', NULL),
      array('user_task_ibfk_2', '{{user_task}}', 'task_id', '{{task}}', 'id', 'CASCADE', NULL),
  );
  private $_operations = array(//Charges 
      'adminAllCharge' => 'Charge Admin All',
      'adminCharge' => 'Charge Admin',
      'createCharge' => 'Charge Create',
      //Application & Roles
      'adminConfig' => 'Application Configuration Management',
      'adminRole' => 'Roles Configuration Management',
      //Project
      'createProject' => 'Project Create',
      'deleteProject' => 'Project Delete',
      'indexAllProject' => 'Project View All',
      'updateProject' => 'Project Update',
      //Task
      'createTask' => 'Task Create',
      'deleteTask' => 'Task Delete',
      'indexAllTask' => 'Task View All',
      'updateTask' => 'Task Update',
      //User
      'createUser' => 'User Create',
      'deleteUser' => 'User Delete',
      'indexAllUser' => 'User View All',
      'updateUser' => 'User Update',
  );
  private $_roles = array(
      'Application Manager' => array('adminConfig', 'adminAllCharge'),
      'Developer' => array('createCharge', 'createTask', 'deleteTask', 'updateTask'),
      'Project Manager' => array('adminCharge', 'createCharge', 'createProject',
          'deleteProject', 'indexAllProject', 'updateProject'),
      'Role Manager' => array('adminRole'),
      'Task Manager' => array('createTask', 'deleteTask', 'indexAllTask', 'updateTask'),
      'User Manager' => array('createUser', 'deleteUser', 'updateUser')
  );
  private $_taskStatuses = array(
      array(
          'name' => 'Open',
          'group_id' => 0,
          'order_by' => 0,
          'default_flg' => true,
      ),
      array(
          'name' => 'Working On',
          'group_id' => 0,
          'order_by' => 1,
      ),
      array(
          'name' => 'Suspended',
          'group_id' => 2,
          'order_by' => 2,
      ),
      array(
          'name' => 'Closed',
          'group_id' => 1,
          'order_by' => 3,
      ),
      array(
          'name' => 'Cancelled',
          'group_id' => 1,
          'order_by' => 4,
      ),
  );
  private $_taskTypes = array(
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

  protected function beforeAction($action) {
    Yii::app()->params['name'] = Yii::app()->name;

    $this->configDir = Yii::app()->basePath . '/config/';

    if (file_exists($this->configDir . 'main.php')) {
      //2022/07/07 modified
      //print 'MyT is already installed.';
      print 'MyTは既にインストールされています。';
      Yii::app()->end();
    }


    return parent::beforeAction($action);
  }

  public function actionIndex() {

    $attachDir = Yii::getPathOfAlias('webroot') . '/attachments/';
    $userImageDir = Yii::getPathOfAlias('webroot') . '/'. $this->userImgPath;
    $runtimeDir = Yii::app()->basePath . '/runtime/';
    $assetsDir = Yii::getPathOfAlias('webroot') . '/assets/';

    /**
     * @var array List of requirements (name, required or not, result check, message if missing)
     */
    $requirements = array(
        array('PHP Version', true, version_compare(PHP_VERSION, "5.3.0", ">="), 'PHP 5.3.0 or higher is required.'), //Php Version Check
        array('$_SERVER var check', true, '' === $message = $this->_checkServerVar(), $message), //$_SERVER check
        array('PHP Extension: Reflection', true, class_exists('Reflection', false), 'PHP Extension \'Reflection\' is required.'), //Reflection Check
        array('PHP Extension: PCRE', true, extension_loaded("pcre"), 'PHP Extension \'PCRE\' is required.'), //PCRE Check
        array('PHP Extension: SPL', true, extension_loaded("SPL"), 'PHP Extension \'SPL\' is required.'), //SPL Check
        array('PHP Extension: PDO', true, extension_loaded("pdo"), 'PHP Extension \'PDO\' is required.'), //PDO Check
        array('PHP Extension: PDO MySQL OR PDO SQLite', true, extension_loaded("pdo_mysql") || extension_loaded("pdo_sqlite"),
              'PHP Extension \'PDO MySQL\' OR \'PDO SQLite\' is required.'), //PDO MySQL Check
        //File Permission Check
        array('Config directory Writable', true, is_writable($this->configDir), 'Please set write permission to ' . $this->configDir), //Config Dir Check
        array('Attachments directory Writable', true, is_writable($attachDir), 'Please set write permission to ' . $attachDir), //Attach Dir Check
        array('User Images directory Writable', true, is_writable($userImageDir), 'Please set write permission to ' . $userImageDir), //Avatar Dir Check
        array('Runtime directory Writable', true, is_writable($runtimeDir), 'Please set write permission to ' . $runtimeDir), //Runtime Dir Check
        array('Asset directory Writable', true, is_writable($assetsDir), 'Please set write permission to ' . $assetsDir), //Asset Dir Check
        //Optional Check
        //	array( 'PHP Extension: DOM', false, class_exists("DOMDocument",false), 'PHP Extension \'DOM\' is required.' ), //DOM Check
        //	array( 'PHP Extension: mcrypt', false, extension_loaded("mcrypt"), 'PHP Extension \'mcrypt\' is required.' ), //mcrypt Check
        //	array( 'PHP Extension: SOAP', false, extension_loaded("soap"), 'PHP Extension \'SOAP\' is required.' ), //SOAP Check
        array('GD extension with FreeType support or ImageMagick extension with PNG support', false,
            '' === $message = $this->_checkCaptchaSupport(), $message), //GD or ImageMagick Check
        array('Apache Mod Rewrite', false, $this->_isRewriteEnabled(), 'Apache mod_rewrite isn\'t enabled on your server or PHP is running in CGI mode'), //Asset Dir Check
    );

    $error = 0;

    foreach ($requirements as $i => $requirement) {
      if ($requirement[1] && !$requirement[2])
        $error = 1; //Check Failed
    }

    if (!$error)
      Yii::app()->user->setState("InstallCheckPassed", "1");

    $this->render('index', array(
        'requirements' => $requirements,
        'error' => $error
    ));
  }

  public function actionConfigureDatabase() {

    if (Yii::app()->user->getState("InstallCheckPassed") == "1") {

      $supportedDriver = array();

      if( extension_loaded("pdo_mysql") )
        $supportedDriver['mysql'] = 'MySQL';
      if( extension_loaded("pdo_sqlite") )
        $supportedDriver['sqlite'] = 'SQLite';     

      $model = new InstallForm('dbConfig');

      if (isset($_POST['InstallForm'])) {
        $model->attributes = $_POST['InstallForm'];

        if ($model->validate()) {
          foreach ($model->attributes as $k => $v) {
            Yii::app()->user->setState($k, $v);
          }

          $this->redirect(array('install/createTables'));
        }
      }



      $this->render('configuredatabase', array('model' => $model, 'dbTypes' => $supportedDriver));
    }
  }

  public function actionCreateTables() {
    $dbType = Yii::app()->user->getState('dbType');
    $dbHost = Yii::app()->user->getState('dbHost');
    $dbName = Yii::app()->user->getState('dbName');
    $dbUsername = Yii::app()->user->getState('dbUsername');
    $dbPassword = Yii::app()->user->getState('dbPassword');
    $dbTablePrefix = Yii::app()->user->getState('dbTablePrefix');

    if (empty($dbType) || empty($dbHost)) {
      $this->redirect(array('install/ConfigureDatabase'));
    }

    $message = array();

    $dsn = $this->_getDSN($dbType, $dbHost, $dbName);

    $connection = new CDbConnection($dsn, $dbUsername, $dbPassword);

    try {
      $connection->active = true;
      $connection->tablePrefix = $dbTablePrefix;

      //Table Creation
      foreach ($this->tables as $tableName => $table) {

        $tableColumns = $table[0];

      	if( $connection->getDriverName() == 'sqlite' )
        {
          $table[1] = null; //sqlite
        	//sqlite fix
        	$search = array('ON UPDATE CURRENT_TIMESTAMP' => '',
        					'DEFAULT CURRENT_TIMESTAMP' => '',
        					'integer unsigned' => 'unsigned integer',
        					'smallint(5) unsigned' => 'unsigned smallint(5)');

        	foreach($table[0] as $key => $val)
        	{
        		$tableColumns[$key] = str_replace(array_keys($search), $search, $val);
        	}
        }

        $connection->createCommand()->createTable($tableName, $tableColumns, $table[1]);


        $realTableName = preg_replace('/{{(.*?)}}/', $dbTablePrefix . '\1', $tableName); //used only for message

        $message[] = "Table {$realTableName} created ...";
      }

      //Index(es) creation
      foreach ($this->indexes as $index) {
        list( $name, $table, $columns, $unique ) = $index;

        $name = $dbTablePrefix . $name;

        $connection->createCommand()->createIndex($name, $table, $columns, $unique);

        $realTableName = preg_replace('/{{(.*?)}}/', $dbTablePrefix . '\1', $table); //used only for message

        $message[] = "Index {$name} for table '{$realTableName}' created  ...";
      }

      //FK(s) creation
      /*
      foreach ($this->fks as $fk) {
        list( $name, $table, $columns, $refTable, $refColumns, $delete, $update ) = $fk;

        $name = $dbTablePrefix . $name;

        $connection->createCommand()->addForeignKey($name, $table, $columns, $refTable, $refColumns, $delete, $update);

        $realTableName = preg_replace('/{{(.*?)}}/', $dbTablePrefix . '\1', $table); //used only for message
        $message[] = "Altered Table {$realTableName} Foreign Key '{$name}' created  ...";
      }
      */

      $connection->active = false;

      Yii::app()->user->setState("DatabaseCreated", "1");
    } catch (CDbException $e) {
      Yii::app()->user->setFlash('install-error', $e->getMessage());
      $message[] = $e->getMessage();
    }

    $this->render('createtables', array('messages' => $message));
  }

  public function actionConfigureApp() {
    if (Yii::app()->user->getState("DatabaseCreated") == "1") {
      $model = new InstallForm('appConfig');

      $errors = array();

      if (isset($_POST['InstallForm'])) {
        $model->attributes = $_POST['InstallForm'];

        if ($model->validate()) {
          $this->_initDbComponent();

          $this->_insertTaskStatuses();
          $this->_insertTaskTypes();

          $auth = $this->_installAuthManager();

          $user = new User('install');
          $user->username = $_POST['InstallForm']['appUsername'];
          $user->password = $_POST['InstallForm']['appPassword'];
          $user->password_confirm = $_POST['InstallForm']['appPassword']; //Already Checked it!
          $user->email = $_POST['InstallForm']['appEmail'];
          $user->gender = 'M';
          $user->name = 'Admin';
          $user->surname = 'Admin';

          if ($user->save()) {
            //Admin has all roles
            foreach ($this->_roles as $role => $operations) {
              $auth->assign($role, $user->id);
            }

            $auth->save();

            Yii::app()->user->id = $user->id;

            $this->_insertChargeProjects();

            $user->associateUserToChargeProjects();

            Yii::app()->cache->flush();

            Yii::app()->user->setState('appName', $_POST['InstallForm']['appName']);
            Yii::app()->user->setState('appLanguage', $_POST['InstallForm']['appLanguage']);
            Yii::app()->user->setState('adminEmail', $_POST['InstallForm']['appEmail']);

            $this->redirect(array('install/finish'));
          }
        }
      }



      $this->render('configureapp', array('model' => $model));
    }
  }

  function actionFinish() {
    $appName = Yii::app()->user->getState('appName');
    $appLanguage = Yii::app()->user->getState('appLanguage');
    $dbType = Yii::app()->user->getState('dbType');
    $dbHost = Yii::app()->user->getState('dbHost');
    $dbName = Yii::app()->user->getState('dbName');

    if (empty($appName)) {
      $this->redirect(array('install/ConfigureApp'));
    }
    if (empty($appLanguage)) {
      $appLanguage = Yii::app()->sourceLanguage;
    }

    $errors = array();

    $dsn = $this->_getDSN($dbType, $dbHost, $dbName);

    $configFile = @file_get_contents($this->configDir . 'main_sample.php');

    if (!empty($configFile)) {
      $vars = array('<APP_NAME>' => $appName,
          '<APP_LANGUAGE>' => $appLanguage,
          '<DSN>' => $dsn,
          '<DATABASE_USER>' => Yii::app()->user->getState('dbUsername'),
          '<DATABASE_PASS>' => Yii::app()->user->getState('dbPassword'),
          '<DATABASE_PREFIX>' => Yii::app()->user->getState('dbTablePrefix'),
      );

      if ($this->_isRewriteEnabled()) {
        $vars = array_merge($vars, array('/* -- REWRITE URL --' => '',
            '-- REWRITE URL -- */' => ''));
      }

      $configFile = str_replace(array_keys($vars), array_values($vars), $configFile);

      if (@file_put_contents($this->configDir . 'main.php', $configFile)) {
        Yii::app()->user->setState('dbHost', null);
        Yii::app()->user->setState('dbName', null);
        Yii::app()->user->setState('dbUsername', null);
        Yii::app()->user->setState('dbPassword', null);
        Yii::app()->user->setState('dbTablePrefix', null);
      } else {
        $errors[] = 'Cannot write file' . $this->configDir . 'main.php';
      }

      //Params File Handling
	  if( !file_exists( $this->configDir . 'params.inc' ) )
		rename( $this->configDir . 'params.inc.example', $this->configDir . 'params.inc' );
		
      $paramsFile = @file_get_contents($this->configDir . 'params.inc');

      if (!empty($paramsFile)) {
        $params = unserialize(base64_decode($paramsFile));
        $params['name'] = $appName;
        $params['language'] = $appLanguage;
        $params['theme'] = 'fluid';
        $params['avatarPath'] = $this->userImgPath;
        $params['pageSize'] = 10;
        $params['notifications'] = $this->notifications;
        $params['adminEmail'] = Yii::app()->user->getState('adminEmail');
		    $params['mytVersion'] = $this->version;
        $params['attachments']['path'] = 'attachments';
        $params['attachments']['maxSize'] = '2048';
        $params['attachments']['extList'] = 'docx, doc, pdf, txt, jpg, png, xlsx, xls, xml, zip';

        $paramsFile = base64_encode(serialize($params));

        if (!@file_put_contents($this->configDir . 'params.inc', $paramsFile)) {
          $errors[] = 'Cannot write file' . $this->configDir . 'params.inc';
        }
      } else {
        $errors[] = 'Cannot read file' . $this->configDir . 'params.inc';
      }
    } else {
      $errors[] = 'Cannot read file' . $this->configDir . 'main_sample.php';
    }

    $this->render('finish', array('errors' => $errors,
        'appName' => $appName));
  }

  private function _checkServerVar() {
    $vars = array('HTTP_HOST', 'SERVER_NAME', 'SERVER_PORT', 'SCRIPT_NAME', 'SCRIPT_FILENAME', 'PHP_SELF', 'HTTP_ACCEPT', 'HTTP_USER_AGENT');
    $missing = array();
    foreach ($vars as $var) {
      if (!isset($_SERVER[$var]))
        $missing[] = $var;
    }
    if (!empty($missing))
      return '$_SERVER does not have ' . implode(', ', $missing);

    if (!isset($_SERVER["REQUEST_URI"]) && isset($_SERVER["QUERY_STRING"]))
      return 'Either $_SERVER["REQUEST_URI"] or $_SERVER["QUERY_STRING"] must exist.';

    if (!isset($_SERVER["PATH_INFO"]) && strpos($_SERVER["PHP_SELF"], $_SERVER["SCRIPT_NAME"]) !== 0)
      return 'Unable to determine URL path info. Please make sure $_SERVER["PATH_INFO"] (or $_SERVER["PHP_SELF"] and $_SERVER["SCRIPT_NAME"]) contains proper value.';

    return '';
  }

  private function _checkCaptchaSupport() {
    if (extension_loaded('imagick')) {
      $imagick = new Imagick();
      $imagickFormats = $imagick->queryFormats('PNG');
    }
    if (extension_loaded('gd'))
      $gdInfo = gd_info();
    if (isset($imagickFormats) && in_array('PNG', $imagickFormats))
      return '';
    elseif (isset($gdInfo)) {
      if ($gdInfo['FreeType Support'])
        return '';
      return 'GD installed, but FreeType support not installed';
    }
    return 'GD or ImageMagick not installed';
  }

  private function _initDbComponent() {
    $dbType = Yii::app()->user->getState('dbType');
    $dbHost = Yii::app()->user->getState('dbHost');
    $dbName = Yii::app()->user->getState('dbName');
    $dbUsername = Yii::app()->user->getState('dbUsername');
    $dbPassword = Yii::app()->user->getState('dbPassword');
    $dbTablePrefix = Yii::app()->user->getState('dbTablePrefix');

    $dsn = $this->_getDSN($dbType, $dbHost, $dbName);

    $db = Yii::createComponent(array(
                'class' => 'CDbConnection',
                'connectionString' => $dsn,
                'emulatePrepare' => true,
                'username' => $dbUsername,
                'password' => $dbPassword,
                'tablePrefix' => $dbTablePrefix,
                'charset' => 'utf8',
                'enableProfiling' => true,
                'enableParamLogging' => true
    ));

    Yii::app()->setComponent('db', $db);
  }

  private function _insertTaskStatuses() {
    foreach ($this->_taskStatuses as $status) {
      $taskStatus = new TaskStatus;
      $taskStatus->attributes = $status;
      $taskStatus->save();
    }
  }

  private function _insertTaskTypes() {
    foreach ($this->_taskTypes as $type) {
      $taskType = new TaskType;
      $taskType->attributes = $type;
      $taskType->save(false);
    }
  }

  private function _insertChargeProjects() {
    foreach (Project::model()->getChargeProjectsList() as $k => $chargeProject) {
      $project = new Project;
      $project->name = $chargeProject;
      $project->status = array_search('Charge Item', $project->getStatusList());
      $project->chargeable_flg = 1;
	  $project->prefix = str_pad($k, 3, '-', STR_PAD_LEFT);
      $project->save();
    }
  }

  private function _installAuthManager() {
    $authManager = Yii::createComponent(array(
                'class' => 'CDbAuthManager',
                'connectionID' => 'db',
                'itemTable' => '{{auth_item}}',
                'itemChildTable' => '{{auth_item_child}}',
                'assignmentTable' => '{{auth_assignment}}',
    ));

    Yii::app()->setComponent('authManager', $authManager);


    $auth = Yii::app()->authManager;

    foreach ($this->_operations as $operation => $description) {
      $auth->createOperation($operation, $description);
    }

    foreach ($this->_roles as $role => $operations) {
      $role = $auth->createRole($role);

      foreach ($operations as $operation) {
        $role->addChild($operation);
      }
    }

    return $auth;
  }

  private function _isRewriteEnabled() {
    //Need to check a solution for CGI
    return function_exists('apache_get_modules') && in_array('mod_rewrite', apache_get_modules());
  }

  private function _getDSN( $dbType, $dbHost, $dbName )
  {
    switch( $dbType )
    {
      case 'sqlite':
        $dsn = 'sqlite:' . $dbHost;
      break;

      case 'mysql':
        $dsn = "mysql:host={$dbHost};dbname={$dbName}";
      break;
    }

    return $dsn;
  }

}
