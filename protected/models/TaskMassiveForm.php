<?php

/**
 * This is the model class for table "pm_task".
 *
 * The followings are the available columns in table 'pm_task':
 * @property string $ids
 * @property string $type
 * @property string $priority
 * @property string $status
 * @property string $owner
 * @property string $par_project_id
 * @property string $start_date
 * @property string $end_date
 */
class TaskMassiveForm extends CFormModel {

  public $ids;
  public $type;
  public $priority;
  public $status;
  public $owner;
  public $par_project_id;
  public $start_date;
  public $end_date;

  /**
   * @return array validation rules for model attributes.
   */
  public function rules() {
    // NOTE: you should only define rules for those attributes that
    // will receive user inputs.
    return array(
        array('start_date, end_date', 'safe'),
        array('start_date, end_date', 'date', 'format' => 'yyyy-MM-dd', 'allowEmpty' => true),
        array('end_date', 'compare', 'compareAttribute' => 'start_date', 'operator' => '>=', 'allowEmpty' => true),
    );
  }

  /**
   * Declares attribute labels.
   */
  public function attributeLabels() {
    return array(
        'ids' => Yii::t('attributes', 'TaskMassiveForm.ids'),
        'type' => Yii::t('attributes', 'TaskMassiveForm.type'),
        'priority' => Yii::t('attributes', 'TaskMassiveForm.priority'),
        'status' => Yii::t('attributes', 'TaskMassiveForm.status'),
        'owner' => Yii::t('attributes', 'TaskMassiveForm.owner'),
        'par_project_id' => Yii::t('attributes', 'TaskMassiveForm.par_project_id'),
        'start_date' => Yii::t('attributes', 'TaskMassiveForm.start_date'),
        'end_date' => Yii::t('attributes', 'TaskMassiveForm.end_date'),
    );
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

}
