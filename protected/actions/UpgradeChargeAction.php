<?php

/**
 * Description of UpgradeChargeAction
 *
 * @author francesco.colamonici
 */
class UpgradeChargeAction extends CAction {

    public $errors = array();

    public function run() {
        $migrated = 0;
        if (Yii::app()->getDb()->getSchema()->getTable('pm_charge_old') === null)
            $this->backupOldTable();
        if (Yii::app()->getDb()->getSchema()->getTable('pm_charge') === null)
            $this->generateNewTable();

        if (empty($this->errors)) {
            $oldCharges = ChargeOld::model()->findAllByAttributes(array('migrated_flg' => false));
            foreach ($oldCharges as $oldCharge) {
                $flag = true;
                $oldChargeData = unserialize($oldCharge->charge_data);
                foreach ($oldChargeData as $day => $hours) {
                    $hours = CPropertyValue::ensureFloat($hours);
                    if ($hours <= 0.0)
                        continue;

                    $newCharge = new Charge;
                    $newCharge->user_id = $oldCharge->user_id;
                    $newCharge->project_id = $oldCharge->project_id;
                    $newCharge->task_id = $oldCharge->task_id;
                    $newCharge->day = substr($oldCharge->month, 0, 8) . str_pad($day, 2, '0', STR_PAD_LEFT);
                    $newCharge->hours = $hours;
                    if ($newCharge->validate() && $newCharge->save())
                        $flag = $flag && true;
                    else {
                        array_push($this->errors, $newCharge);
                        $flag = $flag && false;
                    }
                }
                if ($flag)
                    $migrated++;
                $oldCharge->migrated_flg = $flag;
                $oldCharge->save();
            }
        }

        $this->controller->render('output',
                array(
            'upgrade' => $this->controller->getUpgrade('Charge', 'charge'),
            'errors' => $this->errors,
            'success' => $migrated . ' Charges have been migrated successfully.'
        ));
    }

    private function backupOldTable() {
        $transaction = Yii::app()->getDb()->beginTransaction();
        try {
            Yii::app()->getDb()->createCommand()->renameTable('pm_charge', 'pm_charge_old');
            Yii::app()->getDb()->createCommand()->addColumn('pm_charge_old', 'migrated_flg', 'boolean DEFAULT 0');
            $transaction->commit();
            return true;
        } catch (Exception $ex) {
            $transaction->rollback();
            Yii::log($ex->getTraceAsString(), CLogger::LEVEL_ERROR);
            array_push($this->errors, $ex->getTraceAsString());
            return false;
        }
    }

    /**
     * @see http://www.yiiframework.com/doc/api/1.1/CDbSchema#getColumnType-detail
     */
    private function generateNewTable() {
        $transaction = Yii::app()->getDb()->beginTransaction();
        try {
            Yii::app()->getDb()->createCommand()->createTable(
                    'pm_charge',
                    array(
                'id' => 'pk',
                'created' => 'datetime NOT NULL',
                'created_by' => 'integer NOT NULL',
                'last_upd' => 'datetime NOT NULL',
                'last_upd_by' => 'integer NOT NULL',
                'user_id' => 'integer NOT NULL',
                'project_id' => 'integer NOT NULL',
                'task_id' => 'integer',
                'day' => 'date NOT NULL',
                'hours' => 'float NOT NULL'
                    )
            );
            Yii::app()->getDb()->createCommand()->createIndex(
                    'created_by', 'pm_charge', 'created_by'
            );
            Yii::app()->getDb()->createCommand()->createIndex(
                    'last_upd_by', 'pm_charge', 'last_upd_by'
            );
            Yii::app()->getDb()->createCommand()->createIndex(
                    'user_id', 'pm_charge', 'user_id'
            );
            Yii::app()->getDb()->createCommand()->createIndex(
                    'project_id', 'pm_charge', 'project_id'
            );
            Yii::app()->getDb()->createCommand()->createIndex(
                    'task_id', 'pm_charge', 'task_id'
            );
            $transaction->commit();
            return true;
        } catch (Exception $ex) {
            $transaction->rollback();
            Yii::log($ex->getTraceAsString(), CLogger::LEVEL_ERROR);
            array_push($this->errors, $ex->getTraceAsString());
            return false;
        }
    }

}
