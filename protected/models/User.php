<?php

/**
 * This is the model class for table "{{user}}".
 *
 * The followings are the available columns in table '{{user}}':
 * @property integer $id
 * @property string $created
 * @property integer $created_by
 * @property string $last_upd
 * @property integer $last_upd_by
 * @property string $username
 * @property string $password
 * @property string $email
 * @property boolean $active
 * @property string $name
 * @property string $surname
 * @property string $gender
 * @property string $level
 * @property string $phone
 * @property float $load_cost
 * @property float $daily_hours
 * @property string $mobile
 * @property string $avatar
 * @property integer $page_size
 * @property array $notifications
 * @property Project[] $projects
 * @property Task[] $tasks
 * @property AuthAssignment[] $roles
 */
class User extends CActiveRecord {

  public $password_confirm;
  public static $isLogin = false;
  public $calc_name;
  public $project;
  public $role;
  public $notifications;
  private $_oldAvatar;

  /**
   * Returns the static model of the specified AR class.
   * @param string $className active record class name.
   * @return User the static model class
   */
  public static function model($className = __CLASS__, $isLogin = false) {
    if (!$className)
      $className = __CLASS__;

    self::$isLogin = $isLogin;

    return parent::model($className);
  }

  /**
   * @return string the associated database table name
   */
  public function tableName() {
    return '{{user}}';
  }

  public function behaviors() {
    return array(
        'timestamp' => array(
            'class' => 'application.behaviors.ETimestampBehavior',
        )
    );
  }

  /**
   * @return array validation rules for model attributes.
   */
  public function rules() {
    // NOTE: you should only define rules for those attributes that
    // will receive user inputs.

    // 2024/9/11 add
    $maxSize = isset(Yii::app()->params['imageDimension']['maxSize']) ? Yii::app()->           params['imageDimension']['maxSize'] : 2048; // デフォルト値を2048に設定

    return array(
        array('username, gender', 'required', 'except' => 'resetpass'),
        array('email', 'required'),
        array('username, email', 'length', 'max' => 255),
        array('username', 'unique'),
        array('password', 'length', 'max' => 64),
        array('password, password_confirm', 'required', 'on' => 'insert,confirmkey'),
        array('password_confirm', 'compare', 'compareAttribute' => 'password'),
        array('name, surname, level, phone, mobile', 'length', 'max' => 63),
        array('load_cost', 'numerical', 'min' => 0),
        array('daily_hours', 'default', 'value' => 8),
        array('daily_hours', 'numerical', 'min' => 0, 'max' => 24),
        array('page_size', 'numerical', 'integerOnly' => true, 'max' => 255),
        array('avatar', 'customAvatarValidator', 'types' => 'jpg,jpeg,gif,png', 'allowEmpty' => true, 'maxSize' =>  $maxSize * 1024),
        array('active', 'default', 'value' => true),
        // The following rule is used by search().
        // Please remove those attributes that should not be searched.
        array('id, username, email, calc_name, project, role, active', 'safe', 'on' => 'search'),
    );
  }

  /**
   * @return array relational rules.
   */
  public function relations() {
    // NOTE: you may need to adjust the relation name and the related
    // class name for the relations automatically generated below.
    return array(
        'projects' => array(self::MANY_MANY, 'Project', '{{user_project}}(user_id, project_id)'),
        'projectAssignments' => array(self::HAS_MANY, 'UserProject', 'user_id'),
        'tasks' => array(self::MANY_MANY, 'Task', '{{user_task}}(user_id, task_id)'),
        'roles' => array(self::HAS_MANY, 'AuthAssignment', 'userid'),
    );
  }

  /**
   * @return array customized attribute labels (name=>label)
   */
  public function attributeLabels() {
    return array(
        'id' => Yii::t('attributes', 'User.id'),
        'created' => Yii::t('attributes', 'User.created'),
        'created_by' => Yii::t('attributes', 'User.created_by'),
        'last_upd' => Yii::t('attributes', 'User.last_upd'),
        'last_upd_by' => Yii::t('attributes', 'User.last_upd_by'),
        'active' => Yii::t('attributes', 'User.active'),
        'name' => Yii::t('attributes', 'User.name'),
        'surname' => Yii::t('attributes', 'User.surname'),
        'gender' => Yii::t('attributes', 'User.gender'),
        'level' => Yii::t('attributes', 'User.level'),
        'phone' => Yii::t('attributes', 'User.phone'),
        'daily_hours' => Yii::t('attributes', 'User.daily_hours'),
        'mobile' => Yii::t('attributes', 'User.mobile'),
        'avatar' => Yii::t('attributes', 'User.avatar'),
        'page_size' => Yii::t('attributes', 'User.page_size'),
        'username' => Yii::t('attributes', 'User.username'),
        'password' => Yii::t('attributes', 'User.password'),
        'password_confirm' => Yii::t('attributes', 'User.password_confirm'),
        'email' => Yii::t('attributes', 'User.email'),
        'calc_name' => Yii::t('attributes', 'User.calc_name'),
        'project' => Yii::t('attributes', 'User.project'),
        'role' => Yii::t('attributes', 'User.role'),
        'load_cost' => Yii::t('attributes', 'User.load_cost'),
        'notifications[taskAssociation]' => Yii::t('attributes', 'User.notifications.taskAssociation'),
        'notifications[taskAssociation][android]' => Yii::t('attributes', 'User.notifications.taskAssociation.android'),
        'notifications[taskAssociation][email]' => Yii::t('attributes', 'User.notifications.taskAssociation.email'),
        'notifications[projectAssociation]' => Yii::t('attributes', 'User.notifications.projectAssociation'),
        'notifications[projectAssociation][android]' => Yii::t('attributes', 'User.notifications.projectAssociation.android'),
        'notifications[projectAssociation][email]' => Yii::t('attributes', 'User.notifications.projectAssociation.email'),
    );
  }
  
