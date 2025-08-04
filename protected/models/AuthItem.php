<?php

/**
 * This is the model class for table "{{auth_item}}".
 *
 * The followings are the available columns in table '{{auth_item}}':
 * @property string $name
 * @property integer $type
 * @property string $description
 * @property string $bizrule
 * @property string $data
 *
 * The followings are the available model relations:
 * @property AuthAssignment[] $authAssignments
 * @property AuthItemChild[] $authItemParent
 * @property AuthItemChild[] $authItemChild
 */
class AuthItem extends CActiveRecord {

  /**
   * Returns the static model of the specified AR class.
   * @param string $className active record class name.
   * @return AuthItem the static model class
   */
  public static function model($className = __CLASS__) {
    return parent::model($className);
  }

  /**
   * @return string the associated database table name
   */
  public function tableName() {
    return '{{auth_item}}';
  }

  /**
   * @return array validation rules for model attributes.
   */
  public function rules() {
    // NOTE: you should only define rules for those attributes that
    // will receive user inputs.
    return array(
        array('name, type', 'required'),
        array('type', 'numerical', 'integerOnly' => true),
        array('name', 'length', 'max' => 64),
        array('description, bizrule, data', 'safe'),
        // The following rule is used by search().
        // Please remove those attributes that should not be searched.
        array('name, type, description, bizrule, data', 'safe', 'on' => 'search, searchOperation'),
    );
  }

  /**
   * @link http://www.yiiframework.com/doc/guide/1.1/en/database.arr#statistical-query
   * @return array relational rules.
   */
  public function relations() {
    // NOTE: you may need to adjust the relation name and the related
    // class name for the relations automatically generated below.
    return array(
        'authAssignmentsCount' => array(self::STAT, 'AuthAssignment', 'itemname'),
        'authAssignments' => array(self::HAS_MANY, 'AuthAssignment', 'itemname'),
        'authItemParent' => array(self::HAS_MANY, 'AuthItemChild', 'parent'),
        'authItemChild' => array(self::HAS_MANY, 'AuthItemChild', 'child'),
        'authItemChildren' => array(self::MANY_MANY, 'AuthItem', 'AuthItemChild(parent,child)'),
    );
  }

  /**
   * @return array customized attribute labels (name=>label)
   */
  public function attributeLabels() {
    return array(
        'name' => Yii::t('attributes', 'AuthItem.name'),
        'type' => Yii::t('attributes', 'AuthItem.type'),
        'description' => Yii::t('attributes', 'AuthItem.description'),
        'bizrule' => Yii::t('attributes', 'AuthItem.bizrule'),
        'data' => Yii::t('attributes', 'AuthItem.data'),
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

    $criteria->compare('name', $this->name, true);
    $criteria->compare('type', $this->type);
    $criteria->compare('description', $this->description, true);
    $criteria->compare('bizrule', $this->bizrule, true);
    $criteria->compare('data', $this->data, true);

    return new CActiveDataProvider($this, array(
        'pagination' => array('pageSize' => Yii::app()->user->pageSize),
        'criteria' => $criteria,
    ));
  }

  /**
   * Retrieves a list of models based on the current search/filter conditions.
   * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
   */
  public function searchOperation() {
    $criteria = new CDbCriteria;

    $criteria->compare('name', $this->name, true);
    $criteria->compare('type', CAuthItem::TYPE_OPERATION);
    $criteria->compare('description', $this->description, true);

    $criteria->order = 'description';

    return new CActiveDataProvider($this, array(
        'criteria' => $criteria,
        'pagination' => array('pageSize' => 100),
    ));
  }

  /**
   * Retrieves a list of models based on the current search/filter conditions.
   * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
   */
  public function searchRole() {
    $criteria = new CDbCriteria;

    $criteria->compare('name', $this->name, true);
    $criteria->compare('type', CAuthItem::TYPE_ROLE);
    $criteria->compare('description', $this->description, true);

    return new CActiveDataProvider($this, array(
        'pagination' => array('pageSize' => Yii::app()->user->pageSize),
        'criteria' => $criteria,
    ));
  }

  /**
   * Retrieves Auth Item Types
   * @return array Auth Item Types
   */
  public static function getTypes() {
    return array('Operation', 'Task', 'Role');
  }

  public function getType() {
    $types = AuthItem::getTypes();
    return $types[$this->type];
  }

  public static function getOperations() {
    $ops = array();
    $ops_row = Yii::app()->authManager->getOperations();
    foreach ($ops_row as $row)
      $ops[] = $row->name;
    return $ops;
  }

  public static function getRoles() {
    $roles = array();
    $roles_row = Yii::app()->authManager->getRoles();
    foreach ($roles_row as $row)
      $roles[] = $row->name;
    return $roles;
  }

  /**
   * Wraps out the Auth Item.
   * @return CAuthItem The Auth Item with Model Name.
   */
  public function getItem() {
    return Yii::app()->authManager->getAuthItem($this->name);
  }

}
