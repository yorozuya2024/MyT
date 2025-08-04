<?php

/**
 * This is the model class for table "android_udid".
 *
 * The followings are the available columns in table 'android_udid':
 * @property string $user_name
 * @property string $registration_id
 */
class AndroidUdid extends CActiveRecord {

  /**
   * Returns the static model of the specified AR class.
   * @param string $className active record class name.
   * @return AndroidUdid the static model class
   */
  public static function model($className = __CLASS__) {
    return parent::model($className);
  }

  /**
   * @return string the associated database table name
   */
  public function tableName() {
    return '{{android_udid}}';
  }

  /**
   * @return array validation rules for model attributes.
   */
  public function rules() {
    // NOTE: you should only define rules for those attributes that
    // will receive user inputs.
    return array(
        array('user_name, registration_id', 'required'),
        array('user_name, registration_id', 'length', 'max' => 255),
        // The following rule is used by search().
        // Please remove those attributes that should not be searched.
        array('user_name, registration_id', 'safe', 'on' => 'search'),
    );
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
        'user_name' => Yii::t('attributes', 'Android.user_name'),
        'registration_id' => Yii::t('attributes', 'Android.registration_id'),
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

    $criteria->compare('user_name', $this->user_name, true);
    $criteria->compare('registration_id', $this->registration_id, true);

    return new CActiveDataProvider($this, array(
        'criteria' => $criteria,
    ));
  }

}