  public function customAvatarValidator($attribute, $params)
  {
	 if( $this->_oldAvatar != $this->$attribute )
	 {
		 $validator = CValidator::createValidator('file', $this, $attribute, $params);
		 $validator->validate($this, $attribute);
	 }
  }

  public function getNotification($macro, $type) {
    return isset($this->notifications[$macro]) && isset($this->notifications[$macro][$type]) && CPropertyValue::ensureBoolean($this->notifications[$macro][$type]);
  }

  public function afterFind() {
    parent::afterFind();

    if (!self::$isLogin)
      $this->password = ''; //PP after find remove password for security reasons

    $firstname = $this->name ? $this->name : '?';
    $surname = $this->surname ? $this->surname : '?';
    $this->calc_name = $surname === '?' && $firstname === '?' ? null : $surname . ', ' . $firstname;

    //2025/08/03 modified
    //$this->notifications = unserialize($this->notifications);
    if (!empty($this->notifications)) {
        $this->notifications = unserialize($this->notifications);
    } else {
        $this->notifications = []; // または null、用途に応じて
    }
	
	$this->_oldAvatar = $this->avatar;
  }

  public function beforeSave() {
    if (!empty($this->password)) {
      $this->password = CPasswordHelper::hashPassword($this->password);
    } else {
      unset($this->password);
    }
    $this->notifications = serialize($this->notifications);

    return parent::beforeSave();
  }

  public function afterSave() {

    parent::afterSave();

    if ($this->scenario == 'insert') {
      $this->associateUserToChargeProjects();
    }
  }

  public function scopes() {
    return array(
        'active' => array(
            'condition' => 'active = 1',
        ),
    );
  }

  public function enrolled($projectId = 0) {
    $this->getDbCriteria()->mergeWith(array(
        'together' => true,
        'with' => array(
            'projectAssignments' => array(
                'condition' => 'project_id = :project_id',
                'params' => array(':project_id' => $projectId),
                'scopes' => 'active',
            )
        )
    ));
    return $this;
  }

  /**
   * Retrieves a list of models based on the current search/filter conditions.
   * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
   */
  public function search() {
    // Warning: Please modify the following code to remove attributes that
    // should not be searched.

    $criteria = new CDbCriteria;

    $criteria->compare('t.id', $this->id);
    $criteria->compare('t.username', $this->username, true);
    $criteria->compare('t.email', $this->email, true);
    $criteria->compare('t.active', $this->active);

    $criteria->compare('t.name', $this->calc_name, true);
    $criteria->compare('t.surname', $this->calc_name, true, 'OR');

    if (!empty($this->role)) {
      $criteria->together = true;
      $roleWith = array('roles' => array('select' => false));
      $criteria->with = is_array($criteria->with) ? CMap::mergeArray($criteria->with, $roleWith) : $roleWith;
      $criteria->compare('roles.itemname', $this->role, true);
    }

    if (!empty($this->project)) {
      $criteria->together = true;
      $projectWith = array('projects' => array('select' => 'name', 'alias' => 'p'));
      $criteria->with = is_array($criteria->with) ? CMap::mergeArray($criteria->with, $projectWith) : $projectWith;
      $criteria->compare('p.name', $this->project, true);
    }

    return new CActiveDataProvider($this, array(
        'pagination' => array('pageSize' => Yii::app()->user->pageSize),
        'criteria' => $criteria,
    ));
  }

  public function associateUserToChargeProjects() {
    foreach (Project::model()->chargeOnly()->findAll(array('select' => 'id')) as $chargeProject) {
      $userProject = new UserProject;
      $userProject->project_id = $chargeProject->id;
      $userProject->user_id = $this->id;
      $userProject->rollon_date = date('Y-m-d');
      $userProject->save();
    }
  }

}
