<?php

/**
 * This is the model class for table "{{charge}}".
 *
 * The followings are the available columns in table '{{charge}}':
 * @property integer $id
 * @property string $created
 * @property integer $created_by
 * @property string $last_upd
 * @property integer $last_upd_by
 * @property integer $user_id
 * @property integer $project_id
 * @property integer $task_id
 * @property date $day
 * @property double $hours
 */
class Charge extends CActiveRecord {

  public $user_name;
  public $month;
  public $group_total;
  public $day_total;

  /**
   * Returns the static model of the specified AR class.
   * @param string $className active record class name.
   * @return Charge the static model class
   */
  public static function model($className = __CLASS__) {
    return parent::model($className);
  }

  /**
   * @return string the associated database table name
   */
  public function tableName() {
    return '{{charge}}';
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

    return array(
        array('user_id, project_id, day, hours', 'required'),
        array('user_id, project_id, task_id', 'numerical', 'integerOnly' => true),
        array('day', 'date', 'format' => 'yyyy-MM-dd'),
        array('hours', 'numerical'),
        // The following rule is used by search().
        // Please remove those attributes that should not be searched.
        array('user_id, project_id, task_id, day, hours', 'safe', 'on' => 'search'),
        array('user_name, month, group_total', 'safe', 'on' => 'search'),
        array('day', 'isChargeUnique', 'on' => 'insert')
    );
  }

  /**
   * @return array relational rules.
   */
  public function relations() {
    // NOTE: you may need to adjust the relation name and the related
    // class name for the relations automatically generated below.
    return array(
        'task' => array(self::BELONGS_TO, 'Task', 'task_id', 'select' => 'id, title, par_project_id'),
        'user' => array(self::BELONGS_TO, 'User', 'user_id', 'select' => 'id, username'),
        'project' => array(self::BELONGS_TO, 'Project', 'project_id', 'select' => 'id, name'),
        'alltask' => array(self::HAS_MANY, 'Task', array('par_project_id' => 'project_id'), 'select' => 'id, title, par_project_id',
            'condition' => 'alltask.chargeable_flg = 1'),
    );
  }

  /**
   * @return array customized attribute labels (name=>label)
   */
  public function attributeLabels() {
    return array(
        'id' => Yii::t('attributes', 'Charge.id'),
        'created' => Yii::t('attributes', 'Charge.created'),
        'created_by' => Yii::t('attributes', 'Charge.created_by'),
        'last_upd' => Yii::t('attributes', 'Charge.last_upd'),
        'last_upd_by' => Yii::t('attributes', 'Charge.last_upd_by'),
        'user_id' => Yii::t('attributes', 'Charge.user_id'),
        'project_id' => Yii::t('attributes', 'Charge.project_id'),
        'task_id' => Yii::t('attributes', 'Charge.task_id'),
        'month' => Yii::t('attributes', 'Charge.month'),
        'day' => Yii::t('attributes', 'Charge.day'),
        'hours' => Yii::t('attributes', 'Charge.hours'),
        'first_total' => Yii::t('attributes', 'Charge.first_total'),
        'second_total' => Yii::t('attributes', 'Charge.second_total'),
        'group_total' => Yii::t('attributes', 'Charge.group_total'),
        'day_total' => Yii::t('attributes', 'Charge.day_total'),
        'user_name' => Yii::t('attributes', 'Charge.user_name'),
        'total' => Yii::t('attributes', 'Charge.total'),
        'first_half' => Yii::t('attributes', 'Charge.first_half'),
        'second_half' => Yii::t('attributes', 'Charge.second_half'),
        'half_select' => Yii::t('attributes', 'Charge.half_select'),
    );
  }

  /**
   * Retrieves a list of models based on the current search/filter conditions.
   * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
   */
  public function search() {
    // Warning: Please modify the following code to remove attributes that
    // should not be searched.

    $criteria = new CDbCriteria;

    $criteria->compare('user_id', $this->user_id);
    $criteria->compare('project_id', $this->project_id);
    $criteria->compare('task_id', $this->task_id);
    $criteria->compare('day', $this->day, true);
    $criteria->compare('hours', $this->hours);

    return new CActiveDataProvider($this, array(
        'criteria' => $criteria,
    ));
  }

