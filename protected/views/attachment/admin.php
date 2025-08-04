<?php
/* @var $this AttachmentController */
/* @var $model Attachment */
/* @var $task_id integer */
/* @var $from_taskProject string */
?>

<h2><?php
  echo Yii::t('nav', 'Attachments');

  $this->widget('ActionsWidget', array(
      'data' => $model,
      'template' => '{create}',
      'createButtonOptions' => array('class' => 'create',),
      'createButtonUrl' => 'Yii::app()->controller->createUrl($this->entity . "/create", array("task_id" => ' . $task_id . ', "taskProject" => ' . $from_taskProject . '))',
      'createButtonLabel' => '',
      'createButtonVisible' => 'Yii::app()->user->checkAccess("update" . ucfirst("task"))'
  ));
  ?>
</h2>
<?php
$this->widget('zii.widgets.grid.CGridView', array(
    'id' => 'attachment-grid',
    'dataProvider' => $model->searchTask($task_id),
    'filter' => null,
    'summaryText' => false,
    'selectableRows' => 0,
    'columns' => array(
        array(
            'name' => 'created',
            'type' => 'date',
            'htmlOptions' => array('class' => 'col-date')
        ),
        'name',
        array(
            'name' => 'type',
            'value' => 'Attachment::$types[$data->type]',
            'htmlOptions' => array('class' => 'col-type')
        ),
        array(
            'class' => 'ext.myGridView.MyButtonColumn',
//            'viewButtonUrl' => '$data->type === "file" ? Yii::app()->baseUrl . DIRECTORY_SEPARATOR . $data->uri : $data->uri',
            'viewButtonUrl' => '$data->type === "file" ? array("attachment/download", "id" => $data->id) : $data->uri',
            'deleteButtonUrl' => 'array("attachment/delete", "id" => $data->id)',
            'updateButtonUrl' => $from_taskProject === 'true' ? 'array("attachment/update", "id" => $data->id, "taskProject" => "true")' : 'array("attachment/update", "id" => $data->id, "taskProject" => "false")',
        ),
    ),
));

Yii::app()->clientScript->registerScript('newtab-links', '
    $("#attachment-grid .view").click(function() {
        window.open($(this).attr("href"));
        return false;
    });
', CClientScript::POS_READY);
