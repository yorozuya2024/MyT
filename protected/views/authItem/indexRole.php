<?php
/* @var $this AuthItemController */
/* @var $model AuthItem */

$this->breadcrumbs = array(
    Yii::t('nav', 'Roles'),
);

$this->menu = array(
    array('label' => 'List AuthItem', 'url' => array('index')),
    array('label' => 'Create AuthItem', 'url' => array('create')),
);
?>

<h2><?php
  echo Yii::t('nav', 'Roles');

  $this->widget('ActionsWidget', array(
      'data' => $model,
      'createButtonVisible' => 'true',
      'updateButtonVisible' => 'false',
      'deleteButtonVisible' => 'false',
  ));
  ?>
</h2>

<?php
$ops = Yii::app()->authManager->getOperations();
$this->widget('zii.widgets.grid.CGridView', array(
    'id' => 'auth-item-grid',
    'dataProvider' => $model->searchRole(),
    'filter' => $model,
    'columns' => array(
        array(
            'name' => 'name',
            'type' => 'html',
            'value' => function($data) {
              return CHtml::tag('p', array(), $data->name) . CHtml::tag('small', array(), Yii::t('app', 'Users') . ': ' . $data->authAssignmentsCount);
            }
                ),
                'description:html',
                array(
                    'type' => 'html',
                    'header' => Yii::t('app', 'Operations'),
                    'filter' => false,
                    'sortable' => false,
                    'value' => function($data) use($ops) {
                      $out = array();
                      $assoc_ops = array();
                      $assoc_rows = AuthItemChild::model()->findAllByAttributes(array('parent' => $data->name));
                      foreach ($assoc_rows as $row)
                        $assoc_ops[] = $row->child;
                      foreach ($ops as $op)
                        $out[] = $op->description . ': ' . (in_array($op->name, $assoc_ops) ? Yii::t('app', 'Yes') : Yii::t('app', 'No'));
                      sort($out, SORT_STRING);
                      return nl2br(implode(PHP_EOL, $out));
                    }
                        ),
                        array(
                            'class' => 'ext.myGridView.MyButtonColumn',
                            'template' => '{update} {delete}',
                        )
                    ),
                ));
                