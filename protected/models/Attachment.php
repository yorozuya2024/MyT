<?php

/**
 * This is the model class for table "{{attachment}}".
 *
 * The followings are the available columns in table '{{attachment}}':
 * @property integer $id
 * @property string $name
 * @property string $type
 * @property string $uri
 * @property string $created
 * @property integer $task_id
 * @property integer $project_id
 * @property string $mega_id
 *
 * The followings are the available model relations:
 * @property Project $project
 * @property Task $task
 */
class Attachment extends CActiveRecord {

  public $link;
  public $file;
  public static $types = array('file' => 'File', 'link' => 'Link');

  /**
   * Returns the static model of the specified AR class.
   * @param string $className active record class name.
   * @return Attachment the static model class
   */
  public static function model($className = __CLASS__) {
    return parent::model($className);
  }

  /**
   * @return string the associated database table name
   */
  public function tableName() {
    return '{{attachment}}';
  }

  /**
   * @return array validation rules for model attributes.
   */
  public function rules() {
    $extList = isset(Yii::app()->params['attachments']) && isset(Yii::app()->params['attachments']['extList']) ? preg_replace('/\s+/', '', Yii::app()->params['attachments']['extList']) : null;
    // NOTE: you should only define rules for those attributes that
    // will receive user inputs.
    return array(
        array('name, type, uri, task_id', 'required'),
        array('task_id, project_id', 'numerical', 'integerOnly' => true),
        array('name', 'length', 'max' => 100),
        array('type', 'length', 'max' => 30),
        array('file', 'file',
            'types' => $extList === '' ? null : $extList,
            'allowEmpty' => true,
            'maxSize' => Yii::app()->params['attachments']['maxSize'] * 1024),
        array('link', 'url'),
        //array('uri', 'unique', 'message' => 'This file already exists.'),
        // The following rule is used by search().
        // Please remove those attributes that should not be searched.
        array('id, name, type, uri, task_id, project_id', 'safe', 'on' => 'search'),
    );
  }

  public function beforeSave() {
    if ($this->isNewRecord) {
      $this->created = new CDbExpression('NOW()');
    }
    return parent::beforeSave();
  }

  public function afterFind() {
    parent::afterFind();
    if ($this->type === 'file') {
      $this->file = basename($this->uri);
      $this->link = '';
    }
    if ($this->type === 'link') {
      $this->file = '';
      $this->link = $this->uri;
    }
  }

  /**
   * @return array relational rules.
   */
  public function relations() {
    // NOTE: you may need to adjust the relation name and the related
    // class name for the relations automatically generated below.
    return array(
        'project' => array(self::BELONGS_TO, 'Project', 'project_id'),
        'task' => array(self::BELONGS_TO, 'Task', 'task_id'),
    );
  }

  /**
   * @return array customized attribute labels (name=>label)
   */
  public function attributeLabels() {
    return array(
        'id' => Yii::t('attributes', 'Attachment.id'),
        'name' => Yii::t('attributes', 'Attachment.name'),
        'type' => Yii::t('attributes', 'Attachment.type'),
        'uri' => Yii::t('attributes', 'Attachment.uri'),
        'created' => Yii::t('attributes', 'Attachment.created'),
        'task_id' => Yii::t('attributes', 'Attachment.task_id'),
        'project_id' => Yii::t('attributes', 'Attachment.project_id'),
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

    $criteria->compare('id', $this->id);
    $criteria->compare('name', $this->name, true);
    $criteria->compare('type', $this->type, true);
    $criteria->compare('uri', $this->uri, true);
    $criteria->compare('created', $this->created, true);
    $criteria->compare('task_id', $this->task_id);
    $criteria->compare('project_id', $this->project_id);

    return new CActiveDataProvider($this, array(
        'criteria' => $criteria,
    ));
  }

  public function searchTask($task_id) {
    // Warning: Please modify the following code to remove attributes that
    // should not be searched.

    $criteria = new CDbCriteria;

    $criteria->compare('name', $this->name, true);
    $criteria->compare('type', $this->type, true);
    $criteria->compare('created', $this->created, true);
    $criteria->compare('task_id', $task_id);

    return new CActiveDataProvider($this, array(
        'criteria' => $criteria,
    ));
  }

}
