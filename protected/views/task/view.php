<?php
/* @var $this TaskController */
/* @var $model Task */

$source = Navigator::getTaskType() === 'all' ? 'indexAll' : 'index';
$label = ucfirst(Navigator::getTaskType());
Navigator::setTaskId($model->id);

$projectId = Navigator::getProjectId();
$projectBreadcrumb = empty($projectId) ? array() : array(
    Yii::t('nav', ucfirst(Navigator::getProjectType()) . ' Projects') => array('project/' . (Navigator::getProjectType() === 'all' ? 'indexAll' : 'index')),
    Project::model()->findByPk($projectId)->name => array('project/viewTasks', 'id' => $projectId, 'trkq' => 1)
);

$taskBreadcrumb = array(Yii::t('nav', $label . ' Tasks') => array($source, 'trkq' => 1));

$breadcrumb = empty($projectBreadcrumb) ? $taskBreadcrumb : $projectBreadcrumb;
$this->breadcrumbs = CMap::mergeArray($breadcrumb, array(
            $model->title,
        ));

$this->menu = array(
    array('label' => 'List Task', 'url' => array('index')),
    array('label' => 'Create Task', 'url' => array('create')),
    array('label' => 'Manage Task', 'url' => array('admin')),
    array('label' => 'Update Task', 'url' => array('update', 'id' => $model->id)),
    array('label' => 'Delete Task', 'url' => '#', 'linkOptions' => array('submit' => array('delete', 'id' => $model->id),
            'confirm' => 'Are you sure you want to delete this item?')),
);
?>

<table>
  <tr>
    <td>
      <h2>
        <?php echo $model->calc_id; ?> - <?php echo $model->title; ?>
        <?php
        $redirectUrl = empty($projectId) ? (Navigator::home() ? '"/"' :
                        (Navigator::getTaskType() === 'all' ? '"indexAll"' : '"index"')) : '"project/viewTasks"';
        $redirectParams = empty($projectId) ? 'array()' : "array('id' => $projectId)";

        $this->widget('ActionsWidget', array(
            'data' => $model,
            'deleteRedirectUrl' => "Yii::app()->controller->createUrl($redirectUrl, $redirectParams)",
        ));
        ?>
      </h2>

      <?php
      $users = array();
      foreach ($model->users as $user)
        $users[] = CHtml::mailto($user->username, $user->email);

      $this->widget('zii.widgets.CDetailView', array(
          'data' => $model,
          'attributes' => array(
              array(
                  'label' => $model->getAttributeLabel('project'),
                  'name' => 'project.name',
              ),
              'title',
              array(
                  'label' => $model->getAttributeLabel('parent'),
                  'type' => 'html',
                  'value' => $model->parent ? CHtml::link($model->parent->calc_id . ' - ' . $model->parent->title, array('view', 'id' => $model->parent_id)) : null,
              ),
              'description:html',
              'chargeable_flg:boolean',
              'private_flg:boolean',
              array(
                  'name' => 'type',
                  'value' => $model->getType(),
              ),
              array(
                  'name' => 'priority',
                  'value' => $model->getPriority(),
              ),
              array(
                  'name' => 'status',
                  'value' => $model->getStatus()
              ),
              array(
                  'name' => 'progress',
                  'value' => $model->progress . ' %'
              ),
              array(
                  'label' => $model->getAttributeLabel('owner'),
                  'type' => 'html',
                  'value' => implode(', ', $users),
              ),
              'created:datetime',
              array(
                  'name' => 'created_by',
                  'value' => $model->creator->username
              ),
              'last_upd:datetime',
              'start_date:date',
              'end_date:date',
              'eff_start_date:date',
              'eff_end_date:date',
          ),
      ));
      ?>
    </td>
    <?php if (CPropertyValue::ensureBoolean(Yii::app()->params['attachments']['enable'])): ?>
      <td style="vertical-align: top;" >
        <div style="background-color: #F7FAFB;
             border: 1px solid #D3E7EC;
             margin: 0.25em 0 1em 0;
             padding: 0 1em 1em 1em;
             white-space: nowrap;
             ">
               <?php
               $this->renderPartial('/attachment/admin', array(
                   'model' => Attachment::model(),
                   'task_id' => $model->id,
                   'from_taskProject' => 'false')
               );
               ?>
        </div>
      </td>
    <?php endif; ?>
  </tr>
</table>

<?php
$ajaxHandler = CHtml::ajax(
    array('url' => array('comment/index', 'isAjax' => 1, 'entityId' => $model->id),
	'update' => '#comments') // jQuery selector
);

$this->renderPartial('/comment/_form',
array('model' => Comment::model(),
	  'entity' => 'task',
	  'entityId' => $model->id,
	  'handler' => $ajaxHandler ));

Yii::app()->clientScript->registerScript('ajaxComments', $ajaxHandler);
?>
<div id="comments">
	Loading Comments
</div>