<?php

/**
 * This is the model class for table "{{project}}".
 *
 * The followings are the available columns in table '{{project}}':
 * @property integer $id
 * @property string $created
 * @property integer $created_by
 * @property string $last_upd
 * @property integer $last_upd_by
 * @property string $name
 * @property string $prefix
 * @property string $description
 * @property integer $champion_id
 * @property string $status
 * @property integer $progress
 * @property integer $par_project_id
 * @property string $start_date
 * @property string $end_date
 * @property string $eff_start_date
 * @property string $eff_end_date
 * @property string $client
 * @property boolean $chargeable_flg
 *
 * The followings are the available model relations:
 * @property User $champion
 * @property User $creator
 * @property Project $parProject
 * @property Project[] $hasProject
 * @property Task[] $tasks
 * @property UserProject[] $users
 */
class Project extends CActiveRecord {

  public $level = 0;

  /**
   * Returns the static model of the specified AR class.
   * @param string $className active record class name.
   * @return Project the static model class
   */
  public static function model($className = __CLASS__) {
    return parent::model($className);
  }

  /**
   * @return string the associated database table name
   */
  public function tableName() {
    return '{{project}}';
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
        array('champion_id', 'default', 'value' => Yii::app()->user->id),
        array('name, champion_id, prefix', 'required'),
// 2024/9/15 add
        array('name', 'filter', 'filter' => 'CHtml::encode'),
        // または、XSS対策として、HTMLタグを取り除く場合：
        array('name', 'filter', 'filter' => 'strip_tags'),
        array('progress, par_project_id, champion_id', 'numerical', 'integerOnly' => true),
        array('progress', 'numerical', 'min' => 0, 'max' => 100),
        array('progress', 'default', 'value' => 0),
        array('status', 'default', 'value' => 0),
        array('name, status', 'length', 'max' => 63),
        array('client', 'length', 'max' => 255),
        array('prefix', 'length', 'max' => 3),
        array('name', 'unique'),
        array('prefix', 'unique'),
//        array('prefix', 'default', 'value' => 'PRJ'),
        array('chargeable_flg', 'default', 'value' => false),
        array('chargeable_flg', 'boolean'),
//            array('prefix', 'match', 'pattern' => '/[0-9 ]+/', 'not' => true),
        array('description, end_date, eff_start_date, eff_end_date', 'safe'),
        array('start_date, end_date, eff_start_date, eff_end_date', 'date', 'format' => 'yyyy-MM-dd'),
        array('end_date', 'compare', 'compareAttribute' => 'start_date', 'operator' => '>', 'allowEmpty' => true),
        // The following rule is used by search().
        // Please remove those attributes that should not be searched.
        array('name, prefix, description, champion_id, par_project_id, client', 'safe', 'on' => 'search, searchMy'),
    );
  }

  public function beforeSave() {
    if (empty($this->start_date))
      $this->start_date = new CDbExpression('NULL');
    if (empty($this->end_date))
      $this->end_date = new CDbExpression('NULL');
    if (empty($this->eff_start_date))
      $this->eff_start_date = new CDbExpression('NULL');
    if (empty($this->eff_end_date))
      $this->eff_end_date = new CDbExpression('NULL');
    return parent::beforeSave();
  }

  /**
   * @return array relational rules.
   */
  public function relations() {
    // NOTE: you may need to adjust the relation name and the related
    // class name for the relations automatically generated below.

    // 2024/9/11 modified(bug fix)
    //$now = date("Y-m-s H:i:s", time());
    $now = date("Y-m-d H:i:s", time());

    return array(
        'creator' => array(self::BELONGS_TO, 'User', 'created_by', 'select' => array('id', 'username')),
        'champion' => array(self::BELONGS_TO, 'User', 'champion_id', 'select' => array('id', 'username', 'email')),
//            'championBadge' => array(self::BELONGS_TO, 'User', 'champion_id'),
        'parProject' => array(self::BELONGS_TO, 'Project', 'par_project_id', 'select' => array('id', 'name')),
        'hasProject' => array(self::HAS_MANY, 'Project', 'par_project_id'),
        'tasks' => array(self::HAS_MANY, 'Task', 'par_project_id'),
        'users' => array(self::MANY_MANY, 'User', '{{user_project}}(project_id,user_id)', 'order' => 'users.username', 'condition' => 'users.active = 1' /* 'scopes' => 'active' */),
        'enrolledUsers' => array(self::MANY_MANY, 'User', '{{user_project}}(project_id,user_id)',
            'order' => 'enrolledUsers.username',
            'condition' => "enrolledUsers.active = 1 AND (rolloff_date IS NULL OR rolloff_date >= '{$now}')"),
    );
  }

  public function scopes() {
    return array(
        'open' => array(
            'condition' => 'status = 0',
        ),
        'charge' => array(
            'condition' => 'status IN (0, 4) AND chargeable_flg = 1',
        ),
        'chargeOnly' => array(
            'condition' => 'status = 4',
        ),
    );
  }

  /**
   * @return array customized attribute labels (name=>label)
   */
  public function attributeLabels() {
    return array(
        'id' => Yii::t('attributes', 'Project.id'),
        'created' => Yii::t('attributes', 'Project.created'),
        'created_by' => Yii::t('attributes', 'Project.created_by'),
        'last_upd' => Yii::t('attributes', 'Project.last_upd'),
        'last_upd_by' => Yii::t('attributes', 'Project.last_upd_by'),
        'name' => Yii::t('attributes', 'Project.name'),
        'prefix' => Yii::t('attributes', 'Project.prefix'),
        'description' => Yii::t('attributes', 'Project.description'),
        'champion_id' => Yii::t('attributes', 'Project.champion_id'),
        'status' => Yii::t('attributes', 'Project.status'),
        'progress' => Yii::t('attributes', 'Project.progress'),
        'par_project_id' => Yii::t('attributes', 'Project.par_project_id'),
        'start_date' => Yii::t('attributes', 'Project.start_date'),
        'end_date' => Yii::t('attributes', 'Project.end_date'),
        'eff_start_date' => Yii::t('attributes', 'Project.eff_start_date'),
        'eff_end_date' => Yii::t('attributes', 'Project.eff_end_date'),
        'client' => Yii::t('attributes', 'Project.client'),
        'chargeable_flg' => Yii::t('attributes', 'Project.chargeable_flg'),
    );
  }

  /**
   * Retrieves a list of models based on the current search/filter conditions.
   * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
   */
  public function search() {
    $criteria = new CDbCriteria;

    $criteria->together = true;
    $criteria->with = array('champion', 'parProject');

    $criteria->compare('t.name', $this->name, true);
    $criteria->compare('t.description', $this->description, true);
    $criteria->compare('t.champion_id', $this->champion_id);
    $criteria->compare('t.par_project_id', $this->par_project_id);
    $criteria->compare('t.client', $this->client, true);
    $criteria->compare('t.status', $this->status);

    $sort = new CSort;
    $sort->defaultOrder = 't.name';
    $sort->attributes = array(
        'champion_id' => array(
            'asc' => 'champion.username',
            'desc' => 'champion.username desc',
        ),
        'par_project_id' => array(
            'asc' => 'parProject.name',
            'desc' => 'parProject.name desc',
        ),
        '*'
    );

    return new CActiveDataProvider($this, array(
        'pagination' => array('pageSize' => Yii::app()->user->pageSize),
        'criteria' => $criteria,
        'sort' => $sort,
    ));
  }

  /**
   * Retrieves a list of models based on the current search/filter conditions.
   * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
   */
  public function searchMy() {
    $criteria = new CDbCriteria;

    $criteria->together = true;
    $criteria->with = array('users' => array('select' => false), 'champion', 'parProject');
    $criteria->compare('users.id', Yii::app()->user->id);

    $criteria->compare('t.name', $this->name, true);
    $criteria->compare('t.description', $this->description, true);
    $criteria->compare('t.champion_id', $this->champion_id);
    $criteria->compare('t.par_project_id', $this->par_project_id);
    $criteria->compare('t.client', $this->client, true);
    $criteria->compare('t.status', $this->status);

    $sort = new CSort;
    $sort->defaultOrder = 't.name';
    $sort->attributes = array(
        'champion_id' => array(
            'asc' => 'champion.username',
            'desc' => 'champion.username desc',
        ),
        'par_project_id' => array(
            'asc' => 'parProject.name',
            'desc' => 'parProject.name desc',
        ),
        '*'
    );

    return new CActiveDataProvider($this, array(
        'pagination' => array('pageSize' => Yii::app()->user->pageSize),
        'criteria' => $criteria,
        'sort' => $sort,
    ));
  }

  /**
   * Provides the list of available Status values.
   * @return array available Status values.
   */
  public function getStatusList() {
    return array(Yii::t('constants', 'Project.status.open'),
        Yii::t('constants', 'Project.status.suspended'),
        Yii::t('constants', 'Project.status.closed'),
        Yii::t('constants', 'Project.status.deleted'),
        Yii::t('constants', 'Project.status.chargeItem'));
  }

  public function getStatus() {
    $list = $this->getStatusList();
    return $list[intval($this->status)];
  }

  public function getChargeProjectsList() {
    return array(Yii::t('constants', 'Project.charge.courses'),
        Yii::t('constants', 'Project.charge.vacation'),
        Yii::t('constants', 'Project.charge.publicHoliday'),
        Yii::t('constants', 'Project.charge.sickLeave'));
  }

  private function hierMaps(&$rootFirstList) {
    $childrenList = array();
    foreach ($rootFirstList as $i => $project) {
      if (!empty($project->par_project_id)) {
        !isset($childrenList[$project->par_project_id]) &&
                $childrenList[$project->par_project_id] = array();
        array_push($childrenList[$project->par_project_id], $i);
      }
    }
    return $childrenList;
  }

  private function hierSort(&$rootFirstList) {
    $childrenList = $this->hierMaps($rootFirstList);
    $helper = array(); // tracks already processed records
    $resultList = array();

    $getChildren = function (&$project, $level) use (
            &$getChildren, // for recursion
            &$rootFirstList, &$childrenList, &$helper, &$resultList
            ) {
      $children = isset($childrenList[$project->id]) ? $childrenList[$project->id] : null;
      if (!empty($children)) {
        foreach ($children as $childIndex) {
          if (!in_array($childIndex, $helper)) {
            $child = $rootFirstList[$childIndex];
            $child->level = $level;

            array_push($helper, $childIndex);
            array_push($resultList, $child);
            $getChildren($child, $level + 1);
          }
        }
      }
    };

    foreach ($rootFirstList as $i => $project) {
      if (!in_array($i, $helper)) {
        array_push($helper, $i);
        array_push($resultList, $project);
        $getChildren($project, 1);
      }
    }

    return $resultList;
  }

  public function findAllHierarchical($condition = '', $params = array()) {
    $inCriteria = $this->getCommandBuilder()->createCriteria($condition, $params);

    $rootCriteria = new CDbCriteria();
    $rootCriteria->order = 't.par_project_id, t.id';
    if (!empty($inCriteria->order) && strpos($inCriteria->order, 'par_project_id') === false) {
      $inCriteria->order = 't.par_project_id, ' . $inCriteria->order;
      $rootCriteria->order = $inCriteria->order;
    }
    $rootCriteria->mergeWith($inCriteria);
    $rootFirstList = Project::model()->findAll($rootCriteria);

    return $this->hierSort($rootFirstList);
  }

  public function findAllRoot($condition = '', $params = array()) {
    $inCriteria = $this->getCommandBuilder()->createCriteria($condition, $params);
    $rootCriteria = new CDbCriteria();
    $rootCriteria->addCondition('t.par_project_id IS NULL');
    $rootCriteria->order = 't.name';
    if (!empty($inCriteria->order))
      $inCriteria->order = $inCriteria->order;
    $rootCriteria->mergeWith($inCriteria);
    return Project::model()->findAll($rootCriteria);
  }

  public function getSearchSortDefault() {
    $sort = new CSort;
    $sort->defaultOrder = 't.name';
    $sort->attributes = array(
        'champion_id' => array(
            'asc' => 'champion.username',
            'desc' => 'champion.username desc',
        ),
        'par_project_id' => array(
            'asc' => 'parProject.name',
            'desc' => 'parProject.name desc',
        ),
        '*'
    );
    return $sort;
  }

  public function getSearchHierarchicalCriteria() {
    $criteria = new CDbCriteria;

    $criteria->together = true;
    $criteria->with = array('champion', 'parProject');

    $criteria->compare('t.name', $this->name, true);
    $criteria->compare('t.description', $this->description, true);
    $criteria->compare('t.champion_id', $this->champion_id);
    $criteria->compare('t.par_project_id', $this->par_project_id);
    $criteria->compare('t.client', $this->client, true);
    $criteria->compare('t.status', $this->status);

    return $criteria;
  }

  public function getSearchMyHierarchicalCriteria() {
    $criteria = $this->searchHierarchicalCriteria;

    if (is_array($criteria->with))
      $criteria->with = array_merge($criteria->with, array('users' => array('select' => false)));
    else
      $criteria->with = array('users' => array('select' => false));

    $criteria->compare('users.id', Yii::app()->user->id);

    return $criteria;
  }

  public function searchHierarchical() {
    $criteria = $this->searchHierarchicalCriteria;

    $sort = $this->searchSortDefault;

    return new EHierActiveDataProvider($this, array(
        'pagination' => array('pageSize' => Yii::app()->user->pageSize),
        'criteria' => $criteria,
        'sort' => $sort,
    ));
  }

  public function searchMyHierarchical() {
    $criteria = $this->searchMyHierarchicalCriteria;

    $sort = $this->searchSortDefault;

    return new EHierActiveDataProvider($this, array(
        'pagination' => array('pageSize' => Yii::app()->user->pageSize),
        'criteria' => $criteria,
        'sort' => $sort,
    ));
  }

}
