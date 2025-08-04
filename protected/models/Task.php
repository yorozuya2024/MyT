<?php

/**
 * This is the model class for table "{{task}}".
 *
 * The followings are the available columns in table '{{task}}':
 * @property integer $id
 * @property string $created
 * @property integer $created_by
 * @property string $last_upd
 * @property integer $last_upd_by
 * @property integer $par_project_id
 * @property integer $parent_id
 * @property string $title
 * @property string $description
 * @property string $type
 * @property string $status
 * @property integer $progress
 * @property string $start_date
 * @property string $end_date
 * @property string $eff_start_date
 * @property string $eff_end_date
 * @property string $priority
 * @property boolean $private_flg
 * @property boolean $expired
 * @property boolean $chargeable_flg
 *
 * The followings are the available model relations:
 * @property User $creator
 * @property Project $project
 * @property User[] $users
 */
class Task extends CActiveRecord {

  /**
   * User Name associated with the Task. Used for queries.
   * @var string
   */
  public $owner;

  /**
   * Creator Username.
   * @var string
   */
  public $author;

  /**
   * Fake Id.
   * @var string
   */
  public $calc_id;

  /**
   * End Date passed.
   * @var boolean
   */
  public $expired;
  public $level = 0;

  /**
   * Returns the static model of the specified AR class.
   * @param string $className active record class name.
   * @return Task the static model class
   */
  public static function model($className = __CLASS__) {
    return parent::model($className);
  }

