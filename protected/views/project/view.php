<?php
/* @var $this ProjectController */
/* @var $model Project */
/* @var $users UserProject */

$source = Navigator::getProjectType() === 'all' ? 'indexAll' : 'index';
$label = ucfirst(Navigator::getProjectType());
Navigator::setProjectId($model->id);

$this->breadcrumbs = array(
    Yii::t('nav', $label . ' Projects') => array($source),
    $model->name,
);
?>

<h1>
  <?php echo $model->name; ?>
  <?php
  $this->widget('ActionsWidget', array(
      'data' => $model
  ));
  ?>
</h1>

<?php
$this->widget('zii.widgets.CDetailView', array(
    'data' => $model,
    'attributes' => array(
        'name',
        'prefix',
        'client',
        'description:html',
        array(
            'name' => 'champion.username',
            'label' => $model->getAttributeLabel('champion_id')
        ),
        array(
            'name' => 'status',
            'value' => $model->getStatus()
        ),
        array(
            'name' => 'progress',
            'value' => $model->progress . ' %'
        ),
        'chargeable_flg:boolean',
        array(
            'name' => 'parProject.name',
            'label' => $model->getAttributeLabel('par_project_id')
        ),
        'start_date:date',
        'end_date:date',
        'eff_start_date:date',
        'eff_end_date:date',
    ),
));
?>

<div style="height: 1%">&nbsp;</div>
<h2><?php echo Yii::t('nav', 'Project.User.association.{project}', array(
    '{project}' => $model->name));
?>
  <?php
  $this->widget('ActionsWidget', array(
      'data' => $users,
      'createButtonLabel' => Yii::t('nav', 'Associate New User'),
      'createButtonUrl' => 'array($this->entity . "/createByProject", "projectId" => ' . $model->id . ')',
      'createButtonVisible' => 'Yii::app()->user->checkAccess("updateProject")',
      'updateButtonVisible' => 'false',
      'deleteButtonVisible' => 'false',
  ));
  ?>
</h2>

<?php
$this->renderPartial('_adminUsers', array(
    'model' => $users,
    'project' => $model
));
