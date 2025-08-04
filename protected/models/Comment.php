<?php

/**
 * This is the model class for table "{{comment}}".
 *
 * The followings are the available columns in table '{{comment}}':
 * @property integer $id
 * @property string $created
 * @property integer $created_by
 * @property string $last_upd
 * @property integer $last_upd_by
 * @property string $entity
 * @property string $body
 * @property integer $stauts
 */
class Comment extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{comment}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('entity, entity_id, body', 'required'),
			array('status', 'default', 'value' => true),
			array('status', 'boolean'),
			array('entity', 'length', 'max'=>255),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, created, created_by, last_upd, last_upd_by, entity, entity_id, body, status', 'safe', 'on'=>'search'),
		);
	}
	
	public function behaviors() {
		return array(
			'timestamp' => array(
				'class' => 'application.behaviors.ETimestampBehavior',
			)
		);
	  }

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'creator' => array(self::BELONGS_TO, 'User', 'created_by'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'created' => 'Created',
			'created_by' => 'Created By',
			'last_upd' => 'Last Upd',
			'last_upd_by' => 'Last Upd By',
			'entity' => 'Entity',
			'entity_id' => 'Entity ID',
			'body' => 'Body',
			'stauts' => 'Stauts',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('created',$this->created,true);
		$criteria->compare('created_by',$this->created_by);
		$criteria->compare('last_upd',$this->last_upd,true);
		$criteria->compare('last_upd_by',$this->last_upd_by);
		$criteria->compare('entity',$this->entity,true);
		$criteria->compare('entity_id',$this->entity_id);
		$criteria->compare('body',$this->body,true);
		$criteria->compare('status',$this->status);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'sort' => array('defaultOrder' => 'created DESC'),
			'pagination'=>false,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Comment the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