  /**
   * @return string the associated database table name
   */
  public function tableName() {
    return '{{task}}';
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
        array('par_project_id, title', 'required'),
        array('par_project_id, parent_id', 'numerical', 'integerOnly' => true),
//        array('par_project_id', 'compare', 'compareAttribute' => 'parent.par_project_id', 'enableClientValidation' => false),
        array('title, status, priority', 'length', 'max' => 63),
// 2024/9/15 add
        array('title', 'filter', 'filter' => 'CHtml::encode'),
        // または、XSS対策として、HTMLタグを取り除く場合：
        array('title', 'filter', 'filter' => 'strip_tags'),
        array('private_flg, chargeable_flg', 'boolean'),
        array('progress', 'default', 'value' => 0),
        array('progress', 'numerical', 'integerOnly' => true, 'min' => 0, 'max' => 100),
        array('priority', 'default', 'value' => 1),
        array('type', 'numerical', 'integerOnly' => true, 'min' => 1),
        array('chargeable_flg', 'default', 'value' => false),
        array('description, eff_start_date, eff_end_date', 'safe'),
        array('start_date, end_date, eff_start_date, eff_end_date', 'date', 'format' => 'yyyy-MM-dd', 'allowEmpty' => true),
        array('end_date', 'compare', 'compareAttribute' => 'start_date', 'operator' => '>=', 'allowEmpty' => true),
        array('parent_id', 'sameProject'),
        array('status', 'childrenStatus'),
        // The following rule is used by search().
        // Please remove those attributes that should not be searched.
        array('id, created, created_by, last_upd, par_project_id, parent_id, title, description, type, status, progress, start_date, end_date, eff_start_date, eff_end_date, priority',
            'safe', 'on' => 'search, searchByProject'),
        array('owner, author, calc_id', 'safe', 'on' => 'search, searchMy, searchByProject'),
    );
  }

  public function childrenStatus($attribute) {
    if (!$this->hasErrors() && !empty($this->$attribute) && !$this->isNewRecord && in_array($this->$attribute, TaskStatus::model()->closedStatusList)) {
      $children = Task::model()->open()->findByAttributes(array('parent_id' => $this->id));
      if (!empty($children)) {
        $this->addError('status', Yii::t('app', 'Task.status.closed.invalid.parent'));
      }
    }
  }

  public function sameProject($attribute) {
    if (!$this->hasErrors() && !empty($this->$attribute)) {
      $parent = Task::model()->findByPk($this->$attribute);
      if ($parent) {
        if ($this->par_project_id !== $parent->par_project_id)
          $this->addError('parent_id', Yii::t('app', 'Task.parent_id.invalid.project'));
      } else
        $this->addError('parent_id', Yii::t('app', 'Task.parent_id.invalid'));
    }
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
    return array(
        'creator' => array(self::BELONGS_TO, 'User', 'created_by'),
        'project' => array(self::BELONGS_TO, 'Project', 'par_project_id',
            'select' => array('id', 'name', 'prefix', 'par_project_id')),
        'inter_users' => array(self::HAS_MANY, 'UserTask', 'task_id'),
        'assoc_users' => array(self::HAS_MANY, 'User', 'user_id', 'through' => 'inter_users'),
        'users' => array(self::MANY_MANY, 'User', '{{user_task}}(task_id, user_id)',
            'order' => 'users.username', 'select' => array('users.id', 'users.username', 'users.email')),
        'task_status' => array(self::BELONGS_TO, 'TaskStatus', array('status' => 'id'),
            'order' => 'task_status.order_by'),
        'task_type' => array(self::BELONGS_TO, 'TaskType', array('type' => 'id'),
            'order' => 'task_type.order_by'),
        'parent' => array(self::BELONGS_TO, 'Task', 'parent_id'),
        'children' => array(self::HAS_MANY, 'Task', 'parent_id'),
    );
  }

  /**
   * @return array customized attribute labels (name=>label)
   */
  public function attributeLabels() {
    return array(
        'id' => Yii::t('attributes', 'Task.id'),
        'created' => Yii::t('attributes', 'Task.created'),
        'created_by' => Yii::t('attributes', 'Task.created_by'),
        'last_upd' => Yii::t('attributes', 'Task.last_upd'),
        'last_upd_by' => Yii::t('attributes', 'Task.last_upd_by'),
        'par_project_id' => Yii::t('attributes', 'Task.par_project_id'),
        'title' => Yii::t('attributes', 'Task.title'),
        'description' => Yii::t('attributes', 'Task.description'),
        'status' => Yii::t('attributes', 'Task.status'),
        'progress' => Yii::t('attributes', 'Task.progress'),
        'start_date' => Yii::t('attributes', 'Task.start_date'),
        'end_date' => Yii::t('attributes', 'Task.end_date'),
        'eff_start_date' => Yii::t('attributes', 'Task.eff_start_date'),
        'eff_end_date' => Yii::t('attributes', 'Task.eff_end_date'),
        'priority' => Yii::t('attributes', 'Task.priority'),
        'type' => Yii::t('attributes', 'Task.type'),
        'calc_id' => Yii::t('attributes', 'Task.calc_id'),
        'author' => Yii::t('attributes', 'Task.author'),
        'owner' => Yii::t('attributes', 'Task.owner'),
        'expired' => Yii::t('attributes', 'Task.expired'),
        'private_flg' => Yii::t('attributes', 'Task.private_flg'),
        'chargeable_flg' => Yii::t('attributes', 'Task.chargeable_flg'),
        'project' => Yii::t('attributes', 'Task.project'),
        'parent' => Yii::t('attributes', 'Task.parent'),
        'parent_id' => Yii::t('attributes', 'Task.parent_id'),
    );
  }

  /**
   * Retrieves a list of models based on the current search/filter conditions.
   * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
   */
  public function search() {
    $criteria = new CDbCriteria;

    $criteria->compare('t.par_project_id', $this->par_project_id);
    $criteria->compare('t.title', $this->title, true);
    $criteria->compare('t.type', $this->type);
    $criteria->compare('t.status', $this->status);
    $criteria->compare('t.progress', $this->progress);
    $criteria->compare('t.start_date', $this->start_date, true);
    $criteria->compare('t.end_date', $this->end_date, true);
    $criteria->compare('t.eff_start_date', $this->eff_start_date, true);
    $criteria->compare('t.eff_end_date', $this->eff_end_date, true);
    $criteria->compare('t.priority', $this->priority, true);

    $criteria->together = true;
    if (!empty($this->owner)) {
      $criteria->with = array('users' => array('select' => false));
      $criteria->compare('users.username', $this->owner, true);
    }
    if (is_array($criteria->with))
      $criteria->with = array_merge($criteria->with, array('creator' => array('select' => false)));
    else
      $criteria->with = array('creator' => array('select' => false));
    $criteria->compare('creator.username', $this->author, true);

    if (is_array($criteria->with))
      $criteria->with = array_merge($criteria->with, array('project'));
    else
      $criteria->with = array('project');
    if (!empty($this->calc_id))
      $criteria->compare('CONCAT(project.prefix, LPAD(t.id,' . $this->idLength . ',\'0\'))', $this->calc_id, true);

    $criteria->addCondition('t.private_flg = 0 OR (t.private_flg = 1 AND EXISTS (
            SELECT id FROM {{user_task}} WHERE task_id = t.id AND user_id = ' . Yii::app()->user->id . '
        ))');

