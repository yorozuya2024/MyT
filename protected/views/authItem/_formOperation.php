<?php
/* @var $this AuthItemController */
/* @var $model AuthItem */
/* @var $form CActiveForm */

$ops = array();
$ops_row = Yii::app()->authManager->getOperations();
foreach ($ops_row as $row)
  $ops[$row->name] = $row->description;

asort($ops);

$assoc_ops = array();
$assoc_rows = AuthItemChild::model()->findAllByAttributes(array('parent' => $model->name));
foreach ($assoc_rows as $row)
  array_push($assoc_ops, $row->child);

foreach ($ops as $op => $desc) {
  ?>
  <div class="row clear">
    <span class="span-6"><?php echo CHtml::label($desc, 'AuthItemChild_' . preg_replace('/\s+/', '_', $op)); ?></span>
    <span class="span-1"><?php echo CHtml::checkBox('AuthItemChild[' . $op . ']', in_array($op, $assoc_ops)); ?></span>
  </div>
  <?php
}
