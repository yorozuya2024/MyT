<?php

/**
 * This is the model class for table "{{user_task}}".
 *
 * The followings are the available columns in table '{{user_task}}':
 * @property integer $id
 * @property string $created
 * @property string $last_upd
 * @property integer $user_id
 * @property integer $task_id
 *
 * The followings are the available model relations:
 * @property User $user
 * @property Task $task
 */
class UserTask extends CActiveRecord {

  /**
   * Returns the static model of the specified AR class.
   * @param string $className active record class name.
   * @return UserTask the static model class
   */
  public static function model($className = __CLASS__) {
    return parent::model($className);
  }

  /**
   * @return string the associated database table name
   */
  public function tableName() {
    return '{{user_task}}';
  }

  /**
   * @return array validation rules for model attributes.
   */
  public function rules() {
    // NOTE: you should only define rules for those attributes that
    // will receive user inputs.
    return array(
        array('created, last_upd, user_id, task_id', 'required'),
        array('user_id, task_id', 'numerical', 'integerOnly' => true),
        // The following rule is used by search().
        // Please remove those attributes that should not be searched.
        array('id, created, last_upd, user_id, task_id', 'safe', 'on' => 'search'),
    );
  }

  /**
   * @return array relational rules.
   */
  public function relations() {
    // NOTE: you may need to adjust the relation name and the related
    // class name for the relations automatically generated below.
    return array(
        'user' => array(self::BELONGS_TO, 'User', 'user_id'),
        'task' => array(self::BELONGS_TO, 'Task', 'task_id'),
    );
  }

  /**
   * @return array customized attribute labels (name=>label)
   */
  public function attributeLabels() {
    return array(
        'id' => Yii::t('attributes', 'UserTask.id'),
        'created' => Yii::t('attributes', 'UserTask.created'),
        'last_upd' => Yii::t('attributes', 'UserTask.last_upd'),
        'user_id' => Yii::t('attributes', 'UserTask.user_id'),
        'task_id' => Yii::t('attributes', 'UserTask.task_id'),
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
    $criteria->compare('created', $this->created, true);
    $criteria->compare('last_upd', $this->last_upd, true);
    $criteria->compare('user_id', $this->user_id);
    $criteria->compare('task_id', $this->task_id);

    return new CActiveDataProvider($this, array(
        'criteria' => $criteria,
    ));
  }

  /**
   * Retrieves a list of models based on the current search/filter conditions.
   * @param integer $taskId
   * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
   */
  public function searchByTask($taskId) {
    $criteria = new CDbCriteria;
    $criteria->compare('task_id', $taskId, false);
    $criteria->compare('user_id', $this->user_id);

    return new CActiveDataProvider($this, array(
        'criteria' => $criteria,
    ));
  }

  public static function associate($taskId, $userId) {
    $u = new UserTask;
    $u->created = new CDbExpression('NOW()');
    $u->last_upd = new CDbExpression('NOW()');
    $u->task_id = $taskId;
    $u->user_id = $userId;
    return $u->save();
  }

  public static function merge($taskId, array $userIds) {
    $newUsers = array();

    $assocRows = self::model()->findAllByAttributes(array('task_id' => $taskId));
    if ($assocRows === null)
      $assocRows = array();
    $assocUserIds = array();
    foreach ($assocRows as $row) {
      array_push($assocUserIds, $row->user_id);
    }

    foreach ($userIds as $userId) {
      if (!in_array($userId, $assocUserIds) && self::associate($taskId, $userId)) {
        array_push($newUsers, $userId);
      }
    }
    foreach ($assocUserIds as $assocUserId) {
      if (!in_array($assocUserId, $userIds)) {
        self::model()->findByAttributes(array('task_id' => $taskId, 'user_id' => $assocUserId))->delete();
      }
    }

    return $newUsers;
  }

}