  public function customSearch($all = false, $disablePagination = false) {
    $criteria = new CDbCriteria;

    $criteria->select = '*, SUM(hours) as group_total';
    $criteria->together = true;
    $criteria->with = array(
        'project' => array('alias' => 'p'),
        'user' => array('alias' => 'u'),
    );

    if (!empty($this->user_name)) {
      $users = array();
      $userCriteria = new CDbCriteria;
      $userCriteria->select = 'id';
      $userCriteria->compare('username', $this->user_name, true);
      foreach (User::model()->findAll($userCriteria) as $user)
        array_push($users, $user->id);
      $criteria->addInCondition('user_id', $users);
    }

    if ($this->project_id) {
      $criteria->compare('project_id', $this->project_id);
    } else {
      if (!$all) {
        $projectCriteria = new CDbCriteria;
        $projectCriteria->select = 'id';
        $projectCriteria->together = true;
        $projectCriteria->with = array('users' => array('select' => false));
        $projectCriteria->compare('users.id', Yii::app()->user->id);
        $projects = array();
        foreach (Project::model()->findAll($projectCriteria) as $project)
          array_push($projects, $project->id);
        $criteria->addInCondition('project_id', $projects);
      }
    }

    $dateFormat = $this->getDbConnection()->getDriverName() == 'sqlite' ? 'strftime(\'%m %Y\', day)' : 'DATE_FORMAT(day, \'%M %Y\')';
    $dateFormatYM = $this->getDbConnection()->getDriverName() == 'sqlite' ? 'strftime(\'%Y%m\', day)' : 'DATE_FORMAT(day, \'%Y%m\')';
    
	
	if ($this->month) {
      $criteria->addInCondition($dateFormat, $this->month);
	  //$criteria->addCondition($dateFormat . ' = :m');
      //$criteria->params[':m'] = $this->getDbConnection()->getDriverName() == 'sqlite' ? date('m Y', strtotime($this->month)) : $this->month;
    }

    // 2024/9/11 modified
    //$criteria->group = 't.user_id, '. $dateFormat . ', t.project_id';
    // 2024/9/15 modified mysq8‚ÅONLY_FULL_GROUP_BY‚ð‚â‚ß‚Äok‚É‚µ‚½
    //$criteria->group = 't.id, '. 't.user_id, '. $dateFormat . ', t.project_id';
    $criteria->group = 't.user_id, '. $dateFormat . ', t.project_id';

    if ($this->group_total) {
      $criteria->having = $this->_customCompare('SUM(hours)', $this->group_total);
    }

    $sort = new CSort;
    $sort->multiSort = true;
    $sort->defaultOrder = array(
        'month' => CSort::SORT_DESC,
        'project_id' => CSort::SORT_ASC,
        'user_name' => CSort::SORT_ASC,
    );
    $sort->attributes = array(
        'month' => array(
            'asc' => $dateFormatYM,
            'desc' => $dateFormatYM . ' DESC',
        ),
        'project_id' => array(
            'asc' => 'p.name',
            'desc' => 'p.name DESC',
        ),
        'user_name' => array(
            'asc' => 'u.username',
            'desc' => 'u.username DESC',
        ),
        '*'
    );

    return new CActiveDataProvider($this, array(
        'pagination' => $disablePagination ? false : array('pageSize' => Yii::app()->user->pageSize),
        'criteria' => $criteria,
        'sort' => $sort,
    ));
  }

