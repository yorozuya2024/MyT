<?php
/* @var $this ChargeController */
/* @var $model Charge */
/* @var $records Charge[] */
/* @var $projects Project[] */
/* @var $date string */
/* @var $isManage bool */
/* @var $userId integer */
/* @var $prjId integer */
/* @var $queryMonth date */

$userName = $userId ? User::model()->findByPk($userId)->username : '';

if ($isManage) {
  if (Navigator::getChargeType() == 'all') {
    $this->breadcrumbs = array(
        Yii::t('nav', 'Manage All Charges') => array('adminAll', 'trkq' => 1),
        $userName,
    );
  } else {
    $this->breadcrumbs = array(
        Yii::t('nav', 'Manage Charges') => array('admin', 'trkq' => 1),
        $userName,
    );
  }
} else {
  $this->breadcrumbs = array(
      Yii::t('nav', 'Create Charge'),
  );
}

$this->menu = array(
    array('label' => 'List Charge', 'url' => array('index')),
    array('label' => 'Manage Charge', 'url' => array('admin')),
);
?>

<?php if ($isManage): ?>
  <h1><?php echo Yii::t('nav', 'Manage Charges for <em>{name}</em>', array('{name}' => $userName)); ?></h1>
<?php else: ?>
  <h1><?php echo Yii::t('nav', 'Create Charge'); ?></h1>
<?php endif; ?>

<?php
echo $this->renderPartial('_form', array(
    'model' => $model,
    'projects' => $projects,
    'records' => $records,
    'errors' => $errors,
    'date' => $date,
    'userId' => $userId,
    'prjId' => $prjId,
        )
);
