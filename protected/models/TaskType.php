<?php

/**
 * This is the model class for table "{{task_type}}".
 *
 * The followings are the available columns in table '{{task_type}}':
 * @property integer $id
 * @property string $name
 * @property integer $order_by
 * @property boolean $default_flg
 * @property boolean $active_flg
 */
class TaskType extends CActiveRecord {

  /**
   * Returns the static model of the specified AR class.
   * @param string $className active record class name.
   * @return TaskType the static model class
   */
  public static function model($className = __CLASS__) {
    return parent::model($className);
  }

  /**
   * @return string the associated database table name
   */
  public function tableName() {
    return '{{task_type}}';
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
        array('order_by', 'default', 'value' => 0),
        array('order_by', 'numerical', 'integerOnly' => true, 'min' => 0),
        array('active_flg', 'default', 'value' => true),
        array('default_flg', 'default', 'value' => false),
        array('active_flg, default_flg', 'boolean'),
        array('default_flg', 'checkDefault'),
        // The following rule is used by search().
        // Please remove those attributes that should not be searched.
        array('id, name, order_by, active_flg', 'safe', 'on' => 'search'),
    );
  }

  public function checkDefault($attribute, $params) {
    $value = CPropertyValue::ensureBoolean($this->$attribute);
    $current = TaskType::model()->default()->find();
    if ($value) {
      if ($current !== null && $current->id != $this->id) {
        $current->default_flg = false;
        $current->save(false, array('default_flg'));
      }
    } else {
      if ($current === null || $current->id == $this->id)
        $this->addError($attribute, Yii::t('app', 'TaskType.default.error'));
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
        'id' => Yii::t('attributes', 'TaskType.id'),
        'name' => Yii::t('attributes', 'TaskType.name'),
        'order_by' => Yii::t('attributes', 'TaskType.order_by'),
        'default_flg' => Yii::t('attributes', 'TaskType.default_flg'),
        'active_flg' => Yii::t('attributes', 'TaskType.active_flg'),
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
    $criteria->compare('order_by', $this->order_by, false);
    $criteria->compare('default_flg', $this->default_flg, false);
    $criteria->compare('active_flg', $this->active_flg, false);

    return new CActiveDataProvider($this, array(
        'criteria' => $criteria,
    ));
  }

  public static function getTypeId($typeName) {
    return TaskType::model()->findByAttributes(array('name' => $typeName))->id;
  }

  public static function getTypeName($typeId) {
    return TaskType::model()->findByPk($typeId)->name;
  }

}