//    if (is_array($criteria->with))
//      $criteria->with = array_merge($criteria->with, array('assoc_users' => array('select' => false)));
//    else
//      $criteria->with = array('assoc_users' => array('select' => false));

    $sort = $this->searchSortDefault;

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
  public function searchMy($status = null) {
    $criteria = new CDbCriteria;

    $criteria->compare('t.par_project_id', $this->par_project_id);
    $criteria->compare('t.title', $this->title, true);
    $criteria->compare('t.type', $this->type);
    if ($status !== null) {
      if (is_string($status))
        $criteria->compare('t.status', TaskStatus::getStatusId($status));
      else
        $criteria->compare('t.status', $status);
    } else
      $criteria->compare('t.status', $this->status);
    $criteria->compare('t.progress', $this->progress);
    $criteria->compare('t.start_date', $this->start_date, true);
    $criteria->compare('t.end_date', $this->end_date, true);
    $criteria->compare('t.eff_start_date', $this->eff_start_date, true);
    $criteria->compare('t.eff_end_date', $this->eff_end_date, true);
    $criteria->compare('t.priority', $this->priority, true);

    $criteria->together = true;
    $criteria->with = array('inter_users' => array('select' => false));
    $criteria->compare('inter_users.user_id', Yii::app()->user->id);

    if (is_array($criteria->with))
      $criteria->with = array_merge($criteria->with, array('creator' => array('select' => false)));
    else
      $criteria->with = array('creator' => array('select' => false));
    $criteria->compare('creator.username', $this->author, true);

    if (is_array($criteria->with))
      $criteria->with = array_merge($criteria->with, array('project'));
    else
      $criteria->with = array('project');
    if (!empty($this->calc_id))
      $criteria->compare('CONCAT(project.prefix, LPAD(t.id,' . $this->idLength . ',\'0\'))', $this->calc_id, true);

    if (!empty($this->owner)) {
      $criteria->with = array_merge($criteria->with, array('users' => array('select' => false)));
      $criteria->compare('users.username', $this->owner, true);
    }

    $sort = $this->searchSortDefault;

    return new CActiveDataProvider($this, array(
        'pagination' => array('pageSize' => Yii::app()->user->pageSize),
        'criteria' => $criteria,
        'sort' => $sort,
    ));
  }

  /**
   * Search Specification linked to Project.
   * @param int $projectId
   * @return CActiveDataProvider
   * @link http://www.yiiframework.com/wiki/281/searching-and-sorting-by-related-model-in-cgridview/
   */
  public function searchByProject($projectId) {
    $criteria = new CDbCriteria;
    $criteria->compare('t.par_project_id', $projectId, false);

    $criteria->together = true;
    if (!empty($this->owner)) {
      $criteria->with = array('users' => array('select' => false));
      $criteria->compare('users.username', $this->owner, true);
    }
    if (is_array($criteria->with))
      $criteria->with = array_merge($criteria->with, array('creator' => array('select' => false)));
    else
      $criteria->with = array('creator' => array('select' => false));
    $criteria->compare('creator.username', $this->author, true);

    if (is_array($criteria->with))
      $criteria->with = array_merge($criteria->with, array('project'));
    else
      $criteria->with = array('project');
    if (!empty($this->calc_id))
      $criteria->compare('CONCAT(project.prefix, LPAD(t.id,' . $this->idLength . ',\'0\'))', $this->calc_id, true);

    $criteria->compare('t.title', $this->title, true);
    $criteria->compare('t.description', $this->description, true);
    $criteria->compare('t.type', $this->type);

    $criteria->compare('t.start_date', $this->start_date, true);
    $criteria->compare('t.end_date', $this->end_date, true);
    $criteria->compare('t.status', $this->status);
    $criteria->compare('t.progress', $this->progress);
    $criteria->compare('t.priority', $this->priority);

    $criteria->addCondition('t.private_flg = 0 OR (t.private_flg = 1 AND EXISTS (
            SELECT id FROM {{user_task}} WHERE task_id = t.id AND user_id = ' . Yii::app()->user->id . '
        ))');

    $sort = $this->searchSortDefault;
    $sort->multiSort = true;

    return new CActiveDataProvider($this, array(
        'pagination' => array('pageSize' => Yii::app()->user->pageSize),
        'criteria' => $criteria,
        'sort' => $sort,
    ));
  }

  public function scopes() {
    return array(
        'public' => array(
            'condition' => 't.private_flg = 0',
        ),
    );
  }

  public function open() {
    $criteria = new CDbCriteria;
    $criteria->compare('t.status', TaskStatus::model()->openStatusList);
    $this->getDbCriteria()->mergeWith($criteria);
    return $this;
  }

  public function afterFind() {
    parent::afterFind();

    $prefix = $this->project ? $this->project->prefix : '';
    $this->calc_id = (empty($prefix) ? 'TSK' : $prefix) . str_pad($this->id, $this->idLength, '0', STR_PAD_LEFT);
    $this->expired = $this->end_date !== null && strtotime($this->end_date) <= strtotime('now');
  }

  public function getIdLength() {
    return Yii::app()->params['taskIdLength'] ? Yii::app()->params['taskIdLength'] : 5;
  }

  /**
   * Provides the list of available Status values.
   * @return array available Status values.
   */
  public function getStatusList() {
    return TaskStatus::model()->active()->findAll();
  }

  public function getStatus() {
    return $this->task_status->name;
  }

  /**
   * Provides the list of available Priority values.
   * @return array available Priority values.
   */
  public function getPriorityList() {
    return array(Yii::t('constants', 'Task.priority.low'),
        Yii::t('constants', 'Task.priority.medium'),
        Yii::t('constants', 'Task.priority.high'),
        Yii::t('constants', 'Task.priority.escalated'));
  }

  public function getPriority() {
    $list = $this->getPriorityList();
    return $list[intval($this->priority)];
  }

  /**
   * Provides the list of available Priority values.
   * @return array available Type values.
   */
  public function getTypeList() {
    return TaskType::model()->active()->findAll();
  }

  public function getType() {
    return $this->task_type->name;
  }

  public function getSearchSortDefault() {
    $sort = new CSort;
    $sort->defaultOrder = 't.id desc';
    $sort->attributes = array(
        'calc_id' => array(
            'asc' => 't.id',
            'desc' => 't.id desc',
        ),
        'author' => array(
            'asc' => 'creator.username',
            'desc' => 'creator.username desc',
        ),
        'type' => array(
            'asc' => 'task_type.order_by',
            'desc' => 'task_type.order_by desc',
        ),
        '*'
    );
    return $sort;
  }

  public function getSearchHierarchicalCriteria() {
    $criteria = new CDbCriteria;

    $criteria->compare('t.par_project_id', $this->par_project_id);
    $criteria->compare('t.title', $this->title, true);
    $criteria->compare('t.type', $this->type);
    $criteria->compare('t.status', $this->status);
    $criteria->compare('t.progress', $this->progress);
    $criteria->compare('t.start_date', $this->start_date, true);
    $criteria->compare('t.end_date', $this->end_date, true);
    $criteria->compare('t.eff_start_date', $this->eff_start_date, true);
    $criteria->compare('t.eff_end_date', $this->eff_end_date, true);
    $criteria->compare('t.priority', $this->priority, true);

    $criteria->together = true;
    if (!empty($this->owner)) {
      $criteria->with = array('users');
      $criteria->compare('users.username', $this->owner, true);
    }

    $task_status = array('task_status');
    if (is_array($criteria->with))
      $criteria->with = array_merge($criteria->with, $task_status);
    else
      $criteria->with = $task_status;
    
    $task_type = array('task_type');
    if (is_array($criteria->with))
      $criteria->with = array_merge($criteria->with, $task_type);
    else
      $criteria->with = $task_type;

    $creator = array('creator' => array('select' => false));
    if (is_array($criteria->with))
      $criteria->with = array_merge($criteria->with, $creator);
    else
      $criteria->with = $creator;
    $criteria->compare('creator.username', $this->author, true);

    $project = array('project');
    if (is_array($criteria->with))
      $criteria->with = array_merge($criteria->with, $project);
    else
      $criteria->with = $project;

    if (!empty($this->calc_id))
      $criteria->compare('CONCAT(project.prefix, LPAD(t.id,' . $this->idLength . ',\'0\'))', $this->calc_id, true);

    $criteria->addCondition('t.private_flg = 0 OR (t.private_flg = 1 AND EXISTS (
            SELECT id FROM {{user_task}} WHERE task_id = t.id AND user_id = ' . Yii::app()->user->id . '
        ))');

