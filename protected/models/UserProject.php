<?php

/**
 * This is the model class for table "{{user_project}}".
 *
 * The followings are the available columns in table '{{user_project}}':
 * @property integer $id
 * @property string $created
 * @property string $last_upd
 * @property integer $user_id
 * @property integer $project_id
 * @property string $rollon_date
 * @property string $rolloff_date
 *
 * The followings are the available model relations:
 * @property User $user
 * @property Project $project
 */
class UserProject extends CActiveRecord {

  /**
   * Returns the static model of the specified AR class.
   * @param string $className active record class name.
   * @return UserProject the static model class
   */
  public static function model($className = __CLASS__) {
    return parent::model($className);
  }

  /**
   * @return string the associated database table name
   */
  public function tableName() {
    return '{{user_project}}';
  }
  
  public function behaviors() {
    return array(
        'timestamp' => array(
            'class' => 'application.behaviors.ETimestampBehavior',
			'createUserAttribute' => null,
			'updateUserAttribute' => null
        )
    );
  }

  /**
   * @return array validation rules for model attributes.
   */
  public function rules() {
    // NOTE: you should only define rules for those attributes that
    // will receive user inputs.
    return array(
        array('user_id, project_id', 'required'),
        array('user_id, project_id', 'numerical', 'integerOnly' => true),
        array('rollon_date, rolloff_date', 'safe'),
        array('rollon_date, rolloff_date', 'date', 'format' => 'yyyy-MM-dd'),
        // The following rule is used by search().
        // Please remove those attributes that should not be searched.
        array('id, created, last_upd, user_id, project_id, rollon_date, rolloff_date', 'safe', 'on' => 'search, searchByProject'),
    );
  }

  public function beforeSave() {
    if (empty($this->rollon_date))
      $this->rollon_date = new CDbExpression('NULL');
    if (empty($this->rolloff_date))
      $this->rolloff_date = new CDbExpression('NULL');
    return parent::beforeSave();
  }

  /**
   * @return array relational rules.
   */
  public function relations() {
    // NOTE: you may need to adjust the relation name and the related
    // class name for the relations automatically generated below.
    return array(
        'user' => array(self::BELONGS_TO, 'User', 'user_id'),
        'project' => array(self::BELONGS_TO, 'Project', 'project_id')
    );
  }

  public function scopes() {
  /*
    return array(
        'active' => array(
            'condition' => "rolloff_date IS NULL OR rolloff_date >= '". date("Y-m-s H:i:s", time()) . "'",
        ),
    );
  */
    // 2024/9/11 modified(bug fix)
    return array(
        'active' => array(
            'condition' => "rolloff_date IS NULL OR rolloff_date >= '". date("Y-m-d H:i:s", time()) . "'",
        ),
    );

  }

  /**
   * @return array customized attribute labels (name=>label)
   */
  public function attributeLabels() {
    return array(
        'id' => Yii::t('attributes', 'UserProject.id'),
        'created' => Yii::t('attributes', 'UserProject.created'),
        'last_upd' => Yii::t('attributes', 'UserProject.last_upd'),
        'user_id' => Yii::t('attributes', 'UserProject.user_id'),
        'project_id' => Yii::t('attributes', 'UserProject.project_id'),
        'rollon_date' => Yii::t('attributes', 'UserProject.rollon_date'),
        'rolloff_date' => Yii::t('attributes', 'UserProject.rolloff_date'),
    );
  }

  /**
   * Retrieves a list of models based on the current search/filter conditions.
   * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
   */
  public function search() {
    $criteria = new CDbCriteria;

    $criteria->compare('user_id', $this->user_id);
    $criteria->compare('project_id', $this->project_id);
    $criteria->compare('rollon_date', $this->rollon_date, true);
    $criteria->compare('rolloff_date', $this->rolloff_date, true);

    return new CActiveDataProvider($this, array(
        'pagination' => array('pageSize' => Yii::app()->user->pageSize),
        'criteria' => $criteria,
    ));
  }

  /**
   * Retrieves a list of models based on the current search/filter conditions.
   * @param integer $projectId
   * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
   */
  public function searchByProject($projectId) {
    $criteria = new CDbCriteria;
    $criteria->compare('project_id', $projectId);
    $criteria->compare('user_id', $this->user_id);
    $criteria->compare('user.active', true);
    $criteria->compare('rollon_date', $this->rollon_date, true);
    $criteria->compare('rolloff_date', $this->rolloff_date, true);
    $criteria->together = true;
    $criteria->with = array('user');
    if (isset($_GET['User'])) {
      $criteria->compare('user.email', $_GET['User']['email'], true);
      $criteria->compare('user.level', $_GET['User']['level'], true);
      $criteria->addCondition("user.name LIKE '%{$_GET["User"]["calc_name"]}%' OR user.surname LIKE '%{$_GET["User"]["calc_name"]}%'");
    }

    $sort = new CSort();
    $sort->defaultOrder = 'user.username ASC';
    $sort->attributes = array(
        'user.level' => array(
            'asc' => 'user.level',
            'desc' => 'user.level desc',
        ),
        'user_id' => array(
            'asc' => 'user.username',
            'desc' => 'user.username desc',
        ),
        '*'
    );

    return new CActiveDataProvider($this, array(
        'criteria' => $criteria,
        'sort' => $sort,
    ));
  }

  public static function merge($projectId, array $userIds) {
    $newUsers = array();

    $assocRows = self::model()->findAllByAttributes(array('project_id' => $projectId));
    if ($assocRows === null)
      $assocRows = array();
    $assocUserIds = array();
    foreach ($assocRows as $row) {
      array_push($assocUserIds, $row->user_id);
    }

    foreach ($userIds as $userId) {
      if (!in_array($userId, $assocUserIds) && self::associate($projectId, $userId)) {
        array_push($newUsers, $userId);
      }
    }

    foreach ($assocUserIds as $assocUserId) {
      if (!in_array($assocUserId, $userIds)) {
        self::model()->findByAttributes(array('project_id' => $projectId, 'user_id' => $assocUserId))->delete();
      }
    }

    return $newUsers;
  }

  public static function associate($projectId, $userId, $from = null) {
    if ($from === null)
      $from = date('Y-m-d');

    $up = new UserProject;
    $up->rollon_date = $from;
    $up->project_id = $projectId;
    $up->user_id = $userId;
    return $up->save();
  }

}
