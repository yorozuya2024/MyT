<?php

/**
 * Based on CTimestampBehavior class.
 *
 * @author Jonah Turnquist <poppitypop@gmail.com>
 * @link http://www.yiiframework.com/
 * @copyright 2008-2013 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

/**
 * ETimestampBehavior manages 'created_by' and 'updated_by' attributes.
 *
 * @author Francesco Colamonici <f.colamonici@gmail.com>
 */
class ETimestampBehavior extends CActiveRecordBehavior {

    /**
     * The type of the property to trace the upsert action time.
     * Defaults to 'datetime'.
     * @see http://www.yiiframework.com/doc/api/1.1/CDbSchema#getColumnType-detail
     * @var string
     */
    public $upsertAttributeType = 'datetime';

    /**
     * The name of the attribute to store the creation time.
     * Set to null to not use a timestamp for the creation attribute.
     * Defaults to 'created'.
     * @var string
     */
    public $createAttribute = 'created';

    /**
     * The name of the attribute to store the creation author.
     * Set to null to not use a User property for the creation attribute.
     * Defaults to 'created_by'.
     * @var string
     */
    public $createUserAttribute = 'created_by';

    /**
     * The name of the attribute to store the modification time.
     * Set to null to not use a timestamp for the update attribute.
     * Defaults to 'last_upd'.
     * @var string
     */
    public $updateAttribute = 'last_upd';

    /**
     * The name of the attribute to store the modification author.
     * Set to null to not use a User property for the update attribute.
     * Defaults to 'last_upd_by'.
     * @var string
     */
    public $updateUserAttribute = 'last_upd_by';

    /**
     * The name of the User property to trace the upsert action author.
     * Set to null to not save this information.
     * @var string
     */
    public $userAttribute = 'id';

    /**
     * The type of the User property to trace the upsert action author.
     * @see http://www.yiiframework.com/doc/api/1.1/CDbSchema#getColumnType-detail
     * @var string
     */
    public $userAttributeType = 'integer';

    /**
     * Whether to set the update attribute to the creation timestamp upon creation.
     * Otherwise it will be left alone.  Defaults to true.
     * @var bool
     */
    public $setUpdateOnCreate = true;

    /**
     * Whether to check the Table Schema for the attributes to set.
     * @var bool
     */
    public $checkAttributes = false;

    /**
     * The expression that will be used for generating the timestamp.
     * This can be either a string representing a PHP expression (e.g. 'time()'),
     * or a {@link CDbExpression} object representing a DB expression (e.g. new CDbExpression('NOW()')).
     * Defaults to null, meaning that we will attempt to figure out the appropriate timestamp
     * automatically. If we fail at finding the appropriate timestamp, then it will
     * fall back to using the current UNIX timestamp.
     *
     * A PHP expression can be any PHP code that has a value. To learn more about what an expression is,
     * please refer to the {@link http://www.php.net/manual/en/language.expressions.php php manual}.
     *
     * @var string
     */
    public $timestampExpression;

    /**
     * Maps column types to database method.
     * @var array
     */
    protected static $map = array(
        'datetime' => 'NOW()',
        'timestamp' => 'NOW()',
        'date' => 'NOW()',
    );

    /**
     * Last inserted Column
     * @var string
     */
    private $lastColumn = 'id';

    /**
     * Responds to {@link CModel::onBeforeSave} event.
     * Sets the values of the creation or modified attributes as configured
     *
     * @param CModelEvent $event event parameter
     */
    public function beforeSave($event) {
        $model = $this->getOwner();

        if ($model->getIsNewRecord() && $this->createAttribute !== null) {
            $model->{$this->createAttribute} = $this->getTimestampByAttribute($this->createAttribute);
            if ($this->createUserAttribute !== null &&  $this->userAttribute !== null) 
			{
                $model->{$this->createUserAttribute} = Yii::app()->user->isGuest ? 0 : Yii::app()->user->{$this->userAttribute};
            }
        }

        if ((!$model->getIsNewRecord() || $this->setUpdateOnCreate) && $this->updateAttribute !== null) {
            $model->{$this->updateAttribute} = $this->getTimestampByAttribute($this->updateAttribute);
            if ($this->updateUserAttribute !== null && $this->userAttribute !== null)
			{
                $model->{$this->updateUserAttribute} = Yii::app()->user->isGuest ? 0 : Yii::app()->user->{$this->userAttribute};
            }
        }
    }

    /**
     * Gets the appropriate timestamp depending on the column type $attribute is
     *
     * @param string $attribute Attribute
     * @return mixed timestamp (eg unix timestamp or a mysql function)
     */
    protected function getTimestampByAttribute($attribute) {
        if ($this->timestampExpression instanceof CDbExpression)
            return $this->timestampExpression;
        elseif ($this->timestampExpression !== null)
            return @eval('return ' . $this->timestampExpression . ';');

        $columnType = $this->getOwner()->getTableSchema()->getColumn($attribute)->dbType;
        return $this->getTimestampByColumnType($columnType);
    }

    /**
     * Returns the appropriate timestamp depending on $columnType
     *
     * @param string $columnType Column Type
     * @return mixed timestamp (eg unix timestamp or a mysql function)
     */
    protected function getTimestampByColumnType($columnType) {
        return isset(self::$map[$columnType]) ? date("Y-m-d H:i:s", time()) : time();
    }

    public function afterConstruct($event) {
        if ($this->checkAttributes)
            $this->checkUpsertSchemaAttributes();
    }

    protected function checkUpsertSchemaAttributes() {
        $model = $this->getOwner();
        $table = $model->getTableSchema();

        if ($this->createAttribute !== null && $table->getColumn($this->createAttribute) === null) {
            $model->getDbConnection()->createCommand()->addColumn(
                    $table->name, $this->createAttribute, $this->upsertAttributeType . ' NOT NULL AFTER ' . $this->lastColumn
            );
            $this->lastColumn = $this->createAttribute;
        }

        if ($this->updateAttribute !== null && $table->getColumn($this->updateAttribute) === null) {
            $type = $this->upsertAttributeType . ($this->setUpdateOnCreate ? ' NOT NULL' : '') . ' AFTER ' . $this->lastColumn;
            $model->getDbConnection()->createCommand()->addColumn(
                    $table->name, $this->updateAttribute, $type
            );
            $this->lastColumn = $this->updateAttribute;
        }

        $this->checkUpsertUserSchemaAttributes();
    }

    protected function checkUpsertUserSchemaAttributes() {
        $model = $this->getOwner();
        $table = $model->getTableSchema();

        if ($this->createUserAttribute !== null && $table->getColumn($this->createUserAttribute) === null) {
            $type = $this->userAttributeType . ' NOT NULL' . ' AFTER ' . $this->lastColumn;
            $model->getDbConnection()->createCommand()->addColumn(
                    $table->name, $this->createUserAttribute, $type
            );
            $model->getDbConnection()->createCommand()->createIndex(
                    'author', $table->name, $this->createUserAttribute
            );
            $this->lastColumn = $this->createUserAttribute;
        }

        if ($this->updateUserAttribute !== null && $table->getColumn($this->updateUserAttribute) === null) {
            $type = $this->userAttributeType . ($this->setUpdateOnCreate ? ' NOT NULL' : '') . ' AFTER ' . $this->lastColumn;
            $model->getDbConnection()->createCommand()->addColumn(
                    $table->name, $this->updateUserAttribute, $type
            );
            $this->lastColumn = $this->updateUserAttribute;
        }
    }

}