//    if (is_array($criteria->with))
//      $criteria->with = array_merge($criteria->with, array('assoc_users' => array('select' => false)));
//    else
//      $criteria->with = array('assoc_users' => array('select' => false));

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

  public function getSearchMyHierarchicalCriteria($status = null) {
    $criteria = $this->searchHierarchicalCriteria;

    if ($status !== null) {
      if (is_string($status))
        $criteria->compare('t.status', TaskStatus::getStatusId($status));
      else
        $criteria->compare('t.status', $status);
    } else
      $criteria->compare('t.status', $this->status);
    if (is_array($criteria->with))
      $criteria->with = array_merge($criteria->with, array('inter_users' => array('select' => false)));
    else
      $criteria->with = array('inter_users' => array('select' => false));
    $criteria->compare('inter_users.user_id', Yii::app()->user->id);

    return $criteria;
  }

  public function searchMyHierarchical($status = null) {
    $criteria = $this->getSearchMyHierarchicalCriteria($status);

    $sort = $this->searchSortDefault;

    return new EHierActiveDataProvider($this, array(
        'pagination' => array('pageSize' => Yii::app()->user->pageSize),
        'criteria' => $criteria,
        'sort' => $sort,
    ));
  }

  public function getSearchByProjectHierarchicalCriteria($projectId) {
    $criteria = $this->searchHierarchicalCriteria;
    $criteria->compare('t.par_project_id', $projectId, false);
    return $criteria;
  }

  public function searchByProjectHierarchical($projectId) {
    $criteria = $this->getSearchByProjectHierarchicalCriteria($projectId);

    $sort = $this->searchSortDefault;

    return new EHierActiveDataProvider($this, array(
        'pagination' => array('pageSize' => Yii::app()->user->pageSize),
        'criteria' => $criteria,
        'sort' => $sort,
    ));
  }

  private function hierMaps(&$rootFirstList) {
    $childrenList = array();
    foreach ($rootFirstList as $i => $task) {
      if (!empty($task->parent_id)) {
        !isset($childrenList[$task->parent_id]) &&
                $childrenList[$task->parent_id] = array();
        array_push($childrenList[$task->parent_id], $i);
      }
    }
    return $childrenList;
  }

  private function hierSort(&$rootFirstList) {
    $childrenList = $this->hierMaps($rootFirstList);
    $helper = array(); // tracks already processed records
    $resultList = array();

    $getChildren = function (&$task, $level) use (
            &$getChildren, // for recursion
            &$rootFirstList, &$childrenList, &$helper, &$resultList
            ) {
      $children = isset($childrenList[$task->id]) ? $childrenList[$task->id] : null;
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

    foreach ($rootFirstList as $i => $task) {
      if (!in_array($i, $helper)) {
        array_push($helper, $i);
        array_push($resultList, $task);
        $getChildren($task, 1);
      }
    }

    return $resultList;
  }

  public function findAllHierarchical($condition = '', $params = array()) {
    $inCriteria = $this->getCommandBuilder()->createCriteria($condition, $params);

    $rootCriteria = new CDbCriteria();
    $rootCriteria->order = 't.parent_id, t.id';
    if (!empty($inCriteria->order) && strpos($inCriteria->order, 'parent_id') === false) {
      $inCriteria->order = 't.parent_id, ' . $inCriteria->order;
      $rootCriteria->order = $inCriteria->order;
    }
    $rootCriteria->mergeWith($inCriteria);
    $rootFirstList = Task::model()->findAll($rootCriteria);

    return $this->hierSort($rootFirstList);
  }

}