  public function exportDetailsSearch() {
	  
    if (!$this->project_id || !$this->month)
      return null;

    $dateFormat = $this->getDbConnection()->getDriverName() == 'sqlite' ? 'strftime(\'%m %Y\', day)' : 'DATE_FORMAT(day, \'%M %Y\')';

    $criteria = new CDbCriteria;
    $criteria->select = 'user_id, project_id, day, SUM(hours) as day_total';
    $criteria->together = true;
    $criteria->with = array('project', 'user', 'task');
    $criteria->compare('project_id', $this->project_id);
    if ($this->month) {
		$criteria->addInCondition($dateFormat, $this->month);
    //  $criteria->addCondition($dateFormat . ' = :m');
    //  $criteria->params[':m'] = $this->getDbConnection()->getDriverName() == 'sqlite' ? date('m Y', strtotime($this->month)) : $this->month;
    }
      if (!empty($this->user_name)) {
          $users = array();
          $userCriteria = new CDbCriteria;
          $userCriteria->select = 'id';
          $userCriteria->compare('username', $this->user_name, true);
          foreach (User::model()->findAll($userCriteria) as $user)
              array_push($users, $user->id);
          $criteria->addInCondition('user_id', $users);
      }
    $criteria->group = 'user_id, project_id, task_id, day';
    $criteria->order = 'user.username, project.name, day';

    $charges = Charge::model()->findAll($criteria);

    $rawData = array();
    foreach ($charges as $charge) {
	  $taskTitle = isset( $charge->task->title ) ? $charge->task->title : "";
      $rawData[$charge->user->username][$charge->project->name][$taskTitle][$charge->day] = $charge->day_total;
    }
    $arrayData = array();
    foreach ($rawData as $user => $userAttr) {
      foreach ($userAttr as $project => $projAttr) {
		  foreach( $projAttr as $task => $charge )
		  {
			array_push($arrayData, array(
				'user' => $user,
				'project' => $project,
				'task' => $task,
				'charge' => $charge,
			));
		  }
      }
    }

//        Yii::log(CVarDumper::dumpAsString($arrayData), CLogger::LEVEL_WARNING, 'colamonici');
    return new CArrayDataProvider($arrayData, array(
        'id' => 'chargeDetails',
//            'keyField' => 'user_id',
        'sort' => false,
        'pagination' => false,
    ));
  }

  private function _customCompare($sqlField, $value) {
    if (preg_match('/^(?:\s*(<>|<=|>=|<|>|=))?(.*)$/', $value, $matches)) {
      $value = $matches[2];
      $op = $matches[1];
    }

    if ($op === '') {
      $op = '=';
    }

    return $sqlField . ' ' . $op . ' ' . $value;
  }

  public function getTotal($records, $field) {
    $total = 0;
    foreach ($records as $record) {
      $total += $record->$field;
    }
    return $total;
  }

  public function getFirstTotal() {
    $group_month = date('Y-m', strtotime($this->day));
    return round(CPropertyValue::ensureFloat(Yii::app()->getDb()->createCommand()
                            ->select('SUM(hours)')
                            ->from($this->tableName())
                            ->where(
                                    array(
                                'AND',
                                'user_id = :user_id',
                                'project_id = :project_id',
                                'day BETWEEN :start AND :end'
                                    ), array(
                                ':user_id' => $this->user_id,
                                ':project_id' => $this->project_id,
                                ':start' => $group_month . '-01',
                                ':end' => $group_month . '-15'
                                    )
                            )
                            ->queryScalar()), 2);
  }

  public function getSecondTotal() {
    return round($this->group_total - $this->firstTotal, 2);
  }

  public function afterFind() {
    parent::afterFind();
    if ($this->day_total)
      $this->day_total = round($this->day_total, 2);
    if ($this->group_total)
      $this->group_total = round($this->group_total, 2);
  }

  public function isChargeUnique($attribute, $params) {

    $criteria = array(
        'criteria' => array(
            'condition' => 'project_id = :project_id AND user_id = :user_id',
            'params' => array(
                ':project_id' => $this->project_id,
                ':user_id' => $this->user_id,
            )
        ),
    );

    if ($this->task_id != '') {
      $criteria['criteria']['condition'] .= " AND task_id = :task_id";
      $criteria['criteria']['params'][':task_id'] = $this->task_id;
    } else {
      $criteria['criteria']['condition'] .= " AND task_id IS NULL";
    }


    CValidator::createValidator('unique', $this, $attribute, $criteria)->validate($this);
  }

}
