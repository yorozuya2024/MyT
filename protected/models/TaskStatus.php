<?php

/**
 * This is the model class for table "{{task_status}}".
 *
 * The followings are the available columns in table '{{task_status}}':
 * @property integer $id
 * @property string $name
 * @property integer $order_by
 * @property integer $group_id
 * @property boolean $default_flg
 * @property boolean $active_flg
 */
class TaskStatus extends CActiveRecord {

  /**
   * Returns the static model of the specified AR class.
   * @param string $className active record class name.
   * @return TaskStatus the static model class
   */
  public static function model($className = __CLASS__) {
    return parent::model($className);
  }

  /**
   * @return string the associated database table name
   */
  public function tableName() {
    return '{{task_status}}';
  }

  /**
   * @return array validation rules for model attributes.
   */
  public function rules() {
    // NOTE: you should only define rules for those attributes that
    // will receive user inputs.
    return array(
        array('name', 'required'),
        array('name', 'length', 'max' => 30),
        array('group_id', 'default', 'value' => 0),
        array('group_id', 'numerical', 'integerOnly' => true, 'min' => 0),
        array('order_by', 'default', 'value' => 0),
        array('order_by', 'numerical', 'integerOnly' => true, 'min' => 0),
        array('active_flg', 'default', 'value' => true),
        array('default_flg', 'default', 'value' => false),
        array('active_flg, default_flg', 'boolean'),
        array('default_flg', 'checkDefault'),
        // The following rule is used by search().
        // Please remove those attributes that should not be searched.
        array('id, name, group_id, order_by, active_flg', 'safe', 'on' => 'search'),
    );
  }

  public function checkDefault($attribute, $params) {
    $value = CPropertyValue::ensureBoolean($this->$attribute);
    $current = TaskStatus::model()->default()->find();
    if ($value) {
      if ($current !== null && $current->id != $this->id) {
        $current->default_flg = false;
        $current->save(false, array('default_flg'));
      }
    } else {
      if ($current === null || $current->id == $this->id)
        $this->addError($attribute, Yii::t('app', 'TaskStatus.default.error'));
    }
  }

  /**
   * @return array relational rules.
   */
  public function relations() {
    // NOTE: you may need to adjust the relation name and the related
    // class name for the relations automatically generated below.
    return array(
    );
  }

  /**
   * @return array customized attribute labels (name=>label)
   */
  public function attributeLabels() {
    return array(
        'id' => Yii::t('attributes', 'TaskStatus.id'),
        'name' => Yii::t('attributes', 'TaskStatus.name'),
        'group_id' => Yii::t('attributes', 'TaskStatus.group_id'),
        'order_by' => Yii::t('attributes', 'TaskStatus.order_by'),
        'default_flg' => Yii::t('attributes', 'TaskStatus.default_flg'),
        'active_flg' => Yii::t('attributes', 'TaskStatus.active_flg'),
    );
  }

  public function scopes() {
    return array(
        'active' => array(
            'condition' => 'active_flg = 1',
            'order' => 'order_by'
        ),
        'default' => array(
            'condition' => 'default_flg = 1',
        ),
        'open' => array(
            'condition' => 'group_id = ' . self::getGroupId(Yii::t('constants', 'TaskStatus.group.open')),
            'order' => 'order_by'
        ),
        'closed' => array(
            'condition' => 'group_id = ' . self::getGroupId(Yii::t('constants', 'TaskStatus.group.closed')),
            'order' => 'order_by'
        ),
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

    $criteria->compare('id', $this->id, false);
    $criteria->compare('name', $this->name, true);
    $criteria->compare('group_id', $this->group_id, false);
    $criteria->compare('order_by', $this->order_by, false);
    $criteria->compare('default_flg', $this->default_flg, false);
    $criteria->compare('active_flg', $this->active_flg, false);

    return new CActiveDataProvider($this, array(
        'criteria' => $criteria,
    ));
  }

  public static function getStatusIdByGroup($groupName) {
    $groupId = array_search($groupName, self::getGroupList());
    if ($groupId === false)
      throw new DomainException(Yii::t('app', 'TaskStatus.group.invalid'));
    $out = null;
    $status = TaskStatus::model()->findAllByAttributes(array('group_id' => $groupId));
    if ($status === null)
      $out = -1;
    elseif (count($status) === 1)
      $out = (int) $status->id;
    else
      $out = array_map(function($element) {
        return (int) $element['id'];
      }, $status);
    return $out;
  }

  public static function getStatusId($statusName) {
    return TaskStatus::model()->findByAttributes(array('name' => $statusName))->id;
  }

  public static function getStatusName($statusId) {
    return TaskStatus::model()->findByPk($statusId)->name;
  }

  public static function getGroupList() {
    return array(Yii::t('constants', 'TaskStatus.group.open'),
        Yii::t('constants', 'TaskStatus.group.closed'),
        Yii::t('constants', 'TaskStatus.group.onHold'));
  }

  public static function getGroupId($groupName) {
    return array_search($groupName, self::getGroupList());
  }

  public function getGroup() {
    $list = self::getGroupList();
    return $list[$this->group_id];
  }

  public function getClosedStatusList() {
    $list = array();
    foreach (TaskStatus::model()->active()->closed()->findAll(array('select' => 'id')) as $status) {
      array_push($list, $status->id);
    }
    return $list;
  }

  public function getOpenStatusList() {
    $list = array();
    foreach (TaskStatus::model()->active()->open()->findAll(array('select' => 'id')) as $status) {
      array_push($list, $status->id);
    }
    return $list;
  }

}
