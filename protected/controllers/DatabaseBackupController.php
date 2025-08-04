<?php

class DatabaseBackupController extends Controller {

  public function actionCreate() {
    $backup = new DatabaseBackup;

    if ($backup->createBackup()) {
      Yii::app()->user->setFlash('success', Yii::t('app', 'Backup.create.success'));
    } else {
      Yii::app()->user->setFlash('error', $backup->error);
    }

    $this->redirect(array('index'));
  }

  public function actionDelete($filename) {
    $backup = new DatabaseBackup;

    if ($backup->deleteFile($filename)) {
      Yii::app()->user->setFlash('success', Yii::t('app', 'Backup.delete.success.{filename}', array('{filename}' => $filename)));
    } else {
      Yii::app()->user->setFlash('error', Yii::t('app', 'Backup.delete.failure.{filename}', array('{filename}' => $filename)));
    }

    $this->redirect(array('index'));
  }

  public function actionDownload($filename) {
    $backup = new DatabaseBackup;

    if ($backup->downloadBackup($filename) === false) {
      Yii::app()->user->setFlash('error', $backup->error);
      $this->redirect(array('index'));
    }
  }

  public function actionIndex() {
    $backup = new DatabaseBackup;

    if (!empty($backup->error))
      Yii::app()->user->setFlash('error', $backup->error);

    $this->render('index', array('backups' => $backup->getBackupDataProvider()));
  }

}
